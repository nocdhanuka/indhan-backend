<?php

namespace App\Http\Controllers;

use Request;
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
use App\SalesRegionalManagerOrn;
use App\SalesRegionalManagerState;
use App\SalesRegionalManagerStateOrn;
use App\SalesRegionalManagerProductCategory;
use App\SalesAreaManagerProductCategoryOrn;
use App\AreaManager;
use App\AreaManagerState;
use App\AreaManagerProductCategory;
use App\SalesAreaManager;
use App\SalesAreaManagerState;
use App\SalesAreaManagerProductCategory;
use App\SalesRegionalManagerProductCategoryOrn;
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



use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class salesRegionalManagerController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    
    public function generateRandomString($length = 8) {
        $characters = '0123456789ABCDEFGHJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function postCheckSalesRegionalEcn(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();             
            if($my_data) {
                $sales_regional_manager_hq_code     = $my_data['sales_regional_manager_hq_code'];
                $sales_regional_manager_id          = $my_data['_sales_regional'];
                $check_sales_regional_manager_code  = SalesRegionalManager::where('sales_regional_manager_id', '!=', $sales_regional_manager_id)
                                                        ->where('ECN', $sales_regional_manager_hq_code)
                                                        ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                             UNION
                            (SELECT dd_id AS id FROM dhanuka_doctor WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                echo ($check_sales_regional_manager_code>0 || count($user)>0?'false':'true');die;
            }
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $my_data    = Input::all();             
            if($my_data) {
                $sales_regional_manager_hq_code     = $my_data['sales_regional_manager_hq_code'];
                $sales_regional_manager_id          = $my_data['_sales_regional'];
                $check_sales_regional_manager_code  = SalesRegionalManager::where('sales_regional_manager_id', '!=', $sales_regional_manager_id)
                                                        ->where('ECN', $sales_regional_manager_hq_code)
                                                        ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT dd_id AS id FROM dhanuka_doctor WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                echo ($check_sales_regional_manager_code>0 || count($user)>0?'false':'true');die;
            }
        }
    } 
    public function getAddSalesRegionalManager() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $category_list      = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $zonal_manager_list = ZonalManager::where('status', '1')->orderBy('zonal_manager_name', 'asc')->get();
        $state_list         = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        return View::make('backend.sales_regional_manager.addsalesregionalmanager', array('state_list'=>$state_list, 'zonal_manager_list'=>$zonal_manager_list, 'category_list'=>$category_list));
    }
    public function postCreateSalesRegionalManager(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();  
            $rules  = array(
                            'zonal_manager'                     => 'required|numeric',
                            'sales_regional_manager_hq_code'    => 'required|string',
                            'sales_regional_name'               => 'required|string',
                            'sales_regional_mobile_number'      => 'required|numeric',
                            'sales_regional_state'              => 'required|array',
                            'sales_regional_pincode'            => 'required|numeric',
                            'product_category'                  => 'required|array'
                        );
            
            //dd($my_data);
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            
            $zonal_manager                      = $my_data['zonal_manager'];
            $sales_regional_manager_hq_code     = $my_data['sales_regional_manager_hq_code'];
            $sales_regional_name                = $my_data['sales_regional_name'];
            $sales_regional_mobile_number       = $my_data['sales_regional_mobile_number'];
            $sales_regional_state               = $my_data['sales_regional_state'];
            $sales_regional_pincode             = $my_data['sales_regional_pincode'];
            $product_category                   = $my_data['product_category'];
            
            
            if(count($sales_regional_state)>0) {            
                $check_sales_regional_manager_code  = SalesRegionalManager::where('ECN', $sales_regional_manager_hq_code)->count();
                if($check_sales_regional_manager_code>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $adminController        = new adminController;
                    $random_string          = $adminController->generateRandomString(8);
                    $password               = \Hash::make($random_string);
                    $sales_regional_manager_array   = array(
                                                            'ECN'                               => $sales_regional_manager_hq_code,
                                                            'dash'                              => $random_string,
                                                            'password'                          => $password,
                                                            'zonal_manager_id'                  => $zonal_manager,
                                                            'sales_regional_manager_hq_code'    => $sales_regional_manager_hq_code,
                                                            'sales_regional_manager_name'       => $sales_regional_name,
                                                            'sales_regional_manager_mobile'     => $sales_regional_mobile_number,
                                                            'sales_regional_manager_pincode'    => $sales_regional_pincode,
                                                            'status'                            => '1'
                                                            );

                    $insert_sales_regional_manager  = SalesRegionalManager::firstOrCreate($sales_regional_manager_array);
                    $result                         = $insert_sales_regional_manager->sales_regional_manager_id;


                    if($result) {
                        $sales_regional_manager_states   = array();
                        foreach($sales_regional_state AS $state_id) {
                            $sales_regional_manager_states[] = array(
                                                                    'sales_regional_manager_id' => $result,
                                                                    'state_id'                  => $state_id,
                                                                    'status'                    => '1'
                                                                    );
                        }                    
                        $insert_sales_regional_manager_states= SalesRegionalManagerState::insert($sales_regional_manager_states);
                        
                        
                            
                        $sales_regional_manager_product_category = array();
                        foreach($product_category AS $product_category_id) {
                            $sales_regional_manager_product_category[]   = array(
                                                                    'sales_regional_manager_id' => $result,
                                                                    'product_crop_category_id'  => $product_category_id,
                                                                    'status'                    => '1'
                                                                    );
                        }
                        $insert_regional_manager_product_category  = SalesRegionalManagerProductCategory::insert($sales_regional_manager_product_category);
                            
                        Session::flash('message', "Sales Regional Manager Successfully Created.");
                        return redirect('salesregional/sales-regional-manager-list');
                    }
                    else {
                        Session::flash('message', "An error occurred. Please try again later.");
                        return Redirect::back()->withInput()->withErrors($validator);
                    }
                }
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  
    public function getSalesRegionalManagerList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.sales_regional_manager.salesregionalmanagerlist');
    } 
    public function postAllsalesregionalmanagerlist(Request $request) {
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
        
        $zonalId = "";
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if ($status != '') {
                $zonalId = " and rm.status = ".$status;
            }
            else{
                $zonalId = "";
            }
        }

        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        if ($userData1) {
            if ($status != '') {
                $zonalId = " and rm.zonal_manager_id = ".$userData1->zonal_manager_id." and rm.status = ".$status;
            }
            else{
                $zonalId = " and rm.zonal_manager_id = ".$userData1->zonal_manager_id;
            }
            
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
                                    rm.sales_regional_manager_hq_code,
                                    zm.zonal_manager_name,
                                    rm.sales_regional_manager_name,
                                    rm.sales_regional_manager_mobile,
                                    rm.sales_regional_manager_pincode,                                                                   
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN rm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    rm.ECN,
                                    rm.dash,                                    
                                    DATE_FORMAT(rm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                    rm.sales_regional_manager_id,                                    
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        sales_regional_managers                     rm
                                LEFT JOIN   active_logs                                 al      ON rm.sales_regional_manager_id=al.user_id AND al.user_type='sales_regional' 
                                LEFT JOIN   zonal_managers                              zm      ON zm.zonal_manager_id=rm.zonal_manager_id
                                LEFT JOIN   sales_regional_manager_states               rms     ON rms.sales_regional_manager_id=rm.sales_regional_manager_id
                                LEFT JOIN   sales_regional_manager_product_categories   rmpc    ON rmpc.sales_regional_manager_id=rm.sales_regional_manager_id and rmpc.status=1
                                LEFT JOIN   states                                      s       ON rms.state_id=s.state_id
                                LEFT JOIN   product_crop_categories                     pcc     ON pcc.product_crop_category_id=rmpc.product_crop_category_id
                                WHERE
                                    1=1   ".$zonalId." 
                                GROUP BY rms.sales_regional_manager_id
                                )   A,
                                (select @a:=0) initvars";
                                //echo $query;die;
            $sales_regional_manager  = collect(DB::select($query));
            foreach ($sales_regional_manager as $key => $value) {
                $states = DB::table('sales_regional_manager_states as zms')
                          ->join('states as s','s.state_id','=','zms.state_id')  
                          ->where('sales_regional_manager_id',$sales_regional_manager[$key]->sales_regional_manager_id)
                          ->where('zms.status','1')
                          ->select(DB::raw("GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n') as state"))
                          ->get();
                if (count($states)>0) 
                {
                    $sales_regional_manager[$key]->states = $states[0]->state;
                }         
            }  
            //echo "<pre>";print_r($sales_regional_manager);die;
            return Datatables::of($sales_regional_manager)->make();
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
                                rm.sales_regional_manager_hq_code,
                                zm.zonal_manager_name,
                                rm.sales_regional_manager_name,
                                rm.sales_regional_manager_mobile,
                                rm.sales_regional_manager_pincode,                                                                   
                                GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                (CASE WHEN rm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                rm.ECN,
                                rm.dash,                                    
                                DATE_FORMAT(rm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                rm.sales_regional_manager_id,                                    
                                GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                            FROM        sales_regional_managers                     rm
                            LEFT JOIN   active_logs                                 al      ON rm.sales_regional_manager_id=al.user_id AND al.user_type='sales_regional' 
                            LEFT JOIN   zonal_managers                              zm      ON zm.zonal_manager_id=rm.zonal_manager_id
                            LEFT JOIN   sales_regional_manager_states               rms     ON rms.sales_regional_manager_id=rm.sales_regional_manager_id
                            LEFT JOIN   sales_regional_manager_product_categories   rmpc    ON rmpc.sales_regional_manager_id=rm.sales_regional_manager_id and rmpc.status=1
                            LEFT JOIN   states                                      s       ON rms.state_id=s.state_id
                            LEFT JOIN   product_crop_categories                     pcc     ON pcc.product_crop_category_id=rmpc.product_crop_category_id
                            WHERE
                                (                                
                                rm.sales_regional_manager_name      LIKE '%".$search."%'    OR
                                rm.sales_regional_manager_mobile    LIKE '%".$search."%'    OR
                                rm.sales_regional_manager_pincode   LIKE '%".$search."%'    OR
                                rm.ECN                              LIKE '%".$search."%'    OR
                                s.state                             LIKE '%".$search."%'
                                ) ".$zonalId." 
                            GROUP BY rms.sales_regional_manager_id
                            )   A,
                            (select @a:=0) initvars"; 
            
            $sales_regional_manager  = collect(DB::select($query));
            foreach ($sales_regional_manager as $key => $value) {
                $states = DB::table('sales_regional_manager_states as zms')
                          ->join('states as s','s.state_id','=','zms.state_id')  
                          ->where('sales_regional_manager_id',$sales_regional_manager[$key]->sales_regional_manager_id)
                          ->where('zms.status','1')
                          ->select(DB::raw("GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n') as state"))
                          ->get();
                if (count($states)>0) 
                {
                    $sales_regional_manager[$key]->states = $states[0]->state;
                }         
            }  

            return Datatables::of($sales_regional_manager)->make();            
        }
    }    
    public function getEditSalesRegionalManager($sales_regional_manager_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($sales_regional_manager_id!='') {
                $zonal_manager                  = ZonalManager::where('status', '1')->get();
                $sales_regional_manager         = SalesRegionalManager::where('sales_regional_manager_id', $sales_regional_manager_id)->first();
                $sales_regional_manager_state   = SalesRegionalManagerState::select(DB::raw("GROUP_CONCAT(state_id SEPARATOR ',')  AS state_id"))->where('sales_regional_manager_id', $sales_regional_manager_id)->where('status', '1')->first();
                $state_list                     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                
                $sales_regional_manager_product_category    = SalesRegionalManagerProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('sales_regional_manager_id', $sales_regional_manager_id)->where('status', '1')->first();
                $category_list                              = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
                
                return View::make('backend.sales_regional_manager.editsalesregionalmanager', array('zonal_manager_list'=>$zonal_manager, 'sales_regional_manager'=>$sales_regional_manager, 'sales_regional_manager_state'=>$sales_regional_manager_state, 'state_list'=>$state_list, 'category_list'=>$category_list, 'sales_regional_manager_category_list'=>$sales_regional_manager_product_category));
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }  
    public function postUpdateSalesRegionalManager(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'zonal_manager'                     => 'required|numeric',
                            'sales_regional_manager_hq_code'    => 'required|string',
                            'sales_regional_name'               => 'required|string',
                            'sales_regional_mobile_number'      => 'required|numeric',
                            'sales_regional_state'              => 'required|array',
                            'sales_regional_pincode'            => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $sales_regional_id                  = $my_data['sales_regional_id'];
            $zonal_manager                      = $my_data['zonal_manager'];
            $sales_regional_manager_hq_code     = $my_data['sales_regional_manager_hq_code'];
            $sales_regional_name                = $my_data['sales_regional_name'];
            $sales_regional_mobile_number       = $my_data['sales_regional_mobile_number'];
            $sales_regional_state               = $my_data['sales_regional_state'];
            $sales_regional_pincode             = $my_data['sales_regional_pincode'];
            $product_category                   = $my_data['product_category'];
            $change_password                    = isset($my_data['change_password'])?'1':'0';
            if(count($sales_regional_state)>0) {
                $check_sales_regional_manager_code    = SalesRegionalManager::where('sales_regional_manager_id', '!=', $sales_regional_id)
                                                        ->where('ECN', $sales_regional_manager_hq_code)
                                                        ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                if($check_sales_regional_manager_code>0 || count($user)>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $sales_regional_manager_array    = array(
                                                        'ECN'                               => $sales_regional_manager_hq_code,
                                                        'zonal_manager_id'                  => $zonal_manager,
                                                        'sales_regional_manager_hq_code'    => $sales_regional_manager_hq_code,
                                                        'sales_regional_manager_name'       => $sales_regional_name,
                                                        'sales_regional_manager_mobile'     => $sales_regional_mobile_number,
                                                        'sales_regional_manager_pincode'    => $sales_regional_pincode,
                                                        'updated_at'                        => date('Y-m-d H:i:s')
                                                    );
                    if($change_password=='1'){
                        $random_string                              = $this->generateRandomString(8);
                        $password                                   = \Hash::make($random_string);
                        $sales_regional_manager_array['password']   = $password;
                        $sales_regional_manager_array['dash']       = $random_string;
                    }
                    
                    
                    $update_sales_regional_manager  = DB::table('sales_regional_managers')
                                                        ->where('sales_regional_manager_id', $sales_regional_id)
                                                        ->where('status', '1')
                                                        ->update($sales_regional_manager_array); 
                    $update_sales_area_manager      = DB::table('sales_area_managers')
                                                        ->where('sales_regional_manager_id', $sales_regional_id)
                                                        ->where('status', '1')
                                                        ->update(['zonal_manager_id'=>$zonal_manager]); 
                    $update_bdo                     = DB::table('users')
                                                        ->where('sales_regional_manager_id', $sales_regional_id)
                                                        ->where('status', '1')
                                                        ->update(['zonal_manager_id'=>$zonal_manager]); 
                    
                    
                    $reset_sales_regional_manager_states   = DB::table('sales_regional_manager_states')
                                                            ->where('sales_regional_manager_id', $sales_regional_id)
                                                            ->update(['status'=>'0']); 
                    foreach($sales_regional_state AS $state_id) {
                        $update_sales_regional_manager   = DB::table('sales_regional_manager_states')
                                                    ->where('sales_regional_manager_id', $sales_regional_id)
                                                    ->where('state_id', $state_id)
                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $sales_regional_manager_states   = array(
                                                        'sales_regional_manager_id'  => $sales_regional_id,
                                                        'state_id'          => $state_id,
                                                        'status'            => '1'
                                                        );
                        $insert_sales_regional_manager   = SalesRegionalManagerState::firstOrCreate($sales_regional_manager_states);
                    }   
                    
                    
                    
                
                    $reset_regional_manager_category= DB::table('sales_regional_manager_product_categories')
                                                    ->where('sales_regional_manager_id', $sales_regional_id)
                                                    ->update(['status'=>'0']); 
                    foreach($product_category AS $category_id) {
                        $update_regional_manager   = DB::table('sales_regional_manager_product_categories')
                                                    ->where('sales_regional_manager_id', $sales_regional_id)
                                                    ->where('product_crop_category_id', $category_id)
                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $regional_manager_states   = array(
                                                        'sales_regional_manager_id' => $sales_regional_id,
                                                        'product_crop_category_id'  => $category_id,
                                                        'status'                    => '1'
                                                        );
                        $insert_regional_manager   = SalesRegionalManagerProductCategory::firstOrCreate($regional_manager_states);
                    }
                
                
                    Session::flash('message', "Sales Regional Manager Successfully Updated.");
                    return redirect('salesregional/sales-regional-manager-list');
                }
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    } 
    public function postUpdateSalesRegionalStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $sales_regional_manager_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $sales_regional_manager_data    = SalesRegionalManager::where('sales_regional_manager_id', $sales_regional_manager_id)->first();
            if($sales_regional_manager_data) {
                $sales_regional_manager_data_status = ($sales_regional_manager_data->status=='0'?'1':'0');
                $update_sales_regional_manager      = DB::table('sales_regional_managers')
                                                ->where('sales_regional_manager_id', $sales_regional_manager_id)
                                                ->update(['status'=>$sales_regional_manager_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    public function postSalesRegionalDelete(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            '_ticket'    => 'required|numeric'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                echo '0';
            }
            else {
                $sales_regional_manager_id     = $my_data['_ticket'];
                $update_sales_regional_manager = DB::table('sales_regional_managers')
                                                    ->where('sales_regional_manager_id', $sales_regional_manager_id)
                                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_sales_regional_manager) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    } 
    
    //Organogram ============================================
    public function getAddSalesRegionalManagerOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        $category_list      = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $zonal_manager_list = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
        $state_list         = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        return View::make('backend.organogram_sales_regional_manager.addsalesregionalmanager', array('state_list'=>$state_list, 'zonal_manager_list'=>$zonal_manager_list, 'category_list'=>$category_list));
    }

    public function postCreateSalesRegionalManagerOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();  
            $rules  = array(
                            'zonal_manager'                     => 'required|numeric',
                            'sales_regional_manager_hq_code'    => 'required|string',
                            'sales_regional_name'               => 'required|string',
                            'sales_regional_mobile_number'      => 'required|numeric',
                            'sales_regional_state'              => 'required|array',
                            'sales_regional_pincode'            => 'required|numeric',
                            'product_category'                  => 'required|array'
                        );
            
            //dd($my_data);
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            
            $zonal_manager                      = $my_data['zonal_manager'];
            $sales_regional_manager_hq_code     = $my_data['sales_regional_manager_hq_code'];
            $sales_regional_name                = $my_data['sales_regional_name'];
            $sales_regional_mobile_number       = $my_data['sales_regional_mobile_number'];
            $sales_regional_state               = $my_data['sales_regional_state'];
            $sales_regional_pincode             = $my_data['sales_regional_pincode'];
            $product_category                   = $my_data['product_category'];
            
            
            if(count($sales_regional_state)>0) {            
                $check_sales_regional_manager_code  = SalesRegionalManager::where('ECN', $sales_regional_manager_hq_code)->count();
                if($check_sales_regional_manager_code>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $adminController        = new adminController;
                    $random_string          = $adminController->generateRandomString(8);
                    $password               = \Hash::make($random_string);
                    $sales_regional_manager_array   = array(
                                                            'ECN'                               => $sales_regional_manager_hq_code,
                                                            'dash'                              => $random_string,
                                                            'password'                          => $password,
                                                            'zonal_manager_id'                  => $zonal_manager,
                                                            'sales_regional_manager_hq_code'    => $sales_regional_manager_hq_code,
                                                            'sales_regional_manager_name'       => $sales_regional_name,
                                                            'sales_regional_manager_mobile'     => $sales_regional_mobile_number,
                                                            'sales_regional_manager_pincode'    => $sales_regional_pincode,
                                                            'status'                            => '1',
                                                            'updated_at'                        => date('Y-m-d H:i:s')
                                                            );

                    $insert_sales_regional_manager  = SalesRegionalManagerOrn::firstOrCreate($sales_regional_manager_array);
                    $result                         = $insert_sales_regional_manager->sales_regional_managers_orn_id;


                    if($result) {
                        $sales_regional_manager_states   = array();
                        foreach($sales_regional_state AS $state_id) {
                            $sales_regional_manager_states[] = array(
                                                                    'sales_regional_manager_id' => $result,
                                                                    'state_id'                  => $state_id,
                                                                    'status'                    => '1',
                                                                    'orn_id' => $result
                                                                    );
                        }                    
                        $insert_sales_regional_manager_states= SalesRegionalManagerStateOrn::insert($sales_regional_manager_states);
                        
                        
                            
                        $sales_regional_manager_product_category = array();
                        foreach($product_category AS $product_category_id) {
                            $sales_regional_manager_product_category[]   = array(
                                                                    'sales_regional_manager_id' => $result,
                                                                    'product_crop_category_id'  => $product_category_id,
                                                                    'status'                    => '1',
                                                                    'orn_id' => $result
                                                                    );
                        }
                        $insert_regional_manager_product_category  = SalesRegionalManagerProductCategoryOrn::insert($sales_regional_manager_product_category);
                            
                        Session::flash('message', "Sales Regional Manager Successfully Created. Please wait for Super Admin approval.");
                        return redirect('salesregional/sales-regional-manager-list-orn');
                    }
                    else {
                        Session::flash('message', "An error occurred. Please try again later.");
                        return Redirect::back()->withInput()->withErrors($validator);
                    }
                }
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    } 
    public function getSalesRegionalManagerListOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.organogram_sales_regional_manager.salesregionalmanagerlist');
    }
    public function postAllsalesregionalmanagerlistorn(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        } 
        $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        
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
                                    rm.sales_regional_manager_hq_code,
                                    zm.zonal_manager_name,
                                    rm.sales_regional_manager_name,
                                    rm.sales_regional_manager_mobile,
                                    rm.sales_regional_manager_pincode,                                                                   
                                    GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS state,
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN rm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    rm.ECN,
                                    rm.dash,                                    
                                    DATE_FORMAT(rm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                    rm.sales_regional_manager_id,                                    
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        sales_regional_managers                     rm
                                LEFT JOIN   active_logs                                 al      ON rm.sales_regional_manager_id=al.user_id AND al.user_type='sales_regional' 
                                LEFT JOIN   zonal_managers                              zm      ON zm.zonal_manager_id=rm.zonal_manager_id
                                LEFT JOIN   sales_regional_manager_states               rms     ON rms.sales_regional_manager_id=rm.sales_regional_manager_id
                                LEFT JOIN   sales_regional_manager_product_categories   rmpc    ON rmpc.sales_regional_manager_id=rm.sales_regional_manager_id and rmpc.status=1
                                LEFT JOIN   states                                      s       ON rms.state_id=s.state_id
                                LEFT JOIN   product_crop_categories                     pcc     ON pcc.product_crop_category_id=rmpc.product_crop_category_id
                                WHERE
                                    rm.zonal_manager_id = '".$zonalId->zonal_manager_id."' 
                                GROUP BY rms.sales_regional_manager_id
                                )   A,
                                (select @a:=0) initvars";
            $sales_regional_manager  = collect(DB::select($query)); 
            if (count($sales_regional_manager)>0) {
                foreach ($sales_regional_manager as $key => $value) {
                    $checkStatus = DB::table('sales_regional_managers_orn')
                                 ->where('sales_regional_manager_id',$sales_regional_manager[$key]->sales_regional_manager_id)
                                 ->where('orn_status','0')
                                 ->get();
                    if (count($checkStatus)>0) {
                        if ($sales_regional_manager[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $sales_regional_manager[$key]->status = 'ACTIVE';
                        }
                        elseif ($sales_regional_manager[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $sales_regional_manager[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $sales_regional_manager[$key]->status = 'PENDING';
                        }
                    }                
                }               
            } 
            return Datatables::of($sales_regional_manager)->make();
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
                                rm.sales_regional_manager_hq_code,
                                zm.zonal_manager_name,
                                rm.sales_regional_manager_name,
                                rm.sales_regional_manager_mobile,
                                rm.sales_regional_manager_pincode,                                                                   
                                GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS state,
                                GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                (CASE WHEN rm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                rm.ECN,
                                rm.dash,                                    
                                DATE_FORMAT(rm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                rm.sales_regional_manager_id,                                    
                                GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                            FROM        sales_regional_managers                     rm
                            LEFT JOIN   active_logs                                 al      ON rm.sales_regional_manager_id=al.user_id AND al.user_type='sales_regional' 
                            LEFT JOIN   zonal_managers                              zm      ON zm.zonal_manager_id=rm.zonal_manager_id
                            LEFT JOIN   sales_regional_manager_states               rms     ON rms.sales_regional_manager_id=rm.sales_regional_manager_id
                            LEFT JOIN   sales_regional_manager_product_categories   rmpc    ON rmpc.sales_regional_manager_id=rm.sales_regional_manager_id and rmpc.status=1
                            LEFT JOIN   states                                      s       ON rms.state_id=s.state_id
                            LEFT JOIN   product_crop_categories                     pcc     ON pcc.product_crop_category_id=rmpc.product_crop_category_id
                            WHERE
                                (                                
                                rm.sales_regional_manager_name      LIKE '%".$search."%'    OR
                                rm.sales_regional_manager_mobile    LIKE '%".$search."%'    OR
                                rm.sales_regional_manager_pincode   LIKE '%".$search."%'    OR
                                rm.ECN                              LIKE '%".$search."%'    OR
                                s.state                             LIKE '%".$search."%'
                                ) 
                                and rm.zonal_manager_id = '".$zonalId->zonal_manager_id."'
                            GROUP BY rms.sales_regional_manager_id
                            )   A,
                            (select @a:=0) initvars"; 
            
            $sales_regional_manager  = collect(DB::select($query));
            if (count($sales_regional_manager)>0) {
                foreach ($sales_regional_manager as $key => $value) {
                    $checkStatus = DB::table('sales_regional_managers_orn')
                                 ->where('sales_regional_manager_id',$sales_regional_manager[$key]->sales_regional_manager_id)
                                 ->where('orn_status','0')
                                 ->get();
                    if (count($checkStatus)>0) {
                        if ($sales_regional_manager[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $sales_regional_manager[$key]->status = 'ACTIVE';
                        }
                        elseif ($sales_regional_manager[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $sales_regional_manager[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $sales_regional_manager[$key]->status = 'PENDING';
                        }
                    }                
                }               
            } 
            return Datatables::of($sales_regional_manager)->make();            
        }
    }
    public function postUpdateSalesRegionalStatusOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $sales_regional_manager_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $sales_regional_manager_data    = SalesRegionalManager::where('sales_regional_manager_id', $sales_regional_manager_id)->first();
            if($sales_regional_manager_data) {
                $sales_regional_manager_data_status = ($sales_regional_manager_data->status=='0'?'1':'0');
                $insert_asm     = array(       
                                        'sales_regional_manager_id' => $sales_regional_manager_data->sales_regional_manager_id,
                                        'ECN' => $sales_regional_manager_data->ECN,
                                        'dash' => $sales_regional_manager_data->dash,
                                        'password' => $sales_regional_manager_data->password,
                                        'api_token' => $sales_regional_manager_data->api_token,
                                        'zonal_manager_id' => $sales_regional_manager_data->zonal_manager_id,
                                        'sales_regional_manager_hq_code' => $sales_regional_manager_data->sales_regional_manager_hq_code,
                                        'sales_regional_manager_name' => $sales_regional_manager_data->sales_regional_manager_name,
                                        'sales_regional_manager_mobile' => $sales_regional_manager_data->sales_regional_manager_mobile,
                                        'sales_regional_manager_pincode' => $sales_regional_manager_data->sales_regional_manager_pincode,
                                        'status' => $sales_regional_manager_data_status,
                                        'device_id' => $sales_regional_manager_data->device_id,
                                        'created_at' => $sales_regional_manager_data->created_at,
                                        'updated_at'    => date('Y-m-d H:i:s')
                                            ); 
                $insert_user    = SalesRegionalManagerOrn::firstOrCreate($insert_asm);
                $result         = $insert_user->sales_regional_managers_orn_id;
                if($result) {
                    echo '1';
                }
                else
                {
                    echo '2';
                }
                // $update_sales_regional_manager      = DB::table('sales_regional_managers')
                //                                 ->where('sales_regional_manager_id', $sales_regional_manager_id)
                //                                 ->update(['status'=>$sales_regional_manager_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
                // echo '1';
            }
            else {
                echo '0';
            }
        }
        else {
            echo '0';
        }
    }
    public function getEditSalesRegionalManagerOrn($sales_regional_manager_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($sales_regional_manager_id!='') {
                $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
                $zonal_manager             = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
                $sales_regional_manager         = SalesRegionalManager::where('sales_regional_manager_id', $sales_regional_manager_id)->first();
                $sales_regional_manager_state   = SalesRegionalManagerState::select(DB::raw("GROUP_CONCAT(state_id SEPARATOR ',')  AS state_id"))->where('sales_regional_manager_id', $sales_regional_manager_id)->where('status', '1')->first();
                $state_list                     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                
                $sales_regional_manager_product_category    = SalesRegionalManagerProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('sales_regional_manager_id', $sales_regional_manager_id)->where('status', '1')->first();
                $category_list                              = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
                
                return View::make('backend.organogram_sales_regional_manager.editsalesregionalmanager', array('zonal_manager_list'=>$zonal_manager, 'sales_regional_manager'=>$sales_regional_manager, 'sales_regional_manager_state'=>$sales_regional_manager_state, 'state_list'=>$state_list, 'category_list'=>$category_list, 'sales_regional_manager_category_list'=>$sales_regional_manager_product_category));
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }  
    public function postUpdateSalesRegionalManagerOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'zonal_manager'                     => 'required|numeric',
                            'sales_regional_manager_hq_code'    => 'required|string',
                            'sales_regional_name'               => 'required|string',
                            'sales_regional_mobile_number'      => 'required|numeric',
                            'sales_regional_state'              => 'required|array',
                            'sales_regional_pincode'            => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $sales_regional_id                  = $my_data['sales_regional_id'];
            $zonal_manager                      = $my_data['zonal_manager'];
            $sales_regional_manager_hq_code     = $my_data['sales_regional_manager_hq_code'];
            $sales_regional_name                = $my_data['sales_regional_name'];
            $sales_regional_mobile_number       = $my_data['sales_regional_mobile_number'];
            $sales_regional_state               = $my_data['sales_regional_state'];
            $sales_regional_pincode             = $my_data['sales_regional_pincode'];
            $product_category                   = $my_data['product_category'];
            $change_password                    = isset($my_data['change_password'])?'1':'0';
            if(count($sales_regional_state)>0) {
                $check_sales_regional_manager_code    = SalesRegionalManager::where('sales_regional_manager_id', '!=', $sales_regional_id)
                                                        ->where('ECN', $sales_regional_manager_hq_code)
                                                        ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                if($check_sales_regional_manager_code>0 || count($user)>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $sales_regional_manager_array    = array(
                                                        'sales_regional_manager_id'         => $sales_regional_id,
                                                        'ECN'                               => $sales_regional_manager_hq_code,
                                                        'zonal_manager_id'                  => $zonal_manager,
                                                        'sales_regional_manager_hq_code'    => $sales_regional_manager_hq_code,
                                                        'sales_regional_manager_name'       => $sales_regional_name,
                                                        'sales_regional_manager_mobile'     => $sales_regional_mobile_number,
                                                        'sales_regional_manager_pincode'    => $sales_regional_pincode,
                                                        'updated_at'                        => date('Y-m-d H:i:s')
                                                    );
                    if($change_password=='1'){
                        $random_string                              = $this->generateRandomString(8);
                        $password                                   = \Hash::make($random_string);
                        $sales_regional_manager_array['password']   = $password;
                        $sales_regional_manager_array['dash']       = $random_string;
                    }

                    if (isset($random_string) && !empty($random_string)) {
                        $rsmData = SalesRegionalManager::where('sales_regional_manager_id', $sales_regional_id)->first();
                        $sales_regional_manager_array['api_token']    = $rsmData->api_token;
                        $sales_regional_manager_array['status']       = $rsmData->status;
                        $sales_regional_manager_array['created_at']   = $rsmData->created_at;
                        $sales_regional_manager_array['device_id']    = $rsmData->device_id;
                    }
                    else
                    {
                        $rsmData = SalesRegionalManager::where('sales_regional_manager_id', $sales_regional_id)->first();
                        $sales_regional_manager_array['password']     = $rsmData->password;
                        $sales_regional_manager_array['dash']         = $rsmData->dash;
                        $sales_regional_manager_array['api_token']    = $rsmData->api_token;
                        $sales_regional_manager_array['created_at']   = $rsmData->created_at;
                        $sales_regional_manager_array['device_id']    = $rsmData->device_id;
                    }
                    
                    $insert_user    = SalesRegionalManagerOrn::firstOrCreate($sales_regional_manager_array);
                    $result         = $insert_user->sales_regional_managers_orn_id;
                    if ($result) 
                    {
                        foreach($sales_regional_state AS $state_id) {
                            $sales_regional_manager_states   = array(
                                                            'sales_regional_manager_id'  => $sales_regional_id,
                                                            'state_id'          => $state_id,
                                                            'status'            => '1',
                                                            'orn_id' => $result
                                                            );
                            $insert_sales_regional_manager   = SalesRegionalManagerStateOrn::firstOrCreate($sales_regional_manager_states);
                        }   
                        
                        foreach($product_category AS $category_id) {
                            $regional_manager_states   = array(
                                                            'sales_regional_manager_id' => $sales_regional_id,
                                                            'product_crop_category_id'  => $category_id,
                                                            'status'                    => '1',
                                                            'orn_id' => $result
                                                            );
                            $insert_regional_manager   = SalesRegionalManagerProductCategoryOrn::firstOrCreate($regional_manager_states);
                        }
                    
                    
                        Session::flash('message', "Sales Regional Manager Successfully Updated.");
                        return redirect('salesregional/sales-regional-manager-list-orn');
                    }
                    else
                    {   
                        Session::flash('message', "Please try again.");
                        return redirect('salesregional/sales-regional-manager-list-orn');
                    }
                }
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }
    // public function postUpdateSalesRegionalManagerOrn(Request $request) {         
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data= Input::all();        
    //         $rules  = array(
    //                         'zonal_manager'                     => 'required|numeric',
    //                         'sales_regional_manager_hq_code'    => 'required|string',
    //                         'sales_regional_name'               => 'required|string',
    //                         'sales_regional_mobile_number'      => 'required|numeric',
    //                         'sales_regional_state'              => 'required|array',
    //                         'sales_regional_pincode'            => 'required|numeric',
    //                         'product_category'          => 'required|array'
    //                     );
    //         $validator  = Validator::make($my_data, $rules);
    //         if ($validator->fails()) {
    //             return Redirect::back()->withInput()->withErrors($validator);
    //         }
            
    //         $sales_regional_id                  = $my_data['sales_regional_id'];
    //         $zonal_manager                      = $my_data['zonal_manager'];
    //         $sales_regional_manager_hq_code     = $my_data['sales_regional_manager_hq_code'];
    //         $sales_regional_name                = $my_data['sales_regional_name'];
    //         $sales_regional_mobile_number       = $my_data['sales_regional_mobile_number'];
    //         $sales_regional_state               = $my_data['sales_regional_state'];
    //         $sales_regional_pincode             = $my_data['sales_regional_pincode'];
    //         $product_category                   = $my_data['product_category'];
    //         $change_password                    = isset($my_data['change_password'])?'1':'0';
    //         if(count($sales_regional_state)>0) {
    //             $check_sales_regional_manager_code    = SalesRegionalManager::where('sales_regional_manager_id', '!=', $sales_regional_id)
    //                                                     ->where('ECN', $sales_regional_manager_hq_code)
    //                                                     ->count();
    //             $query  =   "
    //                         (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT id AS id FROM users WHERE ECN='".$sales_regional_manager_hq_code."' LIMIT 0,1)
    //                         ";
    //             $user   = DB::select(DB::raw($query));
    //             if($check_sales_regional_manager_code>0 || count($user)>0) {
    //                 Session::flash('message', "ECN already exists, Please choose a different ECN.");
    //                 return Redirect::back()->withInput()->withErrors($validator);
    //             }
    //             else {
    //                 $sales_regional_manager_array    = array(
    //                                                     'ECN'                               => $sales_regional_manager_hq_code,
    //                                                     'zonal_manager_id'                  => $zonal_manager,
    //                                                     'sales_regional_manager_hq_code'    => $sales_regional_manager_hq_code,
    //                                                     'sales_regional_manager_name'       => $sales_regional_name,
    //                                                     'sales_regional_manager_mobile'     => $sales_regional_mobile_number,
    //                                                     'sales_regional_manager_pincode'    => $sales_regional_pincode,
    //                                                     'updated_at'                        => date('Y-m-d H:i:s')
    //                                                 );
    //                 if($change_password=='1'){
    //                     $random_string                              = $this->generateRandomString(8);
    //                     $password                                   = \Hash::make($random_string);
    //                     $sales_regional_manager_array['password']   = $password;
    //                     $sales_regional_manager_array['dash']       = $random_string;
    //                 }
                    
                    
    //                 $update_sales_regional_manager  = DB::table('sales_regional_managers')
    //                                                     ->where('sales_regional_manager_id', $sales_regional_id)
    //                                                     ->where('status', '1')
    //                                                     ->update($sales_regional_manager_array); 
    //                 $update_sales_area_manager      = DB::table('sales_area_managers')
    //                                                     ->where('sales_regional_manager_id', $sales_regional_id)
    //                                                     ->where('status', '1')
    //                                                     ->update(['zonal_manager_id'=>$zonal_manager]); 
    //                 $update_bdo                     = DB::table('users')
    //                                                     ->where('sales_regional_manager_id', $sales_regional_id)
    //                                                     ->where('status', '1')
    //                                                     ->update(['zonal_manager_id'=>$zonal_manager]); 
                    
                    
    //                 $reset_sales_regional_manager_states   = DB::table('sales_regional_manager_states')
    //                                                         ->where('sales_regional_manager_id', $sales_regional_id)
    //                                                         ->update(['status'=>'0']); 
    //                 foreach($sales_regional_state AS $state_id) {
    //                     $update_sales_regional_manager   = DB::table('sales_regional_manager_states')
    //                                                 ->where('sales_regional_manager_id', $sales_regional_id)
    //                                                 ->where('state_id', $state_id)
    //                                                 ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
    //                     $sales_regional_manager_states   = array(
    //                                                     'sales_regional_manager_id'  => $sales_regional_id,
    //                                                     'state_id'          => $state_id,
    //                                                     'status'            => '1'
    //                                                     );
    //                     $insert_sales_regional_manager   = SalesRegionalManagerState::firstOrCreate($sales_regional_manager_states);
    //                 }   
                    
                    
                    
                
    //                 $reset_regional_manager_category= DB::table('sales_regional_manager_product_categories')
    //                                                 ->where('sales_regional_manager_id', $sales_regional_id)
    //                                                 ->update(['status'=>'0']); 
    //                 foreach($product_category AS $category_id) {
    //                     $update_regional_manager   = DB::table('sales_regional_manager_product_categories')
    //                                                 ->where('sales_regional_manager_id', $sales_regional_id)
    //                                                 ->where('product_crop_category_id', $category_id)
    //                                                 ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
    //                     $regional_manager_states   = array(
    //                                                     'sales_regional_manager_id' => $sales_regional_id,
    //                                                     'product_crop_category_id'  => $category_id,
    //                                                     'status'                    => '1'
    //                                                     );
    //                     $insert_regional_manager   = SalesRegionalManagerProductCategory::firstOrCreate($regional_manager_states);
    //                 }
                
                
    //                 Session::flash('message', "Sales Regional Manager Successfully Updated.");
    //                 return redirect('salesregional/sales-regional-manager-list');
    //             }
    //         }
    //         else {
    //             Session::flash('message', "Please choose state and try again.");
    //             return Redirect::back()->withInput()->withErrors($validator);
    //         }
    //     }
    // }
    // public function postSalesRegionalDeleteOrn(Request $request) {         
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data= Input::all();        
    //         $rules  = array(
    //                         '_ticket'    => 'required|numeric'
    //                     );
    //         $validator  = Validator::make($my_data, $rules);
    //         if ($validator->fails()) {
    //             echo '0';
    //         }
    //         else {
    //             $sales_regional_manager_id     = $my_data['_ticket'];
    //             $update_sales_regional_manager = DB::table('sales_regional_managers')
    //                                                 ->where('sales_regional_manager_id', $sales_regional_manager_id)
    //                                                 ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
    //             if ($update_sales_regional_manager) {
    //                 echo 1;
    //             } else {
    //                 echo 2;
    //             }
    //         }
    //     }
    // } 
}