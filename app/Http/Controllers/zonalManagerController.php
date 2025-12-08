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
use Illuminate\Support\MessageBag;
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



use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class zonalManagerController extends Controller {
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
    public function postCheckEcn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();             
            if($my_data) {
                $bdo_code       = $my_data['bdo_code'];
                $bdo_id         = $my_data['_bdo'];                
                $check_bdo_code = ZonalManager::where('zonal_manager_id', '!=', $bdo_id)
                                        ->where('ECN', $bdo_code)
                                        ->count();
                $query  =   "
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$bdo_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                
                echo ($check_bdo_code>0 || count($user)>0?'false':'true');
            }
        }
    } 
    public function getAddZonalManager() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $category_list  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $state_list     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        return View::make('backend.zonal_manager.zonalmanagerlist', array('state_list'=>$state_list, 'category_list'=>$category_list));       
    }
    public function postCreateZonalManager(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'ECN'                   => 'required|numeric',
                            'zonal_name'            => 'required|string',
                            'zonal_mobile_number'   => 'required|numeric',
                            'zonal_state'           => 'required|array',
                            'zonal_pincode'         => 'required|numeric',
                            'product_category'      => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $ECN                    = $my_data['ECN'];
            $zonal_name             = $my_data['zonal_name'];
            $zonal_mobile_number    = $my_data['zonal_mobile_number'];
            $zonal_state            = $my_data['zonal_state'];
            $zonal_pincode          = $my_data['zonal_pincode'];
            $product_category       = $my_data['product_category'];
            //dd($product_category);
            if(count($zonal_state)>0) {
                if(count($product_category)>0) {
                    $adminController    = new adminController;
                    $random_string      = $adminController->generateRandomString(8);
                    $password           = \Hash::make($random_string);

                    $zonal_manager_array    = array(
                                                    'ECN'                   => $ECN,
                                                    'dash'                  => $random_string,
                                                    'password'              => $password,
                                                    'zonal_manager_name'    => $zonal_name,
                                                    'zonal_manager_mobile'  => $zonal_mobile_number,
                                                    'zonal_manager_pincode' => $zonal_pincode,
                                                    'status'                => '1'
                                                );
                    $insert_zonal_manager   = ZonalManager::firstOrCreate($zonal_manager_array);
                    $result                 = $insert_zonal_manager->zonal_manager_id;

                    //dd($insert_zonal_manager);
                    
                    if($result) {
                        $zonal_manager_states   = array();
                        foreach($zonal_state AS $state_id) {
                            $zonal_manager_states[] = array(
                                                            'zonal_manager_id'  => $result,
                                                            'state_id'          => $state_id,
                                                            'status'            => '1'
                                                            );
                        }  
                        $insert_zonal_manager_states= ZonalManagerState::insert($zonal_manager_states);
                        
                        $zonal_manager_product_category = array();
                        foreach($product_category AS $product_category_id) {
                            $zonal_manager_product_category[]   = array(
                                                                    'zonal_manager_id'          => $result,
                                                                    'product_crop_category_id'  => $product_category_id,
                                                                    'status'                    => '1'
                                                                    );
                        }
                        $insert_zonal_manager_product_category  = ZonalManagerProductCategory::insert($zonal_manager_product_category);
                        
                        Session::flash('message', "Zonal Manager Successfully Created.");
                        return redirect('zonal/add-zonal-manager');
                    }
                    else {
                        Session::flash('message', "An error occurred. Please try again later.");
                        return Redirect::back()->withInput()->withErrors($validator);
                    }
                }                
                else {
                    Session::flash('message', "Please choose product crop category and try again.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }
    public function getZonalManagerList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.zonal_manager.zonalmanagerlist');
    } 
    public function postAllzonalmanagerlist(Request $request) {
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
                $zonalId = " and zm.status = '".$status."'";
            }
            else{
                $zonalId = "";
            }
        }

        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        if ($userData1) {
            if ($status != '') {
                $zonalId = "and zm.zonal_manager_id = ".$userData1->zonal_manager_id."' and zm.status = '".$status."'";
            }
            else{
                $zonalId = "and zm.zonal_manager_id = ".$userData1->zonal_manager_id;
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
                                    zm.zonal_manager_name,
                                    zm.zonal_manager_mobile,
                                    zm.zonal_manager_pincode,                                                                   
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN zm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    zm.ECN,
                                    zm.dash,                                    
                                    DATE_FORMAT(zm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                    zm.zonal_manager_id,
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        zonal_managers                      zm
                                LEFT JOIN   active_logs                         al      ON zm.zonal_manager_id=al.user_id AND al.user_type='zonal'
                                LEFT JOIN   zonal_manager_states                zms     ON zms.zonal_manager_id=zm.zonal_manager_id
                                LEFT JOIN   zonal_manager_product_categories    zmpc    ON zmpc.zonal_manager_id=zm.zonal_manager_id and zmpc.status=1
                                LEFT JOIN   states                              s       ON zms.state_id=s.state_id
                                LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=zmpc.product_crop_category_id
                                WHERE
                                    1=1  ".$zonalId."   
                                GROUP BY zms.zonal_manager_id
                                )   A,
                                (select @a:=0) initvars";

            $zonal_manager  = collect(DB::select($query));
            foreach ($zonal_manager as $key => $value) {
                $states = DB::table('zonal_manager_states as zms')
                          ->join('states as s','s.state_id','=','zms.state_id')  
                          ->where('zonal_manager_id',$zonal_manager[$key]->zonal_manager_id)
                          ->where('zms.status','1')
                          ->select(DB::raw("GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n') as state"))
                          ->get();
                if (count($states)>0) 
                {
                    $zonal_manager[$key]->states = $states[0]->state;
                }         
            } 
            return Datatables::of($zonal_manager)->make();
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
                                zm.zonal_manager_name,
                                zm.zonal_manager_mobile,
                                zm.zonal_manager_pincode,                                                                   
                                GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                (CASE WHEN zm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                zm.ECN,
                                zm.dash,                                    
                                DATE_FORMAT(zm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                zm.zonal_manager_id,
                                GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                            FROM        zonal_managers                      zm
                            LEFT JOIN   active_logs                         al      ON zm.zonal_manager_id=al.user_id AND al.user_type='zonal'
                            LEFT JOIN   zonal_manager_states                zms     ON zms.zonal_manager_id=zm.zonal_manager_id
                            LEFT JOIN   zonal_manager_product_categories    zmpc    ON zmpc.zonal_manager_id=zm.zonal_manager_id
                            LEFT JOIN   states                              s       ON zms.state_id=s.state_id
                            LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=zmpc.product_crop_category_id
                            WHERE
                                (                                
                                zm.zonal_manager_name       LIKE '%".$search."%'    OR
                                zm.zonal_manager_mobile     LIKE '%".$search."%'    OR
                                zm.zonal_manager_pincode    LIKE '%".$search."%'    OR
                                zm.ECN                      LIKE '%".$search."%'    OR
                                s.state                     LIKE '%".$search."%'
                                ) ".$zonalId."
                            GROUP BY zms.zonal_manager_id
                            )   A,
                            (select @a:=0) initvars"; 
            //echo "<pre>";print_r($query);die;
            $zonal_manager  = collect(DB::select($query));
            foreach ($zonal_manager as $key => $value) {
                $states = DB::table('zonal_manager_states as zms')
                          ->join('states as s','s.state_id','=','zms.state_id')  
                          ->where('zonal_manager_id',$zonal_manager[$key]->zonal_manager_id)
                          ->where('zms.status','1')
                          ->select(DB::raw("GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n') as state"))
                          ->get();
                if (count($states)>0) 
                {
                    $zonal_manager[$key]->states = $states[0]->state;
                }         
            } 
            return Datatables::of($zonal_manager)->make();            
        }
    } 
    public function getEditZonalManager($zonal_manager_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($zonal_manager_id!='') {
                $zonal_manager                  = ZonalManager::where('zonal_manager_id', $zonal_manager_id)->first();
                $zonal_manager_state            = ZonalManagerState::select(DB::raw("GROUP_CONCAT(state_id SEPARATOR ',')  AS state_id"))->where('zonal_manager_id', $zonal_manager_id)->where('status', '1')->first();
                $zonal_manager_product_category = ZonalManagerProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('zonal_manager_id', $zonal_manager_id)->where('status', '1')->first();
                $state_list                     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                $category_list                  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
                return View::make('backend.zonal_manager.editzonalmanager', array('zonal_manager'=>$zonal_manager, 'zonal_manager_state'=>$zonal_manager_state, 'state_list'=>$state_list, 'category_list'=>$category_list, 'zonal_manager_category_list'=>$zonal_manager_product_category));
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateZonalManager(Request $request, MessageBag $errors) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'ECN'                   => 'required|numeric',
                            'zonal_name'            => 'required|string',
                            'zonal_mobile_number'   => 'required|numeric',
                            'zonal_state'           => 'required|array',
                            'zonal_pincode'         => 'required|numeric',
                            'product_category'      => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $zonal_id               = $my_data['zonal_id'];
            $ECN                    = $my_data['ECN'];
            $zonal_name             = $my_data['zonal_name'];
            $zonal_mobile_number    = $my_data['zonal_mobile_number'];
            $zonal_state            = $my_data['zonal_state'];
            $zonal_pincode          = $my_data['zonal_pincode'];
            $product_category       = $my_data['product_category'];
            
            $change_password        = isset($my_data['change_password'])?'1':'0';
            
            if(count($zonal_state)>0) {
                $check_bdo_code     = ZonalManager::where('zonal_manager_id', '!=', $zonal_id)
                                        ->where('ECN', $ECN)
                                        ->count();
                $query  =   "
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$ECN."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$ECN."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$ECN."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$ECN."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$ECN."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                if($check_bdo_code>0 || count($user)>0) {
                    $errors->add('your_custom_error',   "ECN already exists, Please choose a different ECN.");                    
                    return Redirect::back()->withInput()->withErrors($errors);
                }
                else {
                    $zonal_manager_array    = array(
                                                    'ECN'                   => $ECN,
                                                    'zonal_manager_name'    => $zonal_name,
                                                    'zonal_manager_mobile'  => $zonal_mobile_number,
                                                    'zonal_manager_pincode' => $zonal_pincode,
                                                    'updated_at'            => date('Y-m-d H:i:s')
                                                );
                    
                    if($change_password=='1'){
                        $random_string                      = $this->generateRandomString(8);
                        $password                           = \Hash::make($random_string);
                        $zonal_manager_array['password']    = $password;
                        $zonal_manager_array['dash']        = $random_string;
                    }
                    
                    $update_zonal_manager   = DB::table('zonal_managers')
                                                ->where('zonal_manager_id', $zonal_id)
                                                ->where('status', '1')
                                                ->update($zonal_manager_array); 
                    
                    $reset_zonal_manager_states   = DB::table('zonal_manager_states')
                                                    ->where('zonal_manager_id', $zonal_id)
                                                    ->update(['status'=>'0']); 
                    foreach($zonal_state AS $state_id) {
                        $update_zonal_manager   = DB::table('zonal_manager_states')
                                                    ->where('zonal_manager_id', $zonal_id)
                                                    ->where('state_id', $state_id)
                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $zonal_manager_states   = array(
                                                        'zonal_manager_id'  => $zonal_id,
                                                        'state_id'          => $state_id,
                                                        'status'            => '1'
                                                        );
                        $insert_zonal_manager   = ZonalManagerState::firstOrCreate($zonal_manager_states);
                    }

                    $reset_zonal_manager_category= DB::table('zonal_manager_product_categories')
                                                    ->where('zonal_manager_id', $zonal_id)
                                                    ->update(['status'=>'0']); 
                    foreach($product_category AS $category_id) {
                        $update_zonal_manager   = DB::table('zonal_manager_product_categories')
                                                    ->where('zonal_manager_id', $zonal_id)
                                                    ->where('product_crop_category_id', $category_id)
                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $zonal_manager_states   = array(
                                                        'zonal_manager_id'          => $zonal_id,
                                                        'product_crop_category_id'  => $category_id,
                                                        'status'                    => '1'
                                                        );
                        $insert_zonal_manager   = ZonalManagerProductCategory::firstOrCreate($zonal_manager_states);
                    }
                                    
                    Session::flash('message', "Zonal Manager Successfully Updated.");
                    return redirect('zonal/add-zonal-manager');
                }
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    } 
    public function postUpdateZonalStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $zonal_manager_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $zonal_manager_data    = ZonalManager::where('zonal_manager_id', $zonal_manager_id)->first();
            if($zonal_manager_data) {
                $zonal_manager_data_status = ($zonal_manager_data->status=='0'?'1':'0');
                $update_zonal_manager      = DB::table('zonal_managers')
                                                ->where('zonal_manager_id', $zonal_manager_id)
                                                ->update(['status'=>$zonal_manager_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    public function postZonalDelete(Request $request) {         
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
                $zonal_manager_id     = $my_data['_ticket'];
                $update_zonal_manager = DB::table('zonal_managers')
                                    ->where('zonal_manager_id', $zonal_manager_id)
                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_zonal_manager) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    } 

    //Organogram ====================================
    public function getAddZonalManagerOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $category_list  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $state_list     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        return View::make('backend.zonal_manager.zonalmanagerlist', array('state_list'=>$state_list, 'category_list'=>$category_list));       
    }
    public function postCreateZonalManagerOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'ECN'                   => 'required|numeric',
                            'zonal_name'            => 'required|string',
                            'zonal_mobile_number'   => 'required|numeric',
                            'zonal_state'           => 'required|array',
                            'zonal_pincode'         => 'required|numeric',
                            'product_category'      => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $ECN                    = $my_data['ECN'];
            $zonal_name             = $my_data['zonal_name'];
            $zonal_mobile_number    = $my_data['zonal_mobile_number'];
            $zonal_state            = $my_data['zonal_state'];
            $zonal_pincode          = $my_data['zonal_pincode'];
            $product_category       = $my_data['product_category'];
            //dd($product_category);
            if(count($zonal_state)>0) {
                if(count($product_category)>0) {
                    $adminController    = new adminController;
                    $random_string      = $adminController->generateRandomString(8);
                    $password           = \Hash::make($random_string);

                    $zonal_manager_array    = array(
                                                    'ECN'                   => $ECN,
                                                    'dash'                  => $random_string,
                                                    'password'              => $password,
                                                    'zonal_manager_name'    => $zonal_name,
                                                    'zonal_manager_mobile'  => $zonal_mobile_number,
                                                    'zonal_manager_pincode' => $zonal_pincode,
                                                    'status'                => '1'
                                                );
                    $insert_zonal_manager   = ZonalManager::firstOrCreate($zonal_manager_array);
                    $result                 = $insert_zonal_manager->zonal_manager_id;

                    //dd($insert_zonal_manager);
                    
                    if($result) {
                        $zonal_manager_states   = array();
                        foreach($zonal_state AS $state_id) {
                            $zonal_manager_states[] = array(
                                                            'zonal_manager_id'  => $result,
                                                            'state_id'          => $state_id,
                                                            'status'            => '1'
                                                            );
                        }  
                        $insert_zonal_manager_states= ZonalManagerState::insert($zonal_manager_states);
                        
                        $zonal_manager_product_category = array();
                        foreach($product_category AS $product_category_id) {
                            $zonal_manager_product_category[]   = array(
                                                                    'zonal_manager_id'          => $result,
                                                                    'product_crop_category_id'  => $product_category_id,
                                                                    'status'                    => '1'
                                                                    );
                        }
                        $insert_zonal_manager_product_category  = ZonalManagerProductCategory::insert($zonal_manager_product_category);
                        
                        Session::flash('message', "Zonal Manager Successfully Created.");
                        return redirect('zonal/add-zonal-manager');
                    }
                    else {
                        Session::flash('message', "An error occurred. Please try again later.");
                        return Redirect::back()->withInput()->withErrors($validator);
                    }
                }                
                else {
                    Session::flash('message', "Please choose product crop category and try again.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  
    public function getZonalManagerListOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.organogram_zonal_manager.zonalmanagerlist');
    } 
    public function postAllzonalmanagerlistorn(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        } 
        $zonalId = \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        
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
                                    zm.zonal_manager_name,
                                    zm.zonal_manager_mobile,
                                    zm.zonal_manager_pincode,                                                                   
                                    GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS state,
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN zm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    zm.ECN,
                                    zm.dash,                                    
                                    DATE_FORMAT(zm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                    zm.zonal_manager_id,
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        zonal_managers                      zm
                                LEFT JOIN   active_logs                         al      ON zm.zonal_manager_id=al.user_id AND al.user_type='zonal'
                                LEFT JOIN   zonal_manager_states                zms     ON zms.zonal_manager_id=zm.zonal_manager_id
                                LEFT JOIN   zonal_manager_product_categories    zmpc    ON zmpc.zonal_manager_id=zm.zonal_manager_id and zmpc.status=1
                                LEFT JOIN   states                              s       ON zms.state_id=s.state_id
                                LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=zmpc.product_crop_category_id
                                where zm.zonal_manager_id = '".$zonalId->zonal_manager_id."'    
                                GROUP BY zms.zonal_manager_id
                                )   A,
                                (select @a:=0) initvars";

            $zonal_manager  = collect(DB::select($query));  //print_r($zonal_manager);die;
            return Datatables::of($zonal_manager)->make();
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
                                zm.zonal_manager_name,
                                zm.zonal_manager_mobile,
                                zm.zonal_manager_pincode,                                                                   
                                GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS state,
                                GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                (CASE WHEN zm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                zm.ECN,
                                zm.dash,                                    
                                DATE_FORMAT(zm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                zm.zonal_manager_id,
                                GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                            FROM        zonal_managers                      zm
                            LEFT JOIN   active_logs                         al      ON zm.zonal_manager_id=al.user_id AND al.user_type='zonal'
                            LEFT JOIN   zonal_manager_states                zms     ON zms.zonal_manager_id=zm.zonal_manager_id
                            LEFT JOIN   zonal_manager_product_categories    zmpc    ON zmpc.zonal_manager_id=zm.zonal_manager_id
                            LEFT JOIN   states                              s       ON zms.state_id=s.state_id
                            LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=zmpc.product_crop_category_id
                            WHERE
                                (                                
                                zm.zonal_manager_name       LIKE '%".$search."%'    OR
                                zm.zonal_manager_mobile     LIKE '%".$search."%'    OR
                                zm.zonal_manager_pincode    LIKE '%".$search."%'    OR
                                zm.ECN                      LIKE '%".$search."%'    OR
                                s.state                     LIKE '%".$search."%'
                                ) 
                                and zm.zonal_manager_id = '".$zonalId->zonal_manager_id."'
                            GROUP BY zms.zonal_manager_id
                            )   A,
                            (select @a:=0) initvars"; 
            
            $zonal_manager  = collect(DB::select($query));
            return Datatables::of($zonal_manager)->make();            
        }
    } 
    public function getEditZonalManagerOrn($zonal_manager_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($zonal_manager_id!='') {
                $zonal_manager                  = ZonalManager::where('zonal_manager_id', $zonal_manager_id)->first();
                $zonal_manager_state            = ZonalManagerState::select(DB::raw("GROUP_CONCAT(state_id SEPARATOR ',')  AS state_id"))->where('zonal_manager_id', $zonal_manager_id)->where('status', '1')->first();
                $zonal_manager_product_category = ZonalManagerProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('zonal_manager_id', $zonal_manager_id)->where('status', '1')->first();
                $state_list                     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                $category_list                  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
                return View::make('backend.zonal_manager.editzonalmanager', array('zonal_manager'=>$zonal_manager, 'zonal_manager_state'=>$zonal_manager_state, 'state_list'=>$state_list, 'category_list'=>$category_list, 'zonal_manager_category_list'=>$zonal_manager_product_category));
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    } 
public function postUpdateZonalManagerOrn(Request $request, MessageBag $errors) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'ECN'                   => 'required|numeric',
                            'zonal_name'            => 'required|string',
                            'zonal_mobile_number'   => 'required|numeric',
                            'zonal_state'           => 'required|array',
                            'zonal_pincode'         => 'required|numeric',
                            'product_category'      => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $zonal_id               = $my_data['zonal_id'];
            $ECN                    = $my_data['ECN'];
            $zonal_name             = $my_data['zonal_name'];
            $zonal_mobile_number    = $my_data['zonal_mobile_number'];
            $zonal_state            = $my_data['zonal_state'];
            $zonal_pincode          = $my_data['zonal_pincode'];
            $product_category       = $my_data['product_category'];
            
            $change_password        = isset($my_data['change_password'])?'1':'0';
            
            if(count($zonal_state)>0) {
                $check_bdo_code     = ZonalManager::where('zonal_manager_id', '!=', $zonal_id)
                                        ->where('ECN', $ECN)
                                        ->count();
                $query  =   "
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$ECN."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$ECN."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$ECN."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$ECN."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$ECN."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                if($check_bdo_code>0 || count($user)>0) {
                    $errors->add('your_custom_error',   "ECN already exists, Please choose a different ECN.");                    
                    return Redirect::back()->withInput()->withErrors($errors);
                }
                else {
                    $zonal_manager_array    = array(
                                                    'ECN'                   => $ECN,
                                                    'zonal_manager_name'    => $zonal_name,
                                                    'zonal_manager_mobile'  => $zonal_mobile_number,
                                                    'zonal_manager_pincode' => $zonal_pincode,
                                                    'updated_at'            => date('Y-m-d H:i:s')
                                                );
                    
                    if($change_password=='1'){
                        $random_string                      = $this->generateRandomString(8);
                        $password                           = \Hash::make($random_string);
                        $zonal_manager_array['password']    = $password;
                        $zonal_manager_array['dash']        = $random_string;
                    }
                    
                    $update_zonal_manager   = DB::table('zonal_managers')
                                                ->where('zonal_manager_id', $zonal_id)
                                                ->where('status', '1')
                                                ->update($zonal_manager_array); 
                    
                    $reset_zonal_manager_states   = DB::table('zonal_manager_states')
                                                    ->where('zonal_manager_id', $zonal_id)
                                                    ->update(['status'=>'0']); 
                    foreach($zonal_state AS $state_id) {
                        $update_zonal_manager   = DB::table('zonal_manager_states')
                                                    ->where('zonal_manager_id', $zonal_id)
                                                    ->where('state_id', $state_id)
                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $zonal_manager_states   = array(
                                                        'zonal_manager_id'  => $zonal_id,
                                                        'state_id'          => $state_id,
                                                        'status'            => '1'
                                                        );
                        $insert_zonal_manager   = ZonalManagerState::firstOrCreate($zonal_manager_states);
                    }

                    $reset_zonal_manager_category= DB::table('zonal_manager_product_categories')
                                                    ->where('zonal_manager_id', $zonal_id)
                                                    ->update(['status'=>'0']); 
                    foreach($product_category AS $category_id) {
                        $update_zonal_manager   = DB::table('zonal_manager_product_categories')
                                                    ->where('zonal_manager_id', $zonal_id)
                                                    ->where('product_crop_category_id', $category_id)
                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $zonal_manager_states   = array(
                                                        'zonal_manager_id'          => $zonal_id,
                                                        'product_crop_category_id'  => $category_id,
                                                        'status'                    => '1'
                                                        );
                        $insert_zonal_manager   = ZonalManagerProductCategory::firstOrCreate($zonal_manager_states);
                    }
                                    
                    Session::flash('message', "Zonal Manager Successfully Updated.");
                    return redirect('zonal/add-zonal-manager');
                }
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    } 
    public function postUpdateZonalStatusOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $zonal_manager_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $zonal_manager_data    = ZonalManager::where('zonal_manager_id', $zonal_manager_id)->first();
            if($zonal_manager_data) {
                $zonal_manager_data_status = ($zonal_manager_data->status=='0'?'1':'0');
                $update_zonal_manager      = DB::table('zonal_managers')
                                                ->where('zonal_manager_id', $zonal_manager_id)
                                                ->update(['status'=>$zonal_manager_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    public function postZonalDeleteOrn(Request $request) {         
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
                $zonal_manager_id     = $my_data['_ticket'];
                $update_zonal_manager = DB::table('zonal_managers')
                                    ->where('zonal_manager_id', $zonal_manager_id)
                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_zonal_manager) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    }
}

































































































































































