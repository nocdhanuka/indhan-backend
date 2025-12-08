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
use App\RegionalManagerProductCategoryOrn;
use App\SalesRegionalManager;
use App\SalesRegionalManagerState;
use App\SalesRegionalManagerProductCategory;
use App\SalesRegionalManagerProductCategoryOrn;
use App\AreaManager;
use App\AreaManagerState;
use App\AreaManagerProductCategory;
use App\AreaManagerProductCategoryOrn;
use App\SalesAreaManager;
use App\SalesAreaManagerOrn;
use App\SalesAreaManagerState;
use App\SalesAreaManagerStateOrn;
use App\SalesAreaManagerProductCategory;
use App\SalesAreaManagerProductCategoryOrn;
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

class salesAreaManagerController extends Controller {
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
    public function postCheckSalesAreaEcn(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();             
            if($my_data) {
                $sales_area_manager_hq_code     = $my_data['sales_area_manager_hq_code'];
                $sales_area_manager_id          = $my_data['_sales_area'];
                $check_sales_area_manager_code  = SalesAreaManager::where('sales_area_manager_id', '!=', $sales_area_manager_id)
                                                        ->where('ECN', $sales_area_manager_hq_code)
                                                        ->count();
                
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT dd_id AS id FROM dhanuka_doctor WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                
                echo ($check_sales_area_manager_code>0 || count($user)>0?'false':'true');die;
            }
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $my_data    = Input::all();             
            if($my_data) {
                $sales_area_manager_hq_code     = $my_data['sales_area_manager_hq_code'];
                $sales_area_manager_id          = $my_data['_sales_area'];
                $check_sales_area_manager_code  = SalesAreaManager::where('sales_area_manager_id', '!=', $sales_area_manager_id)
                                                        ->where('ECN', $sales_area_manager_hq_code)
                                                        ->count();
                
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT dd_id AS id FROM dhanuka_doctor WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                
                echo ($check_sales_area_manager_code>0 || count($user)>0?'false':'true');die;
            }
        }
    } 
    public function getAddSalesAreaManager() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $category_list                  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $zonal_manager_list             = ZonalManager::where('status', '1')->orderBy('zonal_manager_name', 'asc')->get();
        $state_list                     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        return View::make('backend.sales_area_manager.addsalesareamanager', array('state_list'=>$state_list, 'zonal_manager_list'=>$zonal_manager_list, 'category_list'=>$category_list));
    }    
    public function postZonalRegional(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();     
            $zonal_manager  = $my_data['zonal_manager'];   
            $regional_list  = RegionalManager::select('regional_manager_id', 'regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
            
            if(count($regional_list)>0) {
                echo json_encode($regional_list, true);die;
            }            
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData1) {
            $my_data        = Input::all();     
            $zonal_manager  = $my_data['zonal_manager'];   
            $regional_list  = RegionalManager::select('regional_manager_id', 'regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
            
            if(count($regional_list)>0) {
                echo json_encode($regional_list, true);die;
            }            
        }
    } 
    public function postZonalSalesRegional(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data                = Input::all();     
            $zonal_manager          = $my_data['zonal_manager'];   
            $sales_regional_list    = SalesRegionalManager::select('sales_regional_manager_id', 'sales_regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('sales_regional_manager_name', 'ASC')->get()->toArray();
            
            if(count($sales_regional_list)>0) {
                echo json_encode($sales_regional_list, true);die;
            }            
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData1) {
            $my_data                = Input::all();     
            $zonal_manager          = $my_data['zonal_manager'];   
            $sales_regional_list    = SalesRegionalManager::select('sales_regional_manager_id', 'sales_regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('sales_regional_manager_name', 'ASC')->get()->toArray();
            
            if(count($sales_regional_list)>0) {
                echo json_encode($sales_regional_list, true);die;
            }            
        }
    } 
    public function postCreateSalesAreaManager(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();   
            $rules  = array(
                            'zonal_manager'                 => 'required|numeric',
                            'regional_manager'              => 'required|numeric',
                            'sales_regional_manager'        => 'required|numeric',
                            'sales_area_manager_hq_code'    => 'required|string',
                            'sales_area_name'               => 'required|string',
                            'sales_area_mobile_number'      => 'required|numeric',
                            'sales_area_state'              => 'required|array',
                            'sales_area_pincode'            => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $zonal_manager                  = $my_data['zonal_manager'];
            $regional_manager               = $my_data['regional_manager'];
            $sales_regional_manager         = $my_data['sales_regional_manager'];
            $sales_area_manager_hq_code     = $my_data['sales_area_manager_hq_code'];
            $sales_area_name                = $my_data['sales_area_name'];
            $sales_area_mobile_number       = $my_data['sales_area_mobile_number'];
            $sales_area_state               = $my_data['sales_area_state'];
            $sales_area_pincode             = $my_data['sales_area_pincode'];
            $product_category               = $my_data['product_category'];
            if(count($sales_area_state)>0) {               
                $check_sales_area_manager_code  = SalesAreaManager::where('ECN', $sales_area_manager_hq_code)->count();
                if($check_sales_area_manager_code>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $adminController        = new adminController;
                    $random_string          = $adminController->generateRandomString(8);
                    $password               = \Hash::make($random_string);
                    $sales_area_manager_array   = array(
                                                    'ECN'                               => $sales_area_manager_hq_code,
                                                    'dash'                              => $random_string,
                                                    'password'                          => $password,
                                                    'zonal_manager_id'                  => $zonal_manager,
                                                    'regional_manager_id'               => $regional_manager,
                                                    'sales_regional_manager_id'         => $sales_regional_manager,
                                                    'sales_area_manager_hq_code'        => $sales_area_manager_hq_code,
                                                    'sales_area_manager_name'           => $sales_area_name,
                                                    'sales_area_manager_mobile'         => $sales_area_mobile_number,
                                                    'sales_area_manager_pincode'        => $sales_area_pincode,
                                                    'status'                            => '1'
                                                );
                    $insert_sales_area_manager  = SalesAreaManager::firstOrCreate($sales_area_manager_array);
                    $result                     = $insert_sales_area_manager->sales_area_manager_id;

                    if($result) {
                        $sales_area_manager_states   = array();
                        foreach($sales_area_state AS $state_id) {
                            $sales_area_manager_states[] = array(
                                                            'sales_area_manager_id' => $result,
                                                            'state_id'              => $state_id,
                                                            'status'                => '1'
                                                            );
                        }                    
                        $insert_sales_area_manager_states= SalesAreaManagerState::insert($sales_area_manager_states);
                        
                         
                        $sales_area_manager_product_category = array();
                        foreach($product_category AS $product_category_id) {
                            $sales_area_manager_product_category[]   = array(
                                                                            'sales_area_manager_id'     => $result,
                                                                            'product_crop_category_id'  => $product_category_id,
                                                                            'status'                    => '1'
                                                                            );
                        }
                        $insert_sales_area_manager_product_category  = SalesAreaManagerProductCategory::insert($sales_area_manager_product_category);

                        Session::flash('message', "Area Sales Manager Successfully Created.");
                        return redirect('salesarea/sales-area-manager-list');
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
    public function getSalesAreaManagerList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.sales_area_manager.salesareamanagerlist');
    } 
    public function postAllsalesareamanagerlist(Request $request) {
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
                $zonalId = " and sam.status = '".$status."'";
            }
            else{
                $zonalId = "";
            }
        }

        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        if ($userData1) {
            if ($status != '') {
                $zonalId = "and sam.zonal_manager_id = ".$userData1->zonal_manager_id."' and sam.status = '".$status."'";
            }
            else{
                $zonalId = "and sam.zonal_manager_id = ".$userData1->zonal_manager_id;
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
                                    sam.sales_area_manager_hq_code,
                                    zm.zonal_manager_name,
                                    rm.regional_manager_name,
                                    srm.sales_regional_manager_name,
                                    sam.sales_area_manager_name,
                                    sam.sales_area_manager_mobile,
                                    sam.sales_area_manager_pincode,  
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN sam.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    sam.ECN,
                                    sam.dash,                                    
                                    DATE_FORMAT(sam.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,                                     
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,                                    
                                    sam.sales_area_manager_id,
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        sales_area_managers         sam
                                LEFT JOIN   active_logs                 al      ON sam.sales_area_manager_id=al.user_id AND al.user_type='sales_area'  
                                LEFT JOIN   zonal_managers              zm      ON zm.zonal_manager_id=sam.zonal_manager_id
                                LEFT JOIN   sales_area_manager_states   sams    ON sams.sales_area_manager_id=sam.sales_area_manager_id
                                LEFT JOIN   regional_managers           rm      ON rm.regional_manager_id=sam.regional_manager_id
                                LEFT JOIN   sales_regional_managers     srm     ON srm.sales_regional_manager_id=sam.sales_regional_manager_id
                                LEFT JOIN   states                      s       ON sams.state_id=s.state_id                                    
                                
                                LEFT JOIN   sales_area_manager_product_categories   sampc    ON sampc.sales_area_manager_id=sam.sales_area_manager_id and sampc.status=1
                                LEFT JOIN   product_crop_categories                 pcc     ON pcc.product_crop_category_id=sampc.product_crop_category_id
                                
                                WHERE
                                    1=1 ".$zonalId."        
                                GROUP BY sams.sales_area_manager_id
                                )   A,
                                (select @a:=0) initvars";
            $sales_area_manager  = collect(DB::select($query));  
            foreach ($sales_area_manager as $key => $value) {
                $states = DB::table('sales_area_manager_states as zms')
                          ->join('states as s','s.state_id','=','zms.state_id')  
                          ->where('sales_area_manager_id',$sales_area_manager[$key]->sales_area_manager_id)
                          ->where('zms.status','1')
                          ->select(DB::raw("GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n') as state"))
                          ->get();
                if (count($states)>0) 
                {
                    $sales_area_manager[$key]->states = $states[0]->state;
                }         
            } 
            return Datatables::of($sales_area_manager)->make();
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
                                sam.sales_area_manager_hq_code,
                                zm.zonal_manager_name,
                                rm.regional_manager_name,
                                srm.sales_regional_manager_name,
                                sam.sales_area_manager_name,
                                sam.sales_area_manager_mobile,
                                sam.sales_area_manager_pincode,  
                                GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                (CASE WHEN sam.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                sam.ECN,
                                sam.dash,                                    
                                DATE_FORMAT(sam.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,                                     
                                DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,                                    
                                sam.sales_area_manager_id,
                                GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                            FROM        sales_area_managers         sam
                            LEFT JOIN   active_logs                 al      ON sam.sales_area_manager_id=al.user_id AND al.user_type='sales_area' 
                            LEFT JOIN   zonal_managers              zm      ON zm.zonal_manager_id=sam.zonal_manager_id
                            LEFT JOIN   sales_area_manager_states   sams    ON sams.sales_area_manager_id=sam.sales_area_manager_id
                            LEFT JOIN   regional_managers           rm      ON rm.regional_manager_id=sam.regional_manager_id
                            LEFT JOIN   sales_regional_managers     srm     ON srm.sales_regional_manager_id=sam.sales_regional_manager_id
                            LEFT JOIN   states                      s       ON sams.state_id=s.state_id                                    

                            LEFT JOIN   sales_area_manager_product_categories   sampc    ON sampc.sales_area_manager_id=sam.sales_area_manager_id  and sampc.status=1
                            LEFT JOIN   product_crop_categories                 pcc     ON pcc.product_crop_category_id=sampc.product_crop_category_id

                            WHERE
                            (                                
                            sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                            sam.sales_area_manager_mobile       LIKE '%".$search."%'    OR
                            sam.sales_area_manager_pincode      LIKE '%".$search."%'    OR
                            sam.ECN                             LIKE '%".$search."%'    OR
                            s.state                             LIKE '%".$search."%'
                            ) ".$zonalId." 
                        GROUP BY sams.sales_area_manager_id
                        )   A,
                        (select @a:=0) initvars";
            
            $sales_area_manager  = collect(DB::select($query));
            foreach ($sales_area_manager as $key => $value) {
                $states = DB::table('sales_area_manager_states as zms')
                          ->join('states as s','s.state_id','=','zms.state_id')  
                          ->where('sales_area_manager_id',$sales_area_manager[$key]->sales_area_manager_id)
                          ->where('zms.status','1')
                          ->select(DB::raw("GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n') as state"))
                          ->get();
                if (count($states)>0) 
                {
                    $sales_area_manager[$key]->states = $states[0]->state;
                }         
            } 
            return Datatables::of($sales_area_manager)->make();            
        }
    }   
    public function getEditSalesAreaManager($sales_area_manager_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($sales_area_manager_id!='') {
                $zonal_manager              = ZonalManager::where('status', '1')->get();
                $regional_manager           = RegionalManager::where('status', '1')->get();
                $sales_regional_manager     = SalesRegionalManager::where('status', '1')->get();
                $sales_area_manager         = SalesAreaManager::where('sales_area_manager_id', $sales_area_manager_id)->first();
                $sales_area_manager_state   = SalesAreaManagerState::select(DB::raw("GROUP_CONCAT(state_id SEPARATOR ',')  AS state_id"))->where('sales_area_manager_id', $sales_area_manager_id)->where('status', '1')->first();
                $state_list                 = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                
                $sales_area_manager_product_category  = SalesAreaManagerProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('sales_area_manager_id', $sales_area_manager_id)->where('status', '1')->first();
                $category_list                  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
                
                return View::make('backend.sales_area_manager.editsalesareamanager', array('zonal_manager_list'=>$zonal_manager,'regional_manager_list'=>$regional_manager,'sales_regional_manager_list'=>$sales_regional_manager, 'sales_area_manager_list'=>$sales_area_manager, 'sales_area_manager_state'=>$sales_area_manager_state, 'state_list'=>$state_list, 'category_list'=>$category_list, 'sales_area_manager_category_list'=>$sales_area_manager_product_category)); 
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }    
    public function postUpdateSalesAreaManager(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();       
            $rules  = array(
                            'zonal_manager'                 => 'required|numeric',
                            'regional_manager'              => 'required|numeric',
                            'sales_regional_manager'        => 'required|numeric',
                            'sales_area_manager_hq_code'    => 'required|string',
                            'sales_area_name'               => 'required|string',
                            'sales_area_mobile_number'      => 'required|numeric',
                            'sales_area_state'              => 'required|array',
                            'sales_area_pincode'            => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $sales_area_id                  = $my_data['sales_area_id'];
            $zonal_manager                  = $my_data['zonal_manager'];
            $regional_manager               = $my_data['regional_manager'];
            $sales_regional_manager         = $my_data['sales_regional_manager'];
            $sales_area_manager_hq_code     = $my_data['sales_area_manager_hq_code'];
            $sales_area_name                = $my_data['sales_area_name'];
            $sales_area_mobile_number       = $my_data['sales_area_mobile_number'];
            $sales_area_state               = $my_data['sales_area_state'];
            $sales_area_pincode             = $my_data['sales_area_pincode'];
            $product_category               = $my_data['product_category'];
            $change_password                = isset($my_data['change_password'])?'1':'0';
            
            if(count($sales_area_state)>0) {
                $check_sales_area_manager_code    = SalesAreaManager::where('sales_area_manager_id', '!=', $sales_area_id)
                                                        ->where('ECN', $sales_area_manager_hq_code)
                                                        ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                if($check_sales_area_manager_code>0 || count($user)>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $sales_area_manager_array    = array(
                                                    'ECN'                               => $sales_area_manager_hq_code,
                                                    'zonal_manager_id'                  => $zonal_manager,
                                                    'regional_manager_id'               => $regional_manager,
                                                    'sales_regional_manager_id'         => $sales_regional_manager,
                                                    'sales_area_manager_hq_code'        => $sales_area_manager_hq_code,
                                                    'sales_area_manager_name'           => $sales_area_name,
                                                    'sales_area_manager_mobile'         => $sales_area_mobile_number,
                                                    'sales_area_manager_pincode'        => $sales_area_pincode,
                                                    'status'                            => '1',
                                                    'updated_at'                        => date('Y-m-d H:i:s')
                                                );
                    
                    if($change_password=='1'){
                        $random_string                          = $this->generateRandomString(8);
                        $password                               = \Hash::make($random_string);
                        $sales_area_manager_array['password']   = $password;
                        $sales_area_manager_array['dash']       = $random_string;
                    }
                    
                    $update_sales_area_manager  = DB::table('sales_area_managers')
                                                    ->where('sales_area_manager_id', $sales_area_id)
                                                    ->where('status', '1')
                                                    ->update($sales_area_manager_array); 

                    
                    $update_bdo                 = DB::table('users')
                                                    ->where('sales_regional_manager_id', $sales_regional_manager)
                                                    ->where('status', '1')
                                                    ->update(['zonal_manager_id'=>$zonal_manager, 'regional_manager_id'=>$regional_manager]); 
                    
                    
                    $reset_sales_area_manager_states   = DB::table('sales_area_manager_states')
                                                        ->where('sales_area_manager_id', $sales_area_id)
                                                        ->update(['status'=>'0', 'updated_at' => date('Y-m-d H:i:s')]); 
                    
                    foreach($sales_area_state AS $state_id) {

                        $update_sales_area_manager      = DB::table('sales_area_manager_states')
                                                        ->where('sales_area_manager_id', $sales_area_id)
                                                        ->where('state_id', $state_id)
                                                        ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $sales_area_manager_states   = array(
                                                        'sales_area_manager_id' => $sales_area_id,
                                                        'state_id'              => $state_id,
                                                        'status'                => '1'
                                                        );
                        $insert_sales_area_manager   = SalesAreaManagerState::firstOrCreate($sales_area_manager_states);
                        
                    }  
                    
                    
                    
                    $reset_sales_area_manager_category= DB::table('sales_area_manager_product_categories')
                                                        ->where('sales_area_manager_id', $sales_area_id)
                                                        ->update(['status'=>'0']); 
                    foreach($product_category AS $category_id) {
                        $update_sales_area_manager  = DB::table('sales_area_manager_product_categories')
                                                        ->where('sales_area_manager_id', $sales_area_id)
                                                        ->where('product_crop_category_id', $category_id)
                                                        ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $sales_area_manager_states  = array(
                                                        'sales_area_manager_id'       => $sales_area_id,
                                                        'product_crop_category_id'  => $category_id,
                                                        'status'                    => '1'
                                                        );
                        $insert_sales_area_manager        = SalesAreaManagerProductCategory::firstOrCreate($sales_area_manager_states);
                    }
                    Session::flash('message', "Area Sales Manager Successfully Updated.");
                    return redirect('salesarea/sales-area-manager-list');
                }                
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    } 
    public function postUpdateSalesAreaStatus(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $sales_area_manager_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $sales_area_manager_data    = SalesAreaManager::where('sales_area_manager_id', $sales_area_manager_id)->first();
            if($sales_area_manager_data) {
                $sales_area_manager_data_status = ($sales_area_manager_data->status=='0'?'1':'0');
                $update_sales_area_manager      = DB::table('sales_area_managers')
                                                ->where('sales_area_manager_id', $sales_area_manager_id)
                                                ->update(['status'=>$sales_area_manager_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    public function postSalesAreaDelete(Request $request) {         
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
                $sales_area_manager_id     = $my_data['_ticket'];
                $update_sales_area_manager = DB::table('sales_area_managers')
                                            ->where('sales_area_manager_id', $sales_area_manager_id)
                                            ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_sales_area_manager) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    } 

    //Organogram ===================
    public function postZonalRegionalOrn(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();     
            $zonal_manager  = $my_data['zonal_manager'];   
            $regional_list  = RegionalManager::select('regional_manager_id', 'regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
            
            if(count($regional_list)>0) {
                echo json_encode($regional_list, true);
            }            
        }
    } 
    public function postZonalSalesRegionalOrn(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data                = Input::all();     
            $zonal_manager          = $my_data['zonal_manager'];   
            $sales_regional_list    = SalesRegionalManager::select('sales_regional_manager_id', 'sales_regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('sales_regional_manager_name', 'ASC')->get()->toArray();
            
            if(count($sales_regional_list)>0) {
                echo json_encode($sales_regional_list, true);
            }            
        }
    } 
    public function getAddSalesAreaManagerOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        $category_list                  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $zonal_manager_list             = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->orderBy('zonal_manager_name', 'asc')->get();
        $state_list                     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        return View::make('backend.organogram_sales_area_manager.addsalesareamanager', array('state_list'=>$state_list, 'zonal_manager_list'=>$zonal_manager_list, 'category_list'=>$category_list));
    } 
    public function postCreateSalesAreaManagerOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data= Input::all();   
            $rules  = array(
                            'zonal_manager'                 => 'required|numeric',
                            'regional_manager'              => 'required|numeric',
                            'sales_regional_manager'        => 'required|numeric',
                            'sales_area_manager_hq_code'    => 'required|string',
                            'sales_area_name'               => 'required|string',
                            'sales_area_mobile_number'      => 'required|numeric',
                            'sales_area_state'              => 'required|array',
                            'sales_area_pincode'            => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $zonal_manager                  = $my_data['zonal_manager'];
            $regional_manager               = $my_data['regional_manager'];
            $sales_regional_manager         = $my_data['sales_regional_manager'];
            $sales_area_manager_hq_code     = $my_data['sales_area_manager_hq_code'];
            $sales_area_name                = $my_data['sales_area_name'];
            $sales_area_mobile_number       = $my_data['sales_area_mobile_number'];
            $sales_area_state               = $my_data['sales_area_state'];
            $sales_area_pincode             = $my_data['sales_area_pincode'];
            $product_category               = $my_data['product_category'];
            if(count($sales_area_state)>0) {               
                $check_sales_area_manager_code  = SalesAreaManager::where('ECN', $sales_area_manager_hq_code)->count();
                if($check_sales_area_manager_code>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $adminController        = new adminController;
                    $random_string          = $adminController->generateRandomString(8);
                    $password               = \Hash::make($random_string);
                    $sales_area_manager_array   = array(
                                                    'ECN'                               => $sales_area_manager_hq_code,
                                                    'dash'                              => $random_string,
                                                    'password'                          => $password,
                                                    'zonal_manager_id'                  => $zonal_manager,
                                                    'regional_manager_id'               => $regional_manager,
                                                    'sales_regional_manager_id'         => $sales_regional_manager,
                                                    'sales_area_manager_hq_code'        => $sales_area_manager_hq_code,
                                                    'sales_area_manager_name'           => $sales_area_name,
                                                    'sales_area_manager_mobile'         => $sales_area_mobile_number,
                                                    'sales_area_manager_pincode'        => $sales_area_pincode,
                                                    'status'                            => '1',
                                                    'updated_at'                        => date('Y-m-d H:i:s')
                                                );
                    $insert_sales_area_manager  = SalesAreaManagerOrn::firstOrCreate($sales_area_manager_array);
                    $result                     = $insert_sales_area_manager->sales_area_manager_id;

                    if($result) {
                        $sales_area_manager_states   = array();
                        foreach($sales_area_state AS $state_id) {
                            $sales_area_manager_states[] = array(
                                                            'sales_area_manager_id' => $result,
                                                            'state_id'              => $state_id,
                                                            'status'                => '1',
                                                            'orn_id' => $result
                                                            );
                        }                    
                        $insert_sales_area_manager_states= SalesAreaManagerStateOrn::insert($sales_area_manager_states);
                        
                         
                        $sales_area_manager_product_category = array();
                        foreach($product_category AS $product_category_id) {
                            $sales_area_manager_product_category[]   = array(
                                                                            'sales_area_manager_id'     => $result,
                                                                            'product_crop_category_id'  => $product_category_id,
                                                                            'status'                    => '1',
                                                                            'orn_id' => $result
                                                                            );
                        }
                        $insert_sales_area_manager_product_category  = SalesAreaManagerProductCategoryOrn::insert($sales_area_manager_product_category);

                        Session::flash('message', "Area Sales Manager Successfully Created. Wait for admin approvel");
                        return redirect('salesarea/sales-area-manager-list-orn');
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
    public function getSalesAreaManagerListOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.organogram_sales_area_manager.salesareamanagerlist');
    } 
    public function postAllsalesareamanagerlistorn(Request $request) {
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
                                    sam.sales_area_manager_hq_code,
                                    zm.zonal_manager_name,
                                    rm.regional_manager_name,
                                    srm.sales_regional_manager_name,
                                    sam.sales_area_manager_name,
                                    sam.sales_area_manager_mobile,
                                    sam.sales_area_manager_pincode,  
                                    GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS state,
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN sam.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    sam.ECN,
                                    sam.dash,                                    
                                    DATE_FORMAT(sam.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,                                     
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,                                    
                                    sam.sales_area_manager_id,
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        sales_area_managers         sam
                                LEFT JOIN   active_logs                 al      ON sam.sales_area_manager_id=al.user_id AND al.user_type='sales_area'  
                                LEFT JOIN   zonal_managers              zm      ON zm.zonal_manager_id=sam.zonal_manager_id
                                LEFT JOIN   sales_area_manager_states   sams    ON sams.sales_area_manager_id=sam.sales_area_manager_id
                                LEFT JOIN   regional_managers           rm      ON rm.regional_manager_id=sam.regional_manager_id
                                LEFT JOIN   sales_regional_managers     srm     ON srm.sales_regional_manager_id=sam.sales_regional_manager_id
                                LEFT JOIN   states                      s       ON sams.state_id=s.state_id                                    
                                
                                LEFT JOIN   sales_area_manager_product_categories   sampc    ON sampc.sales_area_manager_id=sam.sales_area_manager_id and sampc.status=1
                                LEFT JOIN   product_crop_categories                 pcc     ON pcc.product_crop_category_id=sampc.product_crop_category_id
                                
                                WHERE sam.zonal_manager_id = '".$zonalId->zonal_manager_id."'        
                                GROUP BY sams.sales_area_manager_id
                                )   A,
                                (select @a:=0) initvars";
            $sales_area_manager  = collect(DB::select($query));
            if (count($sales_area_manager)>0) {
                foreach ($sales_area_manager as $key => $value) {
                  $checkStatus = DB::table('sales_area_managers_orn')
                                 ->where('sales_area_manager_id',$sales_area_manager[$key]->sales_area_manager_id)
                                 ->where('orn_status','0')
                                 ->get();
                    if (count($checkStatus)>0) {
                        if ($sales_area_manager[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $sales_area_manager[$key]->status = 'ACTIVE';
                        }
                        elseif ($sales_area_manager[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $sales_area_manager[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $sales_area_manager[$key]->status = 'PENDING';
                        }
                    }                
                }               
            }          
            return Datatables::of($sales_area_manager)->make();
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
                                sam.sales_area_manager_hq_code,
                                zm.zonal_manager_name,
                                rm.regional_manager_name,
                                srm.sales_regional_manager_name,
                                sam.sales_area_manager_name,
                                sam.sales_area_manager_mobile,
                                sam.sales_area_manager_pincode,  
                                GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS state,
                                GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                (CASE WHEN sam.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                sam.ECN,
                                sam.dash,                                    
                                DATE_FORMAT(sam.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,                                     
                                DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,                                    
                                sam.sales_area_manager_id,
                                GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                            FROM        sales_area_managers         sam
                            LEFT JOIN   active_logs                 al      ON sam.sales_area_manager_id=al.user_id AND al.user_type='sales_area' 
                            LEFT JOIN   zonal_managers              zm      ON zm.zonal_manager_id=sam.zonal_manager_id
                            LEFT JOIN   sales_area_manager_states   sams    ON sams.sales_area_manager_id=sam.sales_area_manager_id
                            LEFT JOIN   regional_managers           rm      ON rm.regional_manager_id=sam.regional_manager_id
                            LEFT JOIN   sales_regional_managers     srm     ON srm.sales_regional_manager_id=sam.sales_regional_manager_id
                            LEFT JOIN   states                      s       ON sams.state_id=s.state_id                                    

                            LEFT JOIN   sales_area_manager_product_categories   sampc    ON sampc.sales_area_manager_id=sam.sales_area_manager_id  and sampc.status=1
                            LEFT JOIN   product_crop_categories                 pcc     ON pcc.product_crop_category_id=sampc.product_crop_category_id

                            WHERE
                            (                                
                            sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                            sam.sales_area_manager_mobile       LIKE '%".$search."%'    OR
                            sam.sales_area_manager_pincode      LIKE '%".$search."%'    OR
                            sam.ECN                             LIKE '%".$search."%'    OR
                            s.state                             LIKE '%".$search."%'
                            ) 
                        GROUP BY sams.sales_area_manager_id
                        )   A,
                        (select @a:=0) initvars";
            
            $sales_area_manager  = collect(DB::select($query));
            if (count($sales_area_manager)>0) {
                foreach ($sales_area_manager as $key => $value) {
                    $checkStatus = DB::table('users_orn')
                                   ->where('sales_area_manager_id',$sales_area_manager[$key]->sales_area_manager_id)
                                   ->where('orn_status','0')
                                   ->get();
                    if (count($checkStatus)>0) {
                        if ($sales_area_manager[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $sales_area_manager[$key]->status = 'ACTIVE';
                        }
                        elseif ($sales_area_manager[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $sales_area_manager[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $sales_area_manager[$key]->status = 'PENDING';
                        }
                    }                
                }               
            }  
            return Datatables::of($sales_area_manager)->make();            
        }
    }   
    public function postUpdateSalesAreaStatusOrn(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $sales_area_manager_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $sales_area_manager_data    = SalesAreaManager::where('sales_area_manager_id', $sales_area_manager_id)->first();
            if($sales_area_manager_data) {
                $sales_area_manager_data_status = ($sales_area_manager_data->status=='0'?'1':'0');
                $insert_asm     = array(       
                                        'sales_area_manager_id' => $sales_area_manager_data->sales_area_manager_id,
                                        'ECN' => $sales_area_manager_data->ECN,
                                        'dash' => $sales_area_manager_data->dash,
                                        'password' => $sales_area_manager_data->password,
                                        'api_token' => $sales_area_manager_data->api_token,
                                        'zonal_manager_id' => $sales_area_manager_data->zonal_manager_id,
                                        'regional_manager_id' => $sales_area_manager_data->regional_manager_id,
                                        'sales_regional_manager_id' => $sales_area_manager_data->sales_regional_manager_id,
                                        'sales_area_manager_hq_code' => $sales_area_manager_data->sales_area_manager_hq_code,
                                        'sales_area_manager_name' => $sales_area_manager_data->sales_area_manager_name,
                                        'sales_area_manager_mobile' => $sales_area_manager_data->sales_area_manager_mobile,
                                        'sales_area_manager_pincode' => $sales_area_manager_data->sales_area_manager_pincode,
                                        'status' => $sales_area_manager_data_status,
                                        'device_id' => $sales_area_manager_data->device_id,
                                        'created_at' => $sales_area_manager_data->created_at,
                                        'updated_at'    => date('Y-m-d H:i:s')
                                            ); 
                $insert_user    = SalesAreaManagerOrn::firstOrCreate($insert_asm);
                $result         = $insert_user->sales_area_managers_orn_id;
                if($result) {
                    echo '1';
                }
                else
                {
                    echo '2';
                }
                // $update_sales_area_manager      = DB::table('sales_area_managers')
                //                                 ->where('sales_area_manager_id', $sales_area_manager_id)
                //                                 ->update(['status'=>$sales_area_manager_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
                //echo '1';
            }
            else {
                echo '0';
            }
        }
        else {
            echo '0';
        }
    } 
    public function getEditSalesAreaManagerOrn($sales_area_manager_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($sales_area_manager_id!='') {
                $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
                $zonal_manager             = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
                $regional_manager           = RegionalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
                $sales_regional_manager     = SalesRegionalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
                $sales_area_manager         = SalesAreaManager::where('sales_area_manager_id', $sales_area_manager_id)->first();
                $sales_area_manager_state   = SalesAreaManagerState::select(DB::raw("GROUP_CONCAT(state_id SEPARATOR ',')  AS state_id"))->where('sales_area_manager_id', $sales_area_manager_id)->where('status', '1')->first();
                $state_list                 = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                
                $sales_area_manager_product_category  = SalesAreaManagerProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('sales_area_manager_id', $sales_area_manager_id)->where('status', '1')->first();
                $category_list                  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
                
                return View::make('backend.organogram_sales_area_manager.editsalesareamanager', array('zonal_manager_list'=>$zonal_manager,'regional_manager_list'=>$regional_manager,'sales_regional_manager_list'=>$sales_regional_manager, 'sales_area_manager_list'=>$sales_area_manager, 'sales_area_manager_state'=>$sales_area_manager_state, 'state_list'=>$state_list, 'category_list'=>$category_list, 'sales_area_manager_category_list'=>$sales_area_manager_product_category)); 
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    } 
    public function postUpdateSalesAreaManagerOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();       
            $rules  = array(
                            'zonal_manager'                 => 'required|numeric',
                            'regional_manager'              => 'required|numeric',
                            'sales_regional_manager'        => 'required|numeric',
                            'sales_area_manager_hq_code'    => 'required|string',
                            'sales_area_name'               => 'required|string',
                            'sales_area_mobile_number'      => 'required|numeric',
                            'sales_area_state'              => 'required|array',
                            'sales_area_pincode'            => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $sales_area_id                  = $my_data['sales_area_id'];
            $zonal_manager                  = $my_data['zonal_manager'];
            $regional_manager               = $my_data['regional_manager'];
            $sales_regional_manager         = $my_data['sales_regional_manager'];
            $sales_area_manager_hq_code     = $my_data['sales_area_manager_hq_code'];
            $sales_area_name                = $my_data['sales_area_name'];
            $sales_area_mobile_number       = $my_data['sales_area_mobile_number'];
            $sales_area_state               = $my_data['sales_area_state'];
            $sales_area_pincode             = $my_data['sales_area_pincode'];
            $product_category               = $my_data['product_category'];
            $change_password                = isset($my_data['change_password'])?'1':'0';
            
            if(count($sales_area_state)>0) {
                $check_sales_area_manager_code    = SalesAreaManager::where('sales_area_manager_id', '!=', $sales_area_id)
                                                        ->where('ECN', $sales_area_manager_hq_code)
                                                        ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                if($check_sales_area_manager_code>0 || count($user)>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $sales_area_manager_array    = array(
                                                    'ECN'                               => $sales_area_manager_hq_code,
                                                    'zonal_manager_id'                  => $zonal_manager,
                                                    'regional_manager_id'               => $regional_manager,
                                                    'sales_regional_manager_id'         => $sales_regional_manager,
                                                    'sales_area_manager_hq_code'        => $sales_area_manager_hq_code,
                                                    'sales_area_manager_name'           => $sales_area_name,
                                                    'sales_area_manager_mobile'         => $sales_area_mobile_number,
                                                    'sales_area_manager_pincode'        => $sales_area_pincode,
                                                    'status'                            => '1',
                                                    'sales_area_manager_id'                     => $sales_area_id,
                                                    'updated_at'                        => date('Y-m-d H:i:s')
                                                );
                    
                    if($change_password=='1'){
                        $random_string                          = $this->generateRandomString(8);
                        $password                               = \Hash::make($random_string);
                        $sales_area_manager_array['password']   = $password;
                        $sales_area_manager_array['dash']       = $random_string;
                    }

                    if (isset($random_string) && !empty($random_string)) {
                        $asmData = SalesAreaManager::where('sales_area_manager_id', $sales_area_id)->first();
                        $sales_area_manager_array['api_token']    = $asmData->api_token;
                        $sales_area_manager_array['status']       = $asmData->status;
                        $sales_area_manager_array['created_at']   = $asmData->created_at;
                        $sales_area_manager_array['device_id']    = $asmData->device_id;
                    }
                    else
                    {
                        $asmData = SalesAreaManager::where('sales_area_manager_id', $sales_area_id)->first();
                        $sales_area_manager_array['password']     = $asmData->password;
                        $sales_area_manager_array['dash']         = $asmData->dash;
                        $sales_area_manager_array['api_token']    = $asmData->api_token;
                        $sales_area_manager_array['created_at']   = $asmData->created_at;
                        $sales_area_manager_array['device_id']    = $asmData->device_id;
                    }

                    $insert_user    = SalesAreaManagerOrn::firstOrCreate($sales_area_manager_array);
                    $result         = $insert_user->sales_area_managers_orn_id;
                    if ($result) 
                    { 
                        foreach($sales_area_state AS $state_id) {
                            $sales_area_manager_states[] = array(
                                                            'sales_area_manager_id' => $sales_area_id,
                                                            'state_id'              => $state_id,
                                                            'status'                => '1',
                                                            'orn_id' => $result
                                                            );
                        }                    
                        $insert_sales_area_manager_states= SalesAreaManagerStateOrn::insert($sales_area_manager_states);
                        
                         
                        $sales_area_manager_product_category = array();
                        foreach($product_category AS $product_category_id) {
                            $sales_area_manager_product_category[]   = array(
                                                                            'sales_area_manager_id'       => $sales_area_id,
                                                            'product_crop_category_id'  => $product_category_id,
                                                            'status'                    => '1',
                                                            'orn_id' => $result
                                                                            );
                        }
                        $insert_sales_area_manager_product_category  = SalesAreaManagerProductCategoryOrn::insert($sales_area_manager_product_category);
                        Session::flash('message', "Area Sales Manager Successfully Updated. Please wait for Super Admin Approval.");
                        return redirect('salesarea/sales-area-manager-list-orn');
                    }
                    else
                    {   
                        Session::flash('message', "Please try again.");
                        return redirect('salesarea/sales-area-manager-list-orn');
                    }
                    
                }                
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }    
    // public function postUpdateSalesAreaManagerOrn(Request $request) {         
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data= Input::all();       
    //         $rules  = array(
    //                         'zonal_manager'                 => 'required|numeric',
    //                         'regional_manager'              => 'required|numeric',
    //                         'sales_regional_manager'        => 'required|numeric',
    //                         'sales_area_manager_hq_code'    => 'required|string',
    //                         'sales_area_name'               => 'required|string',
    //                         'sales_area_mobile_number'      => 'required|numeric',
    //                         'sales_area_state'              => 'required|array',
    //                         'sales_area_pincode'            => 'required|numeric',
    //                         'product_category'          => 'required|array'
    //                     );
    //         $validator  = Validator::make($my_data, $rules);
    //         if ($validator->fails()) {
    //             return Redirect::back()->withInput()->withErrors($validator);
    //         }
            
    //         $sales_area_id                  = $my_data['sales_area_id'];
    //         $zonal_manager                  = $my_data['zonal_manager'];
    //         $regional_manager               = $my_data['regional_manager'];
    //         $sales_regional_manager         = $my_data['sales_regional_manager'];
    //         $sales_area_manager_hq_code     = $my_data['sales_area_manager_hq_code'];
    //         $sales_area_name                = $my_data['sales_area_name'];
    //         $sales_area_mobile_number       = $my_data['sales_area_mobile_number'];
    //         $sales_area_state               = $my_data['sales_area_state'];
    //         $sales_area_pincode             = $my_data['sales_area_pincode'];
    //         $product_category               = $my_data['product_category'];
    //         $change_password                = isset($my_data['change_password'])?'1':'0';
            
    //         if(count($sales_area_state)>0) {
    //             $check_sales_area_manager_code    = SalesAreaManager::where('sales_area_manager_id', '!=', $sales_area_id)
    //                                                     ->where('ECN', $sales_area_manager_hq_code)
    //                                                     ->count();
    //             $query  =   "
    //                         (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT id AS id FROM users WHERE ECN='".$sales_area_manager_hq_code."' LIMIT 0,1)
    //                         ";
    //             $user   = DB::select(DB::raw($query));
    //             if($check_sales_area_manager_code>0 || count($user)>0) {
    //                 Session::flash('message', "ECN already exists, Please choose a different ECN.");
    //                 return Redirect::back()->withInput()->withErrors($validator);
    //             }
    //             else {
    //                 $sales_area_manager_array    = array(
    //                                                 'ECN'                               => $sales_area_manager_hq_code,
    //                                                 'zonal_manager_id'                  => $zonal_manager,
    //                                                 'regional_manager_id'               => $regional_manager,
    //                                                 'sales_regional_manager_id'         => $sales_regional_manager,
    //                                                 'sales_area_manager_hq_code'        => $sales_area_manager_hq_code,
    //                                                 'sales_area_manager_name'           => $sales_area_name,
    //                                                 'sales_area_manager_mobile'         => $sales_area_mobile_number,
    //                                                 'sales_area_manager_pincode'        => $sales_area_pincode,
    //                                                 'status'                            => '1',
    //                                                 'updated_at'                        => date('Y-m-d H:i:s')
    //                                             );
                    
    //                 if($change_password=='1'){
    //                     $random_string                          = $this->generateRandomString(8);
    //                     $password                               = \Hash::make($random_string);
    //                     $sales_area_manager_array['password']   = $password;
    //                     $sales_area_manager_array['dash']       = $random_string;
    //                 }
                    
    //                 $update_sales_area_manager  = DB::table('sales_area_managers')
    //                                                 ->where('sales_area_manager_id', $sales_area_id)
    //                                                 ->where('status', '1')
    //                                                 ->update($sales_area_manager_array); 

                    
    //                 $update_bdo                 = DB::table('users')
    //                                                 ->where('sales_regional_manager_id', $sales_regional_manager)
    //                                                 ->where('status', '1')
    //                                                 ->update(['zonal_manager_id'=>$zonal_manager, 'regional_manager_id'=>$regional_manager]); 
                    
                    
    //                 $reset_sales_area_manager_states   = DB::table('sales_area_manager_states')
    //                                                     ->where('sales_area_manager_id', $sales_area_id)
    //                                                     ->update(['status'=>'0']); 
    //                 foreach($sales_area_state AS $state_id) {
    //                     $update_sales_area_manager      = DB::table('sales_area_manager_states')
    //                                                     ->where('sales_area_manager_id', $sales_area_id)
    //                                                     ->where('state_id', $state_id)
    //                                                     ->update(['status'=>'1']);
    //                     $sales_area_manager_states   = array(
    //                                                     'sales_area_manager_id' => $sales_area_id,
    //                                                     'state_id'              => $state_id,
    //                                                     'status'                => '1'
    //                                                     );
    //                     $insert_sales_area_manager   = SalesAreaManagerState::firstOrCreate($sales_area_manager_states);
    //                 }  
                    
                    
                    
    //                 $reset_sales_area_manager_category= DB::table('sales_area_manager_product_categories')
    //                                                     ->where('sales_area_manager_id', $sales_area_id)
    //                                                     ->update(['status'=>'0']); 
    //                 foreach($product_category AS $category_id) {
    //                     $update_sales_area_manager  = DB::table('sales_area_manager_product_categories')
    //                                                     ->where('sales_area_manager_id', $sales_area_id)
    //                                                     ->where('product_crop_category_id', $category_id)
    //                                                     ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
    //                     $sales_area_manager_states  = array(
    //                                                     'sales_area_manager_id'       => $sales_area_id,
    //                                                     'product_crop_category_id'  => $category_id,
    //                                                     'status'                    => '1'
    //                                                     );
    //                     $insert_sales_area_manager        = SalesAreaManagerProductCategory::firstOrCreate($sales_area_manager_states);
    //                 }
    //                 Session::flash('message', "Area Sales Manager Successfully Updated.");
    //                 return redirect('salesarea/sales-area-manager-list');
    //             }                
    //         }
    //         else {
    //             Session::flash('message', "Please choose state and try again.");
    //             return Redirect::back()->withInput()->withErrors($validator);
    //         }
    //     }
    // } 
    // public function postSalesAreaDeleteOrn(Request $request) {         
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
    //             $sales_area_manager_id     = $my_data['_ticket'];
    //             $update_sales_area_manager = DB::table('sales_area_managers')
    //                                         ->where('sales_area_manager_id', $sales_area_manager_id)
    //                                         ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
    //             if ($update_sales_area_manager) {
    //                 echo 1;
    //             } else {
    //                 echo 2;
    //             }
    //         }
    //     }
    // } 
}