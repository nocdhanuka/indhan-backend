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
use App\User;
use App\AppMenu;
use App\AppMenuPermission;
use App\DataSource;
use App\State;
use App\District;
use App\Tehsil;
use App\Product;
use App\Crop;
use App\Ticket;
use App\Farmer;
use App\Language;
use App\ZonalManager;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;
use App\MarketingSupervisorProductCategory;
use App\MarketingSupervisor;
use App\MarketingSupervisorRSM;

class permissionController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAddDepartmentWise() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $menu_list              = AppMenu::where('status', '1')->orderBy('menu', 'ASC')->get();
        $zonal_list             = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('department', 'zonal')->where('status', '1')->get()->toArray();
        $regional_list          = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('department', 'regional')->where('status', '1')->get()->toArray();
        $sales_regional_list    = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('department', 'sales_regional')->where('status', '1')->get()->toArray();
        $area_list              = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('department', 'area')->where('status', '1')->get()->toArray();
        $sales_area_list        = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('department', 'sales_area')->where('status', '1')->get()->toArray();
        $marketing_supervisor_list    = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('department', 'marketing_supervisor')->where('status', '1')->get()->toArray();
        $bdo_list               = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('department', 'bdo')->where('status', '1')->get()->toArray();
        $dd_list               = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('department', 'dhanuka_doctor')->where('status', '1')->get()->toArray();
        $general_list          = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('department', 'general')->where('status', '1')->get()->toArray();
        
        return View::make('backend.permission.adddepartmentpermission', array(
                                                                            'menu_list'             => $menu_list,
                                                                            'zonal_list'            => $zonal_list, 
                                                                            'regional_list'         => $regional_list, 
                                                                            'sales_regional_list'   => $sales_regional_list,
                                                                            'area_list'             => $area_list, 
                                                                            'sales_area_list'       => $sales_area_list, 
                                                                            'marketing_supervisor_list' => $marketing_supervisor_list,
                                                                            'bdo_list'              => $bdo_list,
                                                                            'dd_list'              => $dd_list,
                                                                            'general_list'              => $general_list
                                                                            )
                        );
    }
    public function postCreateDepartmentPermission(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();        
            //dd($my_data);
            // $rules      = array(
            //                     'zonalappmenu'              => 'array',
            //                     'regionalappmenu'           => 'array',
            //                     'salesregionalappmenu'      => 'array',
            //                     'areaappmenu'               => 'array',
            //                     'salesareaappmenu'          => 'array',
            //                     'marketingsupervisorappmenu' => 'array',
            //                     'bdoappmenu'                => 'array',
            //                     'ddappmenu'                => 'array'
            //                 );
            // $validator  = Validator::make($my_data, $rules);
            // if ($validator->fails()) {
            //     return Redirect::back()->withInput()->withErrors($validator);
            // }
            
            $zonalappmenu               = (isset($my_data['zonalappmenu'])?$my_data['zonalappmenu']:array());
            $regionalappmenu            = (isset($my_data['regionalappmenu'])?$my_data['regionalappmenu']:array());
            $salesregionalappmenu       = (isset($my_data['salesregionalappmenu'])?$my_data['salesregionalappmenu']:array());
            $areaappmenu                = (isset($my_data['areaappmenu'])?$my_data['areaappmenu']:array());
            $salesareaappmenu           = (isset($my_data['salesareaappmenu'])?$my_data['salesareaappmenu']:array());
            $supervisorappmenu           = (isset($my_data['marketingsupervisorappmenu'])?$my_data['marketingsupervisorappmenu']:array());
            $bdoappmenu                 = (isset($my_data['bdoappmenu'])?$my_data['bdoappmenu']:array());
            $ddappmenu                 = (isset($my_data['ddappmenu'])?$my_data['ddappmenu']:array());
            $generalappmenu                 = (isset($my_data['genappmenu'])?$my_data['genappmenu']:array()); //genappmenu
            
            if(count($zonalappmenu)>0) {                
                $del_permission = DB::table('app_menu_permissions')
                                  ->where('department', 'zonal')
                                  ->where('user_type', 'zonal_managers')
                                  ->delete();
                $del_permission1 = DB::table('app_menu_permissions')
                                  //->orWhereNull('user_id')
                                  ->where('user_type', 'zonal')
                                  ->delete();
                foreach($zonalappmenu AS $zonal_app_menu) {
                    $insert_permission  =   AppMenuPermission::firstOrCreate(
                                                array(
                                                'department'    => 'zonal',
                                                'app_menu_id'   => $zonal_app_menu,
                                                'user_type'     => 'zonal_managers',
                                                'status'        => '1'
                                                )
                                            );
                }
            }
            if(count($regionalappmenu)>0) {
                $del_permission = DB::table('app_menu_permissions')
                                  ->where('department', 'regional')
                                  ->where('user_type', 'regional_managers')
                                  ->delete();
                $del_permission1 = DB::table('app_menu_permissions')
                                  //->orWhereNull('user_id')
                                  ->where('user_type', 'regional')
                                  ->delete();
                foreach($regionalappmenu AS $regional_app_menu) {
                    $insert_permission  =   AppMenuPermission::firstOrCreate(
                                                array(
                                                'department'    => 'regional',
                                                'app_menu_id'   => $regional_app_menu,
                                                'user_type'     => 'regional_managers',
                                                'status'        => '1'
                                                )
                                            );
                }
            }
            if(count($salesregionalappmenu)>0) {
                $del_permission = DB::table('app_menu_permissions')
                                  ->where('department', 'sales_regional')
                                  ->where('user_type', 'sales_regional_managers')
                                  ->delete();
                $del_permission1 = DB::table('app_menu_permissions')
                                  //->orWhereNull('user_id')
                                  ->where('user_type', 'sales_regional')
                                  ->delete();
                foreach($salesregionalappmenu AS $sales_regional_app_menu) {
                    $insert_permission  =   AppMenuPermission::firstOrCreate(
                                                array(
                                                'department'    => 'sales_regional',
                                                'app_menu_id'   => $sales_regional_app_menu,
                                                'user_type'     => 'sales_regional_managers',
                                                'status'        => '1'
                                                )
                                            );
                }
            }
            if(count($areaappmenu)>0) {
                $del_permission = DB::table('app_menu_permissions')
                                  ->where('department', 'area')
                                  ->where('user_type', 'area_managers')
                                  ->delete();
                $del_permission1 = DB::table('app_menu_permissions')
                                  ->orWhereNull('user_id')
                                  ->where('user_type', 'area')
                                  ->delete();
                foreach($areaappmenu AS $area_app_menu) {
                    $insert_permission  =   AppMenuPermission::firstOrCreate(
                                                array(
                                                'department'    => 'area',
                                                'app_menu_id'   => $area_app_menu,
                                                'user_type'     => 'area_managers',
                                                'status'        => '1'
                                                )
                                            );
                }
            }
            if(count($salesareaappmenu)>0) {
                $del_permission = DB::table('app_menu_permissions')
                                  ->where('department', 'sales_area')
                                  ->where('user_type', 'sales_area_managers')
                                  ->delete();
                $del_permission1 = DB::table('app_menu_permissions')
                                  //->orWhereNull('user_id')
                                  ->where('user_type', 'sales_area')
                                  ->delete();
                //print_r($salesareaappmenu); exit;
                foreach($salesareaappmenu AS $sales_area_app_menu) {
                    $insert_permission  =   AppMenuPermission::firstOrCreate(
                                                array(
                                                'department'    => 'sales_area',
                                                'app_menu_id'   => $sales_area_app_menu,
                                                'user_type'     => 'sales_area_managers',
                                                'status'        => '1'
                                                )
                                            );
                }
            }
            if (count($supervisorappmenu) > 0) { 
                $del_permission = DB::table('app_menu_permissions')
                                  ->where('department', 'marketing_supervisor')
                                  ->where('user_type', 'marketing_supervisor')
                                  ->delete();
                $del_permission1 = DB::table('app_menu_permissions')
                                  //->orWhereNull('user_id')
                                  ->where('user_type', 'marketing_supervisor')
                                  ->delete();
                foreach($supervisorappmenu AS $supervisor_app_menu) {
                    $insert_permission  =   AppMenuPermission::firstOrCreate(
                                                array(
                                                'department'    => 'marketing_supervisor',
                                                'app_menu_id'   => $supervisor_app_menu,
                                                'user_type'     => 'marketing_supervisor',
                                                'status'        => '1'
                                                )
                                            );
                }
            }
            if(count($bdoappmenu)>0) {
                $del_permission = DB::table('app_menu_permissions')
                                  ->where('department', 'bdo')
                                  ->where('user_type', 'users')
                                  ->delete();
                $del_permission1 = DB::table('app_menu_permissions')
                                  //->orWhereNull('user_id')
                                  ->where('user_type', 'bdo')
                                  ->delete();
                foreach($bdoappmenu AS $bdo_app_menu) {
                    $insert_permission  =   AppMenuPermission::firstOrCreate(
                                                array(
                                                'department'    => 'bdo',
                                                'app_menu_id'   => $bdo_app_menu,
                                                'user_type'     => 'users',
                                                'status'        => '1'
                                                )
                                            );
                }
            }
            if(count($ddappmenu)>0) { 
                $del_permission = DB::table('app_menu_permissions')
                                  ->where('department', 'dhanuka_doctor')
                                  ->where('user_type', 'dhanuka_doctor')
                                  ->delete();
                $del_permission1 = DB::table('app_menu_permissions')
                                  //->orWhereNull('user_id')
                                  ->where('user_type', 'dhanuka_doctor')
                                  ->delete();
                foreach($ddappmenu AS $dd_app_menu) {
                    $insert_permission  =   AppMenuPermission::firstOrCreate(
                                                array(
                                                'department'    => 'dhanuka_doctor',
                                                'app_menu_id'   => $dd_app_menu,
                                                'user_type'     => 'dhanuka_doctor',
                                                'status'        => '1'
                                                )
                                            );
                }
            }
            if(count($generalappmenu)>0) { 
                $del_permission = DB::table('app_menu_permissions')
                                  ->where('department', 'general')
                                  ->where('user_type', 'general')
                                  ->delete();
                $del_permission1 = DB::table('app_menu_permissions')
                                  //->orWhereNull('user_id')
                                  ->where('user_type', 'general')
                                  ->delete();
                foreach($ddappmenu AS $dd_app_menu) {
                    $insert_permission  =   AppMenuPermission::firstOrCreate(
                                                array(
                                                'department'    => 'general',
                                                'app_menu_id'   => $generalappmenu,
                                                'user_type'     => 'general',
                                                'status'        => '1'
                                                )
                                            );
                }
            }
            Session::flash('message', "Permissions Successfully Created.");
            return redirect('permission/add-department-wise');
        }
    }
    
    
    public function getAddZonalWise() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        
        $zonal_list = ZonalManager::where('status', '1')->get();
        $menu_list  = AppMenu::where('status', '1')->orderBy('menu', 'ASC')->get();
        return View::make('backend.permission.addzonalpermission', array('menu_list'=>$menu_list, 'zonal_list'=>$zonal_list));
    }
    public function getAddRegionalWise() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        
        $zonal_list = ZonalManager::where('status', '1')->get();
        $menu_list  = AppMenu::where('status', '1')->orderBy('menu', 'ASC')->get();
        return View::make('backend.permission.addregionalpermission', array('menu_list'=>$menu_list, 'zonal_list'=>$zonal_list));
    }
    public function getAddSalesRegionalWise() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        
        $zonal_list = ZonalManager::where('status', '1')->get();
        $menu_list  = AppMenu::where('status', '1')->orderBy('menu', 'ASC')->get();
        return View::make('backend.permission.addsalesregionalpermission', array('menu_list'=>$menu_list, 'zonal_list'=>$zonal_list));
    }
    public function getAddAreaWise() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        
        $zonal_list = ZonalManager::where('status', '1')->get();
        $menu_list  = AppMenu::where('status', '1')->orderBy('menu', 'ASC')->get();
        return View::make('backend.permission.addareapermission', array('menu_list'=>$menu_list, 'zonal_list'=>$zonal_list));
    }
    public function getAddSalesAreaWise() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        
        $zonal_list = ZonalManager::where('status', '1')->get();
        $menu_list  = AppMenu::where('status', '1')->orderBy('menu', 'ASC')->get();
        return View::make('backend.permission.addsalesareapermission', array('menu_list'=>$menu_list, 'zonal_list'=>$zonal_list));
    }
    public function getAddBdoWise() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        
        $zonal_list = ZonalManager::where('status', '1')->get();
        $menu_list  = AppMenu::where('status', '1')->orderBy('menu', 'ASC')->get();
        return View::make('backend.permission.addbdopermission', array('menu_list'=>$menu_list, 'zonal_list'=>$zonal_list));
    }

    public function getAddSupervisorWise() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        
        $supervisor_list = MarketingSupervisor::where('status', '1')->get();
        $menu_list  = AppMenu::where('status', '1')->orderBy('menu', 'ASC')->get();
        return View::make('backend.permission.addsupervisorpermission', array('menu_list'=>$menu_list, 'supervisor_list'=>$supervisor_list));
    }

    
    public function postTeamPermission(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();        
            //dd($my_data);            
            $user_id    = (isset($my_data['user_dept_id'])?$my_data['user_dept_id']:NULL);
            $user_type  = (isset($my_data['_dept'])?$my_data['_dept']:'XXX');
            //dd($my_data);
            $get_user_permission    = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('user_type', $user_type)->where('user_id', $user_id)->where('status', '1')->get()->toArray();
            //dd($get_user_permission);
            if($get_user_permission) {
                if($get_user_permission[0]['app_menu_id']!='') {
                    $user_permission    = explode(',', $get_user_permission[0]['app_menu_id']);
                    echo json_encode($user_permission, true);
                    exit;
                }
                else {
                    if($user_id!='') {
                        $get_deptartment_permission = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('department', $user_type)->where('status', '1')->get()->toArray();
                        $department_permission      = explode(',', $get_deptartment_permission[0]['app_menu_id']);
                        echo json_encode($department_permission, true);
                        exit;
                    }
                }
            }
            else {
                if($user_id!='') {
                    $get_deptartment_permission = AppMenuPermission::select(\DB::raw('GROUP_CONCAT(app_menu_id) AS app_menu_id'))->where('department', $user_type)->where('status', '1')->get()->toArray();
                    $department_permission      = explode(',', $get_deptartment_permission[0]['app_menu_id']);
                    echo json_encode($department_permission, true);
                    exit;
                }
            }
        }
    }
    public function postCreatePermission(Request $request) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();
            $rules      = array(
                                'user_dept_id'  =>  'required',
                                'appmenu'       =>  'array',
                                'user_type'     =>  'required'
                            );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $appmenu    = (isset($my_data['appmenu'])?$my_data['appmenu']:array());
            $user_id    = (isset($my_data['user_dept_id'])?$my_data['user_dept_id']:NULL);
            $user_type  = (isset($my_data['user_type'])?$my_data['user_type']:NULL);
            
            if(count($appmenu)>0) {                
                $del_permission = DB::table('app_menu_permissions')->where('user_type', $user_type)->where('user_id', $user_id)->delete();
                foreach($appmenu AS $app_menu_id) {
                    $insert_permission  =   AppMenuPermission::firstOrCreate(
                                                array(
                                                    'app_menu_id'   => $app_menu_id,
                                                    'user_type'     => $user_type,
                                                    'user_id'       => $user_id,
                                                    'status'        => '1'
                                                )
                                            );
                }
                Session::flash('message', "Permissions Successfully Created.");
                //return redirect('permission/add-zonal-wise');
                return Redirect::back();
            }
            else {
                Session::flash('message', "Please select a menu");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }


    public function postCreateBranchPermission(Request $request)
    {
        $input = Request::all();
         //echo "<pre>"; print_r($input); die;
        if(isset($input['adminmenu']) && $input['adminmenu'] != '')
        {
          $menus = implode(',', $input['adminmenu']);
        }
        else
        {
          $menus = '';  
        }
        $exist = DB::table('role_permission')->where('role_id','8')->count();
        if($exist > 0)
        {
            $data['page_id'] = $menus;
            DB::table('role_permission')->where('role_id','8')->update(['page_id'=>'']);
            DB::table('role_permission')->where('role_id','8')->update($data);
            Session::flash('message', "Permissions Successfully Created.");
                return Redirect::back();
        }
        else
        {
            $data['role_id'] = 8;
            $data['page_id'] = $menus;
            DB::table('role_permission')->insert($data);
            Session::flash('message', "Permissions Successfully Created.");
                return Redirect::back();
        }
    }


     public function getAddBranchWise() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        
        $menu_list  = DB::table('admin_menus')->where('status','1')->orderBy('menu', 'ASC')->get();
        $checked  = DB::table('role_permission')->where('role_id', '8')->get();
        $checkedMenu = explode(',', $checked[0]->page_id);
        // echo "<pre>"; print_r($checkedMenu); die;
        return View::make('backend.permission.addbranchadmin_permission', array('menu_list'=>$menu_list,'checked' => $checkedMenu));
    }

    public function getAddDdWise() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        
        $zonal_list = ZonalManager::where('status', '1')->get();
        $menu_list  = AppMenu::where('status', '1')->orderBy('menu', 'ASC')->get();
        return View::make('backend.permission.addddpermission', array('menu_list'=>$menu_list, 'zonal_list'=>$zonal_list));
    }
    
}