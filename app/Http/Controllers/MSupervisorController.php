<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\models\permission;
use Session;
use Carbon\Carbon;
use View;
use Input;
use Response;
use Excel;
use File;
use App\models\dailyrecommendation;
use Swift_Mailer;
use Flash;
use Notify;
use Redirect;
use Illuminate\Support\Facades\Validator;
use App\AppMenu;
use App\AppMenuPermission;
use App\DataSource;
use App\Language;
use App\ZonalManager;
use App\ZonalManagerState;
use App\ZonalManagerProductCategory;
use App\RegionalManager;
use App\RegionalManagerState;
use App\RegionalManagerProductCategory;
use App\SalesRegionalManager;
use App\SalesRegionalManagerState;
use App\SalesRegionalManagerProductCategory;
use App\AreaManager;
use App\AreaManagerState;
use App\AreaManagerProductCategory;
use App\SalesAreaManager;
use App\SalesAreaManagerState;
use App\SalesAreaManagerProductCategory;
use App\User;
use App\UserDeputation;
use App\UserProductCategory;
use App\State;
use App\District;
use App\Tehsil;
use App\Product;
use App\Crop;
use App\ProductCropRelation;
use App\ProductCropCategory;
use App\Ticket;
use App\Farmer;
use App\MarketingSupervisorProductCategory;
use App\MarketingSupervisor;
use App\MarketingSupervisorRSM;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\adminController;

class MSupervisorController extends Controller
{
    public function getAddMarketingSupervisor() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $category_list      = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $SalesRegionalManager_list = SalesRegionalManager::where('status', '1')->orderBy('sales_regional_manager_name', 'asc')->get();
        return View::make('backend.marketing_supervisor.addmarketingsupervisor', array('SalesRegionalManager_list'=>$SalesRegionalManager_list, 'category_list'=>$category_list));
    }

    public function postCreateMarketingSupervisor(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();  
            $rules  = array(
                            'sales_regional_manager_id'       => 'required|array',
                            'marketing_supervisor_hq_code'    => 'required|string',
                            'marketing_supervisor_name'       => 'required|string',
                            'marketing_supervisor_mobile'     => 'required|numeric',
                            'product_category'                => 'required|array'
                        );
            
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $sales_regional_manager_id        = $my_data['sales_regional_manager_id'];
            $marketing_supervisor_hq_code     = $my_data['marketing_supervisor_hq_code'];
            $marketing_supervisor_name        = $my_data['marketing_supervisor_name'];
            $marketing_supervisor_mobile      = $my_data['marketing_supervisor_mobile'];
            $product_category                 = $my_data['product_category'];
            
            $check_marketing_supervisor_code  = MarketingSupervisor::where('ECN', $marketing_supervisor_hq_code)->count();
            if($check_marketing_supervisor_code>0) {
                Session::flash('message', "ECN already exists, Please choose a different ECN.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
            else {
                $adminController        = new adminController;
                $random_string          = $adminController->generateRandomString(8);
                $password               = \Hash::make($random_string);
                $marketing_supervisor_array   = array(
                                                        'ECN'                               => $marketing_supervisor_hq_code,
                                                        'dash'                              => $random_string,
                                                        'password'                          => $password,
                                                        'marketing_supervisor_hq_code'    => $marketing_supervisor_hq_code,
                                                        'marketing_supervisor_name'       => $marketing_supervisor_name,
                                                        'marketing_supervisor_mobile'     => $marketing_supervisor_mobile,
                                                        'status'                            => '1'
                                                        );

                $insert_marketing_supervisor  = MarketingSupervisor::firstOrCreate($marketing_supervisor_array);
                $result = $insert_marketing_supervisor->marketing_supervisor_id;

                if($result) {
                    $sales_regional_manager_data   = array();
                    foreach($sales_regional_manager_id as $key => $value) {
                        $sales_regional_manager_data[] = array(
                                                                'sales_regional_manager_id' => $sales_regional_manager_id[$key],
                                                                'marketing_supervisor_id'   => $result,
                                                                'status'                    => '1',
                                                                'creation_at' 				=> date('Y-m-d H:i:s')
                                                                );
                    }                    
                    $insert_marketing_supervisor_rsm= MarketingSupervisorRSM::insert($sales_regional_manager_data);
                    
                    
                        
                    $marketing_supervisor_product_category = array();
                    foreach($product_category AS $product_category_id) {
                        $marketing_supervisor_product_category[]   = array(
                                                                'marketing_supervisor_id' => $result,
                                                                'product_crop_category_id'  => $product_category_id,
                                                                'status'                    => '1'
                                                                );
                    }
                    $insert_marketing_supervisor_product_category  = MarketingSupervisorProductCategory::insert($marketing_supervisor_product_category);
                        
                    Session::flash('message', "Marketing Supervisor Successfully Created.");
                    return redirect('supervisor/marketing-supervisor-list');
                }
                else {
                    Session::flash('message', "An error occurred. Please try again later.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            }
            
        }
    } 

    public function getMarketingSupervisorList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.marketing_supervisor.marketingsupervisorlist');
    } 
    public function postAllmarketingsupervisorlist(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        } 
        $value          = Input::all();
        $status         = isset($value['status']) ? $value['status'] :'';
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        if ($status != '') {
            $status = " and ms.status = '".$status."'";
        }
        else{
            $status = "";
        }
        if(empty($search_array['value']))
        {
            $sorting    = '';
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT
                                	ms.marketing_supervisor_id,
                                    ms.marketing_supervisor_hq_code,
                                    ms.marketing_supervisor_name,
                                    ms.marketing_supervisor_mobile,
                                    (CASE WHEN ms.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    ms.ECN,
                                    ms.dash,                                    
                                    DATE_FORMAT(ms.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time
                                                                      
                                FROM        marketing_supervisor   ms
                                LEFT JOIN   active_logs  al ON ms.marketing_supervisor_id = al.user_id AND al.user_type='marketing_supervisor' 
                                WHERE
                                    1=1  ".$status." 
                                GROUP BY ms.marketing_supervisor_id
                                )   A,
                                (select @a:=0) initvars";
            $marketing_supervisor  = collect(DB::select($query));
            //Sales regional manager list in comma saperate
            foreach ($marketing_supervisor as $key => $value) {
            	$rsmlist = DB::table('marketing_supervisor_rsm')
            			   ->join('sales_regional_managers','sales_regional_managers.sales_regional_manager_id', '=', 'marketing_supervisor_rsm.sales_regional_manager_id')
            			   ->where('marketing_supervisor_rsm.marketing_supervisor_id',$marketing_supervisor[$key]->marketing_supervisor_id)
            			   ->select('sales_regional_managers.sales_regional_manager_name')
            			   ->get();
            	$rsm_arr = array();
            	foreach($rsmlist as $rsm)
				{
				    $rsm_arr[] = $rsm->sales_regional_manager_name;
				}
				$marketing_supervisor[$key]->sales_regional_managers = implode(',',$rsm_arr);
            }

            //product list in comma seprate
            foreach ($marketing_supervisor as $key => $value) {
            	$productlist = DB::table('marketing_supervisor_product_categories')
	            			   ->join('product_crop_categories','product_crop_categories.product_crop_category_id', '=', 'marketing_supervisor_product_categories.product_crop_category_id')
	            			   ->where('marketing_supervisor_product_categories.marketing_supervisor_id',$marketing_supervisor[$key]->marketing_supervisor_id)
	            			   ->select('product_crop_categories.category_name')
	            			   ->get();
            	$p_arr = array();
            	foreach($productlist as $product)
				{
				    $p_arr[] = $product->category_name;
				}
				$marketing_supervisor[$key]->product_crop_categories = implode(',',$p_arr);
            }
            //print_r($marketing_supervisor);die; 
            return Datatables::of($marketing_supervisor)->make();
        }
        else {
             $search =  $search_array['value']; 
             $sorting= '';            
             $query  = " 
                         SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT
                                    ms.marketing_supervisor_id,
                                    ms.marketing_supervisor_hq_code,
                                    ms.marketing_supervisor_name,
                                    ms.marketing_supervisor_mobile,
                                    (CASE WHEN ms.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    ms.ECN,
                                    ms.dash,                                    
                                    DATE_FORMAT(ms.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time
                                                                      
                                FROM        marketing_supervisor   ms
                                LEFT JOIN   active_logs  al ON ms.marketing_supervisor_id = al.user_id AND al.user_type='marketing_supervisor'
                                WHERE 
                                (                                
                                 ms.marketing_supervisor_name      LIKE '%".$search."%'    OR
                                 ms.marketing_supervisor_mobile    LIKE '%".$search."%'    OR
                                 ms.ECN                            LIKE '%".$search."%'
                                ) ".$status."
                            	GROUP BY ms.marketing_supervisor_id
                            )   A,
                            (select @a:=0) initvars"; 
            //echo $query;die;
            $marketing_supervisor  = collect(DB::select($query));
            //Sales regional manager list in comma saperate
            foreach ($marketing_supervisor as $key => $value) {
                $rsmlist = DB::table('marketing_supervisor_rsm')
                           ->join('sales_regional_managers','sales_regional_managers.sales_regional_manager_id', '=', 'marketing_supervisor_rsm.sales_regional_manager_id')
                           ->where('marketing_supervisor_rsm.marketing_supervisor_id',$marketing_supervisor[$key]->marketing_supervisor_id)
                           ->select('sales_regional_managers.sales_regional_manager_name')
                           ->get();
                $rsm_arr = array();
                foreach($rsmlist as $rsm)
                {
                    $rsm_arr[] = $rsm->sales_regional_manager_name;
                }
                $marketing_supervisor[$key]->sales_regional_managers = implode(',',$rsm_arr);
            }

            //product list in comma seprate
            foreach ($marketing_supervisor as $key => $value) {
                $productlist = DB::table('marketing_supervisor_product_categories')
                               ->join('product_crop_categories','product_crop_categories.product_crop_category_id', '=', 'marketing_supervisor_product_categories.product_crop_category_id')
                               ->where('marketing_supervisor_product_categories.marketing_supervisor_id',$marketing_supervisor[$key]->marketing_supervisor_id)
                               ->select('product_crop_categories.category_name')
                               ->get();
                $p_arr = array();
                foreach($productlist as $product)
                {
                    $p_arr[] = $product->category_name;
                }
                $marketing_supervisor[$key]->product_crop_categories = implode(',',$p_arr);
            }
            //print_r($marketing_supervisor);die; 
            return Datatables::of($marketing_supervisor)->make();          
        }
    } 

    public function getEditMarketingSupervisor($supervisor_id) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $category_list      = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $SalesRegionalManager_list = SalesRegionalManager::where('status', '1')->orderBy('sales_regional_manager_name', 'asc')->get();
        $marketing_supervisor = [];
        if (!empty($supervisor_id)) 
        {
            $marketing_supervisor = DB::table('marketing_supervisor')
                                    ->where('marketing_supervisor.marketing_supervisor_id',$supervisor_id)
                                    ->get();
            //rsm
            $supervisor_rsm = MarketingSupervisorRSM::where('marketing_supervisor_rsm.marketing_supervisor_id',$supervisor_id)->select('marketing_supervisor_rsm.sales_regional_manager_id')
                              ->get()->toArray();
            $supervisorrsmdata = [];     
            foreach ($supervisor_rsm as $k => $v){
                array_push($supervisorrsmdata, $v['sales_regional_manager_id']);
            }

            //crop product
            $supervisor_cp = MarketingSupervisorProductCategory::where('marketing_supervisor_product_categories.marketing_supervisor_id',$supervisor_id)
                              ->select('marketing_supervisor_product_categories.product_crop_category_id')
                              ->get()->toArray();
            $supervisorcpdata = [];     
            foreach ($supervisor_cp as $k => $v){
                array_push($supervisorcpdata, $v['product_crop_category_id']);
            }
        }
        
        return View::make('backend.marketing_supervisor.editmarketingsupervisor', array('SalesRegionalManager_list'=>$SalesRegionalManager_list, 'category_list'=>$category_list, 'marketing_supervisor'=>$marketing_supervisor, 'supervisor_rsm'=>$supervisorrsmdata, 'supervisorcpdata' => $supervisorcpdata));
    } 


    public function postUpdateMarketingSupervisor(Request $request) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();  
            $rules  = array(
                            'sales_regional_manager_id'       => 'required|array',
                            'marketing_supervisor_hq_code'    => 'required|string',
                            'marketing_supervisor_name'       => 'required|string',
                            'marketing_supervisor_mobile'     => 'required|numeric',
                            'product_category'                => 'required|array'
                        );
            
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $sales_regional_manager_id        = $my_data['sales_regional_manager_id'];
            $marketing_supervisor_hq_code     = $my_data['marketing_supervisor_hq_code'];
            $marketing_supervisor_name        = $my_data['marketing_supervisor_name'];
            $marketing_supervisor_mobile      = $my_data['marketing_supervisor_mobile'];
            $product_category                 = $my_data['product_category'];
            $marketing_supervisor_id          = $my_data['marketing_supervisor_id'];
            
            $check_supervisor_code  = MarketingSupervisor::where('marketing_supervisor_id', $marketing_supervisor_id)->select('ECN')->get();
            
            if ($check_supervisor_code[0]->ECN == $marketing_supervisor_hq_code) 
            {
                // $adminController        = new adminController;
                // $random_string          = $adminController->generateRandomString(8);
                // $password               = \Hash::make($random_string);
                $marketing_supervisor_array   = array(
                                                        'ECN' => $marketing_supervisor_hq_code,
                                                        'marketing_supervisor_hq_code' => $marketing_supervisor_hq_code,
                                                        'marketing_supervisor_name' => $marketing_supervisor_name,
                                                        'marketing_supervisor_mobile' => $marketing_supervisor_mobile
                                                        );

                $result  = MarketingSupervisor::where('marketing_supervisor_id',$marketing_supervisor_id)->update($marketing_supervisor_array);
                
                //$result = $update_marketing_supervisor->marketing_supervisor_id;

                //if($result) {
                    
                    $deletersm = MarketingSupervisorRSM::where('marketing_supervisor_id',$marketing_supervisor_id)->delete();
                    
                    $sales_regional_manager_data   = array();
                    foreach($sales_regional_manager_id as $key => $value) {
                        $sales_regional_manager_data[] = array(
                                                                'sales_regional_manager_id' => $sales_regional_manager_id[$key],
                                                                'marketing_supervisor_id'   => $marketing_supervisor_id,
                                                                'status'                    => '1',
                                                                'creation_at'               => date('Y-m-d H:i:s')
                                                                );
                    }                    
                    $insert_marketing_supervisor_rsm= MarketingSupervisorRSM::insert($sales_regional_manager_data);
                    
                    
                    //crop product
                    $deletecp = MarketingSupervisorProductCategory::where('marketing_supervisor_id',$marketing_supervisor_id)->delete();
                        
                    $marketing_supervisor_product_category = array();
                    foreach($product_category AS $product_category_id) {
                        $marketing_supervisor_product_category[]   = array(
                                                                'marketing_supervisor_id' => $marketing_supervisor_id,
                                                                'product_crop_category_id'  => $product_category_id,
                                                                'status'                    => '1'
                                                                );
                    }
                    $insert_marketing_supervisor_product_category  = MarketingSupervisorProductCategory::insert($marketing_supervisor_product_category);
                        
                    Session::flash('message', "Marketing Supervisor Successfully Updated.");
                    return redirect('supervisor/marketing-supervisor-list');
                // }
                // else {
                //     Session::flash('message', "An error occurred. Please try again later.");
                //     return Redirect::back()->withInput()->withErrors($validator);
                // }
            
            }
            else
            {
                $check_marketing_supervisor_code  = MarketingSupervisor::where('ECN', $marketing_supervisor_hq_code)->count();
                if($check_marketing_supervisor_code>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else 
                {
                    // $adminController        = new adminController;
                    // $random_string          = $adminController->generateRandomString(8);
                    // $password               = \Hash::make($random_string);
                    $marketing_supervisor_array   = array(
                                                            'ECN' => $marketing_supervisor_hq_code,
                                                            'marketing_supervisor_hq_code' => $marketing_supervisor_hq_code,
                                                            'marketing_supervisor_name' => $marketing_supervisor_name,
                                                            'marketing_supervisor_mobile' => $marketing_supervisor_mobile
                                                            );

                    $result  = MarketingSupervisor::where('marketing_supervisor_id',$marketing_supervisor_id)->update($marketing_supervisor_array);
                    //print_r($result);die;
                    //$result = $update_marketing_supervisor->marketing_supervisor_id;

                    //if($result > 0) {
                        
                        $deletersm = MarketingSupervisorRSM::where('marketing_supervisor_id',$marketing_supervisor_id)->delete();
                        
                        $sales_regional_manager_data   = array();
                        foreach($sales_regional_manager_id as $key => $value) {
                            $sales_regional_manager_data[] = array(
                                                                    'sales_regional_manager_id' => $sales_regional_manager_id[$key],
                                                                    'marketing_supervisor_id'   => $marketing_supervisor_id,
                                                                    'status'                    => '1',
                                                                    'creation_at'               => date('Y-m-d H:i:s')
                                                                    );
                        }                    
                        $insert_marketing_supervisor_rsm= MarketingSupervisorRSM::insert($sales_regional_manager_data);
                        
                        
                        //crop product
                        $deletecp = MarketingSupervisorProductCategory::where('marketing_supervisor_id',$marketing_supervisor_id)->delete();
                            
                        $marketing_supervisor_product_category = array();
                        foreach($product_category AS $product_category_id) {
                            $marketing_supervisor_product_category[]   = array(
                                                                    'marketing_supervisor_id' => $marketing_supervisor_id,
                                                                    'product_crop_category_id'  => $product_category_id,
                                                                    'status'                    => '1'
                                                                    );
                        }
                        $insert_marketing_supervisor_product_category  = MarketingSupervisorProductCategory::insert($marketing_supervisor_product_category);
                            
                        Session::flash('message', "Marketing Supervisor Successfully Updated.");
                        return redirect('supervisor/marketing-supervisor-list');
                    // }
                    // else {
                    //     Session::flash('message', "An error occurred. Please try again later.");
                    //     return Redirect::back()->withInput()->withErrors($validator);
                    // }
                }
            }
            
            
        }
    }

    public function postUpdateMarketingSupervisorStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $marketing_supervisor_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $marketing_supervisor_data    = MarketingSupervisor::where('marketing_supervisor_id', $marketing_supervisor_id)->first();
            if($marketing_supervisor_data) {
                $marketing_supervisor_data_status = ($marketing_supervisor_data->status=='0'?'1':'0');
                $update_marketing_supervisor      = MarketingSupervisor::where('marketing_supervisor_id',$marketing_supervisor_id)->update(['status'=>$marketing_supervisor_data_status, 'updated_at' => date('Y-m-d H:i:s')]);
                echo '1';
            }
            else {
                echo '0';
            }
        }
        else {
            echo '0';
        }
    }

    public function postDeleteMarketingSupervisor(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $marketing_supervisor_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $update_marketing_supervisor      = MarketingSupervisor::where('marketing_supervisor_id',$marketing_supervisor_id)->update(['status'=>'2', 'updated_at' => date('Y-m-d H:i:s')]);
            if ($update_marketing_supervisor) {
                echo 1;
            } else {
                echo 2;
            }

        }
    }
}
