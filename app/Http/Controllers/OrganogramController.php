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
use App\Userorn;
use App\UserDeputation;
use App\UserDeputationOrn;
use App\UserProductCategory;
use App\UserProductCategoryOrn;

use App\State;
use App\District;
use App\Tehsil;
use App\Village;
use App\Product;
use App\Crop;
use App\ProductCropRelation;
use App\ProductCropCategory;

use App\Ticket;
use App\Farmer;

use App\Testimonial;
use App\SelfParticipation;
use App\CustomerQuery;

use App\DhanukaDoctor;
use App\DhanukaDoctorOrn;
use App\DdProductCategory;
use App\DdProductCategoryOrn;

use App\Efficiency;
use App\AssignedVillage;
use App\AssignedVillageOrn;
use App\Reminder;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use Khill\Lavacharts\Lavacharts;
class OrganogramController extends Controller
{   
    public function generateRandomString($length = 8) {
        $characters = '0123456789ABCDEFGHJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getAddBdoOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        $category_list  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $state_list     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        $language_list  = Language::select('id', 'language')->orderBy('language', 'asc')->get();
        $zonal_list     = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
        return View::make('backend.organogram_bdo.addbdo', array('zonal_list'    => $zonal_list,'state_list'=>$state_list, 'language_list'=>$language_list, 'category_list'=>$category_list));
    }
    public function postCreateBdoOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first(); 
        if ($userData) {
            $my_data        = Input::all();        
            $rules  = array(
                            'zonal_manager'         => 'required',
                            'regional_manager'      => 'required',
                            'sales_regional_manager'=> 'required',
                            'area_manager'          => 'required',
                            'sales_area_manager'    => 'required',
                            'state'                 => 'required',
                            'district'              => 'required',
                            'language'              => 'required',
                            'department'            => 'required',
                            'designation'           => 'required',
                            'bdo_code'              => 'required',
                            'name'                  => 'required',
                            'mobile_no'             => 'required',
                            'bdo_hq'                => 'required',
                            'product_category'      => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $zonal_manager_id           = $my_data['zonal_manager'];
            $regional_manager_id        = $my_data['regional_manager'];
            $sales_regional_manager_id  = $my_data['sales_regional_manager'];
            $area_manager_id            = $my_data['area_manager'];
            $sales_area_manager_id      = $my_data['sales_area_manager'];
            $state_id                   = $my_data['state'];
            $district_id                = $my_data['district'];
            $language_id                = $my_data['language'];
            $department                 = $my_data['department'];
            $designation                = $my_data['designation'];
            $counterpart_fl                = $my_data['counterpart_fl'];
            $bdo_code                   = $my_data['bdo_code'];
            $name                       = $my_data['name'];
            $mobile_no                  = $my_data['mobile_no'];
            $bdo_hq                     = $my_data['bdo_hq'];
            $product_category           = $my_data['product_category'];
            
            $app_permission_edit_ticket     = isset($my_data['app_permission_edit_ticket'])?'1':'0';
            $app_permission_close_ticket    = isset($my_data['app_permission_close_ticket'])?'1':'0';
            
            $check_dd_code         = DhanukaDoctor::where('ECN', $bdo_code)->count();
            $check_bdo_code        = User::where('ECN', $bdo_code)->count();

            if($check_dd_code > 0 || $check_bdo_code > 0) {
                $errors = array('your_custom_error' => 'ECN already exists, Please choose a different ECN.');
                return Redirect::back()->withErrors($errors)->withInput();
            }
            else 
            {
                if(count($product_category)>0) { 
                    $random_string  = $this->generateRandomString(8);
                    $password       = \Hash::make($random_string);
                    $insert_bdo     = array(       
                                                'ECN'                       => $bdo_code,
                                                'password'                  => $password,
                                                'dash'                      => $random_string,
                                                'zonal_manager_id'          => $zonal_manager_id,
                                                'regional_manager_id'       => $regional_manager_id,
                                                'sales_regional_manager_id' => $sales_regional_manager_id,
                                                'area_manager_id'           => $area_manager_id,
                                                'sales_area_manager_id'     => $sales_area_manager_id,
                                                'state_id'                  => $state_id,
                                                'district_id'               => $district_id,
                                                'language_id'               => $language_id,
                                                'department'                => $department,
                                                'designation'               => $designation, 
                                                'counterpart_fl'               => $counterpart_fl,                           
                                                'bdo_code'                  => $bdo_code,
                                                'name'                      => $name,                            
                                                'bdo_mobile_no'             => $mobile_no,                            
                                                'bdo_hq'                    => $bdo_hq, 
                                                'status'                    => '1',
                                                'app_permission_edit_ticket'    => $app_permission_edit_ticket,
                                                'app_permission_close_ticket'   => $app_permission_close_ticket,
                                                'updated_at'                => date('Y-m-d H:i:s')
                                            ); 
                    $insert_user    = Userorn::firstOrCreate($insert_bdo);
                    $result         = $insert_user->users_orn_id;

                    if($result) {
                        $user_product_category = array();
                        foreach($product_category AS $product_category_id) {
                            $user_product_category[]    = array(
                                                                'user_id'                   => $result,
                                                                'product_crop_category_id'  => $product_category_id,
                                                                'status'                    => '1',
                                                                'orn_id' => $result
                                                                );
                        }
                        $insert_user_product_category   = UserProductCategoryOrn::insert($user_product_category);
                        
                        Session::flash('message', "BDO Successfully Created. Please wait for Super Admin approval.");
                        return redirect('organogram/bdo-list-orn');
                    }
                    else {
                        Session::flash('message', "An error occurred. Please try again later.");
                        return Redirect::back()->withInput()->withErrors($validator);
                    }
                }
                else {
                    Session::flash('message', "Please choose category and try again.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            }
        }
    }    
    public function getBdoListOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.organogram_bdo.bdolist');
    }    
    public function postAllbdolistorn(Request $request) {
        //print_r(Request::all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        //print_r(Request::all()); exit;
        // $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();

        $zonalId = "";
        $status  = "";
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('state')->first();
        if ($userData1) {
            $state_ids = str_replace("]", "", str_replace("[", "", $userData1->state));
            if ($status != '') {
                $zonalId = "where u.state_id IN (".$state_ids.") and u.status = '".$status."'";
            } else{
                $zonalId = "where u.state_id IN (".$state_ids.")";
            }
        }

        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        
        if(empty($search_array['value']))
        {
            $sorting    =   '';
            $query      =   
                            " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*
                            FROM
                                (
                                SELECT
                                    u.bdo_code,
                                    u.bdo_hq,
                                    u.name,
                                    u.bdo_mobile_no,
                                    u.department,
                                    u.designation,
                                    s.state,
                                    d.district,
                                    l.language, 
                                    zm.zonal_manager_name,
                                    zm.zonal_manager_mobile,
                                    zm.zonal_manager_pincode,
                                    rm.regional_manager_name,
                                    rm.regional_manager_mobile,
                                    rm.regional_manager_pincode,
                                    srm.sales_regional_manager_name,
                                    srm.sales_regional_manager_mobile,
                                    srm.sales_regional_manager_pincode,
                                    am.area_manager_name,
                                    am.area_manager_mobile,
                                    am.area_manager_pincode,
                                    sam.sales_area_manager_name,
                                    sam.sales_area_manager_mobile,
                                    sam.sales_area_manager_pincode,
                                    
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN ud.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as deputation_status,
                                
                                    u.ECN,
                                    u.dash,
                                    DATE_FORMAT(u.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,
                                
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                
                                    (CASE WHEN u.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    u.id
                                    
                                FROM        users u
                                LEFT JOIN   active_logs                 al      ON u.id=al.user_id AND al.user_type='bdo' 
                                
                                LEFT JOIN   zonal_managers              zm      ON u.zonal_manager_id=zm.zonal_manager_id
                                LEFT JOIN   regional_managers           rm      ON u.regional_manager_id=rm.regional_manager_id
                                LEFT JOIN   sales_regional_managers     srm     ON u.sales_regional_manager_id=srm.sales_regional_manager_id
                                LEFT JOIN   area_managers               am      ON u.area_manager_id=am.area_manager_id
                                LEFT JOIN   sales_area_managers         sam     ON u.sales_area_manager_id=sam.sales_area_manager_id
                                LEFT JOIN   states                      s       ON u.state_id=s.state_id
                                LEFT JOIN   districts                   d       ON u.district_id=d.district_id
                                LEFT JOIN   languages                   l       ON u.language_id=l.id
                                
                                LEFT JOIN   user_product_categories     upc     ON upc.user_id=u.id and upc.status=1 
                                LEFT JOIN   product_crop_categories     pcc     ON pcc.product_crop_category_id=upc.product_crop_category_id and pcc.status=1
                                 
                                LEFT JOIN   user_deputations            ud      ON ud.user_id=u.id
                                ".$zonalId."
                                GROUP BY u.id
                                ORDER BY
                                    u.created_at DESC
                                ) A,
                                (select @a:=0) initvars";
            $top_open_ticket    = collect(DB::select($query));            
            //print_r($top_open_ticket);
            if (count($top_open_ticket)>0) {
                foreach ($top_open_ticket as $key => $value) {
                  $checkStatus = DB::table('users_orn')
                                 ->where('id',$top_open_ticket[$key]->id)
                                 ->where('orn_status','0')
                                 ->get();
                    if (count($checkStatus)>0) {
                        if ($top_open_ticket[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $top_open_ticket[$key]->status = 'ACTIVE';
                        }
                        elseif ($top_open_ticket[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $top_open_ticket[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $top_open_ticket[$key]->status = 'PENDING';
                        }
                    }                
                }               
            }        
            return Datatables::of($top_open_ticket)->make();
            
        }
        else {
            $search = $search_array['value'];             
            $query  =   
                        " 
                        SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                u.bdo_code,
                                u.bdo_hq,
                                u.name,
                                u.bdo_mobile_no,
                                u.department,
                                u.designation,
                                s.state,
                                d.district,
                                l.language, 
                                zm.zonal_manager_name,
                                zm.zonal_manager_mobile,
                                zm.zonal_manager_pincode,
                                rm.regional_manager_name,
                                rm.regional_manager_mobile,
                                rm.regional_manager_pincode,
                                srm.sales_regional_manager_name,
                                srm.sales_regional_manager_mobile,
                                srm.sales_regional_manager_pincode,
                                am.area_manager_name,
                                am.area_manager_mobile,
                                am.area_manager_pincode,
                                sam.sales_area_manager_name,
                                sam.sales_area_manager_mobile,
                                sam.sales_area_manager_pincode,

                                GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                (CASE WHEN ud.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as deputation_status,

                                u.ECN,
                                u.dash,
                                DATE_FORMAT(u.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,
                                
                                DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,

                                (CASE WHEN u.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                u.id

                            FROM        users u
                            LEFT JOIN   active_logs                 al      ON u.id=al.user_id AND al.user_type='bdo' 
                            
                            LEFT JOIN   zonal_managers              zm      ON u.zonal_manager_id=zm.zonal_manager_id
                            LEFT JOIN   regional_managers           rm      ON u.regional_manager_id=rm.regional_manager_id
                            LEFT JOIN   sales_regional_managers     srm     ON u.sales_regional_manager_id=srm.sales_regional_manager_id
                            LEFT JOIN   area_managers               am      ON u.area_manager_id=am.area_manager_id
                            LEFT JOIN   sales_area_managers         sam     ON u.sales_area_manager_id=sam.sales_area_manager_id
                            LEFT JOIN   states                      s       ON u.state_id=s.state_id
                            LEFT JOIN   districts                   d       ON u.district_id=d.district_id
                            LEFT JOIN   languages                   l       ON u.language_id=l.id
                             
                            LEFT JOIN   user_product_categories     upc     ON upc.user_id=u.id and upc.status=1 
                            LEFT JOIN   product_crop_categories     pcc     ON pcc.product_crop_category_id=upc.product_crop_category_id
                            
                            LEFT JOIN   user_deputations            ud      ON ud.user_id=u.id
                            
                            ".$zonalId."
                            
                            GROUP BY u.id
                            
                            ORDER BY
                                u.created_at DESC
                            ) A,
                            (select @a:=0) initvars                   
                        WHERE 
                            (
                                A.name                          LIKE '%".$search."%'    OR
                                A.bdo_code                      LIKE '%".$search."%'    OR
                                A.bdo_mobile_no                 LIKE '%".$search."%'    OR
                                A.state                         LIKE '%".$search."%'    OR
                                A.district                      LIKE '%".$search."%'    OR
                                A.zonal_manager_name            LIKE '%".$search."%'    OR
                                A.regional_manager_name         LIKE '%".$search."%'    OR
                                A.sales_regional_manager_name   LIKE '%".$search."%'    OR
                                A.area_manager_name             LIKE '%".$search."%'    OR
                                A.sales_area_manager_name       LIKE '%".$search."%' 
                            ) 
                        ";
            $top_open_ticket    = collect(DB::select($query));
            if (count($top_open_ticket)>0) {
                foreach ($top_open_ticket as $key => $value) {
                  $checkStatus = DB::table('users_orn')
                                 ->where('id',$top_open_ticket[$key]->id)
                                 ->where('orn_status','0')
                                 ->get();
                    if (count($checkStatus)>0) {
                        if ($top_open_ticket[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $top_open_ticket[$key]->status = 'ACTIVE';
                        }
                        elseif ($top_open_ticket[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $top_open_ticket[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $top_open_ticket[$key]->status = 'PENDING';
                        }
                    }                
                }               
            }           
            return Datatables::of($top_open_ticket)->make();            
        }
    }
    public function postUpdateBdoStatusOrn(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $bdo_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $bdo_data    = User::where('id', $bdo_id)->first();
            if($bdo_data) {
                $bdo_data_status = ($bdo_data->status=='0'?'1':'0');
                $insert_bdo     = array( 
                                        'id'                        => $bdo_data->id,      
                                        'ECN'                       => $bdo_data->ECN,
                                        'password'                  => $bdo_data->password,
                                        'api_token'                 => $bdo_data->api_token,
                                        'dash'                      => $bdo_data->dash,
                                        'zonal_manager_id'          => $bdo_data->zonal_manager_id,
                                        'regional_manager_id'       => $bdo_data->regional_manager_id,
                                        'sales_regional_manager_id' => $bdo_data->sales_regional_manager_id,
                                        'area_manager_id'           => $bdo_data->area_manager_id,
                                        'sales_area_manager_id'     => $bdo_data->sales_area_manager_id,
                                        'state_id'                  => $bdo_data->state_id,
                                        'district_id'               => $bdo_data->district_id,
                                        'language_id'               => $bdo_data->language_id,
                                        'department'                => $bdo_data->department,
                                        'designation'               => $bdo_data->designation,
                                        'bdo_code'                  => $bdo_data->bdo_code,
                                        'name'                      => $bdo_data->name,                            
                                        'bdo_mobile_no'              => $bdo_data->bdo_mobile_no,                            
                                        'bdo_hq'                     => $bdo_data->bdo_hq, 
                                        'app_permission_edit_ticket' => $bdo_data->app_permission_edit_ticket,
                                        'app_permission_close_ticket' => $bdo_data->app_permission_close_ticket,
                                        'device_id'                 => $bdo_data->device_id,
                                        'created_at'                => $bdo_data->created_at,
                                        'updated_at'                => date('Y-m-d H:i:s'),
                                        'status'                    => $bdo_data_status
                                        );
                $insert_user    = Userorn::firstOrCreate($insert_bdo);
                $result         = $insert_user->users_orn_id;
                if ($result) {
                    echo '1';    
                }
                else
                {
                    echo '2';
                }
                // $bdo_data_status = ($bdo_data->status=='0'?'1':'0');
                // $update_bdo      = DB::table('users')
                //                     ->where('id', $bdo_id)
                //                     ->update(['status'=>$bdo_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    public function getEditBdoOrn($userId) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($userId!='') {
                $user_data = User::where('id', $userId)->first();
                if($user_data) {
                    $state_list             = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                    $district_list          = District::where('status', '1')->select('district_id', 'district')->where(['state_id' => $user_data->state_id])->orderBy('district', 'asc')->get();
                    $language_list          = Language::select('id', 'language')->orderBy('language', 'asc')->get();
                    $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
                    $zonal_list             = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
                    $regional_list          = RegionalManager::where('status', '1')->select('regional_manager_id', 'regional_manager_name')->where(['zonal_manager_id' => $user_data->zonal_manager_id])->orderBy('regional_manager_name', 'asc')->get();
                    $sales_regional_list    = SalesRegionalManager::where('status', '1')->select('sales_regional_manager_id', 'sales_regional_manager_name')->where(['zonal_manager_id' => $user_data->zonal_manager_id])->orderBy('sales_regional_manager_name', 'asc')->get();
                    $area_list              = AreaManager::where('status', '1')->select('area_manager_id', 'area_manager_name')->where(['regional_manager_id' => $user_data->regional_manager_id])->orderBy('area_manager_name', 'asc')->get();
                    $sales_area_list        = SalesAreaManager::where('status', '1')->select('sales_area_manager_id', 'sales_area_manager_name')->where(['regional_manager_id' => $user_data->regional_manager_id])->orderBy('sales_area_manager_name', 'asc')->get();
                     $counterpart_fl = User::where('designation', 'COUNTERPART-FL')->get()->toArray();

                    $deputation_list        = UserDeputation::where('status', '1')->where('user_id', $userId)->first();

                    // dd($deputation_list);
                    // die;
                    $user_product_category  = UserProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('user_id', $userId)->where('status', '1')->first();
                    $category_list          = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();

        return View::make('backend.organogram_bdo.editbdo', array( 'zonal_list'            => $zonal_list,
                        'regional_list'         => $regional_list,
                        'sales_regional_list'   => $sales_regional_list,
                        'area_list'             => $area_list,
                        'sales_area_list'       => $sales_area_list,
                        'userdata'              => $user_data, 
                        'state_list'            => $state_list,
                        'district_list'         => $district_list, 
                        'language_list'         => $language_list, 
                        'deputation_list'       => $deputation_list,
                        'counterpart_fl'       => $counterpart_fl,
                        
                        'user_category_list'    => $user_product_category, 
                        'category_list'         => $category_list
                    ));       
                }
                else {
                    return redirect('/');
                }
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }    
    public function postUpdateBdoOrn(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();            
            
            $bdo_id                     = $my_data['_bdo'];
            $zonal_manager_id           = $my_data['zonal_manager'];
            $regional_manager_id        = $my_data['regional_manager'];
            $sales_regional_manager_id  = $my_data['sales_regional_manager'];
            $area_manager_id            = $my_data['area_manager'];
            $sales_area_manager_id      = $my_data['sales_area_manager'];
            $state_id                   = $my_data['state'];
            $district_id                = $my_data['district'];
            $language_id                = $my_data['language'];
            $department                 = $my_data['department'];
            $designation                = $my_data['designation'];
            $counterpart_fl             = $my_data['counterpart_fl'];
            $bdo_code                   = $my_data['bdo_code'];
            $name                       = $my_data['name'];
            $mobile_no                  = $my_data['mobile_no'];
            $bdo_hq                     = $my_data['bdo_hq'];  
            $product_category           = $my_data['product_category'];          
            $app_permission_edit_ticket = isset($my_data['app_permission_edit_ticket'])?'1':'0';
            $app_permission_close_ticket= isset($my_data['app_permission_close_ticket'])?'1':'0';  

            $change_password            = isset($my_data['change_password'])?'1':'0';                       
            
            $on_deputation                          = isset($my_data['on_deputation'])?'1':'0';               
            $deputation_zonal_manager_id            = $my_data['deputation_zonal_manager'];
            $deputation_regional_manager_id         = $my_data['deputation_regional_manager'];
            $deputation_sales_regional_manager_id   = $my_data['deputation_sales_regional_manager'];
            $deputation_area_manager_id             = isset($my_data['deputation_area_manager']) ? $my_data['deputation_area_manager'] : 0;
            $deputation_sales_area_manager_id       = isset($my_data['deputation_sales_area_manager']) ? $my_data['deputation_sales_area_manager'] : 0;

            $deputation_from_date             = isset($my_data['deputation_from_date']) ? $my_data['deputation_from_date'] : '0000-00-00';
            $deputation_to_date             = isset($my_data['deputation_to_date']) ? $my_data['deputation_to_date'] : '0000-00-00';
            
            $check_bdo_code             = User::where('id', '!=', $bdo_id)
                                            ->where('ECN', $bdo_code)
                                            ->count();
            $query  =   "
                        (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                        UNION
                        (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                        UNION
                        (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                        UNION
                        (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                        UNION
                        (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                        UNION
                        (SELECT dd_id AS id FROM dhanuka_doctor WHERE ECN='".$bdo_code."' LIMIT 0,1)
                        ";
            $user   = DB::select(DB::raw($query));
                
                
            if($check_bdo_code>0 || count($user)>0) {                    
                $errors->add('your_custom_error',   "ECN already exists, Please choose a different ECN.");
                return Redirect::back()->withInput()->withErrors($errors);
            }
            else {
                if ($designation != 'BDO-FL') {
                    $counterpart_fl = 0;
                }
                $updated_values         = array(
                                                'ECN'                       => $bdo_code,
                                                'zonal_manager_id'          => $zonal_manager_id,
                                                'regional_manager_id'       => $regional_manager_id,
                                                'sales_regional_manager_id' => $sales_regional_manager_id,
                                                'area_manager_id'           => $area_manager_id,
                                                'sales_area_manager_id'     => $sales_area_manager_id,
                                                'state_id'                  => $state_id,
                                                'district_id'               => $district_id,
                                                'language_id'               => $language_id,
                                                'department'                => $department,
                                                'designation'               => $designation,
                                                'counterpart_fl'            => $counterpart_fl,                             
                                                'bdo_code'                  => $bdo_code,
                                                'name'                      => $name,                            
                                                'bdo_mobile_no'             => $mobile_no,                            
                                                'bdo_hq'                    => $bdo_hq, 
                                                
                                                'app_permission_edit_ticket'    => $app_permission_edit_ticket,
                                                'app_permission_close_ticket'   => $app_permission_close_ticket,
                                                'id' => $bdo_id
                                            ); 
                if($change_password=='1'){
                    $random_string              = $this->generateRandomString(8);
                    $password                   = \Hash::make($random_string);
                    $updated_values['password'] = $password;
                    $updated_values['dash']     = $random_string;
                } 
                


                if($on_deputation=='1') {
                    if (isset($random_string) && !empty($random_string)) {
                        $asmData = User::where('id', $bdo_id)->first();
                        $updated_values['api_token']    = $asmData->api_token;
                        $updated_values['status']       = $asmData->status;
                        $updated_values['created_at']   = $asmData->created_at;
                        $updated_values['device_id']    = $asmData->device_id;
                        $updated_values['updated_at']   = date('Y-m-d H:i:s');

                    }
                    else
                    {
                        $asmData = User::where('id', $bdo_id)->first();
                        $updated_values['password']     = $asmData->password;
                        $updated_values['dash']         = $asmData->dash;
                        $updated_values['api_token']    = $asmData->api_token;
                        $updated_values['created_at']   = $asmData->created_at;
                        $updated_values['device_id']    = $asmData->device_id;
                        $updated_values['status']       = $asmData->status;
                        $updated_values['updated_at']   = date('Y-m-d H:i:s');
                    }
                    
                    $insert_user    = Userorn::firstOrCreate($updated_values);
                    $result         = $insert_user->users_orn_id;
                    if ($result) 
                    {

                        if($deputation_zonal_manager_id!='' && $deputation_regional_manager_id!='' && $deputation_sales_regional_manager_id!='' && $deputation_area_manager_id!='' && $deputation_sales_area_manager_id!='' && $deputation_from_date!='' && $deputation_to_date!='') {

                            $insert_bdo_deputation  = UserDeputationOrn::create([       
                                                        'user_id'                   => $bdo_id,
                                                        'zonal_manager_id'          => $deputation_zonal_manager_id,
                                                        'regional_manager_id'       => $deputation_regional_manager_id,
                                                        'sales_regional_manager_id' => $deputation_sales_regional_manager_id,
                                                        'area_manager_id'           => $deputation_area_manager_id,
                                                        'sales_area_manager_id'     => $deputation_sales_area_manager_id,
                                                        'deputation_from_date' => $deputation_from_date,
                                                        'deputation_to_date' => $deputation_to_date,
                                                        'status'                    => '1',
                                                        'orn_id' => $result,
                                                        'updated_at'                => date('Y-m-d H:i:s'),
                                                        'created_at'                => date('Y-m-d H:i:s')
                                                    ]); 
                            
                            
                            foreach($product_category AS $category_id) {
                                $user_manager_states   = array(
                                                                'user_id'                   => $bdo_id,
                                                                'product_crop_category_id'  => $category_id,
                                                                'status'                    => '1',
                                                                'orn_id' => $result
                                                                );
                                $insert_user_manager   = UserProductCategoryOrn::firstOrCreate($user_manager_states);
                            }
                            Session::flash('message', "BDO Successfully Updated. Please wait for Super Admin approval.");
                            return redirect('organogram/bdo-list-orn');
                            
                        }
                        else {
                            Session::flash('message', "BDO Successfully Updated. Please wait for Super Admin approval.");
                            return redirect('organogram/bdo-list-orn');
                        }
                    }
                    else
                    {
                        Session::flash('message', "Please try again.");
                        return redirect('organogram/bdo-list-orn');
                    }
                }
                else 
                {   
                    if (isset($random_string) && !empty($random_string)) {
                        $asmData = User::where('id', $bdo_id)->first();
                        $updated_values['api_token']    = $asmData->api_token;
                        $updated_values['status']       = $asmData->status;
                        $updated_values['created_at']   = $asmData->created_at;
                        $updated_values['device_id']    = $asmData->device_id;
                        $updated_values['updated_at']   = date('Y-m-d H:i:s');

                    }
                    else
                    {
                        $asmData = User::where('id', $bdo_id)->first();
                        $updated_values['password']     = $asmData->password;
                        $updated_values['dash']         = $asmData->dash;
                        $updated_values['api_token']    = $asmData->api_token;
                        $updated_values['created_at']   = $asmData->created_at;
                        $updated_values['device_id']    = $asmData->device_id;
                        $updated_values['status']       = $asmData->status;
                        $updated_values['updated_at']   = date('Y-m-d H:i:s');
                    }

                    $insert_user    = Userorn::firstOrCreate($updated_values);
                    $result         = $insert_user->users_orn_id;
                    if ($result) 
                    {
                        
                        $uData = UserDeputation::where('user_id', $bdo_id)->first();
                        if (count($uData)>0) 
                        {
                            $Dudata = array(    
                                                'user_deputation_id'        => $uData->user_deputation_id,
                                                'user_id'                   => $uData->user_id,
                                                'zonal_manager_id'          => $uData->zonal_manager_id,
                                                'regional_manager_id'       => $uData->regional_manager_id,
                                                'sales_regional_manager_id' => $uData->sales_regional_manager_id,
                                                'area_manager_id'           => $uData->area_manager_id,
                                                'sales_area_manager_id'     => $uData->sales_area_manager_id,
                                                'deputation_from_date' => $deputation_from_date,
                                                'deputation_to_date' => $deputation_to_date,
                                                'status'                    => $uData->status,
                                                'orn_id' => $result,
                                                'created_at'                => $uData->created_at 
                                            );
                            $insert_user    = UserDeputationOrn::firstOrCreate($Dudata); 
                        }
                        
                        foreach($product_category AS $category_id) {
                            $user_manager_states   = array(
                                                            'user_id'                   => $bdo_id,
                                                            'product_crop_category_id'  => $category_id,
                                                            'status'                    => '1',
                                                            'orn_id' => $result
                                                            );
                            $insert_user_manager   = UserProductCategoryOrn::firstOrCreate($user_manager_states);
                        }
                        
                        Session::flash('message', "BDO Successfully Updated. Please wait for Super Admin approval.");
                        return redirect('organogram/bdo-list-orn');
                    }
                    else
                    {
                        Session::flash('message', "Please try again.");
                        return redirect('organogram/bdo-list-orn');
                    }
                }
            }
        }
    } 
    
    //dd organogram
    public function postCheckDdEcn(Request $request) {        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();             
            if($my_data) {
                $dd_code       = $my_data['dd_code'];
                $dd_id         = $my_data['_dd'];

                $check_dd_code         = DhanukaDoctor::where('ECN', $dd_code)->where('dd_id','!=',$dd_id)->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$dd_code."' LIMIT 0,1)
                            ";

                $user   = DB::select(DB::raw($query));
                echo ($check_dd_code > 0 || count($user) > 0 ? 'false':'true');
            }
        }

        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $my_data    = Input::all();             
            if($my_data) {
                $dd_code       = $my_data['dd_code'];
                $dd_id         = $my_data['_dd'];

                $check_dd_code         = DhanukaDoctor::where('ECN', $dd_code)->where('dd_id','!=',$dd_id)->count();
                $query  =   "
                            (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                            UNION
                            (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                            UNION
                            (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                            UNION
                            (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                            UNION
                            (SELECT id AS id FROM users WHERE ECN='".$dd_code."' LIMIT 0,1)
                            ";

                $user   = DB::select(DB::raw($query));
                echo ($check_dd_code > 0 || count($user) > 0 ? 'false':'true');
            }
        }
    } 
    
    //DD
    public function getAddDdOrn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        $category_list  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
        $state_list     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        $language_list  = Language::select('id', 'language')->orderBy('language', 'asc')->get();
        $zonal_list     = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
       // print_r($state_list); exit;
        return View::make('backend.organogram_dd.addDD', array('zonal_list' => $zonal_list,'state_list'=>$state_list, 'language_list'=>$language_list, 'category_list'=>$category_list));
    }
    public function postCreateDdOrn(Request $request) {       
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        //print_r($my_data); exit;
        if ($userData) {
            $my_data        = Input::all();    
            $rules  = array(
                            'zonal_manager'         => 'required',
                            'regional_manager'      => 'required',
                            'sales_regional_manager'=> 'required',
                            'area_manager'          => 'required',
                            'sales_area_manager'    => 'required',
                            'bdo_id'                => 'required',
                            'state'                 => 'required',
                            'district'              => 'required',
                            'language'              => 'required',
                            'department'            => 'required',
                            'designation'           => 'required',
                            'dd_code'               => 'required',
                            'name'                  => 'required',
                            'mobile_no'             => 'required',
                            'dd_hq'                 => 'required',
                            'product_category'      => 'required|array',
                            'dd_email'              => 'required',
                            'dd_address'            => 'required',
                            'tehsil'                => 'required',
                            'village'               => 'required'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $zonal_manager_id           = $my_data['zonal_manager'];
            $regional_manager_id        = $my_data['regional_manager'];
            $sales_regional_manager_id  = $my_data['sales_regional_manager'];
            $area_manager_id            = $my_data['area_manager'];
            $sales_area_manager_id      = $my_data['sales_area_manager'];
            $state_id                   = $my_data['state'];
            $district_id                = $my_data['district'];
            $language_id                = $my_data['language'];
            $department                 = $my_data['department'];
            $designation                = $my_data['designation'];
            $dd_code                    = $my_data['dd_code'];
            $name                       = $my_data['name'];
            $mobile_no                  = $my_data['mobile_no'];
            $dd_hq                      = $my_data['dd_hq'];
            $product_category           = $my_data['product_category'];
            $dd_email                   = $my_data['dd_email'];
            $dd_address                 = $my_data['dd_address'];
            $bdo_id                     = $my_data['bdo_id'];
            $dd_alt_mobile_no           = isset($my_data['dd_alt_mobile_no'])?$my_data['dd_alt_mobile_no']:'';
            
            $app_permission_edit_ticket     = isset($my_data['app_permission_edit_ticket'])?'1':'0';
            $app_permission_close_ticket    = isset($my_data['app_permission_close_ticket'])?'1':'0';
            $tehsil                 = $my_data['tehsil'];
            $village                 = $my_data['village'];
            
            $check_dd_code         = DhanukaDoctor::where('ECN', $dd_code)->count();
            $query  =   "
                        (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                        UNION
                        (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                        UNION
                        (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                        UNION
                        (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                        UNION
                        (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$dd_code."' LIMIT 0,1)
                        UNION
                        (SELECT id AS id FROM users WHERE ECN='".$dd_code."' LIMIT 0,1)
                        ";

            $user   = DB::select(DB::raw($query));
            if($check_dd_code > 0 || count($user) > 0) {
                
                //$errors->add('your_custom_error', "ECN already exists, Please choose a different ECN.");
                $errors = array('your_custom_error' => 'ECN already exists, Please choose a different ECN.');
                return Redirect::back()->withInput()->withErrors($errors);
            }
            else 
            {   
                if(count($product_category)>0) { 
                    $random_string  = $this->generateRandomString(8);
                    $password       = \Hash::make($random_string);
                    $insert_dd     = array(       
                                    'ECN'                       => $dd_code,
                                    'password'                  => $password,
                                    'dash'                      => $random_string,
                                    'zonal_manager_id'          => $zonal_manager_id,
                                    'regional_manager_id'       => $regional_manager_id,
                                    'sales_regional_manager_id' => $sales_regional_manager_id,
                                    'area_manager_id'           => $area_manager_id,
                                    'sales_area_manager_id'     => $sales_area_manager_id,
                                    'state_id'                  => $state_id,
                                    'district_id'               => $district_id,
                                    'language_id'               => $language_id,
                                    'department'                => $department,
                                    'designation'               => $designation,
                                    'dd_code'                   => $dd_code,
                                    'name'                      => $name,                            
                                    'dd_mobile_no'              => $mobile_no,                            
                                    'dd_hq'                     => $dd_hq, 
                                    'status'                    => '1',
                                    'dd_alt_mobile_no'          => $dd_alt_mobile_no,
                                    'app_permission_edit_ticket' => $app_permission_edit_ticket,
                                    'app_permission_close_ticket' => $app_permission_close_ticket,
                                    'dd_email'                  => $dd_email,
                                    'dd_address'                => $dd_address,
                                    'bdo_id'                    => $bdo_id,
                                    'tehsil_id'                 => $tehsil,
                                    'updated_at'                => date('Y-m-d H:i:s')
                                            ); 
                    $insert_user    = DhanukaDoctorOrn::firstOrCreate($insert_dd);
                    $result         = $insert_user->dhanuka_doctor_orn_id;
                    if($result) {
                        $dd_product_category = array();
                        foreach($product_category AS $product_category_id) {
                            $dd_product_category[]    = array(
                                                            'dd_id' => $result,
                                                            'product_crop_category_id' => $product_category_id,
                                                            'status' => '1',
                                                            'orn_id' => $result
                                                            );
                        }
                        $insert_dd_product_category = DdProductCategoryOrn::insert($dd_product_category);

                        //insert village data
                        $dd_village_data = array();
                        foreach($village AS $village_id) {
                            $dd_village_data[] = array(
                                                        'assigned_to_dd_id' => $result,
                                                        'assigned_by_bdo_id' => $bdo_id,
                                                        'village_id' => $village_id,
                                                        'orn_id' => $result,
                                                        'created_at' => date('Y-m-d H:i:s'),
                                                        'updated_at' => date('Y-m-d H:i:s')
                                                        );
                        }
                        $insert_dd_village_data = AssignedVillageOrn::insert($dd_village_data); 
                        Session::flash('message', "DD successfully created. Please wait for Super Admin approval.");
                        return redirect('organogram/dd-list-orn');
                    }
                    else 
                    {   
                        Session::flash('message', "An error occurred. Please try again later.");
                        return Redirect::back()->withInput()->withErrors($validator);
                    }
                }
                else {
                    Session::flash('message', "Please choose category and try again.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            
                
            }
        }
    }    
    public function getDdListOrn() {
    	$sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.organogram_dd.DDlist');
    }  

    public function postAllddlistorn(Request $request) {
        //print_r(Request::all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }

        //$zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
		/*$state_Id = DB::table('branch_admin')->where(['id' => $sess])->select('state')->first();
        $state=json_decode($state_Id->state);
        $state_id=implode(",",$state);	*/

        $zonalId = "";
        $status  = "";
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('state')->first();
        if ($userData1) {
            $state_ids = str_replace("]", "", str_replace("[", "", $userData1->state));
            if ($status != '') {
                $zonalId = "where u.state_id IN (".$state_ids.") and u.status = '".$status."'";
            } else{
                $zonalId = "where u.state_id IN (".$state_ids.")";
            }
        }

        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     
        $column_array   = $value['columns'];    
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        
        if(empty($search_array['value']))
        {
            $sorting    =   '';
            $query      =   
                            " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*
                            FROM
                                (
                                SELECT
                                    u.dd_code,
                                    u.dd_hq,
                                    u.name,
                                    u.dd_mobile_no,
                                    u.department,
                                    u.designation,
                                    s.state,
                                    d.district,
                                    l.language, 
                                    zm.zonal_manager_name,
                                    zm.zonal_manager_mobile,
                                    zm.zonal_manager_pincode,
                                    rm.regional_manager_name,
                                    rm.regional_manager_mobile,
                                    rm.regional_manager_pincode,
                                    srm.sales_regional_manager_name,
                                    srm.sales_regional_manager_mobile,
                                    srm.sales_regional_manager_pincode,
                                    am.area_manager_name,
                                    am.area_manager_mobile,
                                    am.area_manager_pincode,
                                    sam.sales_area_manager_name,
                                    sam.sales_area_manager_mobile,
                                    sam.sales_area_manager_pincode,
                                    
                                    GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                    (CASE WHEN ud.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as deputation_status,
                                
                                    u.ECN,
                                    u.dash,
                                    DATE_FORMAT(u.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,
                                
                                    DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,
                                
                                    (CASE WHEN u.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    u.dd_id,
                                    us.name as flname,
                                    us.bdo_mobile_no,
                                    us.designation as fldesignation
                                    
                                FROM        dhanuka_doctor u
                                LEFT JOIN   active_logs                 al      ON u.dd_id=al.user_id AND al.user_type='dhanuka_doctor' 
                                
                                LEFT JOIN   users              us      ON u.bdo_id=us.id
                                LEFT JOIN   zonal_managers              zm      ON u.zonal_manager_id=zm.zonal_manager_id
                                LEFT JOIN   regional_managers           rm      ON u.regional_manager_id=rm.regional_manager_id
                                LEFT JOIN   sales_regional_managers     srm     ON u.sales_regional_manager_id=srm.sales_regional_manager_id
                                LEFT JOIN   area_managers               am      ON u.area_manager_id=am.area_manager_id
                                LEFT JOIN   sales_area_managers         sam     ON u.sales_area_manager_id=sam.sales_area_manager_id
                                LEFT JOIN   states                      s       ON u.state_id=s.state_id
                                LEFT JOIN   districts                   d       ON u.district_id=d.district_id
                                LEFT JOIN   languages                   l       ON u.language_id=l.id
                                
                                LEFT JOIN   user_product_categories     upc     ON upc.user_id=u.dd_id and upc.status=1 
                                LEFT JOIN   product_crop_categories     pcc     ON pcc.product_crop_category_id=upc.product_crop_category_id and pcc.status=1
                                 
                                LEFT JOIN   user_deputations            ud      ON ud.user_id=u.dd_id
                                ".$zonalId." 
                                GROUP BY u.dd_id
                                ORDER BY
                                    u.created_at DESC
                                ) A,
                                (select @a:=0) initvars";
            $top_open_ticket    = collect(DB::select($query)); 
            if (count($top_open_ticket)>0) {
                foreach ($top_open_ticket as $key => $value) {
                  $checkStatus = DB::table('dhanuka_doctor_orn')
                                 ->where('dd_id',$top_open_ticket[$key]->dd_id)
                                 ->where('orn_status','0')
                                 ->get();
                    if (count($checkStatus)>0) {
                        if ($top_open_ticket[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $top_open_ticket[$key]->status = 'ACTIVE';
                        }
                        elseif ($top_open_ticket[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $top_open_ticket[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $top_open_ticket[$key]->status = 'PENDING';
                        }
                    }                
                }               
            }           
            //print_r($top_open_ticket);die;
            return Datatables::of($top_open_ticket)->make();
            
        }
        else 
        {
            $search = $search_array['value'];             
            $query  =   
                        " 
                        SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                u.dd_code,
                                u.dd_hq,
                                u.name,
                                u.dd_mobile_no,
                                u.department,
                                u.designation,
                                s.state,
                                d.district,
                                l.language, 
                                zm.zonal_manager_name,
                                zm.zonal_manager_mobile,
                                zm.zonal_manager_pincode,
                                rm.regional_manager_name,
                                rm.regional_manager_mobile,
                                rm.regional_manager_pincode,
                                srm.sales_regional_manager_name,
                                srm.sales_regional_manager_mobile,
                                srm.sales_regional_manager_pincode,
                                am.area_manager_name,
                                am.area_manager_mobile,
                                am.area_manager_pincode,
                                sam.sales_area_manager_name,
                                sam.sales_area_manager_mobile,
                                sam.sales_area_manager_pincode,

                                GROUP_CONCAT(DISTINCT(pcc.category_name) SEPARATOR ',\n')  AS category_name,
                                (CASE WHEN ud.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as deputation_status,

                                u.ECN,
                                u.dash,
                                DATE_FORMAT(u.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,
                                
                                DATE_FORMAT(al.last_access_time, '%Y-%m-%d, %h:%i %p') as last_access_time,

                                (CASE WHEN u.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                u.dd_id,
                                us.name as flname,
                                us.bdo_mobile_no

                            FROM        dhanuka_doctor u
                            LEFT JOIN   active_logs                 al      ON u.dd_id=al.user_id AND al.user_type='dhanuka_doctor' 
                            LEFT JOIN   users              us      ON u.bdo_id=us.id
                            LEFT JOIN   zonal_managers              zm      ON u.zonal_manager_id=zm.zonal_manager_id
                            LEFT JOIN   regional_managers           rm      ON u.regional_manager_id=rm.regional_manager_id
                            LEFT JOIN   sales_regional_managers     srm     ON u.sales_regional_manager_id=srm.sales_regional_manager_id
                            LEFT JOIN   area_managers               am      ON u.area_manager_id=am.area_manager_id
                            LEFT JOIN   sales_area_managers         sam     ON u.sales_area_manager_id=sam.sales_area_manager_id
                            LEFT JOIN   states                      s       ON u.state_id=s.state_id
                            LEFT JOIN   districts                   d       ON u.district_id=d.district_id
                            LEFT JOIN   languages                   l       ON u.language_id=l.id
                             
                            LEFT JOIN   user_product_categories     upc     ON upc.user_id=u.dd_id and upc.status=1 
                            LEFT JOIN   product_crop_categories     pcc     ON pcc.product_crop_category_id=upc.product_crop_category_id
                            
                            LEFT JOIN   user_deputations            ud      ON ud.user_id=u.dd_id
                            ".$zonalId." 
                            
                            GROUP BY u.dd_id

                            ORDER BY
                                u.created_at DESC
                            ) A,
                            (select @a:=0) initvars                   
                        WHERE 
                            (
                                A.name                          LIKE '%".$search."%'    OR
                                A.dd_code                      LIKE '%".$search."%'    OR
                                A.dd_mobile_no                 LIKE '%".$search."%'    OR
                                A.state                         LIKE '%".$search."%'    OR
                                A.district                      LIKE '%".$search."%'    OR
                                A.zonal_manager_name            LIKE '%".$search."%'    OR
                                A.regional_manager_name         LIKE '%".$search."%'    OR
                                A.sales_regional_manager_name   LIKE '%".$search."%'    OR
                                A.area_manager_name             LIKE '%".$search."%'    OR
                                A.sales_area_manager_name       LIKE '%".$search."%' 
                            )
                        ";
            $top_open_ticket = collect(DB::select($query));
            if (count($top_open_ticket)>0) {
                foreach ($top_open_ticket as $key => $value) {
                  $checkStatus = DB::table('dhanuka_doctor_orn')
                                 ->where('dd_id',$top_open_ticket[$key]->dd_id)
                                 ->where('orn_status','0')
                                 ->get();
                    if (count($checkStatus)>0) {
                        if ($top_open_ticket[$key]->status == 'ACTIVE' && $checkStatus[0]->status == '1') {
                            $top_open_ticket[$key]->status = 'ACTIVE';
                        }
                        elseif ($top_open_ticket[$key]->status == 'INACTIVE' && $checkStatus[0]->status == '0') {
                            $top_open_ticket[$key]->status = 'INACTIVE';
                        }
                        else
                        {
                            $top_open_ticket[$key]->status = 'PENDING';
                        }
                    }                
                }               
            }   
            // print_r($top_open_ticket);die;
            return Datatables::of($top_open_ticket)->make();            
        }
    }
    public function postUpdateDdStatusOrn(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $dd_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $dd_data    = DhanukaDoctor::where('dd_id', $dd_id)->first();
            if($dd_data) { //echo "<pre>";print_r($dd_data);die;
                $dd_data_status = ($dd_data->status=='0'?'1':'0');
                $insert_dd     = array( 
                                        'dd_id'                     => $dd_data->dd_id,      
                                        'ECN'                       => $dd_data->ECN,
                                        'password'                  => $dd_data->password,
                                        'api_token'                 => $dd_data->api_token,
                                        'dash'                      => $dd_data->dash,
                                        'zonal_manager_id'          => $dd_data->zonal_manager_id,
                                        'regional_manager_id'       => $dd_data->regional_manager_id,
                                        'sales_regional_manager_id' => $dd_data->sales_regional_manager_id,
                                        'area_manager_id'           => $dd_data->area_manager_id,
                                        'sales_area_manager_id'     => $dd_data->sales_area_manager_id,
                                        'state_id'                  => $dd_data->state_id,
                                        'district_id'               => $dd_data->district_id,
                                        'language_id'               => $dd_data->language_id,
                                        'department'                => $dd_data->department,
                                        'designation'               => $dd_data->designation,
                                        'dd_code'                   => $dd_data->dd_code,
                                        'name'                      => $dd_data->name,                            
                                        'dd_mobile_no'              => $dd_data->dd_mobile_no,                            
                                        'dd_hq'                     => $dd_data->dd_hq, 
                                        'dd_alt_mobile_no'          => $dd_data->dd_alt_mobile_no,
                                        'app_permission_edit_ticket' => $dd_data->app_permission_edit_ticket,
                                        'app_permission_close_ticket' => $dd_data->app_permission_close_ticket,
                                        'dd_email'                  => $dd_data->dd_email,
                                        'dd_address'                => $dd_data->dd_address,
                                        'bdo_id'                    => $dd_data->bdo_id,
                                        'tehsil_id'                 => $dd_data->tehsil_id,
                                        'device_id'                 => $dd_data->device_id,
                                        'created_at'                => $dd_data->created_at,
                                        'updated_at'                => date('Y-m-d H:i:s'),
                                        'status'                    => $dd_data_status
                                        );
                //echo "<pre>";print_r($dd_data);die;
                $insert_user    = DhanukaDoctorOrn::firstOrCreate($insert_dd);
                $result         = $insert_user->dhanuka_doctor_orn_id;
                // $dd_data_status = ($dd_data->status=='0'?'1':'0');
                // $update_dd      = DB::table('dhanuka_doctor')
                //                     ->where('dd_id', $dd_id)
                //                     ->update(['status'=>$dd_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
                if ($result) {
                    echo '1';    
                }
                else
                {
                    echo '2';
                }
                
            }
            else {
                echo '0';
            }
        }
        else {
            echo '0';
        }
    }
    public function getEditDdOrn($dd_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($dd_id!='') {
                $user_data = DhanukaDoctor::where('dd_id', $dd_id)->first();
                if($user_data) {
                    $state_list             = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                    $district_list          = District::where('status', '1')->select('district_id', 'district')->where(['state_id' => $user_data->state_id])->orderBy('district', 'asc')->get();
                    $tehsil_list          = Tehsil::where('status', '1')->select('tehsil_id', 'tehsil')->where(['district_id' => $user_data->district_id])->orderBy('tehsil', 'asc')->get();
                    $village_list          = Village::where('status', '1')->select('village_id', 'village')->where(['tehsil_id' => $user_data->tehsil_id])->orderBy('village', 'asc')->get();
                    $language_list          = Language::select('id', 'language')->orderBy('language', 'asc')->get();
                    $zonalId = DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
                    $zonal_list             = ZonalManager::where('status', '1')->where('zonal_manager_id',$zonalId->zonal_manager_id)->get();
                    $regional_list          = RegionalManager::where('status', '1')->select('regional_manager_id', 'regional_manager_name')->where(['zonal_manager_id' => $user_data->zonal_manager_id])->orderBy('regional_manager_name', 'asc')->get();
                    $sales_regional_list    = SalesRegionalManager::where('status', '1')->select('sales_regional_manager_id', 'sales_regional_manager_name')->where(['zonal_manager_id' => $user_data->zonal_manager_id])->orderBy('sales_regional_manager_name', 'asc')->get();
                    $area_list              = AreaManager::where('status', '1')->select('area_manager_id', 'area_manager_name')->where(['regional_manager_id' => $user_data->regional_manager_id])->orderBy('area_manager_name', 'asc')->get();
                    $sales_area_list        = SalesAreaManager::where('status', '1')->select('sales_area_manager_id', 'sales_area_manager_name')->where(['regional_manager_id' => $user_data->regional_manager_id])->orderBy('sales_area_manager_name', 'asc')->get();
                    $bdo_list        = User::where('status', '1')->select('id', 'name')->where(['sales_area_manager_id' => $user_data->sales_area_manager_id])->orderBy('name', 'asc')->get();


                    //$deputation_list        = UserDeputation::where('status', '1')->where('user_id', $userId)->first();
                    // dd($deputation_list);
                    // die;
                    $user_product_category  = DdProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('dd_id', $dd_id)->where('status', '1')->first();
                    $category_list = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();

                    $dd_village_list = AssignedVillage::select(DB::raw("GROUP_CONCAT(village_id SEPARATOR ',')  AS ddvillage_id"))->where('assigned_to_dd_id', $dd_id)->first();

                    return View::make('backend.organogram_dd.editDD', array( 'zonal_list'            => $zonal_list,
                                                                    'regional_list'         => $regional_list,
                                                                    'sales_regional_list'   => $sales_regional_list,
                                                                    'area_list'             => $area_list,
                                                                    'sales_area_list'       => $sales_area_list,
                                                                    'userdata'              => $user_data, 
                                                                    'state_list'            => $state_list,
                                                                    'district_list'         => $district_list,
                                                                    'tehsil_list'         => $tehsil_list,
                                                                    'village_list'         => $village_list,
                                                                    'dd_village_list' => $dd_village_list, 
                                                                    'language_list'         => $language_list,
                                                                    'bdo_id'                => $bdo_list, 
                                                                    //'deputation_list'       => $deputation_list,
                                                                    
                                                                    'user_category_list'    => $user_product_category, 
                                                                    'category_list'         => $category_list
                                                                ));       
                }
                else {
                    return redirect('/');
                }
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }  
    public function postUpdateDdOrn(Request $request) { 
        $date = date('Y-m-d H:i:s');        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); //echo "<pre>"; print_r($my_data);die;           
            $rules  = array(
                            'zonal_manager'         => 'required',
                            'regional_manager'      => 'required',
                            'sales_regional_manager'=> 'required',
                            'area_manager'          => 'required',
                            'sales_area_manager'    => 'required',
                            'bdo_id'                => 'required',
                            'state'                 => 'required',
                            'district'              => 'required',
                            'language'              => 'required',
                            'department'            => 'required',
                            'designation'           => 'required',
                            'dd_code'               => 'required',
                            'name'                  => 'required',
                            'mobile_no'             => 'required',
                            'dd_hq'                 => 'required',
                            'product_category'      => 'required|array',
                            'dd_email'              => 'required',
                            'dd_address'            => 'required',
                            'tehsil'                => 'required',
                            'village'               => 'required'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            //echo "<pre>";print_r($my_data['area_manager']);die;
            $dd_id                      = $my_data['dd_id'];
            $zonal_manager_id           = $my_data['zonal_manager'];
            $regional_manager_id        = $my_data['regional_manager'];
            $sales_regional_manager_id  = $my_data['sales_regional_manager'];
            $area_manager_id            = $my_data['area_manager'];
            $sales_area_manager_id      = $my_data['sales_area_manager'];
            $bdo_id                     = $my_data['bdo_id'];
            $state_id                   = $my_data['state'];
            $district_id                = $my_data['district'];
            $language_id                = $my_data['language'];
            $department                 = $my_data['department'];
            $designation                = $my_data['designation'];
            $dd_code                    = $my_data['dd_code'];
            $name                       = $my_data['name'];
            $mobile_no                  = $my_data['mobile_no'];
            $dd_hq                      = $my_data['dd_hq']; 
            $dd_alt_mobile_no           = $my_data['dd_alt_mobile_no'];
            $dd_email                   = $my_data['dd_email'];
            $dd_address                 = $my_data['dd_address']; 
            $product_category           = $my_data['product_category']; 
            $tehsil                     = $my_data['tehsil'];
            $village                    = $my_data['village'];         
            $app_permission_edit_ticket = isset($my_data['app_permission_edit_ticket'])?'1':'0';
            $app_permission_close_ticket= isset($my_data['app_permission_close_ticket'])?'1':'0';  

            $change_password            = isset($my_data['change_password'])?'1':'0';
            $bdo_id                     = $my_data['bdo_id'];                       
            
                $updated_values = array(
                                'ECN'                         => $dd_code,
                                'zonal_manager_id'            => $zonal_manager_id,
                                'regional_manager_id'         => $regional_manager_id,
                                'sales_regional_manager_id'   => $sales_regional_manager_id,
                                'area_manager_id'             => $area_manager_id,
                                'sales_area_manager_id'       => $sales_area_manager_id,
                                'state_id'                    => $state_id,
                                'district_id'                 => $district_id,
                                'language_id'                 => $language_id,
                                'department'                  => $department,
                                'designation'                 => $designation,                            
                                'dd_code'                     => $dd_code,
                                'name'                        => $name,                            
                                'dd_mobile_no'                => $mobile_no,                            
                                'dd_hq'                       => $dd_hq, 
                                'dd_alt_mobile_no'            => $dd_alt_mobile_no,
                                'dd_email'                    => $dd_email,
                                'dd_address'                  => $dd_address,
                                'app_permission_edit_ticket'  => $app_permission_edit_ticket,
                                'app_permission_close_ticket' => $app_permission_close_ticket,
                                'tehsil_id'                   => $tehsil,
                                'bdo_id'                      => $bdo_id,
                                'dd_id'                       => $dd_id
                                
                            ); 
                if($change_password=='1'){
                    $random_string              = $this->generateRandomString(8);
                    $password                   = \Hash::make($random_string);
                    $updated_values['password'] = $password;
                    $updated_values['dash']     = $random_string;
                } 
                
                if (isset($random_string) && !empty($random_string)) {
                    $ddData = DhanukaDoctor::where('dd_id', $dd_id)->first();
                    $updated_values['api_token']    = $ddData->api_token;
                    $updated_values['status']       = $ddData->status;
                    $updated_values['created_at']   = $ddData->created_at;
                    $updated_values['updated_at']   = date('Y-m-d H:i:s');
                }
                else
                {
                    $ddData = DhanukaDoctor::where('dd_id', $dd_id)->first();
                    $updated_values['password']     = $ddData->password;
                    $updated_values['dash']         = $ddData->dash;
                    $updated_values['api_token']    = $ddData->api_token;
                    $updated_values['status']       = $ddData->status;
                    $updated_values['created_at']   = $ddData->created_at;
                    $updated_values['updated_at']   = date('Y-m-d H:i:s');
                }
                
                $insert_user    = DhanukaDoctorOrn::firstOrCreate($updated_values);
                $result         = $insert_user->dhanuka_doctor_orn_id;
                if ($result) { 
                    $dd_product_category = array();
                    foreach($product_category AS $product_category_id) {
                        $dd_product_category[]    = array(
                                                        'dd_id' => $dd_id,
                                                        'product_crop_category_id' => $product_category_id,
                                                        'status' => '1',
                                                        'orn_id' => $result
                                                        );
                    }
                    $insert_dd_product_category = DdProductCategoryOrn::insert($dd_product_category);

                    //insert village data
                    $dd_village_data = array();
                    foreach($village AS $village_id) {
                        $dd_village_data[] = array(
                                                    'assigned_to_dd_id' => $dd_id,
                                                    'assigned_by_bdo_id' => $bdo_id,
                                                    'village_id' => $village_id,
                                                    'orn_id' => $result,
                                                    'created_at' => date('Y-m-d H:i:s'),
                                                    'updated_at' => date('Y-m-d H:i:s')
                                                    );
                    }
                    $insert_dd_village_data = AssignedVillageOrn::insert($dd_village_data); 
                    Session::flash('message', "DD Successfully Updated. Please wait for Super Admin approval.");
                    return redirect('organogram/dd-list-orn');
                }
                else
                {   
                     Session::flash('message', "Please try again.");
                    return redirect('organogram/dd-list-orn');
                }
               
               
           
        }
    }  

    public function getDeputationorn() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.organogram_bdo.deputationorn');
    }    
    public function postAlldeputationorn(Request $request) {
        //print_r(Request::all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        //print_r(Request::all()); exit;
        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        
        $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
        $end_date       = isset($value['end_date']) ?  $value['end_date']:'';

        $datesearch ="";
        if($start_date!='' && $end_date!='') {
                $datesearch = "and DATE(ud.deputation_from_date) >= DATE('".$start_date."') AND DATE(ud.deputation_to_date) <= DATE('".$end_date."')";
        }elseif($start_date!='') {
                $datesearch = "and DATE(ud.deputation_from_date) >= DATE('".$start_date."')";
        }
        else {
                $datesearch = "and (1=1)";
        }

        $zonalId = "";
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $zonalId = "where ud.zonal_manager_id = '".$userData->zonal_manager_id."' and ud.status = '1' ".$datesearch;
        }

        /*$userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
        if ($userData1) {
            $zonalId = "where ud.zonal_manager_id = '".$userData1->zonal_manager_id."' and ud.status = '1' ".$datesearch;            
        }*/

        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('state')->first();
        if ($userData1) {
            $state_ids  = str_replace("]", "", str_replace("[", "", $userData1->state));
            $zonalId    = "where u.state_id IN (".$state_ids.")";
        }
        
        if(empty($search_array['value']))
        {
            $sorting    =   '';
            $query      =   
                            " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*
                            FROM
                                (
                                SELECT
                                    u.bdo_code,
                                    u.bdo_hq,
                                    u.name,
                                    u.bdo_mobile_no,
                                    zm.zonal_manager_name,
                                    zm.zonal_manager_mobile,
                                    zm.zonal_manager_pincode,
                                    rm.regional_manager_name,
                                    rm.regional_manager_mobile,
                                    rm.regional_manager_pincode,
                                    srm.sales_regional_manager_name,
                                    srm.sales_regional_manager_mobile,
                                    srm.sales_regional_manager_pincode,
                                    am.area_manager_name,
                                    am.area_manager_mobile,
                                    am.area_manager_pincode,
                                    sam.sales_area_manager_name,
                                    sam.sales_area_manager_mobile,
                                    sam.sales_area_manager_pincode,
                                    ud.user_deputation_id
                                FROM        user_deputations  ud  
                                LEFT JOIN   users u      ON u.id=ud.user_id 
                                LEFT JOIN   active_logs                 al      ON u.id=al.user_id AND al.user_type='bdo' 
                                
                                LEFT JOIN   zonal_managers              zm      ON ud.zonal_manager_id=zm.zonal_manager_id
                                LEFT JOIN   regional_managers           rm      ON ud.regional_manager_id=rm.regional_manager_id
                                LEFT JOIN   sales_regional_managers     srm     ON ud.sales_regional_manager_id=srm.sales_regional_manager_id
                                LEFT JOIN   area_managers               am      ON ud.area_manager_id=am.area_manager_id
                                LEFT JOIN   sales_area_managers         sam     ON ud.sales_area_manager_id=sam.sales_area_manager_id
                                
                                  ".$zonalId."                          
                                GROUP BY u.id
                                ORDER BY
                                    ud.created_at DESC
                                ) A,
                                (select @a:=0) initvars";
            $top_open_ticket    = collect(DB::select($query));            
            //print_r($top_open_ticket);
            return Datatables::of($top_open_ticket)->make();
            
        }
        else {
            $search = $search_array['value'];             
            $query  =   
                        " 
                        SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                u.bdo_code,
                                    u.bdo_hq,
                                    u.name,
                                    u.bdo_mobile_no,
                                    zm.zonal_manager_name,
                                    zm.zonal_manager_mobile,
                                    zm.zonal_manager_pincode,
                                    rm.regional_manager_name,
                                    rm.regional_manager_mobile,
                                    rm.regional_manager_pincode,
                                    srm.sales_regional_manager_name,
                                    srm.sales_regional_manager_mobile,
                                    srm.sales_regional_manager_pincode,
                                    am.area_manager_name,
                                    am.area_manager_mobile,
                                    am.area_manager_pincode,
                                    sam.sales_area_manager_name,
                                    sam.sales_area_manager_mobile,
                                    sam.sales_area_manager_pincode,
                                    ud.user_deputation_id
                                FROM        user_deputations ud 
                                LEFT JOIN   users u      ON u.id=ud.user_id
                                LEFT JOIN   active_logs                 al      ON u.id=al.user_id AND al.user_type='bdo' 
                                
                                LEFT JOIN   zonal_managers              zm      ON ud.zonal_manager_id=zm.zonal_manager_id
                                LEFT JOIN   regional_managers           rm      ON ud.regional_manager_id=rm.regional_manager_id
                                LEFT JOIN   sales_regional_managers     srm     ON ud.sales_regional_manager_id=srm.sales_regional_manager_id
                                LEFT JOIN   area_managers               am      ON ud.area_manager_id=am.area_manager_id
                                LEFT JOIN   sales_area_managers         sam     ON ud.sales_area_manager_id=sam.sales_area_manager_id
                             ".$zonalId." 
                            GROUP BY u.id

                            ORDER BY
                                ud.created_at DESC
                            ) A,
                            (select @a:=0) initvars                   
                        WHERE 
                            (
                                A.name                          LIKE '%".$search."%'    OR
                                A.bdo_code                      LIKE '%".$search."%'    OR
                                A.bdo_mobile_no                 LIKE '%".$search."%'    OR
                                A.zonal_manager_name            LIKE '%".$search."%'    OR
                                A.regional_manager_name         LIKE '%".$search."%'    OR
                                A.sales_regional_manager_name   LIKE '%".$search."%'    OR
                                A.area_manager_name             LIKE '%".$search."%'    OR
                                A.sales_area_manager_name       LIKE '%".$search."%' 
                            ) 
                        ";
            $top_open_ticket    = collect(DB::select($query));
            return Datatables::of($top_open_ticket)->make();            
        }
    } 
    // public function postUpdateDdOrn--(Request $request) { 
    //     $date = date('Y-m-d H:i:s');        
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
    //     if ($userData) {
    //         $my_data        = Input::all(); //echo "<pre>"; print_r($my_data);die;           
    //         $rules  = array(
    //                         'zonal_manager'         => 'required',
    //                         'regional_manager'      => 'required',
    //                         'sales_regional_manager'=> 'required',
    //                         'area_manager'          => 'required',
    //                         'sales_area_manager'    => 'required',
    //                         'bdo_id'                => 'required',
    //                         'state'                 => 'required',
    //                         'district'              => 'required',
    //                         'language'              => 'required',
    //                         'department'            => 'required',
    //                         'designation'           => 'required',
    //                         'dd_code'               => 'required',
    //                         'name'                  => 'required',
    //                         'mobile_no'             => 'required',
    //                         'dd_hq'                 => 'required',
    //                         'product_category'      => 'required|array',
    //                         'dd_email'              => 'required',
    //                         'dd_address'            => 'required',
    //                         'tehsil'                => 'required',
    //                         'village'               => 'required'
    //                     );
    //         $validator  = Validator::make($my_data, $rules);
    //         if ($validator->fails()) {
    //             return Redirect::back()->withInput()->withErrors($validator);
    //         }
    //         //echo "<pre>";print_r($my_data['area_manager']);die;
    //         $dd_id                      = $my_data['dd_id'];
    //         $zonal_manager_id           = $my_data['zonal_manager'];
    //         $regional_manager_id        = $my_data['regional_manager'];
    //         $sales_regional_manager_id  = $my_data['sales_regional_manager'];
    //         $area_manager_id            = $my_data['area_manager'];
    //         $sales_area_manager_id      = $my_data['sales_area_manager'];
    //         $bdo_id                     = $my_data['bdo_id'];
    //         $state_id                   = $my_data['state'];
    //         $district_id                = $my_data['district'];
    //         $language_id                = $my_data['language'];
    //         $department                 = $my_data['department'];
    //         $designation                = $my_data['designation'];
    //         $dd_code                    = $my_data['dd_code'];
    //         $name                       = $my_data['name'];
    //         $mobile_no                  = $my_data['mobile_no'];
    //         $dd_hq                      = $my_data['dd_hq']; 
    //         $dd_alt_mobile_no           = $my_data['dd_alt_mobile_no'];
    //         $dd_email                   = $my_data['dd_email'];
    //         $dd_address                 = $my_data['dd_address']; 
    //         $product_category           = $my_data['product_category']; 
    //         $tehsil                     = $my_data['tehsil'];
    //         $village                    = $my_data['village'];         
    //         $app_permission_edit_ticket = isset($my_data['app_permission_edit_ticket'])?'1':'0';
    //         $app_permission_close_ticket= isset($my_data['app_permission_close_ticket'])?'1':'0';  

    //         $change_password            = isset($my_data['change_password'])?'1':'0';
    //         $bdo_id                     = $my_data['bdo_id'];                       
            
    //         if(!empty($my_data['name']) && !empty($my_data['mobile_no']))
    //         {
    //             $ddExist = DhanukaDoctor::where('name', '=', $my_data['name'])->where('dd_mobile_no','=', $my_data['mobile_no'])->where('dd_id','=',$my_data['dd_id'])->count();
              
    //             if($ddExist == 0)
    //             {
    //                $getData = DhanukaDoctor::where('dd_id',$my_data['dd_id'])->get()->toArray();
    //                $historyData['dd_id']= $getData[0]['dd_id'];
    //                $historyData['name']= $getData[0]['name'];
    //                $historyData['dd_mobile_no']= $getData[0]['dd_mobile_no'];
    //                $historyData['from']= $getData[0]['updated_at'];
    //                $historyData['end']= $date;
    //                // echo "<pre>"; print_r($historyData); die;
                  
    //                DB::table('dd_history')->insert($historyData);

    //             }                
    //         }

    //         // if($check_dd_code>0 || count($user)>0) {  
    //         //     $errors->add('your_custom_error',   "ECN already exists, Please choose a different ECN.");
    //         //     return Redirect::back()->withInput()->withErrors($errors);
    //         // }
    //         // else 
    //         // {   
    //             $updated_values = array(
    //                             'ECN'                         => $dd_code,
    //                             'zonal_manager_id'            => $zonal_manager_id,
    //                             'regional_manager_id'         => $regional_manager_id,
    //                             'sales_regional_manager_id'   => $sales_regional_manager_id,
    //                             'area_manager_id'             => $area_manager_id,
    //                             'sales_area_manager_id'       => $sales_area_manager_id,
    //                             'state_id'                    => $state_id,
    //                             'district_id'                 => $district_id,
    //                             'language_id'                 => $language_id,
    //                             'department'                  => $department,
    //                             'designation'                 => $designation,                            
    //                             'dd_code'                     => $dd_code,
    //                             'name'                        => $name,                            
    //                             'dd_mobile_no'                => $mobile_no,                            
    //                             'dd_hq'                       => $dd_hq, 
    //                             'dd_alt_mobile_no'            => $dd_alt_mobile_no,
    //                             'dd_email'                    => $dd_email,
    //                             'dd_address'                  => $dd_address,
    //                             'app_permission_edit_ticket'  => $app_permission_edit_ticket,
    //                             'app_permission_close_ticket' => $app_permission_close_ticket,
    //                             'tehsil_id'                   => $tehsil,
    //                             'bdo_id'                      => $bdo_id
                                
    //                         ); 
    //             if($change_password=='1'){
    //                 $random_string              = $this->generateRandomString(8);
    //                 $password                   = \Hash::make($random_string);
    //                 $updated_values['password'] = $password;
    //                 $updated_values['dash']     = $random_string;
    //             } 
                
    //             //$result = DB::table('dhanuka_doctor')->where('dd_id', $dd_id)->update($updated_values);
    //             //print_r($result);die; 
    //             if (isset($random_string) && !empty($random_string)) {
    //                 $result=DhanukaDoctor::find($dd_id);
    //                 $result->ECN=$dd_code;
    //                 $result->zonal_manager_id=$zonal_manager_id;
    //                 $result->regional_manager_id=$regional_manager_id;
    //                 $result->sales_regional_manager_id=$sales_regional_manager_id;
    //                 $result->area_manager_id=$area_manager_id;
    //                 $result->sales_area_manager_id= $sales_area_manager_id;
    //                 $result->bdo_id= $bdo_id;
    //                 $result->state_id=$state_id;
    //                 $result->district_id=$district_id;
    //                 $result->language_id=$language_id;
    //                 $result->department=$department;
    //                 $result->designation=$designation;
    //                 $result->dd_code=$dd_code;
    //                 $result->name=$name;
    //                 $result->dd_mobile_no=$mobile_no;
    //                 $result->dd_hq=$dd_hq;
    //                 $result->dd_alt_mobile_no=$dd_alt_mobile_no;
    //                 $result->dd_email=$dd_email;
    //                 $result->dd_address=$dd_address;
    //                 $result->app_permission_edit_ticket=$app_permission_edit_ticket;
    //                 $result->app_permission_close_ticket=$app_permission_close_ticket;
    //                 $result->tehsil_id=$tehsil;
    //                 $result->bdo_id=$bdo_id;
    //                 $result->dash=$random_string;
    //                 $result->password=$password;
    //                 $result->save();
    //             }
    //             else
    //             {
    //                 $result=DhanukaDoctor::find($dd_id);
    //                 $result->ECN=$dd_code;
    //                 $result->zonal_manager_id=$zonal_manager_id;
    //                 $result->regional_manager_id=$regional_manager_id;
    //                 $result->sales_regional_manager_id=$sales_regional_manager_id;
    //                 $result->area_manager_id=$area_manager_id;
    //                 $result->sales_area_manager_id= $sales_area_manager_id;
    //                 $result->bdo_id= $bdo_id;
    //                 $result->state_id=$state_id;
    //                 $result->district_id=$district_id;
    //                 $result->language_id=$language_id;
    //                 $result->department=$department;
    //                 $result->designation=$designation;
    //                 $result->dd_code=$dd_code;
    //                 $result->name=$name;
    //                 $result->dd_mobile_no=$mobile_no;
    //                 $result->dd_hq=$dd_hq;
    //                 $result->dd_alt_mobile_no=$dd_alt_mobile_no;
    //                 $result->dd_email=$dd_email;
    //                 $result->dd_address=$dd_address;
    //                 $result->app_permission_edit_ticket=$app_permission_edit_ticket;
    //                 $result->app_permission_close_ticket=$app_permission_close_ticket;
    //                 $result->tehsil_id=$tehsil;
    //                 $result->bdo_id=$bdo_id;
    //                 $result->save();
    //             }
                
                
    //             if ($result) { 
    //                 $reset_dd_category = DB::table('dd_product_categories')
    //                                     ->where('dd_id', $dd_id)
    //                                     ->update(['status'=>'0']); 
    //                 foreach($product_category AS $category_id) {
    //                     $update_dd_manager   = DB::table('dd_product_categories')
    //                                                 ->where('dd_id', $dd_id)
    //                                                 ->where('product_crop_category_id', $category_id)
    //                                                 ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
    //                     $dd_manager_states   = array(
    //                                                     'dd_id'                   => $dd_id,
    //                                                     'product_crop_category_id'  => $category_id,
    //                                                     'status'                    => '1'
    //                                                     );
    //                     $insert_dd_manager   = DdProductCategory::firstOrCreate($dd_manager_states);
    //                 }

    //                 //dd village update
    //                 $reset_dd_delete    = DB::table('assigned_village')->where('assigned_to_dd_id', $dd_id)->delete(); 
    //                 $dd_village_data = array();
    //                 foreach($village AS $village_id) {
    //                     $dd_village_data[] = array(
    //                                                 'assigned_to_dd_id' => $dd_id,
    //                                                 'assigned_by_bdo_id' => $bdo_id,
    //                                                 'village_id' => $village_id,
    //                                                 'created_at' => date('Y-m-d H:i:s'),
    //                                                 'updated_at' => date('Y-m-d H:i:s')
    //                                                 );
    //                 }
    //                 $insert_dd_village_data = AssignedVillage::insert($dd_village_data); 
    //                 Session::flash('message', "DD Successfully Updated.");
    //                 return redirect('admin/dd-list');
    //             }
    //             else
    //             {   
    //                  Session::flash('message', "Please try again.");
    //                 return redirect('admin/dd-list');
    //             }
               
               
    //         //}
    //     }
    // }

    
}
