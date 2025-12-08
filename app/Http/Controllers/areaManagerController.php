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
use App\SalesRegionalManagerState;
use App\SalesRegionalManagerProductCategory;
use App\AreaManager;
use App\AreaManagerorn;
use App\AreaManagerState;
use App\AreaManagerStateOrn;
use App\AreaManagerProductCategory;
use App\AreaManagerProductCategoryOrn;
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


use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class areaManagerController extends Controller {
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
    public function postCheckAreaEcn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();             
            if($my_data) {
                $area_manager_code          = $my_data['area_manager_hq_code'];
                $area_manager_id            = $my_data['_area'];
                $check_area_manager_code    = AreaManager::where('area_manager_id', '!=', $area_manager_id)
                                                ->where('ECN', $area_manager_code)
                                                ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$area_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$area_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$area_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$area_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$area_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT dd_id AS id FROM dhanuka_doctor WHERE ECN='".$area_manager_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                echo ($check_area_manager_code>0 || count($user)>0?'false':'true');die;
            }
        }

        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData1) {
            $my_data    = Input::all();             
            if($my_data) {
                $area_manager_code          = $my_data['area_manager_hq_code'];
                $area_manager_id            = $my_data['_area'];
                $check_area_manager_code    = AreaManager::where('area_manager_id', '!=', $area_manager_id)
                                                ->where('ECN', $area_manager_code)
                                                ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$area_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$area_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$area_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$area_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$area_manager_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                echo ($check_area_manager_code>0 || count($user)>0?'false':'true');die;
            }
        }
    }     
    public function getAddAreaManager() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $category_list                  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $zonal_manager_list             = ZonalManager::where('status', '1')->orderBy('zonal_manager_name', 'asc')->get();
        $state_list                     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        return View::make('backend.area_manager.addareamanager', array('state_list'=>$state_list, 'zonal_manager_list'=>$zonal_manager_list, 'category_list'=>$category_list));
    }    
    public function postZonalRegional(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();     
            $zonal_manager  = $my_data['zonal_manager'];   
            $regional_list  = RegionalManager::select('regional_manager_id', 'regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
            
            if(count($regional_list)>0) {
                echo json_encode($regional_list);die;
            }            
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData1) {
            $my_data        = Input::all();     
            $zonal_manager  = $my_data['zonal_manager'];   
            $regional_list  = RegionalManager::select('regional_manager_id', 'regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
            
            if(count($regional_list)>0) {
                echo json_encode($regional_list);die;
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
                echo json_encode($sales_regional_list);die;
            }            
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData1) {
            $my_data                = Input::all();     
            $zonal_manager          = $my_data['zonal_manager'];   
            $sales_regional_list    = SalesRegionalManager::select('sales_regional_manager_id', 'sales_regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('sales_regional_manager_name', 'ASC')->get()->toArray();
            
            if(count($sales_regional_list)>0) {
                echo json_encode($sales_regional_list);die;
            }            
        }
    } 
    public function postAreaManager(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data            = Input::all();     
            $regional_manager   = $my_data['regional_manager'];   
            $area_manager_list  = AreaManager::select('area_manager_id', 'area_manager_name')->where('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('area_manager_name', 'ASC')->get()->toArray();
            
            if(count($area_manager_list)>0) {
                echo json_encode($area_manager_list);die;
            }
        }
    } 
    public function postSalesAreaManager(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data                    = Input::all();     
            $regional_manager           = $my_data['regional_manager'];   
            $sales_area_manager_list    = SalesAreaManager::select('sales_area_manager_id', 'sales_area_manager_name')->where('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('sales_area_manager_name', 'ASC')->get()->toArray();
            
            if(count($sales_area_manager_list)>0) {
                echo json_encode($sales_area_manager_list);die;
            }
        }
    } 
    public function postBdo(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data            = Input::all();     
            $area_manager       = $my_data['area_manager'];   
            $area_manager_list  = User::select('id', 'name', 'ECN')->where('area_manager_id', $area_manager)->where('status', '1')->orderBy('name', 'ASC')->get()->toArray();
            
            if(count($area_manager_list)>0) {
                echo json_encode($area_manager_list);die;
            }            
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData1) {
            $branchState = $userData1->state;
            $my_data            = Input::all();     
            $area_manager       = $my_data['area_manager'];   
            $area_manager_list  = User::select('id', 'name', 'ECN')->where('area_manager_id', $area_manager)->where('state_id',$branchState)->where('status', '1')->orderBy('name', 'ASC')->get()->toArray();
            
            if(count($area_manager_list)>0) {
                echo json_encode($area_manager_list);die;
            }            
        }
    } 
    public function postCreateAreaManager(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();   
            $rules  = array(
                            'zonal_manager'         => 'required|numeric',
                            'regional_manager'      => 'required|numeric',
                            'sales_regional_manager'=> 'required|numeric',
                            'area_manager_hq_code'  => 'required|string',
                            'area_name'             => 'required|string',
                            'area_mobile_number'    => 'required|numeric',
                            'area_state'            => 'required|array',
                            'area_pincode'          => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $zonal_manager          = $my_data['zonal_manager'];
            $regional_manager       = $my_data['regional_manager'];
            $sales_regional_manager = $my_data['sales_regional_manager'];
            $area_manager_hq_code   = $my_data['area_manager_hq_code'];
            $area_name              = $my_data['area_name'];
            $area_mobile_number     = $my_data['area_mobile_number'];
            $area_state             = $my_data['area_state'];
            $area_pincode           = $my_data['area_pincode'];
            $product_category       = $my_data['product_category'];
            if(count($area_state)>0) {
                if(count($product_category)>0) { 
                    $check_area_manager_code  = AreaManager::where('ECN', $area_manager_hq_code)->count();
                    if($check_area_manager_code>0) {
                        Session::flash('message', "ECN already exists, Please choose a different ECN.");
                        return Redirect::back()->withInput()->withErrors($validator);
                    }
                    else {
                        $adminController    = new adminController;
                        $random_string      = $adminController->generateRandomString(8);
                        $password           = \Hash::make($random_string);
                        $area_manager_array     = array(
                                                        'ECN'                       => $area_manager_hq_code,
                                                        'dash'                      => $random_string,
                                                        'password'                  => $password,
                                                        'zonal_manager_id'          => $zonal_manager,
                                                        'regional_manager_id'       => $regional_manager,
                                                        'sales_regional_manager_id' => $sales_regional_manager,
                                                        'area_manager_hq_code'      => $area_manager_hq_code,
                                                        'area_manager_name'         => $area_name,
                                                        'area_manager_mobile'       => $area_mobile_number,
                                                        'area_manager_pincode'      => $area_pincode,
                                                        'status'                    => '1'
                                                    );
                        $insert_area_manager    = AreaManager::firstOrCreate($area_manager_array);
                        $result                 = $insert_area_manager->area_manager_id;

                        if($result) {
                            $area_manager_states   = array();
                            foreach($area_state AS $state_id) {
                                $area_manager_states[] = array(
                                                                'area_manager_id'   => $result,
                                                                'state_id'          => $state_id,
                                                                'status'            => '1'
                                                                );
                            }                    
                            $insert_area_manager_states= AreaManagerState::insert($area_manager_states);
                            
                            
                            
                            
                            $area_manager_product_category = array();
                            foreach($product_category AS $product_category_id) {
                                $area_manager_product_category[]   = array(
                                                                        'area_manager_id'           => $result,
                                                                        'product_crop_category_id'  => $product_category_id,
                                                                        'status'                    => '1'
                                                                        );
                            }
                            $insert_area_manager_product_category  = AreaManagerProductCategory::insert($area_manager_product_category);
                            
                            
                            Session::flash('message', "Area Manager Successfully Created.");
                            return redirect('area/area-manager-list');
                                            
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
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  
    public function getAreaManagerList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.area_manager.areamanagerlist');
    } 
    public function postAllareamanagerlist(Request $request) {
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
                $zonalId = " and am.status = '".$status."'";
            }
            else{
                $zonalId = "";
            }
            
        }

        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        if ($userData1) {
            if ($status != '') {
                $zonalId = " and am.zonal_manager_id = ".$userData1->zonal_manager_id."' and am.status = '".$status."'";
            }
            else{
                $zonalId = "and am.zonal_manager_id = ".$userData1->zonal_manager_id;
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
                                    am.area_manager_hq_code,
                                    zm.zonal_manager_name,
                                    rm.regional_manager_name,
                                    srm.sales_regional_manager_name,
                                    am.area_manager_name,
                                    am.area_manager_mobile,
                                    am.area_manager_pincode,  
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN am.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    am.ECN,
                                    am.dash,                                    
                                    DATE_FORMAT(am.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,                                    
                                    am.area_manager_id,
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        area_managers           am
                                LEFT JOIN   active_logs             al  ON am.area_manager_id=al.user_id AND al.user_type='area'
                                LEFT JOIN   zonal_managers          zm  ON zm.zonal_manager_id=am.zonal_manager_id
                                LEFT JOIN   area_manager_states     ams ON ams.area_manager_id=am.area_manager_id
                                LEFT JOIN   regional_managers       rm  ON rm.regional_manager_id=am.regional_manager_id
                                LEFT JOIN   sales_regional_managers srm ON srm.sales_regional_manager_id=am.sales_regional_manager_id
                                LEFT JOIN   states                  s   ON ams.state_id=s.state_id                                    
                                
                                LEFT JOIN   area_manager_product_categories     ampc    ON ampc.area_manager_id=am.area_manager_id and ampc.status=1
                                LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=ampc.product_crop_category_id
                                
                                WHERE
                                    1=1  ".$zonalId."    
                                GROUP BY ams.area_manager_id
                                )   A,
                                (select @a:=0) initvars";
            $area_manager  = collect(DB::select($query));  
            foreach ($area_manager as $key => $value) {
                $states = DB::table('area_manager_states as zms')
                          ->join('states as s','s.state_id','=','zms.state_id')  
                          ->where('area_manager_id',$area_manager[$key]->area_manager_id)
                          ->where('zms.status','1')
                          ->select(DB::raw("GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n') as state"))
                          ->get();
                if (count($states)>0) 
                {
                    $area_manager[$key]->states = $states[0]->state;
                }         
            } 
            return Datatables::of($area_manager)->make();
        }
        else {
            $search =  $search_array['value']; 
            $sorting= '';  
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT                                
                                    am.area_manager_hq_code,
                                    zm.zonal_manager_name,
                                    rm.regional_manager_name,
                                    srm.sales_regional_manager_name,
                                    am.area_manager_name,
                                    am.area_manager_mobile,
                                    am.area_manager_pincode,  
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN am.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    am.ECN,
                                    am.dash,                                    
                                    DATE_FORMAT(am.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,                                    
                                    am.area_manager_id,
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        area_managers           am
                                LEFT JOIN   active_logs             al  ON am.area_manager_id=al.user_id AND al.user_type='area'
                                LEFT JOIN   zonal_managers          zm  ON zm.zonal_manager_id=am.zonal_manager_id
                                LEFT JOIN   area_manager_states     ams ON ams.area_manager_id=am.area_manager_id
                                LEFT JOIN   regional_managers       rm  ON rm.regional_manager_id=am.regional_manager_id
                                LEFT JOIN   sales_regional_managers srm ON srm.sales_regional_manager_id=am.sales_regional_manager_id
                                LEFT JOIN   states                  s   ON ams.state_id=s.state_id                                    
                                
                                LEFT JOIN   area_manager_product_categories     ampc    ON ampc.area_manager_id=am.area_manager_id and ampc.status=1
                                LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=ampc.product_crop_category_id
                                
                                WHERE
                                    (                                
                                    am.area_manager_name        LIKE '%".$search."%'    OR
                                    am.area_manager_mobile      LIKE '%".$search."%'    OR
                                    am.area_manager_pincode     LIKE '%".$search."%'    OR
                                    am.ECN                      LIKE '%".$search."%'    OR
                                    s.state                     LIKE '%".$search."%'
                                    )      ".$zonalId." 
                                GROUP BY ams.area_manager_id
                                )   A,
                                (select @a:=0) initvars";
            
            $area_manager  = collect(DB::select($query));
            foreach ($area_manager as $key => $value) {
                $states = DB::table('area_manager_states as zms')
                          ->join('states as s','s.state_id','=','zms.state_id')  
                          ->where('area_manager_id',$area_manager[$key]->area_manager_id)
                          ->where('zms.status','1')
                          ->select(DB::raw("GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n') as state"))
                          ->get();
                if (count($states)>0) 
                {
                    $area_manager[$key]->states = $states[0]->state;
                }         
            }
            return Datatables::of($area_manager)->make();            
        }
    }    
    public function getEditAreaManager($area_manager_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($area_manager_id!='') {
                $zonal_manager          = ZonalManager::where('status', '1')->get();
                $regional_manager       = RegionalManager::where('status', '1')->get();
                $sales_regional_manager = SalesRegionalManager::where('status', '1')->get();
                $area_manager           = AreaManager::where('area_manager_id', $area_manager_id)->first();
                $area_manager_state     = AreaManagerState::select(DB::raw("GROUP_CONCAT(state_id SEPARATOR ',')  AS state_id"))->where('area_manager_id', $area_manager_id)->where('status', '1')->first();
                $state_list             = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                
                $area_manager_product_category  = AreaManagerProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('area_manager_id', $area_manager_id)->where('status', '1')->first();
                $category_list                  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
                
                return View::make('backend.area_manager.editareamanager', array('zonal_manager_list'=>$zonal_manager,'regional_manager_list'=>$regional_manager,'sales_regional_manager_list'=>$sales_regional_manager, 'area_manager'=>$area_manager, 'area_manager_state'=>$area_manager_state, 'state_list'=>$state_list, 'category_list'=>$category_list, 'area_manager_category_list'=>$area_manager_product_category)); 
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }    
    public function postUpdateAreaManager(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'zonal_manager'         => 'required|numeric',
                            'regional_manager'      => 'required|numeric',
                            'sales_regional_manager'=> 'required|numeric',
                            'area_manager_hq_code'  => 'required|string',
                            'area_name'             => 'required|string',
                            'area_mobile_number'    => 'required|numeric',
                            'area_state'            => 'required|array',
                            'area_pincode'          => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $area_id                = $my_data['area_id'];
            $zonal_manager          = $my_data['zonal_manager'];
            $regional_manager       = $my_data['regional_manager'];
            $sales_regional_manager = $my_data['sales_regional_manager'];
            $area_manager_hq_code   = $my_data['area_manager_hq_code'];
            $area_name              = $my_data['area_name'];
            $area_mobile_number     = $my_data['area_mobile_number'];
            $area_state             = $my_data['area_state'];
            $area_pincode           = $my_data['area_pincode'];
            $product_category       = $my_data['product_category'];
            $change_password        = isset($my_data['change_password'])?'1':'0';
            if(count($area_state)>0) {
                $check_area_manager_code    = AreaManager::where('area_manager_id', '!=', $area_id)
                                                ->where('ECN', $area_manager_hq_code)
                                                ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$area_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                
                if($check_area_manager_code>0 || count($user)>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $area_manager_array         = array(
                                                        'ECN'                       => $area_manager_hq_code,
                                                        'zonal_manager_id'          => $zonal_manager,
                                                        'regional_manager_id'       => $regional_manager,
                                                        'sales_regional_manager_id' => $sales_regional_manager,
                                                        'area_manager_hq_code'      => $area_manager_hq_code,
                                                        'area_manager_name'         => $area_name,
                                                        'area_manager_mobile'       => $area_mobile_number,
                                                        'area_manager_pincode'      => $area_pincode,
                                                        'status'                    => '1',
                                                        'updated_at'                => date('Y-m-d H:i:s')
                                                    );
                    if($change_password=='1'){
                        $random_string                      = $this->generateRandomString(8);
                        $password                           = \Hash::make($random_string);
                        $area_manager_array['password']     = $password;
                        $area_manager_array['dash']         = $random_string;
                    }
                    $update_area_manager        = DB::table('area_managers')
                                                    ->where('area_manager_id', $area_id)
                                                    ->where('status', '1')
                                                    ->update($area_manager_array); 
                    
                     
                    $update_bdo                 = DB::table('users')
                                                    ->where('regional_manager_id', $regional_manager)
                                                    ->where('status', '1')
                                                    ->update(['zonal_manager_id'=>$zonal_manager, 'regional_manager_id'=>$regional_manager]); 
                    
                    
                    $reset_area_manager_states   = DB::table('area_manager_states')
                                                        ->where('area_manager_id', $area_id)
                                                        ->update(['status'=>'0', 'updated_at' => date('Y-m-d H:i:s')]); 
                    foreach($area_state AS $state_id) {
                        $update_area_manager   = DB::table('area_manager_states')
                                                    ->where('area_manager_id', $area_id)
                                                    ->where('state_id', $state_id)
                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $area_manager_states   = array(
                                                        'area_manager_id'  => $area_id,
                                                        'state_id'          => $state_id,
                                                        'status'            => '1'
                                                        );
                        $insert_area_manager   = AreaManagerState::firstOrCreate($area_manager_states);
                    }  
                    
                    
                    
                    
                    $reset_area_manager_category= DB::table('area_manager_product_categories')
                                                    ->where('area_manager_id', $area_id)
                                                    ->update(['status'=>'0']); 
                    foreach($product_category AS $category_id) {
                        $update_area_manager   = DB::table('area_manager_product_categories')
                                                    ->where('area_manager_id', $area_id)
                                                    ->where('product_crop_category_id', $category_id)
                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $area_manager_states   = array(
                                                        'area_manager_id'       => $area_id,
                                                        'product_crop_category_id'  => $category_id,
                                                        'status'                    => '1'
                                                        );
                        $insert_area_manager   = AreaManagerProductCategory::firstOrCreate($area_manager_states);
                    }
                
                    
                    
                    Session::flash('message', "Area Manager Successfully Updated.");
                    return redirect('area/area-manager-list');
                }
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    } 
    public function postAreaDelete(Request $request) {         
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
                $area_manager_id     = $my_data['_ticket'];
                $update_area_manager = DB::table('area_managers')
                                            ->where('area_manager_id', $area_manager_id)
                                            ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_area_manager) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    } 
    public function postRegionalSales(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) { 
            $my_data                = Input::all();     
            $zonal_manager          = $my_data['zonal_manager'];   
            $regional_list          = RegionalManager::select('regional_manager_id', 'regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
            $sales_regional_list    = SalesRegionalManager::select('sales_regional_manager_id', 'sales_regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('sales_regional_manager_name', 'ASC')->get()->toArray();
            $regional_sales         = array();
            if(count($regional_list)>0) {
                $regional_sales['regional'] = $regional_list;
            }
            if(count($sales_regional_list)>0) {
                $regional_sales['sales']    = $sales_regional_list;
            }
            echo json_encode($regional_sales);die;
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $branchState = $userData1->state;
            $my_data                = Input::all();     
            $zonal_manager          = $my_data['zonal_manager']; 
            //->where('regional_manager_states.state_id',$branchState)  
            $regional_list          = RegionalManager::join('regional_manager_states','regional_managers.regional_manager_id','=','regional_manager_states.regional_manager_id')->select('regional_managers.regional_manager_id', 'regional_manager_name')->where('regional_managers.zonal_manager_id', $zonal_manager)->where('regional_managers.status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
            //->where('sales_regional_manager_states.state_id',$branchState)
            $sales_regional_list    = SalesRegionalManager::join('sales_regional_manager_states','sales_regional_managers.sales_regional_manager_id','=','sales_regional_manager_states.sales_regional_manager_id')->select('sales_regional_managers.sales_regional_manager_id', 'sales_regional_manager_name')->where('sales_regional_managers.zonal_manager_id', $zonal_manager)->where('sales_regional_managers.status', '1')->orderBy('sales_regional_manager_name', 'ASC')->get()->toArray();
            $regional_sales         = array();
            if(count($regional_list)>0) {
                $regional_sales['regional'] = $regional_list;
            }
            if(count($sales_regional_list)>0) {
                $regional_sales['sales']    = $sales_regional_list;
            }
            echo json_encode($regional_sales);die;
        }
    } 
    public function postAreaSales(Request $request) {

        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data            = Input::all();     
            $regional_manager   = $my_data['regional_manager'];   
            $area_list          = AreaManager::select('area_manager_id', 'area_manager_name')->where('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('area_manager_name', 'ASC')->get()->toArray();
            $sales_area_list    = SalesAreaManager::select('sales_area_manager_id', 'sales_area_manager_name')->where('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('sales_area_manager_name', 'ASC')->get()->toArray();
            $area_sales         = array();
            if(count($area_list)>0) {
                $area_sales['area'] = $area_list;
            }
            if(count($sales_area_list)>0) {
                $area_sales['sales']    = $sales_area_list;
            }
            echo json_encode($area_sales);die;
        }

        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $branchState = $userData1->state;
             $my_data            = Input::all();     
            $regional_manager   = $my_data['regional_manager'];  
            //->where('area_manager_states.state_id',$branchState) 
            $area_list          = AreaManager::join('area_manager_states','area_managers.area_manager_id','=','area_manager_states.area_manager_id')->select('area_managers.area_manager_id', 'area_manager_name')->where('area_managers.regional_manager_id', $regional_manager)->where('area_managers.status', '1')->orderBy('area_manager_name', 'ASC')->get()->toArray();
            $sales_area_list    = SalesAreaManager::join('sales_area_manager_states','sales_area_managers.sales_area_manager_id','=','sales_area_manager_states.sales_area_manager_id')->select('sales_area_managers.sales_area_manager_id', 'sales_area_manager_name')->where('sales_area_managers.regional_manager_id', $regional_manager)->where('sales_area_managers.status', '1')->orderBy('sales_area_manager_name', 'ASC')->get()->toArray();
            $area_sales         = array();
            if(count($area_list)>0) {
                $area_sales['area'] = $area_list;
            }
            if(count($sales_area_list)>0) {
                $area_sales['sales']    = $sales_area_list;
            }
            echo json_encode($area_sales);die;
        }
    }
    public function postFrontLiners(Request $request) {
        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data            = Input::all();     
            $sales_area_manager   = $my_data['sales_area_manager'];   
            $area_list          = User::select('id', 'name')->where('sales_area_manager_id', $sales_area_manager)->where('status', '1')->orderBy('name', 'ASC')->get()->toArray();
            //$area_sales         = array();
            //if(count($area_list)>0) {
               // $area_list;
           // }
           
            echo json_encode($area_list);die;
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $my_data            = Input::all();     
            $sales_area_manager   = $my_data['sales_area_manager'];   
            $area_list          = User::select('id', 'name')->where('sales_area_manager_id', $sales_area_manager)->where('status', '1')->orderBy('name', 'ASC')->get()->toArray();
            //$area_sales         = array();
            //if(count($area_list)>0) {
               // $area_list;
           // }
           
            echo json_encode($area_list);die;
        }
    }
    public function postUpdateAreaStatus(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data            = Input::all(); 
            $area_manager_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $area_manager_data  = AreaManager::where('area_manager_id', $area_manager_id)->first();
            if($area_manager_data) {
                $area_manager_data_status = ($area_manager_data->status=='0'?'1':'0');
                $update_area_manager      = DB::table('area_managers')
                                            ->where('area_manager_id', $area_manager_id)
                                            ->update(['status'=>$area_manager_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    public function postDd(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data            = Input::all();     
            $area_manager       = $my_data['area_manager'];   
            $area_manager_list  = DB::table('dhanuka_doctor')->select('dd_id', 'name', 'ECN')->where('bdo_id', $area_manager)->where('status', '1')->orderBy('name', 'ASC')->get();
            // echo "<pre>"; print_r($area_manager_list); die;
            if(count($area_manager_list)>0) {
                echo json_encode($area_manager_list);die;
            }            
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $my_data            = Input::all();     
            $area_manager       = $my_data['area_manager'];   
            $area_manager_list  = DB::table('dhanuka_doctor')->select('dd_id', 'name', 'ECN')->where('bdo_id', $area_manager)->where('status', '1')->orderBy('name', 'ASC')->get();
            // echo "<pre>"; print_r($area_manager_list); die;
            if(count($area_manager_list)>0) {
                echo json_encode($area_manager_list);die;
            }            
        }
    } 

    //Organogram ====================
    
    public function getAddAreaManagerOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        $category_list                  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $zonal_manager_list             = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
        $state_list                     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        return View::make('backend.organogram_area_manager.addareamanager', array('state_list'=>$state_list, 'zonal_manager_list'=>$zonal_manager_list, 'category_list'=>$category_list));
    }
    public function postCreateAreaManagerOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first(); 
        if ($userData) {
            $my_data= Input::all();   
            $rules  = array(
                            'zonal_manager'         => 'required|numeric',
                            'regional_manager'      => 'required|numeric',
                            'sales_regional_manager'=> 'required|numeric',
                            'area_manager_hq_code'  => 'required|string',
                            'area_name'             => 'required|string',
                            'area_mobile_number'    => 'required|numeric',
                            'area_state'            => 'required|array',
                            'area_pincode'          => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $zonal_manager          = $my_data['zonal_manager'];
            $regional_manager       = $my_data['regional_manager'];
            $sales_regional_manager = $my_data['sales_regional_manager'];
            $area_manager_hq_code   = $my_data['area_manager_hq_code'];
            $area_name              = $my_data['area_name'];
            $area_mobile_number     = $my_data['area_mobile_number'];
            $area_state             = $my_data['area_state'];
            $area_pincode           = $my_data['area_pincode'];
            $product_category       = $my_data['product_category'];
            if(count($area_state)>0) {
                if(count($product_category)>0) { 
                    $check_area_manager_code  = AreaManager::where('ECN', $area_manager_hq_code)->count();
                    if($check_area_manager_code>0) {
                        Session::flash('message', "ECN already exists, Please choose a different ECN.");
                        return Redirect::back()->withInput()->withErrors($validator);
                    }
                    else {
                        $adminController    = new adminController;
                        $random_string      = $adminController->generateRandomString(8);
                        $password           = \Hash::make($random_string);
                        $area_manager_array     = array(
                                                        'ECN'                       => $area_manager_hq_code,
                                                        'dash'                      => $random_string,
                                                        'password'                  => $password,
                                                        'zonal_manager_id'          => $zonal_manager,
                                                        'regional_manager_id'       => $regional_manager,
                                                        'sales_regional_manager_id' => $sales_regional_manager,
                                                        'area_manager_hq_code'      => $area_manager_hq_code,
                                                        'area_manager_name'         => $area_name,
                                                        'area_manager_mobile'       => $area_mobile_number,
                                                        'area_manager_pincode'      => $area_pincode,
                                                        'status'                    => '1',
                                                        'updated_at'                        => date('Y-m-d H:i:s')
                                                    );
                        $insert_area_manager    = AreaManagerorn::firstOrCreate($area_manager_array);
                        $result                 = $insert_area_manager->area_managers_orn_id;

                        if($result) {
                            $area_manager_states   = array();
                            foreach($area_state AS $state_id) {
                                $area_manager_states[] = array(
                                                                'area_manager_id'   => $result,
                                                                'state_id'          => $state_id,
                                                                'status'            => '1',
                                                                'orn_id' => $result
                                                                );
                            }                    
                            $insert_area_manager_states= AreaManagerStateOrn::insert($area_manager_states);
                            
                            
                            
                            
                            $area_manager_product_category = array();
                            foreach($product_category AS $product_category_id) {
                                $area_manager_product_category[]   = array(
                                                                        'area_manager_id'           => $result,
                                                                        'product_crop_category_id'  => $product_category_id,
                                                                        'status'                       => '1',
                                                                        'orn_id' => $result
                                                                        );
                            }
                            $insert_area_manager_product_category  = AreaManagerProductCategoryOrn::insert($area_manager_product_category);
                            
                            
                            Session::flash('message', "Area Manager Successfully Created. Wait for Admin approval.");
                            return redirect('area/area-manager-list-orn');
                                            
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
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  
    public function getAreaManagerListOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.organogram_area_manager.areamanagerlist');
    } 
    public function postAllareamanagerlistorn(Request $request) {
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
                                    am.area_manager_hq_code,
                                    zm.zonal_manager_name,
                                    rm.regional_manager_name,
                                    srm.sales_regional_manager_name,
                                    am.area_manager_name,
                                    am.area_manager_mobile,
                                    am.area_manager_pincode,  
                                    GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS state,
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN am.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    am.ECN,
                                    am.dash,                                    
                                    DATE_FORMAT(am.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,                                    
                                    am.area_manager_id,
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        area_managers           am
                                LEFT JOIN   active_logs             al  ON am.area_manager_id=al.user_id AND al.user_type='area'
                                LEFT JOIN   zonal_managers          zm  ON zm.zonal_manager_id=am.zonal_manager_id
                                LEFT JOIN   area_manager_states     ams ON ams.area_manager_id=am.area_manager_id
                                LEFT JOIN   regional_managers       rm  ON rm.regional_manager_id=am.regional_manager_id
                                LEFT JOIN   sales_regional_managers srm ON srm.sales_regional_manager_id=am.sales_regional_manager_id
                                LEFT JOIN   states                  s   ON ams.state_id=s.state_id                                    
                                
                                LEFT JOIN   area_manager_product_categories     ampc    ON ampc.area_manager_id=am.area_manager_id and ampc.status=1
                                LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=ampc.product_crop_category_id
                                
                                WHERE am.zonal_manager_id = '".$zonalId->zonal_manager_id."'     
                                GROUP BY ams.area_manager_id
                                )   A,
                                (select @a:=0) initvars";
            $area_manager  = collect(DB::select($query)); 
            if (count($area_manager)>0) {
                foreach ($area_manager as $key => $value) {
                    $checkStatus = DB::table('area_managers_orn')
                                 ->where('area_manager_id',$area_manager[$key]->area_manager_id)
                                 ->where('orn_status','0')
                                 ->get();
                    if (count($checkStatus)>0) {
                        if ($area_manager[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $area_manager[$key]->status = 'ACTIVE';
                        }
                        elseif ($area_manager[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $area_manager[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $area_manager[$key]->status = 'PENDING';
                        }
                    }                
                }               
            }       
            return Datatables::of($area_manager)->make();
        }
        else {
            $search =  $search_array['value']; 
            $sorting= '';  
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT                                
                                    am.area_manager_hq_code,
                                    zm.zonal_manager_name,
                                    rm.regional_manager_name,
                                    srm.sales_regional_manager_name,
                                    am.area_manager_name,
                                    am.area_manager_mobile,
                                    am.area_manager_pincode,  
                                    GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS state,
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN am.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    am.ECN,
                                    am.dash,                                    
                                    DATE_FORMAT(am.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,                                    
                                    am.area_manager_id,
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        area_managers           am
                                LEFT JOIN   active_logs             al  ON am.area_manager_id=al.user_id AND al.user_type='area'
                                LEFT JOIN   zonal_managers          zm  ON zm.zonal_manager_id=am.zonal_manager_id
                                LEFT JOIN   area_manager_states     ams ON ams.area_manager_id=am.area_manager_id
                                LEFT JOIN   regional_managers       rm  ON rm.regional_manager_id=am.regional_manager_id
                                LEFT JOIN   sales_regional_managers srm ON srm.sales_regional_manager_id=am.sales_regional_manager_id
                                LEFT JOIN   states                  s   ON ams.state_id=s.state_id                                    
                                
                                LEFT JOIN   area_manager_product_categories     ampc    ON ampc.area_manager_id=am.area_manager_id and ampc.status=1
                                LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=ampc.product_crop_category_id
                                
                                WHERE
                                    (                                
                                    am.area_manager_name        LIKE '%".$search."%'    OR
                                    am.area_manager_mobile      LIKE '%".$search."%'    OR
                                    am.area_manager_pincode     LIKE '%".$search."%'    OR
                                    am.ECN                      LIKE '%".$search."%'    OR
                                    s.state                     LIKE '%".$search."%'
                                    )   
                                    and am.zonal_manager_id = '".$zonalId->zonal_manager_id."'  
                                GROUP BY ams.area_manager_id
                                )   A,
                                (select @a:=0) initvars";
            
            $area_manager  = collect(DB::select($query));
            if (count($area_manager)>0) {
                foreach ($area_manager as $key => $value) {
                    $checkStatus = DB::table('area_managers_orn')
                                 ->where('area_manager_id',$area_manager[$key]->area_manager_id)
                                 ->where('orn_status','0')
                                 ->get();
                    if (count($checkStatus)>0) {
                        if ($area_manager[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $area_manager[$key]->status = 'ACTIVE';
                        }
                        elseif ($area_manager[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $area_manager[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $area_manager[$key]->status = 'PENDING';
                        }
                    }                
                }               
            }
            return Datatables::of($area_manager)->make();            
        }
    } 
    public function postUpdateAreaStatusOrn(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data            = Input::all(); 
            $area_manager_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $area_manager_data  = AreaManager::where('area_manager_id', $area_manager_id)->first();
            if($area_manager_data) {
                $area_manager_data_status = ($area_manager_data->status=='0'?'1':'0');
                $insert_area     = array(       
                                        'area_manager_id' => $area_manager_data->area_manager_id,
                                        'ECN' => $area_manager_data->ECN,
                                        'dash' => $area_manager_data->dash,
                                        'password' => $area_manager_data->password,
                                        'api_token' => $area_manager_data->api_token,
                                        'zonal_manager_id' => $area_manager_data->zonal_manager_id,
                                        'regional_manager_id' => $area_manager_data->regional_manager_id,
                                        'sales_regional_manager_id' => $area_manager_data->sales_regional_manager_id,
                                        'area_manager_hq_code' => $area_manager_data->area_manager_hq_code,
                                        'area_manager_name' => $area_manager_data->area_manager_name,
                                        'area_manager_mobile' => $area_manager_data->area_manager_mobile,
                                        'area_manager_pincode' => $area_manager_data->area_manager_pincode,
                                        'status' => $area_manager_data_status,
                                        'device_id' => $area_manager_data->device_id,
                                        'created_at' => $area_manager_data->created_at,
                                        'updated_at'    => date('Y-m-d H:i:s')
                                            ); 
                $insert_user    = AreaManagerorn::firstOrCreate($insert_area);
                $result         = $insert_user->area_managers_orn_id;
                if($result) {
                    echo '1';
                }
                else
                {
                    echo '2';
                }
                // $update_area_manager      = DB::table('area_managers')
                //                             ->where('area_manager_id', $area_manager_id)
                //                             ->update(['status'=>$area_manager_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    public function getEditAreaManagerOrn($area_manager_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first();  
        if ($userData) {
            if($area_manager_id!='') {
                $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
                $zonal_manager             = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
                $regional_manager       = RegionalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
                $sales_regional_manager = SalesRegionalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
                $area_manager           = AreaManager::where('area_manager_id', $area_manager_id)->first();
                $area_manager_state     = AreaManagerState::select(DB::raw("GROUP_CONCAT(state_id SEPARATOR ',')  AS state_id"))->where('area_manager_id', $area_manager_id)->where('status', '1')->first();
                $state_list             = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                
                $area_manager_product_category  = AreaManagerProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('area_manager_id', $area_manager_id)->where('status', '1')->first();
                $category_list                  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
                
                return View::make('backend.organogram_area_manager.editareamanager', array('zonal_manager_list'=>$zonal_manager,'regional_manager_list'=>$regional_manager,'sales_regional_manager_list'=>$sales_regional_manager, 'area_manager'=>$area_manager, 'area_manager_state'=>$area_manager_state, 'state_list'=>$state_list, 'category_list'=>$category_list, 'area_manager_category_list'=>$area_manager_product_category)); 
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateAreaManagerOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'zonal_manager'         => 'required|numeric',
                            'regional_manager'      => 'required|numeric',
                            'sales_regional_manager'=> 'required|numeric',
                            'area_manager_hq_code'  => 'required|string',
                            'area_name'             => 'required|string',
                            'area_mobile_number'    => 'required|numeric',
                            'area_state'            => 'required|array',
                            'area_pincode'          => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $area_id                = $my_data['area_id'];
            $zonal_manager          = $my_data['zonal_manager'];
            $regional_manager       = $my_data['regional_manager'];
            $sales_regional_manager = $my_data['sales_regional_manager'];
            $area_manager_hq_code   = $my_data['area_manager_hq_code'];
            $area_name              = $my_data['area_name'];
            $area_mobile_number     = $my_data['area_mobile_number'];
            $area_state             = $my_data['area_state'];
            $area_pincode           = $my_data['area_pincode'];
            $product_category       = $my_data['product_category'];
            $change_password        = isset($my_data['change_password'])?'1':'0';
            if(count($area_state)>0) {
                $check_area_manager_code    = AreaManager::where('area_manager_id', '!=', $area_id)
                                                ->where('ECN', $area_manager_hq_code)
                                                ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$area_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$area_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                
                if($check_area_manager_code>0 || count($user)>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $area_manager_array         = array(
                                                        'area_manager_id'           => $area_id,           
                                                        'ECN'                       => $area_manager_hq_code,
                                                        'zonal_manager_id'          => $zonal_manager,
                                                        'regional_manager_id'       => $regional_manager,
                                                        'sales_regional_manager_id' => $sales_regional_manager,
                                                        'area_manager_hq_code'      => $area_manager_hq_code,
                                                        'area_manager_name'         => $area_name,
                                                        'area_manager_mobile'       => $area_mobile_number,
                                                        'area_manager_pincode'      => $area_pincode,
                                                        'status'                    => '1',
                                                        'updated_at'                => date('Y-m-d H:i:s')
                                                    );
                    if($change_password=='1'){
                        $random_string                      = $this->generateRandomString(8);
                        $password                           = \Hash::make($random_string);
                        $area_manager_array['password']     = $password;
                        $area_manager_array['dash']         = $random_string;
                    }
                    
                    if (isset($random_string) && !empty($random_string)) {
                        $asmData = AreaManager::where('area_manager_id', $area_id)->first();
                        $area_manager_array['api_token']    = $asmData->api_token;
                        $area_manager_array['status']       = $asmData->status;
                        $area_manager_array['created_at']   = $asmData->created_at;
                        $area_manager_array['device_id']    = $asmData->device_id;
                    }
                    else
                    {
                        $asmData = AreaManager::where('area_manager_id', $area_id)->first();
                        $area_manager_array['password']     = $asmData->password;
                        $area_manager_array['dash']         = $asmData->dash;
                        $area_manager_array['api_token']    = $asmData->api_token;
                        $area_manager_array['created_at']   = $asmData->created_at;
                        $area_manager_array['device_id']    = $asmData->device_id;
                    }

                    $insert_user    = AreaManagerorn::firstOrCreate($area_manager_array);
                    $result         = $insert_user->area_managers_orn_id;
                    if ($result) 
                    { 
                        foreach($area_state AS $state_id) {
                            $area_manager_states   = array(
                                                            'area_manager_id'  => $area_id,
                                                            'state_id'          => $state_id,
                                                            'status'            => '1',
                                                            'orn_id' => $result
                                                            );
                            $insert_area_manager   = AreaManagerStateOrn::firstOrCreate($area_manager_states);
                        }  
                        
                        
                        foreach($product_category AS $category_id) {
                            $area_manager_states   = array(
                                                            'area_manager_id'       => $area_id,
                                                            'product_crop_category_id'  => $category_id,
                                                            'status'                    => '1',
                                                            'orn_id' => $result
                                                            );
                            $insert_area_manager   = AreaManagerProductCategoryOrn::firstOrCreate($area_manager_states);
                        }
                        
                        
                        
                        Session::flash('message', "Area Manager Successfully Updated. Please wait for Sauper Admin approval.");
                        return redirect('area/area-manager-list-orn');
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
    
    // Post Area Sales For Reports 
    public function postAreaSalesForReport(Request $request) {

        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data            = Input::all();     
            $regional_manager   = $my_data['regional_manager'];   
            $area_list          = AreaManager::select('area_manager_id', 'area_manager_name')->whereIn('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('area_manager_name', 'ASC')->get()->toArray();
            $sales_area_list    = SalesAreaManager::select('sales_area_manager_id', 'sales_area_manager_name')->whereIn('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('sales_area_manager_name', 'ASC')->get()->toArray();
            $area_sales         = array();
            if(count($area_list)>0) {
                $area_sales['area'] = $area_list;
            }
            if(count($sales_area_list)>0) {
                $area_sales['sales']    = $sales_area_list;
            }
            echo json_encode($area_sales);die;
        }

        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $branchState = $userData1->state;
             $my_data            = Input::all();     
            $regional_manager   = $my_data['regional_manager'];  
            //->where('area_manager_states.state_id',$branchState) 
            $area_list          = AreaManager::join('area_manager_states','area_managers.area_manager_id','=','area_manager_states.area_manager_id')->select('area_managers.area_manager_id', 'area_manager_name')->whereIn('area_managers.regional_manager_id', $regional_manager)->where('area_managers.status', '1')->orderBy('area_manager_name', 'ASC')->get()->toArray();
            $sales_area_list    = SalesAreaManager::join('sales_area_manager_states','sales_area_managers.sales_area_manager_id','=','sales_area_manager_states.sales_area_manager_id')->select('sales_area_managers.sales_area_manager_id', 'sales_area_manager_name')->whereIn('sales_area_managers.regional_manager_id', $regional_manager)->where('sales_area_managers.status', '1')->orderBy('sales_area_manager_name', 'ASC')->get()->toArray();
            $area_sales         = array();
            if(count($area_list)>0) {
                $area_sales['area'] = $area_list;
            }
            if(count($sales_area_list)>0) {
                $area_sales['sales']    = $sales_area_list;
            }
            echo json_encode($area_sales);die;
        }
    }

    // Post FL For Reports
    public function postFrontLinersForReport(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data            = Input::all();     
            $sales_area_manager   = $my_data['sales_area_manager'];   
            $area_list          = User::select('id', 'name')->whereIn('sales_area_manager_id', $sales_area_manager)->where('status', '1')->orderBy('name', 'ASC')->get()->toArray();
            echo json_encode($area_list);die;
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $my_data            = Input::all();     
            $sales_area_manager   = $my_data['sales_area_manager'];   
            $area_list          = User::select('id', 'name')->whereIn('sales_area_manager_id', $sales_area_manager)->where('status', '1')->orderBy('name', 'ASC')->get()->toArray();
            echo json_encode($area_list);die;
        }
    }

    // public function postAreaDeleteOrn(Request $request) {         
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
    //             $area_manager_id     = $my_data['_ticket'];
    //             $update_area_manager = DB::table('area_managers')
    //                                         ->where('area_manager_id', $area_manager_id)
    //                                         ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
    //             if ($update_area_manager) {
    //                 echo 1;
    //             } else {
    //                 echo 2;
    //             }
    //         }
    //     }
    // } 
    // public function postAreaManagerOrn(Request $request) {
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data            = Input::all();     
    //         $regional_manager   = $my_data['regional_manager'];   
    //         $area_manager_list  = AreaManager::select('area_manager_id', 'area_manager_name')->where('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('area_manager_name', 'ASC')->get()->toArray();
            
    //         if(count($area_manager_list)>0) {
    //             echo json_encode($area_manager_list);die;
    //         }
    //     }
    // }
    // public function postRegionalSalesOrn(Request $request) {
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) { 
    //         $my_data                = Input::all();     
    //         $zonal_manager          = $my_data['zonal_manager'];   
    //         $regional_list          = RegionalManager::select('regional_manager_id', 'regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
    //         $sales_regional_list    = SalesRegionalManager::select('sales_regional_manager_id', 'sales_regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('sales_regional_manager_name', 'ASC')->get()->toArray();
    //         $regional_sales         = array();
    //         if(count($regional_list)>0) {
    //             $regional_sales['regional'] = $regional_list;
    //         }
    //         if(count($sales_regional_list)>0) {
    //             $regional_sales['sales']    = $sales_regional_list;
    //         }
    //         echo json_encode($regional_sales);die;
    //     }
    //     $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
    //     if ($userData1) {
    //         $branchState = $userData1->state;
    //         $my_data                = Input::all();     
    //         $zonal_manager          = $my_data['zonal_manager'];   
    //         $regional_list          = RegionalManager::join('regional_manager_states','regional_managers.regional_manager_id','=','regional_manager_states.regional_manager_id')->select('regional_managers.regional_manager_id', 'regional_manager_name')->where('regional_manager_states.state_id',$branchState)->where('regional_managers.zonal_manager_id', $zonal_manager)->where('regional_managers.status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
    //         $sales_regional_list    = SalesRegionalManager::join('sales_regional_manager_states','sales_regional_managers.sales_regional_manager_id','=','sales_regional_manager_states.sales_regional_manager_id')->select('sales_regional_managers.sales_regional_manager_id', 'sales_regional_manager_name')->where('sales_regional_manager_states.state_id',$branchState)->where('sales_regional_managers.zonal_manager_id', $zonal_manager)->where('sales_regional_managers.status', '1')->orderBy('sales_regional_manager_name', 'ASC')->get()->toArray();
    //         $regional_sales         = array();
    //         if(count($regional_list)>0) {
    //             $regional_sales['regional'] = $regional_list;
    //         }
    //         if(count($sales_regional_list)>0) {
    //             $regional_sales['sales']    = $sales_regional_list;
    //         }
    //         echo json_encode($regional_sales);die;
    //     }
    // } 
    // public function postAreaSalesOrn(Request $request) {

    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data            = Input::all();     
    //         $regional_manager   = $my_data['regional_manager'];   
    //         $area_list          = AreaManager::select('area_manager_id', 'area_manager_name')->where('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('area_manager_name', 'ASC')->get()->toArray();
    //         $sales_area_list    = SalesAreaManager::select('sales_area_manager_id', 'sales_area_manager_name')->where('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('sales_area_manager_name', 'ASC')->get()->toArray();
    //         $area_sales         = array();
    //         if(count($area_list)>0) {
    //             $area_sales['area'] = $area_list;
    //         }
    //         if(count($sales_area_list)>0) {
    //             $area_sales['sales']    = $sales_area_list;
    //         }
    //         echo json_encode($area_sales);die;
    //     }

    //      $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
    //     if ($userData1) {
    //         $branchState = $userData1->state;
    //          $my_data            = Input::all();     
    //         $regional_manager   = $my_data['regional_manager'];   
    //         $area_list          = AreaManager::join('area_manager_states','area_managers.area_manager_id','=','area_manager_states.area_manager_id')->select('area_managers.area_manager_id', 'area_manager_name')->where('area_managers.regional_manager_id', $regional_manager)->where('area_manager_states.state_id',$branchState)->where('area_managers.status', '1')->orderBy('area_manager_name', 'ASC')->get()->toArray();
    //         $sales_area_list    = SalesAreaManager::join('sales_area_manager_states','sales_area_managers.sales_area_manager_id','=','sales_area_manager_states.sales_area_manager_id')->select('sales_area_managers.sales_area_manager_id', 'sales_area_manager_name')->where('sales_area_managers.regional_manager_id', $regional_manager)->where('sales_area_managers.status', '1')->orderBy('sales_area_manager_name', 'ASC')->get()->toArray();
    //         $area_sales         = array();
    //         if(count($area_list)>0) {
    //             $area_sales['area'] = $area_list;
    //         }
    //         if(count($sales_area_list)>0) {
    //             $area_sales['sales']    = $sales_area_list;
    //         }
    //         echo json_encode($area_sales);die;
    //     }
    // }
    // public function postFrontLinersOrn(Request $request) {
        
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data            = Input::all();     
    //         $sales_area_manager   = $my_data['sales_area_manager'];   
    //         $area_list          = User::select('id', 'name')->where('sales_area_manager_id', $sales_area_manager)->where('status', '1')->orderBy('name', 'ASC')->get()->toArray();
    //         //$area_sales         = array();
    //         //if(count($area_list)>0) {
    //            // $area_list;
    //        // }
           
    //         echo json_encode($area_list);die;
    //     }
    // }
    // public function postDdOrn(Request $request) {
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data            = Input::all();     
    //         $area_manager       = $my_data['area_manager'];   
    //         $area_manager_list  = DB::table('dhanuka_doctor')->select('dd_id', 'name', 'ECN')->where('bdo_id', $area_manager)->where('status', '1')->orderBy('name', 'ASC')->get();
    //         // echo "<pre>"; print_r($area_manager_list); die;
    //         if(count($area_manager_list)>0) {
    //             echo json_encode($area_manager_list);die;
    //         }            
    //     }
    // } 
    // public function postZonalRegionalOrn(Request $request) {
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data        = Input::all();     
    //         $zonal_manager  = $my_data['zonal_manager'];   
    //         $regional_list  = RegionalManager::select('regional_manager_id', 'regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
            
    //         if(count($regional_list)>0) {
    //             echo json_encode($regional_list);die;
    //         }            
    //     }
    // } 
    // public function postZonalSalesRegionalOrn(Request $request) {
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data                = Input::all();     
    //         $zonal_manager          = $my_data['zonal_manager'];   
    //         $sales_regional_list    = SalesRegionalManager::select('sales_regional_manager_id', 'sales_regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('sales_regional_manager_name', 'ASC')->get()->toArray();
            
    //         if(count($sales_regional_list)>0) {
    //             echo json_encode($sales_regional_list);die;
    //         }            
    //     }
    // } 
    // public function postSalesAreaManagerOrn(Request $request) {
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data                    = Input::all();     
    //         $regional_manager           = $my_data['regional_manager'];   
    //         $sales_area_manager_list    = SalesAreaManager::select('sales_area_manager_id', 'sales_area_manager_name')->where('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('sales_area_manager_name', 'ASC')->get()->toArray();
            
    //         if(count($sales_area_manager_list)>0) {
    //             echo json_encode($sales_area_manager_list);die;
    //         }
    //     }
    // } 
    // public function postBdoOrn(Request $request) {
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data            = Input::all();     
    //         $area_manager       = $my_data['area_manager'];   
    //         $area_manager_list  = User::select('id', 'name', 'ECN')->where('area_manager_id', $area_manager)->where('status', '1')->orderBy('name', 'ASC')->get()->toArray();
            
    //         if(count($area_manager_list)>0) {
    //             echo json_encode($area_manager_list);die;
    //         }            
    //     }
    //     $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
    //     if ($userData1) {
    //         $branchState = $userData1->state;
    //         $my_data            = Input::all();     
    //         $area_manager       = $my_data['area_manager'];   
    //         $area_manager_list  = User::select('id', 'name', 'ECN')->where('area_manager_id', $area_manager)->where('state_id',$branchState)->where('status', '1')->orderBy('name', 'ASC')->get()->toArray();
            
    //         if(count($area_manager_list)>0) {
    //             echo json_encode($area_manager_list);die;
    //         }            
    //     }
    // } 
    
}