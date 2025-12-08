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
use App\RegionalManagerOrn;
use App\RegionalManagerState;
use App\RegionalManagerStateOrn;
use App\RegionalManagerProductCategory;
use App\RegionalManagerProductCategoryOrn;
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

class regionalManagerController extends Controller {
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
    public function postCheckRegionalEcn(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();             
            if($my_data) {
                $regional_manager_code      = $my_data['regional_manager_hq_code'];
                $regional_manager_id        = $my_data['_regional'];
                $check_regional_manager_code= RegionalManager::where('regional_manager_id', '!=', $regional_manager_id)
                                                ->where('ECN', $regional_manager_code)
                                                ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$regional_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$regional_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$regional_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$regional_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$regional_manager_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                echo ($check_regional_manager_code>0 || count($user)>0?'false':'true');
            }
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData1) {
            $my_data    = Input::all();             
            if($my_data) {
                $regional_manager_code      = $my_data['regional_manager_hq_code'];
                $regional_manager_id        = $my_data['_regional'];
                $check_regional_manager_code= RegionalManager::where('regional_manager_id', '!=', $regional_manager_id)
                                                ->where('ECN', $regional_manager_code)
                                                ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$regional_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$regional_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$regional_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$regional_manager_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$regional_manager_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                if ($check_regional_manager_code>0 || count($user)>0) {
                    echo 'false';die;
                }else{
                    echo 'true';die;
                }
               // echo ($check_regional_manager_code>0 || count($user)>0?'false':'true');
            }
        }
    }
    public function getAddRegionalManager() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $category_list      = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $zonal_manager_list = ZonalManager::where('status', '1')->orderBy('zonal_manager_name', 'asc')->get();
        $state_list         = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        return View::make('backend.regional_manager.addregionalmanager', array('state_list'=>$state_list, 'zonal_manager_list'=>$zonal_manager_list, 'category_list'=>$category_list));
    }
    public function postCreateRegionalManager(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'zonal_manager'             => 'required|numeric',
                            'regional_manager_hq_code'  => 'required|string',
                            'regional_name'             => 'required|string',
                            'regional_mobile_number'    => 'required|numeric',
                            'regional_state'            => 'required|array',
                            'regional_pincode'          => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $zonal_manager              = $my_data['zonal_manager'];
            $regional_manager_hq_code   = $my_data['regional_manager_hq_code'];
            $regional_name              = $my_data['regional_name'];
            $regional_mobile_number     = $my_data['regional_mobile_number'];
            $regional_state             = $my_data['regional_state'];
            $regional_pincode           = $my_data['regional_pincode'];
            $product_category           = $my_data['product_category'];
            if(count($regional_state)>0) { 
                if(count($product_category)>0) {               
                    $check_regional_manager_code  = RegionalManager::where('ECN', $regional_manager_hq_code)->count();
                    if($check_regional_manager_code>0) {
                        Session::flash('message', "ECN already exists, Please choose a different ECN.");
                        return Redirect::back()->withInput()->withErrors($validator);
                    }
                    else {
                        $adminController        = new adminController;
                        $random_string          = $adminController->generateRandomString(8);
                        $password               = \Hash::make($random_string);
                        $regional_manager_array     = array(
                                                        'ECN'                       => $regional_manager_hq_code,
                                                        'dash'                      => $random_string,
                                                        'password'                  => $password,
                                                        'zonal_manager_id'          => $zonal_manager,
                                                        'regional_manager_hq_code'  => $regional_manager_hq_code,
                                                        'regional_manager_name'     => $regional_name,
                                                        'regional_manager_mobile'   => $regional_mobile_number,
                                                        'regional_manager_pincode'  => $regional_pincode,
                                                        'status'                    => '1'
                                                    );
                        $insert_regional_manager    = RegionalManager::firstOrCreate($regional_manager_array);
                        $result                     = $insert_regional_manager->regional_manager_id;

                        if($result) {
                            $regional_manager_states   = array();
                            foreach($regional_state AS $state_id) {
                                $regional_manager_states[] = array(
                                                                'regional_manager_id'   => $result,
                                                                'state_id'              => $state_id,
                                                                'status'                => '1'
                                                                );
                            }                    
                            $insert_regional_manager_states= RegionalManagerState::insert($regional_manager_states);
                            
                            
                            $regional_manager_product_category = array();
                            foreach($product_category AS $product_category_id) {
                                $regional_manager_product_category[]   = array(
                                                                        'regional_manager_id'       => $result,
                                                                        'product_crop_category_id'  => $product_category_id,
                                                                        'status'                    => '1'
                                                                        );
                            }
                            $insert_regional_manager_product_category  = RegionalManagerProductCategory::insert($regional_manager_product_category);
                            
                            Session::flash('message', "Regional Manager Successfully Created.");
                            return redirect('regional/regional-manager-list');                 
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
    public function getRegionalManagerList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.regional_manager.regionalmanagerlist');
    }
    public function postAllregionalmanagerlist(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        } 
        $value          = Input::all();
        $status1         = isset($value['status']) ? $value['status'] :'';
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        
        $zonalId = "";
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if ($status1 != '') {
                $zonalId = "and rm.status = '".$status1."'";
            }
            else{
                $zonalId = "";
            }
        }

        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        if ($userData1) {
            if ($status1 != '') {
                $zonalId = "and rm.zonal_manager_id = ".$userData1->zonal_manager_id."' and rm.status = '".$status1."'";
            }
            else{
                $zonalId = "and rm.zonal_manager_id = ".$userData1->zonal_manager_id;
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
                                    rm.regional_manager_hq_code,
                                    zm.zonal_manager_name,
                                    rm.regional_manager_name,
                                    rm.regional_manager_mobile,
                                    rm.regional_manager_pincode,                                                                   
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN rm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    rm.ECN,
                                    rm.dash,                                    
                                    DATE_FORMAT(rm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                    rm.regional_manager_id,
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        regional_managers                   rm
                                LEFT JOIN   active_logs                         al      ON rm.regional_manager_id=al.user_id AND al.user_type='regional'                   
                                LEFT JOIN   zonal_managers                      zm      ON zm.zonal_manager_id=rm.zonal_manager_id
                                LEFT JOIN   regional_manager_states             rms     ON rms.regional_manager_id=rm.regional_manager_id                                
                                LEFT JOIN   regional_manager_product_categories rmpc    ON rmpc.regional_manager_id=rm.regional_manager_id and rmpc.status=1
                                LEFT JOIN   states                              s       ON rms.state_id=s.state_id
                                LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=rmpc.product_crop_category_id and  pcc.status=1
                                
                                WHERE
                                    1=1 ".$zonalId."       
                                GROUP BY rms.regional_manager_id
                                )   A,
                                (select @a:=0) initvars";
            $regional_manager  = collect(DB::select($query)); 
            foreach ($regional_manager as $key => $value) {
                $states = DB::table('regional_manager_states as zms')
                          ->join('states as s','s.state_id','=','zms.state_id')  
                          ->where('regional_manager_id',$regional_manager[$key]->regional_manager_id)
                          ->where('zms.status','1')
                          ->select(DB::raw("GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n') as state"))
                          ->get();
                if (count($states)>0) 
                {
                    $regional_manager[$key]->states = $states[0]->state;
                }         
            }
            return Datatables::of($regional_manager)->make();
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
                                rm.regional_manager_hq_code,
                                zm.zonal_manager_name,
                                rm.regional_manager_name,
                                rm.regional_manager_mobile,
                                rm.regional_manager_pincode,                                                                   
                                GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                (CASE WHEN rm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                rm.ECN,
                                rm.dash,                                    
                                DATE_FORMAT(rm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                rm.regional_manager_id,
                                GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                            FROM        regional_managers                   rm
                            LEFT JOIN   active_logs                         al      ON rm.regional_manager_id=al.user_id AND al.user_type='regional'  
                            LEFT JOIN   zonal_managers                      zm      ON zm.zonal_manager_id=rm.zonal_manager_id
                            LEFT JOIN   regional_manager_states             rms     ON rms.regional_manager_id=rm.regional_manager_id                                
                            LEFT JOIN   regional_manager_product_categories rmpc    ON rmpc.regional_manager_id=rm.regional_manager_id and rmpc.status=1
                            LEFT JOIN   states                              s       ON rms.state_id=s.state_id
                            LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=rmpc.product_crop_category_id
                            WHERE
                                (                                
                                rm.regional_manager_name        LIKE '%".$search."%'    OR
                                rm.regional_manager_mobile      LIKE '%".$search."%'    OR
                                rm.regional_manager_pincode     LIKE '%".$search."%'    OR
                                rm.ECN                          LIKE '%".$search."%'    OR
                                s.state                         LIKE '%".$search."%'
                                )   ".$zonalId." 
                            GROUP BY rms.regional_manager_id
                            )   A,
                            (select @a:=0) initvars"; 
            
            $regional_manager  = collect(DB::select($query));
            foreach ($regional_manager as $key => $value) {
                $states = DB::table('regional_manager_states as zms')
                          ->join('states as s','s.state_id','=','zms.state_id')  
                          ->where('regional_manager_id',$regional_manager[$key]->regional_manager_id)
                          ->where('zms.status','1')
                          ->select(DB::raw("GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n') as state"))
                          ->get();
                if (count($states)>0) 
                {
                    $regional_manager[$key]->states = $states[0]->state;
                }         
            } 
            return Datatables::of($regional_manager)->make();            
        }
    } 
    public function getEditRegionalManager($regional_manager_id) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($regional_manager_id!='') {
                $zonal_manager              = ZonalManager::where('status', '1')->get();
                $regional_manager           = RegionalManager::where('regional_manager_id', $regional_manager_id)->first();
                $regional_manager_state     = RegionalManagerState::select(DB::raw("GROUP_CONCAT(state_id SEPARATOR ',')  AS state_id"))->where('regional_manager_id', $regional_manager_id)->where('status', '1')->first();
                $state_list                 = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                
                $regional_manager_product_category  = RegionalManagerProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('regional_manager_id', $regional_manager_id)->where('status', '1')->first();
                $category_list                      = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
                
                return View::make('backend.regional_manager.editregionalmanager', array('zonal_manager_list'=>$zonal_manager, 'regional_manager'=>$regional_manager, 'regional_manager_state'=>$regional_manager_state, 'state_list'=>$state_list, 'category_list'=>$category_list, 'regional_manager_category_list'=>$regional_manager_product_category));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    } 
    public function postUpdateRegionalManager(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'zonal_manager'             => 'required|numeric',
                            'regional_manager_hq_code'  => 'required|string',
                            'regional_name'             => 'required|string',
                            'regional_mobile_number'    => 'required|numeric',
                            'regional_state'            => 'required|array',
                            'regional_pincode'          => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $regional_id                = $my_data['regional_id'];
            $zonal_manager              = $my_data['zonal_manager'];
            $regional_manager_hq_code   = $my_data['regional_manager_hq_code'];
            $regional_name              = $my_data['regional_name'];
            $regional_mobile_number     = $my_data['regional_mobile_number'];
            $regional_state             = $my_data['regional_state'];
            $regional_pincode           = $my_data['regional_pincode'];
            $product_category           = $my_data['product_category'];
            $change_password            = isset($my_data['change_password'])?'1':'0';
            if(count($regional_state)>0) {
                $check_regional_manager_code    = RegionalManager::where('regional_manager_id', '!=', $regional_id)
                                                ->where('ECN', $regional_manager_hq_code)
                                                ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                if($check_regional_manager_code>0 || count($user)>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $regional_manager_array    = array(
                                                    'ECN'                       => $regional_manager_hq_code,
                                                    'zonal_manager_id'          => $zonal_manager,
                                                    'regional_manager_hq_code'  => $regional_manager_hq_code,
                                                    'regional_manager_name'     => $regional_name,
                                                    'regional_manager_mobile'   => $regional_mobile_number,
                                                    'regional_manager_pincode'  => $regional_pincode,
                                                    'updated_at' => date('Y-m-d H:i:s')
                                                );
                    if($change_password=='1'){
                        $random_string                          = $this->generateRandomString(8);
                        $password                               = \Hash::make($random_string);
                        $regional_manager_array['password']     = $password;
                        $regional_manager_array['dash']         = $random_string;
                    }
                    $update_regional_manager   = DB::table('regional_managers')
                                                    ->where('regional_manager_id', $regional_id)
                                                    ->where('status', '1')
                                                    ->update($regional_manager_array); 
                    $update_area_manager        = DB::table('area_managers')
                                                    ->where('regional_manager_id', $regional_id)
                                                    ->where('status', '1')
                                                    ->update(['zonal_manager_id'=>$zonal_manager]); 
                    $update_bdo                 = DB::table('users')
                                                    ->where('regional_manager_id', $regional_id)
                                                    ->where('status', '1')
                                                    ->update(['zonal_manager_id'=>$zonal_manager]); 

                    $reset_regional_manager_states   = DB::table('regional_manager_states')
                                                        ->where('regional_manager_id', $regional_id)
                                                        ->update(['status'=>'0']); 
                    foreach($regional_state AS $state_id) {
                        $update_regional_manager   = DB::table('regional_manager_states')
                                                    ->where('regional_manager_id', $regional_id)
                                                    ->where('state_id', $state_id)
                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $regional_manager_states   = array(
                                                        'regional_manager_id'  => $regional_id,
                                                        'state_id'          => $state_id,
                                                        'status'            => '1'
                                                        );
                        $insert_regional_manager   = RegionalManagerState::firstOrCreate($regional_manager_states);
                    }  
                    
                
                
                    $reset_regional_manager_category= DB::table('regional_manager_product_categories')
                                                    ->where('regional_manager_id', $regional_id)
                                                    ->update(['status'=>'0']); 
                    foreach($product_category AS $category_id) {
                        $update_regional_manager   = DB::table('regional_manager_product_categories')
                                                    ->where('regional_manager_id', $regional_id)
                                                    ->where('product_crop_category_id', $category_id)
                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                        $regional_manager_states   = array(
                                                        'regional_manager_id'       => $regional_id,
                                                        'product_crop_category_id'  => $category_id,
                                                        'status'                    => '1'
                                                        );
                        $insert_regional_manager   = RegionalManagerProductCategory::firstOrCreate($regional_manager_states);
                    }
                
                
                    Session::flash('message', "Regional Manager Successfully Updated.");
                    return redirect('regional/regional-manager-list');
                }
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    } 
    public function postUpdateRegionalStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $regional_manager_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $regional_manager_data    = RegionalManager::where('regional_manager_id', $regional_manager_id)->first();
            if($regional_manager_data) {
                $regional_manager_data_status = ($regional_manager_data->status=='0'?'1':'0');
                $update_regional_manager      = DB::table('regional_managers')
                                                ->where('regional_manager_id', $regional_manager_id)
                                                ->update(['status'=>$regional_manager_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    public function postRegionalDelete(Request $request) {         
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
                $regional_manager_id     = $my_data['_ticket'];
                $update_regional_manager = DB::table('regional_managers')
                                    ->where('regional_manager_id', $regional_manager_id)
                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_regional_manager) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    } 
    
    //Organogram ============================
    public function getAddRegionalManagerOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        $category_list      = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $zonal_manager_list = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
        $state_list         = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        return View::make('backend.organogram_regional_manager.addregionalmanager', array('state_list'=>$state_list, 'zonal_manager_list'=>$zonal_manager_list, 'category_list'=>$category_list));
    }
    public function postCreateRegionalManagerOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'zonal_manager'             => 'required|numeric',
                            'regional_manager_hq_code'  => 'required|string',
                            'regional_name'             => 'required|string',
                            'regional_mobile_number'    => 'required|numeric',
                            'regional_state'            => 'required|array',
                            'regional_pincode'          => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $zonal_manager              = $my_data['zonal_manager'];
            $regional_manager_hq_code   = $my_data['regional_manager_hq_code'];
            $regional_name              = $my_data['regional_name'];
            $regional_mobile_number     = $my_data['regional_mobile_number'];
            $regional_state             = $my_data['regional_state'];
            $regional_pincode           = $my_data['regional_pincode'];
            $product_category           = $my_data['product_category'];
            if(count($regional_state)>0) { 
                if(count($product_category)>0) {               
                    $check_regional_manager_code  = RegionalManager::where('ECN', $regional_manager_hq_code)->count();
                    if($check_regional_manager_code>0) {
                        Session::flash('message', "ECN already exists, Please choose a different ECN.");
                        return Redirect::back()->withInput()->withErrors($validator);
                    }
                    else {
                        $adminController        = new adminController;
                        $random_string          = $adminController->generateRandomString(8);
                        $password               = \Hash::make($random_string);
                        $regional_manager_array     = array(
                                                        'ECN'                       => $regional_manager_hq_code,
                                                        'dash'                      => $random_string,
                                                        'password'                  => $password,
                                                        'zonal_manager_id'          => $zonal_manager,
                                                        'regional_manager_hq_code'  => $regional_manager_hq_code,
                                                        'regional_manager_name'     => $regional_name,
                                                        'regional_manager_mobile'   => $regional_mobile_number,
                                                        'regional_manager_pincode'  => $regional_pincode,
                                                        'status'                    => '1',
                                                        'updated_at'                        => date('Y-m-d H:i:s')
                                                    );
                        $insert_regional_manager    = RegionalManagerOrn::firstOrCreate($regional_manager_array);
                        $result                     = $insert_regional_manager->regional_managers_orn_id;

                        if($result) {
                            $regional_manager_states   = array();
                            foreach($regional_state AS $state_id) {
                                $regional_manager_states[] = array(
                                                                'regional_manager_id'   => $result,
                                                                'state_id'              => $state_id,
                                                                'status'                => '1',
                                                                'orn_id' => $result
                                                                );
                            }                    
                            $insert_regional_manager_states= RegionalManagerStateOrn::insert($regional_manager_states);
                            
                            
                            $regional_manager_product_category = array();
                            foreach($product_category AS $product_category_id) {
                                $regional_manager_product_category[]   = array(
                                                                        'regional_manager_id'       => $result,
                                                                        'product_crop_category_id'  => $product_category_id,
                                                                        'status'                    => '1',
                                                                        'orn_id' => $result
                                                                        );
                            }
                            $insert_regional_manager_product_category  = RegionalManagerProductCategoryOrn::insert($regional_manager_product_category);
                            
                            Session::flash('message', "Regional Manager Successfully Created. Wait for Super Admin approval.");
                            return redirect('regional/regional-manager-list-orn');                 
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
    public function getRegionalManagerListOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.organogram_regional_manager.regionalmanagerlist');
    }  
    public function postAllregionalmanagerlistorn(Request $request) {
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
                                    rm.regional_manager_hq_code,
                                    zm.zonal_manager_name,
                                    rm.regional_manager_name,
                                    rm.regional_manager_mobile,
                                    rm.regional_manager_pincode,                                                                   
                                    GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS state,
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN rm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    rm.ECN,
                                    rm.dash,                                    
                                    DATE_FORMAT(rm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                    rm.regional_manager_id,
                                    GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                                FROM        regional_managers                   rm
                                LEFT JOIN   active_logs                         al      ON rm.regional_manager_id=al.user_id AND al.user_type='regional'                   
                                LEFT JOIN   zonal_managers                      zm      ON zm.zonal_manager_id=rm.zonal_manager_id
                                LEFT JOIN   regional_manager_states             rms     ON rms.regional_manager_id=rm.regional_manager_id                                
                                LEFT JOIN   regional_manager_product_categories rmpc    ON rmpc.regional_manager_id=rm.regional_manager_id and rmpc.status=1
                                LEFT JOIN   states                              s       ON rms.state_id=s.state_id
                                LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=rmpc.product_crop_category_id and  pcc.status=1
                                
                                WHERE
                                    rm.zonal_manager_id = '".$zonalId->zonal_manager_id."'       
                                GROUP BY rms.regional_manager_id
                                )   A,
                                (select @a:=0) initvars";
            $regional_manager  = collect(DB::select($query)); 
            if (count($regional_manager)>0) {
                foreach ($regional_manager as $key => $value) {
                    $checkStatus = DB::table('regional_managers_orn')
                                 ->where('regional_manager_id',$regional_manager[$key]->regional_manager_id)
                                 ->where('orn_status','0')
                                 ->get();
                    if (count($checkStatus)>0) {
                        if ($regional_manager[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $regional_manager[$key]->status = 'ACTIVE';
                        }
                        elseif ($regional_manager[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $regional_manager[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $regional_manager[$key]->status = 'PENDING';
                        }
                    }                
                }               
            }  
            return Datatables::of($regional_manager)->make();
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
                                rm.regional_manager_hq_code,
                                zm.zonal_manager_name,
                                rm.regional_manager_name,
                                rm.regional_manager_mobile,
                                rm.regional_manager_pincode,                                                                   
                                GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS state,
                                GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                (CASE WHEN rm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                rm.ECN,
                                rm.dash,                                    
                                DATE_FORMAT(rm.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                                DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                rm.regional_manager_id,
                                GROUP_CONCAT(DISTINCT(s.state_id) SEPARATOR ',')  AS state_id
                            FROM        regional_managers                   rm
                            LEFT JOIN   active_logs                         al      ON rm.regional_manager_id=al.user_id AND al.user_type='regional'  
                            LEFT JOIN   zonal_managers                      zm      ON zm.zonal_manager_id=rm.zonal_manager_id
                            LEFT JOIN   regional_manager_states             rms     ON rms.regional_manager_id=rm.regional_manager_id                                
                            LEFT JOIN   regional_manager_product_categories rmpc    ON rmpc.regional_manager_id=rm.regional_manager_id and rmpc.status=1
                            LEFT JOIN   states                              s       ON rms.state_id=s.state_id
                            LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=rmpc.product_crop_category_id
                            WHERE
                                (                                
                                rm.regional_manager_name        LIKE '%".$search."%'    OR
                                rm.regional_manager_mobile      LIKE '%".$search."%'    OR
                                rm.regional_manager_pincode     LIKE '%".$search."%'    OR
                                rm.ECN                          LIKE '%".$search."%'    OR
                                s.state                         LIKE '%".$search."%'
                                )  
                                and rm.zonal_manager_id = '".$zonalId->zonal_manager_id."' 
                            GROUP BY rms.regional_manager_id
                            )   A,
                            (select @a:=0) initvars"; 
            
            $regional_manager  = collect(DB::select($query));
            if (count($regional_manager)>0) {
                foreach ($regional_manager as $key => $value) {
                    $checkStatus = DB::table('regional_managers_orn')
                                 ->where('regional_manager_id',$regional_manager[$key]->regional_manager_id)
                                 ->where('orn_status','0')
                                 ->get();
                    if (count($checkStatus)>0) {
                        if ($regional_manager[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $regional_manager[$key]->status = 'ACTIVE';
                        }
                        elseif ($regional_manager[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $regional_manager[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $regional_manager[$key]->status = 'PENDING';
                        }
                    }                
                }               
            }  
            return Datatables::of($regional_manager)->make();            
        }
    }   
    public function postUpdateRegionalStatusOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $regional_manager_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $regional_manager_data    = RegionalManager::where('regional_manager_id', $regional_manager_id)->first();
            if($regional_manager_data) {
                $regional_manager_data_status = ($regional_manager_data->status=='0'?'1':'0');
                $insert_r     = array(       
                                        'regional_manager_id' => $regional_manager_data->regional_manager_id,
                                        'ECN' => $regional_manager_data->ECN,
                                        'dash' => $regional_manager_data->dash,
                                        'password' => $regional_manager_data->password,
                                        'api_token' => $regional_manager_data->api_token,
                                        'zonal_manager_id' => $regional_manager_data->zonal_manager_id,
                                        'regional_manager_hq_code' => $regional_manager_data->regional_manager_hq_code,
                                        'regional_manager_name' => $regional_manager_data->regional_manager_name,
                                        'regional_manager_mobile' => $regional_manager_data->regional_manager_mobile,
                                        'regional_manager_pincode' => $regional_manager_data->regional_manager_pincode,
                                        'status' => $regional_manager_data_status,
                                        'device_id' => $regional_manager_data->device_id,
                                        'created_at' => $regional_manager_data->created_at,
                                        'updated_at'    => date('Y-m-d H:i:s')
                                            ); 
                $insert_user    = RegionalManagerOrn::firstOrCreate($insert_r);
                $result         = $insert_user->regional_managers_orn_id;
                if($result) {
                    echo '1';
                }
                else
                {
                    echo '2';
                }
                // $update_regional_manager      = DB::table('regional_managers')
                //                                 ->where('regional_manager_id', $regional_manager_id)
                //                                 ->update(['status'=>$regional_manager_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    public function getEditRegionalManagerOrn($regional_manager_id) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($regional_manager_id!='') {
                $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
                $zonal_manager             = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
                $regional_manager           = RegionalManager::where('regional_manager_id', $regional_manager_id)->first();
                $regional_manager_state     = RegionalManagerState::select(DB::raw("GROUP_CONCAT(state_id SEPARATOR ',')  AS state_id"))->where('regional_manager_id', $regional_manager_id)->where('status', '1')->first();
                $state_list                 = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                
                $regional_manager_product_category  = RegionalManagerProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('regional_manager_id', $regional_manager_id)->where('status', '1')->first();
                $category_list                      = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
                
                return View::make('backend.organogram_regional_manager.editregionalmanager', array('zonal_manager_list'=>$zonal_manager, 'regional_manager'=>$regional_manager, 'regional_manager_state'=>$regional_manager_state, 'state_list'=>$state_list, 'category_list'=>$category_list, 'regional_manager_category_list'=>$regional_manager_product_category));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    } 
    public function postUpdateRegionalManagerOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'zonal_manager'             => 'required|numeric',
                            'regional_manager_hq_code'  => 'required|string',
                            'regional_name'             => 'required|string',
                            'regional_mobile_number'    => 'required|numeric',
                            'regional_state'            => 'required|array',
                            'regional_pincode'          => 'required|numeric',
                            'product_category'          => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $regional_id                = $my_data['regional_id'];
            $zonal_manager              = $my_data['zonal_manager'];
            $regional_manager_hq_code   = $my_data['regional_manager_hq_code'];
            $regional_name              = $my_data['regional_name'];
            $regional_mobile_number     = $my_data['regional_mobile_number'];
            $regional_state             = $my_data['regional_state'];
            $regional_pincode           = $my_data['regional_pincode'];
            $product_category           = $my_data['product_category'];
            $change_password            = isset($my_data['change_password'])?'1':'0';
            if(count($regional_state)>0) {
                $check_regional_manager_code    = RegionalManager::where('regional_manager_id', '!=', $regional_id)
                                                ->where('ECN', $regional_manager_hq_code)
                                                ->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
                            ";
                $user   = DB::select(DB::raw($query));
                if($check_regional_manager_code>0 || count($user)>0) {
                    Session::flash('message', "ECN already exists, Please choose a different ECN.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                else {
                    $regional_manager_array    = array(
                                                    'regional_manager_id'       => $regional_id,
                                                    'ECN'                       => $regional_manager_hq_code,
                                                    'zonal_manager_id'          => $zonal_manager,
                                                    'regional_manager_hq_code'  => $regional_manager_hq_code,
                                                    'regional_manager_name'     => $regional_name,
                                                    'regional_manager_mobile'   => $regional_mobile_number,
                                                    'regional_manager_pincode'  => $regional_pincode,
                                                    'updated_at' => date('Y-m-d H:i:s')
                                                );
                    if($change_password=='1'){
                        $random_string                          = $this->generateRandomString(8);
                        $password                               = \Hash::make($random_string);
                        $regional_manager_array['password']     = $password;
                        $regional_manager_array['dash']         = $random_string;
                    }

                    if (isset($random_string) && !empty($random_string)) {
                        $asmData = RegionalManager::where('regional_manager_id', $regional_id)->first();
                        $regional_manager_array['api_token']    = $asmData->api_token;
                        $regional_manager_array['status']       = $asmData->status;
                        $regional_manager_array['created_at']   = $asmData->created_at;
                        $regional_manager_array['device_id']    = $asmData->device_id;
                    }
                    else
                    {
                        $asmData = RegionalManager::where('regional_manager_id', $regional_id)->first();
                        $regional_manager_array['password']     = $asmData->password;
                        $regional_manager_array['dash']         = $asmData->dash;
                        $regional_manager_array['api_token']    = $asmData->api_token;
                        $regional_manager_array['created_at']   = $asmData->created_at;
                        $regional_manager_array['device_id']    = $asmData->device_id;
                    }

                    $insert_user    = RegionalManagerOrn::firstOrCreate($regional_manager_array);
                    $result         = $insert_user->regional_managers_orn_id;
                    if ($result) 
                    {
                        foreach($regional_state AS $state_id) {
                            $regional_manager_states   = array(
                                                            'regional_manager_id'  => $regional_id,
                                                            'state_id'          => $state_id,
                                                            'status'            => '1',
                                                            'orn_id' => $result
                                                            );
                            $insert_regional_manager   = RegionalManagerStateOrn::firstOrCreate($regional_manager_states);
                        }  
                        
                        foreach($product_category AS $category_id) {
                            $regional_manager_states   = array(
                                                            'regional_manager_id'       => $regional_id,
                                                            'product_crop_category_id'  => $category_id,
                                                            'status'                    => '1',
                                                            'orn_id' => $result
                                                            );
                            $insert_regional_manager   = RegionalManagerProductCategoryOrn::firstOrCreate($regional_manager_states);
                        }
                        
                        Session::flash('message', "Regional Manager Successfully Updated. Please wait for Super Admin approval.");
                        return redirect('regional/regional-manager-list-orn');
                    }
                    else
                    {
                        Session::flash('message', "Please try again.");
                        return redirect('regional/regional-manager-list-orn');
                    }
                }
            }
            else {
                Session::flash('message', "Please choose state and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }   
    // public function postUpdateRegionalManagerOrn(Request $request) {         
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data= Input::all();        
    //         $rules  = array(
    //                         'zonal_manager'             => 'required|numeric',
    //                         'regional_manager_hq_code'  => 'required|string',
    //                         'regional_name'             => 'required|string',
    //                         'regional_mobile_number'    => 'required|numeric',
    //                         'regional_state'            => 'required|array',
    //                         'regional_pincode'          => 'required|numeric',
    //                         'product_category'          => 'required|array'
    //                     );
    //         $validator  = Validator::make($my_data, $rules);
    //         if ($validator->fails()) {
    //             return Redirect::back()->withInput()->withErrors($validator);
    //         }
            
    //         $regional_id                = $my_data['regional_id'];
    //         $zonal_manager              = $my_data['zonal_manager'];
    //         $regional_manager_hq_code   = $my_data['regional_manager_hq_code'];
    //         $regional_name              = $my_data['regional_name'];
    //         $regional_mobile_number     = $my_data['regional_mobile_number'];
    //         $regional_state             = $my_data['regional_state'];
    //         $regional_pincode           = $my_data['regional_pincode'];
    //         $product_category           = $my_data['product_category'];
    //         $change_password            = isset($my_data['change_password'])?'1':'0';
    //         if(count($regional_state)>0) {
    //             $check_regional_manager_code    = RegionalManager::where('regional_manager_id', '!=', $regional_id)
    //                                             ->where('ECN', $regional_manager_hq_code)
    //                                             ->count();
    //             $query  =   "
    //                         (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
    //                         UNION
    //                         (SELECT id AS id FROM users WHERE ECN='".$regional_manager_hq_code."' LIMIT 0,1)
    //                         ";
    //             $user   = DB::select(DB::raw($query));
    //             if($check_regional_manager_code>0 || count($user)>0) {
    //                 Session::flash('message', "ECN already exists, Please choose a different ECN.");
    //                 return Redirect::back()->withInput()->withErrors($validator);
    //             }
    //             else {
    //                 $regional_manager_array    = array(
    //                                                 'ECN'                       => $regional_manager_hq_code,
    //                                                 'zonal_manager_id'          => $zonal_manager,
    //                                                 'regional_manager_hq_code'  => $regional_manager_hq_code,
    //                                                 'regional_manager_name'     => $regional_name,
    //                                                 'regional_manager_mobile'   => $regional_mobile_number,
    //                                                 'regional_manager_pincode'  => $regional_pincode,
    //                                                 'updated_at' => date('Y-m-d H:i:s')
    //                                             );
    //                 if($change_password=='1'){
    //                     $random_string                          = $this->generateRandomString(8);
    //                     $password                               = \Hash::make($random_string);
    //                     $regional_manager_array['password']     = $password;
    //                     $regional_manager_array['dash']         = $random_string;
    //                 }
    //                 $update_regional_manager   = DB::table('regional_managers')
    //                                                 ->where('regional_manager_id', $regional_id)
    //                                                 ->where('status', '1')
    //                                                 ->update($regional_manager_array); 
    //                 $update_area_manager        = DB::table('area_managers')
    //                                                 ->where('regional_manager_id', $regional_id)
    //                                                 ->where('status', '1')
    //                                                 ->update(['zonal_manager_id'=>$zonal_manager]); 
    //                 $update_bdo                 = DB::table('users')
    //                                                 ->where('regional_manager_id', $regional_id)
    //                                                 ->where('status', '1')
    //                                                 ->update(['zonal_manager_id'=>$zonal_manager]); 

    //                 $reset_regional_manager_states   = DB::table('regional_manager_states')
    //                                                     ->where('regional_manager_id', $regional_id)
    //                                                     ->update(['status'=>'0']); 
    //                 foreach($regional_state AS $state_id) {
    //                     $update_regional_manager   = DB::table('regional_manager_states')
    //                                                 ->where('regional_manager_id', $regional_id)
    //                                                 ->where('state_id', $state_id)
    //                                                 ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
    //                     $regional_manager_states   = array(
    //                                                     'regional_manager_id'  => $regional_id,
    //                                                     'state_id'          => $state_id,
    //                                                     'status'            => '1'
    //                                                     );
    //                     $insert_regional_manager   = RegionalManagerState::firstOrCreate($regional_manager_states);
    //                 }  
                    
                
                
    //                 $reset_regional_manager_category= DB::table('regional_manager_product_categories')
    //                                                 ->where('regional_manager_id', $regional_id)
    //                                                 ->update(['status'=>'0']); 
    //                 foreach($product_category AS $category_id) {
    //                     $update_regional_manager   = DB::table('regional_manager_product_categories')
    //                                                 ->where('regional_manager_id', $regional_id)
    //                                                 ->where('product_crop_category_id', $category_id)
    //                                                 ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
    //                     $regional_manager_states   = array(
    //                                                     'regional_manager_id'       => $regional_id,
    //                                                     'product_crop_category_id'  => $category_id,
    //                                                     'status'                    => '1'
    //                                                     );
    //                     $insert_regional_manager   = RegionalManagerProductCategory::firstOrCreate($regional_manager_states);
    //                 }
                
                
    //                 Session::flash('message', "Regional Manager Successfully Updated.");
    //                 return redirect('regional/regional-manager-list');
    //             }
    //         }
    //         else {
    //             Session::flash('message', "Please choose state and try again.");
    //             return Redirect::back()->withInput()->withErrors($validator);
    //         }
    //     }
    // } 
    // public function postRegionalDeleteOrn(Request $request) {         
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
    //             $regional_manager_id     = $my_data['_ticket'];
    //             $update_regional_manager = DB::table('regional_managers')
    //                                 ->where('regional_manager_id', $regional_manager_id)
    //                                 ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
    //             if ($update_regional_manager) {
    //                 echo 1;
    //             } else {
    //                 echo 2;
    //             }
    //         }
    //     }
    // } 
}