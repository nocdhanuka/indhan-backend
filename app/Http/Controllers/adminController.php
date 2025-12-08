<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Http\Request as RequestsData;
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
use App\markets;
use App\territories;
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
use App\DdProductCategory;

use App\Efficiency;
use App\AssignedVillage;
use App\Reminder;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use Khill\Lavacharts\Lavacharts;


use App\UserProductCategoryOrn;
use App\DdProductCategoryOrn;
use App\AssignedVillageOrn;
use App\DhanukaDoctorOrn;
use App\Userorn;
use App\UserDeputationOrn;
use App\AreaManagerorn;
use App\AreaManagerStateOrn;
use App\AreaManagerProductCategoryOrn;
use App\RegionalManagerOrn;
use App\RegionalManagerStateOrn;
use App\RegionalManagerProductCategoryOrn;
use App\SalesAreaManagerOrn;
use App\SalesAreaManagerStateOrn;
use App\SalesAreaManagerProductCategoryOrn;
use App\SalesRegionalManagerOrn;
use App\SalesRegionalManagerStateOrn;
use App\SalesRegionalManagerProductCategoryOrn;
use App\IFCVillageFarmer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

ini_set('max_execution_time', 0);
// ini_set("memory_limit", "-1");
// set_time_limit(0);

class adminController extends Controller {

        public function __construct() {
            //$this->authMiddleWare();        
            //echo "<pre>"; print_r(session()->all()); exit;
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect(env('APP_URL'));
            }
        }
        
        private function categoryWiseBdoListForQuery($key='1', $value='1') {
            $bdo_query  = " SELECT 
                                GROUP_CONCAT(CONCAT('''', users.id, '''' )) AS user_id
                            FROM    users
                            WHERE   users.status='1' AND users.".$key."='".$value."' LIMIT 0,1 ";
            DB::statement('SET SESSION group_concat_max_len = 1000000');
            $bdo_detail = DB::select(DB::raw($bdo_query));
            $bdo_list   = array_map(function ($value) {
                                    return (array)$value;
                                }, $bdo_detail);
            return $bdo_list;
        }
        private function categoryWiseBdoListForQuery1($key='1', $value='1',$state) {
            $bdo_query  = " SELECT 
                                GROUP_CONCAT(CONCAT('''', users.id, '''' )) AS user_id
                            FROM    users
                            WHERE   users.status='1' AND users.".$key."='".$value."' AND users.state_id = ".$state." LIMIT 0,1 ";
            DB::statement('SET SESSION group_concat_max_len = 1000000');
            $bdo_detail = DB::select(DB::raw($bdo_query));
            $bdo_list   = array_map(function ($value) {
                                    return (array)$value;
                                }, $bdo_detail);
            return $bdo_list;
        }
        
        public function postAdminLogin() { 
            //$sess   = \Session::get('adminid');                
            //if (!isset($sess) || $sess == "") {            return redirect('/');        }
            $value      = Input::all();
            //   dd($value);
            $username   = $value['username'];
            $pass       = $value['password'];
            $password   = base64_encode($pass);
            
            $login      = \ DB::table('admin')
                            ->where('username', $username)
                            ->where('password', $password)
                            ->get();
            if(count($login) > 0)
            {   
                if (isset($value['remember'])) {
                    $remember = $value['remember'];
                    if ($remember == '1') 
                    {
                        $hour = time() + 3600 * 24 * 30;
                        \Cookie::queue('name', $username, $hour);
                        \Cookie::queue('pass', $pass, $hour);
                    }
                }
                else
                {
                    if (\Cookie::get('name') !== null) {
                        \Cookie::queue('name', '');
                    }
                    if (\Cookie::get('pass') !== null) {
                        \Cookie::queue('pass', '');
                    }
                }
                
                foreach ($login as $sess) {
                    $id     = $sess->id;
                    $role   = $sess->role_id;
                    \Session::put(['adminid' => $id, 'login_role_id' => $role]);
                }
               
                $roleid_log = \Session::get('login_role_id');
                $query      = \DB::table('role_permission')
                                ->join('role', 'role.r_id', '=', 'role_permission.role_id')
                                ->where('role_id', $roleid_log)
                                ->get();
                foreach ($query as $value) {
                    $page_id = $value->page_id;
                    \Session::put(['permission' => $page_id]);
                }

                $user       = \Session::get('permission');

                if ($login || $login1) 
                {
                    echo $user;
                } else {
                     echo 0;
                }
            }

            $branch      = \DB::table('branch_admin')
                            ->where('user_name', $username)
                            ->where('password', $password)
                            ->where('status','1')
                            ->get();
                            
            if(count($branch) > 0)
            {
                foreach ($branch as $sess) {
                    $id     = $sess->id;
                    $role   = $sess->role_id;
                    $zonal  = $sess->zonal_manager_id;
                    \Session::put(['adminid' => $id, 'login_role_id' => $role, 'zonal_manager_id' => $zonal]);
                }
                $roleid_log = \Session::get('login_role_id');

                $query      = \DB::table('role_permission')
                                ->join('role', 'role.r_id', '=', 'role_permission.role_id')
                                ->where('role_id', $roleid_log)
                                ->get();
                foreach ($query as $value) {
                    $page_id = $value->page_id;
                    \Session::put(['permission' => $page_id]);
                }

                $user  = \Session::get('permission');
                //print_r($user);die;
                if ($user) {
                    echo $user;
                } else {
                    echo 0;
                }
            }
        }
        
        
        private function regionalSales($zonal_manager) {
            $sess       =   \Session::get('adminid');
            $roleid_log = \Session::get('login_role_id');
            if ($roleid_log == 8) {
                $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
                if ($userData) {   
                    $regional_list              = RegionalManager::select('regional_manager_id', 'regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
                    $sales_regional_list        = SalesRegionalManager::select('sales_regional_manager_id', 'sales_regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('sales_regional_manager_name', 'ASC')->get()->toArray();
                    $regional_sales             = array();
                    $regional_sales['regional'] = $regional_list;
                    $regional_sales['sales']    = $sales_regional_list;
                    return $regional_sales;
                }
            }
            else
            {
                $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
                if ($userData) {   
                    $regional_list              = RegionalManager::select('regional_manager_id', 'regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('regional_manager_name', 'ASC')->get()->toArray();
                    $sales_regional_list        = SalesRegionalManager::select('sales_regional_manager_id', 'sales_regional_manager_name')->where('zonal_manager_id', $zonal_manager)->where('status', '1')->orderBy('sales_regional_manager_name', 'ASC')->get()->toArray();
                    $regional_sales             = array();
                    $regional_sales['regional'] = $regional_list;
                    $regional_sales['sales']    = $sales_regional_list;
                    return $regional_sales;
                }
            }
            
        }
        private function areaSales($regional_manager) {
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) { 
                $area_list          = AreaManager::select('area_manager_id', 'area_manager_name')->where('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('area_manager_name', 'ASC')->get()->toArray();
                $sales_area_list    = SalesAreaManager::select('sales_area_manager_id', 'sales_area_manager_name')->where('regional_manager_id', $regional_manager)->where('status', '1')->orderBy('sales_area_manager_name', 'ASC')->get()->toArray();
                $area_sales         = array();
                $area_sales['area'] = $area_list;
                $area_sales['sales']= $sales_area_list;
                return $area_sales;
            }
        }
        private function bdo($area_manager) {
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {  
                $bdo_list  = User::select('id', 'name', 'ECN')->where('area_manager_id', $area_manager)->where('status', '1')->orderBy('name', 'ASC')->get()->toArray();
                return $bdo_list;            
            }
        } 
        public function anyDashboard() {
            //dd(date('Y-m-d'));
            $sess = \Session::get('adminid');
            //   dd($sess);
            if (!isset($sess) || $sess == "") {
            return redirect('/');
            }
            else {
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            //  dd($userData);
            if($userData) {
            $my_data                    = Input::all();
            //  dd($my_data);
            $zonal_manager_id           = (isset($my_data['zonal_manager'])?$my_data['zonal_manager']:'');
            $regional_manager_id        = (isset($my_data['regional_manager'])?$my_data['regional_manager']:'');
            $sales_regional_manager_id  = (isset($my_data['sales_regional_manager'])?$my_data['sales_regional_manager']:'');
            $area_manager_id            = (isset($my_data['area_manager'])?$my_data['area_manager']:'');
            $sales_area_manager_id      = (isset($my_data['sales_area_manager'])?$my_data['sales_area_manager']:'');
            $bdo_id                     = (isset($my_data['bdo'])?$my_data['bdo']:'');
            $start_month                = (isset($my_data['start']) && $my_data['start']!=''?date('m', strtotime($my_data['start'])):'');
            $end_month                  = (isset($my_data['end']) && $my_data['end']!=''?date('m', strtotime($my_data['end'])):'');
            $start_year                 = (isset($my_data['start']) && $my_data['start']!=''?date('Y', strtotime($my_data['start'])):'');
            $end_year                   = (isset($my_data['end']) && $my_data['end']!=''?date('Y', strtotime($my_data['end'])):'');
                        
            $where = "(1=1) ";
            if($bdo_id!='') {
            $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $bdo_id);
            $regional_count         = '';
            $area_count             = '';
            $sales_regional_count   = '';
            $sales_area_count       = '';
            $where .= " AND user_id = '".$bdo_id."' AND user_type='bdo'";
            }
            else if($sales_area_manager_id!='') {
            $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $sales_area_manager_id);
            $regional_count         = '';
            $area_count             = '';
            $sales_regional_count   = '';
            $sales_area_count       = '';
            $where .= " AND user_id = '".$sales_area_manager_id."' AND user_type='sales_area'";
            }
            else if($area_manager_id!='') {
            $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $area_manager_id);
            $regional_count         = '';
            $area_count             = '';
            $sales_regional_count   = '';
            $sales_area_count       = '';
            $where .= " AND user_id = '".$area_manager_id."' AND user_type='area'";
            }
            else if($sales_regional_manager_id!='') {
            $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $sales_regional_manager_id);
            $regional_count         = '';
            $area_count             = AreaManager::where('sales_regional_manager_id', $sales_regional_manager_id)->where('status','1')->count();
            $sales_regional_count   = '';
            $sales_area_count       = SalesAreaManager::where('sales_regional_manager_id', $sales_regional_manager_id)->where('status','1')->count();
            $where .= " AND user_id = '".$sales_regional_manager_id."' AND user_type='sales_regional'";
            }
            else if($regional_manager_id!='') {
            $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $regional_manager_id);
            $regional_count         = '';
            $area_count             = AreaManager::where('regional_manager_id', $regional_manager_id)->where('status','1')->count();
            $sales_regional_count   = '';
            $sales_area_count       = SalesAreaManager::where('regional_manager_id', $regional_manager_id)->where('status','1')->count();
            $where .= " AND user_id = '".$regional_manager_id."' AND user_type='regional'";
            }
            else if($zonal_manager_id!='') {
            $bdo_list_query         = $this->categoryWiseBdoListForQuery('zonal_manager_id', $zonal_manager_id);
            $regional_count         = RegionalManager::where('zonal_manager_id', $zonal_manager_id)->where('status','1')->count();
            $area_count             = AreaManager::where('zonal_manager_id', $zonal_manager_id)->where('status','1')->count();
            $sales_regional_count   = SalesRegionalManager::where('zonal_manager_id', $zonal_manager_id)->where('status','1')->count();
            $sales_area_count       = SalesAreaManager::where('zonal_manager_id', $zonal_manager_id)->where('status','1')->count();
            $where .= " AND user_id = '".$zonal_manager_id."' AND user_type='zonal'";
            }
            else {
            $bdo_list_query         = array(array('user_id'=>''));
            $regional_count         = '';
            $area_count             = '';
            $sales_regional_count   = '';
            $sales_area_count       = '';
            }

            // ZONAL MANAGER LIST
            $zonal_list = ZonalManager::where('status', '1')->get();
            // print_r($zonal_list);die;
            $whereMonth   = "(1=1) ";


            if ($bdo_list_query && $bdo_list_query[0]['user_id']!='') {

            $bdo_id_list            = $bdo_list_query[0]['user_id']; 

            $bdo_count              = count(explode(',', $bdo_list_query[0]['user_id']));
            if($start_month!='' || $end_month!='' || $start_year!='' || $end_year!='') {
            $whereMonth .= " AND (MONTH(created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(created_at) between '".$start_year."' and '".$end_year."') ";
            }
            // EFFICIENCY RESULT
            $total_no_of_activities         = Ticket::whereRaw($whereMonth)->whereRaw("user_id IN (".$bdo_id_list.")")->where('user_type', 'bdo')->count();
            $total_farmer                   = Farmer::whereRaw($whereMonth)->whereRaw("user_id IN (".$bdo_id_list.")")->where('ticket_status', '1')->count();


            $total_validate_farmer          = Farmer::whereRaw($whereMonth)->whereRaw("user_id IN (".$bdo_id_list.")")->where('ticket_status', '1')->where('validation_status', 'Validated')->count();
            //dd($total_validate_farmer);
            $average_farmer                 = (($total_no_of_activities!='' || $total_no_of_activities!='0')?number_format((float)($total_farmer/$total_no_of_activities), 2, '.', ''):'0');
            $average_validated_farmer       = (($total_no_of_activities!='' || $total_no_of_activities!='0')?number_format((float)($total_validate_farmer/$total_no_of_activities), 2, '.', ''):'0');
            $testimonial                    = Testimonial::whereRaw($whereMonth)->whereRaw("user_id IN (".$bdo_id_list.")")->count();
            $customer_care_closed           = CustomerQuery::whereRaw($whereMonth)->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '!=', 'Sale')->where('status', '3')->count();
            $customer_care_follow_up        = CustomerQuery::whereRaw($whereMonth)->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '!=', 'Sale')->where('status', '2')->count();
            $customer_care_open             = CustomerQuery::whereRaw($whereMonth)->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '!=', 'Sale')->where('status', '1')->count();
            $customer_care_sale_closed      = CustomerQuery::whereRaw($whereMonth)->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '=', 'Sale')->where('status', '3')->count();
            $customer_care_sale_follow_up   = CustomerQuery::whereRaw($whereMonth)->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '=', 'Sale')->where('status', '2')->count();
            $customer_care_sale_open        = CustomerQuery::whereRaw($whereMonth)->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '=', 'Sale')->where('status', '1')->count();                
            $efficiency_query               = " SELECT  *
                                            FROM efficiency_results
                                            WHERE ".$where."
                                                AND month=MONTH(CURRENT_DATE())
                                                AND year=YEAR(CURRENT_DATE()) LIMIT 0,1 ";
            $user_efficiency_data           = DB::select($efficiency_query);                
            $efficiency_result              = array_map(function ($value) {
                                                return (array)$value;
                                            }, $user_efficiency_data);

            // ACTIVITIY / REGISTERED / VALIDATED GRAPH RESULT
            $user_activity_query        = " SELECT  IFNULL(DATE_FORMAT(created_at,'%Y-%m-%d'), 0) AS y_m_d, 
                                                IFNULL(COUNT(ticket_id), 0) AS activities, 
                                                IFNULL(SUM(farmer_count), 0) AS registered_farmers, 
                                                IFNULL(SUM(validated_farmers), 0) AS validated_farmers
                                        FROM tickets
                                        WHERE ".$whereMonth." AND (user_id IN (".$bdo_id_list.")) AND (user_type ='bdo')
                                        GROUP BY YEAR(created_at), MONTH(created_at)
                                        ORDER BY created_at ";                   
            $self_participation_query   = " SELECT  
                                            IFNULL(DATE_FORMAT(created_at,'%Y-%m-%d'), 0) AS y_m_d, 
                                            IFNULL(COUNT(self_participation_id), 0) AS self_participations
                                        FROM self_participations SP
                                        WHERE ".$where."
                                        GROUP BY YEAR(created_at), MONTH(created_at)
                                        ORDER BY created_at ";
            $user_activity_data         =   DB::select($user_activity_query);
            $user_activity_result       =   array_map(function ($value) {
                                            return (array)$value;
                                        }, $user_activity_data);
            $self_participation_data    =   DB::select($self_participation_query);
            $self_participation_result  =   array_map(function ($value) {
                                            return (array)$value;
                                        }, $self_participation_data);
            }
            else {

            // ACTIVITIY / REGISTERED / VALIDATED GRAPH RESULT
            if($start_month!='' || $end_month!='' || $start_year!='' || $end_year!='') {
            $whereMonth .= " AND (MONTH(created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(created_at) between '".$start_year."' and '".$end_year."')";
            }
            $total_no_of_activities         = Ticket::whereRaw($whereMonth)->where('user_type', 'bdo')->count();


            $total_farmer                   = Farmer::whereRaw($whereMonth)->where('ticket_status', '1')->count();

            // print_r($total_farmer);die;

            $total_validate_farmer          = Farmer::whereRaw($whereMonth)->where('ticket_status', '1')->where('validation_status', 'Validated')->count();
            $average_farmer                 = (($total_no_of_activities!='' || $total_no_of_activities!='0')?number_format((float)($total_farmer/$total_no_of_activities), 2, '.', ''):'0');
            $average_validated_farmer       = (($total_no_of_activities!='' || $total_no_of_activities!='0')?number_format((float)($total_validate_farmer/$total_no_of_activities), 2, '.', ''):'0');
            $testimonial                    = Testimonial::whereRaw($whereMonth)->count();
            $customer_care_closed           = CustomerQuery::whereRaw($whereMonth)->where('type_of_call', '!=', 'Sale')->where('status', '3')->count();
            $customer_care_follow_up        = CustomerQuery::whereRaw($whereMonth)->where('type_of_call', '!=', 'Sale')->where('status', '2')->count();
            $customer_care_open             = CustomerQuery::whereRaw($whereMonth)->where('type_of_call', '!=', 'Sale')->where('status', '1')->count();
            $customer_care_sale_closed      = CustomerQuery::whereRaw($whereMonth)->where('type_of_call', '=', 'Sale')->where('status', '3')->count();
            $customer_care_sale_follow_up   = CustomerQuery::whereRaw($whereMonth)->where('type_of_call', '=', 'Sale')->where('status', '2')->count();
            $customer_care_sale_open        = CustomerQuery::whereRaw($whereMonth)->where('type_of_call', '=', 'Sale')->where('status', '1')->count();                
            $efficiency_query               = " SELECT  *
                                            FROM efficiency_results
                                            WHERE 1=2 LIMIT 0,1 ";

            $user_efficiency_data           = DB::select($efficiency_query);                
            $efficiency_result              = array_map(function ($value) {
                                                return (array)$value;
                                            }, $user_efficiency_data);
            //echo "<pre>"; print_r($efficiency_result); die;
                                            
            $user_activity_query        = " SELECT  IFNULL(DATE_FORMAT(created_at,'%Y-%m-%d'), 0) AS y_m_d, 
                                                IFNULL(COUNT(ticket_id), 0) AS activities, 
                                                IFNULL(SUM(farmer_count), 0) AS registered_farmers, 
                                                IFNULL(SUM(validated_farmers), 0) AS validated_farmers
                                        FROM tickets
                                        WHERE user_type ='bdo' AND ".$whereMonth."
                                        GROUP BY YEAR(created_at), MONTH(created_at)
                                        ORDER BY created_at ";




            $self_participation_query   = " SELECT  
                                            IFNULL(DATE_FORMAT(created_at,'%Y-%m-%d'), 0) AS y_m_d, 
                                            IFNULL(COUNT(self_participation_id), 0) AS self_participations
                                        FROM self_participations SP
                                        WHERE ".$whereMonth."
                                        GROUP BY YEAR(created_at), MONTH(created_at)
                                        ORDER BY created_at ";


            $user_activity_data         =   DB::select($user_activity_query);

            $user_activity_result       =   array_map(function ($value) {
                                            return (array)$value;
                                        }, $user_activity_data);

            $self_participation_data    =   DB::select($self_participation_query);
            $self_participation_result  =   array_map(function ($value) {
                                            return (array)$value;
                                        }, $self_participation_data);
            }

            $regional_sales = $this->regionalSales($zonal_manager_id);
            $area_sales     = $this->areaSales($regional_manager_id);
            
            $bdo            = $this->bdo($area_manager_id);
            $month          = date('m');


            return View::make('backend.dashboard', array(
                'zonal_list'                    => $zonal_list,
                'activities'                    => $user_activity_result,
                'self_participations'           => $self_participation_result,                  
                'efficiency'                    => $efficiency_result,
                'total_no_of_activities'        => $total_no_of_activities,
                'total_farmer'                  => $total_farmer,
                'total_validate_farmer'         => $total_validate_farmer,
                'average_farmer'                => $average_farmer,
                'average_validated_farmer'      => $average_validated_farmer,                                    
                'testimonial'                   => $testimonial,                                    
                'customer_care_closed'          => $customer_care_closed,
                'customer_care_follow_up'       => $customer_care_follow_up,
                'customer_care_open'            => $customer_care_open,
                'customer_care_sale_closed'     => $customer_care_sale_closed,
                'customer_care_sale_follow_up'  => $customer_care_sale_follow_up,
                'customer_care_sale_open'       => $customer_care_sale_open,
                
                'my_data'               => $my_data,
                'regional_sales'        => $regional_sales,
                'area_sales'            => $area_sales,
                'bdo'                   => $bdo,
                'month'                 => $month
            ));                
            }
            else
            {
            $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
            // print_r($user_data);die;
            if($userData) {
            $branchState = $userData->state;
            $my_data                    = Input::all();
            //dd($my_data);
            $zonal_manager_id           = (isset($my_data['zonal_manager'])?$my_data['zonal_manager']:'');
            $regional_manager_id        = (isset($my_data['regional_manager'])?$my_data['regional_manager']:'');
            $sales_regional_manager_id  = (isset($my_data['sales_regional_manager'])?$my_data['sales_regional_manager']:'');
            $area_manager_id            = (isset($my_data['area_manager'])?$my_data['area_manager']:'');
            $sales_area_manager_id      = (isset($my_data['sales_area_manager'])?$my_data['sales_area_manager']:'');
            $bdo_id                     = (isset($my_data['bdo'])?$my_data['bdo']:'');
            $start_month                = (isset($my_data['start']) && $my_data['start']!=''?date('m', strtotime($my_data['start'])):'');
            $end_month                  = (isset($my_data['end']) && $my_data['end']!=''?date('m', strtotime($my_data['end'])):'');
            $start_year                 = (isset($my_data['start']) && $my_data['start']!=''?date('Y', strtotime($my_data['start'])):'');
            $end_year                   = (isset($my_data['end']) && $my_data['end']!=''?date('Y', strtotime($my_data['end'])):'');
                        
            $where = "(1=1) ";
            if($bdo_id!='') {
            $bdo_list_query = $this->categoryWiseBdoListForQuery1('id', $bdo_id,$branchState);
            $regional_count         = '';
            $area_count             = '';
            $sales_regional_count   = '';
            $sales_area_count       = '';
            $where .= " AND user_id = '".$bdo_id."' AND user_type='bdo'";
            }
            else if($sales_area_manager_id!='') {
            $bdo_list_query = $this->categoryWiseBdoListForQuery1('sales_area_manager_id', $sales_area_manager_id,$branchState);
            $regional_count         = '';
            $area_count             = '';
            $sales_regional_count   = '';
            $sales_area_count       = '';
            $where .= " AND user_id = '".$sales_area_manager_id."' AND user_type='sales_area'";
            }
            else if($area_manager_id!='') {
            $bdo_list_query = $this->categoryWiseBdoListForQuery1('area_manager_id', $area_manager_id,$branchState);
            $regional_count         = '';
            $area_count             = '';
            $sales_regional_count   = '';
            $sales_area_count       = '';
            $where .= " AND user_id = '".$area_manager_id."' AND user_type='area'";
            }
            else if($sales_regional_manager_id!='') {
            $bdo_list_query = $this->categoryWiseBdoListForQuery1('sales_regional_manager_id', $sales_regional_manager_id,$branchState);
            $regional_count         = '';
            $area_count             = AreaManager::join('area_manager_states','area_managers.area_manager_id','=','area_manager_states.area_manager_id')->where('area_managers.sales_regional_manager_id', $sales_regional_manager_id)->where('area_managers.status','1')->where('area_manager_states.state_id',$branchState)->count();
            $sales_regional_count   = '';
             $sales_area_count       = SalesAreaManager::join('sales_area_manager_states','sales_area_managers.sales_area_manager_id','=','sales_area_manager_states.sales_area_manager_id')->where('sales_area_managers.sales_regional_manager_id', $sales_regional_manager_id)->where('sales_area_managers.status','1')->where('sales_area_manager_states.state_id',$branchState)->count();
            $where .= " AND user_id = '".$sales_regional_manager_id."' AND user_type='sales_regional'";
            }
            else if($regional_manager_id!='') {
            $bdo_list_query = $this->categoryWiseBdoListForQuery1('regional_manager_id', $regional_manager_id,$branchState);
            $regional_count         = '';
             $area_count             = AreaManager::join('area_manager_states','area_managers.area_manager_id','=','area_manager_states.area_manager_id')->where('area_managers.regional_manager_id', $regional_manager_id)->where('area_managers.status','1')->where('area_manager_states.state_id',$branchState)->count();
            $sales_regional_count   = '';
             $sales_area_count       = SalesAreaManager::join('sales_area_manager_states','sales_area_managers.sales_area_manager_id','=','sales_area_manager_states.sales_area_manager_id')->where('sales_area_managers.regional_manager_id', $regional_manager_id)->where('sales_area_managers.status','1')->where('sales_area_manager_states.state_id',$branchState)->count();
           
            $where .= " AND user_id = '".$regional_manager_id."' AND user_type='regional'";
            }
            else if($zonal_manager_id!='') {
            $bdo_list_query         = $this->categoryWiseBdoListForQuery1('zonal_manager_id', $zonal_manager_id,$branchState);
            $regional_count         = RegionalManager::join('regional_manager_states','regional_managers.regional_manager_id','=','regional_manager_states.regional_manager_id')->where('regional_managers.zonal_manager_id', $zonal_manager_id)->where('regional_managers.status','1')->where('regional_manager_states.state_id',$branchState)->count();
            $area_count             = AreaManager::join('area_manager_states','area_managers.area_manager_id','=','area_manager_states.area_manager_id')->where('area_managers.zonal_manager_id', $zonal_manager_id)->where('area_managers.status','1')->where('area_manager_states.state_id',$branchState)->count();
            $sales_regional_count   = SalesRegionalManager::join('sales_regional_manager_states','sales_regional_managers.sales_regional_manager_id','=','sales_regional_manager_states.sales_regional_manager_id')->where('sales_regional_managers.zonal_manager_id', $zonal_manager_id)->where('sales_regional_managers.status','1')->where('sales_regional_manager_states.state_id',$branchState)->count();
            $sales_area_count       = SalesAreaManager::join('sales_area_manager_states','sales_area_managers.sales_area_manager_id','=','sales_area_manager_states.sales_area_manager_id')->where('sales_area_managers.zonal_manager_id', $zonal_manager_id)->where('sales_area_managers.status','1')->where('sales_area_manager_states.state_id',$branchState)->count();
            $where .= " AND user_id = '".$zonal_manager_id."' AND user_type='zonal'";
            }
            else {
            $bdo_list_query         = array(array('user_id'=>''));
            $regional_count         = '';
            $area_count             = '';
            $sales_regional_count   = '';
            $sales_area_count       = '';
            }

            // ZONAL MANAGER LIST
			$State =json_decode($userData->state);
            //print_r($State);die;
            $zonal_list = ZonalManager::join('zonal_manager_states','zonal_managers.zonal_manager_id','=','zonal_manager_states.zonal_manager_id')->wherein('zonal_manager_states.state_id',$State)->where('zonal_managers.status', '1')->get();
			//print_r($zonal_list);die
            $whereMonth   = "(1=1) ";
            $whereMonth1   = "(1=1) ";


            if ($bdo_list_query && $bdo_list_query[0]['user_id']!='') {
                $bdo_id_list            = $bdo_list_query[0]['user_id']; 
                $bdo_count              = count(explode(',', $bdo_list_query[0]['user_id']));
                
                if($start_month!='' || $end_month!='' || $start_year!='' || $end_year!='') {
                    $whereMonth .= " AND (MONTH(created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(created_at) between '".$start_year."' and '".$end_year."') ";
                }
                if($start_month!='' || $end_month!='' || $start_year!='' || $end_year!='') {
                    $whereMonth1 .= " AND (MONTH(testimonials.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(testimonials.created_at) between '".$start_year."' and '".$end_year."')";
                }
                // EFFICIENCY RESULT
                $total_no_of_activities         = Ticket::whereRaw($whereMonth)->whereRaw("user_id IN (".$bdo_id_list.")")->where('state_id',$branchState)->where('user_type', 'bdo')->count();
                $total_farmer                   = Farmer::join('tickets','tickets.ticket_id','=','farmers.ticket_id')->whereRaw("(MONTH(farmers.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(farmers.created_at) between '".$start_year."' and '".$end_year."')")->whereRaw("farmers.user_id IN (".$bdo_id_list.")")->where('tickets.state_id',$branchState)->where('farmers.ticket_status', '1')->count();

                $total_validate_farmer          = Farmer::join('tickets','tickets.ticket_id','=','farmers.ticket_id')->whereRaw("(MONTH(farmers.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(farmers.created_at) between '".$start_year."' and '".$end_year."')")->whereRaw("farmers.user_id IN (".$bdo_id_list.")")->where('tickets.state_id',$branchState)->where('farmers.ticket_status', '1')->where('validation_status', 'Validated')->count();

                //dd($total_validate_farmer);
                $average_farmer                 = (($total_no_of_activities!='' || $total_no_of_activities!='0')?number_format((float)($total_farmer/$total_no_of_activities), 2, '.', ''):'0');
                $average_validated_farmer       = (($total_no_of_activities!='' || $total_no_of_activities!='0')?number_format((float)($total_validate_farmer/$total_no_of_activities), 2, '.', ''):'0');

                // $testimonial                    = Testimonial::whereRaw($whereMonth)->whereRaw("user_id IN (".$bdo_id_list.")")->count();

                $test1 =  Testimonial::join('zonal_manager_states','testimonials.user_id','=','zonal_manager_states.zonal_manager_id')->where('zonal_manager_states.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','zonal')->whereRaw("testimonials.user_id IN (".$bdo_id_list.")")->count();

                $test2 =  Testimonial::join('regional_manager_states','testimonials.user_id','=','regional_manager_states.regional_manager_id')->where('regional_manager_states.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','regional')->whereRaw("testimonials.user_id IN (".$bdo_id_list.")")->count();

                $test3 =  Testimonial::join('area_manager_states','testimonials.user_id','=','area_manager_states.area_manager_id')->where('area_manager_states.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','area')->whereRaw("testimonials.user_id IN (".$bdo_id_list.")")->count();

                $test4 =  Testimonial::join('sales_area_manager_states','testimonials.user_id','=','sales_area_manager_states.sales_area_manager_id')->where('sales_area_manager_states.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','sales_area')->whereRaw("testimonials.user_id IN (".$bdo_id_list.")")->count();

                $test5 =  Testimonial::join('users','testimonials.user_id','=','users.id')->where('users.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','bdo')->whereRaw("testimonials.user_id IN (".$bdo_id_list.")")->count();

                $test6 =  Testimonial::join('sales_regional_manager_states','testimonials.user_id','=','sales_regional_manager_states.sales_regional_manager_id')->where('sales_regional_manager_states.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','sales_regional')->whereRaw("testimonials.user_id IN (".$bdo_id_list.")")->count();

                $test7 =  Testimonial::join('dhanuka_doctor','testimonials.user_id','=','dhanuka_doctor.dd_id')->where('dhanuka_doctor.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','dhanuka_doctor')->whereRaw("testimonials.user_id IN (".$bdo_id_list.")")->count();

                // echo "<pre>"; print_r($test7); die;
                $testimonial = $test1+$test2+$test3+$test4+$test5+$test6+$test7;

                $customer_care_closed           = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '!=', 'Sale')->where('customer_queries.status', '3')->where('users.state_id',$branchState)->count();

                $customer_care_follow_up        = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '!=', 'Sale')->where('customer_queries.status', '2')->where('users.state_id',$branchState)->count();

                $customer_care_open             = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '!=', 'Sale')->where('customer_queries.status', '1')->where('users.state_id',$branchState)->count();

                $customer_care_sale_closed      = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '=', 'Sale')->where('customer_queries.status', '3')->where('users.state_id',$branchState)->count();

                $customer_care_sale_follow_up   = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '=', 'Sale')->where('customer_queries.status', '2')->where('users.state_id',$branchState)->count();

                $customer_care_sale_open        = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->whereRaw("bdo_id IN (".$bdo_id_list.")")->where('type_of_call', '=', 'Sale')->where('customer_queries.status', '1')->where('users.state_id',$branchState)->count();

                $efficiency_query = " SELECT  *
                                            FROM efficiency_results
                                            WHERE ".$where."
                                                AND month=MONTH(CURRENT_DATE())
                                                AND year=YEAR(CURRENT_DATE()) LIMIT 0,1 ";

                $user_efficiency_data           = DB::select($efficiency_query);
                $efficiency_result              = array_map(function ($value) {
                                                return (array)$value;
                                            }, $user_efficiency_data);

                // ACTIVITIY / REGISTERED / VALIDATED GRAPH RESULT
                
                $user_activity_query        = " SELECT  IFNULL(DATE_FORMAT(created_at,'%Y-%m-%d'), 0) AS y_m_d, 
                            IFNULL(COUNT(ticket_id), 0) AS activities, 
                            IFNULL(SUM(farmer_count), 0) AS registered_farmers, 
                            IFNULL(SUM(validated_farmers), 0) AS validated_farmers
                    FROM tickets
                    WHERE ".$whereMonth." AND (user_id IN (".$bdo_id_list.")) AND (user_type ='bdo') AND state_id = ".$branchState."
                    GROUP BY YEAR(created_at), MONTH(created_at)
                    ORDER BY created_at ";                   
                
                $self_participation_query   = " SELECT  
                        IFNULL(DATE_FORMAT(SP.created_at,'%Y-%m-%d'), 0) AS y_m_d, 
                        IFNULL(COUNT(self_participation_id), 0) AS self_participations
                    FROM self_participations SP JOIN tickets as T ON SP.ticket_id = T.ticket_id
                    WHERE  SP.user_id = '".$zonal_manager_id."' AND SP.user_type='zonal' AND T.state_id = ".$branchState."
                    GROUP BY YEAR(SP.created_at), MONTH(SP.created_at)
                    ORDER BY SP.created_at ";
            
                $user_activity_data         =   DB::select($user_activity_query);
                $user_activity_result       =   array_map(function ($value) {
                                            return (array)$value;
                                        }, $user_activity_data);
                $self_participation_data    =   DB::select($self_participation_query);
                $self_participation_result  =   array_map(function ($value) {
                                            return (array)$value;
                                        }, $self_participation_data);
            }
            else {
                // ACTIVITIY / REGISTERED / VALIDATED GRAPH RESULT
                if($start_month!='' || $end_month!='' || $start_year!='' || $end_year!='') {
                $whereMonth .= " AND (MONTH(created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(created_at) between '".$start_year."' and '".$end_year."')";
                }
                if($start_month!='' || $end_month!='' || $start_year!='' || $end_year!='') {
                $whereMonth1 .= " AND (MONTH(testimonials.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(testimonials.created_at) between '".$start_year."' and '".$end_year."')";
                }
                $total_no_of_activities         = Ticket::whereRaw($whereMonth)->where('user_type', 'bdo')->where('state_id',$branchState)->count();


                $total_farmer  = Farmer::join('tickets','farmers.ticket_id','=','tickets.ticket_id')->whereRaw("(MONTH(farmers.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(farmers.created_at) between '".$start_year."' and '".$end_year."')")->where('tickets.state_id',$branchState)->where('farmers.ticket_status', '1')->count();

                // print_r($total_farmer);die;

                $total_validate_farmer          = Farmer::join('tickets','tickets.ticket_id','=','farmers.ticket_id')->whereRaw("(MONTH(farmers.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(farmers.created_at) between '".$start_year."' and '".$end_year."')")->where('farmers.ticket_status', '1')->where('validation_status', 'Validated')->where('tickets.state_id',$branchState)->count();
                $average_farmer                 = (($total_no_of_activities!='' || $total_no_of_activities!='0')?number_format((float)($total_farmer/$total_no_of_activities), 2, '.', ''):'0');
                $average_validated_farmer       = (($total_no_of_activities!='' || $total_no_of_activities!='0')?number_format((float)($total_validate_farmer/$total_no_of_activities), 2, '.', ''):'0');
                // $testimonial                    = Testimonial::whereRaw($whereMonth)->count();
               $test1 =  Testimonial::join('zonal_manager_states','testimonials.user_id','=','zonal_manager_states.zonal_manager_id')->where('zonal_manager_states.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','zonal')->count();
               $test2 =  Testimonial::join('regional_manager_states','testimonials.user_id','=','regional_manager_states.regional_manager_id')->where('regional_manager_states.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','regional')->count();
               $test3 =  Testimonial::join('area_manager_states','testimonials.user_id','=','area_manager_states.area_manager_id')->where('area_manager_states.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','area')->count();
               $test4 =  Testimonial::join('sales_area_manager_states','testimonials.user_id','=','sales_area_manager_states.sales_area_manager_id')->where('sales_area_manager_states.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','sales_area')->count();
               $test5 =  Testimonial::join('users','testimonials.user_id','=','users.id')->where('users.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','bdo')->count();
               $test6 =  Testimonial::join('sales_regional_manager_states','testimonials.user_id','=','sales_regional_manager_states.sales_regional_manager_id')->where('sales_regional_manager_states.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','sales_regional')->count();
               $test7 =  Testimonial::join('dhanuka_doctor','testimonials.user_id','=','dhanuka_doctor.dd_id')->where('dhanuka_doctor.state_id',$branchState)->whereRaw($whereMonth1)->where('testimonials.user_type','dhanuka_doctor')->count();
                // echo "<pre>"; print_r($test7); die;
                $testimonial = $test1+$test2+$test3+$test4+$test5+$test6+$test7;
                $customer_care_closed           = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->where('type_of_call', '!=', 'Sale')->where('users.state_id',$branchState)->where('customer_queries.status', '3')->count();
                $customer_care_follow_up        = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->where('type_of_call', '!=', 'Sale')->where('users.state_id',$branchState)->where('customer_queries.status', '2')->count();
                $customer_care_open             = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->where('type_of_call', '!=', 'Sale')->where('users.state_id',$branchState)->where('customer_queries.status', '1')->count();
                $customer_care_sale_closed      = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->where('type_of_call', '=', 'Sale')->where('users.state_id',$branchState)->where('customer_queries.status', '3')->count();
                $customer_care_sale_follow_up   = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->where('type_of_call', '=', 'Sale')->where('users.state_id',$branchState)->where('customer_queries.status', '2')->count();
                $customer_care_sale_open        = CustomerQuery::join('users','users.id','=','customer_queries.bdo_id')->whereRaw("(MONTH(customer_queries.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(customer_queries.created_at) between '".$start_year."' and '".$end_year."')")->where('type_of_call', '=', 'Sale')->where('users.state_id',$branchState)->where('customer_queries.status', '1')->count(); 

                $efficiency_query               = " SELECT  *
                                                FROM efficiency_results
                                                WHERE 1=2 LIMIT 0,1 ";
                $user_efficiency_data           = DB::select($efficiency_query);                
                $efficiency_result              = array_map(function ($value) {
                                                    return (array)$value;
                                                }, $user_efficiency_data);
                // echo "<pre>"; print_r($efficiency_result); die;
                                                
                $user_activity_query        = " SELECT  IFNULL(DATE_FORMAT(created_at,'%Y-%m-%d'), 0) AS y_m_d, 
                            IFNULL(COUNT(ticket_id), 0) AS activities, 
                            IFNULL(SUM(farmer_count), 0) AS registered_farmers, 
                            IFNULL(SUM(validated_farmers), 0) AS validated_farmers
                    FROM tickets
                    WHERE user_type ='bdo' AND ".$whereMonth." AND state_id in(' ".implode(',',$State)."')
                    GROUP BY YEAR(created_at), MONTH(created_at)
                    ORDER BY created_at ";
                //   print_r($user_activity_query);die;
                


                $self_participation_query   = " SELECT  
                        IFNULL(DATE_FORMAT(SP.created_at,'%Y-%m-%d'), 0) AS y_m_d, 
                        IFNULL(COUNT(self_participation_id), 0) AS self_participations
                    FROM self_participations SP JOIN tickets as T ON SP.ticket_id = T.ticket_id
                    WHERE (MONTH(SP.created_at) between '".$start_month."' and '".$end_month."') AND (YEAR(SP.created_at) between '".$start_year."' and '".$end_year."') AND T.state_id in(' ".implode(',',$State)."')
                    GROUP BY YEAR(SP.created_at), MONTH(SP.created_at)
                    ORDER BY SP.created_at ";


                $user_activity_data         =   DB::select($user_activity_query);

                $user_activity_result       =   array_map(function ($value) {
                                                return (array)$value;
                                            }, $user_activity_data);

                $self_participation_data    =   DB::select($self_participation_query);
                $self_participation_result  =   array_map(function ($value) {
                                                return (array)$value;
                                            }, $self_participation_data);
            }

            $regional_sales = $this->regionalSales($zonal_manager_id);
            $area_sales     = $this->areaSales($regional_manager_id);
            $bdo            = $this->bdo($area_manager_id);
            $month          = date('m');


            return View::make('backend.dashboard', array(
                'zonal_list'                    => $zonal_list,
                'activities'                    => $user_activity_result,
                'self_participations'           => $self_participation_result,                  
                'efficiency'                    => $efficiency_result,
                'total_no_of_activities'        => $total_no_of_activities,
                'total_farmer'                  => $total_farmer,
                'total_validate_farmer'         => $total_validate_farmer,
                'average_farmer'                => $average_farmer,
                'average_validated_farmer'      => $average_validated_farmer,                                    
                'testimonial'                   => $testimonial,                                    
                'customer_care_closed'          => $customer_care_closed,
                'customer_care_follow_up'       => $customer_care_follow_up,
                'customer_care_open'            => $customer_care_open,
                'customer_care_sale_closed'     => $customer_care_sale_closed,
                'customer_care_sale_follow_up'  => $customer_care_sale_follow_up,
                'customer_care_sale_open'       => $customer_care_sale_open,
                
                'my_data'               => $my_data,
                'regional_sales'        => $regional_sales,
                'area_sales'            => $area_sales,
                'bdo'                   => $bdo,
                'month'                 => $month
            ));                
            }
            else {
            return redirect('/');
            }
            }

            } 
        }    
        public function anyEfficiency() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            else {
                $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
                if($userData) {
                    $my_data                    = Input::all();
                    //dd($my_data);
                    $zonal_manager_id           = (isset($my_data['zonal_manager'])?$my_data['zonal_manager']:'');
                    $regional_manager_id        = (isset($my_data['regional_manager'])?$my_data['regional_manager']:'');
                    $sales_regional_manager_id  = (isset($my_data['sales_regional_manager'])?$my_data['sales_regional_manager']:'');
                    $area_manager_id            = (isset($my_data['area_manager'])?$my_data['area_manager']:'');
                    $sales_area_manager_id      = (isset($my_data['sales_area_manager'])?$my_data['sales_area_manager']:'');
                    $bdo_id                     = (isset($my_data['bdo'])?$my_data['bdo']:'');
                    $month                      = (isset($my_data['month_type'], $my_data['month']) && $my_data['month_type']=='next'?$my_data['month']+1:$my_data['month']-1);
                    
                    if($month>=1 && $month<=12) {
                        $where = "(1=1) ";
                        if($bdo_id!='') {
                            $where .= " AND user_id = '".$bdo_id."' AND user_type='bdo'";
                        }
                        else if($sales_area_manager_id!='') {
                            $where .= " AND user_id = '".$sales_area_manager_id."' AND user_type='sales_area'";
                        }
                        else if($area_manager_id!='') {
                            $where .= " AND user_id = '".$area_manager_id."' AND user_type='area'";
                        }
                        else if($sales_regional_manager_id!='') {
                            $where .= " AND user_id = '".$sales_regional_manager_id."' AND user_type='sales_regional'";
                        }
                        else if($regional_manager_id!='') {
                            $where .= " AND user_id = '".$regional_manager_id."' AND user_type='regional'";
                        }
                        else if($zonal_manager_id!='') {
                            $where .= " AND user_id = '".$zonal_manager_id."' AND user_type='zonal'";
                        }
                        else {
                        }

                        $user_efficiency_data           = Efficiency::select('efficiency')->whereRaw($where)->where('month', $month)->where('year', date('Y'))->first();

                        $efficiency_data                = array();
                        $efficiency_data['efficiency']  = ($user_efficiency_data?$user_efficiency_data->efficiency:0.00);
                        $efficiency_data['month']       = $month;
                        $efficiency_data['month_string']= date('M Y', strtotime(date('Y').'-'.$month.'-01'));
                        //echo date('M Y', strtotime(date('Y').'-'.$month.'-01')); exit;
                        echo json_encode($efficiency_data, true);
                    }
                }

                $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
                if($userData1) {
                    $my_data                    = Input::all();
                    $branchState = $userData1->state;
                    //dd($my_data);
                    $zonal_manager_id           = (isset($my_data['zonal_manager'])?$my_data['zonal_manager']:'');
                    $regional_manager_id        = (isset($my_data['regional_manager'])?$my_data['regional_manager']:'');
                    $sales_regional_manager_id  = (isset($my_data['sales_regional_manager'])?$my_data['sales_regional_manager']:'');
                    $area_manager_id            = (isset($my_data['area_manager'])?$my_data['area_manager']:'');
                    $sales_area_manager_id      = (isset($my_data['sales_area_manager'])?$my_data['sales_area_manager']:'');
                    $bdo_id                     = (isset($my_data['bdo'])?$my_data['bdo']:'');
                    $month                      = (isset($my_data['month_type'], $my_data['month']) && $my_data['month_type']=='next'?$my_data['month']+1:$my_data['month']-1);
                    
                    if($month>=1 && $month<=12) {
                        $where = "(1=1) ";
                        if($bdo_id!='') {
                            $where .= " AND user_id = '".$bdo_id."' AND user_type='bdo'";
                        }
                        else if($sales_area_manager_id!='') {
                            $where .= " AND user_id = '".$sales_area_manager_id."' AND user_type='sales_area'";
                        }
                        else if($area_manager_id!='') {
                            $where .= " AND user_id = '".$area_manager_id."' AND user_type='area'";
                        }
                        else if($sales_regional_manager_id!='') {
                            $where .= " AND user_id = '".$sales_regional_manager_id."' AND user_type='sales_regional'";
                        }
                        else if($regional_manager_id!='') {
                            $where .= " AND user_id = '".$regional_manager_id."' AND user_type='regional'";
                        }
                        else if($zonal_manager_id!='') {
                            $where .= " AND user_id = '".$zonal_manager_id."' AND user_type='zonal'";
                        }
                        else {
                        }

                        $user_efficiency_data           = Efficiency::select('efficiency')->whereRaw($where)->where('month', $month)->where('year', date('Y'))->first();
                        
                        $efficiency_data                = array();
                        $efficiency_data['efficiency']  = ($user_efficiency_data?$user_efficiency_data->efficiency:0.00);
                       
                        $efficiency_data['month']       = $month;
                        $efficiency_data['month_string']= date('M Y', strtotime(date('Y').'-'.$month.'-01'));
                        //echo date('M Y', strtotime(date('Y').'-'.$month.'-01')); exit;
                        echo json_encode($efficiency_data, true);
                    }
                }
            } 
        }




        public function getChangePassword() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.change_password');
        }
        public function postPasswordChange() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $rolename   = \Session::get('role_admin');
            $data       = Input::all();

            $pas        = $data['current_password'];
            $pass       = base64_encode($pas);

            $query_changepwd    = \ DB::table('admin')
                                    ->where('role_id', '5')
                                    ->where('password', $pass)
                                    ->get();
            // var_dump($query_changepwd);exit;

            $count      = count($query_changepwd);

            if ($count == 1) {
                if ($data['new_password'] == $data['confirm_password']) {
                    $a          = $data['new_password'];
                    $b          = base64_encode($a);
                    $value      = array('password' => $b);
                    $chng_pwd   = \DB::table('admin')
                                    ->where('role_id', '5')
                                    ->update($value);
                    if ($chng_pwd) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                } else {
                    //echo"newpassword and confirm password are  not correct";
                    echo 2;
                }
            } else {
                //echo"currentpassword not correct";
                echo 3;
            }
        }

        public function getLogout() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            Session::flush();
            return redirect('admin/');
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
        public function getAddBdo() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $category_list   = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
            $state_list      = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
            $language_list   = Language::select('id', 'language')->orderBy('language', 'asc')->get();
            $zonal_list      = ZonalManager::where('status', '1')->get();
            $territory       = territories::where('status', '1')->get();
            $market          = markets::where('status', '1')->get();
            //   print_r($maket_list); exit;
            return View::make('backend.bdo.addbdo', array('zonal_list'=> $zonal_list,'state_list'=>$state_list, 'language_list'=>$language_list, 'category_list'=>$category_list,'territory'=>$territory,'market'=>$market));
        }
        
		public function postCreateBdo(Request $request) {         
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            //print_r($my_data); exit;
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
                                'product_category'      => 'required|array',
								// 'states'                => 'required',
                                // 'district_name'         => 'required|array',
                                // 'territory_name'        => 'required|array',
                                // 'market_name'           => 'required|array'
							);
                $validator  = Validator::make($my_data, $rules);
                if ($validator->fails()) {
                    return Redirect::back()->withInput()->withErrors($validator);
                }

                $sess           = \Session::get('adminid');
                $curTime        = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "admin/create-bdo",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);
                
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
				$states                     = (!empty($my_data['states'])) ? $my_data['states'] : '';
                $district                   = (!empty($my_data['district_name'])) ? json_encode($my_data['district_name']) : '';
                $territory                  = (!empty($my_data['territory_name'])) ? json_encode($my_data['territory_name']) : '';
                $market                     = (!empty($my_data['market_name'])) ? json_encode($my_data['market_name']) : ''; 
                $product_category           = $my_data['product_category'];
                
                $app_permission_edit_ticket     = isset($my_data['app_permission_edit_ticket'])?'1':'0';
                $app_permission_close_ticket    = isset($my_data['app_permission_close_ticket'])?'1':'0';
                
                $check_dd_code         = DhanukaDoctor::where('ECN', $bdo_code)->count();
                $check_bdo_code        = User::where('ECN', $bdo_code)->count();

                if($check_dd_code > 0 || $check_bdo_code > 0) {
                    //$errors->add('your_custom_error', "ECN already exists, Please choose a different ECN.");
                    $errors = array('your_custom_error' => 'ECN already exists, Please choose a different ECN.');
                    //return Redirect::back()->withInput()->withErrors($errors);
                    return Redirect::back()->withErrors($errors)->withInput();
                }
                else {
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
                                                    'states'                    => $states,
                                                    'districts'                 => $district,
                                                    'territory'                 => $territory,
                                                    'market'                    => $market,
                                                    'status'                    => '1',
                                                    'app_permission_edit_ticket'    => $app_permission_edit_ticket,
                                                    'app_permission_close_ticket'   => $app_permission_close_ticket
                                                );

                        // echo "<pre>"; print_r($insert_bdo); exit();
                        $insert_user    = User::firstOrCreate($insert_bdo);
                        $result         = $insert_user->id;

                        if($result) {
                            $user_product_category = array();
                            foreach($product_category AS $product_category_id) {
                                $user_product_category[]    = array(
                                                                    'user_id'                   => $result,
                                                                    'product_crop_category_id'  => $product_category_id,
                                                                    'status'                    => '1'
                                                                    );
                            }
                            $insert_user_product_category   = UserProductCategory::insert($user_product_category);

                            $log_firstTime  = strtotime($log_start_time);$log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                            
                            Session::flash('message', "BDO Successfully Created.");
                            return redirect('admin/bdo-list');
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
		
        public function getDeputation() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.bdo.deputation');
        }    
        public function postAlldeputation(Request $request) {
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
                $zonalId = "where ud.status = '1' ".$datesearch;
            }

            $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
            if ($userData1) {
                
                $zonalId = "where ud.status = '1' ".$datesearch;
                
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
        public function getBdoList() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.bdo.bdolist');
        }    
        public function postAllbdolist(Request $request) {
            //print_r(Request::all()); exit;
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "admin/bdo-list",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            //print_r(Request::all()); exit;
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
                    $zonalId = "where u.status = '".$status."'";
                }
                else{
                    $zonalId = "";
                }
            }

            /*$userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
            if ($userData1) {
                if ($status != '') {
                    $zonalId = "where u.zonal_manager_id = ".$userData1->zonal_manager_id."' and u.status = '".$status."'";
                }
                else{
                    $zonalId = "where u.zonal_manager_id = ".$userData1->zonal_manager_id;
                }
            }*/

            $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('state')->first();
            if ($userData1) {
                $state_ids = str_replace("]", "", str_replace("[", "", $userData1->state));
                if ($status != '') {
                    $zonalId = "where u.state_id IN (".$state_ids.") and u.status = '".$status."'";
                } else{
                    $zonalId = "where u.state_id IN (".$state_ids.")";
                }
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
                                        u.id,
                                        REPLACE(REPLACE(u.districts,'[',''),']','') as districtIds,
                                        REPLACE(REPLACE(u.territory,'[',''),']','') as territory,
                                        REPLACE(REPLACE(u.market,'[',''),']','') as market
                                        
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
                                     
                                    LEFT JOIN   user_deputations            ud      ON ud.user_id=u.id and ud.status='1'
                                      ".$zonalId."                          
                                    GROUP BY u.id
                                    ORDER BY
                                        u.created_at DESC
                                    ) A,
                                    (select @a:=0) initvars";
                                    // echo $query; exit;
                $top_open_ticket    = collect(DB::select($query));            
                //print_r($top_open_ticket);
                foreach ($top_open_ticket as $key => $value) {
                    $districtIds  = $value->districtIds;
                    $territory = $value->territory;
                    $market = $value->market;
                    if($districtIds) {
                        $qr_districts = 'SELECT GROUP_CONCAT(DISTINCT district) as districtname FROM `districts` WHERE district_id IN ('.$districtIds.')';
                        $districts_list = collect(DB::select($qr_districts));
                        $top_open_ticket[$key]->districtname = $districts_list[0]->districtname;
                    } else {
                        $top_open_ticket[$key]->districtname = "";
                    }
                    if($territory) {
                        $qr_territory = 'SELECT GROUP_CONCAT(DISTINCT territory) as territory FROM `territories` WHERE territory_id IN ('.$territory.')';
                        $territory_list = collect(DB::select($qr_territory));
                        $top_open_ticket[$key]->territory = $territory_list[0]->territory;
                    } else {
                        $top_open_ticket[$key]->territory = "";
                    }
                    if($market) {
                        $qr_markets = 'SELECT GROUP_CONCAT(DISTINCT market SEPARATOR ", ") as market FROM `markets` WHERE market_id IN ('.$market.')';
                        $markets_list = collect(DB::select($qr_markets));
                        $top_open_ticket[$key]->market = $markets_list[0]->market;
                    } else {
                        $top_open_ticket[$key]->market = "";
                    }
                }
                $log_firstTime  = strtotime($log_start_time);$log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

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
                                    u.id,
                                    REPLACE(REPLACE(u.districts,'[',''),']','') as districtIds,
                                    REPLACE(REPLACE(u.territory,'[',''),']','') as territory,
                                    REPLACE(REPLACE(u.market,'[',''),']','') as market

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
                                
                                LEFT JOIN   user_deputations            ud      ON ud.user_id=u.id and ud.status='1'
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
                            // echo $query; exit;
                $top_open_ticket    = collect(DB::select($query));
                foreach ($top_open_ticket as $key => $value) {
                    $districtIds  = $value->districtIds;
                    $territory = $value->territory;
                    $market = $value->market;
                    if($districtIds) {
                        $qr_districts = 'SELECT GROUP_CONCAT(DISTINCT district) as districtname FROM `districts` WHERE district_id IN ('.$districtIds.')';
                        $districts_list = collect(DB::select($qr_districts));
                        $top_open_ticket[$key]->districtname = $districts_list[0]->districtname;
                    } else {
                        $top_open_ticket[$key]->districtname = "";
                    }
                    if($territory) {
                        $qr_territory = 'SELECT GROUP_CONCAT(DISTINCT territory) as territory FROM `territories` WHERE territory_id IN ('.$territory.')';
                        $territory_list = collect(DB::select($qr_territory));
                        $top_open_ticket[$key]->territory = $territory_list[0]->territory;
                    } else {
                        $top_open_ticket[$key]->territory = "";
                    }
                    if($market) {
                        $qr_markets = 'SELECT GROUP_CONCAT(DISTINCT market SEPARATOR ", ") as market FROM `markets` WHERE market_id IN ('.$market.')';
                        $markets_list = collect(DB::select($qr_markets));
                        $top_open_ticket[$key]->market = $markets_list[0]->market;
                    } else {
                        $top_open_ticket[$key]->market = "";
                    }
                }
                $log_firstTime  = strtotime($log_start_time);$log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return Datatables::of($top_open_ticket)->make();            
            }
        }
        
		public function getEditBdo($userId) { 
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $curTime        = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "admin/edit-bdo",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);
                if($userId!='') {
                    $user_data = User::where('id', $userId)->first();
                    //echo "<pre>";print_r($user_data);die;
                    if($user_data) {
                        $state_list             = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                        $district_list          = District::where('status', '1')->select('district_id', 'district')->where(['state_id' => $user_data->state_id])->orderBy('district', 'asc')->get();
                        $language_list          = Language::select('id', 'language')->orderBy('language', 'asc')->get();
                        $zonal_list             = ZonalManager::where('status', '1')->get();
                        $regional_list          = RegionalManager::where('status', '1')->select('regional_manager_id', 'regional_manager_name')->where(['zonal_manager_id' => $user_data->zonal_manager_id])->orderBy('regional_manager_name', 'asc')->get();
                        $sales_regional_list    = SalesRegionalManager::where('status', '1')->select('sales_regional_manager_id', 'sales_regional_manager_name')->where(['zonal_manager_id' => $user_data->zonal_manager_id])->orderBy('sales_regional_manager_name', 'asc')->get();
                        $area_list              = AreaManager::where('status', '1')->select('area_manager_id', 'area_manager_name')->where(['regional_manager_id' => $user_data->regional_manager_id])->orderBy('area_manager_name', 'asc')->get();

                        $sales_area_list        = SalesAreaManager::where('status', '1')->select('sales_area_manager_id', 'sales_area_manager_name')->where(['regional_manager_id' => $user_data->regional_manager_id])->orderBy('sales_area_manager_name', 'asc')->get();
                         $counterpart_fl = User::where('designation', 'COUNTERPART-FL')->get()->toArray();

                        $deputation_list        = UserDeputation::where('status', '1')->where('user_id', $userId)->first();

                        // dd($deputation_list);
                        // die;
						$territory_list          = territories::where('status', '1')->select('territory_id', 'territory')->orderBy('territory', 'asc')->get();
                        $market_list             = markets::where('status', '1')->select('market_id', 'market')->orderBy('market', 'asc')->get();

                        $states                  = $user_data->states;
                        
                        if($states != NULL) {
                            $user_district_list      = District::where('status', '1')->select('district_id', 'district')->where(['state_id' => $states])->orderBy('district', 'asc')->get();
                            if($user_data->districts) {
                                $district_data = str_replace('"', "", str_replace('[', "",str_replace(']', "",$user_data->districts)));
                                $districts = explode(',',$district_data);
                            } else {
                                $user_district_list   = District::where('status', '1')->select('district_id', 'district')->orderBy('district', 'asc')->get();
                                $districts = array();
                            }
                            // echo "<pre>"; print_r($user_district_list); print_r($districts); exit();
                        } else {
                            $user_district_list = array();
                            $districts = array();
                        }

                        if($districts) {
                            $territory               = $user_data->territory;
                            $user_territory_list     = territories::where('status', '1')->select('territory_id', 'territory')->whereIn('district_id', $districts)->orderBy('territory', 'asc')->get();
                            if($user_data->territory) {
                                $territory_data = str_replace('"', "", str_replace('[', "",str_replace(']', "",$user_data->territory)));
                                $territory = explode(',',$territory_data);
                            } else {
                                $user_territory_list   = territories::where('status', '1')->select('territory_id', 'territory')->orderBy('territory', 'asc')->get();
                                $territory = array();
                            }
                            // echo "<pre>"; print_r($user_territory_list); print_r($territory); exit();
                        } else {
                            $user_territory_list = array();
                            $territory      = array();
                        }

                        if($territory) {
                            $market               = $user_data->market;
                            $user_market_list     = markets::where('status', '1')->select('market_id', 'market')->whereIn('territory_id', $territory)->orderBy('market', 'asc')->get();
                            if($user_data->territory) {
                                $market_data = str_replace('"', "", str_replace('[', "",str_replace(']', "",$user_data->market)));
                                $market = explode(',',$market_data);
                            } else {
                                $user_market_list   = markets::where('status', '1')->select('market_id', 'market')->orderBy('market', 'asc')->get();
                                $market = array();
                            }
                            // echo "<pre>"; print_r($user_market_list); print_r($market); exit();
                        } else {
                            $user_market_list = array();
                            $market      = array();
                        }
						
                        $user_product_category  = UserProductCategory::select(DB::raw("GROUP_CONCAT(product_crop_category_id SEPARATOR ',')  AS category_id"))->where('user_id', $userId)->where('status', '1')->first();
                        $category_list          = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();

                        $log_firstTime  = strtotime($log_start_time);$log_firstTime  = strtotime($log_start_time);
                        $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                        $log_time_diff  = $log_lastTime-$log_firstTime;
                        $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                        DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

            
            return View::make('backend.bdo.editbdo', array( 'zonal_list'            => $zonal_list,
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
                            'territory_list'        => $territory_list,
                            'market_list'           => $market_list,
                            'user_category_list'    => $user_product_category, 
							'states'                => $states,
							'districts'             => $districts,
                            'user_district_list'    => $user_district_list,
                            'territory'             => $territory,
                            'user_territory_list'   => $user_territory_list,
                            'market'                => $market,
                            'user_market_list'      => $user_market_list,
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
		
		
        public function postUpdateBdo(Request $request) {         
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $curTime        = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "admin/update-bdo",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);

                $my_data        = Input::all();
                // echo "<pre>"; print_r($my_data); exit();
                
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
                if($designation != 'BDO-FL') {
                    $counterpart_fl         = NULL;
                } else {
                    $counterpart_fl         = $my_data['counterpart_fl'];
                }
                $bdo_code                   = $my_data['bdo_code'];
                $name                       = $my_data['name'];
                $mobile_no                  = $my_data['mobile_no'];
                $bdo_hq                     = $my_data['bdo_hq'];  
				$states                     = ($my_data['states']) ? $my_data['states'] : '';
                $district                   = (!empty($my_data['districts'])) ? json_encode($my_data['districts']) : '';
                $territory                  = (!empty($my_data['territory_name'])) ? json_encode($my_data['territory_name']) : '';
                $market                     = (!empty($my_data['market_name'])) ? json_encode($my_data['market_name']) : '';
                $product_category           = $my_data['product_category'];          
                $app_permission_edit_ticket = isset($my_data['app_permission_edit_ticket'])?'1':'0';
                $app_permission_close_ticket= isset($my_data['app_permission_close_ticket'])?'1':'0';  

                $change_password			= isset($my_data['change_password'])?'1':'0';                       
                
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
													'states'                    => $states,
                                                    'districts'                 => $district,
                                                    'states'                    => $states,
                                                    'market'                    => $market,
                                                    'territory'                 => $territory,
													
                                                    'bdo_mobile_no'             => $mobile_no,                            
                                                    'bdo_hq'                    => $bdo_hq, 
                                                    
                                                    'app_permission_edit_ticket'    => $app_permission_edit_ticket,
                                                    'app_permission_close_ticket'   => $app_permission_close_ticket
                                                ); 
                    if($change_password=='1'){
                        $random_string  			= $this->generateRandomString(8);
                        $password       			= \Hash::make($random_string);
                        $updated_values['password']	= $password;
                        $updated_values['dash']		= $random_string;
                    } 
                    


                    if($on_deputation=='1') {


                        $result                 = DB::table('users')
                                                    ->where('id', $bdo_id)
                                                    ->update($updated_values);  
                        if($deputation_zonal_manager_id!='' && $deputation_regional_manager_id!='' && $deputation_sales_regional_manager_id!='' && $deputation_area_manager_id!='' && $deputation_sales_area_manager_id!='' && $deputation_from_date!='' && $deputation_to_date!='') {
                            $DpStatus = array('status'=>'0');
                            $changeDeputationStatus = DB::table('user_deputations')
                                                      ->where('user_id',$bdo_id)
                                                      ->update($DpStatus);
                            $insert_bdo_deputation  = UserDeputation::create([       
                                                        'user_id'                   => $bdo_id,
                                                        'zonal_manager_id'          => $deputation_zonal_manager_id,
                                                        'regional_manager_id'       => $deputation_regional_manager_id,
                                                        'sales_regional_manager_id' => $deputation_sales_regional_manager_id,
                                                        'area_manager_id'           => $deputation_area_manager_id,
                                                        'sales_area_manager_id'     => $deputation_sales_area_manager_id,
                                                        'deputation_from_date'      => $deputation_from_date,
                                                        'deputation_to_date'        => $deputation_to_date,
                                                        'status'     => '1',
                                                        'created_at' => date('Y-m-d H:i:s'),
                                                        'updated_at' => date('Y-m-d H:i:s')
                                                    ]);
                            
                            
                            $reset_user_category    = DB::table('user_product_categories')
                                                            ->where('user_id', $bdo_id)
                                                            ->update(['status'=>'0']); 
                            foreach($product_category AS $category_id) {
                                $update_user_manager   = DB::table('user_product_categories')
                                                            ->where('user_id', $bdo_id)
                                                            ->where('product_crop_category_id', $category_id)
                                                            ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                                $user_manager_states   = array(
                                                                'user_id'                   => $bdo_id,
                                                                'product_crop_category_id'  => $category_id,
                                                                'status'                    => '1'
                                                                );
                                $insert_user_manager   = UserProductCategory::firstOrCreate($user_manager_states);
                            }
                        
                        
                            $log_firstTime  = strtotime($log_start_time);$log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                            Session::flash('message', "BDO Successfully Updated.");
                            return redirect('admin/bdo-list');
                            
                        } else {
                            $log_firstTime  = strtotime($log_start_time);$log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                            Session::flash('message', "BDO Successfully Updated.");
                            return redirect('admin/bdo-list');
                        }
                    }
                    else {
                        $update_bdo             = DB::table('users')
                                                    ->where('id', $bdo_id)
                                                    ->update($updated_values); 
                        $update_bdo_deputation  = DB::table('user_deputations')
                                                    ->where('user_id', $bdo_id)
                                                    ->update(['status'=>'0', 'updated_at'=>date('Y-m-d H:i:s')]); 
                        
                        
                        $reset_user_category    = DB::table('user_product_categories')
                                                        ->where('user_id', $bdo_id)
                                                        ->update(['status'=>'0']); 
                        foreach($product_category AS $category_id) {
                            $update_user_manager   = DB::table('user_product_categories')
                                                        ->where('user_id', $bdo_id)
                                                        ->where('product_crop_category_id', $category_id)
                                                        ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                            $user_manager_states   = array(
                                                            'user_id'                   => $bdo_id,
                                                            'product_crop_category_id'  => $category_id,
                                                            'status'                    => '1'
                                                            );
                            $insert_user_manager   = UserProductCategory::firstOrCreate($user_manager_states);
                        }
                        $log_firstTime  = strtotime($log_start_time);$log_firstTime  = strtotime($log_start_time);
                        $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                        $log_time_diff  = $log_lastTime-$log_firstTime;
                        $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                        DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                        Session::flash('message', "BDO Successfully Updated.");
                        return redirect('admin/bdo-list');
                    }
                }
            }
        }
		
        public function postCheckEcn(Request $request) {         
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $my_data    = Input::all();             
                if($my_data) {
                    $bdo_code       = $my_data['bdo_code'];
                    $bdo_id         = $my_data['_bdo'];                
                    $check_bdo_code = User::where('ECN', $bdo_code)->where('id','!=',$bdo_id)
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
				UNION
                                (SELECT general_id AS id FROM general WHERE ECN='".$bdo_code."' LIMIT 0,1) 
                                ";
                    $user   = DB::select(DB::raw($query));
                    
                    echo ($check_bdo_code > 0 || count($user) > 0 ? 'false':'true');die;
                }
            }

            $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
            if ($userData1) {
                $my_data    = Input::all();             
                if($my_data) {
                    $bdo_code       = $my_data['bdo_code'];
                    $bdo_id         = $my_data['_bdo'];                
                    $check_bdo_code = User::where('ECN', $bdo_code)->where('id','!=',$bdo_id)
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
                    
                    echo ($check_bdo_code > 0 || count($user) > 0 ? 'false':'true');die;
                }
            }
        } 

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
                                (SELECT marketing_supervisor_id AS id FROM marketing_supervisor WHERE ECN='".$dd_code."' LIMIT 0,1)
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


                    // $bdo_code       = $my_data['bdo_code'];
                    // $bdo_id         = $my_data['_bdo'];                
                    // $check_bdo_code = User::where('id', '!=', $bdo_id)
                    //                         ->where('ECN', $bdo_code)
                    //                         ->count();

                    
                    // $query  =   "
                    //             (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                    //             UNION
                    //             (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                    //             UNION
                    //             (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                    //             UNION
                    //             (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                    //             UNION
                    //             (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                    //             ";
                    // $user   = DB::select(DB::raw($query));
                    
                    echo ($check_dd_code > 0 || count($user) > 0 ? 'false':'true');die;
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


                    // $bdo_code       = $my_data['bdo_code'];
                    // $bdo_id         = $my_data['_bdo'];                
                    // $check_bdo_code = User::where('id', '!=', $bdo_id)
                    //                         ->where('ECN', $bdo_code)
                    //                         ->count();

                    
                    // $query  =   "
                    //             (SELECT zonal_manager_id AS id FROM zonal_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                    //             UNION
                    //             (SELECT regional_manager_id AS id FROM regional_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                    //             UNION
                    //             (SELECT area_manager_id AS id FROM area_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                    //             UNION
                    //             (SELECT sales_regional_manager_id AS id FROM sales_regional_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                    //             UNION
                    //             (SELECT sales_area_manager_id AS id FROM sales_area_managers WHERE ECN='".$bdo_code."' LIMIT 0,1)
                    //             ";
                    // $user   = DB::select(DB::raw($query));
                    
                    echo ($check_dd_code > 0 || count($user) > 0 ? 'false':'true');
                }
            }
        } 
        public function postUpdateBdoStatus(Request $request) {
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $my_data        = Input::all(); 
                $bdo_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
                $bdo_data    = User::where('id', $bdo_id)->first();
                if($bdo_data) {
                    $bdo_data_status = ($bdo_data->status=='0'?'1':'0');
                    $update_bdo      = DB::table('users')
                                        ->where('id', $bdo_id)
                                        ->update(['status'=>$bdo_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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

        public function getEditticket($ticket_id) {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $dataSourceList = DB::table('data_sources')
                              ->select('data_source_id','data_source')
                              ->where('status','1')
                              ->orderBy('data_source','asc')
                              ->get();

            $cropList = DB::table('crops')
                              ->select('crop_id','crop')
                              ->where('status','1')
                              ->orderBy('crop','asc')
                              ->get();

            $productList = DB::table('products')
                              ->select('product_id','product')
                              ->where('status','1')
                              ->orderBy('product','asc')
                              ->get();

            $languageList = DB::table('languages')
                              ->select('id','language')
                              ->where('status','1')
                              ->orderBy('language','asc')
                              ->get();

            $ticketData = DB::table('tickets')
                          ->where('ticket_no',$ticket_id)
                          ->select('data_source_id','language_id','product_1_id','crop_1_id','product_2_id','crop_2_id','product_3_id','crop_3_id','ticket_no','ticket_id','plan_atp_id')
                          ->get();

            return View::make('backend.ticket.editTicket', array('dataSourceList'=>$dataSourceList, 'cropList'=>$cropList, 'productList'=>$productList, 'languageList'=>$languageList, 'ticketData' => $ticketData[0]));
        }

        public function postUpdateTicket(Request $request){
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $my_data      = Input::all(); 
                $plan_atp_id     = $my_data['plan_atp_id'];
                $ticket_id       = $my_data['ticket_id'];
                $ticket_no       = $my_data['ticket_no'];
                $crop_1_id       = $my_data['crop_1_id'];
                $product_1_id    = $my_data['product_1_id'];
                $crop_2_id       = $my_data['crop_2_id'];
                $product_2_id    = $my_data['product_2_id'];
                $crop_3_id       = $my_data['crop_3_id'];
                $product_3_id    = $my_data['product_3_id'];
                $language_id     = $my_data['language_id'];
                $data_source_id  = $my_data['data_source_id'];
                if ($plan_atp_id != 0) {
                    $update_ticket = DB::table('tickets')
                                     ->where('ticket_id', $ticket_id)
                                     ->update(['data_source_id'=>$data_source_id,'language_id'=>$language_id,'crop_1_id'=>$crop_1_id,'product_1_id'=>$product_1_id,'crop_2_id'=>$crop_2_id,'product_2_id'=>$product_2_id,'crop_3_id'=>$crop_3_id,'product_3_id'=>$product_3_id ]); 
                    $update_plan = DB::table('plan_atp')
                                     ->where('plan_atp_id', $plan_atp_id)
                                     ->update(['data_source_id'=>$data_source_id,'language_id'=>$language_id,'crop_id'=>$crop_1_id,'product_id'=>$product_1_id,'other_crop_1'=>$crop_2_id,'product_id_2'=>$product_2_id,'other_crop_2'=>$crop_3_id,'product_id_3'=>$product_3_id ]);   
                }
                else{
                    $update_ticket = DB::table('tickets')
                                     ->where('ticket_id', $ticket_id)
                                     ->update(['data_source_id'=>$data_source_id,'language_id'=>$language_id,'crop_1_id'=>$crop_1_id,'product_1_id'=>$product_1_id,'crop_2_id'=>$crop_2_id,'product_2_id'=>$product_2_id,'crop_3_id'=>$crop_3_id,'product_3_id'=>$product_3_id ]);
                } 
                if ($update_ticket) {
                    Session::flash('message', "Ticket Successfully Updated.");
                    return redirect('admin/editticket/'.$ticket_no);
                }
                else{
                    Session::flash('message', "Please try again.");
                    return redirect('admin/editticket/'.$ticket_no);
                }
                           
            }
        }
        
        public function getOpenTickets() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.ticket.opentickets');
        }
        public function postAllopenticket(Request $request) {
            //print_r(Request::all()); exit;
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "admin/openticket",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $tids = "";
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $tids = "";

            }

            $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
            if ($userData1) {
                //dd
               $ddData = DB::table('dhanuka_doctor')
                          ->where('zonal_manager_id',$userData1->zonal_manager_id)
                          ->where('status','1')
                          ->select('dd_id')
                          ->get();
                $ddresult = array(); 
                if (isset($ddData) && !empty($ddData)) 
                {
                    $ddDatass = json_decode(json_encode($ddData),true);
                    
                    foreach ($ddDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $ddresult = array_merge($ddresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                          $ddresult[$key] = $value; 
                        } 
                    } 
                    $ticket_ids1 = DB::table('tickets')->whereIn('user_id',$ddresult)->where('user_type','dhanuka_doctor')->select('ticket_id')->get(); 
                    $ticketresult = array();
                    if (count($ticket_ids1)>0) {
                        $ticketDatass = json_decode(json_encode($ticket_ids1),true);
                        foreach ($ticketDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $ticketresult = array_merge($ticketresult, array_flatten($value)); 
                            } 
                            else 
                            { 
                              $ticketresult[$key] = $value; 
                            } 
                        } 
                    }
                }  
                    

                //bdo
                $bdoData = DB::table('users')
                          ->where('zonal_manager_id',$userData1->zonal_manager_id)
                          ->where('status','1')
                          ->select('id')
                          ->get();
                $bdoresult = array(); 
                if (isset($bdoData) && !empty($bdoData)) 
                {
                    $bdoDatass = json_decode(json_encode($bdoData),true);
                    
                    foreach ($bdoDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                          $bdoresult[$key] = $value; 
                        } 
                    } 
                    $ticket_ids2 = DB::table('tickets')->whereIn('user_id',$bdoresult)->where('user_type','bdo')->select('ticket_id')->get();
                    $ticketresult1 = array();
                    if (count($ticket_ids2)>0) {
                        $ticketDatass = json_decode(json_encode($ticket_ids2),true);
                        
                        foreach ($ticketDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $ticketresult1 = array_merge($ticketresult1, array_flatten($value)); 
                            } 
                            else 
                            { 
                              $ticketresult1[$key] = $value; 
                            } 
                        } 
                    }
                } 
                
                if(count($ticketresult)>0 && count($ticketresult1)>0)
                {   
                    $tdata = array_merge($ticketresult,$ticketresult1);
                    $ticketDataMerge = implode("','", $tdata);
                    $tids = "and ut.ticket_id in('".$ticketDataMerge."')";
                }  
                elseif (count($ticketresult)>0) {
                    $ticketDataMerge = implode("','", $ticketresult);
                    $tids = "and ut.ticket_id in('".$ticketDataMerge."')";
                }
                elseif (count($ticketresult1)>0) {
                    $ticketDataMerge = implode("','", $ticketresult1);
                    $tids = "and ut.ticket_id in ('".$ticketDataMerge."')";
                }
                else
                {
                    $tids = "";
                }
            }

            $columns        = array(  
                                    1 =>'A.ticket_no',
                                    2=> 'A.bdo',
                                    3=> 'A.data_source',
                                    4=> 'A.state',
                                    5=> 'A.district',
                                    6=> 'A.tehsil',
                                    7=> 'A.village',
                                    8=> 'A.product',
                                    9=> 'A.crop',
                                    10=> 'A.expected_pda',
                                    11=> 'A.expected_farmers',
                                    12=> 'A.farmer_count',
                                    13=> 'A.invited_farmer_count',
                                    14=> 'A.ticket_status'
                                );  
            
            //print_r(Request::all()); exit;
            $value          = Input::all();
            $limit          = $value['length'];
            $start          = $value['start'];
            $search_array   = $value['search'];     // value & regax
            $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
            
            $order_array    = $value['order'][0]['column'];
            $dir            = $value['order'][0]['dir'];
            $base_url       = url('/');
            $base_url1 = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
            
            $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
            $end_date       = isset($value['end_date']) ?  $value['end_date']:'';


            if($start_date!='' && $end_date!='') {
                    $datesearch = "DATE(ut.created_at) >= DATE('".$start_date."') AND DATE(ut.created_at) <= DATE('".$end_date."')";
            }elseif($start_date!='') {
                    $datesearch = "DATE(ut.created_at) >= DATE('".$start_date."')";
            }
            else {
                    $datesearch = "(1=1)";
            }



            if(empty($search_array['value'])) {      
                $query  =   " 
                                SELECT                                 
                                    @a:=@a+1  as serial_number,
                                    A.*                                
                                FROM
                                    (
                                    SELECT
                                        ut.ticket_no,
                                        CONCAT_WS(
                                                        '\n', 
                                                        NULLIF(dds.ECN, '')
                                                    ) AS assign_ecn,
                                        CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(dds.name, '')
                                                 ) AS assign_to_dd,
                                        CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(ms.ECN, ''),
                                                    NULLIF(zm.ECN, ''),
                                                    NULLIF(rm.ECN, ''),
                                                    NULLIF(am.ECN, ''),
                                                    NULLIF(srm.ECN, ''),
                                                    NULLIF(sam.ECN, ''),
                                                    NULLIF(u.ECN, ''),
                                                    NULLIF(dd.ECN, '')
                                                ) AS ecn,
                                        CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(ms.marketing_supervisor_name, ''),
                                                    NULLIF(zm.zonal_manager_name, ''),
                                                    NULLIF(rm.regional_manager_name, ''),
                                                    NULLIF(am.area_manager_name, ''),
                                                    NULLIF(srm.sales_regional_manager_name, ''),
                                                    NULLIF(sam.sales_area_manager_name, ''),
                                                    NULLIF(u.name, ''),
                                                    NULLIF(dd.name, '')
                                                ) AS bdo,
                                        UCASE(ut.user_type) AS user_type,
                                        l.language,
                                        ds.data_source,
                                        s.state,
                                        d.district,
                                        t.tehsil,
                                        ut.village,
                                        c1.crop     AS crop_1,
                                        p1.product  AS product_1, 
                                        c2.crop     AS crop_2,
                                        p2.product  AS product_2,
                                        c3.crop     AS crop_3,
                                        p3.product  AS product_3,
                                        ut.expected_pda,
                                        ut.expected_farmers,
                                        ut.farmer_count,
                                        ut.invited_farmer_count,
                                        CONCAT_WS(
                                                    '#', 
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_1, ''), NULLIF(ut.sp_number_1, '')), ''),
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_2, ''), NULLIF(ut.sp_number_2, '')), ''),
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_3, ''), NULLIF(ut.sp_number_3, '')), ''),
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_4, ''), NULLIF(ut.sp_number_4, '')), ''),
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_5, ''), NULLIF(ut.sp_number_5, '')), ''),
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_6, ''), NULLIF(ut.sp_number_6, '')), '')
                                                )  AS speacial_participant,                                        
                                        IF( ut.demo_request_id = '0', 'NA',  der.demo_code) as demo_no,
                                        (CASE WHEN ut.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status,
                                        
                                        IF( ut.image_upload_type != '0', CONCAT('".$base_url1."/', ut.uploaded_file), CONCAT('".$base_url."/', ut.uploaded_file)) as uploaded_file,
                                        CONCAT_WS(
                                            '\n', 
                                            NULLIF(zms.zonal_manager_name, ''),
                                            NULLIF(rms.regional_manager_name, ''),
                                            NULLIF(ams.area_manager_name, ''),
                                            NULLIF(srms.sales_regional_manager_name, ''),
                                            NULLIF(sams.sales_area_manager_name, ''),
                                            NULLIF(us.name, ''),
                                            NULLIF(ddss.name, '')
                                        ) AS uploaderName,
                                        ut.latitude,
                                        ut.longitude,
                                            
                                        DATE_FORMAT(ut.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                                        (CASE   WHEN ut.updated_at<>'0000-00-00 00:00:00' 
                                                THEN 
                                                    DATE_FORMAT(ut.updated_at, '%Y-%m-%d, %h:%i %p')
                                                ELSE 
                                                    '-' 
                                        END),
                                        ut.village_id,
                                        ut.ifc_farmer_count,
                                        ut.activity_duration,
                                        ut.total_activity_distance,
                                        (CASE WHEN ut.dd_ticket_role='1' THEN 'Alone' ELSE 'With FL' END) as dd_ticket_role,
                                        CONCAT_WS(
                                            '\n', 
                                            NULLIF(zms.ECN, ''),
                                            NULLIF(rms.ECN, ''),
                                            NULLIF(ams.ECN, ''),
                                            NULLIF(srms.ECN, ''),
                                            NULLIF(sams.ECN, ''),
                                            NULLIF(us.ECN, ''),
                                            NULLIF(ddss.ECN, '')
                                        ) AS uploaderECN
                                    FROM        tickets         ut
                                    LEFT JOIN   data_sources    ds  ON ut.data_source_id=ds.data_source_id
                                    LEFT JOIN   states          s   ON ut.state_id=s.state_id
                                    LEFT JOIN   districts       d   ON ut.district_id=d.district_id
                                    LEFT JOIN   tehsils         t   ON ut.tehsil_id=t.tehsil_id

                                    LEFT JOIN   products        p1  ON ut.product_1_id=p1.product_id
                                    LEFT JOIN   crops           c1  ON ut.crop_1_id=c1.crop_id
                                    LEFT JOIN   products        p2  ON ut.product_2_id=p2.product_id
                                    LEFT JOIN   crops           c2  ON ut.crop_2_id=c2.crop_id
                                    LEFT JOIN   products        p3  ON ut.product_3_id=p3.product_id
                                    LEFT JOIN   crops           c3  ON ut.crop_3_id=c3.crop_id

                                    LEFT JOIN   users                   u   ON ut.user_id=u.id AND ut.user_type='bdo'
                                    LEFT JOIN   marketing_supervisor          ms  ON ut.user_id=ms.marketing_supervisor_id AND ut.user_type='marketing_supervisor'
                                    LEFT JOIN   zonal_managers          zm  ON ut.user_id=zm.zonal_manager_id AND ut.user_type='zonal'
                                    LEFT JOIN   regional_managers       rm  ON ut.user_id=rm.regional_manager_id AND ut.user_type='regional'
                                    LEFT JOIN   area_managers           am  ON ut.user_id=am.area_manager_id AND ut.user_type='area'
                                    LEFT JOIN   sales_regional_managers srm ON ut.user_id=srm.sales_regional_manager_id AND ut.user_type='sales_regional'
                                    LEFT JOIN   sales_area_managers     sam ON ut.user_id=sam.sales_area_manager_id AND ut.user_type='sales_area'
                                    LEFT JOIN   dhanuka_doctor dd ON ut.user_id=dd.dd_id AND ut.user_type='dhanuka_doctor'

                                    LEFT JOIN   users       as            us   ON us.id=ut.uploadedBy AND (ut.uploadedByType='bdo' or ut.uploadedByType = 'SL-FL' or ut.uploadedByType = 'COUNTERPART-FL' or ut.uploadedByType = 'BDO-FL' or ut.uploadedByType = 'BD officer' or ut.uploadedByType = 'SALES-FL')
                                    LEFT JOIN   zonal_managers    as      zms  ON zms.zonal_manager_id=ut.uploadedBy AND ut.uploadedByType='zonal'
                                    LEFT JOIN   regional_managers  as     rms  ON rms.regional_manager_id=ut.uploadedBy AND ut.uploadedByType='regional'
                                    LEFT JOIN   area_managers    as       ams  ON ams.area_manager_id=ut.uploadedBy AND ut.uploadedByType='area'
                                    LEFT JOIN   sales_regional_managers as srms ON srms.sales_regional_manager_id=ut.uploadedBy AND ut.uploadedByType='sales_regional'
                                    LEFT JOIN   sales_area_managers  as   sams ON sams.sales_area_manager_id=ut.uploadedBy AND ut.uploadedByType='sales_area'
                                    LEFT JOIN   dhanuka_doctor as ddss ON ddss.dd_id=ut.uploadedBy AND (ut.uploadedByType='dhanuka_doctor' or ut.uploadedByType='Dhanuka Doctor')
                                    
                                    LEFT JOIN   demo_request der ON ut.demo_request_id=der.demo_request_id 
                                    LEFT JOIN   dhanuka_doctor dds ON ut.assign_to_dd=dds.dd_id

                                    LEFT JOIN   languages l   ON ut.language_id=l.id
                                        WHERE  ".$datesearch." and ut.status='1' ".$tids."  

                                        GROUP BY ut.ticket_id
                                        ) A,
                                        (select @a:=0) initvars
                                    " ;

                                    // echo $query; exit;
                $top_open_ticket    = collect(DB::select($query));
                foreach ($top_open_ticket as $key => $value) {
                    $village_id = $value->village_id;
                    if($village_id) {
                        $lat = $value->latitude;
                        $lon = $value->longitude;
                        if($lat && $lon) {
                            $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                            if($distanceData !==0) {
                                $expDistanceData = explode("~", $distanceData);
                                $top_open_ticket[$key]->isValidate = $expDistanceData[1];
                                $top_open_ticket[$key]->distance = $expDistanceData[0];
                            } else {
                                $top_open_ticket[$key]->isValidate = "No";
                                $top_open_ticket[$key]->distance = "0";
                            }
                        } else {
                            $top_open_ticket[$key]->isValidate = "No";
                            $top_open_ticket[$key]->distance = "0";
                        }
                    } else {
                        $top_open_ticket[$key]->isValidate = "No";
                        $top_open_ticket[$key]->distance = "0";
                    }
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);
                return Datatables::of($top_open_ticket)->make();            
            }
            else {            
                $search = $search_array['value'];
                
                $query  =   " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT
                                    ut.ticket_no,
                                    CONCAT_WS(
                                                        '\n', 
                                                        NULLIF(dds.ECN, '')
                                                    ) AS assign_ecn,
                                    CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(dds.name, '')
                                                 ) AS assign_to_dd,
                                    CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.ECN, ''),
                                                NULLIF(rm.ECN, ''),
                                                NULLIF(am.ECN, ''),
                                                NULLIF(srm.ECN, ''),
                                                NULLIF(sam.ECN, ''),
                                                NULLIF(u.ECN, ''),
                                                NULLIF(dd.ECN, '')
                                            ) AS ecn,
                                    CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.zonal_manager_name, ''),
                                                NULLIF(rm.regional_manager_name, ''),
                                                NULLIF(am.area_manager_name, ''),
                                                NULLIF(srm.sales_regional_manager_name, ''),
                                                NULLIF(sam.sales_area_manager_name, ''),
                                                NULLIF(u.name, ''),
                                                NULLIF(dd.name, '')
                                            ) AS bdo,
                                    UCASE(ut.user_type) AS user_type,
                                    l.language,
                                    ds.data_source,
                                    s.state,
                                    d.district,
                                    t.tehsil,
                                    ut.village,
                                    p1.product  AS product_1, 
                                    c1.crop     AS crop_1,
                                    p2.product  AS product_2,
                                    c2.crop     AS crop_2,
                                    p3.product  AS product_3,
                                    c3.crop     AS crop_3,
                                    ut.expected_pda,
                                    ut.expected_farmers,
                                    ut.farmer_count,
                                    ut.invited_farmer_count,
                                    CONCAT_WS(
                                                '#', 
                                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_1, ''), NULLIF(ut.sp_number_1, '')), ''),
                                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_2, ''), NULLIF(ut.sp_number_2, '')), ''),
                                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_3, ''), NULLIF(ut.sp_number_3, '')), ''),
                                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_4, ''), NULLIF(ut.sp_number_4, '')), ''),
                                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_5, ''), NULLIF(ut.sp_number_5, '')), ''),
                                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_6, ''), NULLIF(ut.sp_number_6, '')), '')
                                            )  AS speacial_participant,                                        
									IF( ut.demo_request_id = '0', 'NA',  der.demo_code) as demo_no,           

                                    (CASE WHEN ut.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status,
                                        

                                    IF( ut.image_upload_type != '0', CONCAT('".$base_url1."/', ut.uploaded_file), CONCAT('".$base_url."/', ut.uploaded_file)) as uploaded_file,
									CONCAT_WS(
                                        '\n', 
                                        NULLIF(zms.zonal_manager_name, ''),
                                        NULLIF(rms.regional_manager_name, ''),
                                        NULLIF(ams.area_manager_name, ''),
                                        NULLIF(srms.sales_regional_manager_name, ''),
                                        NULLIF(sams.sales_area_manager_name, ''),
                                        NULLIF(us.name, ''),
                                        NULLIF(ddss.name, '')
                                    ) AS uploaderName,
                                    ut.latitude,
                                    ut.longitude,
                                    DATE_FORMAT(ut.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                                    (CASE   WHEN ut.updated_at<>'0000-00-00 00:00:00' 
                                            THEN 
                                                DATE_FORMAT(ut.updated_at, '%Y-%m-%d, %h:%i %p')
                                            ELSE 
                                                '-' 
                                    END),
                                    ut.village_id,
                                    ut.ifc_farmer_count,
                                    ut.activity_duration,
                                    ut.total_activity_distance,
                                    (CASE WHEN ut.dd_ticket_role='1' THEN 'Alone' ELSE 'With FL' END) as dd_ticket_role,
                                    CONCAT_WS(
                                        '\n', 
                                        NULLIF(zms.ECN, ''),
                                        NULLIF(rms.ECN, ''),
                                        NULLIF(ams.ECN, ''),
                                        NULLIF(srms.ECN, ''),
                                        NULLIF(sams.ECN, ''),
                                        NULLIF(us.ECN, ''),
                                        NULLIF(ddss.ECN, '')
                                    ) AS uploaderECN
                                FROM        tickets         ut
                                LEFT JOIN   data_sources    ds  ON ut.data_source_id=ds.data_source_id
                                LEFT JOIN   states          s   ON ut.state_id=s.state_id
                                LEFT JOIN   districts       d   ON ut.district_id=d.district_id
                                LEFT JOIN   tehsils         t   ON ut.tehsil_id=t.tehsil_id

                                LEFT JOIN   products        p1  ON ut.product_1_id=p1.product_id
                                LEFT JOIN   crops           c1  ON ut.crop_1_id=c1.crop_id
                                LEFT JOIN   products        p2  ON ut.product_2_id=p2.product_id
                                LEFT JOIN   crops           c2  ON ut.crop_2_id=c2.crop_id
                                LEFT JOIN   products        p3  ON ut.product_3_id=p3.product_id
                                LEFT JOIN   crops           c3  ON ut.crop_3_id=c3.crop_id

                                LEFT JOIN   users                   u   ON ut.user_id=u.id AND ut.user_type='bdo'
                                LEFT JOIN   zonal_managers          zm  ON ut.user_id=zm.zonal_manager_id AND ut.user_type='zonal'
                                LEFT JOIN   regional_managers       rm  ON ut.user_id=rm.regional_manager_id AND ut.user_type='regional'
                                LEFT JOIN   area_managers           am  ON ut.user_id=am.area_manager_id AND ut.user_type='area'
                                LEFT JOIN   sales_regional_managers srm ON ut.user_id=srm.sales_regional_manager_id AND ut.user_type='sales_regional'
                                LEFT JOIN   sales_area_managers     sam ON ut.user_id=sam.sales_area_manager_id AND ut.user_type='sales_area'
                                LEFT JOIN   dhanuka_doctor dd ON ut.user_id=dd.dd_id AND ut.user_type='dhanuka_doctor' 

                                LEFT JOIN   users       as            us   ON us.id=ut.uploadedBy AND (ut.uploadedByType='bdo' or ut.uploadedByType = 'SL-FL' or ut.uploadedByType = 'COUNTERPART-FL' or ut.uploadedByType = 'BDO-FL' or ut.uploadedByType = 'BD officer' or ut.uploadedByType = 'SALES-FL')
                                LEFT JOIN   zonal_managers    as      zms  ON zms.zonal_manager_id=ut.uploadedBy AND ut.uploadedByType='zonal'
                                LEFT JOIN   regional_managers  as     rms  ON rms.regional_manager_id=ut.uploadedBy AND ut.uploadedByType='regional'
                                LEFT JOIN   area_managers    as       ams  ON ams.area_manager_id=ut.uploadedBy AND ut.uploadedByType='area'
                                LEFT JOIN   sales_regional_managers as srms ON srms.sales_regional_manager_id=ut.uploadedBy AND ut.uploadedByType='sales_regional'
                                LEFT JOIN   sales_area_managers  as   sams ON sams.sales_area_manager_id=ut.uploadedBy AND ut.uploadedByType='sales_area'
                                LEFT JOIN   dhanuka_doctor as ddss ON ddss.dd_id=ut.uploadedBy AND (ut.uploadedByType='dhanuka_doctor' or ut.uploadedByType='Dhanuka Doctor')

                                LEFT JOIN   demo_request der ON ut.demo_request_id=der.demo_request_id 
                                LEFT JOIN   dhanuka_doctor dds ON ut.assign_to_dd=dds.dd_id

                                LEFT JOIN   languages               l   ON ut.language_id=l.id
                                WHERE 
                                    (ut.status='1') ".$tids." AND
                                    (
                                        ut.ticket_no     LIKE '%".$search."%'    OR
                                        u.name          LIKE '%".$search."%'    OR
                                        ds.data_source  LIKE '%".$search."%'    OR 
                                        s.state         LIKE '%".$search."%'    OR 
                                        d.district      LIKE '%".$search."%'    OR 
                                        t.tehsil        LIKE '%".$search."%'    OR 
                                        ut.village      LIKE '%".$search."%'    OR 

                                        p1.product      LIKE '%".$search."%'    OR 
                                        p2.product      LIKE '%".$search."%'    OR                                     
                                        p3.product      LIKE '%".$search."%'    OR 

                                        c1.crop         LIKE '%".$search."%'    OR 
                                        c2.crop         LIKE '%".$search."%'    OR                                     
                                        c3.crop         LIKE '%".$search."%'     
                                    )                        
                                GROUP BY ut.ticket_id
                                ) A,
                                (select @a:=0) initvars
                            " ;            
                $top_open_ticket    = collect(DB::select($query));
                foreach ($top_open_ticket as $key => $value) {
                    $village_id = $value->village_id;
                    if($village_id) {
                        $lat = $value->latitude;
                        $lon = $value->longitude;
                        if($lat && $lon) {
                            $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                            if($distanceData !==0) {
                                $expDistanceData = explode("~", $distanceData);
                                $top_open_ticket[$key]->isValidate = $expDistanceData[1];
                                $top_open_ticket[$key]->distance = $expDistanceData[0];
                            } else {
                                $top_open_ticket[$key]->isValidate = "No";
                                $top_open_ticket[$key]->distance = "0";
                            }
                        } else {
                            $top_open_ticket[$key]->isValidate = "No";
                            $top_open_ticket[$key]->distance = "0";
                        }
                    } else {
                        $top_open_ticket[$key]->isValidate = "No";
                        $top_open_ticket[$key]->distance = "0";
                    }
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return Datatables::of($top_open_ticket)->make();
            }
        }
        
        
        public function getCloseTickets() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.ticket.closetickets');
        }
        public function postAllcloseticket(Request $request) {
            //print_r(Request::all()); exit;
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "admin/allcloseticket",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);
            $columns    = array(  
                            1 =>'A.ticket_no',
                            2=> 'A.bdo',
                            3=> 'A.data_source',
                            4=> 'A.state',
                            5=> 'A.district',
                            6=> 'A.tehsil',
                            7=> 'A.village',
                            8=> 'A.product',
                            9=> 'A.crop',
                            10=> 'A.expected_pda',
                            11=> 'A.expected_farmers',
                            12=> 'A.farmer_count',
                            13=> 'A.invited_farmer_count',
                            14=> 'A.ticket_status'
                        );  
            //print_r(Request::all()); exit;
            $value          = Input::all();
            $limit          = $value['length'];
            $start          = $value['start'];
            $search_array   = $value['search'];     // value & regax
            $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
            $order_array    = $value['order'][0]['column'];
            $dir            = $value['order'][0]['dir'];
            $base_url       = url('/');
            $base_url1 = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
            $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
            $end_date       = isset($value['end_date']) ?  $value['end_date']:'';
            $search_query = " (1=1) ";
            if($start_date!='' && $end_date!='') {
                    $search_query.=" and DATE(ut.created_at) >= DATE('".$start_date."') AND DATE(ut.created_at) <= DATE('".$end_date."')";
            }elseif($start_date!='') {
                    $search_query.= " and DATE(ut.created_at) >= DATE('".$start_date."')";
            }
            if(isset($search_array['value']) && $search_array['value']!=''){
                $search = $search_array['value']; 
                $search_query.=" and  (
                    ut.ticket_no    LIKE '%".$search."%'    OR
                    u.name          LIKE '%".$search."%'    OR
                    ds.data_source  LIKE '%".$search."%'    OR 
                    s.state         LIKE '%".$search."%'    OR 
                    d.district      LIKE '%".$search."%'    OR 
                    t.tehsil        LIKE '%".$search."%'    OR 
                    ut.village      LIKE '%".$search."%'    OR 
                    p1.product      LIKE '%".$search."%'    OR 
                    p2.product      LIKE '%".$search."%'    OR                                     
                    p3.product      LIKE '%".$search."%'    OR 
                    c1.crop         LIKE '%".$search."%'    OR 
                    c2.crop         LIKE '%".$search."%'    OR                                     
                    c3.crop         LIKE '%".$search."%'     
                ) ";            
            }

            $query =   " 
                SELECT                                 
                    @a:=@a+1  as serial_number,
                    A.*                                
                FROM
                    (
                        SELECT
                            ut.ticket_no,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(dds.ECN, '')
                                    ) AS assign_ecn,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(dds.name, '')
                                    ) AS assign_to_dd,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(ms.ECN, ''),
                                        NULLIF(zm.ECN, ''),
                                        NULLIF(rm.ECN, ''),
                                        NULLIF(am.ECN, ''),
                                        NULLIF(srm.ECN, ''),
                                        NULLIF(sam.ECN, ''),
                                        NULLIF(u.ECN, ''),
                                        NULLIF(dd.ECN, '')
                                    ) AS ecn,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(ms.marketing_supervisor_name, ''),
                                        NULLIF(zm.zonal_manager_name, ''),
                                        NULLIF(rm.regional_manager_name, ''),
                                        NULLIF(am.area_manager_name, ''),
                                        NULLIF(srm.sales_regional_manager_name, ''),
                                        NULLIF(sam.sales_area_manager_name, ''),
                                        NULLIF(u.name, ''),
                                        NULLIF(dd.name, '')
                                    ) AS bdo,
                            UCASE(ut.user_type) AS user_type,
                            l.language,
                            ds.data_source,
                            s.state,
                            d.district,
                            t.tehsil,
                            ut.village,
                            c1.crop     AS crop_1,
                            p1.product  AS product_1, 
                            c2.crop     AS crop_2,
                            p2.product  AS product_2,
                            c3.crop     AS crop_3,
                            p3.product  AS product_3,
                            ut.expected_pda,
                            ut.expected_farmers,
                            ut.farmer_count,
                            ut.invited_farmer_count,
                            CONCAT_WS(
                                '#', 
                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_1, ''), NULLIF(ut.sp_number_1, '')), ''),
                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_2, ''), NULLIF(ut.sp_number_2, '')), ''),
                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_3, ''), NULLIF(ut.sp_number_3, '')), ''),
                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_4, ''), NULLIF(ut.sp_number_4, '')), ''),
                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_5, ''), NULLIF(ut.sp_number_5, '')), ''),
                                NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_6, ''), NULLIF(ut.sp_number_6, '')), '')
                            )  AS speacial_participant,                                        
                            IF( ut.demo_request_id = '0', 'NA',  der.demo_code) as demo_no,
                            (CASE WHEN ut.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status,
                            sum(If(fs.validation_status = 'Validated',1,0)) as validated_farmers,
                            IF( ut.image_upload_type != '0', CONCAT('".$base_url1."/', ut.uploaded_file), CONCAT('".$base_url."/', ut.uploaded_file)) as uploaded_file,
                            CONCAT_WS(
                                '\n', 
                                NULLIF(zms.zonal_manager_name, ''),
                                NULLIF(rms.regional_manager_name, ''),
                                NULLIF(ams.area_manager_name, ''),
                                NULLIF(srms.sales_regional_manager_name, ''),
                                NULLIF(sams.sales_area_manager_name, ''),
                                NULLIF(us.name, ''),
                                NULLIF(ddss.name, '')
                            ) AS uploaderName,
                            ut.latitude,
                            ut.longitude,
                            DATE_FORMAT(ut.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                            (CASE   WHEN ut.updated_at<>'0000-00-00 00:00:00' 
                                    THEN 
                                        DATE_FORMAT(ut.updated_at, '%Y-%m-%d, %h:%i %p')
                                    ELSE 
                                        '-' 
                            END),

                            (CASE   WHEN ut.closing_datetime<>'0000-00-00 00:00:00' 
                                    THEN 
                                        DATE_FORMAT(ut.closing_datetime, '%Y-%m-%d, %h:%i %p')
                                    ELSE 
                                        '-' 
                            END),
                            ut.ticket_id,
                            ut.village_id,
                            ut.ifc_farmer_count,
                            ut.activity_duration,
                            ut.total_activity_distance,
                            (CASE WHEN ut.dd_ticket_role='1' THEN 'Alone' ELSE 'With FL' END) as dd_ticket_role,
                            CONCAT_WS(
                                '\n', 
                                NULLIF(zms.ECN, ''),
                                NULLIF(rms.ECN, ''),
                                NULLIF(ams.ECN, ''),
                                NULLIF(srms.ECN, ''),
                                NULLIF(sams.ECN, ''),
                                NULLIF(us.ECN, ''),
                                NULLIF(ddss.ECN, '')
                            ) AS uploaderECN
                    FROM        tickets         ut
                    LEFT JOIN   farmers  fs  ON fs.ticket_id=ut.ticket_id 
                    LEFT JOIN   data_sources    ds  ON ut.data_source_id=ds.data_source_id
                    LEFT JOIN   states          s   ON ut.state_id=s.state_id
                    LEFT JOIN   districts       d   ON ut.district_id=d.district_id
                    LEFT JOIN   tehsils         t   ON ut.tehsil_id=t.tehsil_id
                    LEFT JOIN   products        p1  ON ut.product_1_id=p1.product_id
                    LEFT JOIN   crops           c1  ON ut.crop_1_id=c1.crop_id
                    LEFT JOIN   products        p2  ON ut.product_2_id=p2.product_id
                    LEFT JOIN   crops           c2  ON ut.crop_2_id=c2.crop_id
                    LEFT JOIN   products        p3  ON ut.product_3_id=p3.product_id
                    LEFT JOIN   crops           c3  ON ut.crop_3_id=c3.crop_id
                    LEFT JOIN   users                   u   ON ut.user_id=u.id AND ut.user_type='bdo'
                    LEFT JOIN   marketing_supervisor          ms  ON ut.user_id=ms.marketing_supervisor_id AND ut.user_type='marketing_supervisor'
                    LEFT JOIN   zonal_managers          zm  ON ut.user_id=zm.zonal_manager_id AND ut.user_type='zonal'
                    LEFT JOIN   regional_managers       rm  ON ut.user_id=rm.regional_manager_id AND ut.user_type='regional'
                    LEFT JOIN   area_managers           am  ON ut.user_id=am.area_manager_id AND ut.user_type='area'
                    LEFT JOIN   sales_regional_managers srm ON ut.user_id=srm.sales_regional_manager_id AND ut.user_type='sales_regional'
                    LEFT JOIN   sales_area_managers     sam ON ut.user_id=sam.sales_area_manager_id AND ut.user_type='sales_area'
                    LEFT JOIN   dhanuka_doctor dd ON ut.user_id=dd.dd_id AND ut.user_type='dhanuka_doctor'
                    LEFT JOIN   users       as            us   ON us.id=ut.uploadedBy AND (ut.uploadedByType='bdo' or ut.uploadedByType = 'SL-FL' or ut.uploadedByType = 'COUNTERPART-FL' or ut.uploadedByType = 'BDO-FL' or ut.uploadedByType = 'BD officer' or ut.uploadedByType = 'SALES-FL')
                    LEFT JOIN   zonal_managers    as      zms  ON zms.zonal_manager_id=ut.uploadedBy AND ut.uploadedByType='zonal'
                    LEFT JOIN   regional_managers  as     rms  ON rms.regional_manager_id=ut.uploadedBy AND ut.uploadedByType='regional'
                    LEFT JOIN   area_managers    as       ams  ON ams.area_manager_id=ut.uploadedBy AND ut.uploadedByType='area'
                    LEFT JOIN   sales_regional_managers as srms ON srms.sales_regional_manager_id=ut.uploadedBy AND ut.uploadedByType='sales_regional'
                    LEFT JOIN   sales_area_managers  as   sams ON sams.sales_area_manager_id=ut.uploadedBy AND ut.uploadedByType='sales_area'
                    LEFT JOIN   dhanuka_doctor as ddss ON ddss.dd_id=ut.uploadedBy AND (ut.uploadedByType='dhanuka_doctor' or ut.uploadedByType='Dhanuka Doctor')
                    LEFT JOIN   demo_request der ON ut.demo_request_id=der.demo_request_id
                    LEFT JOIN   dhanuka_doctor dds ON ut.assign_to_dd=dds.dd_id
                    LEFT JOIN   languages   l   ON ut.language_id=l.id
                    WHERE ".$search_query." and ut.status='2'
                    GROUP BY ut.ticket_id
                ) A,
                (select @a:=0) initvars";
                
            // echo $query; exit;
            $top_open_ticket    = collect(DB::select($query));
            // print_r($top_open_ticket); die();
            //$base_url       = url('/assets/atpfeedbackimage');
            $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/";
            foreach ($top_open_ticket as $key => $value) {
                $village_id = $value->village_id;
                if($village_id) {
                    $lat = $value->latitude;
                    $lon = $value->longitude;
                    if($lat && $lon) {
                        $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                        if($distanceData !==0) {
                            $expDistanceData = explode("~", $distanceData);
                            $top_open_ticket[$key]->isValidate = $expDistanceData[1];
                            $top_open_ticket[$key]->distance = $expDistanceData[0];
                        } else {
                            $top_open_ticket[$key]->isValidate = "No";
                            $top_open_ticket[$key]->distance = "0";
                        }
                    } else {
                        $top_open_ticket[$key]->isValidate = "No";
                        $top_open_ticket[$key]->distance = "0";
                    }
                } else {
                    $top_open_ticket[$key]->isValidate = "No";
                    $top_open_ticket[$key]->distance = "0";
                }
                //for supervisor
                $atpfeedback = "select user_type from self_participations as sp where sp.ticket_id = '".$top_open_ticket[$key]->ticket_id."' order by self_participation_id desc limit 0,1";
                $atpfeedback2  = collect(DB::select($atpfeedback));
                if (count($atpfeedback2) > 0) {
                    if ($atpfeedback2[0]->user_type == 'sales_area') {
                       $supervisorTable = 'sales_area_managers';
                       $name = 'su.sales_area_manager_name';
                       $id = 'su.sales_area_manager_id';
                       $Suser_type = "and sp.user_type = 'sales_area'";
                    }
                    elseif ($atpfeedback2[0]->user_type == 'sales_regional') {
                       $supervisorTable = 'sales_regional_managers';
                       $name = 'su.sales_regional_manager_name';
                       $id = 'su.sales_regional_manager_id';
                       $Suser_type = "and sp.user_type = 'sales_regional'";
                    }
                    elseif ($atpfeedback2[0]->user_type == 'zonal') {
                       $supervisorTable = 'zonal_managers';
                       $name = 'su.zonal_manager_name';
                       $id = 'su.zonal_manager_id';
                       $Suser_type = "and sp.user_type = 'zonal'";
                    }
                    elseif ($atpfeedback2[0]->user_type == 'regional') {
                       $supervisorTable = 'regional_managers';
                       $name = 'su.regional_manager_name';
                       $id = 'su.regional_manager_id';
                       $Suser_type = "and sp.user_type = 'regional'";
                    }
                    elseif ($atpfeedback2[0]->user_type == 'area') {
                       $supervisorTable = 'area_managers';
                       $name = 'su.area_manager_name';
                       $id = 'su.area_manager_id';
                       $Suser_type = "and sp.user_type = 'area'";
                    }
                    elseif ($atpfeedback2[0]->user_type == 'marketing_supervisor') {
                       $supervisorTable = 'marketing_supervisor';
                       $name = 'su.marketing_supervisor_name';
                       $id = 'su.marketing_supervisor_id';
                       $Suser_type = "and sp.user_type = 'marketing_supervisor'";
                    }
                    elseif ($atpfeedback2[0]->user_type == 'bdo') {
                       $supervisorTable = 'users';
                       $name = 'su.name';
                       $id = 'su.id';
                       $Suser_type = "and (sp.user_type = 'bdo' or sp.user_type = 'SL-FL' or sp.user_type = 'COUNTERPART-FL' or sp.user_type = 'BDO-FL' or sp.user_type = 'BD officer')";
                    } else {
                        $supervisorTable = 'marketing_supervisor';
                        $name = 'su.marketing_supervisor_name';
                        $id = 'su.marketing_supervisor_id';
                        $Suser_type = "and sp.user_type = 'marketing_supervisor'";
                    }

                    $atpfeedback1 = "select ".$name." as name, CONCAT('".$base_url."', sp.uploaded_file) as feedbackImage from self_participations as sp LEFT JOIN ".$supervisorTable." as su ON sp.user_id = ".$id." where sp.ticket_id = '".$top_open_ticket[$key]->ticket_id."' ".$Suser_type." order by self_participation_id desc limit 0,1";
                    $atpfeedbackSupervisor  = collect(DB::select($atpfeedback1));
                    if (count($atpfeedbackSupervisor) > 0) {
                        $top_open_ticket[$key]->supervisor_name = $atpfeedbackSupervisor[0]->name;
                        $top_open_ticket[$key]->supervisor_feedbackImage = $atpfeedbackSupervisor[0]->feedbackImage;
                    } else {
                        $top_open_ticket[$key]->supervisor_name = "";
                        $top_open_ticket[$key]->supervisor_feedbackImage = "";
                    }
                } else {
                    $top_open_ticket[$key]->supervisor_name = "";
                    $top_open_ticket[$key]->supervisor_feedbackImage = "";
                }
            }
            // print_r($top_open_ticket);die;
            $log_firstTime  = strtotime($log_start_time);
            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
            $log_time_diff  = $log_lastTime-$log_firstTime;
            $timeConsumed = round(microtime(true) - $curTime,3)*1000;
            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

            return Datatables::of($top_open_ticket)->make();
        }

        public function CalculateDistance($village_id,$lat2, $lon2) {
            $villageData = \DB::table('villages')->where(['village_id' => $village_id])->first();
            if ($villageData) {
                $lat1 = $villageData->latitude;
                $lon1 = $villageData->longitude;
                if($lat1 && $lon1) {
                    // echo "=>".$lat1."-".$lon1; exit();
                    $radius = ($villageData->radius) ? $villageData->radius : 0;
                    $rad = M_PI / 180;
                    $distance = acos(sin($lat2*$rad) * sin($lat1*$rad) + cos($lat2*$rad) * cos($lat1*$rad) * cos($lon2*$rad - $lon1*$rad)) * 6371;// Kilometers
                    $distancemtr = round($distance*1000,2);
                    if($distancemtr <= $radius) {
                        $validate = "Yes";
                    } else {
                        $validate = "No";
                    }
                    $response = array($distancemtr,$validate);
                    $impResponse = implode("~", $response);
                    // echo "=>".$distancemtr."~".$validate; exit();
                    return $impResponse;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }
        
        public function getTicketsList() {         
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.ticket.ticketslist');
        }   
        
        public function postAllticket(Request $request) {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            // sleep(1);
            // session_write_close(); // From here on out, concurrent requests are no longer blocked
            $request_data   = Input::all();
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "admin/tickets-list",
                'start_time' => $log_start_time,
                'request_data' => json_encode($request_data),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $tids = "";
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $tids = "";

            }
            $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
            if ($userData1) {
                //dd
               $ddData = DB::table('dhanuka_doctor')
                          ->where('zonal_manager_id',$userData1->zonal_manager_id)
                          ->where('status','1')
                          ->select('dd_id')
                          ->get();
                $ddresult = array();
                if (isset($ddData) && !empty($ddData))
                {
                    $ddDatass = json_decode(json_encode($ddData),true);
                    
                    foreach ($ddDatass as $key => $value) {
                        if (is_array($value))
                        {
                            $ddresult = array_merge($ddresult, array_flatten($value));
                        }
                        else
                        {
                          $ddresult[$key] = $value;
                        }
                    }
                    $ticket_ids1 = DB::table('tickets')->whereIn('user_id',$ddresult)->where('user_type','dhanuka_doctor')->select('ticket_id')->get();
                    $ticketresult = array();
                    if (count($ticket_ids1)>0) {
                        $ticketDatass = json_decode(json_encode($ticket_ids1),true);
                        foreach ($ticketDatass as $key => $value) {
                            if (is_array($value))
                            {
                                $ticketresult = array_merge($ticketresult, array_flatten($value));
                            }
                            else
                            {
                              $ticketresult[$key] = $value;
                            }
                        }
                    }
                }

                //bdo
                $bdoData = DB::table('users')
                          ->where('zonal_manager_id',$userData1->zonal_manager_id)
                          ->where('status','1')
                          ->select('id')
                          ->get();
                $bdoresult = array();
                if (isset($bdoData) && !empty($bdoData))
                {
                    $bdoDatass = json_decode(json_encode($bdoData),true);
                    
                    foreach ($bdoDatass as $key => $value) {
                        if (is_array($value))
                        {
                            $bdoresult = array_merge($bdoresult, array_flatten($value));
                        }
                        else
                        {
                          $bdoresult[$key] = $value;
                        }
                    }
                    $ticket_ids2 = DB::table('tickets')->whereIn('user_id',$bdoresult)->where('user_type','bdo')->select('ticket_id')->get();
                    $ticketresult1 = array();
                    if (count($ticket_ids2)>0) {
                        $ticketDatass = json_decode(json_encode($ticket_ids2),true);
                        
                        foreach ($ticketDatass as $key => $value) {
                            if (is_array($value))
                            {
                                $ticketresult1 = array_merge($ticketresult1, array_flatten($value));
                            }
                            else
                            {
                              $ticketresult1[$key] = $value;
                            }
                        }
                    }
                }
                
                if(count($ticketresult)>0 && count($ticketresult1)>0)
                {
                    $tdata = array_merge($ticketresult,$ticketresult1);
                    $ticketDataMerge = implode("','", $tdata);
                    $tids = "and ut.ticket_id in('".$ticketDataMerge."')";
                }  
                elseif (count($ticketresult)>0) {
                    $ticketDataMerge = implode("','", $ticketresult);
                    $tids = "and ut.ticket_id in('".$ticketDataMerge."')";
                }
                elseif (count($ticketresult1)>0) {
                    $ticketDataMerge = implode("','", $ticketresult1);
                    $tids = "and ut.ticket_id in ('".$ticketDataMerge."')";
                }
                else
                {
                    $tids = "";
                }
            }
            
            $columns        = array(  
                                    1 =>'A.ticket_no',
                                    2=> 'A.bdo',
                                    3=> 'A.data_source',
                                    4=> 'A.state',
                                    5=> 'A.district',
                                    6=> 'A.tehsil',
                                    7=> 'A.village',
                                    8=> 'A.product',
                                    9=> 'A.crop',
                                    10=> 'A.expected_pda',
                                    11=> 'A.expected_farmers',
                                    12=> 'A.farmer_count',
                                    13=> 'A.invited_farmer_count',
                                    14=> 'A.ticket_status'
                                );  
            
            //print_r(Request::all()); exit;
            $value          = Input::all();
            $limit          = $value['length'];
            $start          = $value['start'];
            $search_array   = $value['search'];     // value & regax
            $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
            
            $order_array    = $value['order'][0]['column'];
            $dir            = $value['order'][0]['dir'];
            
            $start_date     = $value['start_date'];
            $end_date       = $value['end_date'];

            $base_url       = url('/');
            $base_url1 = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";


            $datesearch     = '';
            $sorting='';
            $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
            $end_date       = isset($value['end_date']) ?  $value['end_date']:'';

            $search_query=" (1=1)  ";

            if($start_date!='' && $end_date!='') {
                $search_query.=" and DATE(ut.created_at) >= DATE('".$start_date."') AND DATE(ut.created_at) <= DATE('".$end_date."')";
            }elseif($start_date!='') {
                $search_query.= " and DATE(ut.created_at) >= DATE('".$start_date."')";
            }
            if(!empty($value['only_activity']) && ($value['only_activity']==1)) {
                $search_query .= " and ut.status != '0' and ut.ticket_status!='0'";
            }

            if(isset($search_array['value']) && $search_array['value']!=''){
                $search = $search_array['value']; 


                $search_query.=" and (
                ut.ticket_no     LIKE '%".$search."%'    OR
                u.name          LIKE '%".$search."%'    OR
                ds.data_source  LIKE '%".$search."%'    OR 
                s.state         LIKE '%".$search."%'    OR 
                d.district      LIKE '%".$search."%'    OR 
                t.tehsil        LIKE '%".$search."%'    OR 
                tr.territory    LIKE '%".$search."%'    OR 
                ut.village      LIKE '%".$search."%'    OR 

                p1.product      LIKE '%".$search."%'    OR 
                p2.product      LIKE '%".$search."%'    OR                                     
                p3.product      LIKE '%".$search."%'    OR 

                c1.crop         LIKE '%".$search."%'    OR 
                c2.crop         LIKE '%".$search."%'    OR                                     
                c3.crop         LIKE '%".$search."%'     
                ) ";                  
            }
                    
            $query              =   " 
                                    SELECT                                 
                                        @a:=@a+1  as serial_number,
                                        A.*                                
                                    FROM
                                        (
                                        SELECT
                                            ut.ticket_no,
                                            CONCAT_WS(
                                                        '\n', 
                                                        NULLIF(dds.ECN, '')
                                                    ) AS assign_ecn,
                                            CONCAT_WS(
                                                        '\n', 
                                                        NULLIF(dds.name, '')
                                                    ) AS assign_to_dd,
                                            CONCAT_WS(
                                                        '\n', 
                                                        NULLIF(ms.ECN, ''),
                                                        NULLIF(zm.ECN, ''),
                                                        NULLIF(rm.ECN, ''),
                                                        NULLIF(am.ECN, ''),
                                                        NULLIF(srm.ECN, ''),
                                                        NULLIF(sam.ECN, ''),
                                                        NULLIF(u.ECN, ''),
                                                        NULLIF(dd.ECN, '')
                                                    ) AS ecn,
                                            CONCAT_WS(
                                                        '\n', 
                                                        NULLIF(ms.marketing_supervisor_name, ''),
                                                        NULLIF(zm.zonal_manager_name, ''),
                                                        NULLIF(rm.regional_manager_name, ''),
                                                        NULLIF(am.area_manager_name, ''),
                                                        NULLIF(srm.sales_regional_manager_name, ''),
                                                        NULLIF(sam.sales_area_manager_name, ''),
                                                        NULLIF(u.name, ''),
                                                        NULLIF(dd.name, '')
                                                    ) AS bdo,
                                            UCASE(ut.user_type) AS user_type,
                                            l.language,
                                            ds.data_source,
                                            s.state,
                                            d.district,
                                            t.tehsil,
                                            ut.village,
                                            c1.crop     AS crop_1,
                                            p1.product  AS product_1, 
                                            c2.crop     AS crop_2,
                                            p2.product  AS product_2,
                                            c3.crop     AS crop_3,
                                            p3.product  AS product_3,
                                            ut.expected_pda,
                                            ut.expected_farmers,
                                            ut.farmer_count,
                                            ut.invited_farmer_count,
                                            count('th.atpfeedback_id') as total_highlight,
                                            CONCAT_WS(
                                                        '\n', 
                                                        NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_1, ''), NULLIF(ut.sp_number_1, '')), ''),
                                                        NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_2, ''), NULLIF(ut.sp_number_2, '')), ''),
                                                        NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_3, ''), NULLIF(ut.sp_number_3, '')), ''),
                                                        NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_4, ''), NULLIF(ut.sp_number_4, '')), ''),
                                                        NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_5, ''), NULLIF(ut.sp_number_5, '')), ''),
                                                        NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_6, ''), NULLIF(ut.sp_number_6, '')), '')
                                                    )  AS speacial_participant,                                        
                                            -- (CASE WHEN ut.demo_request_id='0' THEN 'NA' END)
                                            IF( ut.demo_request_id = '0', 'NA',  der.demo_code) as demo_no,
                                            (CASE WHEN ut.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status,
                                            
                                            (CASE WHEN ut.is_qualified='1' THEN 'Yes' ELSE 'No' END) as qualified_status,
                                            ut.validated_farmers,
                                            IF( ut.image_upload_type != '0', CONCAT('".$base_url1."/', ut.uploaded_file), CONCAT('".$base_url."/', ut.uploaded_file)) as uploaded_file,
                                            CONCAT_WS(
                                                '\n', 
                                                NULLIF(zms.zonal_manager_name, ''),
                                                NULLIF(rms.regional_manager_name, ''),
                                                NULLIF(ams.area_manager_name, ''),
                                                NULLIF(srms.sales_regional_manager_name, ''),
                                                NULLIF(sams.sales_area_manager_name, ''),
                                                NULLIF(us.name, ''),
                                                NULLIF(ddss.name, '')
                                            ) AS uploaderName,
                                            ut.latitude,
                                            ut.longitude,
                                            
                                            DATE_FORMAT(ut.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                                            (CASE   WHEN ut.updated_at<>'0000-00-00 00:00:00' 
                                                    THEN 
                                                        DATE_FORMAT(ut.updated_at, '%Y-%m-%d, %h:%i %p')
                                                    ELSE 
                                                        '-' 
                                            END),
                                            (CASE   WHEN ut.closing_datetime<>'0000-00-00 00:00:00' 
                                            THEN 
                                                DATE_FORMAT(ut.closing_datetime, '%Y-%m-%d, %h:%i %p')
                                            ELSE 
                                                '-' 
                                            END),
                                            ut.ticket_id,
                                            ut.village_id,
                                            ut.ifc_farmer_count,
                                            ut.activity_duration,
                                            ut.total_activity_distance,
                                            (CASE WHEN ut.dd_ticket_role='1' THEN 'Alone' ELSE 'With FL' END) as dd_ticket_role,
                                            tr.territory,
                                            CONCAT_WS(
                                                '\n', 
                                                NULLIF(zms.ECN, ''),
                                                NULLIF(rms.ECN, ''),
                                                NULLIF(ams.ECN, ''),
                                                NULLIF(srms.ECN, ''),
                                                NULLIF(sams.ECN, ''),
                                                NULLIF(us.ECN, ''),
                                                NULLIF(ddss.ECN, '')
                                            ) AS uploaderECN
                                        FROM        tickets         ut
                                        LEFT JOIN   data_sources    ds  ON ut.data_source_id=ds.data_source_id
                                        LEFT JOIN   states          s   ON ut.state_id=s.state_id
                                        LEFT JOIN   districts       d   ON ut.district_id=d.district_id
                                        LEFT JOIN   tehsils         t   ON ut.tehsil_id=t.tehsil_id
                                        LEFT JOIN   territories     tr  ON ut.territory_id=tr.territory_id
                                        LEFT JOIN   products        p1  ON ut.product_1_id=p1.product_id
                                        LEFT JOIN   crops           c1  ON ut.crop_1_id=c1.crop_id
                                        LEFT JOIN   products        p2  ON ut.product_2_id=p2.product_id
                                        LEFT JOIN   crops           c2  ON ut.crop_2_id=c2.crop_id
                                        LEFT JOIN   products        p3  ON ut.product_3_id=p3.product_id
                                        LEFT JOIN   crops           c3  ON ut.crop_3_id=c3.crop_id
                                        LEFT JOIN   ticket_highlights   th  ON ut.ticket_id=th.ticket_id
                                        LEFT JOIN   users           u   ON ut.user_id=u.id AND ut.user_type='bdo'
                                        LEFT JOIN   marketing_supervisor          ms  ON ut.user_id=ms.marketing_supervisor_id AND ut.user_type='marketing_supervisor'
                                        LEFT JOIN   zonal_managers          zm  ON ut.user_id=zm.zonal_manager_id AND ut.user_type='zonal'
                                        LEFT JOIN   regional_managers       rm  ON ut.user_id=rm.regional_manager_id AND ut.user_type='regional'
                                        LEFT JOIN   area_managers           am  ON ut.user_id=am.area_manager_id AND ut.user_type='area'
                                        LEFT JOIN   sales_regional_managers srm ON ut.user_id=srm.sales_regional_manager_id AND ut.user_type='sales_regional'
                                        LEFT JOIN   sales_area_managers     sam ON ut.user_id=sam.sales_area_manager_id AND ut.user_type='sales_area'
                                        LEFT JOIN   dhanuka_doctor dd ON ut.user_id=dd.dd_id AND ut.user_type='dhanuka_doctor'
                                        LEFT JOIN   users       as            us   ON us.id=ut.uploadedBy AND (ut.uploadedByType='bdo' or ut.uploadedByType = 'SL-FL' or ut.uploadedByType = 'COUNTERPART-FL' or ut.uploadedByType = 'BDO-FL' or ut.uploadedByType = 'BD officer' or ut.uploadedByType = 'SALES-FL')
                                        LEFT JOIN   zonal_managers    as      zms  ON zms.zonal_manager_id=ut.uploadedBy AND (ut.uploadedByType='zonal'  or ut.uploadedByType='ZONAL')
                                        LEFT JOIN   regional_managers  as     rms  ON rms.regional_manager_id=ut.uploadedBy AND (ut.uploadedByType='regional'  or ut.uploadedByType='REGIONAL')
                                        LEFT JOIN   area_managers    as       ams  ON ams.area_manager_id=ut.uploadedBy AND (ut.uploadedByType='area'  or ut.uploadedByType='AREA' )
                                        LEFT JOIN   sales_regional_managers as srms ON srms.sales_regional_manager_id=ut.uploadedBy AND (ut.uploadedByType='sales_regional'  or ut.uploadedByType='RSM' or ut.uploadedByType='rsm')
                                        LEFT JOIN   sales_area_managers  as   sams ON sams.sales_area_manager_id=ut.uploadedBy AND (ut.uploadedByType='sales_area' or ut.uploadedByType='ASM' or ut.uploadedByType='asm')
                                        LEFT JOIN   dhanuka_doctor as ddss ON ddss.dd_id=ut.uploadedBy AND (ut.uploadedByType='dhanuka_doctor' or ut.uploadedByType='Dhanuka Doctor')
                                        LEFT JOIN   dhanuka_doctor dds ON ut.assign_to_dd=dds.dd_id 
                                        LEFT JOIN   demo_request der ON ut.demo_request_id=der.demo_request_id
                                        LEFT JOIN   languages               l   ON ut.language_id=l.id
                                        WHERE 
                                            ".$search_query." ".$tids." 

                                        GROUP BY ut.ticket_id
                                        )   A,
                                        (select @a:=0) initvars";   
                   // echo $query;die;
                    $top_open_ticket    = collect(DB::select($query));
                    //$base_url       = url('/assets/atpfeedbackimage');
                    $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/";
                    foreach ($top_open_ticket as $key => $value) {
                        $village_id = $value->village_id;
                        if($village_id) {
                            $lat = $value->latitude;
                            $lon = $value->longitude;
                            if($lat && $lon) {
                                $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                                if($distanceData !==0) {
                                    $expDistanceData = explode("~", $distanceData);
                                    $top_open_ticket[$key]->isValidate = $expDistanceData[1];
                                    $top_open_ticket[$key]->distance = $expDistanceData[0];
                                } else {
                                    $top_open_ticket[$key]->isValidate = "No";
                                    $top_open_ticket[$key]->distance = "0";
                                }
                            } else {
                                $top_open_ticket[$key]->isValidate = "No";
                                $top_open_ticket[$key]->distance = "0";
                            }
                        } else {
                            $top_open_ticket[$key]->isValidate = "No";
                            $top_open_ticket[$key]->distance = "0";
                        }
                        //for supervisor
                        $atpfeedback = "select user_type from self_participations as sp where sp.ticket_id = '".$top_open_ticket[$key]->ticket_id."' and user_type = 'sales_area' order by self_participation_id desc limit 0,1";
                        $atpfeedback2  = collect(DB::select($atpfeedback));
                        if (count($atpfeedback2) > 0) {
                            if ($atpfeedback2[0]->user_type == 'sales_area') {
                               $supervisorTable = 'sales_area_managers'; 
                               $name = 'su.sales_area_manager_name';
                               $id = 'su.sales_area_manager_id';
                               $Suser_type = "and sp.user_type = 'sales_area'";
                            }
                            elseif ($atpfeedback2[0]->user_type == 'sales_regional') {
                               $supervisorTable = 'sales_regional_managers'; 
                               $name = 'su.sales_regional_manager_name';
                               $id = 'su.sales_regional_manager_id';
                               $Suser_type = "and sp.user_type = 'sales_regional'";
                            }
                            elseif ($atpfeedback2[0]->user_type == 'zonal') {
                               $supervisorTable = 'zonal_managers';
                               $name = 'su.zonal_manager_name'; 
                               $id = 'su.zonal_manager_id';
                               $Suser_type = "and sp.user_type = 'zonal'";
                            }
                            elseif ($atpfeedback2[0]->user_type == 'regional') {
                               $supervisorTable = 'regional_managers';
                               $name = 'su.regional_manager_name';
                               $id = 'su.regional_manager_id';
                               $Suser_type = "and sp.user_type = 'regional'";
                            }
                            elseif ($atpfeedback2[0]->user_type == 'area') {
                               $supervisorTable = 'area_managers';
                               $name = 'su.area_manager_name';
                               $id = 'su.area_manager_id';
                               $Suser_type = "and sp.user_type = 'area'";
                            }
                            elseif ($atpfeedback2[0]->user_type == 'marketing_supervisor') {
                               $supervisorTable = 'marketing_supervisor';
                               $name = 'su.marketing_supervisor_name';
                               $id = 'su.marketing_supervisor_id';
                               $Suser_type = "and sp.user_type = 'marketing_supervisor'";
                            }
                            elseif ($atpfeedback2[0]->user_type == 'bdo') {
                               $supervisorTable = 'users';
                               $name = 'su.name';
                               $id = 'su.id';
                               $Suser_type = "and (sp.user_type = 'bdo' or sp.user_type = 'SL-FL' or sp.user_type = 'COUNTERPART-FL' or sp.user_type = 'BDO-FL' or sp.user_type = 'BD officer')";
                            } else {
                                $supervisorTable = 'marketing_supervisor';
                                $name = 'su.marketing_supervisor_name';
                                $id = 'su.marketing_supervisor_id';
                                $Suser_type = "and sp.user_type = 'marketing_supervisor'";
                            }

                            $atpfeedback1 = "select ".$name." as name, CONCAT('".$base_url."', sp.uploaded_file) as feedbackImage from self_participations as sp LEFT JOIN ".$supervisorTable." as su ON sp.user_id = ".$id." where sp.ticket_id = '".$top_open_ticket[$key]->ticket_id."' ".$Suser_type." order by self_participation_id desc limit 0,1";
                            $atpfeedbackSupervisor  = collect(DB::select($atpfeedback1));
                            if (count($atpfeedbackSupervisor) > 0) {
                                $top_open_ticket[$key]->supervisor_name = $atpfeedbackSupervisor[0]->name;
                                $top_open_ticket[$key]->supervisor_feedbackImage = $atpfeedbackSupervisor[0]->feedbackImage;
                            } else {
                                $top_open_ticket[$key]->supervisor_name = "";
                                $top_open_ticket[$key]->supervisor_feedbackImage = "";
                            }
                        } else {
                            $top_open_ticket[$key]->supervisor_name = "";
                            $top_open_ticket[$key]->supervisor_feedbackImage = "";
                        }

                        $ticket_highlights = DB::table('ticket_highlights')
                                            ->where('ticket_id',$top_open_ticket[$key]->ticket_id)
                                            ->count();

                        if ($ticket_highlights>0) {
                            $top_open_ticket[$key]->total_highlight = $ticket_highlights;
                        } else {
                            $top_open_ticket[$key]->total_highlight = 0;
                        }
                    }
                    //echo "<pre>"; print_r($top_open_ticket); exit;
                    // $top_ticket_data = array();
                    // foreach ($top_open_ticket as $key => $value) {
                    //     $uploadedbydata = " SELECT  CONCAT_WS(
                    //                                             '\n', 
                    //                                             NULLIF(zm.zonal_manager_name, ''),
                    //                                             NULLIF(rm.regional_manager_name, ''),
                    //                                             NULLIF(am.area_manager_name, ''),
                    //                                             NULLIF(srm.sales_regional_manager_name, ''),
                    //                                             NULLIF(sam.sales_area_manager_name, ''),
                    //                                             NULLIF(u.name, ''),
                    //                                             NULLIF(dd.name, '')
                    //                                         ) AS UploadedByName,
                    //                                 UCASE(t.uploadedByType) AS uploadedByType
                    //                         FROM  tickets  t
                    //                         LEFT JOIN   users  u  ON t.uploadedBy=u.id AND t.uploadedByType='bdo'
                    //                         LEFT JOIN   zonal_managers  zm  ON t.uploadedBy=zm.zonal_manager_id AND t.uploadedByType='zonal'
                    //                         LEFT JOIN   regional_managers rm  ON t.uploadedBy=rm.regional_manager_id AND t.uploadedByType='regional'
                    //                         LEFT JOIN   area_managers  am  ON t.uploadedBy=am.area_manager_id AND t.uploadedByType='area'
                    //                         LEFT JOIN   sales_regional_managers srm ON t.uploadedBy=srm.sales_regional_manager_id AND t.uploadedByType='sales_regional'
                    //                         LEFT JOIN   sales_area_managers  sam ON t.uploadedBy=sam.sales_area_manager_id AND t.uploadedByType='sales_area'
                    //                         LEFT JOIN   dhanuka_doctor dd ON t.uploadedBy=dd.dd_id AND t.uploadedByType='dhanuka_doctor'
                    //                         WHERE t.ticket_no = '".$top_open_ticket[$key]->ticket_no."' 
                    //                         GROUP BY t.ticket_id ";
                    //     $top_ticket_data[]    = collect(DB::select($uploadedbydata));
                        
                    // }
                    // echo "<pre>";
                    // print_r($top_open_ticket);die;
                    $log_firstTime  = strtotime($log_start_time);
                    $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                    $log_time_diff  = $log_lastTime-$log_firstTime;
                    $timeConsumed = round(microtime(true) - $curTime,3)*1000;

                    $serialized_data= serialize($top_open_ticket);
                    $size           = strlen($serialized_data);
                    $size_in_kb     = ($size * 8 / 1000);   // Size in KB

                    DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'response_size'=> $size_in_kb,'updated_at'=>date('Y-m-d H:i:s')]);
                    return Datatables::of($top_open_ticket)->make(); 
        }

      
        
        public function postViewFarmers(Request $request) {        
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            //print_r($userData); exit;
            if ($userData) {
                $curTime        = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "admin/view-farmers",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);

                $my_data        = Input::all(); 
                $ticket_no      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
                $ticket_data    = Ticket::where('ticket_no', $ticket_no)->first();
                if($ticket_data) {
                    $ticket_id      = $ticket_data->ticket_id;
                    $farmer_list    = Farmer::where('ticket_id', $ticket_id)->where('ticket_status', '1')->groupBy('mobile')->orderBy('farmer_id','desc')->get()->toArray();
                    /*echo "<pre>";
                    print_r($farmer_list);
                    echo "</pre>";*/

                    //var_dump($farmer_list);
                    
                    if($farmer_list) {
                        echo    '<button name="export-farmers" id="export-farmers"  data-token="'.csrf_token().'" data-ticket="'.$ticket_id.'" >Export Farmer Data</button>';
                        echo    '<br>';
                        echo    '<ol>';
                        foreach($farmer_list as $farmer) {
                            echo "<li><span style='width: 50%;float: left;'>".$farmer['mobile']."</span> <span style='width: 50%;float: left;'> - ".date('M, d-Y, g:i:s A', strtotime($farmer['datetime']))."</span></li>";
                        }
                        echo    '</ol>';
                        echo    '<br>';
                        echo '<a style="text-align:center;" class="btn btn-info" href="javascript:void(0);" id="viewfarmers1" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers1" data-ticket="'.$ticket_id.'">Add Farmer</a>';
                        // echo    '<button name="add-farmers" id="add-farmers"  data-token="'.csrf_token().'" data-ticket="'.$ticket_id.'" >Add Farmer</button>';
                        //  echo    '<br>';
                        //  echo    '<input type="text" name="contact_number" value="Contact Number" id="ticketCNumber" style="display:none;">';
                    }
                    else {
                        echo "<h4>No Record Found !</h4>";
                          echo    '<br>';
                         echo '<a style="text-align:center;" class="btn btn-info" href="javascript:void(0);" id="viewfarmers1" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers1" data-ticket="'.$ticket_id.'">Add Farmer</a>';
                    }
                } else {
                    echo "<h4>No Record Found !</h4>";
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

            } else {
                echo "<h4>No Record Found !</h4>";
            }
            
        }


        public function postExportFarmers(Request $request) {          
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            //print_r($userData); exit;
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "admin/export-farmers",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);
            if ($userData) {
                $my_data        = Input::all(); 
                $ticket_no      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');
                $query          = " 
                                    SELECT 
                                        @a:=@a+1  as serial_number,
                                        A.*
                                    FROM
                                        (
                                        SELECT
                                            ut.ticket_no,
                                            f.mobile,
                                            f.registration_type,
                                            DATE_FORMAT(f.datetime, '%Y-%m-%d, %h:%i %p') as f_datetime_format,
                                            CONCAT_WS(
                                                        '\n', 
                                                        NULLIF(dds.name, '')
                                                    ) AS assign_to_dd,
                                            CONCAT_WS(
                                                        '\n', 
                                                        NULLIF(zm.zonal_manager_name, ''),
                                                        NULLIF(rm.regional_manager_name, ''),
                                                        NULLIF(am.area_manager_name, ''),
                                                        NULLIF(srm.sales_regional_manager_name, ''),
                                                        NULLIF(sam.sales_area_manager_name, ''),
                                                        NULLIF(u.name, ''),
                                                        NULLIF(dd.name, '')
                                                    ) AS bdo,
                                            UCASE(ut.user_type) AS user_type,
                                            ds.data_source,
                                            s.state,
                                            d.district,
                                            t.tehsil,
                                            ut.village,
                                            p1.product  AS product_1, 
                                            c1.crop     AS crop_1,
                                            p2.product  AS product_2,
                                            c2.crop     AS crop_2,
                                            p3.product  AS product_3,
                                            c3.crop     AS crop_3,
                                            DATE_FORMAT(ut.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format
                                            
                                            
                                            
                                        FROM        tickets         ut
                                        LEFT JOIN   data_sources    ds ON ut.data_source_id=ds.data_source_id
                                        LEFT JOIN   users                   u   ON ut.user_id=u.id AND ut.user_type='bdo'
                                        LEFT JOIN   zonal_managers          zm  ON ut.user_id=zm.zonal_manager_id AND ut.user_type='zonal'
                                        LEFT JOIN   regional_managers       rm  ON ut.user_id=rm.regional_manager_id AND ut.user_type='regional'
                                        LEFT JOIN   area_managers           am  ON ut.user_id=am.area_manager_id AND ut.user_type='area'
                                        LEFT JOIN   sales_regional_managers srm ON ut.user_id=srm.sales_regional_manager_id AND ut.user_type='sales_regional'
                                        LEFT JOIN   sales_area_managers     sam ON ut.user_id=sam.sales_area_manager_id AND ut.user_type='sales_area'
                                        LEFT JOIN   dhanuka_doctor           dd  ON ut.user_id=dd.dd_id AND ut.user_type='dhanuka_doctor'
                                        LEFT JOIN   states          s ON ut.state_id=s.state_id
                                        LEFT JOIN   districts       d ON ut.district_id=d.district_id
                                        LEFT JOIN   tehsils         t ON ut.tehsil_id=t.tehsil_id

                                        LEFT JOIN   products        p1 ON ut.product_1_id=p1.product_id
                                        LEFT JOIN   crops           c1 ON ut.crop_1_id=c1.crop_id
                                        LEFT JOIN   products        p2 ON ut.product_2_id=p2.product_id
                                        LEFT JOIN   crops           c2 ON ut.crop_2_id=c2.crop_id
                                        LEFT JOIN   products        p3 ON ut.product_3_id=p3.product_id
                                        LEFT JOIN   crops           c3 ON ut.crop_3_id=c3.crop_id
                                        
                                        LEFT JOIN   farmers         f  ON f.ticket_id=ut.ticket_id

                                        LEFT JOIN   users       as            us   ON us.id=ut.uploadedBy AND (ut.uploadedByType='bdo' or ut.uploadedByType = 'SL-FL' or ut.uploadedByType = 'COUNTERPART-FL' or ut.uploadedByType = 'BDO-FL' or ut.uploadedByType = 'BD officer' or ut.uploadedByType = 'SALES-FL')
                                        LEFT JOIN   zonal_managers    as      zms  ON zms.zonal_manager_id=ut.uploadedBy AND (ut.uploadedByType='zonal'  or ut.uploadedByType='ZONAL')
                                        LEFT JOIN   regional_managers  as     rms  ON rms.regional_manager_id=ut.uploadedBy AND (ut.uploadedByType='regional'  or ut.uploadedByType='REGIONAL')
                                        LEFT JOIN   area_managers    as       ams  ON ams.area_manager_id=ut.uploadedBy AND (ut.uploadedByType='area'  or ut.uploadedByType='AREA' )
                                        LEFT JOIN   sales_regional_managers as srms ON srms.sales_regional_manager_id=ut.uploadedBy AND (ut.uploadedByType='sales_regional'  or ut.uploadedByType='RSM' or ut.uploadedByType='rsm')
                                        LEFT JOIN   sales_area_managers  as   sams ON sams.sales_area_manager_id=ut.uploadedBy AND (ut.uploadedByType='sales_area' or ut.uploadedByType='ASM' or ut.uploadedByType='asm')
                                        LEFT JOIN   dhanuka_doctor as ddss ON ddss.dd_id=ut.uploadedBy AND ut.uploadedByType='dhanuka_doctor'
                                        

                                        LEFT JOIN   dhanuka_doctor dds ON ut.assign_to_dd=dds.dd_id 
                                        
                                        WHERE ut.ticket_id='".$ticket_no."'  AND f.ticket_status='1'
                                        GROUP BY f.mobile
                                        ) A,
                                        (select @a:=0) initvars" ;
            
                $ticket_data    = DB::select(DB::raw($query));
                $result         = array_map(function ($value) {
                                                    return (array)$value;
                                                }, $ticket_data);
            }        
            //print_r($result);
                    
            $ticket_column  = array(
                                    'Id',
                                    'Ticket',
                                    'Registered Farmer Mobile',
                                    'Registration Type',
                                    'Registration Date',
                                    'Assign To DD',
                                    'User (created By)',
                                    'Role (created By)',
                                    'Data Source',
                                    'State',
                                    'District',
                                    'Tehsil',
                                    'Village',
                                    'Product 1',
                                    'Crop 1',
                                    'Product 2',
                                    'Crop 2',
                                    'Product 3',
                                    'Crop 3',
                                    'Ticket Created At'
                                    );
            array_unshift($result, $ticket_column);
            
            
            $primarycolor = "rgb(255, 0, 0)";
            $myFile= Excel::create("filename", function($excel) use($result, $primarycolor) {
                        $excel->setTitle('title');
                        $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
                            $sheet->fromArray($result, null, 'A1', false, false);
                            $sheet->row(1, function($row) use ($primarycolor) {
                                $row->setFontSize(12)
                                    ->setFontWeight('bold')
                                    ->setBackground('#62bdb9')
                                    ->setAlignment('left')
                                    ->setFontColor('#000000');
                            });
                        });
                     });
                     
            $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
            $response   =  array(
                            'name' => "Farmer List.xlsx", //no extention needed
                            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                         );
            $log_firstTime  = strtotime($log_start_time);
            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
            $log_time_diff  = $log_lastTime-$log_firstTime;
            $timeConsumed = round(microtime(true) - $curTime,3)*1000;
            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

            return response()->json($response);
        }
        

        public function postViewinvitedfarmers(Request $request) {        
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            //print_r($userData); exit;
            if ($userData) {
                $my_data        = Input::all(); 
                $ticket_no      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
                $ticket_data    = Ticket::where('ticket_no', $ticket_no)->first();
                if($ticket_data) {
                    $plan_atp_id    = $ticket_data->plan_atp_id; 
                    $farmer_list    = \DB::table('invite_farmer')->where('plan_atp_id', $plan_atp_id)->groupBy('contact_number')->orderBy('invite_farmer_id','desc')->get();
                    //print_r($farmer_list);die;
                    //Farmer::where('ticket_id', $ticket_id)->where('ticket_status', '1')->groupBy('mobile')->orderBy('farmer_id','desc')->get()->toArray();

                    //var_dump($farmer_list);
                    
                    if($farmer_list) {
                        echo    '<button name="export-farmers" id="export-invited-farmers"  data-token="'.csrf_token().'" data-ticket="'.$ticket_data->ticket_id.'" >Export Farmer Data</button>';
                        echo    '<br>';
                        echo    '<ol>';
                        
                        foreach($farmer_list as $key => $farmer) {
                            echo "<li><span style='width: 50%;float: left;'>".$farmer_list[$key]->contact_number."</span> <span style='width: 50%;float: left;'> - ".date('M, d-Y, g:i:s A', strtotime($farmer_list[$key]->creation_time))."</span></li>";
                        }
                        echo    '</ol>';
                        echo    '<br>';
                        
                        // echo    '<button name="add-farmers" id="add-farmers"  data-token="'.csrf_token().'" data-ticket="'.$ticket_id.'" >Add Farmer</button>';
                        //  echo    '<br>';
                        //  echo    '<input type="text" name="contact_number" value="Contact Number" id="ticketCNumber" style="display:none;">';
                    }
                    else {
                        echo "<h4>No Record Found !</h4>";
                          echo    '<br>';
                         
                    }
                }
                else {
                    echo "<h4>No Record Found !</h4>";
                }            
            }
            else {
                echo "<h4>No Record Found !</h4>";
            }
            
        }

        public function postViewvalidatedfarmers(Request $request) {        
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            //print_r($userData); exit;
            if ($userData) {
                $my_data        = Input::all(); 
                $ticket_no      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
                $ticket_data    = Ticket::where('ticket_no', $ticket_no)->first();
                if($ticket_data) {
                    $ticket_id      = $ticket_data->ticket_id;
                    $farmer_list    = Farmer::where('ticket_id', $ticket_id)->where('validation_status', 'Validated')->groupBy('mobile')->orderBy('farmer_id','desc')->get()->toArray();
                    
                    //var_dump($farmer_list);
                    
                    if($farmer_list) 
                    {
                        echo    '<button name="export-farmers" id="export-farmers"  data-token="'.csrf_token().'" data-ticket="'.$ticket_id.'" >Export Farmer Data</button>';
                        echo    '<br>';
                        echo    '<ol>';
                        foreach($farmer_list as $farmer) {
                            echo "<li><span style='width: 50%;float: left;'>".$farmer['mobile']."</span> <span style='width: 50%;float: left;'> - ".date('M, d-Y, g:i:s A', strtotime($farmer['datetime']))."</span></li>";
                        }
                        echo    '</ol>';
                        echo    '<br>';
                        // echo '<a style="text-align:center;" class="btn btn-info" href="javascript:void(0);" id="viewfarmers1" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers2" data-ticket="'.$ticket_id.'">Add Farmer</a>';
                        // echo    '<button name="add-farmers" id="add-farmers"  data-token="'.csrf_token().'" data-ticket="'.$ticket_id.'" >Add Farmer</button>';
                        //  echo    '<br>';
                        //  echo    '<input type="text" name="contact_number" value="Contact Number" id="ticketCNumber" style="display:none;">';
                    }
                    else 
                    {
                        echo "<h4>No Record Found !</h4>";
                        echo    '<br>';
                        // echo '<a style="text-align:center;" class="btn btn-info" href="javascript:void(0);" id="viewfarmers1" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers2" data-ticket="'.$ticket_id.'">Add Farmer</a>';
                    }
                }
                else 
                {
                    echo "<h4>No Record Found !</h4>";
                }            
            }
            else {
                echo "<h4>No Record Found !</h4>";
            }
            
        }
        
        public function postViewvisits(Request $request) {        
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $my_data        = Input::all(); 
                $farmer_id      = (isset($my_data['_farmer'])?$my_data['_farmer']:'');            
                $visit_data    = DB::table('mark_visits')->where('farmer_id', $farmer_id)->orderBy('mark_visit_id','desc')->get();
                if($visit_data) {
                    echo    '<ol>';
                    foreach($visit_data as $visits) {
                        echo "<li><span style='width: 50%;float: left;'> - ".date('M, d-Y, g:i:s A', strtotime($visits->created_at))."</span></li>";
                    }
                    echo    '</ol>';
                    echo    '<br>';
                }
                else 
                {
                    echo "<h4>No Record Found !</h4>";
                    echo    '<br>';
                }            
            }
            else 
            {
                echo "<h4>No Record Found !</h4>";
                echo    '<br>';
            }
            
        }    
        
        public function getProductWiseFarmer() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.farmer.productwisefarmer');
        }

        public function postAllproductwisefarmer(Request $request) {
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
            
            $start_date     = $value['start_date'];
            $end_date       = $value['end_date'];

            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "admin/product-wise-farmer",
                'start_time' => $log_start_time,
                'request_data' => json_encode($value),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);
            
            if(empty($search_array['value']))
            {            
                $sorting        ='';            
                $datesearch     = '';

                if($start_date!='' && $end_date!='') {
                    $datesearch = "DATE(F.datetime) >= DATE('".$start_date."') AND DATE(F.datetime) <= DATE('".$end_date."')";
                }
                else {
                    $datesearch = "(1=1)";
                }

                $query              =   " 
                                    SELECT                                 
                                        @a:=@a+1  as serial_number,
                                        A.*                                
                                    FROM
                                        (
                                        SELECT 
                                            T.ticket_no, 
                                            CONCAT_WS(
                                                        '\n', 
                                                        NULLIF(zm.ECN, ''),
                                                        NULLIF(rm.ECN, ''),
                                                        NULLIF(am.ECN, ''),
                                                        NULLIF(srm.ECN, ''),
                                                        NULLIF(sam.ECN, ''),
                                                        NULLIF(u.ECN, ''),
                                                        NULLIF(dd.ECN, '')
                                                    ) AS ecn,
                                            CONCAT_WS(
                                                        '\n', 
                                                        NULLIF(zm.zonal_manager_name, ''),
                                                        NULLIF(rm.regional_manager_name, ''),
                                                        NULLIF(am.area_manager_name, ''),
                                                        NULLIF(srm.sales_regional_manager_name, ''),
                                                        NULLIF(sam.sales_area_manager_name, ''),
                                                        NULLIF(u.name, ''),
                                                        NULLIF(dd.name, '')
                                                    ) AS bdo,

                                            UCASE(F.user_type) AS user_type,
                                            
                                            L.language,
                                            
                                            P1.product AS p1_product, 
                                            C1.crop AS c1_crop,
                                            P2.product AS p2_product, 
                                            C2.crop AS c2_crop, 
                                            
                                            P3.product AS p3_product, 
                                            C3.crop AS c3_crop,                                                
                                            ST.state,
                                            DT.district,
                                            
                                            TS.tehsil,
                                            T.village,                                        
                                            F.mobile,
                                            IF( F.validation_status = '', 'Non-Contacted', F.validation_status )
                                            ,
                                            DATE_FORMAT(F.datetime, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                                            
                                            (CASE WHEN F.registration_type='manual' THEN 'Manual Entry' ELSE 'Missed Call' END),
                                            IF( F.farmer_name = 'NA', '', F.farmer_name )
                                        FROM        farmers		F

                                        LEFT JOIN   tickets         T   ON F.ticket_id=T.ticket_id

                                        LEFT JOIN   products    P1  ON T.product_1_id=P1.product_id
                                        LEFT JOIN   products    P2  ON T.product_2_id=P2.product_id
                                        LEFT JOIN   products    P3  ON T.product_3_id=P3.product_id 

                                        LEFT JOIN   crops           C1  ON T.crop_1_id=C1.crop_id
                                        LEFT JOIN   crops           C2  ON T.crop_2_id=C2.crop_id
                                        LEFT JOIN   crops           C3  ON T.crop_3_id=C3.crop_id 

                                        LEFT JOIN   states          ST  ON T.state_id=ST.state_id
                                        LEFT JOIN   districts       DT  ON T.district_id=DT.district_id
                                        LEFT JOIN   tehsils         TS  ON T.tehsil_id=TS.tehsil_id
       
                                        LEFT JOIN   users                   u   ON F.user_id=u.id AND F.user_type='bdo'
                                        LEFT JOIN   zonal_managers          zm  ON F.user_id=zm.zonal_manager_id AND F.user_type='zonal'
                                        LEFT JOIN   regional_managers       rm  ON F.user_id=rm.regional_manager_id AND F.user_type='regional'
                                        LEFT JOIN   area_managers           am  ON F.user_id=am.area_manager_id AND F.user_type='area'
                                        LEFT JOIN   sales_regional_managers srm ON F.user_id=srm.sales_regional_manager_id AND F.user_type='sales_regional'
                                        LEFT JOIN   sales_area_managers     sam ON F.user_id=sam.sales_area_manager_id AND F.user_type='sales_area'
                                        LEFT JOIN   dhanuka_doctor dd   ON F.user_id=dd.dd_id AND F.user_type='dhanuka_doctor'

                                        LEFT JOIN   languages       L   ON u.language_id=L.id
                                        
                                        WHERE 
                                            (F.ticket_status='1' AND F.farmer_type='1') AND
                                            ".$datesearch." 
                                        GROUP BY F.ticket_id, F.mobile
                                        )   A,
                                        (select @a:=0) initvars";

                                       // LIMIT ". $start." , ".$limit."
                // echo $query; exit;
                $product_farmer = collect(DB::select($query));
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                
                $serialized_data= serialize($product_farmer);
                $size           = strlen($serialized_data);
                $size_in_kb     = ($size * 8 / 1000);   // Size in KB
                
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed, 'response_size' => $size_in_kb, 'updated_at'=>date('Y-m-d H:i:s')]);
                return Datatables::of($product_farmer)->make();
            }
            else
            {
                $search         = $search_array['value']; 
                $sorting        = '';
                $datesearch     = '';
                $datesearch     = " (F.ticket_status='1' AND F.farmer_type='1') ";
                if($start_date!='' && $end_date!='') {
                    $datesearch.= " and  DATE(F.datetime) >= DATE('".$start_date."') AND DATE(F.datetime) <= DATE('".$end_date."')";
                }
                $query  =   " 
                            SELECT
                                @a:=@a+1  as serial_number,
                                A.*
                            FROM
                                (
                                SELECT 
                                    T.ticket_no, 
                                    CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.ECN, ''),
                                                NULLIF(rm.ECN, ''),
                                                NULLIF(am.ECN, ''),
                                                NULLIF(srm.ECN, ''),
                                                NULLIF(sam.ECN, ''),
                                                NULLIF(u.ECN, ''),
                                                NULLIF(dd.ECN, '')
                                            ) AS ecn,
                                    CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.zonal_manager_name, ''),
                                                NULLIF(rm.regional_manager_name, ''),
                                                NULLIF(am.area_manager_name, ''),
                                                NULLIF(srm.sales_regional_manager_name, ''),
                                                NULLIF(sam.sales_area_manager_name, ''),
                                                NULLIF(u.name, ''),
                                                NULLIF(dd.name, '')
                                            ) AS bdo,

                                    UCASE(F.user_type) AS user_type,
                                    L.language,

                                    P1.product AS p1_product, 
                                    C1.crop AS c1_crop,
                                    P2.product AS p2_product, 
                                    C2.crop AS c2_crop,

                                    P3.product AS p3_product, 
                                    C3.crop AS c3_crop,                                                
                                    ST.state,
                                    DT.district,

                                    TS.tehsil,
                                    T.village,
                                    F.mobile,
                                    IF( F.validation_status = '', 'Non-Contacted', F.validation_status )
                                            ,
                                    DATE_FORMAT(F.datetime, '%Y-%m-%d, %h:%i %p') as c_datetime_format,

                                    (CASE WHEN F.registration_type='manual' THEN 'Manual Entry' ELSE 'Missed Call' END)
                                FROM        farmers		F
                                LEFT JOIN   tickets         T   ON F.ticket_id=T.ticket_id

                                LEFT JOIN   products	P1  ON T.product_1_id=P1.product_id
                                LEFT JOIN   products	P2  ON T.product_2_id=P2.product_id
                                LEFT JOIN   products	P3  ON T.product_3_id=P3.product_id 

                                LEFT JOIN   crops           C1  ON T.crop_1_id=C1.crop_id
                                LEFT JOIN   crops           C2  ON T.crop_2_id=C2.crop_id
                                LEFT JOIN   crops           C3  ON T.crop_3_id=C3.crop_id 

                                LEFT JOIN   states          ST  ON T.state_id=ST.state_id
                                LEFT JOIN   districts       DT  ON T.district_id=DT.district_id
                                LEFT JOIN   tehsils         TS  ON T.tehsil_id=TS.tehsil_id                     
                                LEFT JOIN   users                   u   ON F.user_id=u.id AND F.user_type='bdo'
                                LEFT JOIN   zonal_managers          zm  ON F.user_id=zm.zonal_manager_id AND F.user_type='zonal'
                                LEFT JOIN   regional_managers       rm  ON F.user_id=rm.regional_manager_id AND F.user_type='regional'
                                LEFT JOIN   area_managers           am  ON F.user_id=am.area_manager_id AND F.user_type='area'
                                LEFT JOIN   sales_regional_managers srm ON F.user_id=srm.sales_regional_manager_id AND F.user_type='sales_regional'
                                LEFT JOIN   sales_area_managers     sam ON F.user_id=sam.sales_area_manager_id AND F.user_type='sales_area'
                                LEFT JOIN   dhanuka_doctor dd   ON F.user_id=dd.dd_id AND F.user_type='dhanuka_doctor'

                                LEFT JOIN   languages       L   ON u.language_id=L.id

                                WHERE 
                                    (".$datesearch.")       AND
                                    (
                                        P1.product                          LIKE '%".$search."%'    OR
                                        P2.product                          LIKE '%".$search."%'    OR
                                        P3.product                          LIKE '%".$search."%'    OR
                                        T.ticket_no                         LIKE '%".$search."%'    OR
                                        zm.zonal_manager_name               LIKE '%".$search."%'    OR
                                        rm.regional_manager_name            LIKE '%".$search."%'    OR
                                        am.area_manager_name                LIKE '%".$search."%'    OR
                                        srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                                        sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                                        u.name                              LIKE '%".$search."%'    OR
                                        F.mobile                            LIKE '%".$search."%'    
                                    )
                                    GROUP BY F.ticket_id, F.mobile
                                ) A,
                                (select @a:=0) initvars
                            ".$sorting ;
                //echo $query;die;
                $product_farmer = collect(DB::select($query));
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed   = round(microtime(true) - $curTime,3)*1000;

                $serialized_data= serialize($product_farmer);
                $size           = strlen($serialized_data);
                $size_in_kb     = ($size * 8 / 1000);   // Size in KB

                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'response_size' => $size_in_kb,'updated_at'=>date('Y-m-d H:i:s')]);
                return Datatables::of($product_farmer)->make();
            }
        }

        public function getPermissions() {
            $sess = \Session::get('adminid');
        
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $menu_list;
            $zonal_manager_list;
            $regional_manager_list;
            $area_manager_list;
            $bdo_list;
            
            $team_permission;
            /*
            TEAM
                Zonal, Regional, Area, BDO      MENU ITEMS
            INDIVIDUAL
                Zonal
                    Zonal Manager List          MENU ITEMS
                Regional
                    Regional Manager List       MENU ITEMS
                Area
                    Area Manager List           MENU ITEMS
                BDO
                    BDO Manager List            MENU ITEMS
             * 
            */
            
                    
            return View::make('backend.bdo.bdolist');
        }

        public function postExportAllFarmers(Request $request) {          
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
            if ($userData) {
                $my_data    =   Input::all(); 
                //dd($my_data);
                $start_date =   $my_data['startdate'];
                $end_date   =   $my_data['enddate'];
                $search     =   $my_data['search'];
                
                $search         = $my_data['search']; 
                $sorting        = '';
                $datesearch     = '';
                $datesearch     = " (F.ticket_status='1' AND F.farmer_type='1') ";
                if($start_date!='' && $end_date!='') {
                    $datesearch.= " and  DATE(F.datetime) >= DATE('".$start_date."') AND DATE(F.datetime) <= DATE('".$end_date."')";
                }
                $query  =   " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT 
                                    T.ticket_no, 
                                    CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.ECN, ''),
                                                NULLIF(rm.ECN, ''),
                                                NULLIF(am.ECN, ''),
                                                NULLIF(srm.ECN, ''),
                                                NULLIF(sam.ECN, ''),
                                                NULLIF(u.ECN, ''),
                                                NULLIF(dd.ECN, '')
                                            ) AS ecn,
                                    CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.zonal_manager_name, ''),
                                                NULLIF(rm.regional_manager_name, ''),
                                                NULLIF(am.area_manager_name, ''),
                                                NULLIF(srm.sales_regional_manager_name, ''),
                                                NULLIF(sam.sales_area_manager_name, ''),
                                                NULLIF(u.name, ''),
                                                NULLIF(dd.name, '')
                                            ) AS bdo,

                                    UCASE(F.user_type) AS user_type,
                                    L.language,

                                    P1.product AS p1_product, 
                                    C1.crop AS c1_crop,
                                    P2.product AS p2_product, 
                                    C2.crop AS c2_crop, 

                                    P3.product AS p3_product, 
                                    C3.crop AS c3_crop,                                                
                                    ST.state,
                                    DT.district,

                                    TS.tehsil,
                                    T.village,                                        
                                    F.mobile,
                                    IF( F.validation_status = '', 'Non-Contacted', F.validation_status )
                                            ,
                                    DATE_FORMAT(F.datetime, '%Y-%m-%d, %h:%i %p') as c_datetime_format,

                                    (CASE WHEN F.registration_type='manual' THEN 'Manual Entry' ELSE 'Missed Call' END)
                                FROM        farmers     F
                                LEFT JOIN   tickets         T   ON F.ticket_id=T.ticket_id

                                LEFT JOIN   products    P1  ON T.product_1_id=P1.product_id
                                LEFT JOIN   products    P2  ON T.product_2_id=P2.product_id
                                LEFT JOIN   products    P3  ON T.product_3_id=P3.product_id 

                                LEFT JOIN   crops           C1  ON T.crop_1_id=C1.crop_id
                                LEFT JOIN   crops           C2  ON T.crop_2_id=C2.crop_id
                                LEFT JOIN   crops           C3  ON T.crop_3_id=C3.crop_id 

                                LEFT JOIN   states          ST  ON T.state_id=ST.state_id
                                LEFT JOIN   districts       DT  ON T.district_id=DT.district_id
                                LEFT JOIN   tehsils         TS  ON T.tehsil_id=TS.tehsil_id

                                                                                                               
                                LEFT JOIN   users                   u   ON F.user_id=u.id AND F.user_type='bdo'
                                LEFT JOIN   zonal_managers          zm  ON F.user_id=zm.zonal_manager_id AND F.user_type='zonal'
                                LEFT JOIN   regional_managers       rm  ON F.user_id=rm.regional_manager_id AND F.user_type='regional'
                                LEFT JOIN   area_managers           am  ON F.user_id=am.area_manager_id AND F.user_type='area'
                                LEFT JOIN   sales_regional_managers srm ON F.user_id=srm.sales_regional_manager_id AND F.user_type='sales_regional'
                                LEFT JOIN   sales_area_managers     sam ON F.user_id=sam.sales_area_manager_id AND F.user_type='sales_area'
                                LEFT JOIN   dhanuka_doctor dd   ON F.user_id=dd.dd_id AND F.user_type='dhanuka_doctor'

                                LEFT JOIN   languages       L   ON u.language_id=L.id

                                WHERE 
                                       
                                    (".$datesearch.")       AND
                                    (
                                        P1.product                          LIKE '%".$search."%'    OR
                                        P2.product                          LIKE '%".$search."%'    OR
                                        P3.product                          LIKE '%".$search."%'    OR
                                        T.ticket_no                         LIKE '%".$search."%'    OR
                                        zm.zonal_manager_name               LIKE '%".$search."%'    OR
                                        rm.regional_manager_name            LIKE '%".$search."%'    OR
                                        am.area_manager_name                LIKE '%".$search."%'    OR
                                        srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                                        sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                                        u.name                              LIKE '%".$search."%'    OR
                                        F.mobile                            LIKE '%".$search."%'    
                                    ) 
                                    GROUP BY F.mobile, F.ticket_id          
                                ) A,
                                (select @a:=0) initvars
                            ".$sorting ;
                        // Log::info("postexportallfarmers query=>");
                        // Log::info($query);
                

                $product_farmer = DB::select(DB::raw($query));
                $result         = array_map(function ($value) {
                                                    return (array)$value;
                                                }, $product_farmer);
                $ticket_column  = array(
                                    'Id',
                                    'Ticket',
                                    'ECN',
                                    'Name',
                                    'Role',
                                    'Language',
                                    'Product 1',
                                    'Crop 1',
                                    'Product 2',
                                    'Crop 2',
                                    'Product 3',
                                    'Crop 3',
                                    'State',
                                    'District',
                                    'Tehsil',
                                    'Village',
                                    'Registered Farmer Mobile',
                                    'validation status',
                                    'Registration Date',
                                    'Registration Type'
                                    );
                array_unshift($result, $ticket_column);
            
            
                $primarycolor   =   "rgb(255, 0, 0)";
                $myFile         =   Excel::create("filename", function($excel) use($result, $primarycolor) {
                                        $excel->setTitle('title');
                                        $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
                                            $sheet->fromArray($result, null, 'A1', false, false);
                                            $sheet->row(1, function($row) use ($primarycolor) {
                                                $row->setFontSize(12)
                                                    ->setFontWeight('bold')
                                                    ->setBackground('#62bdb9')
                                                    ->setAlignment('left')
                                                    ->setFontColor('#000000');
                                            });
                                        });
                                     });
                     
                $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
                $response   =  array(
                                'name' => "Farmer Registration List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                             );
                return response()->json($response);
            }
        }

        public function postExportAllInvitedFarmerList(Request $request) {          
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
            if ($userData) {
                $my_data    =   Input::all(); 
                //dd($my_data);
                $start_date = (!empty($my_data['startdate'])) ? $my_data['startdate'] : '';
                $end_date   = (!empty($my_data['enddate'])) ? $my_data['enddate'] : '';
                $search     = (!empty($my_data['search'])) ? $my_data['search'] : '';

                $datesearch     = '';
                if($start_date!='' && $end_date!='') {
                    $datesearch = "DATE(F.creation_time) >= DATE('".$start_date."') AND DATE(F.creation_time) <= DATE('".$end_date."')";
                }
                else {
                    $datesearch = "(1=1)";
                }
                
                $dataSearch = '';
                if($search!='') {
                    $dataSearch =   "
                                    AND
                                    (
                                        P1.product                          LIKE '%".$search."%'    OR
                                        P2.product                          LIKE '%".$search."%'    OR
                                        P3.product                          LIKE '%".$search."%'    OR
                                        T.ticket_no                         LIKE '%".$search."%'    OR
                                        zm.zonal_manager_name               LIKE '%".$search."%'    OR
                                        rm.regional_manager_name            LIKE '%".$search."%'    OR
                                        am.area_manager_name                LIKE '%".$search."%'    OR
                                        srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                                        sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                                        u.name                              LIKE '%".$search."%'    OR
                                        F.farmer_name                       LIKE '%".$search."%'    OR
                                        F.contact_number                    LIKE '%".$search."%'    
                                    )             
                                    ";
                }
                else {
                    $dataSearch = " AND (1=1)";
                }
                
                $query      =   " 
                                SELECT                                 
                                    @a:=@a+1  as serial_number,
                                    A.*                                
                                FROM
                                    (
                                    SELECT 
                                        T.ticket_no,
                                        IF( ddss.ECN = '', CONCAT_WS(
                                            '\n', 
                                            NULLIF(U.ECN, ''),
                                            NULLIF(dd.ECN, '')
                                        ),  ddss.ECN) as ECN,
                                        IF( ddss.name = '', CONCAT_WS(
                                            '\n', 
                                            NULLIF(U.name, ''),
                                            NULLIF(dd.name, '')
                                        ),  ddss.name) AS name,

                                        IF( ddss.ECN = '', UCASE(pa.user_type), 'DD') AS role,
                                        L.language,

                                        P1.product AS p1_product, 
                                        C1.crop AS c1_crop,
                                        P2.product AS p2_product, 
                                        C2.crop AS c2_crop, 

                                        P3.product AS p3_product, 
                                        C3.crop AS c3_crop,                                                
                                        ST.state,
                                        DT.district,

                                        TS.tehsil,
                                        V.village,                                        
                                        F.contact_number,
                                        F.farmer_name,
                                        -- (CASE WHEN fa.validation_status='Validated' THEN 'YES' ELSE 'NO' END) as Attended,
                                        DATE_FORMAT(F.creation_time, '%Y-%m-%d, %h:%i %p') as Invited_Date
                                    FROM        invite_farmer     F
                                    LEFT JOIN   plan_atp    pa  ON F.plan_atp_id=pa.plan_atp_id
                                    -- LEFT JOIN   farmers        fa  ON fa.mobile LIKE CONCAT('%', F.contact_number, '%')
                                    LEFT JOIN   tickets     T   ON pa.plan_atp_id=T.plan_atp_id

                                    LEFT JOIN   users       U   ON U.id=pa.created_by_user_id AND pa.user_type='bdo'
                                    LEFT JOIN   dhanuka_doctor   dd  ON pa.created_by_user_id=dd.dd_id AND pa.user_type='dhanuka_doctor'

                                    LEFT JOIN   dhanuka_doctor ddss   ON pa.dd_id=ddss.dd_id
                                    
                                    LEFT JOIN   products    P1  ON pa.product_id=P1.product_id
                                    LEFT JOIN   products    P2  ON pa.product_id_2=P2.product_id
                                    LEFT JOIN   products    P3  ON pa.product_id_3=P3.product_id 

                                    LEFT JOIN   crops       C1  ON pa.crop_id=C1.crop_id
                                    LEFT JOIN   crops       C2  ON pa.other_crop_1=C2.crop_id
                                    LEFT JOIN   crops       C3  ON pa.other_crop_2=C3.crop_id 
                                                                        
                                    LEFT JOIN   states      ST  ON pa.state_id=ST.state_id
                                    LEFT JOIN   districts   DT  ON pa.district_id=DT.district_id
                                    LEFT JOIN   tehsils     TS  ON pa.tehsil_id=TS.tehsil_id
                                    LEFT JOIN   villages    V   ON pa.village_id=V.village_id             

                                    LEFT JOIN   languages   L   ON U.language_id=L.id
                                    WHERE 
                                        (1=1) AND
                                        ".$datesearch."
                                        ".$dataSearch."
                                    GROUP BY F.contact_number
                                    )   A,
                                    (select @a:=0) initvars";
                // echo $query; exit;
            
                $product_farmer = DB::select(DB::raw($query));
                $result         = array_map(function ($value) {
                                                    return (array)$value;
                                                }, $product_farmer);
                $ticket_column  = array(
                                    'Id',
                                    'Ticket',
                                    'ECN',
                                    'Name',
                                    'Role',
                                    'Language',
                                    'Product 1',
                                    'Crop 1',
                                    'Product 2',
                                    'Crop 2',
                                    'Product 3',
                                    'Crop 3',
                                    'State',
                                    'District',
                                    'Tehsil',
                                    'Village',
                                    'Invited Farmer Mobile',
                                    'Invited Farmer Name',
                                    // 'Attended',
                                    'Invited_Date',
                                    );
                array_unshift($result, $ticket_column);
            
            
                $primarycolor   =   "rgb(255, 0, 0)";
                $myFile         =   Excel::create("filename", function($excel) use($result, $primarycolor) {
                                        $excel->setTitle('title');
                                        $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
                                            $sheet->fromArray($result, null, 'A1', false, false);
                                            $sheet->row(1, function($row) use ($primarycolor) {
                                                $row->setFontSize(12)
                                                    ->setFontWeight('bold')
                                                    ->setBackground('#62bdb9')
                                                    ->setAlignment('left')
                                                    ->setFontColor('#000000');
                                            });
                                        });
                                     });
                     
                $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
                $response   =  array(
                                'name' => "Invited Farmer List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                             );
                return response()->json($response);
            }
        }

        public function postExportAllInvitedFarmers(Request $request) {          
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            //print_r($userData); exit;
            if ($userData) {
                $my_data        = Input::all(); 
                $ticket_no      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');
                $query      =   " 
                                SELECT                                 
                                    @a:=@a+1  as serial_number,
                                    A.*                                
                                FROM
                                    (
                                    SELECT 
                                        T.ticket_no,
                                        IF( ddss.ECN = '', CONCAT_WS(
                                            '\n', 
                                            NULLIF(U.ECN, ''),
                                            NULLIF(dd.ECN, '')
                                        ),  ddss.ECN) as ECN,
                                        IF( ddss.name = '', CONCAT_WS(
                                            '\n', 
                                            NULLIF(U.name, ''),
                                            NULLIF(dd.name, '')
                                        ),  ddss.name) AS name,

                                        IF( ddss.ECN = '', UCASE(pa.user_type), 'DD') AS role,
                                        L.language,

                                        P1.product AS p1_product, 
                                        C1.crop AS c1_crop,
                                        P2.product AS p2_product, 
                                        C2.crop AS c2_crop, 

                                        P3.product AS p3_product, 
                                        C3.crop AS c3_crop,                                                
                                        ST.state,
                                        DT.district,

                                        TS.tehsil,
                                        V.village,                                        
                                        F.contact_number,
                                        F.farmer_name,
                                        -- (CASE WHEN fa.validation_status='Validated' THEN 'YES' ELSE 'NO' END) as Attended,
                                        DATE_FORMAT(F.creation_time, '%Y-%m-%d, %h:%i %p') as Invited_Date
                                    FROM        invite_farmer     F
                                    LEFT JOIN   plan_atp    pa  ON F.plan_atp_id=pa.plan_atp_id
                                    -- LEFT JOIN   farmers        fa  ON fa.mobile LIKE CONCAT('%', F.contact_number, '%')
                                    LEFT JOIN   tickets     T   ON pa.plan_atp_id=T.plan_atp_id

                                    LEFT JOIN   users       U   ON U.id=pa.created_by_user_id AND pa.user_type='bdo'
                                    LEFT JOIN   dhanuka_doctor   dd  ON pa.created_by_user_id=dd.dd_id AND pa.user_type='dhanuka_doctor'

                                    LEFT JOIN   dhanuka_doctor ddss   ON pa.dd_id=ddss.dd_id
                                    
                                    LEFT JOIN   products    P1  ON pa.product_id=P1.product_id
                                    LEFT JOIN   products    P2  ON pa.product_id_2=P2.product_id
                                    LEFT JOIN   products    P3  ON pa.product_id_3=P3.product_id 

                                    LEFT JOIN   crops       C1  ON pa.crop_id=C1.crop_id
                                    LEFT JOIN   crops       C2  ON pa.other_crop_1=C2.crop_id
                                    LEFT JOIN   crops       C3  ON pa.other_crop_2=C3.crop_id 
                                                                        
                                    LEFT JOIN   states      ST  ON pa.state_id=ST.state_id
                                    LEFT JOIN   districts   DT  ON pa.district_id=DT.district_id
                                    LEFT JOIN   tehsils     TS  ON pa.tehsil_id=TS.tehsil_id
                                    LEFT JOIN   villages    V   ON pa.village_id=V.village_id             

                                    LEFT JOIN   languages   L   ON U.language_id=L.id
                                    WHERE T.ticket_id='".$ticket_no."'
                                    GROUP BY F.contact_number
                                    )   A,
                                    (select @a:=0) initvars";
                // echo $query; exit;
            
                $ticket_data    = DB::select(DB::raw($query));
                $result         = array_map(function ($value) {
                                                    return (array)$value;
                                                }, $ticket_data);
            }        
            //print_r($result);
                    
            $ticket_column  = array(
                                    'Id',
                                    'Ticket',
                                    'ECN',
                                    'Name',
                                    'Role',
                                    'Language',
                                    'Product 1',
                                    'Crop 1',
                                    'Product 2',
                                    'Crop 2',
                                    'Product 3',
                                    'Crop 3',
                                    'State',
                                    'District',
                                    'Tehsil',
                                    'Village',
                                    'Invited Farmer Mobile',
                                    'Invited Farmer Name',
                                    // 'Attended',
                                    'Invited_Date',
                                    );
            array_unshift($result, $ticket_column);
            
            
            $primarycolor = "rgb(255, 0, 0)";
            $myFile= Excel::create("filename", function($excel) use($result, $primarycolor) {
                        $excel->setTitle('title');
                        $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
                            $sheet->fromArray($result, null, 'A1', false, false);
                            $sheet->row(1, function($row) use ($primarycolor) {
                                $row->setFontSize(12)
                                    ->setFontWeight('bold')
                                    ->setBackground('#62bdb9')
                                    ->setAlignment('left')
                                    ->setFontColor('#000000');
                            });
                        });
                     });
                     
            $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
            $response   =  array(
                            'name' => "Farmer List.xlsx", //no extention needed
                            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                         );
            return response()->json($response);
        }

        public function postExportAllTickets(Request $request) {          
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
            if ($userData) {
                $my_data    =   Input::all(); 
                //dd($my_data);
                $start_date =   $my_data['startdate'];
                $end_date   =   $my_data['enddate'];
                $search     =   $my_data['search'];
                
                $datesearch  = '';
                if($start_date!='' && $end_date!='') {
                    $datesearch = "DATE(ut.created_at) >= DATE('".$start_date."') AND DATE(ut.created_at) <= DATE('".$end_date."')";
                }
                else {
                    $datesearch = "(1=1)";
                }
                
                $dataSearch = '';
                if($search!='') {
                    $dataSearch =   "
                                    AND
                                    (
                                        ut.ticket_no     LIKE '%".$search."%'   OR
                                        u.name          LIKE '%".$search."%'    OR
                                        ds.data_source  LIKE '%".$search."%'    OR 
                                        s.state         LIKE '%".$search."%'    OR 
                                        d.district      LIKE '%".$search."%'    OR 
                                        t.tehsil        LIKE '%".$search."%'    OR 
                                        tr.territory    LIKE '%".$search."%'    OR 
                                        ut.village      LIKE '%".$search."%'    OR 

                                        p1.product      LIKE '%".$search."%'    OR 
                                        p2.product      LIKE '%".$search."%'    OR                                     
                                        p3.product      LIKE '%".$search."%'    OR 

                                        c1.crop         LIKE '%".$search."%'    OR 
                                        c2.crop         LIKE '%".$search."%'    OR                                     
                                        c3.crop         LIKE '%".$search."%'
                                    )
                                    ";
                }
                else {
                    $dataSearch = " AND (1=1)";
                }
                if(!empty($my_data['only_activity']) && ($my_data['only_activity']==1)) {
                    $dataSearch .= " AND ut.status != '0' AND ut.ticket_status!='0'";
                }
                //$base_url   = url('/');
                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                $query      =   " 
                                SELECT                                 
                                    @a:=@a+1  as serial_number,
                                    A.*                                
                                FROM
                                    (
                                    SELECT
                                        ut.ticket_no,
                                        CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.ECN, ''),
                                                    NULLIF(rm.ECN, ''),
                                                    NULLIF(am.ECN, ''),
                                                    NULLIF(srm.ECN, ''),
                                                    NULLIF(sam.ECN, ''),
                                                    NULLIF(u.ECN, ''),
                                                    NULLIF(dd.ECN, '')
                                                ) AS ecn,
                                        CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.zonal_manager_name, ''),
                                                    NULLIF(rm.regional_manager_name, ''),
                                                    NULLIF(am.area_manager_name, ''),
                                                    NULLIF(srm.sales_regional_manager_name, ''),
                                                    NULLIF(sam.sales_area_manager_name, ''),
                                                    NULLIF(u.name, ''),
                                                    NULLIF(dd.name, '')
                                                ) AS bdo,
                                        UCASE(ut.user_type) AS user_type,
                                        l.language,
                                        ds.data_source,
                                        s.state,
                                        d.district,
                                        t.tehsil,
                                        tr.territory,
                                        ut.village,
                                        p1.product  AS product_1, 
                                        c1.crop     AS crop_1,
                                        p2.product  AS product_2,
                                        c2.crop     AS crop_2,
                                        p3.product  AS product_3,
                                        c3.crop     AS crop_3,
                                        ut.expected_pda,
                                        ut.expected_farmers,
                                        ut.farmer_count,
                                        ut.invited_farmer_count,
                                        CONCAT_WS(
                                                    '#', 
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_1, ''), NULLIF(ut.sp_number_1, '')), ''),
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_2, ''), NULLIF(ut.sp_number_2, '')), ''),
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_3, ''), NULLIF(ut.sp_number_3, '')), ''),
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_4, ''), NULLIF(ut.sp_number_4, '')), ''),
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_5, ''), NULLIF(ut.sp_number_5, '')), ''),
                                                    NULLIF(CONCAT_WS(' - ', NULLIF(ut.sp_name_6, ''), NULLIF(ut.sp_number_6, '')), '')
                                                )  AS speacial_participant,                                        
                                        IF( ut.demo_request_id = '0', 'NA',  der.demo_code) as demo_no,
                                        (CASE WHEN ut.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status,
                                        
                                        (CASE WHEN ut.is_qualified='1' THEN 'Yes' ELSE 'No' END) as qualified_status,
                                        ut.validated_farmers,
                                            
                                        DATE_FORMAT(ut.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                                        (CASE   WHEN ut.updated_at<>'0000-00-00 00:00:00' 
                                                THEN 
                                                    DATE_FORMAT(ut.updated_at, '%Y-%m-%d, %h:%i %p')
                                                ELSE 
                                                    '-' 
                                        END),
                                            CONCAT('".$base_url."/', ut.uploaded_file),
                                            ut.latitude,
                                            ut.longitude,
                                            (CASE WHEN ut.dd_ticket_role='1' THEN 'Alone' ELSE 'With FL' END) as dd_ticket_role
                                    FROM        tickets         ut
                                    LEFT JOIN   data_sources    ds  ON ut.data_source_id=ds.data_source_id
                                    LEFT JOIN   states          s   ON ut.state_id=s.state_id
                                    LEFT JOIN   districts       d   ON ut.district_id=d.district_id
                                    LEFT JOIN   tehsils         t   ON ut.tehsil_id=t.tehsil_id
                                    LEFT JOIN   territories     tr  ON ut.territory_id=tr.territory_id

                                    LEFT JOIN   products        p1  ON ut.product_1_id=p1.product_id
                                    LEFT JOIN   crops           c1  ON ut.crop_1_id=c1.crop_id
                                    LEFT JOIN   products        p2  ON ut.product_2_id=p2.product_id
                                    LEFT JOIN   crops           c2  ON ut.crop_2_id=c2.crop_id
                                    LEFT JOIN   products        p3  ON ut.product_3_id=p3.product_id
                                    LEFT JOIN   crops           c3  ON ut.crop_3_id=c3.crop_id

                                    LEFT JOIN   users                   u   ON ut.user_id=u.id AND ut.user_type='bdo'
                                    LEFT JOIN   zonal_managers          zm  ON ut.user_id=zm.zonal_manager_id AND ut.user_type='zonal'
                                    LEFT JOIN   regional_managers       rm  ON ut.user_id=rm.regional_manager_id AND ut.user_type='regional'
                                    LEFT JOIN   area_managers           am  ON ut.user_id=am.area_manager_id AND ut.user_type='area'
                                    LEFT JOIN   sales_regional_managers srm ON ut.user_id=srm.sales_regional_manager_id AND ut.user_type='sales_regional'
                                    LEFT JOIN   sales_area_managers     sam ON ut.user_id=sam.sales_area_manager_id AND ut.user_type='sales_area'
                                    LEFT JOIN   dhanuka_doctor          dd  ON ut.user_id=dd.dd_id AND ut.user_type='dhanuka_doctor'
                                    LEFT JOIN   dhanuka_doctor as ddss ON ddss.dd_id=ut.uploadedBy AND (ut.uploadedByType='dhanuka_doctor' or ut.uploadedByType='Dhanuka Doctor')
                                    LEFT JOIN   demo_request der ON ut.demo_request_id=der.demo_request_id

                                    LEFT JOIN   languages               l   ON u.language_id=l.id
                                    WHERE 
                                        ".$datesearch."
                                        ".$dataSearch."
                                    GROUP BY ut.ticket_id
                                    ORDER BY ut.ticket_id DESC
                                    )   A,
                                    (select @a:=0) initvars";
                //echo $query; exit;
            
                $product_farmer = DB::select(DB::raw($query));
                $result         = array_map(function ($value) {
                                                    return (array)$value;
                                                }, $product_farmer);
                $ticket_column  = array(                
                                        'Id',
                                        'Ticket',
                                        'ECN',
                                        'User',
                                        'Role',
                                        'Language',
                                        'Data Source',
                                        'State',
                                        'District',
                                        'Tehsil',
                                        'Territory',
                                        'Village',
                                        'Product 1',
                                        'Crop 1',
                                        'Product 2',
                                        'Crop 2',
                                        'Product 3',
                                        'Crop 3',
                                        'Expected PDA',
                                        'Expected Farmers',
                                        'Farmer Count',
                                        'Invited Farmer Count',
                                        'Speacial Participant',
                                        'Demo Number',
                                        'Ticket Status',
                                        'Qualified Status',
                                        'Validated Farmers',
                                        'Registration Date',
                                        'Modification Date',
                                        'Uploaded File',
                                        'Latitude',
                                        'Longitude',
                                        'DD Ticket Role'
                                        );
                array_unshift($result, $ticket_column);
            
                
                $primarycolor   =   "rgb(255, 0, 0)";
                $myFile         =   Excel::create("filename", function($excel) use($result, $primarycolor) {
                                        $excel->setTitle('title');
                                        $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
                                            $sheet->fromArray($result, null, 'A1', false, false);
                                            $sheet->row(1, function($row) use ($primarycolor) {
                                                $row->setFontSize(12)
                                                    ->setFontWeight('bold')
                                                    ->setBackground('#62bdb9')
                                                    ->setAlignment('left')
                                                    ->setFontColor('#000000');
                                            });
                                        });
                                    });
                     
                $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
                $response   = array(
                                    'name' => "Ticket Registration List.xlsx", //no extention needed
                                    'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                                );
                return response()->json($response);
            }
        }
        

        /* IFC Farmer */
        public function postExportifcfarmers(Request $request) {          
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $my_data        = Input::all(); 
                $ticket_no      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');
                if($ticket_no) {
                    $query  = " 
                                SELECT 
                                    @a:=@a+1  as serial_number,
                                    A.*
                                FROM
                                    (
                                    SELECT
                                        ticket_no,
                                        farmer_name,
                                        mobile,
                                        image,
                                        DATE_FORMAT(image_date_time, '%Y-%m-%d, %h:%i %p') as image_date_time,
                                        latitude,
                                        longitude,
                                        acreage,
                                        DATE_FORMAT(created_at, '%Y-%m-%d, %h:%i %p') as created_at
                                    FROM ifc_farmer
                                    WHERE ticket_no='".$ticket_no."'
                                    GROUP BY mobile
                                    ORDER BY id DESC
                                ) A,
                                (select @a:=0) initvars" ;

                    // echo $query; exit;
                    $farmer_data    = DB::select(DB::raw($query));
                    $result         = array_map(function ($value) {
                                            return (array)$value;
                                        }, $farmer_data);
                    $farmer_column  = array(
                                        'Serial No',
                                        'Ticket',
                                        'Farmer Name',
                                        'Mobile Number',
                                        'Image',
                                        'Image Date',
                                        'Latitude',
                                        'Longitude',
                                        'Acreage',
                                        'Created Date'
                                    );
                    array_unshift($result, $farmer_column);
                    
                    $primarycolor = "rgb(255, 0, 0)";
                    $myFile= Excel::create("filename", function($excel) use($result, $primarycolor) {
                                $excel->setTitle('title');
                                $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
                                    $sheet->fromArray($result, null, 'A1', false, false);
                                    $sheet->row(1, function($row) use ($primarycolor) {
                                        $row->setFontSize(12)
                                            ->setFontWeight('bold')
                                            ->setBackground('#62bdb9')
                                            ->setAlignment('left')
                                            ->setFontColor('#000000');
                                    });
                                });
                             });

                    $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
                    $response   = array(
                                    'name' => "IFC Farmer List.xlsx", //no extention needed
                                    'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                                 );
                    return response()->json($response);
                }
            }
        }

        public function postExportAllifcFarmers(Request $request) {          
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
            if ($userData) {
                $my_data    =   Input::all(); 
                //dd($my_data);
                $start_date =   $my_data['startdate'];
                $end_date   =   $my_data['enddate'];
                $search     =   $my_data['search'];
                
                $search         = $my_data['search']; 
                $sorting        = '';
                $datesearch     = '';
                $datesearch     = " (1=1) ";
                if($start_date!='' && $end_date!='') {
                    $datesearch.= " and  DATE(F.created_at) >= DATE('".$start_date."') AND DATE(F.created_at) <= DATE('".$end_date."')";
                }
                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/";

                $query = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT
                                    F.ticket_no,
                                    CONCAT_WS(
                                        '\n',
                                        NULLIF(zm.ECN, ''),
                                        NULLIF(rm.ECN, ''),
                                        NULLIF(am.ECN, ''),
                                        NULLIF(srm.ECN, ''),
                                        NULLIF(sam.ECN, ''),
                                        NULLIF(u.ECN, ''),
                                        NULLIF(dd.ECN, '')
                                    ) AS ecn,
                                    CONCAT_WS(
                                        '\n',
                                        NULLIF(zm.zonal_manager_name, ''),
                                        NULLIF(rm.regional_manager_name, ''),
                                        NULLIF(am.area_manager_name, ''),
                                        NULLIF(srm.sales_regional_manager_name, ''),
                                        NULLIF(sam.sales_area_manager_name, ''),
                                        NULLIF(u.name, ''),
                                        NULLIF(dd.name, '')
                                    ) AS bdo,
                                    F.farmer_name,
                                    F.mobile,
                                    F.acreage,
                                    FORMAT((F.activity_distance/1000),2) as activity_distance,
                                    CONCAT('".$base_url."', F.image) as farmarImage,
                                    F.latitude,
                                    F.longitude,
                                    DATE_FORMAT(F.image_date_time, '%Y-%m-%d, %h:%i %p') as image_date_time,
                                    DATE_FORMAT(F.created_at, '%Y-%m-%d, %h:%i %p') as created_at
                                FROM ifc_farmer F
                                LEFT JOIN users u ON F.user_id=u.id AND F.user_type='bdo'
                                LEFT JOIN zonal_managers zm ON F.user_id=zm.zonal_manager_id AND F.user_type='zonal'
                                LEFT JOIN regional_managers rm ON F.user_id=rm.regional_manager_id AND F.user_type='regional'
                                LEFT JOIN area_managers am ON F.user_id=am.area_manager_id AND F.user_type='area'
                                LEFT JOIN sales_regional_managers srm ON F.user_id=srm.sales_regional_manager_id AND F.user_type='sales_regional'
                                LEFT JOIN sales_area_managers sam ON F.user_id=sam.sales_area_manager_id AND F.user_type='sales_area'
                                LEFT JOIN dhanuka_doctor dd ON F.user_id=dd.dd_id AND F.user_type='dhanuka_doctor'
                                WHERE ".$datesearch." AND
                                    (
                                        F.ticket_no                         LIKE '%".$search."%'    OR
                                        F.farmer_name                       LIKE '%".$search."%'    OR
                                        F.mobile                            LIKE '%".$search."%'    OR
                                        zm.zonal_manager_name               LIKE '%".$search."%'    OR
                                        rm.regional_manager_name            LIKE '%".$search."%'    OR
                                        am.area_manager_name                LIKE '%".$search."%'    OR
                                        srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                                        sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                                        u.name                              LIKE '%".$search."%'
                                    )
                                GROUP BY F.mobile, F.ticket_no
                                ) A,
                                (select @a:=0) initvars
                                ".$sorting ;
                $product_farmer = DB::select(DB::raw($query));
                $result         = array_map(function ($value) {
                                                    return (array)$value;
                                                }, $product_farmer);
                $ticket_column  = array(
                                    'Serial No',
                                    'Ticket Number',
                                    'ECN Number',
                                    'ECN Name',
                                    'Farmer Name',
                                    'Mobile Number',
                                    'PDA',
                                    'Distance',
                                    'Image',
                                    'Latitude',
                                    'Longitude',
                                    'Image Date',
                                    'Created Date'
                                );
                array_unshift($result, $ticket_column);
            
                $primarycolor   =   "rgb(255, 0, 0)";
                $myFile         =   Excel::create("filename", function($excel) use($result, $primarycolor) {
                                        $excel->setTitle('title');
                                        $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
                                            $sheet->fromArray($result, null, 'A1', false, false);
                                            $sheet->row(1, function($row) use ($primarycolor) {
                                                $row->setFontSize(12)
                                                    ->setFontWeight('bold')
                                                    ->setBackground('#62bdb9')
                                                    ->setAlignment('left')
                                                    ->setFontColor('#000000');
                                            });
                                        });
                                     });
                     
                $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
                $response   =  array(
                                'name' => "IFC Farmer Registration List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                             );
                return response()->json($response);
            }
        }

        public function postViewifcfarmers(Request $request) {        
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            //print_r($userData); exit;
            if ($userData) {
                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/";
                $my_data        = Input::all(); 
                $ticket_no      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
                $ticket_data    = Ticket::where('ticket_no', $ticket_no)->first();
                if($ticket_data) {
                    $total_activity_distance = ($ticket_data->total_activity_distance) ? $ticket_data->total_activity_distance : 0;
                    $activity_duration = ($ticket_data->activity_duration) ? gmdate('H:i:s', floor($ticket_data->activity_duration * 3600)) : 0;
                    $farmer_list    = \DB::table('ifc_farmer')->where('ticket_no', $ticket_no)->groupBy('mobile')->orderBy('id','desc')->get();
                    //print_r($farmer_list);die;
                    $path = url('admin/addifcfarmer').'/'.$ticket_no;
                    if($farmer_list) {
                        echo    '<button name="export-ifcfarmers" id="export-ifcfarmers"  data-token="'.csrf_token().'" data-ticket="'.$ticket_no.'" >Export IFC Farmer Data</button>';
                        echo    '<br>';
                        echo    '<p style="float: right; padding-left: 30px;">Activity Duration: '.$activity_duration.' Hrs</p>';
                        echo    '<p style="float: right;">Activity Distance: '.number_format($total_activity_distance/1000,2).' KM</p>';
                        echo    '<br>';
                        echo    '<ol style="list-style-type:none; padding: unset; padding-top: 15px;">';
                        echo "<li style='border-bottom: 1px solid #d5d5d5; margin: 3px; height:28px;'>
                                    <span style='width: 10%;float: left;'>Farmer Name</span>
                                    <span style='width: 10%;float: left;'>Mobile No</span>
                                    <span style='width: 10%;float: left;'>Image</span>
                                    <span style='width: 15%;float: left;'>Image Date</span>
                                    <span style='width: 10%;float: left;'>Latitude</span>
                                    <span style='width: 10%;float: left;'>Longitude</span>
                                    <span style='width: 10%;float: left;'>Acreage</span>
                                    <span style='width: 10%;float: left;'>Distance</span>
                                    <span style='width: 15%;float: left;'>Created Date</span>
                                </li>";
                            foreach($farmer_list as $key => $farmer) {
                                if($farmer_list[$key]->activity_distance) {
                                    $activity_distance = $farmer_list[$key]->activity_distance/1000;
                                } else {
                                    $activity_distance = 0;
                                }
                                echo "<li style='border-bottom: 1px solid #d5d5d5; margin: 3px; height:55px; font-weight: normal;'>
                                    <span style='width: 10%;float: left;'>".$farmer_list[$key]->farmer_name."</span>
                                    <span style='width: 10%;float: left;'>".$farmer_list[$key]->mobile."</span>
                                    <span style='width: 10%;float: left;'><a href='".$base_url.$farmer_list[$key]->image."' target='_blank'><img style='height: 50px; width: 50px;' src='".$base_url.$farmer_list[$key]->image."'></a></span>
                                    <span style='width: 15%;float: left;'>".date('d-m-Y, g:i:s A', strtotime($farmer_list[$key]->image_date_time))."</span>
                                    <span style='width: 10%;float: left;'>".$farmer_list[$key]->latitude."</span>
                                    <span style='width: 10%;float: left;'>".$farmer_list[$key]->longitude."</span>
                                    <span style='width: 10%;float: left;'>".$farmer_list[$key]->acreage."</span>
                                    <span style='width: 10%;float: left;'>".number_format($activity_distance,2)."</span>
                                    <span style='width: 15%;float: left;'>".date('d-m-Y, g:i:s A', strtotime($farmer_list[$key]->created_at))."</span>
                                </li>";
                            }
                        echo    '</ol>';
                        echo    '<br>';
                    } else {
                        echo "<h4>No Record Found !</h4>";
                        echo "<br>";
                    }
                    // if( $_SERVER['REMOTE_ADDR'] == '2409:408a:1509:ac12:e163:da56:dfe:52bd') {
                    //     echo "<a style='text-align:center;' class='btn btn-info' href='javascript:void(0);' onclick='return redirectAddIfc();' data-toggle='modal' data-ticket='".$ticket_no."'>Add IFC Farmer</a>";
                    //     echo "<script>function redirectAddIfc() { window.location.href = '".$path."'; }</script>";
                    // }
                    // echo "IP- ".$_SERVER['REMOTE_ADDR'];
                    // if ($_SERVER['REMOTE_ADDR'] === '157.35.0.175') {
                        echo "<a style='text-align:center;' class='btn btn-info' href='javascript:void(0);' onclick='redirectAddIfc()' data-toggle='modal' data-ticket='{$ticket_no}'>Add IFC Farmer</a>";
                        echo "<script>
                            function redirectAddIfc() {
                                window.location.href = '{$path}';
                            }
                        </script>";
                    // }
                } else {
                    echo "<h4>No Record Found !</h4>";
                }
            } else {
                echo "<h4>No Record Found !</h4>";
            }
        }

        public function getIfcfarmer() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.farmer.ifcfarmer');
        }

        public function postAllifcfarmer(Request $request) {
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
            
            $start_date     = $value['start_date'];
            $end_date       = $value['end_date'];

            $base_url = 'https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/';
            
            if(empty($search_array['value']))
            {            
                $sorting        ='';            
                $datesearch     = '';

                if($start_date!='' && $end_date!='') {
                    $datesearch = "DATE(F.created_at) >= DATE('".$start_date."') AND DATE(F.created_at) <= DATE('".$end_date."')";
                }
                else {
                    $datesearch = "(1=1)";
                }

                $query = " 
                    SELECT
                        @a:=@a+1  as serial_number,
                        A.*
                    FROM
                        (
                        SELECT
                            F.ticket_no,
                            CONCAT_WS(
                                '\n',
                                NULLIF(zm.ECN, ''),
                                NULLIF(rm.ECN, ''),
                                NULLIF(am.ECN, ''),
                                NULLIF(srm.ECN, ''),
                                NULLIF(sam.ECN, ''),
                                NULLIF(u.ECN, ''),
                                NULLIF(dd.ECN, '')
                            ) AS ecn,
                            CONCAT_WS(
                                '\n',
                                NULLIF(zm.zonal_manager_name, ''),
                                NULLIF(rm.regional_manager_name, ''),
                                NULLIF(am.area_manager_name, ''),
                                NULLIF(srm.sales_regional_manager_name, ''),
                                NULLIF(sam.sales_area_manager_name, ''),
                                NULLIF(u.name, ''),
                                NULLIF(dd.name, '')
                            ) AS bdo,
                            F.farmer_name,
                            F.mobile,
                            F.acreage,
                            CONCAT('".$base_url."', F.image) as farmarImage,
                            F.latitude,
                            F.longitude,
                            DATE_FORMAT(F.image_date_time, '%Y-%m-%d, %h:%i %p') as image_date_time,
                            DATE_FORMAT(F.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                            F.activity_distance
                        FROM ifc_farmer F
                        LEFT JOIN users u ON F.user_id=u.id AND F.user_type='bdo'
                        LEFT JOIN zonal_managers zm ON F.user_id=zm.zonal_manager_id AND F.user_type='zonal'
                        LEFT JOIN regional_managers rm ON F.user_id=rm.regional_manager_id AND F.user_type='regional'
                        LEFT JOIN area_managers am ON F.user_id=am.area_manager_id AND F.user_type='area'
                        LEFT JOIN sales_regional_managers srm ON F.user_id=srm.sales_regional_manager_id AND F.user_type='sales_regional'
                        LEFT JOIN sales_area_managers sam ON F.user_id=sam.sales_area_manager_id AND F.user_type='sales_area'
                        LEFT JOIN dhanuka_doctor dd ON F.user_id=dd.dd_id AND F.user_type='dhanuka_doctor'
                        WHERE ".$datesearch." 
                        GROUP BY F.mobile, F.ticket_no
                    )  A,
                    (select @a:=0) initvars";
                    // LIMIT ". $start." , ".$limit."
                // echo $query; exit;
                $product_farmer    = collect(DB::select($query));
                return Datatables::of($product_farmer)->make();
            }
            else 
            {            
                $search         = $search_array['value']; 
                $sorting        = '';
                $datesearch     = '(1=1)';
                if($start_date!='' && $end_date!='') {
                    $datesearch.= " and  DATE(F.created_at) >= DATE('".$start_date."') AND DATE(F.created_at) <= DATE('".$end_date."')";
                }
                $query = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT
                                    F.ticket_no,
                                    CONCAT_WS(
                                        '\n',
                                        NULLIF(zm.ECN, ''),
                                        NULLIF(rm.ECN, ''),
                                        NULLIF(am.ECN, ''),
                                        NULLIF(srm.ECN, ''),
                                        NULLIF(sam.ECN, ''),
                                        NULLIF(u.ECN, ''),
                                        NULLIF(dd.ECN, '')
                                    ) AS ecn,
                                    CONCAT_WS(
                                        '\n',
                                        NULLIF(zm.zonal_manager_name, ''),
                                        NULLIF(rm.regional_manager_name, ''),
                                        NULLIF(am.area_manager_name, ''),
                                        NULLIF(srm.sales_regional_manager_name, ''),
                                        NULLIF(sam.sales_area_manager_name, ''),
                                        NULLIF(u.name, ''),
                                        NULLIF(dd.name, '')
                                    ) AS bdo,
                                    F.farmer_name,
                                    F.mobile,
                                    F.acreage,
                                    CONCAT('".$base_url."', F.image) as farmarImage,
                                    F.latitude,
                                    F.longitude,
                                    DATE_FORMAT(F.image_date_time, '%Y-%m-%d, %h:%i %p') as image_date_time,
                                    DATE_FORMAT(F.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    F.activity_distance
                                FROM ifc_farmer F
                                LEFT JOIN users u ON F.user_id=u.id AND F.user_type='bdo'
                                LEFT JOIN zonal_managers zm ON F.user_id=zm.zonal_manager_id AND F.user_type='zonal'
                                LEFT JOIN regional_managers rm ON F.user_id=rm.regional_manager_id AND F.user_type='regional'
                                LEFT JOIN area_managers am ON F.user_id=am.area_manager_id AND F.user_type='area'
                                LEFT JOIN sales_regional_managers srm ON F.user_id=srm.sales_regional_manager_id AND F.user_type='sales_regional'
                                LEFT JOIN sales_area_managers sam ON F.user_id=sam.sales_area_manager_id AND F.user_type='sales_area'
                                LEFT JOIN dhanuka_doctor dd ON F.user_id=dd.dd_id AND F.user_type='dhanuka_doctor'
                                WHERE ".$datesearch." AND
                                    (
                                        F.ticket_no                         LIKE '%".$search."%'    OR
                                        F.farmer_name                       LIKE '%".$search."%'    OR
                                        F.mobile                            LIKE '%".$search."%'    OR
                                        zm.zonal_manager_name               LIKE '%".$search."%'    OR
                                        rm.regional_manager_name            LIKE '%".$search."%'    OR
                                        am.area_manager_name                LIKE '%".$search."%'    OR
                                        srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                                        sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                                        u.name                              LIKE '%".$search."%'
                                    )
                                GROUP BY F.mobile, F.ticket_no
                                ) A,
                                (select @a:=0) initvars
                            ".$sorting ;
                //echo $query;die;
                $product_farmer    = collect(DB::select($query));
                return Datatables::of($product_farmer)->make();
            }
        }

        public function getAddifcfarmer($ticketno='') {
            if(!empty($ticketno)) {
                $sess = \Session::get('adminid');
                if (!isset($sess) || $sess == "") {
                    return redirect('/');
                }
                return View::make('backend.farmer.addifcfarmer', array('ticketno' => $ticketno));
            } else {
                return redirect('admin/tickets-list');
            }
        }

        public function postCreateifcfarmer(RequestsData $request) {
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            
            if ($userData) {
                $my_data    = Input::all();   
                // print_r($my_data); exit; 
                $rules      = array(
                                'farmer_name'   => 'required',
                                'mobile'        => 'required',
                                'latitude'      => 'required',
                                'longitude'     => 'required',
                                'acreage'       => 'required',
                                'image'         => 'required',
                                'activity_distance' => 'required'
                            );
                $validator  = Validator::make($my_data, $rules);
                if ($validator->fails()) {
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                $farmer_name        = $my_data['farmer_name'];
                $mobile             = $my_data['mobile'];
                $latitude           = $my_data['latitude'];
                $longitude          = $my_data['longitude'];
                $acreage            = $my_data['acreage'];
                $ticket_no          = $my_data['ticket_no'];
                $activity_distance  = $my_data['activity_distance'];

                $ticketData = Ticket::where('ticket_no',$ticket_no)->get();
                // echo "<pre>"; print_r($ticketData); exit();
                if(count($ticketData) == 0) {
                    Session::flash('message', "Invalid Ticket Found!");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                $ifcfarmers = IFCVillageFarmer::where('mobile',$mobile)->where('ticket_no',$ticket_no)->get();
                // echo "<pre>"; print_r($ifcfarmers); exit();
                if(count($ifcfarmers) > 0) {
                    Session::flash('message', "Mobile No already Registered for this ticket.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                $IFCFarmer = new IFCVillageFarmer;
                $IFCFarmer->user_id     = $sess;
                $IFCFarmer->user_type   = 'admin';
                $IFCFarmer->farmer_name = $my_data['farmer_name'];
                $IFCFarmer->mobile      = $my_data['mobile'];
                $IFCFarmer->image_date_time = date('Y-m-d H:i:s');
                $IFCFarmer->latitude    = $my_data['longitude'];
                $IFCFarmer->longitude   = $my_data['longitude'];
                $IFCFarmer->acreage     = $my_data['acreage'];
                $IFCFarmer->ticket_no   = $my_data['ticket_no'];
                $IFCFarmer->activity_distance = $my_data['activity_distance'];
                $IFCFarmer->created_at = date('Y-m-d H:i:s');
                $IFCFarmer->updated_at = date('Y-m-d H:i:s');

                // echo "<pre>"; print_r($request); exit();

                if($request->file('image')) {
                    $file=$request->file('image');
                    //s3Upload
                    $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                    $sTempFileName = 'assets/farmer/' . $fileName;
                    // echo "=>".$sTempFileName; exit();
                    Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                    $IFCFarmer->image= $fileName;
                }
                $IFCFarmer->save();
                $ifcfarmer_id = $IFCFarmer->id;
                // echo "=>".$ifcfarmer_id; exit();
                if($ifcfarmer_id) {
                    $activity_duration = 1;
                    $total_pda = ($ticketData[0]->total_pda) ? $ticketData[0]->total_pda : 0;
                    $total_pda_updated = $total_pda + $acreage;
                    $total_activity_distance = ($ticketData[0]->total_activity_distance) ? $ticketData[0]->total_activity_distance : 0;
                    $total_activity_distance_updated = $total_activity_distance + $activity_distance;

                    Ticket::where(['ticket_no' => $ticket_no])->update(['ifc_farmer_count' => DB::raw('ifc_farmer_count+1'),'total_pda' => $total_pda_updated,'total_activity_distance' => $total_activity_distance_updated,'updated_at'=>date('Y-m-d H:i:s')]);
                    // $ifcfarmer = IFCVillageFarmer::where('id',$ifcfarmer_id)->get();
                    Session::flash('message', "IFC Farmer Created Successfully.");
                    return Redirect::back()->withInput()->withErrors($validator);
                } else {
                    Session::flash('message', "An error occurret. Please try again later.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            }
        }
        /* IFC Farmer /-/ */

        //DD
        public function getAddDd() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $category_list  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
            $state_list     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
            $language_list  = Language::select('id', 'language')->orderBy('language', 'asc')->get();
            $zonal_list     = ZonalManager::where('status', '1')->get();
           // print_r($state_list); exit;
            return View::make('backend.dd.addDD', array('zonal_list' => $zonal_list,'state_list'=>$state_list, 'language_list'=>$language_list, 'category_list'=>$category_list));
        }

        public function postCreateDd(Request $request) {       
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
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
                
                $app_permission_edit_ticket = isset($my_data['app_permission_edit_ticket'])?'1':'0';
                $app_permission_close_ticket= isset($my_data['app_permission_close_ticket'])?'1':'0';
                $activity_permission        = isset($my_data['activity_permission'])?'1':'0';
                $tehsil                     = $my_data['tehsil'];
                $village                    = $my_data['village'];
                
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
                                                'app_permission_edit_ticket'  => $app_permission_edit_ticket,
                                                'app_permission_close_ticket' => $app_permission_close_ticket,
                                                'activity_permission'       => $activity_permission,
                                                'dd_email'                  => $dd_email,
                                                'dd_address'                => $dd_address,
                                                'bdo_id'                    => $bdo_id,
                                                'tehsil_id'                 => $tehsil
                                                ); 
                        $insert_user    = DhanukaDoctor::firstOrCreate($insert_dd);
                        $result         = $insert_user->dd_id;
                        if($result) {
                            DB::table('dhanuka_doctor')->where('dd_id', $result)->update(['activity_permission' => $activity_permission]);
                            $dd_product_category = array();
                            foreach($product_category AS $product_category_id) {
                                $dd_product_category[]    = array(
                                                                'dd_id' => $result,
                                                                'product_crop_category_id' => $product_category_id,
                                                                'status' => '1'
                                                                );
                            }
                            $insert_dd_product_category = DdProductCategory::insert($dd_product_category);

                            //insert village data
                            $dd_village_data = array();
                            foreach($village AS $village_id) {
                                $dd_village_data[] = array(
                                                            'assigned_to_dd_id' => $result,
                                                            'assigned_by_bdo_id' => $bdo_id,
                                                            'village_id' => $village_id,
                                                            'created_at' => date('Y-m-d H:i:s'),
                                                            'updated_at' => date('Y-m-d H:i:s')
                                                            );
                            }
                            $insert_dd_village_data = AssignedVillage::insert($dd_village_data); 
                            Session::flash('message', "DD Successfully Created.");
                            return redirect('admin/dd-list');
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

        public function getDdList() {

            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.dd.DDlist');
        }

        public function postAllddlist(Request $request) {
            //print_r(Request::all()); exit;
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            //prsint_r(Request::all()); exit;
            $value          = Input::all();//print_r($value);die;
            $status         = isset($value['status']) ? $value['status'] :'';
            $limit          = $value['length'];
            $start          = $value['start'];
            $search_array   = $value['search'];     // value & regax
            $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
            
            $order_array    = $value['order'][0]['column'];
            $dir            = $value['order'][0]['dir'];
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            $zonalId = "";
            // 
            if ($userData) {
                if ($status != '') {
                    $zonalId = "where u.status = '".$status."'";
                }
                else{
                    $zonalId = "";
                }
            }

            /*$userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('zonal_manager_id')->first();
            if ($userData1) {
                if ($status != '') {
                    $zonalId = "where u.zonal_manager_id = ".$userData1->zonal_manager_id."' and u.status = '".$status."'";

                } else {
                    $zonalId = "where u.zonal_manager_id = ".$userData1->zonal_manager_id;
                }
            }*/

            $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->select('state')->first();
            if ($userData1) {
                $state_ids = str_replace("]", "", str_replace("[", "", $userData1->state));
                if ($status != '') {
                    $zonalId = "where u.state_id IN (".$state_ids.") and u.status = '".$status."'";
                } else{
                    $zonalId = "where u.state_id IN (".$state_ids.")";
                }
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
                                        us.designation as fldesignation,
                                        (CASE WHEN u.activity_permission='1' THEN 'YES' ELSE 'NO' END) as activity_permission
                                        
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
                                    us.bdo_mobile_no,
                                    us.designation as fldesignation,
                                    (CASE WHEN u.activity_permission='1' THEN 'YES' ELSE 'NO' END) as activity_permission

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
                $top_open_ticket    = collect(DB::select($query));
                 
                return Datatables::of($top_open_ticket)->make();            
            }
        }

        public function getEditDd($dd_id) {
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                if($dd_id!='') {
                    $user_data = DhanukaDoctor::where('dd_id', $dd_id)->first();
                    if($user_data) {
                        $state_list             = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                        $district_list          = District::where('status', '1')->select('district_id', 'district')->where(['state_id' => $user_data->state_id])->orderBy('district', 'asc')->get();
                        $tehsil_list          = Tehsil::where('status', '1')->select('tehsil_id', 'tehsil')->where(['district_id' => $user_data->district_id])->orderBy('tehsil', 'asc')->get();
                        $village_list          = Village::where('status', '1')->select('village_id', 'village')->where(['tehsil_id' => $user_data->tehsil_id])->orderBy('village', 'asc')->get();
                        $village_list1          = Village::where('status', '1')->select('village_id')->where(['tehsil_id' => $user_data->tehsil_id])->orderBy('village_id', 'asc')->get()->toArray();
                        //$village_list2 = array_values($village_list1);
                        $village_list2 = array_column($village_list1,'village_id');
                        $language_list          = Language::select('id', 'language')->orderBy('language', 'asc')->get();
                        $zonal_list             = ZonalManager::where('status', '1')->get();
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

                        $dd_village_list1 = DB::table('assigned_village as av')
                                            ->join('villages as v','v.village_id', '=','av.village_id','left')
                                            ->where('av.assigned_to_dd_id', $dd_id)
                                            ->select('v.village_id','v.village')
                                            ->groupBy('v.village_id')
                                            ->get();

                        return View::make('backend.dd.editDD', array( 'zonal_list'            => $zonal_list,
                                                                        'regional_list'         => $regional_list,
                                                                        'sales_regional_list'   => $sales_regional_list,
                                                                        'area_list'             => $area_list,
                                                                        'sales_area_list'       => $sales_area_list,
                                                                        'userdata'              => $user_data, 
                                                                        'state_list'            => $state_list,
                                                                        'district_list'         => $district_list,
                                                                        'tehsil_list'         => $tehsil_list,
                                                                        'village_list'         => $village_list,
                                                                        'village_list2'         => $village_list2,
                                                                        'dd_village_list' => $dd_village_list, 
                                                                        'dd_village_list1' => $dd_village_list1, 
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

        public function postUpdateDd(Request $request) { 
            $date = date('Y-m-d H:i:s');        
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
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
                // echo "<pre>"; print_r($my_data); die;
                // echo "<pre>";print_r($my_data['area_manager']);die;
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
                $activity_permission        = isset($my_data['activity_permission'])?'1':'0';

                $change_password            = isset($my_data['change_password'])?'1':'0';
                $bdo_id                     = $my_data['bdo_id'];

                if(!empty($my_data['name']) && !empty($my_data['mobile_no']))
                {
                    $ddExist = DhanukaDoctor::where('name', '=', $my_data['name'])->where('dd_mobile_no','=', $my_data['mobile_no'])->where('dd_id','=',$my_data['dd_id'])->count();
                  
                    if($ddExist == 0)
                    {
                       $getData = DhanukaDoctor::where('dd_id',$my_data['dd_id'])->get()->toArray();
                       $historyData['dd_id']= $getData[0]['dd_id'];
                       $historyData['name']= $getData[0]['name'];
                       $historyData['dd_mobile_no']= $getData[0]['dd_mobile_no'];
                       $historyData['from']= $getData[0]['updated_at'];
                       $historyData['end']= $date;
                       // echo "<pre>"; print_r($historyData); die;
                      
                       DB::table('dd_history')->insert($historyData);

                    }                
                }

                // if($check_dd_code>0 || count($user)>0) {  
                //     $errors->add('your_custom_error',   "ECN already exists, Please choose a different ECN.");
                //     return Redirect::back()->withInput()->withErrors($errors);
                // }
                // else 
                // {   
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
                                    'activity_permission'         => $activity_permission,
                                    'tehsil_id'                   => $tehsil,
                                    'bdo_id'                      => $bdo_id
                                    
                                ); 
                    if($change_password=='1'){
                        $random_string              = $this->generateRandomString(8);
                        $password                   = \Hash::make($random_string);
                        $updated_values['password'] = $password;
                        $updated_values['dash']     = $random_string;
                    } 
                    
                    //$result = DB::table('dhanuka_doctor')->where('dd_id', $dd_id)->update($updated_values);
                    //print_r($result);die; 
                    if (isset($random_string) && !empty($random_string)) {
                        $result=DhanukaDoctor::find($dd_id);
                        $result->ECN=$dd_code;
                        $result->zonal_manager_id=$zonal_manager_id;
                        $result->regional_manager_id=$regional_manager_id;
                        $result->sales_regional_manager_id=$sales_regional_manager_id;
                        $result->area_manager_id=$area_manager_id;
                        $result->sales_area_manager_id= $sales_area_manager_id;
                        $result->bdo_id= $bdo_id;
                        $result->state_id=$state_id;
                        $result->district_id=$district_id;
                        $result->language_id=$language_id;
                        $result->department=$department;
                        $result->designation=$designation;
                        $result->dd_code=$dd_code;
                        $result->name=$name;
                        $result->dd_mobile_no=$mobile_no;
                        $result->dd_hq=$dd_hq;
                        $result->dd_alt_mobile_no=$dd_alt_mobile_no;
                        $result->dd_email=$dd_email;
                        $result->dd_address=$dd_address;
                        $result->app_permission_edit_ticket=$app_permission_edit_ticket;
                        $result->app_permission_close_ticket=$app_permission_close_ticket;
                        $result->activity_permission=$activity_permission;
                        $result->tehsil_id=$tehsil;
                        $result->bdo_id=$bdo_id;
                        $result->dash=$random_string;
                        $result->password=$password;
                        $result->save();
                    }
                    else
                    {
                        $result=DhanukaDoctor::find($dd_id);
                        $result->ECN=$dd_code;
                        $result->zonal_manager_id=$zonal_manager_id;
                        $result->regional_manager_id=$regional_manager_id;
                        $result->sales_regional_manager_id=$sales_regional_manager_id;
                        $result->area_manager_id=$area_manager_id;
                        $result->sales_area_manager_id= $sales_area_manager_id;
                        $result->bdo_id= $bdo_id;
                        $result->state_id=$state_id;
                        $result->district_id=$district_id;
                        $result->language_id=$language_id;
                        $result->department=$department;
                        $result->designation=$designation;
                        $result->dd_code=$dd_code;
                        $result->name=$name;
                        $result->dd_mobile_no=$mobile_no;
                        $result->dd_hq=$dd_hq;
                        $result->dd_alt_mobile_no=$dd_alt_mobile_no;
                        $result->dd_email=$dd_email;
                        $result->dd_address=$dd_address;
                        $result->app_permission_edit_ticket=$app_permission_edit_ticket;
                        $result->app_permission_close_ticket=$app_permission_close_ticket;
                        $result->activity_permission=$activity_permission;
                        $result->tehsil_id=$tehsil;
                        $result->bdo_id=$bdo_id;
                        $result->save();
                    }
                    
                    
                    if ($result) { 
                        $reset_dd_category = DB::table('dd_product_categories')
                                            ->where('dd_id', $dd_id)
                                            ->update(['status'=>'0']); 
                        foreach($product_category AS $category_id) {
                            $update_dd_manager   = DB::table('dd_product_categories')
                                                        ->where('dd_id', $dd_id)
                                                        ->where('product_crop_category_id', $category_id)
                                                        ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                            $dd_manager_states   = array(
                                                            'dd_id'                   => $dd_id,
                                                            'product_crop_category_id'  => $category_id,
                                                            'status'                    => '1'
                                                            );
                            $insert_dd_manager   = DdProductCategory::firstOrCreate($dd_manager_states);
                        }

                        //dd village update
                        $reset_dd_delete    = DB::table('assigned_village')->where('assigned_to_dd_id', $dd_id)->delete(); 
                        $dd_village_data = array();
                        foreach($village AS $village_id) {
                            $dd_village_data[] = array(
                                                        'assigned_to_dd_id' => $dd_id,
                                                        'assigned_by_bdo_id' => $bdo_id,
                                                        'village_id' => $village_id,
                                                        'created_at' => date('Y-m-d H:i:s'),
                                                        'updated_at' => date('Y-m-d H:i:s')
                                                        );
                        }
                        $insert_dd_village_data = AssignedVillage::insert($dd_village_data); 
                        Session::flash('message', "DD Successfully Updated.");
                        return redirect('admin/dd-list');
                    }
                    else
                    {   
                         Session::flash('message', "Please try again.");
                        return redirect('admin/dd-list');
                    }
                   
                   
                //}
            }
        }

        public function postUpdateDdStatus(Request $request) {
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $my_data        = Input::all(); 
                $dd_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
                $dd_data    = DhanukaDoctor::where('dd_id', $dd_id)->first();
                if($dd_data) {
                    $dd_data_status = ($dd_data->status=='0'?'1':'0');
                    $update_dd      = DB::table('dhanuka_doctor')
                                        ->where('dd_id', $dd_id)
                                        ->update(['status'=>$dd_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
                    echo '1';
                } else {
                    echo '0';
                }
            }
            else {
                echo '0';
            }
        }

        public function postAddNewFarmers(Request $request) {
         	$date = date('Y-m-d H:i:s');
             $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
            	$post = Request::all();
            	$mobiles = explode(',', $post['mobile']);
                $ticketDatas = Ticket::where('ticket_id', $post['_ticket'])->get();
                $ticketDate = $ticketDatas[0]['created_at'];
                $countMobileNum = 0;
                foreach ($mobiles as $key => $value) {
                    $farmerArr['ticket_id']         = $post['_ticket'];
                    $farmerArr['user_id']           = $sess;
                    $farmerArr['user_type']         = 'admin';
                    $farmerArr['mobile']            = $mobiles[$key];
                    $farmerArr['datetime']          = $ticketDate;
                    $farmerArr['registration_type'] = 'manual';
                    $farmerArr['updated_at']        = $date;
                    if (!empty($mobiles[$key])) {
                        $checkrepeatmobile = DB::table('farmers')
                                             ->where('ticket_id',$farmerArr['ticket_id'])
                                             ->where('mobile',$farmerArr['mobile'])
                                             ->get();
                        if (count($checkrepeatmobile) == 0) 
                        {
                            $insertFarmer  = DB::table('farmers')->insert($farmerArr);
                            $countMobileNum = $countMobileNum + 1;
                        }                     
                    }
                    
                    //Farmer::create($farmerArr);
                }
                //count($mobiles);
                $totalmobiles = $countMobileNum;
            	$getFarmerCount = Ticket::where('ticket_id', $post['_ticket'])->get();
            	$countFarmer = $getFarmerCount[0]['farmer_count']+$totalmobiles;
            	$farmer = ['farmer_count' => $countFarmer];
            	$updateTicket   = Ticket::where('ticket_id', $post['_ticket'])
                                    ->update($farmer);
            	echo '1';
            }
            else 
            {
                echo '0';
            }

         }

         public function getCounterpartFlList()
          {
            $sql = User::where('designation', 'COUNTERPART-FL')->get()->toArray();
           echo json_encode($sql);
              
          } 

        //Lead list
        public function getLeadList() {
            return view('backend.leads.leads');
        }

        public function postLeadList(Request $request){
            $value = Input::all();
            $start_date     = $value['start_date'];
            $end_date       = $value['end_date'];
            $datesearch     = '';

            if($start_date!='' && $end_date!='') {
                    $datesearch = "AND DATE(leads.updated_at) >= DATE('".$start_date." 00:00:00') AND DATE(leads.updated_at) <= DATE('".$end_date." 23:59:59')";
            } else {
                $datesearch = "";
            }
            $leadList="
                        (SELECT leads.lead_code,village_farmers.farmer_name, villages.village, tehsils.tehsil as location, CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.zonal_manager_name, ''),
                                                    NULLIF(rm.regional_manager_name, ''),
                                                    NULLIF(am.area_manager_name, ''),
                                                    NULLIF(srm.sales_regional_manager_name, ''),
                                                    NULLIF(sam.sales_area_manager_name, ''),
                                                    NULLIF(u.name, ''),
                                                    NULLIF(dd.name, '')
                                                ) AS name,
                                    leads.lead_status,
                                    products.product,
                                    village_farmers.contact_number,
                                    leads.farmer_id,
                                    leads.user_type,
                                    leads.expected_purchase_date,
                                    leads.lead_closed_by,
                                    leads.lead_closed_by_role,
                                    DATE_FORMAT(leads.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date,
                                    CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(distributor.name, '')
                                                ) AS distributor_name,
                                    leads.purchased_from,
                                    leads.purchased_area,
                                    leads.lead_closed_by_id,
                                    CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.ECN, ''),
                                                    NULLIF(rm.ECN, ''),
                                                    NULLIF(am.ECN, ''),
                                                    NULLIF(srm.ECN, ''),
                                                    NULLIF(sam.ECN, ''),
                                                    NULLIF(u.ECN, ''),
                                                    NULLIF(dd.ECN, '')
                                                ) AS ECN
                                    FROM leads 
                                    LEFT JOIN distributor ON distributor.distributor_id = leads.distributor_id
                                    LEFT JOIN products ON products.product_id = leads.product_id
                                    LEFT JOIN village_farmer_crop ON village_farmer_crop.farmer_id = leads.farmer_id 
                                    LEFT JOIN village_farmers ON village_farmers.farmer_id = village_farmer_crop.farmer_id 
                                    
                                    LEFT JOIN villages ON villages.village_id = village_farmer_crop.village_id 
                                    LEFT JOIN tehsils ON tehsils.tehsil_id = villages.tehsil_id 
                                    LEFT JOIN   users  u   ON leads.user_id=u.id AND leads.user_type='bdo'
                                    LEFT JOIN   zonal_managers          zm  ON leads.user_id=zm.zonal_manager_id AND leads.user_type='zonal'
                                    LEFT JOIN   regional_managers       rm  ON leads.user_id=rm.regional_manager_id AND leads.user_type='regional'
                                    LEFT JOIN   area_managers           am  ON leads.user_id=am.area_manager_id AND leads.user_type='area'
                                    LEFT JOIN   sales_regional_managers srm ON leads.user_id=srm.sales_regional_manager_id AND leads.user_type='sales_regional'
                                    LEFT JOIN   sales_area_managers     sam ON leads.user_id=sam.sales_area_manager_id AND leads.user_type='sales_area'
                                    LEFT JOIN   dhanuka_doctor dd ON leads.user_id=dd.dd_id AND leads.user_type='dhanuka_doctor' WHERE (leads.farmer_type = 'normale_farmer' or leads.farmer_type = 'normal_farmer')
                                    ".$datesearch."
                                    )
                        UNION
                        (SELECT leads.lead_code,farmers.farmer_name, tickets.village, tehsils.tehsil as location, CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.zonal_manager_name, ''),
                                                    NULLIF(rm.regional_manager_name, ''),
                                                    NULLIF(am.area_manager_name, ''),
                                                    NULLIF(srm.sales_regional_manager_name, ''),
                                                    NULLIF(sam.sales_area_manager_name, ''),
                                                    NULLIF(u.name, ''),
                                                    NULLIF(dd.name, '')
                                                ) AS name,
                                    leads.lead_status,
                                    products.product,
                                    farmers.mobile as contact_number,
                                    leads.farmer_id,
                                    leads.user_type,
                                    leads.expected_purchase_date,
                                    leads.lead_closed_by,
                                    leads.lead_closed_by_role,
                                    DATE_FORMAT(leads.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date,
                                    CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(distributor.name, '')
                                                ) AS distributor_name,
                                    leads.purchased_from,
                                    leads.purchased_area,
                                    leads.lead_closed_by_id,
                                    CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.ECN, ''),
                                                    NULLIF(rm.ECN, ''),
                                                    NULLIF(am.ECN, ''),
                                                    NULLIF(srm.ECN, ''),
                                                    NULLIF(sam.ECN, ''),
                                                    NULLIF(u.ECN, ''),
                                                    NULLIF(dd.ECN, '')
                                                ) AS ECN
                                    FROM leads 
                                    LEFT JOIN distributor ON distributor.distributor_id = leads.distributor_id
                                    LEFT JOIN products ON products.product_id = leads.product_id
                                    LEFT JOIN farmers ON farmers.farmer_id = leads.farmer_id 
                                    LEFT JOIN tickets ON tickets.ticket_id = farmers.ticket_id 
                                    LEFT JOIN tehsils ON tehsils.tehsil_id = tickets.tehsil_id 
                                    
                                    LEFT JOIN   users    u   ON leads.user_id=u.id AND leads.user_type='bdo'
                                    LEFT JOIN   zonal_managers          zm  ON leads.user_id=zm.zonal_manager_id AND leads.user_type='zonal'
                                    LEFT JOIN   regional_managers       rm  ON leads.user_id=rm.regional_manager_id AND leads.user_type='regional'
                                    LEFT JOIN   area_managers           am  ON leads.user_id=am.area_manager_id AND leads.user_type='area'
                                    LEFT JOIN   sales_regional_managers srm ON leads.user_id=srm.sales_regional_manager_id AND leads.user_type='sales_regional'
                                    LEFT JOIN   sales_area_managers     sam ON leads.user_id=sam.sales_area_manager_id AND leads.user_type='sales_area'
                                    LEFT JOIN   dhanuka_doctor dd ON leads.user_id=dd.dd_id AND leads.user_type='dhanuka_doctor' WHERE leads.farmer_type = 'register_farmer'
                                    ".$datesearch."
                                    )
                     ";
              //echo $leadList;die;               
             $leadList  = collect(DB::select($leadList));
             if (count($leadList)>0) {
                foreach ($leadList as $key => $value) {
                    if ($leadList[$key]->lead_status == '1') {
                        $leadList[$key]->lead_status = 'Open';
                    }
                    elseif ($leadList[$key]->lead_status == '2') {
                        $leadList[$key]->lead_status = 'Sold';
                    }
                    elseif ($leadList[$key]->lead_status == '3') {
                        $leadList[$key]->lead_status = 'Unsold';
                    }

                    if ($leadList[$key]->user_type == 'dhanuka_doctor') {
                        $leadList[$key]->user_type = 'DD';
                    }
                    elseif ($leadList[$key]->user_type == 'bdo') {
                       $leadList[$key]->user_type = 'BDO';
                    }

                    if ($leadList[$key]->lead_closed_by_role == 'dhanuka_doctor') {
                        $leadList[$key]->lead_closed_by_role = 'DD';
                    }
                    elseif ($leadList[$key]->lead_closed_by_role == 'bdo') {
                       $leadList[$key]->lead_closed_by_role = 'BDO';
                    }

                    $getacpcrop = DB::table('village_farmer_crop as vfc')
                                  ->join('crops as c','c.crop_id','=','vfc.crop_id')
                                  ->where('vfc.farmer_id',$leadList[$key]->farmer_id)
                                  ->select('c.crop')
                                  ->get();
                    if (count($getacpcrop)>0) {
                        $leadList[$key]->crop = $getacpcrop[0]->crop;
                    }
                    else
                    {
                        $getfarmercrop = DB::table('leads as l')
                                         ->join('farmers as f','f.farmer_id','=','l.farmer_id')
                                         ->join('tickets as t','t.ticket_id','=','f.ticket_id','left')
                                         ->join('crops as c','c.crop_id','=','t.crop_1_id','left')
                                         ->where('l.lead_code',$leadList[$key]->lead_code)
                                         ->where('l.farmer_type','register_farmer')
                                         ->select('c.crop')
                                         ->groupBy('c.crop_id')
                                         ->get(); 
                        if (count($getfarmercrop)>0) {
                            $leadList[$key]->crop = $getfarmercrop[0]->crop;
                        }else{
                            $leadList[$key]->crop = "";
                        }
                    }
                }
            }
            //echo "<pre>";print_r($leadList);die;
             return Datatables::of($leadList)->make();                             
        }

        //Open Lead list
        public function getOpenLeadList() {
            return view('backend.leads.openleads');       
        }

        public function postOpenLeadList(Request $request){
            $value = Input::all();
            $start_date     = $value['start_date'];
            $end_date       = $value['end_date'];
            $datesearch     = '';

            if($start_date!='' && $end_date!='') {
                    $datesearch = "AND DATE(leads.expected_purchase_date) >= DATE('".$start_date."') AND DATE(leads.expected_purchase_date) <= DATE('".$end_date."')";
            }
            else {
                $datesearch = "";
            }
            $leadList="
                        (SELECT leads.lead_code,village_farmers.farmer_name, villages.village, tehsils.tehsil as location, CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.zonal_manager_name, ''),
                                                    NULLIF(rm.regional_manager_name, ''),
                                                    NULLIF(am.area_manager_name, ''),
                                                    NULLIF(srm.sales_regional_manager_name, ''),
                                                    NULLIF(sam.sales_area_manager_name, ''),
                                                    NULLIF(u.name, ''),
                                                    NULLIF(dd.name, '')
                                                ) AS name,
                                    products.product, 
                                    expected_purchase_date,
                                    village_farmers.contact_number,
                                    leads.farmer_id,
                                    leads.user_type,
                                    CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.ECN, ''),
                                                    NULLIF(rm.ECN, ''),
                                                    NULLIF(am.ECN, ''),
                                                    NULLIF(srm.ECN, ''),
                                                    NULLIF(sam.ECN, ''),
                                                    NULLIF(u.ECN, ''),
                                                    NULLIF(dd.ECN, '')
                                                ) AS ECN 
                                    FROM leads 
                                    LEFT JOIN village_farmer_crop ON village_farmer_crop.farmer_id = leads.farmer_id
                                    LEFT JOIN products ON products.product_id = leads.product_id 
                                    LEFT JOIN village_farmers ON village_farmers.farmer_id = village_farmer_crop.farmer_id 
                                    LEFT JOIN villages ON villages.village_id = village_farmer_crop.village_id 
                                    LEFT JOIN tehsils ON tehsils.tehsil_id = villages.tehsil_id 
                                    LEFT JOIN   users                   u   ON leads.user_id=u.id AND leads.user_type='bdo'
                                    LEFT JOIN   zonal_managers          zm  ON leads.user_id=zm.zonal_manager_id AND leads.user_type='zonal'
                                    LEFT JOIN   regional_managers       rm  ON leads.user_id=rm.regional_manager_id AND leads.user_type='regional'
                                    LEFT JOIN   area_managers           am  ON leads.user_id=am.area_manager_id AND leads.user_type='area'
                                    LEFT JOIN   sales_regional_managers srm ON leads.user_id=srm.sales_regional_manager_id AND leads.user_type='sales_regional'
                                    LEFT JOIN   sales_area_managers     sam ON leads.user_id=sam.sales_area_manager_id AND leads.user_type='sales_area'
                                    LEFT JOIN   dhanuka_doctor dd ON leads.user_id=dd.dd_id AND leads.user_type='dhanuka_doctor' WHERE (leads.farmer_type = 'normale_farmer' or leads.farmer_type = 'normal_farmer')
                                    AND leads.lead_status = '1' ".$datesearch." )
                                     
                        UNION
                        (SELECT leads.lead_code,farmers.farmer_name, tickets.village, tehsils.tehsil as location, CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.zonal_manager_name, ''),
                                                NULLIF(rm.regional_manager_name, ''),
                                                NULLIF(am.area_manager_name, ''),
                                                NULLIF(srm.sales_regional_manager_name, ''),
                                                NULLIF(sam.sales_area_manager_name, ''),
                                                NULLIF(u.name, ''),
                                                NULLIF(dd.name, '')
                                            ) AS name ,
                                    products.product,
                                    leads.expected_purchase_date,
                                    farmers.mobile as contact_number,
                                    leads.farmer_id,
                                    leads.user_type,
                                    CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.ECN, ''),
                                                    NULLIF(rm.ECN, ''),
                                                    NULLIF(am.ECN, ''),
                                                    NULLIF(srm.ECN, ''),
                                                    NULLIF(sam.ECN, ''),
                                                    NULLIF(u.ECN, ''),
                                                    NULLIF(dd.ECN, '')
                                                ) AS ECN
                                    FROM leads 
                                    LEFT JOIN farmers ON farmers.farmer_id = leads.farmer_id 
                                    LEFT JOIN products ON products.product_id = leads.product_id 
                                    LEFT JOIN tickets ON tickets.ticket_id = farmers.ticket_id 
                                    LEFT JOIN tehsils ON tehsils.tehsil_id = tickets.tehsil_id 
                                    LEFT JOIN   users                   u   ON leads.user_id=u.id AND leads.user_type='bdo'
                                    LEFT JOIN   zonal_managers          zm  ON leads.user_id=zm.zonal_manager_id AND leads.user_type='zonal'
                                    LEFT JOIN   regional_managers       rm  ON leads.user_id=rm.regional_manager_id AND leads.user_type='regional'
                                    LEFT JOIN   area_managers           am  ON leads.user_id=am.area_manager_id AND leads.user_type='area'
                                    LEFT JOIN   sales_regional_managers srm ON leads.user_id=srm.sales_regional_manager_id AND leads.user_type='sales_regional'
                                    LEFT JOIN   sales_area_managers     sam ON leads.user_id=sam.sales_area_manager_id AND leads.user_type='sales_area'
                                    LEFT JOIN   dhanuka_doctor dd ON leads.user_id=dd.dd_id AND leads.user_type='dhanuka_doctor' WHERE leads.farmer_type = 'register_farmer' 
                                    AND leads.lead_status = '1' ".$datesearch." )

                ";
            $leadList  = collect(DB::select($leadList));
            if (count($leadList)>0) {
                foreach ($leadList as $key => $value) {
                    $leadList[$key]->soldstatus = 'Open';
                    if ($leadList[$key]->user_type == 'dhanuka_doctor') {
                        $leadList[$key]->user_type = 'DD';
                    }
                    elseif ($leadList[$key]->user_type == 'bdo') {
                       $leadList[$key]->user_type = 'BDO';
                    }
                    $getacpcrop = DB::table('village_farmer_crop as vfc')
                                  ->join('crops as c','c.crop_id','=','vfc.crop_id')
                                  ->where('vfc.farmer_id',$leadList[$key]->farmer_id)
                                  ->select('c.crop')
                                  ->get();
                    if (count($getacpcrop)>0) {
                        $leadList[$key]->crop = $getacpcrop[0]->crop;
                    }
                    else
                    {
                        $getfarmercrop = DB::table('leads as l')
                                         ->join('farmers as f','f.farmer_id','=','l.farmer_id')
                                         ->join('tickets as t','t.ticket_id','=','f.ticket_id','left')
                                         ->join('crops as c','c.crop_id','=','t.crop_1_id','left')
                                         ->where('l.lead_code',$leadList[$key]->lead_code)
                                         ->where('l.farmer_type','register_farmer')
                                         ->select('c.crop')
                                         ->groupBy('c.crop_id')
                                         ->get(); 
                        if (count($getfarmercrop)>0) {
                            $leadList[$key]->crop = $getfarmercrop[0]->crop;
                        }else{
                            $leadList[$key]->crop = "";
                        } 
                    }
                }
            }
            //echo "<pre>";print($leadList);die;
            return Datatables::of($leadList)->make();                             
                                         
        }

        //Lead list
        public function getCloseLeadList()
        {
            return view('backend.leads.closeleads');       
        }

        public function postCloseLeadList(Request $request){
            $value = Input::all();
            $start_date     = $value['start_date'];
            $end_date       = $value['end_date'];
            $datesearch     = '';

            if($start_date!='' && $end_date!='') {
                    $datesearch = "AND DATE(leads.updated_at) >= DATE('".$start_date."') AND DATE(leads.updated_at) <= DATE('".$end_date."')";
            }
            else {
                $datesearch = "";
            }
            $leadList="
                        (SELECT leads.lead_code,village_farmers.farmer_name, villages.village, tehsils.tehsil as location, CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.zonal_manager_name, ''),
                                                    NULLIF(rm.regional_manager_name, ''),
                                                    NULLIF(am.area_manager_name, ''),
                                                    NULLIF(srm.sales_regional_manager_name, ''),
                                                    NULLIF(sam.sales_area_manager_name, ''),
                                                    NULLIF(u.name, ''),
                                                    NULLIF(dd.name, '')
                                                ) AS name,
                                    products.product, 
                                    expected_purchase_date,
                                    village_farmers.contact_number,
                                    leads.farmer_id,
                                    leads.user_type,
                                    leads.lead_closed_by,
                                    leads.lead_closed_by_role,
                                    DATE_FORMAT(leads.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date,
                                    CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(distributor.name, '')
                                                ) AS distributor_name,
                                    leads.purchased_from,
                                    leads.purchased_area,
                                    leads.lead_closed_by_id,
                                    CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.ECN, ''),
                                                    NULLIF(rm.ECN, ''),
                                                    NULLIF(am.ECN, ''),
                                                    NULLIF(srm.ECN, ''),
                                                    NULLIF(sam.ECN, ''),
                                                    NULLIF(u.ECN, ''),
                                                    NULLIF(dd.ECN, '')
                                                ) AS ECN
                                    FROM leads 
                                    LEFT JOIN distributor ON distributor.distributor_id = leads.distributor_id
                                    LEFT JOIN village_farmer_crop ON village_farmer_crop.farmer_id = leads.farmer_id
                                    LEFT JOIN products ON products.product_id = leads.product_id 
                                    LEFT JOIN village_farmers ON village_farmers.farmer_id = village_farmer_crop.farmer_id 
                                    LEFT JOIN villages ON villages.village_id = village_farmer_crop.village_id 
                                    LEFT JOIN tehsils ON tehsils.tehsil_id = villages.tehsil_id 
                                    LEFT JOIN   users                   u   ON leads.user_id=u.id AND leads.user_type='bdo'
                                    LEFT JOIN   zonal_managers          zm  ON leads.user_id=zm.zonal_manager_id AND leads.user_type='zonal'
                                    LEFT JOIN   regional_managers       rm  ON leads.user_id=rm.regional_manager_id AND leads.user_type='regional'
                                    LEFT JOIN   area_managers           am  ON leads.user_id=am.area_manager_id AND leads.user_type='area'
                                    LEFT JOIN   sales_regional_managers srm ON leads.user_id=srm.sales_regional_manager_id AND leads.user_type='sales_regional'
                                    LEFT JOIN   sales_area_managers     sam ON leads.user_id=sam.sales_area_manager_id AND leads.user_type='sales_area'
                                    LEFT JOIN   dhanuka_doctor dd ON leads.user_id=dd.dd_id AND leads.user_type='dhanuka_doctor' WHERE (leads.farmer_type = 'normale_farmer' or leads.farmer_type = 'normal_farmer')
                                    AND leads.lead_status = '2' ".$datesearch.")
                                     
                        UNION
                        (SELECT leads.lead_code,farmers.farmer_name, tickets.village, tehsils.tehsil as location, CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.zonal_manager_name, ''),
                                                NULLIF(rm.regional_manager_name, ''),
                                                NULLIF(am.area_manager_name, ''),
                                                NULLIF(srm.sales_regional_manager_name, ''),
                                                NULLIF(sam.sales_area_manager_name, ''),
                                                NULLIF(u.name, ''),
                                                NULLIF(dd.name, '')
                                            ) AS name ,
                                    products.product,
                                    leads.expected_purchase_date,
                                    farmers.mobile as contact_number,
                                    leads.farmer_id,
                                    leads.user_type,
                                    leads.lead_closed_by,
                                    leads.lead_closed_by_role,
                                    DATE_FORMAT(leads.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date,
                                    CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(distributor.name, '')
                                                ) AS distributor_name,
                                    leads.purchased_from,
                                    leads.purchased_area,
                                    leads.lead_closed_by_id,
                                    CONCAT_WS(
                                                    '\n', 
                                                    NULLIF(zm.ECN, ''),
                                                    NULLIF(rm.ECN, ''),
                                                    NULLIF(am.ECN, ''),
                                                    NULLIF(srm.ECN, ''),
                                                    NULLIF(sam.ECN, ''),
                                                    NULLIF(u.ECN, ''),
                                                    NULLIF(dd.ECN, '')
                                                ) AS ECN
                                    FROM leads 
                                    LEFT JOIN distributor ON distributor.distributor_id = leads.distributor_id 
                                    LEFT JOIN farmers ON farmers.farmer_id = leads.farmer_id 
                                    LEFT JOIN products ON products.product_id = leads.product_id 
                                    LEFT JOIN tickets ON tickets.ticket_id = farmers.ticket_id 
                                    LEFT JOIN tehsils ON tehsils.tehsil_id = tickets.tehsil_id 
                                    LEFT JOIN   users                   u   ON leads.user_id=u.id AND leads.user_type='bdo'
                                    LEFT JOIN   zonal_managers          zm  ON leads.user_id=zm.zonal_manager_id AND leads.user_type='zonal'
                                    LEFT JOIN   regional_managers       rm  ON leads.user_id=rm.regional_manager_id AND leads.user_type='regional'
                                    LEFT JOIN   area_managers           am  ON leads.user_id=am.area_manager_id AND leads.user_type='area'
                                    LEFT JOIN   sales_regional_managers srm ON leads.user_id=srm.sales_regional_manager_id AND leads.user_type='sales_regional'
                                    LEFT JOIN   sales_area_managers     sam ON leads.user_id=sam.sales_area_manager_id AND leads.user_type='sales_area'
                                    LEFT JOIN   dhanuka_doctor dd ON leads.user_id=dd.dd_id AND leads.user_type='dhanuka_doctor' WHERE leads.farmer_type = 'register_farmer'
                                    AND leads.lead_status = '2' ".$datesearch.")

                ";
            $leadList  = collect(DB::select($leadList));
            if (count($leadList)>0) {
                foreach ($leadList as $key => $value) {
                    $leadList[$key]->soldstatus = 'Sold';
                    if ($leadList[$key]->user_type == 'dhanuka_doctor') {
                        $leadList[$key]->user_type = 'DD';
                    }
                    elseif ($leadList[$key]->user_type == 'bdo') {
                       $leadList[$key]->user_type = 'BDO';
                    }

                    if ($leadList[$key]->lead_closed_by_role == 'dhanuka_doctor') {
                        $leadList[$key]->lead_closed_by_role = 'DD';
                    }
                    elseif ($leadList[$key]->lead_closed_by_role == 'bdo') {
                       $leadList[$key]->lead_closed_by_role = 'BDO';
                    }
                    
                    $getacpcrop = DB::table('village_farmer_crop as vfc')
                                  ->join('crops as c','c.crop_id','=','vfc.crop_id')
                                  ->where('vfc.farmer_id',$leadList[$key]->farmer_id)
                                  ->select('c.crop')
                                  ->get();
                    if (count($getacpcrop)>0) {
                        $leadList[$key]->crop = $getacpcrop[0]->crop;
                    }
                    else
                    {
                        $getfarmercrop = DB::table('leads as l')
                                         ->join('farmers as f','f.farmer_id','=','l.farmer_id')
                                         ->join('tickets as t','t.ticket_id','=','f.ticket_id','left')
                                         ->join('crops as c','c.crop_id','=','t.crop_1_id','left')
                                         ->where('l.lead_code',$leadList[$key]->lead_code)
                                         ->where('l.farmer_type','register_farmer')
                                         ->select('c.crop')
                                         ->groupBy('c.crop_id')
                                         ->get(); 
                        if (count($getfarmercrop)>0) {
                            $leadList[$key]->crop = $getfarmercrop[0]->crop;
                        }else{
                            $leadList[$key]->crop = "";
                        }
                    }
                }
            }
            //echo "<pre>";print($leadList);die;
            return Datatables::of($leadList)->make();                             
                                         
        }
        	

        public function getAddBranchAdmin() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
           
            $state_list     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
            $zonal_list     = DB::table('zonal_managers')->where('status', '1')->select('zonal_manager_id', 'zonal_manager_name')->orderBy('zonal_manager_name', 'asc')->get();
            return View::make('backend.dd.addBranchAdmin', array('state_list'=>$state_list, 'zonal_list'=>$zonal_list));
        }

        //create-branch

        public function postCreateBranch(Request $request) {       
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
            

            if ($userData) {
                $my_data        = Input::all(); 
                $rules  = array(
                                'user_name'         => 'required',
                                'administrator_name'=> 'required',
                                'state'             => 'required',
                                'branch_name'       => 'required',
                                // 'zonal'             => 'required'
                            );
                $validator  = Validator::make($my_data, $rules);
                if ($validator->fails()) {
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                
                $branch['user_name']           = $my_data['user_name'];
                $random_string  = $this->generateRandomString(8);
                $branch['password']       = base64_encode($random_string);
                $branch['dash']       = $random_string;
                $branch['administrator_name']  = $my_data['administrator_name'];
                $branch['state']            = json_encode($my_data['state']);
                $branch['branch_name']      = $my_data['branch_name'];
                // $branch['zonal_manager_id']      = $my_data['zonal'];
                $branch['role']      = 'branch';
                $branch['role_id']      = '8';
                $insert_user    = DB::table('branch_admin')->insert($branch);   
                Session::flash('message', "Branch Successfully Created.");
                            return redirect('admin/branch-list');
            }
        }
      
        public function getBranchList()
        {
            return view('backend.dd.branchlist');       
        }  

        public function postBranchList(Request $request)
        {   
            $value = Input::all();
            $status = isset($value['status']) ? $value['status'] :'';
            if ($status != '') {
                $branchList="SELECT ba.id,ba.user_name,ba.password,ba.dash,ba.role,ba.role_id,ba.state,ba.administrator_name,ba.branch_name,ba.zonal_manager_id,(CASE WHEN ba.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ba_status,ba.created_at,ba.updated_at, zm.zonal_manager_name FROM  branch_admin as ba left join zonal_managers as zm on zm.zonal_manager_id = ba.zonal_manager_id  WHERE  ba.status =  '".$status."'"; 
            }
            else{
                $branchList="SELECT ba.id,ba.user_name,ba.password,ba.dash,ba.role,ba.role_id,ba.state,ba.administrator_name,ba.branch_name,ba.zonal_manager_id,(CASE WHEN ba.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ba_status,ba.created_at,ba.updated_at, zm.zonal_manager_name FROM  branch_admin as ba left join zonal_managers as zm on zm.zonal_manager_id = ba.zonal_manager_id"; 
            }
            
            $branchList  = collect(DB::select($branchList));
            //print_r($branchList);die;
            return Datatables::of($branchList)->make();
            //$merged =$callList->toArray(); 
        }

        public function getEditBranch($id) { 
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
            if($id!='') {
            $branch_admin = DB::table('branch_admin')->where('id', $id)->first();
            if($branch_admin) {
            $state_list     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
           // $zonal_list = DB::table('zonal_manager_states as zms')
                         // ->join('zonal_managers as zm','zm.zonal_manager_id','=','zms.zonal_manager_id','left')
                         // ->where('zms.state_id',$branch_admin->state)
                         // ->where('zms.status','1')
                          //->select('zm.zonal_manager_id','zm.zonal_manager_name')
                         // ->orderBy('zm.zonal_manager_name','asc')
                         // ->get();
            // $zonal_list     = DB::table('zonal_managers')->where('status', '1')->select('zonal_manager_id', 'zonal_manager_name')->orderBy('zonal_manager_name', 'asc')->get();
            return View::make('backend.dd.editBranchAdmin', array('state_list'=>$state_list,'branch' =>$branch_admin));  
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

        public function postUpdateBranch(Request $request) {       
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $my_data        = Input::all(); 
                // print_r($my_data);die;
                $rules  = array(
                                'user_name'         => 'required',
                                'administrator_name'=> 'required',
                                'state'          => 'required',
                                'branch_name'    => 'required',
                                
                            );
                $validator  = Validator::make($my_data, $rules);
                if ($validator->fails()) {
                    return Redirect::back()->withInput()->withErrors($validator);
                }

              $count = DB::table('branch_admin')->where('id', $my_data['id'])->count();
              if($count > 0)
              {
                $branch['user_name']             = $my_data['user_name'];
                $branch['administrator_name']    = $my_data['administrator_name'];
                $branch['state']                 = json_encode($my_data['state']);
                $branch['branch_name']           = $my_data['branch_name'];
                // $branch['zonal_manager_id']      = $my_data['zonal'];
             
              $insert_user    = DB::table('branch_admin')
                                    ->where('id', $my_data['id'])
                                    ->update($branch);      
               Session::flash('message', "Branch Successfully Updated.");
                            return redirect('admin/branch-list');

              }
              else
              {
               return redirect('/');
              }      
           }
        }

        public function postDeleteBranch(Request $request)
        {   
            $value = Input::all();
            $delete    = DB::table('branch_admin')->where('id', $value['_ticket'])->delete(); 
            if ($delete) {
                echo "1";die;
            }
                
            // Session::flash('message', "Branch Successfully deleted.");
            //                     return redirect('admin/branch-list');
          
        }

        public function postUpdateBranchStatus(Request $request) {
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $my_data        = Input::all(); 
                $branch_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
                $branch_data    = DB::table('branch_admin')->where('id', $branch_id)->get();
                if(count($branch_data)>0) {
                    $branch_data_status = ($branch_data[0]->status=='0'?'1':'0');
                    $update_branch      = DB::table('branch_admin')
                                            ->where('id', $branch_id)
                                            ->update(['status'=>$branch_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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


        public function getActivities() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $tids = array();
            $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
            if (count($userData1)>0) {
                //dd
                $ddData = DB::table('dhanuka_doctor')
                          ->where('zonal_manager_id',$userData1->zonal_manager_id)
                          ->where('status','1')
                          ->select('dd_id')
                          ->get();
                $ddresult = array(); 
                if (isset($ddData) && !empty($ddData)) 
                {
                    $ddDatass = json_decode(json_encode($ddData),true);
                    
                    foreach ($ddDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $ddresult = array_merge($ddresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                          $ddresult[$key] = $value; 
                        } 
                    } 
                    $ticket_ids1 = DB::table('tickets')->whereIn('user_id',$ddresult)->where('user_type','dhanuka_doctor')->select('ticket_id')->get(); 
                    $ticketresult = array();
                    if (count($ticket_ids1)>0) {
                        $ticketDatass = json_decode(json_encode($ticket_ids1),true);
                        foreach ($ticketDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $ticketresult = array_merge($ticketresult, array_flatten($value)); 
                            } 
                            else 
                            { 
                              $ticketresult[$key] = $value; 
                            } 
                        } 
                    }
                }  
                    

                //bdo
                $bdoData = DB::table('users')
                          ->where('zonal_manager_id',$userData1->zonal_manager_id)
                          ->where('status','1')
                          ->select('id')
                          ->get();
                $bdoresult = array(); 
                if (isset($bdoData) && !empty($bdoData)) 
                {
                    $bdoDatass = json_decode(json_encode($bdoData),true);
                    
                    foreach ($bdoDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                          $bdoresult[$key] = $value; 
                        } 
                    } 
                    $ticket_ids2 = DB::table('tickets')->whereIn('user_id',$bdoresult)->where('user_type','bdo')->select('ticket_id')->get();
                    $ticketresult1 = array();
                    if (count($ticket_ids2)>0) {
                        $ticketDatass = json_decode(json_encode($ticket_ids2),true);
                        
                        foreach ($ticketDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $ticketresult1 = array_merge($ticketresult1, array_flatten($value)); 
                            } 
                            else 
                            { 
                              $ticketresult1[$key] = $value; 
                            } 
                        } 
                    }
                } 

                //asm
                $asmData = DB::table('sales_area_managers')
                          ->where('zonal_manager_id',$userData1->zonal_manager_id)
                          ->where('status','1')
                          ->select('sales_area_manager_id')
                          ->get();
                $asmresult = array(); 
                if (isset($asmData) && !empty($asmData)) 
                {
                    $asmDatass = json_decode(json_encode($asmData),true);
                    
                    foreach ($asmDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $asmresult = array_merge($asmresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                          $asmresult[$key] = $value; 
                        } 
                    } 
                    $ticket_ids2 = DB::table('tickets')->whereIn('user_id',$asmresult)->where('user_type','sales_area')->select('ticket_id')->get();
                    $ticketresult2 = array();
                    if (count($ticket_ids2)>0) {
                        $ticketDatass = json_decode(json_encode($ticket_ids2),true);
                        
                        foreach ($ticketDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $ticketresult2 = array_merge($ticketresult2, array_flatten($value)); 
                            } 
                            else 
                            { 
                              $ticketresult2[$key] = $value; 
                            } 
                        } 
                    }
                } 

                //rsm
                $rsmData = DB::table('sales_regional_managers')
                          ->where('zonal_manager_id',$userData1->zonal_manager_id)
                          ->where('status','1')
                          ->select('sales_regional_manager_id')
                          ->get();
                $rsmresult = array(); 
                if (isset($rsmData) && !empty($rsmData)) 
                {
                    $rsmDatass = json_decode(json_encode($rsmData),true);
                    
                    foreach ($rsmDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $rsmresult = array_merge($rsmresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                            $rsmresult[$key] = $value; 
                        } 
                    } 
                    $ticket_ids3 = DB::table('tickets')->whereIn('user_id',$rsmresult)->where('user_type','sales_regional')->select('ticket_id')->get();
                    $ticketresult3 = array();
                    if (count($ticket_ids2)>0) {
                        $ticketDatass = json_decode(json_encode($ticket_ids3),true);
                        foreach ($ticketDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $ticketresult3 = array_merge($ticketresult3, array_flatten($value)); 
                            } 
                            else 
                            { 
                                $ticketresult3[$key] = $value; 
                            } 
                        } 
                    }
                }
                
                if(count($ticketresult)>0 && count($ticketresult1)>0 && count($ticketresult2)>0 && count($ticketresult3)>0)
                {   
                    $tids = array_merge($ticketresult,$ticketresult1,$ticketresult2,$ticketresult3);
                }  
                elseif (count($ticketresult)>0 && count($ticketresult1)>0 && count($ticketresult2)>0 && count($ticketresult3)==0) {
                    $tids = array_merge($ticketresult,$ticketresult1,$ticketresult2);
                }
                elseif (count($ticketresult)>0 && count($ticketresult1)>0 && count($ticketresult2)==0 && count($ticketresult3)==0) {
                    $tids = array_merge($ticketresult,$ticketresult1);
                }
                elseif (count($ticketresult)>0 && count($ticketresult1)==0 && count($ticketresult2)==0 && count($ticketresult3)==0) {
                    $tids = $ticketresult;
                }
                else
                {
                    $tids = array();
                }

                $countActivity = DB::table('tickets')->whereIn('ticket_id',$tids)->count();
                $countNotDoneActivity = DB::table('tickets')->where('tickets.status','0')->whereIn('ticket_id',$tids)->count();
                $countFarmer = DB::table('farmers')->whereIn('ticket_id',$tids)->count();
                $countValidatedFarmer = DB::table('tickets')->whereIn('ticket_id',$tids)->sum('tickets.validated_farmers');
                $countDD = DB::table('dhanuka_doctor')->where('zonal_manager_id',$userData1->zonal_manager_id)->count();
                $countDemoRequest = DB::table('demo_request')->whereIn('dd_id',$ddresult)->count();
                $DemoRequestids = DB::table('demo_request')->whereIn('dd_id',$ddresult)->get();
                $ExtrapolatedFarmer = "NA";
                $demoarray = array();
                if (count($DemoRequestids)>0) {
                    $demoDatass = json_decode(json_encode($DemoRequestids),true);
                    foreach ($demoDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                           $demoarray = array_merge($demoarray, array_flatten($value)); 
                        } 
                        else 
                        { 
                           $demoarray[$key] = $value; 
                        } 
                    }
                    $countDemoFeedback = DB::table('demofeedback')->whereIn('demo_request_id',$demoarray)->groupBy('demo_request_id')->count(); 
                }
                else
                {
                    $countDemoFeedback = 0;
                }
            }
            else
            {
                $countActivity = DB::table('tickets')->count();
                $countNotDoneActivity = DB::table('tickets')->where('tickets.status','0')->count();
                $countFarmer = DB::table('farmers')->count();
                $countValidatedFarmer = DB::table('tickets')->sum('tickets.validated_farmers');
                $countDD = DB::table('dhanuka_doctor')->count();
                $countDemoRequest = DB::table('demo_request')->count();
                $countDemoFeedback = DB::table('demofeedback')->groupBy('demo_request_id')->count();
                $ExtrapolatedFarmer = $countActivity;
            }
            
            $merged = array('countActivity' => $countActivity, 'countNotDoneActivity' => $countNotDoneActivity, 'countFarmer' => $countFarmer, 'countDD' => $countDD, 'countDemoRequest' => $countDemoRequest, 'countDemoFeedback' => $countDemoFeedback, 'countValidatedFarmer' => $countValidatedFarmer, 'ExtrapolatedFarmer' => $ExtrapolatedFarmer);
            return view('backend.activity.activities',compact('merged')); 
            
        } 

        public function postActivityFilter(Request $request)
        {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $requestdata = array();
            $requestdata = Request::input();
            $tids = array();
            $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
            if (count($userData1)>0) {
                //dd
                $ddData = DB::table('dhanuka_doctor')
                          ->where('zonal_manager_id',$userData1->zonal_manager_id)
                          ->where('status','1')
                          ->select('dd_id')
                          ->get();
                $ddsresult = array(); 
                if (isset($ddData) && !empty($ddData)) 
                {
                    $ddDatass = json_decode(json_encode($ddData),true);
                    
                    foreach ($ddDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $ddsresult = array_merge($ddsresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                          $ddsresult[$key] = $value; 
                        } 
                    } 
                    $ticket_ids1 = DB::table('tickets')->whereIn('user_id',$ddsresult)->where('user_type','dhanuka_doctor')->select('ticket_id')->get(); 
                    $ticketresult = array();
                    if (count($ticket_ids1)>0) {
                        $ticketDatass = json_decode(json_encode($ticket_ids1),true);
                        foreach ($ticketDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $ticketresult = array_merge($ticketresult, array_flatten($value)); 
                            } 
                            else 
                            { 
                              $ticketresult[$key] = $value; 
                            } 
                        } 
                    }
                }  
                
                //bdo
                $bdoData = DB::table('users')
                          ->where('zonal_manager_id',$userData1->zonal_manager_id)
                          ->where('status','1')
                          ->select('id')
                          ->get();
                $bdosresult = array(); 
                if (isset($bdoData) && !empty($bdoData)) 
                {
                    $bdoDatass = json_decode(json_encode($bdoData),true);
                    
                    foreach ($bdoDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $bdosresult = array_merge($bdosresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                          $bdosresult[$key] = $value; 
                        } 
                    } 
                    $ticket_ids2 = DB::table('tickets')->whereIn('user_id',$bdosresult)->where('user_type','bdo')->select('ticket_id')->get();
                    $ticketresult1 = array();
                    if (count($ticket_ids2)>0) {
                        $ticketDatass = json_decode(json_encode($ticket_ids2),true);
                        
                        foreach ($ticketDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $ticketresult1 = array_merge($ticketresult1, array_flatten($value)); 
                            } 
                            else 
                            { 
                              $ticketresult1[$key] = $value; 
                            } 
                        } 
                    }
                } 

                if(count($ticketresult)>0 && count($ticketresult1)>0)
                {   
                    $tids = array_merge($ticketresult,$ticketresult1);
                }  
                elseif (count($ticketresult)>0) {
                    $tids = $ticketresult;
                }
                elseif (count($ticketresult1)>0) {
                    $tids = $ticketresult1;
                }
                else
                {
                    $tids = array();
                }


                $countActivity = DB::table('tickets');
                                if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                    //sales regional manager dd
                                    $ddData = DB::table('dhanuka_doctor')
                                              ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                              ->where('status','1')
                                              ->select('dd_id')
                                              ->get();
                                    $ddresult = array(); 
                                    if (isset($ddData) && !empty($ddData)) 
                                    {
                                        $ddDatass = json_decode(json_encode($ddData),true);
                                        foreach ($ddDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $ddresult = array_merge($ddresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $ddresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    //sales regional manager bdo
                                    $bdoData = DB::table('users')
                                              ->where('sales_regional_manager_id',$requestdata['filter_ASM'])
                                              ->where('status','1')
                                              ->select('id')
                                              ->get();
                                    $bdoresult = array(); 
                                    if (isset($bdoData) && !empty($bdoData)) 
                                    {
                                        $bdoDatass = json_decode(json_encode($bdoData),true);
                                        
                                        foreach ($bdoDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $bdoresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    if (count($bdoresult) > 0) {
                                       $countActivity->whereIn('tickets.user_id',$bdoresult);
                                       $countActivity->where('tickets.user_type','bdo');
                                    }

                                    if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                        $countActivity->orWhereIn('tickets.user_id',$ddresult);
                                        $countActivity->where('tickets.user_type','dhanuka_doctor');
                                    }
                                    else
                                    {   
                                        if (count($ddresult) > 0) 
                                        {
                                            $countActivity->whereIn('tickets.user_id',$ddresult);
                                            $countActivity->where('tickets.user_type','dhanuka_doctor');
                                        }
                                    }
                                }

                                if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {

                                    //Sales area manager dd
                                    $ddData = DB::table('dhanuka_doctor')
                                              ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                              ->where('status','1')
                                              ->select('dd_id')
                                              ->get();
                                    $ddresult = array(); 
                                    if (isset($ddData) && !empty($ddData)) 
                                    {
                                        $ddDatass = json_decode(json_encode($ddData),true);
                                        
                                        foreach ($ddDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $ddresult = array_merge($ddresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $ddresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    //area sales manager bdo
                                    $bdoData = DB::table('users')
                                              ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                              ->where('status','1')
                                              ->select('id')
                                              ->get();
                                    $bdoresult = array(); 
                                    if (isset($bdoData) && !empty($bdoData)) 
                                    {
                                        $bdoDatass = json_decode(json_encode($bdoData),true);
                                        
                                        foreach ($bdoDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $bdoresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    if (count($bdoresult) > 0) {
                                       $countActivity->whereIn('tickets.user_id',$bdoresult);
                                       $countActivity->where('tickets.user_type','bdo');
                                    }

                                    if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                        $countActivity->orWhereIn('tickets.user_id',$ddresult);
                                        $countActivity->where('tickets.user_type','dhanuka_doctor');
                                    }
                                    else
                                    {   
                                        if (count($ddresult) > 0) 
                                        {
                                            $countActivity->where('tickets.user_id',$ddresult);
                                            $countActivity->where('tickets.user_type','dhanuka_doctor');
                                        }
                                    }
                                }

                                if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                   $countActivity->where('tickets.user_id',$requestdata['filter_FL']);
                                    $countActivity->where('tickets.user_type','bdo');
                                }

                                if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                    $countActivity->where('tickets.user_id',$requestdata['filter_DD']);
                                    $countActivity->where('tickets.user_type','dhanuka_doctor');
                                }

                                if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                    $from_date=$requestdata['filter_start'];
                                    $to_date=$requestdata['filter_end'];
                                    $countActivity->whereBetween('tickets.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }

                                if(!empty($requestdata['filter_crop'])){
                                    $crop=$requestdata['filter_crop'];
                                    $countActivity->Where('tickets.crop_1_id', $crop);
                                    $countActivity->orWhere('tickets.crop_2_id', $crop);
                                    $countActivity->orWhere('tickets.crop_3_id', $crop);
                                }

                                if(!empty($requestdata['filter_product'])){
                                    $product=$requestdata['filter_product'];
                                    $countActivity->Where('tickets.product_1_id', $product);
                                    $countActivity->orWhere('tickets.product_2_id', $product);
                                    $countActivity->orWhere('tickets.product_3_id', $product);
                                } 
                $countActivity = $countActivity->count();
                if ($countActivity == null) {
                    $countActivity = (int)0;
                }

                $countNotDoneActivity = DB::table('tickets')
                                        ->where('tickets.status','0');
                                        if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                        {
                                            //sales regional manager dd
                                            $ddData = DB::table('dhanuka_doctor')
                                                      ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                      ->where('status','1')
                                                      ->select('dd_id')
                                                      ->get();
                                            $ddresult = array(); 
                                            if (isset($ddData) && !empty($ddData)) 
                                            {
                                                $ddDatass = json_decode(json_encode($ddData),true);
                                                foreach ($ddDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $ddresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            //sales regional manager bdo
                                            $bdoData = DB::table('users')
                                                      ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                      ->where('status','1')
                                                      ->select('id')
                                                      ->get();
                                            $bdoresult = array(); 
                                            if (isset($bdoData) && !empty($bdoData)) 
                                            {
                                                $bdoDatass = json_decode(json_encode($bdoData),true);
                                                
                                                foreach ($bdoDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $bdoresult[$key] = $value; 
                                                    } 
                                                } 
                                            }
                                            if (count($bdoresult) > 0) {
                                               $countNotDoneActivity->whereIn('tickets.user_id',$bdoresult);
                                               $countNotDoneActivity->where('tickets.user_type','bdo');
                                            }

                                            if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                                $countNotDoneActivity->orWhereIn('tickets.user_id',$ddresult);
                                                $countNotDoneActivity->where('tickets.user_type','dhanuka_doctor');
                                            }
                                            else
                                            {   
                                                if (count($ddresult) > 0) 
                                                {
                                                    $countNotDoneActivity->whereIn('tickets.user_id',$ddresult);
                                                    $countNotDoneActivity->where('tickets.user_type','dhanuka_doctor');
                                                }
                                            }
                                        }

                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {

                                            //area regional manager dd
                                            $ddData = DB::table('dhanuka_doctor')
                                                      ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                      ->where('status','1')
                                                      ->select('dd_id')
                                                      ->get();
                                            $ddresult = array(); 
                                            if (isset($ddData) && !empty($ddData)) 
                                            {
                                                $ddDatass = json_decode(json_encode($ddData),true);
                                                foreach ($ddDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $ddresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            //area regional manager bdo
                                            $bdoData = DB::table('users')
                                                      ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                      ->where('status','1')
                                                      ->select('id')
                                                      ->get();
                                            $bdoresult = array(); 
                                            if (isset($bdoData) && !empty($bdoData)) 
                                            {
                                                $bdoDatass = json_decode(json_encode($bdoData),true);
                                                
                                                foreach ($bdoDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $bdoresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            if (count($bdoresult) > 0) {
                                               $countNotDoneActivity->whereIn('tickets.user_id',$bdoresult);
                                               $countNotDoneActivity->where('tickets.user_type','bdo');
                                            }

                                            if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                                $countNotDoneActivity->orWhereIn('tickets.user_id',$ddresult);
                                                $countNotDoneActivity->where('tickets.user_type','dhanuka_doctor');
                                            }
                                            else
                                            {   
                                                if (count($ddresult) > 0) 
                                                {
                                                    $countNotDoneActivity->whereIn('tickets.user_id',$ddresult);
                                                    $countNotDoneActivity->where('tickets.user_type','dhanuka_doctor');
                                                }
                                            }
                                        }

                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                            $countNotDoneActivity->where('tickets.user_id',$requestdata['filter_FL']);
                                            $countNotDoneActivity->where('tickets.user_type','bdo');
                                        }

                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                           $countNotDoneActivity->orWhere('tickets.user_id',$requestdata['filter_DD']);
                                           $countNotDoneActivity->where('tickets.user_type','dhanuka_doctor');
                                        }

                                        if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                            $from_date=$requestdata['filter_start'];
                                            $to_date=$requestdata['filter_end'];
                                            $countNotDoneActivity->whereBetween('tickets.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                        }

                                        if(!empty($requestdata['filter_crop'])){
                                            $crop=$requestdata['filter_crop'];
                                            $countNotDoneActivity->Where('tickets.crop_1_id', $crop);
                                            $countNotDoneActivity->orWhere('tickets.crop_2_id', $crop);
                                            $countNotDoneActivity->orWhere('tickets.crop_3_id', $crop);
                                        }

                                        if(!empty($requestdata['filter_product'])){
                                            $product=$requestdata['filter_product'];
                                            $countNotDoneActivity->Where('tickets.product_1_id', $product);
                                            $countNotDoneActivity->orWhere('tickets.product_2_id', $product);
                                            $countNotDoneActivity->orWhere('tickets.product_3_id', $product);
                                        }
                $countNotDoneActivity= $countNotDoneActivity->count();
                if ($countNotDoneActivity == null) {
                    $countNotDoneActivity = (int)0;
                }

                $countFarmer = DB::table('farmers')
                               ->join('tickets', 'tickets.ticket_id', '=', 'farmers.ticket_id','left');

                                if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                {
                                    //sales regional manager dd
                                    $ddData = DB::table('dhanuka_doctor')
                                              ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                              ->where('status','1')
                                              ->select('dd_id')
                                              ->get();
                                    $ddresult = array(); 
                                    if (isset($ddData) && !empty($ddData)) 
                                    {
                                        $ddDatass = json_decode(json_encode($ddData),true);
                                        foreach ($ddDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $ddresult = array_merge($ddresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $ddresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    //sales regional manager bdo
                                    $bdoData = DB::table('users')
                                              ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                              ->where('status','1')
                                              ->select('id')
                                              ->get();
                                    $bdoresult = array(); 
                                    if (isset($bdoData) && !empty($bdoData)) 
                                    {
                                        $bdoDatass = json_decode(json_encode($bdoData),true);
                                        
                                        foreach ($bdoDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $bdoresult[$key] = $value; 
                                            } 
                                        } 
                                    }
                                    if (count($bdoresult) > 0) {
                                        $countFarmer->whereIn('tickets.user_id',$bdoresult);
                                        $countFarmer->where('tickets.user_type','bdo');
                                    }

                                    if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                        $countFarmer->orWhereIn('tickets.user_id',$ddresult);
                                        $countFarmer->where('tickets.user_type','dhanuka_doctor');
                                        
                                    }
                                    else
                                    {   
                                        if (count($ddresult) > 0) 
                                        {
                                            $countFarmer->whereIn('tickets.user_id',$ddresult);
                                            $countFarmer->where('tickets.user_type','dhanuka_doctor');
                                        }
                                    }
                                }

                                if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {

                                    //area regional manager dd
                                    $ddData = DB::table('dhanuka_doctor')
                                              ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                              ->where('status','1')
                                              ->select('dd_id')
                                              ->get();
                                    $ddresult = array(); 
                                    if (isset($ddData) && !empty($ddData)) 
                                    {
                                        $ddDatass = json_decode(json_encode($ddData),true);
                                        foreach ($ddDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $ddresult = array_merge($ddresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $ddresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    //area regional manager bdo
                                    $bdoData = DB::table('users')
                                              ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                              ->where('status','1')
                                              ->select('id')
                                              ->get();
                                    $bdoresult = array(); 
                                    if (isset($bdoData) && !empty($bdoData)) 
                                    {
                                        $bdoDatass = json_decode(json_encode($bdoData),true);
                                        
                                        foreach ($bdoDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $bdoresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    if (count($bdoresult) > 0) {
                                        $countFarmer->whereIn('tickets.user_id',$bdoresult);
                                        $countFarmer->where('tickets.user_type','bdo');
                                    }

                                    if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                        $countFarmer->orWhereIn('tickets.user_id',$bdoresult);
                                        $countFarmer->where('tickets.user_type','dhanuka_doctor');
                                        
                                    }
                                    else
                                    {   
                                        if (count($ddresult) > 0) 
                                        {
                                            $countFarmer->whereIn('tickets.user_id',$ddresult);
                                            $countFarmer->where('tickets.user_type','dhanuka_doctor');
                                        }
                                    }
                                }

                               if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                   $countFarmer->where('tickets.user_id',$requestdata['filter_FL']);
                                   $countFarmer->where('tickets.user_type','bdo');
                                }

                                if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                    $countFarmer->orWhere('tickets.user_id',$requestdata['filter_DD']);
                                    $countFarmer->where('tickets.user_type','dhanuka_doctor');
                                }

                                if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                    $from_date=$requestdata['filter_start'];
                                    $to_date=$requestdata['filter_end'];
                                    $countFarmer->whereBetween('tickets.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }

                                if(!empty($requestdata['filter_crop'])){
                                    $crop=$requestdata['filter_crop'];
                                    $countFarmer->Where('tickets.crop_1_id', $crop);
                                    $countFarmer->orWhere('tickets.crop_2_id', $crop);
                                    $countFarmer->orWhere('tickets.crop_3_id', $crop);
                                }

                                if(!empty($requestdata['filter_product'])){
                                    $product=$requestdata['filter_product'];
                                    $countFarmer->Where('tickets.product_1_id', $product);
                                    $countFarmer->orWhere('tickets.product_2_id', $product);
                                    $countFarmer->orWhere('tickets.product_3_id', $product);
                                } 
                $countFarmer = $countFarmer->count();
                if ($countFarmer == null) {
                    $countFarmer = (int)0;
                }

                $countValidatedFarmer = DB::table('tickets');
                                        if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                        {
                                            //sales regional manager dd
                                            $ddData = DB::table('dhanuka_doctor')
                                                      ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                      ->where('status','1')
                                                      ->select('dd_id')
                                                      ->get();
                                            $ddresult = array(); 
                                            if (isset($ddData) && !empty($ddData)) 
                                            {
                                                $ddDatass = json_decode(json_encode($ddData),true);
                                                foreach ($ddDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $ddresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            //sales regional manager bdo
                                            $bdoData = DB::table('users')
                                                      ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                      ->where('status','1')
                                                      ->select('id')
                                                      ->get();
                                            $bdoresult = array(); 
                                            if (isset($bdoData) && !empty($bdoData)) 
                                            {
                                                $bdoDatass = json_decode(json_encode($bdoData),true);
                                                
                                                foreach ($bdoDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $bdoresult[$key] = $value; 
                                                    } 
                                                } 
                                            }
                                            if (count($bdoresult) > 0) {
                                                $countValidatedFarmer->whereIn('tickets.user_id',$bdoresult);
                                                $countValidatedFarmer->where('tickets.user_type','bdo');
                                            }

                                            if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                                $countValidatedFarmer->orWhereIn('tickets.user_id',$ddresult);
                                                $countValidatedFarmer->where('tickets.user_type','dhanuka_doctor');
                                                
                                            }
                                            else
                                            {   
                                                if (count($ddresult) > 0) 
                                                {
                                                    $countValidatedFarmer->whereIn('tickets.user_id',$ddresult);
                                                    $countValidatedFarmer->where('tickets.user_type','dhanuka_doctor');
                                                }
                                            }
                                        }

                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                        {

                                            //area regional manager dd
                                            $ddData = DB::table('dhanuka_doctor')
                                                      ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                      ->where('status','1')
                                                      ->select('dd_id')
                                                      ->get();
                                            $ddresult = array(); 
                                            if (isset($ddData) && !empty($ddData)) 
                                            {
                                                $ddDatass = json_decode(json_encode($ddData),true);
                                                foreach ($ddDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $ddresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            //area regional manager bdo
                                            $bdoData = DB::table('users')
                                                      ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                      ->where('status','1')
                                                      ->select('id')
                                                      ->get();
                                            $bdoresult = array(); 
                                            if (isset($bdoData) && !empty($bdoData)) 
                                            {
                                                $bdoDatass = json_decode(json_encode($bdoData),true);
                                                
                                                foreach ($bdoDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $bdoresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            if (count($bdoresult) > 0) {
                                                $countValidatedFarmer->whereIn('tickets.user_id',$bdoresult);
                                                $countValidatedFarmer->where('tickets.user_type','bdo');
                                            }

                                            if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                                $countValidatedFarmer->orWhereIn('tickets.user_id',$bdoresult);
                                                $countValidatedFarmer->where('tickets.user_type','dhanuka_doctor');
                                                
                                            }
                                            else
                                            {   
                                                if (count($ddresult) > 0) 
                                                {
                                                    $countValidatedFarmer->whereIn('tickets.user_id',$ddresult);
                                                    $countValidatedFarmer->where('tickets.user_type','dhanuka_doctor');
                                                }
                                            }
                                        }
                                        
                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                           $countValidatedFarmer->where('tickets.user_id',$requestdata['filter_FL']);
                                            $countValidatedFarmer->where('tickets.user_type','bdo');
                                        }

                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                           $countValidatedFarmer->orWhere('tickets.user_id',$requestdata['filter_DD']);
                                            $countValidatedFarmer->where('tickets.user_type','dhanuka_doctor');
                                        }

                                        if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                            $from_date=$requestdata['filter_start'];
                                            $to_date=$requestdata['filter_end'];
                                            $countValidatedFarmer->whereBetween('tickets.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                        }

                                        if(!empty($requestdata['filter_crop'])){
                                            $crop=$requestdata['filter_crop'];
                                            $countValidatedFarmer->Where('tickets.crop_1_id', $crop);
                                            $countValidatedFarmer->orWhere('tickets.crop_2_id', $crop);
                                            $countValidatedFarmer->orWhere('tickets.crop_3_id', $crop);
                                        }

                                        if(!empty($requestdata['filter_product'])){
                                            $product=$requestdata['filter_product'];
                                            $countValidatedFarmer->Where('tickets.product_1_id', $product);
                                            $countValidatedFarmer->orWhere('tickets.product_2_id', $product);
                                            $countValidatedFarmer->orWhere('tickets.product_3_id', $product);
                                        } 

                $countValidatedFarmer = $countValidatedFarmer->sum('tickets.validated_farmers');
                if ($countValidatedFarmer == null) {
                    $countValidatedFarmer = (int)0;
                }

                //dhanuka doctor 
                $countCropDD = DB::table('product_crop_relations')
                               ->join('dd_product_categories', 'dd_product_categories.product_crop_category_id', '=', 'product_crop_relations.product_crop_category_id','left');
                                if(!empty($requestdata['filter_crop'])){
                                    $crop=$requestdata['filter_crop'];
                                    $countCropDD->where('product_crop_relations.crop_id', $crop);
                                }

                                if(!empty($requestdata['filter_product'])){
                                    $product=$requestdata['filter_product'];
                                    $countCropDD->where('product_crop_relations.product_id', $product);
                                } 
                $countCropDD = $countCropDD->groupBy('product_crop_relations.product_crop_category_id')->select('dd_product_categories.dd_id')->count();
                if ($countCropDD == null) {
                    $countCropDD = (int)0;
                }
                
                $countDD = DB::table('dhanuka_doctor');
                            if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                            {
                                $countDD->where('dhanuka_doctor.sales_regional_manager_id',$requestdata['filter_RSM']);
                            }
                            if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                            {
                                $countDD->where('dhanuka_doctor.sales_area_manager_id',$requestdata['filter_ASM']);
                            }
                            if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                $countDD->where('dhanuka_doctor.bdo_id',$requestdata['filter_FL']);
                            }

                            if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                $countDD->Where('dhanuka_doctor.dd_id',$requestdata['filter_DD']);
                            }

                            if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                $from_date=$requestdata['filter_start'];
                                $to_date=$requestdata['filter_end'];
                                $countDD->whereBetween('dhanuka_doctor.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }

                            
                $countDD = $countDD->where('dhanuka_doctor.zonal_manager_id',$userData1->zonal_manager_id)->count();
                $ddCount = $countCropDD + $countDD;
                if ($ddCount == null) {
                    $ddCount = (int)0;
                }

                $countDemoRequest = DB::table('demo_request');
                                    if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //sales regional manager dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoRequest->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //area regional manager dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoRequest->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //bdo dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('bdo_id',$requestdata['filter_FL'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoRequest->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                       $countDemoRequest->where('demo_request.dd_id',$requestdata['filter_DD']);
                                    }

                                    if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                        $from_date=$requestdata['filter_start'];
                                        $to_date=$requestdata['filter_end'];
                                        $countDemoRequest->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                    }

                                    if(!empty($requestdata['filter_crop'])){
                                        $crop=$requestdata['filter_crop'];
                                        $countDemoRequest->Where('demo_request.crop_id', $crop);
                                    }

                                    if(!empty($requestdata['filter_product'])){
                                        $product=$requestdata['filter_product'];
                                        $countDemoRequest->Where('demo_request.product_id', $product);
                                    } 
                $countDemoRequest = $countDemoRequest->whereIn('demo_request.dd_id',$ddsresult)->count();
                if ($countDemoRequest == null) {
                    $countDemoRequest = (int)0;
                }
                
                $DemoRequestids = DB::table('demo_request')->whereIn('dd_id',$ddsresult)->get();
                $demoarray = array();
                if (count($DemoRequestids)>0) {
                    $demoDatass = json_decode(json_encode($DemoRequestids),true);
                    foreach ($demoDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                           $demoarray = array_merge($demoarray, array_flatten($value)); 
                        } 
                        else 
                        { 
                           $demoarray[$key] = $value; 
                        } 
                    }
                    //$countDemoFeedback = DB::table('demofeedback')->whereIn('demo_request_id',$demoarray)->groupBy('demo_request_id')->count(); 
                }

                $countDemoFeedback = DB::table('demofeedback')
                                     ->join('demo_request', 'demo_request.demo_request_id', '=', 'demofeedback.demo_request_id','left');
                                    if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //sales regional manager dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoFeedback->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //area regional manager dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoFeedback->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //bdo dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('bdo_id',$requestdata['filter_FL'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoFeedback->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                       $countDemoFeedback->where('demo_request.dd_id',$requestdata['filter_DD']);
                                    }

                                    if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                        $from_date=$requestdata['filter_start'];
                                        $to_date=$requestdata['filter_end'];
                                        $countDemoFeedback->whereBetween('demofeedback.creation_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                    }

                                    if(!empty($requestdata['filter_crop'])){
                                        $crop=$requestdata['filter_crop'];
                                        $countDemoFeedback->Where('demo_request.crop_id', $crop);
                                    }

                                    if(!empty($requestdata['filter_product'])){
                                        $product=$requestdata['filter_product'];
                                        $countDemoFeedback->Where('demo_request.product_id', $product);
                                    } 
                $countDemoFeedback = $countDemoFeedback->whereIn('demofeedback.demo_request_id',$demoarray)->groupBy('demofeedback.demo_request_id')->count();
                if ($countDemoFeedback == null) {
                    $countDemoFeedback = (int)0;
                } 

                $ExtrapolatedFarmer = "NA";
                
            }
            else
            {
                $countActivity = DB::table('tickets');
                                if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                    //sales regional manager dd
                                    $ddData = DB::table('dhanuka_doctor')
                                              ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                              ->where('status','1')
                                              ->select('dd_id')
                                              ->get();
                                    $ddresult = array(); 
                                    if (isset($ddData) && !empty($ddData)) 
                                    {
                                        $ddDatass = json_decode(json_encode($ddData),true);
                                        foreach ($ddDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $ddresult = array_merge($ddresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $ddresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    //sales regional manager bdo
                                    $bdoData = DB::table('users')
                                              ->where('sales_regional_manager_id',$requestdata['filter_ASM'])
                                              ->where('status','1')
                                              ->select('id')
                                              ->get();
                                    $bdoresult = array(); 
                                    if (isset($bdoData) && !empty($bdoData)) 
                                    {
                                        $bdoDatass = json_decode(json_encode($bdoData),true);
                                        
                                        foreach ($bdoDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $bdoresult[$key] = $value; 
                                            } 
                                        } 
                                    }
                                    if (count($bdoresult) > 0) {
                                       $countActivity->whereIn('tickets.user_id',$bdoresult);
                                       $countActivity->where('tickets.user_type','bdo');
                                    }

                                    if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                        $countActivity->orWhereIn('tickets.user_id',$ddresult);
                                        $countActivity->where('tickets.user_type','dhanuka_doctor');
                                    }
                                    else
                                    {   
                                        if (count($ddresult) > 0) 
                                        {
                                            $countActivity->whereIn('tickets.user_id',$ddresult);
                                            $countActivity->where('tickets.user_type','dhanuka_doctor');
                                        }
                                    }
                                }

                                if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {

                                    //Sales area manager dd
                                    $ddData = DB::table('dhanuka_doctor')
                                              ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                              ->where('status','1')
                                              ->select('dd_id')
                                              ->get();
                                    $ddresult = array(); 
                                    if (isset($ddData) && !empty($ddData)) 
                                    {
                                        $ddDatass = json_decode(json_encode($ddData),true);
                                        
                                        foreach ($ddDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $ddresult = array_merge($ddresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $ddresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    //area sales manager bdo
                                    $bdoData = DB::table('users')
                                              ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                              ->where('status','1')
                                              ->select('id')
                                              ->get();
                                    $bdoresult = array(); 
                                    if (isset($bdoData) && !empty($bdoData)) 
                                    {
                                        $bdoDatass = json_decode(json_encode($bdoData),true);
                                        
                                        foreach ($bdoDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $bdoresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    if (count($bdoresult) > 0) {
                                       $countActivity->whereIn('tickets.user_id',$bdoresult);
                                       $countActivity->where('tickets.user_type','bdo');
                                    }

                                    if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                        $countActivity->orWhereIn('tickets.user_id',$ddresult);
                                        $countActivity->where('tickets.user_type','dhanuka_doctor');
                                    }
                                    else
                                    {   
                                        if (count($ddresult) > 0) 
                                        {
                                            $countActivity->where('tickets.user_id',$ddresult);
                                            $countActivity->where('tickets.user_type','dhanuka_doctor');
                                        }
                                    }
                                }

                                if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                   $countActivity->where('tickets.user_id',$requestdata['filter_FL']);
                                    $countActivity->where('tickets.user_type','bdo');
                                }

                                if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                    $countActivity->where('tickets.user_id',$requestdata['filter_DD']);
                                    $countActivity->where('tickets.user_type','dhanuka_doctor');
                                }

                                if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                    $from_date=$requestdata['filter_start'];
                                    $to_date=$requestdata['filter_end'];
                                    $countActivity->whereBetween('tickets.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }

                                if(!empty($requestdata['filter_crop'])){
                                    $crop=$requestdata['filter_crop'];
                                    $countActivity->Where('tickets.crop_1_id', $crop);
                                    $countActivity->orWhere('tickets.crop_2_id', $crop);
                                    $countActivity->orWhere('tickets.crop_3_id', $crop);
                                }

                                if(!empty($requestdata['filter_product'])){
                                    $product=$requestdata['filter_product'];
                                    $countActivity->Where('tickets.product_1_id', $product);
                                    $countActivity->orWhere('tickets.product_2_id', $product);
                                    $countActivity->orWhere('tickets.product_3_id', $product);
                                } 
                $countActivity = $countActivity->count();
                if ($countActivity == null) {
                    $countActivity = (int)0;
                }

                $countNotDoneActivity = DB::table('tickets')
                                        ->where('tickets.status','0');
                                        if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                        {
                                            //sales regional manager dd
                                            $ddData = DB::table('dhanuka_doctor')
                                                      ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                      ->where('status','1')
                                                      ->select('dd_id')
                                                      ->get();
                                            $ddresult = array(); 
                                            if (isset($ddData) && !empty($ddData)) 
                                            {
                                                $ddDatass = json_decode(json_encode($ddData),true);
                                                foreach ($ddDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $ddresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            //sales regional manager bdo
                                            $bdoData = DB::table('users')
                                                      ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                      ->where('status','1')
                                                      ->select('id')
                                                      ->get();
                                            $bdoresult = array(); 
                                            if (isset($bdoData) && !empty($bdoData)) 
                                            {
                                                $bdoDatass = json_decode(json_encode($bdoData),true);
                                                
                                                foreach ($bdoDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $bdoresult[$key] = $value; 
                                                    } 
                                                } 
                                            }
                                            if (count($bdoresult) > 0) {
                                               $countNotDoneActivity->whereIn('tickets.user_id',$bdoresult);
                                               $countNotDoneActivity->where('tickets.user_type','bdo');
                                            }

                                            if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                                $countNotDoneActivity->orWhereIn('tickets.user_id',$ddresult);
                                                $countNotDoneActivity->where('tickets.user_type','dhanuka_doctor');
                                            }
                                            else
                                            {   
                                                if (count($ddresult) > 0) 
                                                {
                                                    $countNotDoneActivity->whereIn('tickets.user_id',$ddresult);
                                                    $countNotDoneActivity->where('tickets.user_type','dhanuka_doctor');
                                                }
                                            }
                                        }

                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {

                                            //area regional manager dd
                                            $ddData = DB::table('dhanuka_doctor')
                                                      ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                      ->where('status','1')
                                                      ->select('dd_id')
                                                      ->get();
                                            $ddresult = array(); 
                                            if (isset($ddData) && !empty($ddData)) 
                                            {
                                                $ddDatass = json_decode(json_encode($ddData),true);
                                                foreach ($ddDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $ddresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            //area regional manager bdo
                                            $bdoData = DB::table('users')
                                                      ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                      ->where('status','1')
                                                      ->select('id')
                                                      ->get();
                                            $bdoresult = array(); 
                                            if (isset($bdoData) && !empty($bdoData)) 
                                            {
                                                $bdoDatass = json_decode(json_encode($bdoData),true);
                                                
                                                foreach ($bdoDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $bdoresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            if (count($bdoresult) > 0) {
                                               $countNotDoneActivity->whereIn('tickets.user_id',$bdoresult);
                                               $countNotDoneActivity->where('tickets.user_type','bdo');
                                            }

                                            if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                                $countNotDoneActivity->orWhereIn('tickets.user_id',$ddresult);
                                                $countNotDoneActivity->where('tickets.user_type','dhanuka_doctor');
                                            }
                                            else
                                            {   
                                                if (count($ddresult) > 0) 
                                                {
                                                    $countNotDoneActivity->whereIn('tickets.user_id',$ddresult);
                                                    $countNotDoneActivity->where('tickets.user_type','dhanuka_doctor');
                                                }
                                            }
                                        }

                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                           $countNotDoneActivity->where('tickets.user_id',$requestdata['filter_FL']);
                                            $countNotDoneActivity->where('tickets.user_type','bdo');
                                        }

                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                           $countNotDoneActivity->orWhere('tickets.user_id',$requestdata['filter_DD']);
                                           $countNotDoneActivity->where('tickets.user_type','dhanuka_doctor');
                                        }

                                        if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                            $from_date=$requestdata['filter_start'];
                                            $to_date=$requestdata['filter_end'];
                                            $countNotDoneActivity->whereBetween('tickets.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                        }

                                        if(!empty($requestdata['filter_crop'])){
                                            $crop=$requestdata['filter_crop'];
                                            $countNotDoneActivity->Where('tickets.crop_1_id', $crop);
                                            $countNotDoneActivity->orWhere('tickets.crop_2_id', $crop);
                                            $countNotDoneActivity->orWhere('tickets.crop_3_id', $crop);
                                        }

                                        if(!empty($requestdata['filter_product'])){
                                            $product=$requestdata['filter_product'];
                                            $countNotDoneActivity->Where('tickets.product_1_id', $product);
                                            $countNotDoneActivity->orWhere('tickets.product_2_id', $product);
                                            $countNotDoneActivity->orWhere('tickets.product_3_id', $product);
                                        }
                $countNotDoneActivity= $countNotDoneActivity->count();
                if ($countNotDoneActivity == null) {
                    $countNotDoneActivity = (int)0;
                }

                $countFarmer = DB::table('farmers')
                               ->join('tickets', 'tickets.ticket_id', '=', 'farmers.ticket_id','left');

                                if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                {
                                    //sales regional manager dd
                                    $ddData = DB::table('dhanuka_doctor')
                                              ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                              ->where('status','1')
                                              ->select('dd_id')
                                              ->get();
                                    $ddresult = array(); 
                                    if (isset($ddData) && !empty($ddData)) 
                                    {
                                        $ddDatass = json_decode(json_encode($ddData),true);
                                        foreach ($ddDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $ddresult = array_merge($ddresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $ddresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    //sales regional manager bdo
                                    $bdoData = DB::table('users')
                                              ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                              ->where('status','1')
                                              ->select('id')
                                              ->get();
                                    $bdoresult = array(); 
                                    if (isset($bdoData) && !empty($bdoData)) 
                                    {
                                        $bdoDatass = json_decode(json_encode($bdoData),true);
                                        
                                        foreach ($bdoDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $bdoresult[$key] = $value; 
                                            } 
                                        } 
                                    }
                                    if (count($bdoresult) > 0) {
                                        $countFarmer->whereIn('tickets.user_id',$bdoresult);
                                        $countFarmer->where('tickets.user_type','bdo');
                                    }

                                    if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                        $countFarmer->orWhereIn('tickets.user_id',$ddresult);
                                        $countFarmer->where('tickets.user_type','dhanuka_doctor');
                                        
                                    }
                                    else
                                    {   
                                        if (count($ddresult) > 0) 
                                        {
                                            $countFarmer->whereIn('tickets.user_id',$ddresult);
                                            $countFarmer->where('tickets.user_type','dhanuka_doctor');
                                        }
                                    }
                                }

                                if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {

                                    //area regional manager dd
                                    $ddData = DB::table('dhanuka_doctor')
                                              ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                              ->where('status','1')
                                              ->select('dd_id')
                                              ->get();
                                    $ddresult = array(); 
                                    if (isset($ddData) && !empty($ddData)) 
                                    {
                                        $ddDatass = json_decode(json_encode($ddData),true);
                                        foreach ($ddDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $ddresult = array_merge($ddresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $ddresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    //area regional manager bdo
                                    $bdoData = DB::table('users')
                                              ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                              ->where('status','1')
                                              ->select('id')
                                              ->get();
                                    $bdoresult = array(); 
                                    if (isset($bdoData) && !empty($bdoData)) 
                                    {
                                        $bdoDatass = json_decode(json_encode($bdoData),true);
                                        
                                        foreach ($bdoDatass as $key => $value) { 
                                            if (is_array($value)) 
                                            { 
                                                $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                            } 
                                            else 
                                            { 
                                                $bdoresult[$key] = $value; 
                                            } 
                                        } 
                                    }

                                    if (count($bdoresult) > 0) {
                                        $countFarmer->whereIn('tickets.user_id',$bdoresult);
                                        $countFarmer->where('tickets.user_type','bdo');
                                    }

                                    if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                        $countFarmer->orWhereIn('tickets.user_id',$bdoresult);
                                        $countFarmer->where('tickets.user_type','dhanuka_doctor');
                                        
                                    }
                                    else
                                    {   
                                        if (count($ddresult) > 0) 
                                        {
                                            $countFarmer->whereIn('tickets.user_id',$ddresult);
                                            $countFarmer->where('tickets.user_type','dhanuka_doctor');
                                        }
                                    }
                                }

                               if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                   $countFarmer->where('tickets.user_id',$requestdata['filter_FL']);
                                   $countFarmer->where('tickets.user_type','bdo');
                                }

                                if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                    $countFarmer->orWhere('tickets.user_id',$requestdata['filter_DD']);
                                    $countFarmer->where('tickets.user_type','dhanuka_doctor');
                                }

                                if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                    $from_date=$requestdata['filter_start'];
                                    $to_date=$requestdata['filter_end'];
                                    $countFarmer->whereBetween('tickets.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }

                                if(!empty($requestdata['filter_crop'])){
                                    $crop=$requestdata['filter_crop'];
                                    $countFarmer->Where('tickets.crop_1_id', $crop);
                                    $countFarmer->orWhere('tickets.crop_2_id', $crop);
                                    $countFarmer->orWhere('tickets.crop_3_id', $crop);
                                }

                                if(!empty($requestdata['filter_product'])){
                                    $product=$requestdata['filter_product'];
                                    $countFarmer->Where('tickets.product_1_id', $product);
                                    $countFarmer->orWhere('tickets.product_2_id', $product);
                                    $countFarmer->orWhere('tickets.product_3_id', $product);
                                } 
                $countFarmer = $countFarmer->count();
                if ($countFarmer == null) {
                    $countFarmer = (int)0;
                }

                $countValidatedFarmer = DB::table('tickets');
                                        if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                        {
                                            //sales regional manager dd
                                            $ddData = DB::table('dhanuka_doctor')
                                                      ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                      ->where('status','1')
                                                      ->select('dd_id')
                                                      ->get();
                                            $ddresult = array(); 
                                            if (isset($ddData) && !empty($ddData)) 
                                            {
                                                $ddDatass = json_decode(json_encode($ddData),true);
                                                foreach ($ddDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $ddresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            //sales regional manager bdo
                                            $bdoData = DB::table('users')
                                                      ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                      ->where('status','1')
                                                      ->select('id')
                                                      ->get();
                                            $bdoresult = array(); 
                                            if (isset($bdoData) && !empty($bdoData)) 
                                            {
                                                $bdoDatass = json_decode(json_encode($bdoData),true);
                                                
                                                foreach ($bdoDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $bdoresult[$key] = $value; 
                                                    } 
                                                } 
                                            }
                                            if (count($bdoresult) > 0) {
                                                $countValidatedFarmer->whereIn('tickets.user_id',$bdoresult);
                                                $countValidatedFarmer->where('tickets.user_type','bdo');
                                            }

                                            if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                                $countValidatedFarmer->orWhereIn('tickets.user_id',$ddresult);
                                                $countValidatedFarmer->where('tickets.user_type','dhanuka_doctor');
                                                
                                            }
                                            else
                                            {   
                                                if (count($ddresult) > 0) 
                                                {
                                                    $countValidatedFarmer->whereIn('tickets.user_id',$ddresult);
                                                    $countValidatedFarmer->where('tickets.user_type','dhanuka_doctor');
                                                }
                                            }
                                        }

                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                        {

                                            //area regional manager dd
                                            $ddData = DB::table('dhanuka_doctor')
                                                      ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                      ->where('status','1')
                                                      ->select('dd_id')
                                                      ->get();
                                            $ddresult = array(); 
                                            if (isset($ddData) && !empty($ddData)) 
                                            {
                                                $ddDatass = json_decode(json_encode($ddData),true);
                                                foreach ($ddDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $ddresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            //area regional manager bdo
                                            $bdoData = DB::table('users')
                                                      ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                      ->where('status','1')
                                                      ->select('id')
                                                      ->get();
                                            $bdoresult = array(); 
                                            if (isset($bdoData) && !empty($bdoData)) 
                                            {
                                                $bdoDatass = json_decode(json_encode($bdoData),true);
                                                
                                                foreach ($bdoDatass as $key => $value) { 
                                                    if (is_array($value)) 
                                                    { 
                                                        $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                                                    } 
                                                    else 
                                                    { 
                                                        $bdoresult[$key] = $value; 
                                                    } 
                                                } 
                                            }

                                            if (count($bdoresult) > 0) {
                                                $countValidatedFarmer->whereIn('tickets.user_id',$bdoresult);
                                                $countValidatedFarmer->where('tickets.user_type','bdo');
                                            }

                                            if (count($ddresult) > 0 && count($bdoresult) > 0) {
                                                $countValidatedFarmer->orWhereIn('tickets.user_id',$bdoresult);
                                                $countValidatedFarmer->where('tickets.user_type','dhanuka_doctor');
                                                
                                            }
                                            else
                                            {   
                                                if (count($ddresult) > 0) 
                                                {
                                                    $countValidatedFarmer->whereIn('tickets.user_id',$ddresult);
                                                    $countValidatedFarmer->where('tickets.user_type','dhanuka_doctor');
                                                }
                                            }
                                        }
                                        
                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                           $countValidatedFarmer->where('tickets.user_id',$requestdata['filter_FL']);
                                            $countValidatedFarmer->where('tickets.user_type','bdo');
                                        }

                                        if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                           $countValidatedFarmer->orWhere('tickets.user_id',$requestdata['filter_DD']);
                                            $countValidatedFarmer->where('tickets.user_type','dhanuka_doctor');
                                        }

                                        if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                            $from_date=$requestdata['filter_start'];
                                            $to_date=$requestdata['filter_end'];
                                            $countValidatedFarmer->whereBetween('tickets.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                        }

                                        if(!empty($requestdata['filter_crop'])){
                                            $crop=$requestdata['filter_crop'];
                                            $countValidatedFarmer->Where('tickets.crop_1_id', $crop);
                                            $countValidatedFarmer->orWhere('tickets.crop_2_id', $crop);
                                            $countValidatedFarmer->orWhere('tickets.crop_3_id', $crop);
                                        }

                                        if(!empty($requestdata['filter_product'])){
                                            $product=$requestdata['filter_product'];
                                            $countValidatedFarmer->Where('tickets.product_1_id', $product);
                                            $countValidatedFarmer->orWhere('tickets.product_2_id', $product);
                                            $countValidatedFarmer->orWhere('tickets.product_3_id', $product);
                                        } 

                $countValidatedFarmer = $countValidatedFarmer->sum('tickets.validated_farmers');
                if ($countValidatedFarmer == null) {
                    $countValidatedFarmer = (int)0;
                }

                //dhanuka doctor 
                $countCropDD = DB::table('product_crop_relations')
                               ->join('dd_product_categories', 'dd_product_categories.product_crop_category_id', '=', 'product_crop_relations.product_crop_category_id','left');
                                if(!empty($requestdata['filter_crop'])){
                                    $crop=$requestdata['filter_crop'];
                                    $countCropDD->where('product_crop_relations.crop_id', $crop);
                                }

                                if(!empty($requestdata['filter_product'])){
                                    $product=$requestdata['filter_product'];
                                    $countCropDD->where('product_crop_relations.product_id', $product);
                                } 
                $countCropDD = $countCropDD->groupBy('product_crop_relations.product_crop_category_id')->select('dd_product_categories.dd_id')->count();
                if ($countCropDD == null) {
                    $countCropDD = (int)0;
                }
                
                $countDD = DB::table('dhanuka_doctor');
                            if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                            {
                                $countDD->where('dhanuka_doctor.sales_regional_manager_id',$requestdata['filter_RSM']);
                            }
                            if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                            {
                                $countDD->where('dhanuka_doctor.sales_area_manager_id',$requestdata['filter_ASM']);
                            }
                            if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) {
                                $countDD->where('dhanuka_doctor.bdo_id',$requestdata['filter_FL']);
                            }

                            if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                $countDD->Where('dhanuka_doctor.dd_id',$requestdata['filter_DD']);
                            }

                            if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                $from_date=$requestdata['filter_start'];
                                $to_date=$requestdata['filter_end'];
                                $countDD->whereBetween('dhanuka_doctor.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }

                            
                $countDD = $countDD->count();
                $ddCount = $countCropDD + $countDD;
                if ($ddCount == null) {
                    $ddCount = (int)0;
                }

                $countDemoRequest = DB::table('demo_request');
                                    if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //sales regional manager dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoRequest->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //area regional manager dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoRequest->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //bdo dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('bdo_id',$requestdata['filter_FL'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoRequest->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                       $countDemoRequest->where('demo_request.dd_id',$requestdata['filter_DD']);
                                    }

                                    if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                        $from_date=$requestdata['filter_start'];
                                        $to_date=$requestdata['filter_end'];
                                        $countDemoRequest->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                    }

                                    if(!empty($requestdata['filter_crop'])){
                                        $crop=$requestdata['filter_crop'];
                                        $countDemoRequest->Where('demo_request.crop_id', $crop);
                                    }

                                    if(!empty($requestdata['filter_product'])){
                                        $product=$requestdata['filter_product'];
                                        $countDemoRequest->Where('demo_request.product_id', $product);
                                    } 
                $countDemoRequest = $countDemoRequest->count();
                if ($countDemoRequest == null) {
                    $countDemoRequest = (int)0;
                }
                
                $countDemoFeedback = DB::table('demofeedback')
                                     ->join('demo_request', 'demo_request.demo_request_id', '=', 'demofeedback.demo_request_id','left');
                                    if (!empty($requestdata['filter_RSM']) && empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //sales regional manager dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('sales_regional_manager_id',$requestdata['filter_RSM'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoFeedback->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //area regional manager dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('sales_area_manager_id',$requestdata['filter_ASM'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoFeedback->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && empty($requestdata['filter_DD'])) 
                                    {
                                        //bdo dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->where('bdo_id',$requestdata['filter_FL'])
                                                  ->where('status','1')
                                                  ->select('dd_id')
                                                  ->get();
                                        $ddresult = array(); 
                                        if (isset($ddData) && !empty($ddData)) 
                                        {
                                            $ddDatass = json_decode(json_encode($ddData),true);
                                            foreach ($ddDatass as $key => $value) { 
                                                if (is_array($value)) 
                                                { 
                                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                                } 
                                                else 
                                                { 
                                                    $ddresult[$key] = $value; 
                                                } 
                                            } 
                                        }

                                        if (count($ddresult) > 0) 
                                        {
                                            $countDemoFeedback->whereIn('demo_request.dd_id',$ddresult);
                                        }
                                    }

                                    if (!empty($requestdata['filter_RSM']) && !empty($requestdata['filter_ASM']) && !empty($requestdata['filter_FL']) && !empty($requestdata['filter_DD'])) {
                                       $countDemoFeedback->where('demo_request.dd_id',$requestdata['filter_DD']);
                                    }

                                    if (!empty($requestdata['filter_start']) && !empty($requestdata['filter_end'])) {
                                        $from_date=$requestdata['filter_start'];
                                        $to_date=$requestdata['filter_end'];
                                        $countDemoFeedback->whereBetween('demofeedback.creation_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                    }

                                    if(!empty($requestdata['filter_crop'])){
                                        $crop=$requestdata['filter_crop'];
                                        $countDemoFeedback->Where('demo_request.crop_id', $crop);
                                    }

                                    if(!empty($requestdata['filter_product'])){
                                        $product=$requestdata['filter_product'];
                                        $countDemoFeedback->Where('demo_request.product_id', $product);
                                    } 
                $countDemoFeedback = $countDemoFeedback->groupBy('demofeedback.demo_request_id')->count();
                if ($countDemoFeedback == null) {
                    $countDemoFeedback = (int)0;
                } 
                $ExtrapolatedFarmer = $countActivity;
            }
                       

            $merged = array('countActivity' => $countActivity, 'countNotDoneActivity' => $countNotDoneActivity, 'countFarmer' => $countFarmer, 'countDD' => $ddCount, 'countDemoRequest' => $countDemoRequest, 'countDemoFeedback' => $countDemoFeedback, 'countValidatedFarmer' => $countValidatedFarmer, 'ExtrapolatedFarmer' => $ExtrapolatedFarmer);
            
            echo json_encode($merged); die;
        
        }

        public function getAddReminder()
        {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $stageList = "SELECT stage_id,stage_name FROM stages WHERE  `status` =  '1'"; 
            $stageList  = collect(DB::select($stageList));
            $cropList="SELECT crop_id,crop FROM  `crops` WHERE  `status` =  '1'"; 
            $cropList  = collect(DB::select($cropList));
            $data['stageList'] = $stageList;
            $data['cropList'] = $cropList;
            return View::make('backend.reminder.addreminder',compact('data'));
        }
        
        public function postReminderProcess(Request $request)
        {
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $my_data        = Input::all();    
                $rules  = array(
                                'crop'      => 'required',
                                'product'   => 'required',
                                'stage'     => 'required',
                                'spray'     => 'required',
                                'review'    => 'required'
                            );
                $validator  = Validator::make($my_data, $rules);
                if ($validator->fails()) {
                    return Redirect::back()->withInput()->withErrors($validator);
                }
                
                $crop           = $my_data['crop'];
                $product        = $my_data['product'];
                $stage          = $my_data['stage'];
                $spray          = $my_data['spray'];
                $review         = $my_data['review'];
                $Date           = date('Y-m-d');
                $reviewPlusDay  = date('Y-m-d', strtotime($Date. ' + ' . $review .'days'));
                $insert_reminder    = array(       
                                        'crop_id'           => $crop,
                                        'product_id'        => $product,
                                        'stage_id'          => $stage,
                                        'spray'             => $spray,
                                        'number_day_review' => $review,
                                        'date'              => $reviewPlusDay,
                                        'created_at'        => date('Y-m-d H:i:s'),
                                        'updated_at'        => date('Y-m-d H:i:s')
                                        ); 
                $insert_reminder    = Reminder::firstOrCreate($insert_reminder);
                $result         = $insert_reminder->reminder_id;
                if($result) {
                    Session::flash('message', "Reminder Successfully Created.");
                    return redirect('admin/reminder-list');
                }
                else 
                {
                    Session::flash('message', "An error occurred. Please try again later.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            }
        }  


        public function getReminderList()
        {
          return view('backend.reminder.reminderlist');       
        }

        public function postReminderDataList(){
          
            $reminderList="SELECT `r`.`reminder_id`,`c`.`crop`, `p`.`product`, `s`.`stage_name`, `r`.`spray`, `r`.`created_at` from reminder as r LEFT JOIN `crops` AS c ON `c`.`crop_id` = `r`.`crop_id` LEFT JOIN `products` AS p ON `p`.`product_id` = `r`.`product_id` LEFT JOIN `stages` AS s ON `s`.`stage_id` = `r`.`stage_id` WHERE `r`.`status` = '1'";
            $reminderList  = collect(DB::select($reminderList));
            return Datatables::of($reminderList)->make();                             
                                       
        }   

        public function postReminderDelete(Request $request){
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $my_data= Request::all();        
                $rules  = array(
                                '_ticket'    => 'required|numeric'
                            );
                $validator  = Validator::make($my_data, $rules);
                if ($validator->fails()) {
                    echo '0';
                }
                else {
                    $reminder_id     = $my_data['_ticket'];
                    $update_reminder = DB::table('reminder')
                                        ->where('reminder_id', $reminder_id)
                                        ->update(['status' => '2']);
                    if ($update_reminder) {
                        echo 1;
                    } else {
                        echo 2;
                    }
                }
            }
        }

        public function getEditReminder($id){
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $stageList = "SELECT stage_id,stage_name FROM stages WHERE  `status` =  '1'"; 
            $stageList  = collect(DB::select($stageList));
            $cropList="SELECT crop_id,crop FROM  `crops` WHERE  `status` =  '1'"; 
            $cropList  = collect(DB::select($cropList));
            $productList="SELECT product_id,product FROM  `products` WHERE  `status` =  '1'"; 
            $productList  = collect(DB::select($productList));
            $data['stageList'] = $stageList;
            $data['cropList'] = $cropList;
            $data['productList'] = $productList;
            //return View::make('backend.reminder.addreminder',compact('data'));

            $reminder = DB::table('reminder')
                        ->join('products','products.product_id', '=', 'reminder.product_id','left')
                        ->join('stages','stages.stage_id', '=', 'reminder.stage_id','left')
                        ->where('reminder.reminder_id', $id)
                        ->select('reminder.*','products.product','stages.stage_name')
                        ->first();
            $data['reminder'] = $reminder;
            return view('backend.reminder.editreminder',compact('data'));
            //return $distributorList;                             
        }
        public function postUpdateReminder(Request $request){
            
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            if ($userData) {
                $my_data= Request::all();
                $reminder_id=Request::input('reminder_id');
                DB::table('reminder')->where('reminder_id',$reminder_id)->update(['crop_id'=>$my_data['crop'],'product_id'=>$my_data['product'],'stage_id'=>$my_data['stage'],'spray' => $my_data['spray'],'number_day_review'=> $my_data['review'],'updated_at'=>date("Y-m-d H:i:s")]);
                Session::flash('message', "Reminder Successfully Updated.");
                return redirect('admin/reminder-list');
                
            }else{
                return redirect('admin/edit-reminder/'.$reminder_id);
                
            }

        }

        public function getProductWiseInvitedFarmer() {
                $sess = \Session::get('adminid');
                if (!isset($sess) || $sess == "") {
                    return redirect('/');
                }
                return View::make('backend.farmer.productwiseinvitedfarmer');
        }
        public function postAllproductwiseinvitedfarmer(Request $request) {
            //print_r(Request::all()); exit;
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "admin/product-wise-invited-farmer",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);
            
            //print_r(Request::all()); exit;
            $value          = Input::all();
            $limit          = $value['length'];
            $start          = $value['start'];
            $search_array   = $value['search'];     // value & regax
            $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
            
            $order_array    = $value['order'][0]['column'];
            $dir            = $value['order'][0]['dir'];
            
            $start_date     = $value['start_date'];
            $end_date       = $value['end_date'];
            
            if(empty($search_array['value']))
            {            
                $sorting        ='';            
                $datesearch     = '';

                if($start_date!='' && $end_date!='') {
                    $datesearch = "DATE(F.creation_time) >= DATE('".$start_date."') AND DATE(F.creation_time) <= DATE('".$end_date."')";
                }
                else {
                    $datesearch = "(1=1)";
                }

                $query          =   " 
                                    SELECT                                 
                                        @a:=@a+1  as serial_number,
                                        A.*                                
                                    FROM
                                        (
                                        SELECT 
                                            T.ticket_no, 
                                            IF( ddss.ECN = '', CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.ECN, ''),
                                                NULLIF(rm.ECN, ''),
                                                NULLIF(am.ECN, ''),
                                                NULLIF(srm.ECN, ''),
                                                NULLIF(sam.ECN, ''),
                                                NULLIF(u.ECN, ''),
                                                NULLIF(dd.ECN, '')
                                            ),  ddss.ECN) as ecn,
                                            IF( ddss.name = '', CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.zonal_manager_name, ''),
                                                NULLIF(rm.regional_manager_name, ''),
                                                NULLIF(am.area_manager_name, ''),
                                                NULLIF(srm.sales_regional_manager_name, ''),
                                                NULLIF(sam.sales_area_manager_name, ''),
                                                NULLIF(u.name, ''),
                                                NULLIF(dd.name, '')
                                            ),  ddss.name) AS bdo,

                                            IF( ddss.ECN = '', UCASE(pa.user_type), 'DD') AS user_type,
                                            
                                            L.language,
                                            
                                            P1.product AS p1_product, 
                                            C1.crop AS c1_crop,
                                            P2.product AS p2_product, 
                                            C2.crop AS c2_crop, 
                                            
                                            P3.product AS p3_product, 
                                            C3.crop AS c3_crop,                                                
                                            ST.state,
                                            DT.district,
                                            
                                            TS.tehsil,
                                            V.village,                                        
                                            F.contact_number,
                                            F.farmer_name,
                                            DATE_FORMAT(F.creation_time, '%Y-%m-%d, %h:%i %p') as c_datetime_format
                                            
                                            
                                        FROM        invite_farmer     F

                                        LEFT JOIN   plan_atp       pa  ON F.plan_atp_id=pa.plan_atp_id
                                        LEFT JOIN   tickets        T   ON pa.plan_atp_id=T.plan_atp_id

                                        
                                        LEFT JOIN   users                   u   ON pa.created_by_user_id=u.id AND pa.user_type='bdo'
                                        LEFT JOIN   zonal_managers          zm  ON pa.created_by_user_id=zm.zonal_manager_id AND pa.user_type='zonal'
                                        LEFT JOIN   regional_managers       rm  ON pa.created_by_user_id=rm.regional_manager_id AND pa.user_type='regional'
                                        LEFT JOIN   area_managers           am  ON pa.created_by_user_id=am.area_manager_id AND pa.user_type='area'
                                        LEFT JOIN   sales_regional_managers srm ON pa.created_by_user_id=srm.sales_regional_manager_id AND pa.user_type='sales_regional'
                                        LEFT JOIN   sales_area_managers     sam ON pa.created_by_user_id=sam.sales_area_manager_id AND pa.user_type='sales_area'
                                        LEFT JOIN   dhanuka_doctor dd   ON pa.created_by_user_id=dd.dd_id AND pa.user_type='dhanuka_doctor'
                                        
                                        LEFT JOIN   dhanuka_doctor ddss   ON pa.dd_id=ddss.dd_id

                                        LEFT JOIN   products    P1  ON pa.product_id=P1.product_id
                                        LEFT JOIN   products    P2  ON pa.product_id_2=P2.product_id
                                        LEFT JOIN   products    P3  ON pa.product_id_3=P3.product_id 

                                        LEFT JOIN   crops           C1  ON pa.crop_id=C1.crop_id
                                        LEFT JOIN   crops           C2  ON pa.other_crop_1=C2.crop_id
                                        LEFT JOIN   crops           C3  ON pa.other_crop_2=C3.crop_id 
                                                                            
                                        LEFT JOIN   states          ST  ON pa.state_id=ST.state_id
                                        LEFT JOIN   districts       DT  ON pa.district_id=DT.district_id
                                        LEFT JOIN   tehsils         TS  ON pa.tehsil_id=TS.tehsil_id
                                        LEFT JOIN   villages        V   ON pa.village_id=V.village_id                  
                                        
                                        LEFT JOIN   languages       L   ON u.language_id=L.id
                                        
                                        WHERE 
                                            (1=1) AND
                                            ".$datesearch." 
                                        GROUP BY F.contact_number
                                        )   A,
                                        (select @a:=0) initvars";

                                       // LIMIT ". $start." , ".$limit."
             ///  echo $query; exit;
                $product_farmer    = collect(DB::select($query)); 
                //print_r($product_farmer);die;
                // if (count($product_farmer)>0) {
                //     foreach ($product_farmer as $key => $value) {
                //         $ticketData = DB::table('tickets')
                //                       ->where('ticket_no','=',$product_farmer[$key]->ticket_no)
                //                       ->select('ticket_id')
                //                       ->get();
                //         if (count($ticketData)>0) {
                //             $farmersData = DB::table('farmers')
                //                            ->where('mobile', '=', $product_farmer[$key]->contact_number)
                //                            ->where('ticket_id','=',$ticketData[0]->ticket_id)
                //                            ->select('validation_status')
                //                            ->get();
                //             if (count($farmersData)>0) {
                //                 if ($farmersData[0]->validation_status == 'Validated') {
                //                     $product_farmer[$key]->attended = "YES";
                //                 }
                //                 else
                //                 {
                //                     $product_farmer[$key]->attended = "NO";
                //                 }
                //             }else{
                //                 $product_farmer[$key]->attended = "NO";
                //             }
                //         }
                //         else
                //         {
                //             $product_farmer[$key]->attended = "NO";
                //         }
                        
                //     }
                // }       
                //print_r($product_farmer);die;
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return Datatables::of($product_farmer)->make();
            }
            else {            
                $search         = $search_array['value']; 
                $sorting        = '';
                $datesearch     = '';
                $datesearch     = '1=1';

                if($start_date!='' && $end_date!='') {
                    $datesearch.= " and  DATE(F.creation_time) >= DATE('".$start_date."') AND DATE(F.creation_time) <= DATE('".$end_date."')";
                }
                
                $query  =   " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT 
                                    T.ticket_no, 
                                    IF( ddss.ECN = '', CONCAT_WS(
                                        '\n', 
                                        NULLIF(zm.ECN, ''),
                                        NULLIF(rm.ECN, ''),
                                        NULLIF(am.ECN, ''),
                                        NULLIF(srm.ECN, ''),
                                        NULLIF(sam.ECN, ''),
                                        NULLIF(u.ECN, ''),
                                        NULLIF(dd.ECN, '')
                                    ),  ddss.ECN) as ecn,
                                    IF( ddss.name = '', CONCAT_WS(
                                        '\n', 
                                        NULLIF(zm.zonal_manager_name, ''),
                                        NULLIF(rm.regional_manager_name, ''),
                                        NULLIF(am.area_manager_name, ''),
                                        NULLIF(srm.sales_regional_manager_name, ''),
                                        NULLIF(sam.sales_area_manager_name, ''),
                                        NULLIF(u.name, ''),
                                        NULLIF(dd.name, '')
                                    ),  ddss.name) AS bdo,

                                    IF( ddss.ECN = '', UCASE(pa.user_type), 'DD') AS user_type,
                                    
                                    L.language,
                                    
                                    P1.product AS p1_product, 
                                    C1.crop AS c1_crop,
                                    P2.product AS p2_product, 
                                    C2.crop AS c2_crop, 
                                    
                                    P3.product AS p3_product, 
                                    C3.crop AS c3_crop,                                                
                                    ST.state,
                                    DT.district,
                                    
                                    TS.tehsil,
                                    V.village,                                        
                                    F.contact_number,
                                    F.farmer_name,
                                    DATE_FORMAT(F.creation_time, '%Y-%m-%d, %h:%i %p') as c_datetime_format
                                    
                                    
                                FROM        invite_farmer     F

                                LEFT JOIN   plan_atp        pa  ON F.plan_atp_id=pa.plan_atp_id 
                                LEFT JOIN   tickets         T   ON pa.plan_atp_id=T.plan_atp_id

                                
                                LEFT JOIN   users                   u   ON pa.created_by_user_id=u.id AND pa.user_type='bdo'
                                LEFT JOIN   zonal_managers          zm  ON pa.created_by_user_id=zm.zonal_manager_id AND pa.user_type='zonal'
                                LEFT JOIN   regional_managers       rm  ON pa.created_by_user_id=rm.regional_manager_id AND pa.user_type='regional'
                                LEFT JOIN   area_managers           am  ON pa.created_by_user_id=am.area_manager_id AND pa.user_type='area'
                                LEFT JOIN   sales_regional_managers srm ON pa.created_by_user_id=srm.sales_regional_manager_id AND pa.user_type='sales_regional'
                                LEFT JOIN   sales_area_managers     sam ON pa.created_by_user_id=sam.sales_area_manager_id AND pa.user_type='sales_area'
                                LEFT JOIN   dhanuka_doctor dd   ON pa.created_by_user_id=dd.dd_id AND pa.user_type='dhanuka_doctor'
                                
                                LEFT JOIN   products    P1  ON pa.product_id=P1.product_id
                                LEFT JOIN   products    P2  ON pa.product_id_2=P2.product_id
                                LEFT JOIN   products    P3  ON pa.product_id_3=P3.product_id 

                                LEFT JOIN   crops       C1  ON pa.crop_id=C1.crop_id
                                LEFT JOIN   crops       C2  ON pa.other_crop_1=C2.crop_id
                                LEFT JOIN   crops       C3  ON pa.other_crop_2=C3.crop_id 
                                                                    
                                LEFT JOIN   states      ST  ON pa.state_id=ST.state_id
                                LEFT JOIN   districts   DT  ON pa.district_id=DT.district_id
                                LEFT JOIN   tehsils     TS  ON pa.tehsil_id=TS.tehsil_id
                                LEFT JOIN   villages    V   ON pa.village_id=V.village_id                  
                                
                                LEFT JOIN   languages   L   ON u.language_id=L.id
                                
                                WHERE 
                                       
                                    (".$datesearch.")       AND
                                    (
                                        P1.product                          LIKE '%".$search."%'    OR
                                        P2.product                          LIKE '%".$search."%'    OR
                                        P3.product                          LIKE '%".$search."%'    OR
                                        T.ticket_no                         LIKE '%".$search."%'    OR
                                        zm.zonal_manager_name               LIKE '%".$search."%'    OR
                                        rm.regional_manager_name            LIKE '%".$search."%'    OR
                                        am.area_manager_name                LIKE '%".$search."%'    OR
                                        srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                                        sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                                        u.name                              LIKE '%".$search."%'    OR
                                        F.farmer_name                       LIKE '%".$search."%'    OR
                                        F.contact_number                    LIKE '%".$search."%'    
                                    )
                                GROUP BY F.contact_number            
                                ) A,
                                (select @a:=0) initvars
                            ".$sorting ;
                //echo $query;die;
                $product_farmer    = collect(DB::select($query));
                // if (count($product_farmer)>0) {
                //     foreach ($product_farmer as $key => $value) {
                //         $ticketData = DB::table('tickets')
                //                       ->where('ticket_no','=',$product_farmer[$key]->ticket_no)
                //                       ->select('ticket_id')
                //                       ->get();
                //         if (count($ticketData)>0) {
                //             $farmersData = DB::table('farmers')
                //                            ->where('mobile', '=', $product_farmer[$key]->contact_number)
                //                            ->where('ticket_id','=',$ticketData[0]->ticket_id)
                //                            ->select('validation_status')
                //                            ->get();
                //             if (count($farmersData)>0) {
                //                 if ($farmersData[0]->validation_status == 'Validated') {
                //                     $product_farmer[$key]->attended = "YES";
                //                 }
                //                 else
                //                 {
                //                     $product_farmer[$key]->attended = "NO";
                //                 }
                //             }else{
                //                 $product_farmer[$key]->attended = "NO";
                //             }
                //         }
                //         else
                //         {
                //             $product_farmer[$key]->attended = "NO";
                //         }
                        
                //     }
                // }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return Datatables::of($product_farmer)->make();
            }        
        } 


        public function getAcpfarmer() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.farmer.acpfarmer');
        }
        public function postAllacpfarmer(Request $request) {
            //print_r(Request::all()); exit;
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "admin/acpfarmer",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);
            //print_r(Request::all()); exit;
            $value          = Input::all();
            $limit          = $value['length'];
            $start          = $value['start'];
            $search_array   = $value['search'];     // value & regax
            $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
            $order_array    = $value['order'][0]['column'];
            $dir            = $value['order'][0]['dir'];
            $start_date     = $value['start_date'];
            $end_date       = $value['end_date'];
            //$base_url1       = url('/assets/farmer');
            $base_url1 = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer";
            if(empty($search_array['value']))
            {
                $sorting        ='';            
                $datesearch     = '';

                if($start_date!='' && $end_date!='') {
                    $datesearch = "DATE(vf.created_at) >= DATE('".$start_date."') AND DATE(vf.created_at) <= DATE('".$end_date."')";
                }
                else {
                    $datesearch = "(1=1)";
                }
                $query  =   " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT 
                                    vf.farmer_id as farmerId,
                                    dd.name as dd_name,
                                    vf.farmer_name as farmerName,
                                    vf.father_name as fatherName,
                                    sum(vfc.acres) as acres,
                                    vf.contact_number as contactNumber,
                                    vf.alternate_contact_number as alternateContactNumber,
                                    CONCAT('".$base_url1."/', vf.farmer_image) as farmarImage,
                                    s.state,
                                    t.tehsil,
                                    d.district,
                                    (CASE WHEN vf.call_status='1' THEN 'Yes' ELSE 'No' END) as call_status,
                                    v.village,
                                    v.village_id,
                                    vf.farmer_lat as farmerLat,
                                    vf.farmer_long as farmerLong,
                                    GROUP_CONCAT(c.crop SEPARATOR ',\n')  AS crops,
                                    dd.ECN as ECN,
                                    GROUP_CONCAT(vfc.acres SEPARATOR ',\n')  AS crop_acres

                                FROM village_farmers   vf
                                LEFT JOIN   village_farmer_crop   vfc ON  vfc.farmer_id = vf.farmer_id 
                                LEFT JOIN   villages  v  ON v.village_id = vf.village_id 
                                LEFT JOIN   dhanuka_doctor dd   ON vf.dd_id = dd.dd_id 
                                LEFT JOIN   tehsils  t  ON t.tehsil_id = v.tehsil_id 
                                LEFT JOIN   districts d  ON d.district_id = v.district_id 
                                LEFT JOIN   states s ON s.state_id = d.state_id
                                LEFT JOIN   crops c ON c.crop_id = vfc.crop_id 
                                WHERE 
                                    (vf.status='1') AND
                                    ".$datesearch." 
                                GROUP BY vfc.farmer_id
                                )   A,
                                (select @a:=0) initvars";

                                // LIMIT ". $start." , ".$limit."
                                //  echo $query; exit;
                $acp_farmer    = collect(DB::select($query));   //print_r($acp_farmer);die;
                // echo $query->toSql();die;

                foreach ($acp_farmer as $key => $value) {
                    $markVisit=DB::table('mark_visits')->where('farmer_id',$acp_farmer[$key]->farmerId)->count();
                    $acp_farmer[$key]->acpVisit=$markVisit;
                    // $acp_farmer[$key]->acpVisit= 0;
                    $village_id = $value->village_id;
                    if($village_id) {
                        $lat = $value->farmerLat;
                        $lon = $value->farmerLong;
                        if((is_numeric($lat) == true)  && (is_numeric($lon) == true)) {
                            $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                            if($distanceData !==0) {
                                $expDistanceData = explode("~", $distanceData);
                                $acp_farmer[$key]->isValidate = $expDistanceData[1];
                                $acp_farmer[$key]->distance = $expDistanceData[0];
                            } else {
                                $acp_farmer[$key]->isValidate = "No";
                                $acp_farmer[$key]->distance = "0";
                            }
                        } else {
                            $acp_farmer[$key]->isValidate = "No";
                            $acp_farmer[$key]->distance = "0";
                        }
                    } else {
                        $acp_farmer[$key]->isValidate = "No";
                        $acp_farmer[$key]->distance = "0";
                    }
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return Datatables::of($acp_farmer)->make();
            }
            else 
            {            
                $search         = $search_array['value']; 
                $sorting        = '';
                $datesearch     = '';
                $datesearch = " (vf.status='1') ";

                if($start_date!='' && $end_date!='') {
                    $datesearch.= " and  DATE(vf.created_at) >= DATE('".$start_date."') AND DATE(vf.created_at) <= DATE('".$end_date."')";
                }
                 
                $query  =   "   SELECT                                 
                                    @a:=@a+1  as serial_number,
                                    A.*                                
                                    FROM
                                        (
                                        SELECT 
                                            vf.farmer_id as farmerId,
                                            dd.name as dd_name,
                                            vf.farmer_name as farmerName,
                                            vf.father_name as fatherName,
                                            sum(vfc.acres) as acres,
                                            vf.contact_number as contactNumber,
                                            vf.alternate_contact_number as alternateContactNumber,
                                            CONCAT('".$base_url1."/', vf.farmer_image) as farmarImage,
                                            s.state,
                                            t.tehsil,
                                            d.district,
                                            (CASE WHEN vf.call_status='1' THEN 'Yes' ELSE 'No' END) as call_status,
                                            v.village,
                                            v.village_id,
                                            vf.farmer_lat as farmerLat,
                                            vf.farmer_long as farmerLong,
                                            GROUP_CONCAT(c.crop SEPARATOR ',\n')  AS crops,
                                            dd.ECN as ECN,
                                            GROUP_CONCAT(vfc.acres SEPARATOR ',\n')  AS crop_acres

                                        FROM        village_farmers   vf

                                        LEFT JOIN   village_farmer_crop   vfc ON  vfc.farmer_id = vf.farmer_id 
                                        LEFT JOIN   villages  v  ON v.village_id = vf.village_id 
                                        LEFT JOIN   dhanuka_doctor dd   ON vf.dd_id = dd.dd_id 
                                        LEFT JOIN   tehsils  t  ON t.tehsil_id = v.tehsil_id 
                                        LEFT JOIN   districts d  ON d.district_id = v.district_id 
                                        LEFT JOIN   states s ON s.state_id = d.state_id 
                                        LEFT JOIN   crops c ON c.crop_id = vfc.crop_id 
                                        
                                        WHERE 
                                        (vf.status='1') AND
                                        (".$datesearch.")       AND
                                        (
                                            vf.farmer_name                LIKE '%".$search."%'    OR
                                            dd.name                       LIKE '%".$search."%'    OR
                                            v.village                     LIKE '%".$search."%'    OR
                                            vf.contact_number             LIKE '%".$search."%'    OR
                                            vf.alternate_contact_number   LIKE '%".$search."%'      
                                        ) 

                                        GROUP BY vfc.farmer_id
                                        )   A,
                                        (select @a:=0) initvars ".$sorting ;
                $acp_farmer    = collect(DB::select($query));
                foreach ($acp_farmer as $key => $value) {
                   $markVisit=DB::table('mark_visits')->where('farmer_id',$acp_farmer[$key]->farmerId)->count();
                   $acp_farmer[$key]->acpVisit=$markVisit;
                   $village_id = $value->village_id;
                   if($village_id) {
                        $lat = $value->farmerLat;
                        $lon = $value->farmerLong;
                        if((is_numeric($lat) == true)  && (is_numeric($lon) == true)) {
                            $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                            if($distanceData !==0) {
                                $expDistanceData = explode("~", $distanceData);
                                $acp_farmer[$key]->isValidate = $expDistanceData[1];
                                $acp_farmer[$key]->distance = $expDistanceData[0];
                            } else {
                                $acp_farmer[$key]->isValidate = "No";
                                $acp_farmer[$key]->distance = "0";
                            }
                        } else {
                            $acp_farmer[$key]->isValidate = "No";
                            $acp_farmer[$key]->distance = "0";
                        }
                    } else {
                        $acp_farmer[$key]->isValidate = "No";
                        $acp_farmer[$key]->distance = "0";
                    }
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return Datatables::of($acp_farmer)->make();
            }
        }

        public function getAcpvisitlist() {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.farmer.acpvisitlist');
        }

        public function postAllacpvisitlist(Request $request) {
            //print_r(Request::all()); exit;
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "admin/allacpvisitlist",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            //print_r(Request::all()); exit;
            $value          = Input::all();
            $limit          = $value['length'];
            $start          = $value['start'];
            $search_array   = $value['search'];     // value & regax
            $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
            $order_array    = $value['order'][0]['column'];
            $dir            = $value['order'][0]['dir'];
            $start_date     = $value['start_date'];
            $end_date       = $value['end_date'];
            //$base_url1       = url('/assets/visit');
            $base_url1 = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/visit";
            if(empty($search_array['value']))
            {
                $sorting        ='';
                $datesearch     = '';
                if($start_date!='' && $end_date!='') {
                    $datesearch = "DATE(mv.created_at) >= DATE('".$start_date."') AND DATE(mv.created_at) <= DATE('".$end_date."')";
                }
                else {
                    $datesearch = "(1=1)";
                }
                $query  =   " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT mv.mark_visit_id,vf.farmer_name,
                                        v.village,dd.name,dd.ECN,c.crop,CONCAT('".$base_url1."/', mv.visit_image) as visitImage,mv.created_at,vf.contact_number,vf.alternate_contact_number,mv.expected_date,mv.village_id,mv.latitude,mv.longitude FROM mark_visits   mv
                                LEFT JOIN   village_farmers   vf ON  vf.farmer_id = mv.farmer_id 
                                LEFT JOIN   villages  v  ON v.village_id = mv.village_id 
                                LEFT JOIN   dhanuka_doctor dd   ON mv.dd_id = dd.dd_id 
                                LEFT JOIN   crops  c  ON c.crop_id = mv.crop_id 
                                WHERE 
                                    (mv.status='1') AND
                                    ".$datesearch." order by mv.mark_visit_id desc
                                )   A,
                                (select @a:=0) initvars";

                                // LIMIT ". $start." , ".$limit."
                                //  echo $query; exit;
                $acp_farmer    = collect(DB::select($query));
                foreach ($acp_farmer as $key => $value) {
                    $village_id = $value->village_id;
                    if($village_id) {
                        $lat = $value->latitude;
                        $lon = $value->longitude;
                        if((is_numeric($lat) == true)  && (is_numeric($lon) == true)) {
                            $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                            if($distanceData !==0) {
                                $expDistanceData = explode("~", $distanceData);
                                $acp_farmer[$key]->isValidate = $expDistanceData[1];
                                $acp_farmer[$key]->distance = $expDistanceData[0];
                            } else {
                                $acp_farmer[$key]->isValidate = "No";
                                $acp_farmer[$key]->distance = "0";
                            }
                        } else {
                            $acp_farmer[$key]->isValidate = "No";
                            $acp_farmer[$key]->distance = "0";
                        }
                    } else {
                        $acp_farmer[$key]->isValidate = "No";
                        $acp_farmer[$key]->distance = "0";
                    }
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return Datatables::of($acp_farmer)->make();
            }
            else 
            {            
                $search         = $search_array['value']; 
                $sorting        = '';
                $datesearch     = '';
                $datesearch = " (mv.status='1') ";

                if($start_date!='' && $end_date!='') {
                    $datesearch.= " and  DATE(mv.created_at) >= DATE('".$start_date."') AND DATE(mv.created_at) <= DATE('".$end_date."')";
                }
                 
                $query  =   "   SELECT                                 
                                    @a:=@a+1  as serial_number,
                                    A.*                                
                                    FROM
                                        (
                                        SELECT 
                                          mv.mark_visit_id,vf.farmer_name,
                                        v.village,dd.name,dd.ECN,c.crop,CONCAT('".$base_url1."/', mv.visit_image) as visitImage,mv.created_at,vf.contact_number,vf.alternate_contact_number, mv.expected_date,mv.village_id,mv.latitude,mv.longitude FROM mark_visits  mv
                                LEFT JOIN   village_farmers   vf ON  vf.farmer_id = mv.farmer_id 
                                LEFT JOIN   villages  v  ON v.village_id = mv.village_id 
                                LEFT JOIN   dhanuka_doctor dd   ON mv.dd_id = dd.dd_id 
                                LEFT JOIN   crops  c  ON c.crop_id = mv.crop_id 
                                        WHERE 
                                        (mv.status='1') AND
                                        (".$datesearch.")       AND
                                        (
                                            vf.farmer_name                LIKE '%".$search."%'    OR
                                            dd.name                       LIKE '%".$search."%'    OR
                                            v.village                     LIKE '%".$search."%'    OR
                                            vf.contact_number             LIKE '%".$search."%'    OR
                                            vf.alternate_contact_number   LIKE '%".$search."%'      
                                        ) order by mv.mark_visit_id desc
                                        )   A,
                                        (select @a:=0) initvars ".$sorting ;
                $acp_farmer    = collect(DB::select($query));
                foreach ($acp_farmer as $key => $value) {
                    $village_id = $value->village_id;
                    if($village_id) {
                        $lat = $value->latitude;
                        $lon = $value->longitude;
                        if((is_numeric($lat) == true)  && (is_numeric($lon) == true)) {
                            $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                            if($distanceData !==0) {
                                $expDistanceData = explode("~", $distanceData);
                                $acp_farmer[$key]->isValidate = $expDistanceData[1];
                                $acp_farmer[$key]->distance = $expDistanceData[0];
                            } else {
                                $acp_farmer[$key]->isValidate = "No";
                                $acp_farmer[$key]->distance = "0";
                            }
                        } else {
                            $acp_farmer[$key]->isValidate = "No";
                            $acp_farmer[$key]->distance = "0";
                        }
                    } else {
                        $acp_farmer[$key]->isValidate = "No";
                        $acp_farmer[$key]->distance = "0";
                    }
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return Datatables::of($acp_farmer)->make();
            }        
        }

        public function postExportallacpvisitlist(Request $request) {          
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
            //print_r($userData); exit;
            if ($userData) {
                $curTime        = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "admin/exportallacpvisitlist",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);
                
                $my_data    =   Input::all(); 
                //dd($my_data);
                $start_date =   $my_data['startdate'];
                $end_date   =   $my_data['enddate'];
                $search         = $my_data['search']; 
                $sorting        = '';
                $datesearch     = '';
                $datesearch     = " (mv.status='1') ";
                if($start_date!='' && $end_date!='') {
                    $datesearch.= " and  DATE(mv.created_at) >= DATE('".$start_date."') AND DATE(mv.created_at) <= DATE('".$end_date."')";
                }
                $base_url1      = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/visit";
                


                $query  =   "   SELECT
                                    @a:=@a+1  as serial_number,
                                    A.*
                                    FROM
                                        (
                                        SELECT 
                                            vf.farmer_name,
                                            v.village,
                                            dd.ECN,
                                            dd.name,
                                            vf.contact_number,
                                            vf.alternate_contact_number,
                                            c.crop,
                                            mv.expected_date,
                                            CONCAT('".$base_url1."/', mv.visit_image) as visitImage,
                                            mv.created_at
                                        FROM mark_visits  mv
                                        LEFT JOIN   village_farmers   vf ON  vf.farmer_id = mv.farmer_id 
                                        LEFT JOIN   villages  v  ON v.village_id = mv.village_id 
                                        LEFT JOIN   dhanuka_doctor dd   ON mv.dd_id = dd.dd_id 
                                        LEFT JOIN   crops  c  ON c.crop_id = mv.crop_id 
                                        WHERE 
                                            (mv.status='1') AND
                                            (".$datesearch.") AND
                                            (
                                                vf.farmer_name                LIKE '%".$search."%'    OR
                                                dd.name                       LIKE '%".$search."%'    OR
                                                v.village                     LIKE '%".$search."%'    OR
                                                vf.contact_number             LIKE '%".$search."%'    OR
                                                vf.alternate_contact_number   LIKE '%".$search."%'      
                                            ) order by mv.mark_visit_id desc
                                        )   A,
                                        (select @a:=0) initvars ".$sorting ;
                // echo $query; exit;
                $ticket_data    = DB::select(DB::raw($query));
                /*foreach ($ticket_data as $key => $value) {
                    $village_id = $value->village_id;
                    if($village_id) {
                        $lat = $value->latitude;
                        $lon = $value->longitude;
                        if((is_numeric($lat) == true)  && (is_numeric($lon) == true)) {
                            $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                            if($distanceData !==0) {
                                $expDistanceData = explode("~", $distanceData);
                                $ticket_data[$key]->isValidate = $expDistanceData[1];
                                $ticket_data[$key]->distance = $expDistanceData[0];
                            } else {
                                $ticket_data[$key]->isValidate = "No";
                                $ticket_data[$key]->distance = "0";
                            }
                        } else {
                            $ticket_data[$key]->isValidate = "No";
                            $ticket_data[$key]->distance = "0";
                        }
                    } else {
                        $ticket_data[$key]->isValidate = "No";
                        $ticket_data[$key]->distance = "0";
                    }
                }*/
                $result         = array_map(function ($value) {
                                        return (array)$value;
                                    }, $ticket_data);
            }
            //print_r($result);

            $ticket_column  = array(
                                    'Sr. No.',
                                    'Farmer Name',
                                    'Village',
                                    'DD ECN',
                                    'DD Name',
                                    'Contact Number',
                                    'Alternate Contact Number',
                                    'Crop',
                                    'Expected Date',
                                    'Visit Image',
                                    'created_at'                                );
            array_unshift($result, $ticket_column);
            $primarycolor = "rgb(255, 0, 0)";
            $myFile= Excel::create("filename", function($excel) use($result, $primarycolor) {
                        $excel->setTitle('title');
                        $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
                            $sheet->fromArray($result, null, 'A1', false, false);
                            $sheet->row(1, function($row) use ($primarycolor) {
                                $row->setFontSize(12)
                                    ->setFontWeight('bold')
                                    ->setBackground('#62bdb9')
                                    ->setAlignment('left')
                                    ->setFontColor('#000000');
                            });
                        });
                     });
                     
            $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
            $response   =  array(
                            'name' => "All ACP Visit List.xlsx", //no extention needed
                            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                         );
            $log_firstTime  = strtotime($log_start_time);
            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
            $log_time_diff  = $log_lastTime-$log_firstTime;
            $timeConsumed = round(microtime(true) - $curTime,3)*1000;
            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);
            return response()->json($response);
        }


        public function getTicketHighlight($id) {  
            $tickeId = $id;     
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            return View::make('backend.ticket.tickethighlight',compact('tickeId'));
        }  
            
        public function postHighlightticket(Request $request){
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
            $start_date     = $value['start_date'];
            $end_date       = $value['end_date'];
            //$base_url      = url('/assets/highlightImage');
            $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/highlightImage";
            $tickeId = $value['tickeId'];
            if(empty($search_array['value']))
            {            
                $sorting        ='';            
                $datesearch     = '(1=1)';

                if($start_date!='' && $end_date!='') {
                    $datesearch = "AND DATE(th.creation_at) >= DATE('".$start_date."') AND DATE(th.creation_at) <= DATE('".$end_date."')";
                }
                
                $query  =   " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT th.*,
                        CONCAT_WS(
                                    '\n', 
                                    NULLIF(zm.ECN, ''),
                                    NULLIF(rm.ECN, ''),
                                    NULLIF(am.ECN, ''),
                                    NULLIF(srm.ECN, ''),
                                    NULLIF(sam.ECN, ''),
                                    NULLIF(u.ECN, ''),
                                    NULLIF(dd.ECN, ''),
                                    NULLIF(ms.ECN, '')
                                ) AS ecn,
                        CONCAT_WS(
                                    '\n', 
                                    NULLIF(zm.zonal_manager_name, ''),
                                    NULLIF(rm.regional_manager_name, ''),
                                    NULLIF(am.area_manager_name, ''),
                                    NULLIF(srm.sales_regional_manager_name, ''),
                                    NULLIF(sam.sales_area_manager_name, ''),
                                    NULLIF(u.name, ''),
                                    NULLIF(dd.name, ''),
                                    NULLIF(ms.marketing_supervisor_name, '')
                                    
                                ) AS bdo, t.ticket_no,CONCAT('".$base_url."/', th.highlightImage)
                        FROM ticket_highlights as th
                         LEFT JOIN   tickets  t   ON th.ticket_id=t.ticket_id
                        LEFT JOIN   users  u   ON th.user_id=u.id AND th.user_type='bdo'
                        LEFT JOIN   zonal_managers  zm  ON th.user_id=zm.zonal_manager_id AND th.user_type='zonal'
                        LEFT JOIN   regional_managers rm  ON th.user_id=rm.regional_manager_id AND th.user_type='regional'
                        LEFT JOIN   area_managers am  ON th.user_id=am.area_manager_id AND th.user_type='area'
                        LEFT JOIN   sales_regional_managers srm ON th.user_id=srm.sales_regional_manager_id AND th.user_type='sales_regional'
                        LEFT JOIN   marketing_supervisor ms ON th.user_id=ms.marketing_supervisor_id AND th.user_type='marketing_supervisor'
                        LEFT JOIN   sales_area_managers     sam ON th.user_id=sam.sales_area_manager_id AND th.user_type='sales_area'
                        LEFT JOIN   dhanuka_doctor dd ON th.user_id=dd.dd_id AND th.user_type='dhanuka_doctor'
                                WHERE 
                                    ".$datesearch." AND th.ticket_id = ".$tickeId." order by th.creation_at desc
                                )   A,
                                (select @a:=0) initvars";

                                // LIMIT ". $start." , ".$limit."
                                //  echo $query; exit;
                $highlight    = collect(DB::select($query));  
                // echo "<pre>"; print_r($highlight); die;
                return Datatables::of($highlight)->make();
            }
            else 
            {            
                $search         = $search_array['value']; 
                $sorting        = '';
                $datesearch     = '(1=1)';
                if($start_date!='' && $end_date!='') {
                    $datesearch = "AND DATE(th.creation_at) >= DATE('".$start_date."') AND DATE(th.creation_at) <= DATE('".$end_date."')";
                }

                if(isset($search_array['value']) && $search_array['value']!=''){
                $search = $search_array['value']; 
                $datesearch.=" AND (
                    t.ticket_no     LIKE '%".$search."%' OR 
                    (
                        u.name  LIKE '%".$search."%'    OR
                        zm.zonal_manager_name LIKE '%".$search."%'    OR
                        rm.regional_manager_name LIKE '%".$search."%'  OR
                        am.area_manager_name LIKE '%".$search."%' OR
                        srm.sales_regional_manager_name LIKE '%".$search."%'    OR
                        ms.marketing_supervisor_name LIKE '%".$search."%'    OR
                        sam.sales_area_manager_name LIKE '%".$search."%'    OR
                        dd.name   LIKE '%".$search."%' 
                    ) OR 
                    (
                        u.ECN  LIKE '%".$search."%'    OR
                        zm.ECN LIKE '%".$search."%'    OR
                        rm.ECN LIKE '%".$search."%'  OR
                        am.ECN LIKE '%".$search."%' OR
                        srm.ECN LIKE '%".$search."%'    OR
                        ms.ECN LIKE '%".$search."%'    OR
                        sam.ECN LIKE '%".$search."%'    OR
                        dd.ECN   LIKE '%".$search."%' 
                    )
                ) ";                  
            }
                 
                $query  =   "   SELECT                                 
                                    @a:=@a+1  as serial_number,
                                    A.*                                
                                    FROM
                                        (
                                        SELECT th.*,
                        CONCAT_WS(
                                    '\n', 
                                    NULLIF(zm.ECN, ''),
                                    NULLIF(rm.ECN, ''),
                                    NULLIF(am.ECN, ''),
                                    NULLIF(srm.ECN, ''),
                                    NULLIF(sam.ECN, ''),
                                    NULLIF(u.ECN, ''),
                                    NULLIF(dd.ECN, ''),
                                    NULLIF(ms.ECN, '')
                                ) AS ecn,
                        CONCAT_WS(
                                    '\n', 
                                    NULLIF(zm.zonal_manager_name, ''),
                                    NULLIF(rm.regional_manager_name, ''),
                                    NULLIF(am.area_manager_name, ''),
                                    NULLIF(srm.sales_regional_manager_name, ''),
                                    NULLIF(sam.sales_area_manager_name, ''),
                                    NULLIF(u.name, ''),
                                    NULLIF(dd.name, ''),
                                    NULLIF(ms.marketing_supervisor_name, '')
                                    
                                ) AS bdo, t.ticket_no,CONCAT('".$base_url."/', th.highlightImage)
                        FROM ticket_highlights as th
                         LEFT JOIN   tickets  t   ON th.ticket_id=t.ticket_id
                        LEFT JOIN   users  u   ON th.user_id=u.id AND th.user_type='bdo'
                        LEFT JOIN   zonal_managers  zm  ON th.user_id=zm.zonal_manager_id AND th.user_type='zonal'
                        LEFT JOIN   regional_managers rm  ON th.user_id=rm.regional_manager_id AND th.user_type='regional'
                        LEFT JOIN   area_managers am  ON th.user_id=am.area_manager_id AND th.user_type='area'
                        LEFT JOIN   sales_regional_managers srm ON th.user_id=srm.sales_regional_manager_id AND th.user_type='sales_regional'
                        LEFT JOIN   marketing_supervisor ms ON th.user_id=ms.marketing_supervisor_id AND th.user_type='marketing_supervisor'
                        LEFT JOIN   sales_area_managers     sam ON th.user_id=sam.sales_area_manager_id AND th.user_type='sales_area'
                        LEFT JOIN   dhanuka_doctor dd ON th.user_id=dd.dd_id AND th.user_type='dhanuka_doctor'
                                        WHERE th.ticket_id = ".$tickeId." AND ".$datesearch." 
                                        order by th.creation_at desc
                                        )   A,
                                        (select @a:=0) initvars".$sorting;
                                          //echo $query;die;
                $highlight    = collect(DB::select($query));
                //echo "<pre>";print_r($highlight);die;
                 return Datatables::of($highlight)->make();
            
            }
        }

        public function getGetVillageList()
        {
            return view('backend.village.get-village-list');       
        }

        public function postVillageList(Request $request)
        {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            }
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "admin/village-list",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $value          = Input::all();
            $limit          = $value['length'];
            $start          = $value['start'];
            $search_array   = $value['search'];     // value & regax
            $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
            $order_array    = $value['order'][0]['column'];
            $dir            = $value['order'][0]['dir'];

            $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
            $end_date       = isset($value['end_date']) ?  $value['end_date']:'';

            $search_query=" (1=1)  ";

            if($start_date!='' && $end_date!='') {
                    $search_query.=" and DATE(av.created_at) >= DATE('".$start_date."') AND DATE(av.created_at) <= DATE('".$end_date."')";
            }elseif($start_date!='') {
                    $search_query.= " and DATE(av.created_at) >= DATE('".$start_date."')";
            }
            //echo $search_query;die;
            if(empty($search_array['value']))
            {
                //$categorysearch     = '(1=1)';
                $query    = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    s.state,
                                    d.district,
                                    t.tehsil,
                                    v.village,
                                    (CASE WHEN v.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(av.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                                    av.village_id,
                                    dd.name as dd_name,
                                    dd.ECN as dd_ecn
                                FROM  
                                assigned_village av 
                                LEFT JOIN dhanuka_doctor dd ON dd.dd_id=av.assigned_to_dd_id
                                LEFT JOIN villages v ON v.village_id=av.village_id    
                                LEFT JOIN tehsils t ON t.tehsil_id=v.tehsil_id
                                LEFT JOIN districts d ON d.district_id=t.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id
                                WHERE
                                    ".$search_query." AND
                                    v.status<>'2'
                                GROUP BY av.village_id,av.assigned_to_dd_id
                                ORDER BY
                                    av.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.tehsil ASC,
                                    v.village ASC
                                )  A,
                                (select @a:=0) initvars";
                $villages = collect(DB::select($query));  
                //echo "<pre>";print_r($villages);die;
                foreach ($villages as $key => $value) {
                    //village dd ecn and name
                    // $ddData = DB::table('assigned_village as av')
                    //           ->join('dhanuka_doctor as dd','dd.dd_id','=','av.assigned_to_dd_id')
                    //           ->where('av.village_id',$villages[$key]->village_id)
                    //           ->select('dd.name','dd.ECN')
                    //           ->get();
                    // $villages[$key]->dd_name = $ddData[0]->name;
                    // $villages[$key]->dd_ecn = $ddData[0]->ECN;

                    //farmer count
                    $fcount = DB::table('village_farmers as vf')
                             ->where('vf.village_id',$villages[$key]->village_id)
                             ->count();
                    $villages[$key]->total_farmer = $fcount;

                    //visits
                    $markVisits = DB::table('mark_visits as mv')
                             ->where('mv.village_id',$villages[$key]->village_id)
                             ->count();
                    $villages[$key]->markVisits = $markVisits;
                }
                //echo "<pre>"; print_r($data);die;//print_r($villages);die;
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return Datatables::of($villages)->make();
            }
            else 
            {
                $search     =  $search_array['value']; 
                //$categorysearch     = '(1=1)';
                if($search!='') {
                    $search_query     .= "AND (v.village LIKE '".$search."%')";
                }
                $query  = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    s.state,
                                    d.district,
                                    t.tehsil,
                                    v.village,
                                    (CASE WHEN v.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(av.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                                    dd.name as dd_name,
                                    dd.ECN as dd_ecn,
                                    av.village_id
                                FROM  
                                assigned_village av 
                                LEFT JOIN dhanuka_doctor dd ON dd.dd_id=av.assigned_to_dd_id 
                                LEFT JOIN villages v ON v.village_id=av.village_id   
                                LEFT JOIN tehsils t ON t.tehsil_id=v.tehsil_id
                                LEFT JOIN districts d ON d.district_id=t.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id
                                
                                WHERE
                                    ".$search_query." AND
                                    v.status<>'2'
                                GROUP BY av.village_id,av.assigned_to_dd_id
                                ORDER BY
                                    v.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.tehsil ASC,
                                    v.village ASC
                                )   A,
                                (select @a:=0) initvars";
                $villages  = collect(DB::select($query));
                foreach ($villages as $key => $value) {
                    //village dd ecn and name
                    // $ddData = DB::table('assigned_village as av')
                    //           ->join('dhanuka_doctor as dd','dd.dd_id','=','av.assigned_to_dd_id')
                    //           ->where('av.village_id',$villages[$key]->village_id)
                    //           ->select('dd.name', 'dd.ECN')
                    //           ->get();
                    // $villages[$key]->dd_name = $ddData[0]->name;
                    // $villages[$key]->dd_ecn = $ddData[0]->ECN;

                    //farmer count
                    $fcount = DB::table('village_farmers as vf')
                             ->where('vf.village_id',$villages[$key]->village_id)
                             ->count();
                    $villages[$key]->total_farmer = $fcount;

                    //visits
                    $markVisits = DB::table('mark_visits as mv')
                             ->where('mv.village_id',$villages[$key]->village_id)
                             ->count();
                    $villages[$key]->markVisits = $markVisits;
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return Datatables::of($villages)->make();            
            }
        }
        //village assigned dd list
        public function getVillageAssignddList($village_id)
        {   
            $village_id = $village_id;
            return view('backend.village.assignedDD',compact('village_id'));       
        }

        //village assigned dd list
        public function postVillageAssigndd(Request $request)
        {
            $sess = \Session::get('adminid');
            if (!isset($sess) || $sess == "") {
                return redirect('/');
            } 
            $value          = Input::all();
            $limit          = $value['length'];
            $start          = $value['start'];
            $search_array   = $value['search'];     // value & regax
            $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
            $order_array    = $value['order'][0]['column'];
            $dir            = $value['order'][0]['dir'];

            $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
            $end_date       = isset($value['end_date']) ?  $value['end_date']:'';
            $village_id     = $value['village_id'];
            $search_query="";

            if($start_date!='' && $end_date!='') {
                    $search_query.=" and DATE(av.created_at) >= DATE('".$start_date."') AND DATE(av.created_at) <= DATE('".$end_date."')";
            }elseif($start_date!='') {
                    $search_query.= " and DATE(av.created_at) >= DATE('".$start_date."')";
            }
            //echo $search_query;die;
            if(empty($search_array['value']))
            {
                $query    = " 
                        SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*                                
                        FROM
                            (
                            SELECT    
                                av.village_id,    
                                v.village,
                                u.ECN as bdo_ecn,
                                u.name as bdo_name,
                                dd.ECN as dd_ecn,
                                dd.name as dd_name,
                                DATE_FORMAT(av.created_at, '%Y-%m %d, %h:%i %p') as created_at
                                
                            FROM  assigned_village av 
                            LEFT JOIN dhanuka_doctor dd ON dd.dd_id=av.assigned_to_dd_id
                            LEFT JOIN villages v ON v.village_id=av.village_id    
                            LEFT JOIN users u ON u.id=av.assigned_by_bdo_id
                            where av.village_id = '".$village_id."'". $search_query . "    
                            ORDER BY av.created_at DESC
                            )  A,
                            (select @a:=0) initvars";
                $villages = collect(DB::select($query));  
                //echo "<pre>";print_r($villages);die;
                return Datatables::of($villages)->make();
            }
            else 
            {
                $search     =  $search_array['value']; 
                if($search!='') {
                    $search_query     .= "AND (dd.name LIKE '".$search."%')";
                }
                $query    = " 
                        SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*                                
                        FROM
                            (
                            SELECT    
                                av.village_id,    
                                v.village,
                                u.ECN as bdo_ecn,
                                u.name as bdo_name,
                                dd.ECN as dd_ecn,
                                dd.name as dd_name,
                                DATE_FORMAT(av.created_at, '%Y-%m %d, %h:%i %p') as created_at
                                
                            FROM  assigned_village av 
                            LEFT JOIN dhanuka_doctor dd ON dd.dd_id=av.assigned_to_dd_id
                            LEFT JOIN villages v ON v.village_id=av.village_id    
                            LEFT JOIN users u ON u.id=av.assigned_by_bdo_id
                            where av.village_id = '".$village_id."'". $search_query . "   
                            ORDER BY av.created_at DESC
                            )  A,
                            (select @a:=0) initvars";
                $villages = collect(DB::select($query));  
                //echo "<pre>";print_r($villages);die;
                return Datatables::of($villages)->make();            
            }
        }

        //organogram ============================
        public function getOrganogramList()
        {
            return view('backend.adminOrganogram.organogramList');       
        }

        public function postOrganogramList() {

            $value = Input::all();
            $start_date     = $value['start_date'];
            $end_date       = $value['end_date'];
            $dhanuka_doctor_orn11     = '';
            $users_orn11 = '';
            $sales_regional_managers_orn11 = '';
            $sales_area_managers_orn11 = '';
            $regional_managers_orn11 = '';
            $area_managers_orn11 = '';

            if($start_date!='' && $end_date!='') {
                    $dhanuka_doctor_orn11 = "WHERE DATE(u.updated_at) >= DATE('".$start_date." 00:00:00') AND DATE(u.updated_at) <= DATE('".$end_date." 23:59:59')";
            }
            else {
                $dhanuka_doctor_orn11 = "";
            }
            //DD data
            $query      =   
                            " 
                            SELECT
                                    u.ECN as user_code,
                                    u.dd_hq as user_hq,
                                    u.name as user_name,
                                    u.dd_mobile_no as user_mobile,
                                    s.state as user_state,
                                    zm.zonal_manager_name as user_zonal_manager,
                                    DATE_FORMAT(u.updated_at, '%Y-%m-%d, %h:%i %p') as datetime_format,
                                
                                    u.dd_id as user_id,
                                    u.dhanuka_doctor_orn_id as user_orn_id,
                                    u.orn_status
                                FROM        dhanuka_doctor_orn u
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

                                 ".$dhanuka_doctor_orn11."                           
                                GROUP BY u.dhanuka_doctor_orn_id
                                ORDER BY
                                    u.updated_at DESC
                                ";
            $dd_data    = collect(DB::select($query));
            if (count($dd_data)>0) {
                foreach ($dd_data as $key => $value) {
                    $dd_data[$key]->user_type = 'DD';
                }
                $DDrdata = json_decode(json_encode($dd_data, true));
            }
            else
            {
                $DDrdata = [];
            }
            

            if($start_date!='' && $end_date!='') {
                    $users_orn11 = "WHERE DATE(u.updated_at) >= DATE('".$start_date." 00:00:00') AND DATE(u.updated_at) <= DATE('".$end_date." 23:59:59')";
            }
            else {
                $users_orn11 = "";
            }
            //Bdo Data
            $query1      =   
                        " 
                            SELECT
                                u.ECN as user_code,
                                u.bdo_hq as user_hq,
                                u.name as user_name,
                                u.bdo_mobile_no as user_mobile,
                                s.state as user_state,
                                zm.zonal_manager_name as user_zonal_manager,
                                DATE_FORMAT(u.updated_at, '%Y-%m-%d, %h:%i %p') as datetime_format,
                                u.id as user_id,
                                u.users_orn_id as user_orn_id,
                                u.orn_status
                                
                                FROM        users_orn u
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
                                     
                                     ".$users_orn11." 
                                GROUP BY u.users_orn_id
                                ORDER BY
                                    u.updated_at DESC
                                ";
            $bdo_data    = collect(DB::select($query1));
            if (count($bdo_data)>0) {
                foreach ($bdo_data as $key => $value) {
                    $bdo_data[$key]->user_type = 'BDO';
                }
                $bdordata = json_decode(json_encode($bdo_data, true));
            }  
            else
            {
                $bdordata = [];
            }  

            if($start_date!='' && $end_date!='') {
                    $sales_regional_managers_orn11 = "WHERE DATE(rm.updated_at) >= DATE('".$start_date." 00:00:00') AND DATE(rm.updated_at) <= DATE('".$end_date." 23:59:59')";
            }
            else {
                $sales_regional_managers_orn11 = "";
            }
            //RSM data
            $query2 = " SELECT
                            rm.ECN as user_code,
                            rm.sales_regional_manager_hq_code as user_hq,
                            zm.zonal_manager_name as user_zonal_manager,
                            rm.sales_regional_manager_name as user_name,
                            rm.sales_regional_manager_mobile as user_mobile,
                                                                                               
                            GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS user_state,
                            DATE_FORMAT(rm.updated_at, '%Y-%m-%d, %h:%i %p') as datetime_format,
                            rm.sales_regional_manager_id as user_id,
                            rm.sales_regional_managers_orn_id as user_orn_id,
                            rm.orn_status
                        FROM        sales_regional_managers_orn                     rm
                        LEFT JOIN   active_logs                                 al      ON rm.sales_regional_manager_id=al.user_id AND al.user_type='sales_regional' 
                        LEFT JOIN   zonal_managers                              zm      ON zm.zonal_manager_id=rm.zonal_manager_id
                        LEFT JOIN   sales_regional_manager_states_orn               rms     ON rms.orn_id=rm.sales_regional_managers_orn_id
                        LEFT JOIN   sales_regional_manager_product_categories   rmpc    ON rmpc.sales_regional_manager_id=rm.sales_regional_manager_id and rmpc.status=1
                        LEFT JOIN   states                                      s       ON rms.state_id=s.state_id
                        LEFT JOIN   product_crop_categories                     pcc     ON pcc.product_crop_category_id=rmpc.product_crop_category_id
                        ".$sales_regional_managers_orn11."
                        GROUP BY rm.sales_regional_managers_orn_id
                        ORDER BY
                                rm.updated_at DESC";
            $rsm_data    = collect(DB::select($query2));
            if (count($rsm_data)>0) {
                foreach ($rsm_data as $key => $value) {
                    $rsm_data[$key]->user_type = 'RSM';
                }
                $rsmrdata = json_decode(json_encode($rsm_data, true));
            }  
            else
            {
                $rsmrdata = [];
            } 


            if($start_date!='' && $end_date!='') {
                    $sales_area_managers_orn11 = "WHERE DATE(sam.updated_at) >= DATE('".$start_date." 00:00:00') AND DATE(sam.updated_at) <= DATE('".$end_date." 23:59:59')";
            }
            else {
                $sales_area_managers_orn11 = "";
            }
            //ASM data
            $query3 = "SELECT 
                        sam.ECN as user_code,                               
                        sam.sales_area_manager_hq_code as user_hq,
                        zm.zonal_manager_name as user_zonal_manager,
                        sam.sales_area_manager_name as user_name,
                        sam.sales_area_manager_mobile as user_mobile,
                        GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS user_state,
                        DATE_FORMAT(sam.updated_at, '%Y-%m-%d, %h:%i %p') as datetime_format,                                     
                        sam.sales_area_manager_id as user_id,
                        sam.sales_area_managers_orn_id as user_orn_id,
                        sam.orn_status
                        
                        FROM        sales_area_managers_orn         sam
                        LEFT JOIN   active_logs                 al      ON sam.sales_area_manager_id=al.user_id AND al.user_type='sales_area'  
                        LEFT JOIN   zonal_managers              zm      ON zm.zonal_manager_id=sam.zonal_manager_id
                        LEFT JOIN   sales_area_manager_states_orn   sams    ON sams.orn_id=sam.sales_area_managers_orn_id
                        LEFT JOIN   regional_managers           rm      ON rm.regional_manager_id=sam.regional_manager_id
                        LEFT JOIN   sales_regional_managers     srm     ON srm.sales_regional_manager_id=sam.sales_regional_manager_id
                        LEFT JOIN   states                      s       ON sams.state_id=s.state_id                                    
                        
                        LEFT JOIN   sales_area_manager_product_categories   sampc    ON sampc.sales_area_manager_id=sam.sales_area_manager_id and sampc.status=1
                        LEFT JOIN   product_crop_categories                 pcc     ON pcc.product_crop_category_id=sampc.product_crop_category_id
                         ".$sales_area_managers_orn11."
                        GROUP BY sam.sales_area_managers_orn_id
                        ORDER BY
                            sam.updated_at DESC";
            $asm_data    = collect(DB::select($query3));
            if (count($asm_data)>0) {
                foreach ($asm_data as $key => $value) {
                    $asm_data[$key]->user_type = 'ASM';
                }
                $asmrdata = json_decode(json_encode($asm_data, true));
            }  
            else
            {
                $asmrdata = [];
            } 

            if($start_date!='' && $end_date!='') {
                    $regional_managers_orn11 = "WHERE DATE(rm.updated_at) >= DATE('".$start_date." 00:00:00') AND DATE(rm.updated_at) <= DATE('".$end_date." 23:59:59')";
            }
            else {
                $regional_managers_orn11 = "";
            }
            //regional
            $query4 = "SELECT
                        rm.ECN as user_code,
                        rm.regional_manager_hq_code as user_hq,
                        zm.zonal_manager_name as user_zonal_manager,
                        rm.regional_manager_name as user_name,
                        rm.regional_manager_mobile as user_mobile,
                        GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS user_state,
                        DATE_FORMAT(rm.updated_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                        rm.regional_manager_id as user_id,
                        rm.regional_managers_orn_id as user_orn_id,
                        rm.orn_status
                        FROM        regional_managers_orn               rm
                        LEFT JOIN   active_logs                         al      ON rm.regional_manager_id=al.user_id AND al.user_type='regional'                   
                        LEFT JOIN   zonal_managers                      zm      ON zm.zonal_manager_id=rm.zonal_manager_id
                        LEFT JOIN   regional_manager_states_orn             rms     ON rms.orn_id=rm.regional_managers_orn_id                                
                        LEFT JOIN   regional_manager_product_categories rmpc    ON rmpc.regional_manager_id=rm.regional_manager_id and rmpc.status=1
                        LEFT JOIN   states                              s       ON rms.state_id=s.state_id
                        LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=rmpc.product_crop_category_id and  pcc.status=1
                         ".$regional_managers_orn11."
                        GROUP BY rm.regional_managers_orn_id
                        ORDER BY
                            rm.updated_at DESC";
            $regional_data    = collect(DB::select($query4));
            if (count($regional_data)>0) {
                foreach ($regional_data as $key => $value) {
                    $regional_data[$key]->user_type = 'Regional';
                }
                $regionalrdata = json_decode(json_encode($regional_data, true));
            }  
            else
            {
                $regionalrdata = [];
            } 


            if($start_date!='' && $end_date!='') {
                    $area_managers_orn11 = "WHERE DATE(am.updated_at) >= DATE('".$start_date." 00:00:00') AND DATE(am.updated_at) <= DATE('".$end_date." 23:59:59')";
            }
            else {
                $area_managers_orn11 = "";
            }
            //area data
            $query5 = "SELECT
                        am.ECN as user_code,                                
                        am.area_manager_hq_code as user_hq, 
                        zm.zonal_manager_name as user_zonal_manager,
                        am.area_manager_name as user_name,
                        am.area_manager_mobile as user_mobile,
                        GROUP_CONCAT(DISTINCT(s.state) SEPARATOR ',\n')  AS user_state,
                        DATE_FORMAT(am.updated_at, '%Y-%m-%d, %h:%i %p') as datetime_format, 
                        am.area_manager_id as user_id,
                        am.area_managers_orn_id as user_orn_id,
                        am.orn_status
                        FROM        area_managers_orn       am
                        LEFT JOIN   active_logs             al  ON am.area_manager_id=al.user_id AND al.user_type='area'
                        LEFT JOIN   zonal_managers          zm  ON zm.zonal_manager_id=am.zonal_manager_id
                        LEFT JOIN   area_manager_states_orn     ams ON ams.orn_id=am.area_managers_orn_id
                        LEFT JOIN   regional_managers       rm  ON rm.regional_manager_id=am.regional_manager_id
                        LEFT JOIN   sales_regional_managers srm ON srm.sales_regional_manager_id=am.sales_regional_manager_id
                        LEFT JOIN   states                  s   ON ams.state_id=s.state_id                                    
                        
                        LEFT JOIN   area_manager_product_categories     ampc    ON ampc.area_manager_id=am.area_manager_id and ampc.status=1
                        LEFT JOIN   product_crop_categories             pcc     ON pcc.product_crop_category_id=ampc.product_crop_category_id
                         ".$area_managers_orn11."
                        GROUP BY am.area_managers_orn_id
                        ORDER BY
                            am.updated_at DESC";
            $area_data    = collect(DB::select($query5));
            if (count($area_data)>0) {
                foreach ($area_data as $key => $value) {
                    $area_data[$key]->user_type = 'Area';
                }
                $areardata = json_decode(json_encode($area_data, true));
            }  
            else
            {
                $areardata = [];
            }

            $allRequests = array_merge($DDrdata,$bdordata,$rsmrdata,$asmrdata,$regionalrdata,$areardata);
            
            if (count($allRequests)>0) {
                $ornStatus = [];
                foreach ($allRequests as $key => $row)
                {
                    $ornStatus[$key]  = $allRequests[$key]->orn_status;
                }
                array_multisort($ornStatus, SORT_ASC, $allRequests);
                $allRequests = $allRequests;
            }
            else
            {
                $allRequests = [];
            }
            //echo "<pre>";print_r($allRequests);die;
            $allRequests    = collect($allRequests);
            return Datatables::of($allRequests)->make(); 
            //echo json_encode($allRequests,JSON_UNESCAPED_UNICODE);
            //return View::make('backend.adminOrganogram.organogramList',compact('allRequests'));
        } 

        public function postUpdateOrganogramStatus(Request $request){
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
            if ($userData) {
                $my_data      = Input::all(); 
                $user_id      = (isset($my_data['_id'])?$my_data['_id']:'');            
                $user_type    = (isset($my_data['_userType'])?$my_data['_userType']:''); 
                
                //Area  Regional  ASM  RSM  BDO DD
                if ($user_type == 'Area') 
                {
                    $area_data  = DB::table('area_managers_orn')
                                ->where('area_managers_orn_id', $user_id)
                                ->where('orn_status','0')
                                ->get();
                    if (count($area_data)>0) 
                    {
                        if ($area_data[0]->area_manager_id == 0) {
                            $area_manager_array     = array(
                                                        'ECN'                       => $area_data[0]->ECN,
                                                        'dash'                      => $area_data[0]->dash,
                                                        'password'                  => $area_data[0]->password,
                                                        'zonal_manager_id'          => $area_data[0]->zonal_manager_id,
                                                        'regional_manager_id'       => $area_data[0]->regional_manager_id,
                                                        'sales_regional_manager_id' => $area_data[0]->sales_regional_manager_id,
                                                        'area_manager_hq_code'      => $area_data[0]->area_manager_hq_code,
                                                        'area_manager_name'         => $area_data[0]->area_manager_name,
                                                        'area_manager_mobile'       => $area_data[0]->area_manager_mobile,
                                                        'area_manager_pincode'      => $area_data[0]->area_manager_pincode,
                                                        'status'                    => '1',
                                                        'created_at' => $area_data[0]->updated_at
                                                    );
                            $insert_area_manager    = AreaManager::firstOrCreate($area_manager_array);
                            $result                 = $insert_area_manager->area_manager_id;

                            if($result) {
                                $area_state = DB::table('area_manager_states_orn')
                                            ->where('area_manager_id',$area_data[0]->area_manager_id)
                                            ->where('orn_status','0')
                                            ->get();
                                if (count($area_state)>0) {
                                    $area_manager_states   = array();
                                    foreach($area_state AS $key => $values) {
                                        $area_manager_states[] = array(
                                                        'area_manager_id'   => $result,
                                                        'state_id'          => $area_state[$key]->state_id,
                                                        'status'            => '1'
                                                        );
                                    }                    
                                    $insert_area_manager_states= AreaManagerState::insert($area_manager_states);
                                    $area_Ustate = DB::table('area_manager_states_orn')
                                                ->where('area_manager_id',$user_id)
                                                ->update(['orn_status'=>'2']);
                                }
                                
                                
                                
                                $product_category = DB::table('area_manager_product_categories_orn')
                                            ->where('area_manager_id',$area_data[0]->area_manager_id)
                                            ->where('orn_status','0')
                                            ->get();
                                if (count($product_category)>0) {
                                    $area_manager_product_category = array();
                                    foreach($product_category AS $product_category_id) 
                                    {
                                        $area_manager_product_category[]   = array(
                                                'area_manager_id'           => $result,
                                                'product_crop_category_id'  => $area_data[0]->product_crop_category_id,
                                                'status'                    => '1'
                                                );
                                    }
                                    $insert_area_manager_product_category  = AreaManagerProductCategory::insert($area_manager_product_category);

                                    $product_Ucategory = DB::table('area_manager_product_categories_orn')
                                            ->where('area_manager_id',$user_id)
                                            ->update(['orn_status'=>'2']);
                                }
                                $area_Udata  = DB::table('area_managers_orn')
                                                ->where('area_managers_orn_id', $user_id)
                                                ->update(['orn_status'=>'2']);
                                echo '1';die;
                            }
                            else
                            {
                                echo '2';die;
                            }
                        }
                        else
                        {
                            $area_manager_array     = array(
                                                        'ECN'                       => $area_data[0]->ECN,
                                                        'dash'                      => $area_data[0]->dash,
                                                        'password'                  => $area_data[0]->password,
                                                        'zonal_manager_id'          => $area_data[0]->zonal_manager_id,
                                                        'regional_manager_id'       => $area_data[0]->regional_manager_id,
                                                        'sales_regional_manager_id' => $area_data[0]->sales_regional_manager_id,
                                                        'area_manager_hq_code'      => $area_data[0]->area_manager_hq_code,
                                                        'area_manager_name'         => $area_data[0]->area_manager_name,
                                                        'area_manager_mobile'       => $area_data[0]->area_manager_mobile,
                                                        'area_manager_pincode'      => $area_data[0]->area_manager_pincode,
                                                        'status'                    => $area_data[0]->status,
                                                        'updated_at' => $area_data[0]->updated_at
                                                    );
                            $update_area_manager        = DB::table('area_managers')
                                                    ->where('area_manager_id', $area_data[0]->area_manager_id)
                                                    ->where('status', '1')
                                                    ->update($area_manager_array);
                            if ($update_area_manager) {
                                $update_bdo = DB::table('users')
                                            ->where('area_manager_id', $area_data[0]->area_manager_id)
                                            ->where('status', '1')
                                            ->update(['zonal_manager_id'=>$area_data[0]->zonal_manager_id, 'regional_manager_id'=>$area_data[0]->regional_manager_id]);

                                $area_state = DB::table('area_manager_states_orn')
                                            ->where('area_manager_id',$area_data[0]->area_manager_id)
                                            ->where('orn_status','0')
                                            ->get();
                                if (count($area_state)>0) {
                                    $reset_area_manager_states   = DB::table('area_manager_states')
                                                        ->where('area_manager_id', $area_data[0]->area_manager_id)
                                                        ->update(['status'=>'0']); 
                                    foreach($area_state AS $key => $values) {
                                        $update_area_manager   = DB::table('area_manager_states')
                                                            ->where('area_manager_id', $area_data[0]->area_manager_id)
                                                            ->where('state_id', $area_state[$key]->state_id)
                                                            ->update(['status'=>'1']);
                                        $area_manager_states   = array(
                                                        'area_manager_id'  => $area_data[0]->area_manager_id,
                                                        'state_id'          => $area_state[$key]->state_id,
                                                        'status'  => '1'
                                                                        );
                                        $insert_area_manager   = AreaManagerState::firstOrCreate($area_manager_states);
                                    }
                                    $area_state = DB::table('area_manager_states_orn')
                                                    ->where('area_manager_id',$area_data[0]->area_manager_id)
                                                    ->update(['orn_status'=>'2']);
                                }
                                   
                                $product_category = DB::table('area_manager_product_categories_orn')
                                            ->where('area_manager_id',$area_data[0]->area_manager_id)
                                            ->where('orn_status','0')
                                            ->get();
                                if (count($product_category)>0) {
                                    $reset_area_manager_category= DB::table('area_manager_product_categories')
                                                ->where('area_manager_id', $area_data[0]->area_manager_id)
                                                ->update(['status'=>'0']); 
                                    foreach($product_category AS $key => $values) {
                                        $update_area_manager   = DB::table('area_manager_product_categories')
                                                    ->where('area_manager_id', $area_data[0]->area_manager_id)
                                                    ->where('product_crop_category_id', $product_category[$key]->product_crop_category_id)
                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                                        $area_manager_states   = array(
                                                'area_manager_id'  => $area_data[0]->area_manager_id,
                                                'product_crop_category_id'  => $product_category[$key]->product_crop_category_id,
                                                'status'                    => '1'
                                                                        );
                                        $insert_area_manager   = AreaManagerProductCategory::firstOrCreate($area_manager_states);
                                    }

                                    $product_Ucategory = DB::table('area_manager_product_categories_orn')
                                            ->where('area_manager_id',$area_data[0]->area_manager_id)
                                            ->update(['orn_status'=>'2']);
                                }
                                $area_Udata  = DB::table('area_managers_orn')
                                                ->where('area_managers_orn_id', $user_id)
                                                ->update(['orn_status'=>'2']);
                                echo '1';die; 
                            }
                            else
                            {
                                echo '2';die;
                            }
                        }
                    }
                }
                elseif ($user_type == 'Regional') 
                {
                    $regional_data  = DB::table('regional_managers_orn')
                                ->where('regional_managers_orn_id', $user_id)
                                ->where('orn_status','0')
                                ->get();
                    if (count($regional_data)>0) 
                    {
                        if ($regional_data[0]->regional_manager_id == 0) {
                            $regional_manager_array     = array(
                                                        'ECN'                       => $regional_data[0]->ECN,
                                                        'dash'                      => $regional_data[0]->dash,
                                                        'password'                  => $regional_data[0]->password,
                                                        'zonal_manager_id'          => $regional_data[0]->zonal_manager_id,
                                                        'regional_manager_hq_code'  => $regional_data[0]->regional_manager_hq_code,
                                                        'regional_manager_name'     => $regional_data[0]->regional_manager_name,
                                                        'regional_manager_mobile'   => $regional_data[0]->regional_manager_mobile,
                                                        'regional_manager_pincode'  => $regional_data[0]->regional_manager_pincode,
                                                        'status'                    => '1',
                                                        'created_at' => $regional_data[0]->updated_at
                                                    );
                            $insert_regional_manager    = RegionalManager::firstOrCreate($regional_manager_array);
                            $result                     = $insert_regional_manager->regional_manager_id;

                            if($result) {
                                $regional_state = DB::table('regional_manager_states_orn')
                                    ->where('regional_manager_id', $regional_data[0]->regional_manager_id)
                                    ->where('orn_status','0')
                                    ->get();
                                if (count($regional_state)>0) {
                                    $regional_manager_states   = array();
                                    foreach($regional_state AS $key => $values) {
                                        $regional_manager_states[] = array(
                                                        'regional_manager_id'   => $result,
                                                        'state_id'              => $regional_state[$key]->state_id,
                                                        'status'                => '1'
                                                        );
                                    }                    
                                    $insert_regional_manager_states= RegionalManagerState::insert($regional_manager_states);
                                    $regional_Ustate = DB::table('regional_manager_states_orn')
                                        ->where('regional_manager_id', $user_id)
                                        ->update(['orn_status'=>'2']);
                                }
                                
                                
                                $product_category = DB::table('regional_manager_product_categories_orn')
                                    ->where('regional_manager_id', $regional_data[0]->regional_manager_id)
                                    ->where('orn_status','0')
                                    ->get();
                                if (count($product_category)>0) {
                                    $regional_manager_product_category = array();
                                    foreach($product_category AS $key => $values) 
                                    {
                                        $regional_manager_product_category[]   = array(
                                                        'regional_manager_id'       => $result,
                                                        'product_crop_category_id'  => $product_category[$key]->product_crop_category_id,
                                                        'status'                    => '1'
                                                                                );
                                    }
                                    $insert_regional_manager_product_category  = RegionalManagerProductCategory::insert($regional_manager_product_category);
                                    $product_Ucategory = DB::table('regional_manager_product_categories_orn')
                                        ->where('regional_manager_id', $user_id)
                                        ->update(['orn_status'=>'2']);
                                }
                                $regional_data  = DB::table('regional_managers_orn')
                                                    ->where('regional_managers_orn_id', $user_id)
                                                    ->update(['orn_status'=>'2']);
                                echo '1';die;
                            }
                            else
                            {
                                echo '2';die;
                            }
                        }
                        else
                        {
                            $regional_manager_array     = array(
                                                        'ECN'                       => $regional_data[0]->ECN,
                                                        'dash'                      => $regional_data[0]->dash,
                                                        'password'                  => $regional_data[0]->password,
                                                        'zonal_manager_id'          => $regional_data[0]->zonal_manager_id,
                                                        'regional_manager_hq_code'  => $regional_data[0]->regional_manager_hq_code,
                                                        'regional_manager_name'     => $regional_data[0]->regional_manager_name,
                                                        'regional_manager_mobile'   => $regional_data[0]->regional_manager_mobile,
                                                        'regional_manager_pincode'  => $regional_data[0]->regional_manager_pincode,
                                                        'status'                    => $regional_data[0]->status,
                                                        'updated_at' => $regional_data[0]->updated_at
                                                    );
                            $update_regional_manager   = DB::table('regional_managers')
                                                    ->where('regional_manager_id', $regional_data[0]->regional_manager_id)
                                                    ->where('status', '1')
                                                    ->update($regional_manager_array); 
                            if ($update_regional_manager) {
                                $update_area_manager  = DB::table('area_managers')
                                                ->where('regional_manager_id', $regional_data[0]->regional_manager_id)
                                                ->where('status', '1')
                                                ->update(['zonal_manager_id'=>$regional_data[0]->zonal_manager_id]); 

                                $update_bdo = DB::table('users')
                                                ->where('regional_manager_id', $regional_data[0]->regional_manager_id)
                                                ->where('status', '1')
                                                ->update(['zonal_manager_id'=>$regional_data[0]->zonal_manager_id]); 

                                $regional_state = DB::table('regional_manager_states_orn')
                                    ->where('regional_manager_id',$regional_data[0]->regional_manager_id)
                                    ->where('orn_status','0')
                                    ->get();
                                if (count($regional_state)>0) {
                                    $reset_regional_manager_states   = DB::table('regional_manager_states')
                                                ->where('regional_manager_id', $regional_data[0]->regional_manager_id)
                                                ->update(['status'=>'0']); 
                                    foreach($regional_state AS $key => $values) {
                                        $update_regional_manager   = DB::table('regional_manager_states')
                                                        ->where('regional_manager_id', $regional_data[0]->regional_manager_id)
                                                        ->where('state_id', $regional_state[$key]->state_id)
                                                        ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                                        $regional_manager_states   = array(
                                                        'regional_manager_id'  => $regional_data[0]->regional_manager_id,
                                                        'state_id'          => $regional_state[$key]->state_id,
                                                        'status'            => '1'
                                                        );
                                        $insert_regional_manager   = RegionalManagerState::firstOrCreate($regional_manager_states);
                                    } 

                                    $regional_state = DB::table('regional_manager_states_orn')
                                            ->where('regional_manager_id',$regional_data[0]->regional_manager_id)
                                            ->update(['orn_status'=>'2']);
                                }
                                 
                                $product_category = DB::table('regional_manager_product_categories_orn')
                                    ->where('regional_manager_id',$regional_data[0]->regional_manager_id)
                                    ->where('orn_status','0')
                                    ->get();
                                if (count($product_category)>0) {
                                    $reset_regional_manager_category= DB::table('regional_manager_product_categories')
                                                    ->where('regional_manager_id', $regional_data[0]->regional_manager_id)
                                                    ->update(['status'=>'0']); 
                                    foreach($product_category AS $key => $values) {
                                        $update_regional_manager   = DB::table('regional_manager_product_categories')
                                                        ->where('regional_manager_id', $regional_data[0]->regional_manager_id)
                                                        ->where('product_crop_category_id', $product_category[$key]->product_crop_category_id)
                                                        ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                                        $regional_manager_states   = array(
                                                    'regional_manager_id'       => $regional_data[0]->regional_manager_id,
                                                    'product_crop_category_id'  => $product_category[$key]->product_crop_category_id,
                                                    'status'                    => '1'
                                                                        );
                                        $insert_regional_manager   = RegionalManagerProductCategory::firstOrCreate($regional_manager_states);
                                    }
                                    $product_Ucategory = DB::table('regional_manager_product_categories_orn')
                                        ->where('regional_manager_id',$regional_data[0]->regional_manager_id)
                                        ->update(['orn_status'=>'2']);
                                }
                                $regional_Udata  = DB::table('regional_managers_orn')
                                                    ->where('regional_managers_orn_id', $user_id)
                                                    ->update(['orn_status'=>'2']);
                                echo '1';die;
                            }
                            else
                            {
                                echo '2';die;
                            }
                        }
                    }
                }
                elseif ($user_type == 'ASM') {
                    $asm_data  = DB::table('sales_area_managers_orn')
                                ->where('sales_area_managers_orn_id', $user_id)
                                ->where('orn_status','0')
                                ->get();
                    // print_r($asm_data);
                    // echo "<br/>total_asm_data=>".count($asm_data);
                    if (count($asm_data)>0) 
                    {
                        // echo "<br/>sales_area_manager_id=>".$asm_data[0]->sales_area_manager_id;
                        if ($asm_data[0]->sales_area_manager_id  == 0) {
                            // echo "<br/> IF Case"; die();
                            /*$sales_area_manager_array   = array(
                                        'ECN'                                        => $asm_data[0]->ECN,
                                        'dash'                                       => $asm_data[0]->dash,
                                        'password'                                   => $asm_data[0]->password,
                                        'zonal_manager_id'                  => $asm_data[0]->zonal_manager_id,
                                        //'regional_manager_id'                            => $asm_data[0]->regional_manager_id,
                                        'sales_regional_manager_id'                      => $asm_data[0]->sales_regional_manager_id,
                                        'sales_regional_manager_hq_code'                 => $asm_data[0]->sales_area_manager_hq_code,
                                        'sales_regional_manager_name'                    => $asm_data[0]->sales_area_manager_name,
                                        'sales_regional_manager_mobile'                  => $asm_data[0]->sales_area_manager_mobile,
                                        'sales_regional_manager_pincode'                 => $asm_data[0]->sales_area_manager_pincode,
                                        'status'     => '1',
                                        'created_at' => $asm_data[0]->updated_at
                                        );

                            $insert_sales_area_manager  = SalesRegionalManager::firstOrCreate($sales_area_manager_array);*/

                            $sales_area_manager_array   = array(
                                        'ECN'                           => $asm_data[0]->ECN,
                                        'dash'                          => $asm_data[0]->dash,
                                        'password'                      => $asm_data[0]->password,
                                        'zonal_manager_id'              => $asm_data[0]->zonal_manager_id,
                                        'regional_manager_id'           => $asm_data[0]->regional_manager_id,
                                        'sales_regional_manager_id'     => $asm_data[0]->sales_regional_manager_id,
                                        'sales_area_manager_hq_code'    => $asm_data[0]->sales_area_manager_hq_code,
                                        'sales_area_manager_name'       => $asm_data[0]->sales_area_manager_name,
                                        'sales_area_manager_mobile'     => $asm_data[0]->sales_area_manager_mobile,
                                        'sales_area_manager_pincode'    => $asm_data[0]->sales_area_manager_pincode,
                                        'status'                        => '1',
                                        'updated_at'                    => $asm_data[0]->updated_at
                                        );
                            $insert_sales_area_manager  = SalesAreaManager::firstOrCreate($sales_area_manager_array);

                            $result  = $insert_sales_area_manager->sales_area_manager_id;
                            if ($result) {
                                $sales_area_state  = DB::table('sales_area_manager_states_orn')
                                            ->where('sales_area_manager_id', $user_id)
                                            ->where('orn_status','0')
                                            ->get(); 
                                if (count($sales_area_state)>0) {
                                    $sales_area_manager_states   = array();
                                    foreach($sales_area_state AS $key => $values) {
                                        $sales_area_manager_states[] = array(
                                                            'sales_area_manager_id' => $result,
                                                            'state_id'              => $sales_area_state[$key]->state_id,
                                                                        'status'                => '1'
                                                                        );
                                    }                    
                                    $insert_sales_area_manager_states= SalesAreaManagerState::insert($sales_area_manager_states);

                                    $sales_area_Ustate  = DB::table('sales_area_manager_states_orn')
                                            ->where('sales_area_manager_id', $user_id)
                                            ->update(['orn_status'=>'2']); 
                                }   
                                
                                
                                $product_category  = DB::table('sales_area_manager_product_categories_orn')
                                            ->where('sales_area_manager_id', $user_id)
                                            ->where('orn_status','0')
                                            ->get(); 
                                if (count($product_category)>0) {
                                    $sales_area_manager_product_category = array();
                                    foreach($product_category AS $key => $values) {
                                        $sales_area_manager_product_category[]   = array(
                                                    'sales_area_manager_id'     => $result,
                                                    'product_crop_category_id'  => $product_category[$key]->product_crop_category_id,
                                                    'status'                    => '1'
                                                    );
                                    }
                                    $insert_sales_area_manager_product_category  = SalesAreaManagerProductCategory::insert($sales_area_manager_product_category);

                                    $product_Ucategory  = DB::table('sales_area_manager_product_categories_orn')
                                            ->where('sales_area_manager_id', $user_id)
                                            ->update(['orn_status'=>'2']); 
                                }

                                $asm_Udata  = DB::table('sales_area_managers_orn')
                                            ->where('sales_area_managers_orn_id', $user_id)
                                            ->update(['orn_status'=>'2']);
                                echo '1';die;
                                
                            }
                            else
                            {
                                echo 2;die;
                            }
                        }
                        else
                        {
                            // echo "<br/>Else Case"; die;
                            /*$sales_area_manager_array   = array(
                                        'ECN'      => $asm_data[0]->ECN,
                                        'dash'     => $asm_data[0]->dash,
                                        'password'                          => $asm_data[0]->password,
                                        'zonal_manager_id'                  => $asm_data[0]->zonal_manager_id,
                                        'regional_manager_id'                  => $asm_data[0]->regional_manager_id,
                                        'sales_regional_manager_id'                  => $asm_data[0]->sales_regional_manager_id,
                                        'sales_area_manager_hq_code'    => $asm_data[0]->sales_area_manager_hq_code,
                                        'sales_area_manager_name'       => $asm_data[0]->sales_area_manager_name,
                                        'sales_area_manager_mobile'     => $asm_data[0]->sales_area_manager_mobile,
                                        'sales_area_manager_pincode'    => $asm_data[0]->sales_area_manager_pincode,
                                        'status'     => '1',
                                        'updated_at' => $asm_data[0]->updated_at
                                        );

                            $update_sales_area_manager  = DB::table('sales_area_managers')
                                                    ->where('sales_area_manager_id', $asm_data[0]->sales_area_manager_id)
                                                    ->where('status', '1')
                                                    ->update($sales_area_manager_array);
                            if ($update_sales_area_manager) {
                                $update_bdo = DB::table('users')
                                            ->where('sales_regional_manager_id', $asm_data[0]->sales_regional_manager_id)
                                            ->where('status', '1')
                                            ->update(['zonal_manager_id'=>$asm_data[0]->zonal_manager_id, 'regional_manager_id'=>$asm_data[0]->regional_manager_id]); 
                    
                    
                                $sales_area_state  = DB::table('sales_area_manager_states_orn')
                                                ->where('sales_area_manager_id', $user_id)
                                                ->where('orn_status','0')
                                                ->get(); 
                                if (count($sales_area_state)>0) { 
                                    $reset_sales_area_manager_states   = DB::table('sales_area_manager_states')
                                                                ->where('sales_area_manager_id', $asm_data[0]->sales_area_manager_id)
                                                                ->update(['status'=>'0']);
                                    foreach($sales_area_state AS $key => $values) {
                                        $update_sales_area_manager  = DB::table('sales_area_manager_states')
                                                            ->where('sales_area_manager_id', $asm_data[0]->sales_area_manager_id)
                                                            ->where('state_id', $sales_area_state[$key]->state_id)
                                                            ->update(['status'=>'1']);
                                        $sales_area_manager_states   = array(
                                                            'sales_area_manager_id' => $asm_data[0]->sales_area_manager_id,
                                                            'state_id'              => $sales_area_state[$key]->state_id,
                                                            'status'                => '1'
                                                            );
                                        $insert_sales_area_manager   = SalesAreaManagerState::firstOrCreate($sales_area_manager_states);
                                    }  

                                    $sales_area_Ustate  = DB::table('sales_area_manager_states_orn')
                                                ->where('sales_area_manager_id', $asm_data[0]->sales_area_manager_id)
                                                ->update(['orn_status'=>'2']);
                                }
                                
                                $product_category  = DB::table('sales_area_manager_product_categories_orn')
                                                ->where('sales_area_manager_id', $user_id)
                                                ->where('orn_status','0')
                                                ->get(); 
                                if (count($product_category)>0) {
                                    $reset_sales_area_manager_category= DB::table('sales_area_manager_product_categories')
                                                            ->where('sales_area_manager_id', $asm_data[0]->sales_area_manager_id)
                                                            ->update(['status'=>'0']); 
                                    foreach($product_category AS $key => $values) {
                                        $update_sales_area_manager  = DB::table('sales_area_manager_product_categories')
                                                            ->where('sales_area_manager_id', $asm_data[0]->sales_area_manager_id)
                                                            ->where('product_crop_category_id', $product_category[$key]->product_crop_category_id)
                                                            ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                                        $sales_area_manager_states  = array(
                                                            'sales_area_manager_id'       => $asm_data[0]->sales_area_manager_id,
                                                            'product_crop_category_id'  => $product_category[$key]->product_crop_category_id,
                                                            'status'                    => '1'
                                                                        );
                                        $insert_sales_area_manager        = SalesAreaManagerProductCategory::firstOrCreate($sales_area_manager_states);

                                        $product_Ucategory  = DB::table('sales_area_manager_product_categories_orn')
                                                ->where('sales_area_manager_id', $asm_data[0]->sales_area_manager_id)
                                                ->update(['orn_status' => '2']); 
                                    }
                                }

                                $asm_Udata  = DB::table('sales_area_managers_orn')
                                                ->where('sales_area_managers_orn_id', $user_id)
                                                ->update(['orn_status'=>'2']);
                                echo '1';die;
                            }*/
                            $sales_area_manager_id = $asm_data[0]->sales_area_manager_id;
                            $asm_Udata  = DB::table('sales_area_managers')
                                            ->where('sales_area_manager_id', $sales_area_manager_id)
                                            ->update(['status'=>'0','updated_at'=> date('Y-m-d H:i:s')]);

                            $asm_Udata  = DB::table('sales_area_managers_orn')
                                            ->where('sales_area_managers_orn_id', $user_id)
                                            ->update(['orn_status'=>'2']);
                            echo '1';die;
                            
                        }
                    }
                }  
                elseif ($user_type == 'RSM') {
                    $rsm_data  = DB::table('sales_regional_managers_orn')
                                ->where('sales_regional_managers_orn_id', $user_id)
                                ->where('orn_status','0')
                                ->get();
                    if (count($rsm_data)>0) 
                    {
                        if ($rsm_data[0]->sales_regional_manager_id == 0) {
                            $sales_regional_manager_array   = array(
                                        'ECN'      => $rsm_data[0]->ECN,
                                        'dash'     => $rsm_data[0]->dash,
                                        'password'                          => $rsm_data[0]->password,
                                        'zonal_manager_id'                  => $rsm_data[0]->zonal_manager_id,
                                        'sales_regional_manager_hq_code'    => $rsm_data[0]->sales_regional_manager_hq_code,
                                        'sales_regional_manager_name'       => $rsm_data[0]->sales_regional_manager_name,
                                        'sales_regional_manager_mobile'     => $rsm_data[0]->sales_regional_manager_mobile,
                                        'sales_regional_manager_pincode'    => $rsm_data[0]->sales_regional_manager_pincode,
                                        'status'     => '1',
                                        'created_at' => $rsm_data[0]->updated_at
                                        );

                            $insert_sales_regional_manager  = SalesRegionalManager::firstOrCreate($sales_regional_manager_array);
                            $result  = $insert_sales_regional_manager->sales_regional_manager_id;
                            if ($result) 
                            {       
                                $sales_regional_state  = DB::table('sales_regional_manager_states_orn')
                                            ->where('sales_regional_manager_id', $user_id)
                                            ->where('orn_status','0')
                                            ->get();
                                if (count($sales_regional_state)>0) {
                                    $sales_regional_manager_states   = array();
                                    foreach($sales_regional_state AS $key => $values) {
                                        $sales_regional_manager_states[] = array(
                                            'sales_regional_manager_id' => $result,
                                            'state_id'                  => $sales_regional_state[$key]->state_id,
                                            'status'                    => '1'
                                                                                );
                                    }                    
                                    $insert_sales_regional_manager_states= SalesRegionalManagerState::insert($sales_regional_manager_states);

                                    $rsm_Ustate = DB::table('sales_regional_manager_states_orn')
                                                ->where('sales_regional_manager_id', $user_id)
                                                ->where('orn_status','0')
                                                ->update(['orn_status' => '2']);
                                }
                                
                                $product_category  = DB::table('sales_regional_manager_product_categories_orn')
                                            ->where('sales_regional_manager_id', $user_id)
                                            ->where('orn_status','0')
                                            ->get();  
                                if (count($product_category)>0) {
                                    $sales_regional_manager_product_category = array();
                                    foreach($product_category AS $key => $values) {
                                        $sales_regional_manager_product_category[]   = array(
                                                'sales_regional_manager_id' => $result,
                                                'product_crop_category_id'  => $product_category[$key]->product_crop_category_id,
                                                'status'                    => '1'
                                                );
                                    }
                                    $insert_regional_manager_product_category  = SalesRegionalManagerProductCategory::insert($sales_regional_manager_product_category); 

                                    $rsm_Uproduct = DB::table('sales_regional_manager_product_categories_orn')
                                                ->where('sales_regional_manager_id', $user_id)
                                                ->where('orn_status','0')
                                                ->update(['orn_status' => '2']); 
                                } 

                                $rsmr_data  = DB::table('sales_regional_managers_orn')
                                            ->where('sales_regional_managers_orn_id', $user_id)
                                            ->where('orn_status','0')
                                            ->update(['orn_status'=>'2']); 
                                echo 1;die;
                            }
                            else
                            {
                                echo 2;die;
                            }

                        }
                        else
                        {
                            $sales_regional_manager_array   = array(
                                    'ECN'      => $rsm_data[0]->ECN,
                                    'dash'     => $rsm_data[0]->dash,
                                    'password'                          => $rsm_data[0]->password,
                                    'zonal_manager_id'                  => $rsm_data[0]->zonal_manager_id,
                                    'sales_regional_manager_hq_code'    => $rsm_data[0]->sales_regional_manager_hq_code,
                                    'sales_regional_manager_name'       => $rsm_data[0]->sales_regional_manager_name,
                                    'sales_regional_manager_mobile'     => $rsm_data[0]->sales_regional_manager_mobile,
                                    'sales_regional_manager_pincode'    => $rsm_data[0]->sales_regional_manager_pincode,
                                    'status'                            => '1',
                                    'created_at' => $rsm_data[0]->updated_at
                                        );
                            $update_rsm_data = DB::table('sales_regional_managers')
                                                ->where('sales_regional_manager_id', $rsm_data[0]->sales_regional_manager_id)
                                                ->update($sales_regional_manager_array); 
                            if ($update_rsm_data) {

                                $update_sales_area_manager = DB::table('sales_area_managers')
                                                ->where('sales_regional_manager_id', $rsm_data[0]->sales_regional_manager_id)
                                                ->where('status', '1')
                                                ->update(['zonal_manager_id'=>$rsm_data[0]->zonal_manager_id]);

                                $update_bdo     = DB::table('users')
                                                ->where('sales_regional_manager_id', $rsm_data[0]->sales_regional_manager_id)
                                                ->where('status', '1')
                                                ->update(['zonal_manager_id'=>$rsm_data[0]->zonal_manager_id]);

                                $sales_regional_state  = DB::table('sales_regional_manager_states_orn')
                                            ->where('sales_regional_manager_id', $user_id)
                                            ->where('orn_status','0')
                                            ->get();
                                if(count($sales_regional_state)>0){
                                    $reset_sales_regional_manager_states   = DB::table('sales_regional_manager_states')
                                                                ->where('sales_regional_manager_id', $rsm_data[0]->sales_regional_manager_id)
                                                                ->update(['status'=>'0']); 
                                    foreach($sales_regional_state AS $key => $values) {
                                        $update_sales_regional_manager   = DB::table('sales_regional_manager_states')
                                                ->where('sales_regional_manager_id', $rsm_data[0]->sales_regional_manager_id)
                                                ->where('state_id', $rsm_data[0]->state_id)
                                                ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                                        
                                        $sales_regional_manager_states   = array(
                                                    'sales_regional_manager_id'  => $rsm_data[0]->sales_regional_manager_id,
                                                    'state_id'          => $rsm_data[0]->state_id,
                                                    'status'            => '1'
                                                    );
                                        $insert_sales_regional_manager   = SalesRegionalManagerState::firstOrCreate($sales_regional_manager_states);
                                    } 

                                    $sales_regional_state_r  = DB::table('sales_regional_manager_states_orn')
                                            ->where('sales_regional_manager_id', $rsm_data[0]->sales_regional_manager_id)
                                            ->update(['orn_status'=>'2']);  
                                     
                                }

                                $product_category  = DB::table('sales_regional_manager_product_categories_orn')
                                            ->where('sales_regional_manager_id', $user_id)
                                            ->where('orn_status','0')
                                            ->get();

                                if (count($product_category)>0) {
                                    $reset_regional_manager_category= DB::table('sales_regional_manager_product_categories')
                                                ->where('sales_regional_manager_id', $rsm_data[0]->sales_regional_manager_id)
                                                ->update(['status'=>'0']); 
                                    foreach($product_category AS $key => $values) {
                                        $update_regional_manager   = DB::table('sales_regional_manager_product_categories')
                                                ->where('sales_regional_manager_id', $rsm_data[0]->sales_regional_manager_id)
                                                ->where('product_crop_category_id', $product_category[$key]->product_crop_category_id)
                                                ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                                        $regional_manager_states   = array(
                                                'sales_regional_manager_id' => $rsm_data[0]->sales_regional_manager_id,
                                                'product_crop_category_id'  => $product_category[$key]->product_crop_category_id,
                                                'status'                    => '1'
                                                                        );
                                        $insert_regional_manager   = SalesRegionalManagerProductCategory::firstOrCreate($regional_manager_states);
                                    }

                                    $product_category_r  = DB::table('sales_regional_manager_product_categories_orn')
                                            ->where('sales_regional_manager_id', $rsm_data[0]->sales_regional_manager_id)
                                            ->update(['orn_status'=>'2']);
                                }

                                $rsmr_data  = DB::table('sales_regional_managers_orn')
                                            ->where('sales_regional_managers_orn_id', $user_id)
                                            ->where('orn_status','0')
                                            ->update(['orn_status'=>'2']);
                                echo '1';die;
                                
                            }
                            else
                            {
                                echo '2';die;
                            }
                        }
                    }
                }    
                elseif ($user_type == 'BDO') {
                    $bdo_data  = DB::table('users_orn')
                                ->where('users_orn_id', $user_id)
                                ->where('orn_status','0')
                                ->get();
                    if (count($bdo_data)>0) 
                    {   
                        if ($bdo_data[0]->id == 0) {
                            $bdo_product = DB::table('user_product_categories_orn')
                                            ->where('user_id', $user_id)
                                            ->where('orn_status','0')
                                            ->get();

                            //inserting bdo data
                            $insert_bdo     = array(       
                                                'ECN'                       => $bdo_data[0]->bdo_code,
                                                'password'                  => $bdo_data[0]->password,
                                                'dash'                      => $bdo_data[0]->dash,
                                                'zonal_manager_id'          => $bdo_data[0]->zonal_manager_id,
                                                'regional_manager_id'       => $bdo_data[0]->regional_manager_id,
                                                'sales_regional_manager_id' => $bdo_data[0]->sales_regional_manager_id,
                                                'area_manager_id'           => $bdo_data[0]->area_manager_id,
                                                'sales_area_manager_id'     => $bdo_data[0]->sales_area_manager_id,
                                                'state_id'                  => $bdo_data[0]->state_id,
                                                'district_id'               => $bdo_data[0]->district_id,
                                                'language_id'               => $bdo_data[0]->language_id,
                                                'department'                => $bdo_data[0]->department,
                                                'designation'               => $bdo_data[0]->designation, 
                                                'counterpart_fl'               => $bdo_data[0]->counterpart_fl,                           
                                                'bdo_code'                  => $bdo_data[0]->bdo_code,
                                                'name'                      => $bdo_data[0]->name,                            
                                                'bdo_mobile_no'             => $bdo_data[0]->bdo_mobile_no,                            
                                                'bdo_hq'                    => $bdo_data[0]->bdo_hq, 
                                                'status'                    => $bdo_data[0]->status,
                                                'app_permission_edit_ticket'    => $bdo_data[0]->app_permission_edit_ticket,
                                                'app_permission_close_ticket'   => $bdo_data[0]->app_permission_close_ticket,
                                                'updated_at'                => $bdo_data[0]->updated_at
                                            ); 
                            $insert_user    = User::firstOrCreate($insert_bdo);
                            $result         = $insert_user->id;
                            if($result) {
                                if (count($bdo_product)>0) {
                                    $user_product_category = array();
                                    foreach($bdo_product AS $key => $values) {
                                        $user_product_category[]    = array(
                                                    'user_id'                   => $result,
                                                    'product_crop_category_id'  => $bdo_product[$key]->product_crop_category_id,
                                                    'status'                    => '1'
                                                                            );
                                    }
                                    $insert_user_product_category   = UserProductCategory::insert($user_product_category);
                                    $bdo_Uproduct = DB::table('user_product_categories_orn')
                                            ->where('user_id', $user_id)
                                            ->where('orn_status','0')
                                            ->update(['orn_status' => '2']);
                                }
                                
                                $bdo_Udata  = DB::table('users_orn')
                                            ->where('users_orn_id', $user_id)
                                            ->where('orn_status','0')
                                            ->update(['orn_status' => '2']);
                                
                                echo 1;die;
                            }
                            else 
                            {   
                                echo 2;die;
                            }
                        }
                        else
                        {   
                            $bdo_product = DB::table('user_product_categories_orn')
                                            ->where('user_id', $user_id)
                                            ->where('orn_status','0')
                                            ->get();
                            //updating dd data
                            $update_bdo     = array(       
                                                'ECN'                       => $bdo_data[0]->bdo_code,
                                                'password'                  => $bdo_data[0]->password,
                                                'dash'                      => $bdo_data[0]->dash,
                                                'zonal_manager_id'          => $bdo_data[0]->zonal_manager_id,
                                                'regional_manager_id'       => $bdo_data[0]->regional_manager_id,
                                                'sales_regional_manager_id' => $bdo_data[0]->sales_regional_manager_id,
                                                'area_manager_id'           => $bdo_data[0]->area_manager_id,
                                                'sales_area_manager_id'     => $bdo_data[0]->sales_area_manager_id,
                                                'state_id'                  => $bdo_data[0]->state_id,
                                                'district_id'               => $bdo_data[0]->district_id,
                                                'language_id'               => $bdo_data[0]->language_id,
                                                'department'                => $bdo_data[0]->department,
                                                'designation'               => $bdo_data[0]->designation, 
                                                'counterpart_fl'               => $bdo_data[0]->counterpart_fl,                           
                                                'bdo_code'                  => $bdo_data[0]->bdo_code,
                                                'name'                      => $bdo_data[0]->name,                            
                                                'bdo_mobile_no'             => $bdo_data[0]->bdo_mobile_no,                            
                                                'bdo_hq'                    => $bdo_data[0]->bdo_hq, 
                                                'status'                    => $bdo_data[0]->status,
                                                'app_permission_edit_ticket'    => $bdo_data[0]->app_permission_edit_ticket,
                                                'app_permission_close_ticket'   => $bdo_data[0]->app_permission_close_ticket,
                                                'updated_at'                => $bdo_data[0]->updated_at
                                            ); 
                            $update_bdo_data = DB::table('users')
                                                ->where('id', $bdo_data[0]->id)
                                                ->update($update_bdo); 
                            if ($update_bdo_data) {
                                if (count($bdo_product)>0) {
                                    $reset_bdo_category = DB::table('user_product_categories')
                                                        ->where('user_id', $bdo_data[0]->id)
                                                        ->update(['status'=>'0']); 

                                    foreach($bdo_product AS $key => $values) {
                                        $update_bdo_manager   = DB::table('user_product_categories')
                                                                    ->where('user_id', $bdo_data[0]->id)
                                                                    ->where('product_crop_category_id', $bdo_product[$key]->product_crop_category_id)
                                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                                        $bdo_manager_states   = array(
                                                                        'user_id'                   => $bdo_data[0]->id,
                                                                        'product_crop_category_id'  => $bdo_product[$key]->product_crop_category_id,
                                                                        'status'                    => '1'
                                                                        );
                                        $insert_bdo_manager   = DdProductCategory::firstOrCreate($dd_manager_states);
                                    }
                                    $bdo_Uproduct = DB::table('user_product_categories_orn')
                                                ->where('user_id', $bdo_data[0]->id)
                                                ->where('orn_status','0')
                                                ->update(['orn_status' => '2']);
                                }
                                
                                $bdo_Udata  = DB::table('users_orn')
                                            ->where('users_orn_id', $user_id)
                                            ->update(['orn_status' => '2']);
                                echo 1;die;

                            } 
                            else
                            {
                                echo 2;die;
                            }
                        }
                    }
                } 
                elseif ($user_type == 'DD') {
                    $dd_data  = DB::table('dhanuka_doctor_orn')
                                ->where('dhanuka_doctor_orn_id', $user_id)
                                ->where('orn_status','0')
                                ->get();
                    if (count($dd_data)>0) 
                    {   
                        if ($dd_data[0]->dd_id == 0) {
                            $dd_product = DB::table('dd_product_categories_orn')
                                            ->where('dd_id', $user_id)
                                            ->where('orn_status','0')
                                            ->get();

                            $dd_village = DB::table('assigned_village_orn')
                                            ->where('assigned_to_dd_id', $user_id)
                                            ->where('orn_status','0')
                                            ->get();
                            //inserting dd data
                            $insert_dd     = array(       
                                            'ECN'                       => $dd_data[0]->ECN,
                                            'password'                  => $dd_data[0]->password,
                                            'dash'                      => $dd_data[0]->dash,
                                            'zonal_manager_id'          => $dd_data[0]->zonal_manager_id,
                                            'regional_manager_id'       => $dd_data[0]->regional_manager_id,
                                            'sales_regional_manager_id' => $dd_data[0]->sales_regional_manager_id,
                                            'area_manager_id'           => $dd_data[0]->area_manager_id,
                                            'sales_area_manager_id'     => $dd_data[0]->sales_area_manager_id,
                                            'state_id'                  => $dd_data[0]->state_id,
                                            'district_id'               => $dd_data[0]->district_id,
                                            'language_id'               => $dd_data[0]->language_id,
                                            'department'                => $dd_data[0]->department,
                                            'designation'               => $dd_data[0]->designation,
                                            'dd_code'                   => $dd_data[0]->dd_code,
                                            'name'                      => $dd_data[0]->name,                            
                                            'dd_mobile_no'              => $dd_data[0]->dd_mobile_no,                            
                                            'dd_hq'                     => $dd_data[0]->dd_hq, 
                                            'status'                    => $dd_data[0]->status,
                                            'dd_alt_mobile_no'          => $dd_data[0]->dd_alt_mobile_no,
                                            'app_permission_edit_ticket' => $dd_data[0]->app_permission_edit_ticket,
                                            'app_permission_close_ticket' => $dd_data[0]->app_permission_close_ticket,
                                            'dd_email'                  => $dd_data[0]->dd_email,
                                            'dd_address'                => $dd_data[0]->dd_address,
                                            'bdo_id'                    => $dd_data[0]->bdo_id,
                                            'tehsil_id'                 => $dd_data[0]->tehsil_id,
                                            'created_at'                => $dd_data[0]->updated_at
                                                    ); 
                            $insert_user    = DhanukaDoctor::firstOrCreate($insert_dd);
                            $result         = $insert_user->dd_id;
                            if($result) {
                                if (count($dd_product)>0) {
                                    $dd_product_category = array();
                                    foreach($dd_product AS $key => $values) {
                                        $dd_product_category[]    = array(
                                                                        'dd_id' => $result,
                                                                        'product_crop_category_id' => $dd_product[$key]->product_crop_category_id,
                                                                        'status' => '1'
                                                                        );
                                    }
                                    $insert_dd_product_category = DdProductCategory::insert($dd_product_category);
                                    $dd_Uproduct = DB::table('dd_product_categories_orn')
                                                ->where('dd_id', $user_id)
                                                ->update(['orn_status' => '2']);
                                }
                                
                                //insert village data
                                if (count($dd_village)>0) {
                                    $dd_village_data = array();
                                    foreach($dd_village AS $key => $values) {
                                        $dd_village_data[] = array(
                                                                    'assigned_to_dd_id' => $result,
                                                                    'assigned_by_bdo_id' => $dd_village[$key]->assigned_by_bdo_id,
                                                                    'village_id' => $dd_village[$key]->village_id,
                                                                    'created_at' => date('Y-m-d H:i:s'),
                                                                    'updated_at' => date('Y-m-d H:i:s')
                                                                    );
                                    }
                                    $insert_dd_village_data = AssignedVillage::insert($dd_village_data); 
                                    $dd_Uvillage = DB::table('assigned_village_orn')
                                                ->where('assigned_to_dd_id', $user_id)
                                                ->where('orn_status','0')
                                                ->update(['orn_status' => '2']);
                                }
                                
                                $dd_Udata  = DB::table('dhanuka_doctor_orn')
                                            ->where('dhanuka_doctor_orn_id', $user_id)
                                            ->where('orn_status','0')
                                            ->update(['orn_status' => '2']);
                                
                                echo 1;die;
                            }
                            else 
                            {   
                                echo 2;die;
                            }
                        }
                        else
                        {   
                            $dd_product = DB::table('dd_product_categories_orn')
                                            ->where('dd_id', $dd_data[0]->dd_id)
                                            ->where('orn_status','0')
                                            ->get();

                            $dd_village = DB::table('assigned_village_orn')
                                            ->where('assigned_to_dd_id', $dd_data[0]->dd_id)
                                            ->where('orn_status','0')
                                            ->get();
                            //updating dd data
                            $update_dd     = array(       
                                            'ECN'                       => $dd_data[0]->ECN,
                                            'password'                  => $dd_data[0]->password,
                                            'dash'                      => $dd_data[0]->dash,
                                            'zonal_manager_id'          => $dd_data[0]->zonal_manager_id,
                                            'regional_manager_id'       => $dd_data[0]->regional_manager_id,
                                            'sales_regional_manager_id' => $dd_data[0]->sales_regional_manager_id,
                                            'area_manager_id'           => $dd_data[0]->area_manager_id,
                                            'sales_area_manager_id'     => $dd_data[0]->sales_area_manager_id,
                                            'state_id'                  => $dd_data[0]->state_id,
                                            'district_id'               => $dd_data[0]->district_id,
                                            'language_id'               => $dd_data[0]->language_id,
                                            'department'                => $dd_data[0]->department,
                                            'designation'               => $dd_data[0]->designation,
                                            'dd_code'                   => $dd_data[0]->dd_code,
                                            'name'                      => $dd_data[0]->name,                            
                                            'dd_mobile_no'              => $dd_data[0]->dd_mobile_no,                            
                                            'dd_hq'                     => $dd_data[0]->dd_hq, 
                                            'status'                    => $dd_data[0]->status,
                                            'dd_alt_mobile_no'          => $dd_data[0]->dd_alt_mobile_no,
                                            'app_permission_edit_ticket' => $dd_data[0]->app_permission_edit_ticket,
                                            'app_permission_close_ticket' => $dd_data[0]->app_permission_close_ticket,
                                            'dd_email'                  => $dd_data[0]->dd_email,
                                            'dd_address'                => $dd_data[0]->dd_address,
                                            'bdo_id'                    => $dd_data[0]->bdo_id,
                                            'tehsil_id'                 => $dd_data[0]->tehsil_id,
                                            'updated_at'                => $dd_data[0]->updated_at
                                                    );
                            $update_dd_data = DB::table('dhanuka_doctor')
                                                ->where('dd_id', $dd_data[0]->dd_id)
                                                ->update($update_dd); 
                            if ($update_dd_data) {
                                if (count($dd_product)>0) {
                                    $reset_dd_category = DB::table('dd_product_categories')
                                                        ->where('dd_id', $dd_data[0]->dd_id)
                                                        ->update(['status'=>'0']); 

                                    foreach($dd_product AS $key => $values) {
                                        $update_dd_manager   = DB::table('dd_product_categories')
                                                                    ->where('dd_id', $dd_data[0]->dd_id)
                                                                    ->where('product_crop_category_id', $dd_product[$key]->product_crop_category_id)
                                                                    ->update(['status'=>'1', 'updated_at' => date('Y-m-d H:i:s')]);
                                        $dd_manager_states   = array(
                                                                        'dd_id'                   => $dd_data[0]->dd_id,
                                                                        'product_crop_category_id'  => $dd_product[$key]->product_crop_category_id,
                                                                        'status'                    => '1'
                                                                        );
                                        $insert_dd_manager   = DdProductCategory::firstOrCreate($dd_manager_states);
                                    }
                                    $dd_Uproduct = DB::table('dd_product_categories_orn')
                                                ->where('dd_id', $dd_data[0]->dd_id)
                                                ->where('orn_status','0')
                                                ->update(['orn_status' => '2']);
                                }
                                

                                //dd village update
                                if (count($dd_village)>0) {
                                    $reset_dd_delete    = DB::table('assigned_village')->where('assigned_to_dd_id', $dd_data[0]->dd_id)->delete(); 
                                    $dd_village_data = array();
                                    foreach($dd_village AS $key => $values) {
                                        $dd_village_data[] = array(
                                                                    'assigned_to_dd_id' => $dd_data[0]->dd_id,
                                                                    'assigned_by_bdo_id' => $dd_village[$key]->assigned_by_bdo_id,
                                                                    'village_id' => $dd_village[$key]->village_id,
                                                                    'created_at' => date('Y-m-d H:i:s'),
                                                                    'updated_at' => date('Y-m-d H:i:s')
                                                                    );
                                    }
                                    $insert_dd_village_data = AssignedVillage::insert($dd_village_data); 

                                    $dd_Uvillage = DB::table('assigned_village_orn')
                                                ->where('assigned_to_dd_id', $dd_data[0]->dd_id)
                                                ->where('orn_status','0')
                                                ->update(['orn_status' => '2']);
                                }

                                $dd_Udata  = DB::table('dhanuka_doctor_orn')
                                            ->where('dhanuka_doctor_orn_id', $user_id)
                                            ->update(['orn_status' => '2']);
                                echo 1;die;

                            } 
                            else
                            {
                                echo 2;die;
                            }
                        }
                    }
                } 
            }
            else {
                echo '0';
            }
        }   

        public function getOrganogramDetail(Request $request){
            $sess       =   \Session::get('adminid');
            $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
            if ($userData) {
                
                $user_id      = \Request::segment(3);            
                $user_type    = \Request::segment(4); 
                
                if ($user_type == 'Area') 
                {
                    if($user_id!='') {
                        $regionalData = DB::table('area_managers_orn as rmo')
                                        ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
                                        ->join('regional_managers as r','r.regional_manager_id','=','rmo.regional_manager_id','left')
                                        ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmo.sales_regional_manager_id','left')
                                        ->where('rmo.area_managers_orn_id',$user_id)
                                        ->select('rmo.*','z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name')
                                        ->get();
                        if (count($regionalData)>0) {
                            $regionalData[0]->changepass = '0';
                            if ($regionalData[0]->area_manager_id == 0) {
                                //getting state
                                $regionalState = DB::table('area_manager_states_orn as rmso')
                                                ->join('states as s','s.state_id','=','rmso.state_id','left')
                                                ->where('rmso.area_manager_id',$regionalData[0]->area_managers_orn_id)
                                                ->where('rmso.orn_id',$regionalData[0]->area_managers_orn_id)
                                                ->select('s.state')
                                                ->get();
                                if (count($regionalState)>0) {
                                    $regionalData[0]->states = $regionalState;
                                } 
                                else 
                                {
                                    $regionalData[0]->states = [];
                                }              
                                //getting products
                                $regionalProdut = DB::table('area_manager_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.area_manager_id',$regionalData[0]->area_managers_orn_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->area_managers_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else 
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editareamanager',array('regionalData'=>$regionalData));
                            }
                            else
                            {   
                                $checkpass = DB::table('area_managers')
                                             ->where('area_manager_id',$regionalData[0]->area_manager_id)
                                             ->select('dash')
                                             ->get();
                                if (count($checkpass)>0) {
                                    if ($regionalData[0]->dash == $checkpass[0]->dash) 
                                    {
                                        $regionalData[0]->changepass = '0';
                                    }
                                    else
                                    {
                                        $regionalData[0]->changepass = '1';
                                    }
                                }
                                else
                                {
                                    $regionalData[0]->changepass = '0';
                                }
                                
                                //getting state
                                 $regionalState = DB::table('area_manager_states_orn as rmso')
                                                ->join('states as s','s.state_id','=','rmso.state_id','left')
                                                ->where('rmso.area_manager_id',$regionalData[0]->area_manager_id)
                                                ->where('rmso.orn_id',$regionalData[0]->area_managers_orn_id)
                                                ->select('s.state')
                                                ->get();
                                if (count($regionalState)>0) {
                                    $regionalData[0]->states = $regionalState;
                                }
                                else 
                                {
                                    $regionalData[0]->states = [];
                                }               
                                //getting products
                                $regionalProdut = DB::table('area_manager_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.area_manager_id',$regionalData[0]->area_manager_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->area_managers_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else 
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editareamanager',array('regionalData'=>$regionalData));
                            }
                            
                        }

                    }
                    else {
                        return redirect('/admin/organogram-list');
                    }
                }  
                elseif ($user_type == 'Regional')
                {
                    if($user_id!='') {
                        $regionalData = DB::table('regional_managers_orn as rmo')
                                        ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
                                        ->where('rmo.regional_managers_orn_id',$user_id)
                                        ->select('rmo.*','z.zonal_manager_name')
                                        ->get();
                        if (count($regionalData)>0) {
                            $regionalData[0]->changepass = '0';
                            if ($regionalData[0]->regional_manager_id == 0) {
                                //getting state
                                $regionalState = DB::table('regional_manager_states_orn as rmso')
                                                ->join('states as s','s.state_id','=','rmso.state_id','left')
                                                ->where('rmso.regional_manager_id',$regionalData[0]->regional_managers_orn_id)
                                                ->where('rmso.orn_id',$regionalData[0]->regional_managers_orn_id)
                                                ->select('s.state')
                                                ->get();
                                if (count($regionalState)>0) {
                                    $regionalData[0]->states = $regionalState;
                                } 
                                else 
                                {
                                    $regionalData[0]->states = [];
                                }              
                                //getting products
                                $regionalProdut = DB::table('regional_manager_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.regional_manager_id',$regionalData[0]->regional_managers_orn_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->regional_managers_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else 
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editregionalmanager',array('regionalData'=>$regionalData));
                            }
                            else
                            {   
                                $checkpass = DB::table('regional_managers')
                                             ->where('regional_manager_id',$regionalData[0]->regional_manager_id)
                                             ->select('dash')
                                             ->get();
                                if (count($checkpass)>0) {
                                    if ($regionalData[0]->dash == $checkpass[0]->dash) 
                                    {
                                        $regionalData[0]->changepass = '0';
                                    }
                                    else
                                    {
                                        $regionalData[0]->changepass = '1';
                                    }
                                }
                                else
                                {
                                    $regionalData[0]->changepass = '0';
                                }
                                
                                //getting state
                                $regionalState = DB::table('regional_manager_states_orn as rmso')
                                                ->join('states as s','s.state_id','=','rmso.state_id','left')
                                                ->where('rmso.regional_manager_id',$regionalData[0]->regional_manager_id)
                                                ->where('rmso.orn_id',$regionalData[0]->regional_managers_orn_id)
                                                ->select('s.state')
                                                ->get();
                                if (count($regionalState)>0) {
                                    $regionalData[0]->states = $regionalState;
                                }  
                                else 
                                {
                                    $regionalData[0]->states = [];
                                }             
                                //getting products
                                $regionalProdut = DB::table('regional_manager_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.regional_manager_id',$regionalData[0]->regional_manager_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->regional_managers_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else 
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editregionalmanager',array('regionalData'=>$regionalData));
                            }
                            
                        }

                    }
                    else {
                        return redirect('/admin/organogram-list');
                    }
                } 
                elseif ($user_type == 'ASM')
                {
                    if($user_id!='') {
                        $regionalData = DB::table('sales_area_managers_orn as rmo')
                                        ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
                                        ->join('regional_managers as r','r.regional_manager_id','=','rmo.regional_manager_id','left')
                                        ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmo.sales_regional_manager_id','left')
                                        ->where('rmo.sales_area_managers_orn_id',$user_id)
                                        ->select('rmo.*','z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name')
                                        ->get();
                        if (count($regionalData)>0) {
                            $regionalData[0]->changepass = '0';
                            if ($regionalData[0]->sales_area_manager_id == 0) {
                                //getting state
                                $regionalState = DB::table('sales_area_manager_states_orn as rmso')
                                                ->join('states as s','s.state_id','=','rmso.state_id','left')
                                                ->where('rmso.sales_area_manager_id',$regionalData[0]->sales_area_managers_orn_id)
                                                ->where('rmso.orn_id',$regionalData[0]->sales_area_managers_orn_id)
                                                ->select('s.state')
                                                ->get();
                                if (count($regionalState)>0) {
                                    $regionalData[0]->states = $regionalState;
                                } 
                                else 
                                {
                                    $regionalData[0]->states = [];
                                }              
                                //getting products
                                $regionalProdut = DB::table('sales_area_manager_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.sales_area_manager_id',$regionalData[0]->sales_area_managers_orn_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->sales_area_managers_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else 
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editsalesareamanager',array('regionalData'=>$regionalData));
                            }
                            else
                            {   
                                $checkpass = DB::table('sales_area_managers')
                                             ->where('sales_area_manager_id',$regionalData[0]->sales_area_manager_id)
                                             ->select('dash')
                                             ->get();
                                if (count($checkpass)>0) {
                                    if ($regionalData[0]->dash == $checkpass[0]->dash) 
                                    {
                                        $regionalData[0]->changepass = '0';
                                    }
                                    else
                                    {
                                        $regionalData[0]->changepass = '1';
                                    }
                                }
                                else
                                {
                                    $regionalData[0]->changepass = '0';
                                }
                                
                                //getting state
                                 $regionalState = DB::table('sales_area_manager_states_orn as rmso')
                                                ->join('states as s','s.state_id','=','rmso.state_id','left')
                                                ->where('rmso.sales_area_manager_id',$regionalData[0]->sales_area_manager_id)
                                                ->where('rmso.orn_id',$regionalData[0]->sales_area_managers_orn_id)
                                                ->select('s.state')
                                                ->get();
                                if (count($regionalState)>0) {
                                    $regionalData[0]->states = $regionalState;
                                }
                                else 
                                {
                                    $regionalData[0]->states = [];
                                }               
                                //getting products
                                $regionalProdut = DB::table('sales_area_manager_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.sales_area_manager_id',$regionalData[0]->sales_area_manager_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->sales_area_managers_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else 
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editsalesareamanager',array('regionalData'=>$regionalData));
                            }
                            
                        }

                    }
                    else {
                        return redirect('/admin/organogram-list');
                    }
                } 
                elseif ($user_type == 'RSM')
                {
                    if($user_id!='') {
                        $regionalData = DB::table('sales_regional_managers_orn as rmo')
                                        ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
                                        ->where('rmo.sales_regional_managers_orn_id',$user_id)
                                        ->select('rmo.*','z.zonal_manager_name')
                                        ->get();
                        if (count($regionalData)>0) {
                            $regionalData[0]->changepass = '0';
                            if ($regionalData[0]->sales_regional_manager_id == 0) {
                                //getting state
                                $regionalState = DB::table('sales_regional_manager_states_orn as rmso')
                                                ->join('states as s','s.state_id','=','rmso.state_id','left')
                                                ->where('rmso.sales_regional_manager_id',$regionalData[0]->sales_regional_managers_orn_id)
                                                ->where('rmso.orn_id',$regionalData[0]->sales_regional_managers_orn_id)
                                                ->select('s.state')
                                                ->get();
                                if (count($regionalState)>0) {
                                    $regionalData[0]->states = $regionalState;
                                }    
                                else 
                                {
                                    $regionalData[0]->states = [];
                                }           
                                //getting products
                                $regionalProdut = DB::table('sales_regional_manager_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.sales_regional_manager_id',$regionalData[0]->sales_regional_managers_orn_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->sales_regional_managers_orn_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->sales_regional_managers_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editsalesregionalmanager',array('regionalData'=>$regionalData));
                            }
                            else
                            {   
                                $checkpass = DB::table('sales_regional_managers')
                                             ->where('sales_regional_manager_id',$regionalData[0]->sales_regional_manager_id)
                                             ->select('dash')
                                             ->get();
                                if (count($checkpass)>0) {
                                    if ($regionalData[0]->dash == $checkpass[0]->dash) 
                                    {
                                        $regionalData[0]->changepass = '0';
                                    }
                                    else
                                    {
                                        $regionalData[0]->changepass = '1';
                                    }
                                }
                                else
                                {
                                    $regionalData[0]->changepass = '0';
                                }
                                
                                //getting state
                                $regionalState = DB::table('sales_regional_manager_states_orn as rmso')
                                                ->join('states as s','s.state_id','=','rmso.state_id','left')
                                                ->where('rmso.sales_regional_manager_id',$regionalData[0]->sales_regional_manager_id)
                                                ->where('rmso.orn_id',$regionalData[0]->sales_regional_managers_orn_id)
                                                ->select('s.state')
                                                ->get();
                                if (count($regionalState)>0) {
                                    $regionalData[0]->states = $regionalState;
                                } 
                                else 
                                {
                                    $regionalData[0]->states = [];
                                }              
                                //getting products
                                $regionalProdut = DB::table('sales_regional_manager_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.sales_regional_manager_id',$regionalData[0]->sales_regional_manager_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->sales_regional_managers_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else 
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editsalesregionalmanager',array('regionalData'=>$regionalData));
                            }
                        }
                    }
                    else {
                        return redirect('/admin/organogram-list');
                    }
                }
                elseif ($user_type == 'BDO')
                {
                    if($user_id!='') {
                        $regionalData = DB::table('users_orn as rmo')
                                        ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
                                        ->join('regional_managers as r','r.regional_manager_id','=','rmo.regional_manager_id','left')
                                        ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmo.sales_regional_manager_id','left')
                                        ->join('area_managers as ar','ar.area_manager_id','=','rmo.area_manager_id','left')
                                        ->join('sales_area_managers as sar','sar.sales_area_manager_id','=','rmo.sales_area_manager_id','left')
                                        ->join('states as s','s.state_id','=','rmo.state_id','left')
                                        ->join('districts as d','d.district_id','=','rmo.district_id','left')
                                        ->join('languages as l','l.id','=','rmo.language_id','left')
                                        ->join('users as u','u.counterpart_fl','=','rmo.id','left')
                                        ->where('rmo.users_orn_id',$user_id)
                                        ->select('rmo.*','z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name','ar.area_manager_name','sar.sales_area_manager_name','s.state','d.district','l.language','u.name as counterpart')
                                        ->get();
                        if (count($regionalData)>0) {
                            $regionalData[0]->changepass = '0';
                            if ($regionalData[0]->id == 0) {
                                //getting deputation
                                $regionalDeputation = DB::table('user_deputations_orn as rmso')
                                                ->join('zonal_managers as z','z.zonal_manager_id','=','rmso.zonal_manager_id','left')
                                                ->join('regional_managers as r','r.regional_manager_id','=','rmso.regional_manager_id','left')
                                                ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmso.sales_regional_manager_id','left')
                                                ->join('area_managers as ar','ar.area_manager_id','=','rmso.area_manager_id','left')
                                                ->join('sales_area_managers as sar','sar.sales_area_manager_id','=','rmso.sales_area_manager_id','left')
                                                ->where('rmso.user_id',$regionalData[0]->users_orn_id)
                                                ->where('rmso.orn_id',$regionalData[0]->users_orn_id)
                                                ->select('z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name','ar.area_manager_name','sar.sales_area_manager_name')
                                                ->get();
                                if (count($regionalDeputation)>0) {
                                    $regionalData[0]->Deputation = $regionalDeputation;
                                } 
                                else 
                                {
                                    $regionalData[0]->Deputation = [];
                                }              
                                //getting products
                                $regionalProdut = DB::table('user_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.user_id',$regionalData[0]->users_orn_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->users_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else 
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editbdo',array('regionalData'=>$regionalData));
                            }
                            else
                            {   
                                $checkpass = DB::table('users')
                                             ->where('id',$regionalData[0]->id)
                                             ->select('dash')
                                             ->get();
                                if (count($checkpass)>0) {
                                    if ($regionalData[0]->dash == $checkpass[0]->dash) 
                                    {
                                        $regionalData[0]->changepass = '0';
                                    }
                                    else
                                    {
                                        $regionalData[0]->changepass = '1';
                                    }
                                }
                                else
                                {
                                    $regionalData[0]->changepass = '0';
                                }
                                
                                //getting deputation
                                $regionalDeputation = DB::table('user_deputations_orn as rmso')
                                                ->join('zonal_managers as z','z.zonal_manager_id','=','rmso.zonal_manager_id','left')
                                                ->join('regional_managers as r','r.regional_manager_id','=','rmso.regional_manager_id','left')
                                                ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmso.sales_regional_manager_id','left')
                                                ->join('area_managers as ar','ar.area_manager_id','=','rmso.area_manager_id','left')
                                                ->join('sales_area_managers as sar','sar.sales_area_manager_id','=','rmso.sales_area_manager_id','left')
                                                ->where('rmso.user_id',$regionalData[0]->id)
                                                ->where('rmso.orn_id',$regionalData[0]->users_orn_id)
                                                ->select('z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name','ar.area_manager_name','sar.sales_area_manager_name')
                                                ->get();
                                if (count($regionalDeputation)>0) {
                                    $regionalData[0]->Deputation = $regionalDeputation;
                                } 
                                else 
                                {
                                    $regionalData[0]->Deputation = [];
                                }              
                                //getting products
                                $regionalProdut = DB::table('user_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.user_id',$regionalData[0]->id)
                                                ->where('rmpco.orn_id',$regionalData[0]->users_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else 
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editbdo',array('regionalData'=>$regionalData));
                            }
                            
                        }

                    }
                    else {
                        return redirect('/admin/organogram-list');
                    }
                } 
                elseif ($user_type == 'DD')
                {
                    if($user_id!='') {
                        $regionalData = DB::table('dhanuka_doctor_orn as rmo')
                                        ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
                                        ->join('regional_managers as r','r.regional_manager_id','=','rmo.regional_manager_id','left')
                                        ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmo.sales_regional_manager_id','left')
                                        ->join('area_managers as ar','ar.area_manager_id','=','rmo.area_manager_id','left')
                                        ->join('sales_area_managers as sar','sar.sales_area_manager_id','=','rmo.sales_area_manager_id','left')
                                        ->join('users as u','u.id','=','rmo.bdo_id','left')
                                        ->join('states as s','s.state_id','=','rmo.state_id','left')
                                        ->join('districts as d','d.district_id','=','rmo.district_id','left')
                                        ->join('tehsils as t','t.tehsil_id','=','rmo.tehsil_id','left')
                                        ->join('languages as l','l.id','=','rmo.language_id','left')
                                        ->where('rmo.dhanuka_doctor_orn_id',$user_id)
                                        ->select('rmo.*','z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name','ar.area_manager_name','sar.sales_area_manager_name','s.state','d.district','t.tehsil','l.language','u.name as bdoname')
                                        ->get();
                        if (count($regionalData)>0) {
                            $regionalData[0]->changepass = '0';
                            if ($regionalData[0]->dd_id == 0) {
                                //getting village
                                $regionalVillage = DB::table('assigned_village_orn as rmso')
                                                ->join('villages as z','z.village_id','=','rmso.village_id','left')
                                                ->where('rmso.assigned_to_dd_id',$regionalData[0]->dhanuka_doctor_orn_id)
                                                ->where('rmso.orn_id',$regionalData[0]->dhanuka_doctor_orn_id)
                                                ->select('z.village')
                                                ->get();
                                if (count($regionalVillage)>0) {
                                    $regionalData[0]->villages = $regionalVillage;
                                } 
                                else 
                                {
                                    $regionalData[0]->villages = [];
                                }              
                                //getting products
                                $regionalProdut = DB::table('dd_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.dd_id',$regionalData[0]->dhanuka_doctor_orn_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->dhanuka_doctor_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else 
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editDD',array('regionalData'=>$regionalData));
                            }
                            else
                            {   
                                $checkpass = DB::table('dhanuka_doctor')
                                             ->where('dd_id',$regionalData[0]->dd_id)
                                             ->select('dash')
                                             ->get();
                                if (count($checkpass)>0) {
                                    if ($regionalData[0]->dash == $checkpass[0]->dash) 
                                    {
                                        $regionalData[0]->changepass = '0';
                                    }
                                    else
                                    {
                                        $regionalData[0]->changepass = '1';
                                    }
                                }
                                else
                                {
                                    $regionalData[0]->changepass = '0';
                                }
                                
                                //getting village
                                $regionalVillage = DB::table('assigned_village_orn as rmso')
                                                ->join('villages as z','z.village_id','=','rmso.village_id','left')
                                                ->where('rmso.assigned_to_dd_id',$regionalData[0]->dd_id)
                                                ->where('rmso.orn_id',$regionalData[0]->dhanuka_doctor_orn_id)
                                                ->select('z.village')
                                                ->get();
                                if (count($regionalVillage)>0) {
                                    $regionalData[0]->villages = $regionalVillage;
                                } 
                                else 
                                {
                                    $regionalData[0]->villages = [];
                                }              
                                //getting products
                                $regionalProdut = DB::table('dd_product_categories_orn as rmpco')
                                                ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
                                                ->where('rmpco.dd_id',$regionalData[0]->dd_id)
                                                ->where('rmpco.orn_id',$regionalData[0]->dhanuka_doctor_orn_id)
                                                ->select('p.category_name')
                                                ->get();
                                if (count($regionalProdut)>0) {
                                    $regionalData[0]->products = $regionalProdut;
                                }
                                else 
                                {
                                    $regionalData[0]->products = [];
                                }
                                return view::make('backend.adminOrganogram.editDD',array('regionalData'=>$regionalData));
                            }
                            
                        }

                    }
                    else {
                        return redirect('/admin/organogram-list');
                    }
                }
            }
        } 
    
    public function postExportallvillagelist(Request $request) {          
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "admin/exportallvillagelist",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $my_data          = Input::all();
            //$limit          = $value['length'];
            //$start          = $value['start'];
            $search_array   = $my_data['search'];     // value & regax
            //$column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
            //$order_array    = $value['order'][0]['column'];
            //$dir            = $value['order'][0]['dir'];
            
            $start_date     = isset($my_data['startdate']) ? $my_data['startdate'] :'';
            $end_date       = isset($my_data['enddate']) ?  $my_data['enddate']:'';

            $search_query=" (1=1)  ";

            if($start_date!='' && $end_date!='') {
                    $search_query.=" and DATE(av.created_at) >= DATE('".$start_date."') AND DATE(av.created_at) <= DATE('".$end_date."')";
            }elseif($start_date!='') {
                    $search_query.= " and DATE(av.created_at) >= DATE('".$start_date."')";
            }
            //echo $search_query;
            if(empty($search_array))
            {
                
                $query    = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    s.state,
                                    d.district,
                                    t.tehsil,
                                    v.village,
                                    (CASE WHEN v.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(av.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                                    av.village_id,
                                    dd.ECN as dd_ecn,
                                    dd.name as dd_name
                                    
                                FROM  
                                assigned_village av 
                                LEFT JOIN dhanuka_doctor dd ON dd.dd_id=av.assigned_to_dd_id
                                LEFT JOIN villages v ON v.village_id=av.village_id    
                                LEFT JOIN tehsils t ON t.tehsil_id=v.tehsil_id
                                LEFT JOIN districts d ON d.district_id=t.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id
                                WHERE
                                    ".$search_query." AND
                                    v.status<>'2'
                                GROUP BY av.village_id,av.assigned_to_dd_id
                                ORDER BY
                                    av.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.tehsil ASC,
                                    v.village ASC
                                )  A,
                                (select @a:=0) initvars";
                $villages = DB::select(DB::raw($query));  
                
                foreach ($villages as $key => $value) {
                    //village dd ecn and name
                    // $ddData = DB::table('assigned_village as av')
                    //           ->join('dhanuka_doctor as dd','dd.dd_id','=','av.assigned_to_dd_id')
                    //           ->where('av.village_id',$villages[$key]->village_id)
                    //           ->select('dd.name','dd.ECN')
                    //           ->get();
                    // $villages[$key]->dd_name = $ddData[0]->name;
                    // $villages[$key]->dd_ecn = $ddData[0]->ECN;

                    //farmer count
                    $fcount = DB::table('village_farmers as vf')
                             ->where('vf.village_id',$villages[$key]->village_id)
                             ->count();
                    $villages[$key]->total_farmer = $fcount;

                    //visits
                    $markVisits = DB::table('mark_visits as mv')
                             ->where('mv.village_id',$villages[$key]->village_id)
                             ->count();
                    $villages[$key]->markVisits = $markVisits;
                    unset($villages[$key]->village_id);
                }
                
                $result         = array_map(function ($values) {
                                                    return (array)$values;
                                                }, $villages);
                $village_column  = array(
                                    'Id',
                                    'State',
                                    'District',
                                    'Tehsil',
                                    'Village',
                                    'Status',
                                    'Created At',
                                    'DD ECN',
                                    'DD Name',
                                    'No. Of Farmer',
                                    'No. Of Visits'
                                    );
                array_unshift($result, $village_column);
            
            
                $primarycolor   =   "rgb(255, 0, 0)";
                $myFile         =   Excel::create("filename", function($excel) use($result, $primarycolor) {
                                        $excel->setTitle('title');
                                        $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
                                            $sheet->fromArray($result, null, 'A1', false, false);
                                            $sheet->row(1, function($row) use ($primarycolor) {
                                                $row->setFontSize(12)
                                                    ->setFontWeight('bold')
                                                    ->setBackground('#62bdb9')
                                                    ->setAlignment('left')
                                                    ->setFontColor('#000000');
                                            });
                                        });
                                     });
                     
                $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
                $response   =  array(
                                'name' => "ACP Village List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                             );
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return response()->json($response); 
            }
            else 
            {
                $search     =  $search_array['search']; 
                //$search_query     = '(1=1)';
                if($search!='') {
                    $search_query     .= " AND (v.village LIKE '".$search."%')";
                }
                $query  = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    s.state,
                                    d.district,
                                    t.tehsil,
                                    v.village,
                                    (CASE WHEN v.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(av.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                                    av.village_id,
                                    dd.ECN as dd_ecn,
                                    dd.name as dd_name
                                FROM  
                                assigned_village av 
                                LEFT JOIN dhanuka_doctor dd ON dd.dd_id=av.assigned_to_dd_id 
                                LEFT JOIN villages v ON v.village_id=av.village_id   
                                LEFT JOIN tehsils t ON t.tehsil_id=v.tehsil_id
                                LEFT JOIN districts d ON d.district_id=t.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id
                                
                                WHERE
                                    ".$search_query." AND
                                    v.status<>'2'
                                GROUP BY av.village_id,av.assigned_to_dd_id
                                ORDER BY
                                    av.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.tehsil ASC,
                                    v.village ASC
                                )   A,
                                (select @a:=0) initvars";
                $villages  = DB::select(DB::raw($query));
                foreach ($villages as $key => $value) {
                    //village dd ecn and name
                    // $ddData = DB::table('assigned_village as av')
                    //           ->join('dhanuka_doctor as dd','dd.dd_id','=','av.assigned_to_dd_id')
                    //           ->where('av.village_id',$villages[$key]->village_id)
                    //           ->select('dd.name', 'dd.ECN')
                    //           ->get();
                    // $villages[$key]->dd_name = $ddData[0]->name;
                    // $villages[$key]->dd_ecn = $ddData[0]->ECN;

                    //farmer count
                    $fcount = DB::table('village_farmers as vf')
                             ->where('vf.village_id',$villages[$key]->village_id)
                             ->count();
                    $villages[$key]->total_farmer = $fcount;

                    //visits
                    $markVisits = DB::table('mark_visits as mv')
                             ->where('mv.village_id',$villages[$key]->village_id)
                             ->count();
                    $villages[$key]->markVisits = $markVisits;
                    unset($villages[$key]->village_id);
                }

                $result         = array_map(function ($values) {
                                                    return (array)$values;
                                                }, $villages);
                $village_column  = array(
                                    'Id',
                                    'State',
                                    'District',
                                    'Tehsil',
                                    'Village',
                                    'Status',
                                    'Created At',
                                    'DD ECN',
                                    'DD Name',
                                    'No. Of Farmer',
                                    'No. Of Visits'
                                    );
                array_unshift($result, $village_column);
            
            
                $primarycolor   =   "rgb(255, 0, 0)";
                $myFile         =   Excel::create("filename", function($excel) use($result, $primarycolor) {
                                        $excel->setTitle('title');
                                        $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
                                            $sheet->fromArray($result, null, 'A1', false, false);
                                            $sheet->row(1, function($row) use ($primarycolor) {
                                                $row->setFontSize(12)
                                                    ->setFontWeight('bold')
                                                    ->setBackground('#62bdb9')
                                                    ->setAlignment('left')
                                                    ->setFontColor('#000000');
                                            });
                                        });
                                     });
                     
                $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
                $response   =  array(
                                'name' => "ACP Village List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                             );
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $sess,'user_type'=>'admin','end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return response()->json($response);           
            }
        }
    }

    //data source 
    public function getDataSourceList()
    {
        return view('backend.datasource.datasourcelist');       
    }

    public function postAlldatasource() {

        $value = Input::all();
        $user_type     = $value['user_type'];
        if($user_type!='') {
                $usertypes = "where find_in_set('".$user_type."',user_type) ";
        }
        else {
            $usertypes = "";
        }
        //data source
        $query      =   
                        " 
                        SELECT
                                data_source_id,
                                data_source,
                                status
                            FROM        data_sources 

                             ".$usertypes."                           
                            GROUP BY data_source_id
                            ORDER BY
                                data_source_id DESC
                            ";
        $datasource    = collect(DB::select($query));
        
        return Datatables::of($datasource)->make(); 
    } 

    public function postUpdateDatasourceStatus(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $data_source_id      = (isset($my_data['data_source_id'])?$my_data['data_source_id']:'');            
            $data_source    = DB::table('data_sources')->where('data_source_id', $data_source_id)->get();
            if(count($data_source)>0) {
                $data_status = ($data_source[0]->status=='0'?'1':'0');
                $update_datasource  = DB::table('data_sources')
                                    ->where('data_source_id', $data_source_id)
                                    ->update(['status'=>$data_status]); 
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

    //create data source 
    public function getAddDatasource()
    {
        return view('backend.datasource.addDataSource');       
    }

    public function postCreateDataSource(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $user_type = implode(',', $my_data['user_type']);
            $data_source = $my_data['data_source'];
            $sourceData = array('data_source' => $data_source,'user_type' => $user_type,'status'=>'1'); 
            $data_source = DB::table('data_sources')->insertGetId($sourceData);
            return redirect('admin/data-source-list');
        }
        
    }

    public function getEditDataSource($id){
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $datasource = DB::table('data_sources')
                      ->where('data_source_id', $id)
                      ->select('*')
                      ->first();
        $data['datasource'] = $datasource;
        $data['user_types'] = array('0'=>'marketing_supervisor','1'=>'sales_area','2'=>'sales_regional','3'=>'area','4'=>'regional','5'=>'zonal','6'=>'bdo','7'=>'dhanuka_doctor');
        return view('backend.datasource.editDataSource',compact('data'));
    }

    public function postEditDataSourceProcess(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $user_type = implode(',', $my_data['user_type']);
            $data_source = $my_data['data_source'];
            $data_source_id = $my_data['data_source_id'];
            $sourceData = array('data_source' => $data_source,'user_type' => $user_type); 
            $data_source = DB::table('data_sources')->where('data_source_id',$data_source_id)->update($sourceData);
            return redirect('admin/data-source-list');
        }
        
    }
    // public function getOrganogramDetail(Request $request){
    //     $sess       =   \Session::get('adminid');
    //     $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
    //     if ($userData) {
            
    //         $user_id      = \Request::segment(3);            
    //         $user_type    = \Request::segment(4); 
            
    //         if ($user_type == 'Area') 
    //         {
    //             if($user_id!='') {
    //                 $regionalData = DB::table('area_managers_orn as rmo')
    //                                 ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
    //                                 ->join('regional_managers as r','r.regional_manager_id','=','rmo.regional_manager_id','left')
    //                                 ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmo.sales_regional_manager_id','left')
    //                                 ->where('rmo.area_managers_orn_id',$user_id)
    //                                 ->select('rmo.*','z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name')
    //                                 ->get();
    //                 if (count($regionalData)>0) {
    //                     $regionalData[0]->changepass = '0';
    //                     if ($regionalData[0]->area_manager_id == 0) {
    //                         //getting state
    //                         $regionalState = DB::table('area_manager_states_orn as rmso')
    //                                         ->join('states as s','s.state_id','=','rmso.state_id','left')
    //                                         ->where('rmso.area_manager_id',$regionalData[0]->area_managers_orn_id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->area_managers_orn_id)
    //                                         ->select('s.state')
    //                                         ->get();
    //                         if (count($regionalState)>0) {
    //                             $regionalData[0]->states = $regionalState;
    //                         } 
    //                         else 
    //                         {
    //                             $regionalData[0]->states = [];
    //                         }              
    //                         //getting products
    //                         $regionalProdut = DB::table('area_manager_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.area_manager_id',$regionalData[0]->area_managers_orn_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->area_managers_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editareamanager',array('regionalData'=>$regionalData));
    //                     }
    //                     else
    //                     {   
    //                         $checkpass = DB::table('area_managers')
    //                                      ->where('area_manager_id',$regionalData[0]->area_manager_id)
    //                                      ->select('dash')
    //                                      ->get();
    //                         if (count($checkpass)>0) {
    //                             if ($regionalData[0]->dash == $checkpass[0]->dash) 
    //                             {
    //                                 $regionalData[0]->changepass = '0';
    //                             }
    //                             else
    //                             {
    //                                 $regionalData[0]->changepass = '1';
    //                             }
    //                         }
    //                         else
    //                         {
    //                             $regionalData[0]->changepass = '0';
    //                         }
                            
    //                         //getting state
    //                          $regionalState = DB::table('area_manager_states_orn as rmso')
    //                                         ->join('states as s','s.state_id','=','rmso.state_id','left')
    //                                         ->where('rmso.area_manager_id',$regionalData[0]->area_manager_id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->area_managers_orn_id)
    //                                         ->select('s.state')
    //                                         ->get();
    //                         if (count($regionalState)>0) {
    //                             $regionalData[0]->states = $regionalState;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->states = [];
    //                         }               
    //                         //getting products
    //                         $regionalProdut = DB::table('area_manager_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.area_manager_id',$regionalData[0]->area_manager_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->area_managers_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editareamanager',array('regionalData'=>$regionalData));
    //                     }
                        
    //                 }

    //             }
    //             else {
    //                 return redirect('/admin/organogram-list');
    //             }
    //         }  
    //         elseif ($user_type == 'Regional')
    //         {
    //             if($user_id!='') {
    //                 $regionalData = DB::table('regional_managers_orn as rmo')
    //                                 ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
    //                                 ->where('rmo.regional_managers_orn_id',$user_id)
    //                                 ->select('rmo.*','z.zonal_manager_name')
    //                                 ->get();
    //                 if (count($regionalData)>0) {
    //                     $regionalData[0]->changepass = '0';
    //                     if ($regionalData[0]->regional_manager_id == 0) {
    //                         //getting state
    //                         $regionalState = DB::table('regional_manager_states_orn as rmso')
    //                                         ->join('states as s','s.state_id','=','rmso.state_id','left')
    //                                         ->where('rmso.regional_manager_id',$regionalData[0]->regional_managers_orn_id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->regional_managers_orn_id)
    //                                         ->select('s.state')
    //                                         ->get();
    //                         if (count($regionalState)>0) {
    //                             $regionalData[0]->states = $regionalState;
    //                         } 
    //                         else 
    //                         {
    //                             $regionalData[0]->states = [];
    //                         }              
    //                         //getting products
    //                         $regionalProdut = DB::table('regional_manager_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.regional_manager_id',$regionalData[0]->regional_managers_orn_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->regional_managers_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editregionalmanager',array('regionalData'=>$regionalData));
    //                     }
    //                     else
    //                     {   
    //                         $checkpass = DB::table('regional_managers')
    //                                      ->where('regional_manager_id',$regionalData[0]->regional_manager_id)
    //                                      ->select('dash')
    //                                      ->get();
    //                         if (count($checkpass)>0) {
    //                             if ($regionalData[0]->dash == $checkpass[0]->dash) 
    //                             {
    //                                 $regionalData[0]->changepass = '0';
    //                             }
    //                             else
    //                             {
    //                                 $regionalData[0]->changepass = '1';
    //                             }
    //                         }
    //                         else
    //                         {
    //                             $regionalData[0]->changepass = '0';
    //                         }
                            
    //                         //getting state
    //                         $regionalState = DB::table('regional_manager_states_orn as rmso')
    //                                         ->join('states as s','s.state_id','=','rmso.state_id','left')
    //                                         ->where('rmso.regional_manager_id',$regionalData[0]->regional_manager_id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->regional_managers_orn_id)
    //                                         ->select('s.state')
    //                                         ->get();
    //                         if (count($regionalState)>0) {
    //                             $regionalData[0]->states = $regionalState;
    //                         }  
    //                         else 
    //                         {
    //                             $regionalData[0]->states = [];
    //                         }             
    //                         //getting products
    //                         $regionalProdut = DB::table('regional_manager_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.regional_manager_id',$regionalData[0]->regional_manager_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->regional_managers_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editregionalmanager',array('regionalData'=>$regionalData));
    //                     }
                        
    //                 }

    //             }
    //             else {
    //                 return redirect('/admin/organogram-list');
    //             }
    //         } 
    //         elseif ($user_type == 'ASM')
    //         {
    //             if($user_id!='') {
    //                 $regionalData = DB::table('sales_area_managers_orn as rmo')
    //                                 ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
    //                                 ->join('regional_managers as r','r.regional_manager_id','=','rmo.regional_manager_id','left')
    //                                 ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmo.sales_regional_manager_id','left')
    //                                 ->where('rmo.sales_area_managers_orn_id',$user_id)
    //                                 ->select('rmo.*','z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name')
    //                                 ->get();
    //                 if (count($regionalData)>0) {
    //                     $regionalData[0]->changepass = '0';
    //                     if ($regionalData[0]->sales_area_manager_id == 0) {
    //                         //getting state
    //                         $regionalState = DB::table('sales_area_manager_states_orn as rmso')
    //                                         ->join('states as s','s.state_id','=','rmso.state_id','left')
    //                                         ->where('rmso.sales_area_manager_id',$regionalData[0]->sales_area_managers_orn_id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->sales_area_managers_orn_id)
    //                                         ->select('s.state')
    //                                         ->get();
    //                         if (count($regionalState)>0) {
    //                             $regionalData[0]->states = $regionalState;
    //                         } 
    //                         else 
    //                         {
    //                             $regionalData[0]->states = [];
    //                         }              
    //                         //getting products
    //                         $regionalProdut = DB::table('sales_area_manager_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.sales_area_manager_id',$regionalData[0]->sales_area_managers_orn_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->sales_area_managers_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editsalesareamanager',array('regionalData'=>$regionalData));
    //                     }
    //                     else
    //                     {   
    //                         $checkpass = DB::table('sales_area_managers')
    //                                      ->where('sales_area_manager_id',$regionalData[0]->sales_area_manager_id)
    //                                      ->select('dash')
    //                                      ->get();
    //                         if (count($checkpass)>0) {
    //                             if ($regionalData[0]->dash == $checkpass[0]->dash) 
    //                             {
    //                                 $regionalData[0]->changepass = '0';
    //                             }
    //                             else
    //                             {
    //                                 $regionalData[0]->changepass = '1';
    //                             }
    //                         }
    //                         else
    //                         {
    //                             $regionalData[0]->changepass = '0';
    //                         }
                            
    //                         //getting state
    //                          $regionalState = DB::table('sales_area_manager_states_orn as rmso')
    //                                         ->join('states as s','s.state_id','=','rmso.state_id','left')
    //                                         ->where('rmso.sales_area_manager_id',$regionalData[0]->sales_area_manager_id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->sales_area_managers_orn_id)
    //                                         ->select('s.state')
    //                                         ->get();
    //                         if (count($regionalState)>0) {
    //                             $regionalData[0]->states = $regionalState;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->states = [];
    //                         }               
    //                         //getting products
    //                         $regionalProdut = DB::table('sales_area_manager_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.sales_area_manager_id',$regionalData[0]->sales_area_manager_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->sales_area_managers_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editsalesareamanager',array('regionalData'=>$regionalData));
    //                     }
                        
    //                 }

    //             }
    //             else {
    //                 return redirect('/admin/organogram-list');
    //             }
    //         } 
    //         elseif ($user_type == 'RSM')
    //         {
    //             if($user_id!='') {
    //                 $regionalData = DB::table('sales_regional_managers_orn as rmo')
    //                                 ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
    //                                 ->where('rmo.sales_regional_managers_orn_id',$user_id)
    //                                 ->select('rmo.*','z.zonal_manager_name')
    //                                 ->get();
    //                 if (count($regionalData)>0) {
    //                     $regionalData[0]->changepass = '0';
    //                     if ($regionalData[0]->sales_regional_manager_id == 0) {
    //                         //getting state
    //                         $regionalState = DB::table('sales_regional_manager_states_orn as rmso')
    //                                         ->join('states as s','s.state_id','=','rmso.state_id','left')
    //                                         ->where('rmso.sales_regional_manager_id',$regionalData[0]->sales_regional_managers_orn_id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->sales_regional_managers_orn_id)
    //                                         ->select('s.state')
    //                                         ->get();
    //                         if (count($regionalState)>0) {
    //                             $regionalData[0]->states = $regionalState;
    //                         }    
    //                         else 
    //                         {
    //                             $regionalData[0]->states = [];
    //                         }           
    //                         //getting products
    //                         $regionalProdut = DB::table('sales_regional_manager_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.sales_regional_manager_id',$regionalData[0]->sales_regional_managers_orn_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->sales_regional_managers_orn_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->sales_regional_managers_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editsalesregionalmanager',array('regionalData'=>$regionalData));
    //                     }
    //                     else
    //                     {   
    //                         $checkpass = DB::table('sales_regional_managers')
    //                                      ->where('sales_regional_manager_id',$regionalData[0]->sales_regional_manager_id)
    //                                      ->select('dash')
    //                                      ->get();
    //                         if (count($checkpass)>0) {
    //                             if ($regionalData[0]->dash == $checkpass[0]->dash) 
    //                             {
    //                                 $regionalData[0]->changepass = '0';
    //                             }
    //                             else
    //                             {
    //                                 $regionalData[0]->changepass = '1';
    //                             }
    //                         }
    //                         else
    //                         {
    //                             $regionalData[0]->changepass = '0';
    //                         }
                            
    //                         //getting state
    //                         $regionalState = DB::table('sales_regional_manager_states_orn as rmso')
    //                                         ->join('states as s','s.state_id','=','rmso.state_id','left')
    //                                         ->where('rmso.sales_regional_manager_id',$regionalData[0]->sales_regional_manager_id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->sales_regional_managers_orn_id)
    //                                         ->select('s.state')
    //                                         ->get();
    //                         if (count($regionalState)>0) {
    //                             $regionalData[0]->states = $regionalState;
    //                         } 
    //                         else 
    //                         {
    //                             $regionalData[0]->states = [];
    //                         }              
    //                         //getting products
    //                         $regionalProdut = DB::table('sales_regional_manager_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.sales_regional_manager_id',$regionalData[0]->sales_regional_manager_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->sales_regional_managers_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editsalesregionalmanager',array('regionalData'=>$regionalData));
    //                     }
    //                 }
    //             }
    //             else {
    //                 return redirect('/admin/organogram-list');
    //             }
    //         }
    //         elseif ($user_type == 'BDO')
    //         {
    //             if($user_id!='') {
    //                 $regionalData = DB::table('users_orn as rmo')
    //                                 ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
    //                                 ->join('regional_managers as r','r.regional_manager_id','=','rmo.regional_manager_id','left')
    //                                 ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmo.sales_regional_manager_id','left')
    //                                 ->join('area_managers as ar','ar.area_manager_id','=','rmo.area_manager_id','left')
    //                                 ->join('sales_area_managers as sar','sar.sales_area_manager_id','=','rmo.sales_area_manager_id','left')
    //                                 ->join('states as s','s.state_id','=','rmo.state_id','left')
    //                                 ->join('districts as d','d.district_id','=','rmo.district_id','left')
    //                                 ->join('languages as l','l.id','=','rmo.language_id','left')
    //                                 ->join('users as u','u.counterpart_fl','=','rmo.id','left')
    //                                 ->where('rmo.users_orn_id',$user_id)
    //                                 ->select('rmo.*','z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name','ar.area_manager_name','sar.sales_area_manager_name','s.state','d.district','l.language','u.name as counterpart')
    //                                 ->get();
    //                 if (count($regionalData)>0) {
    //                     $regionalData[0]->changepass = '0';
    //                     if ($regionalData[0]->id == 0) {
    //                         //getting deputation
    //                         $regionalDeputation = DB::table('user_deputations_orn as rmso')
    //                                         ->join('zonal_managers as z','z.zonal_manager_id','=','rmso.zonal_manager_id','left')
    //                                         ->join('regional_managers as r','r.regional_manager_id','=','rmso.regional_manager_id','left')
    //                                         ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmso.sales_regional_manager_id','left')
    //                                         ->join('area_managers as ar','ar.area_manager_id','=','rmso.area_manager_id','left')
    //                                         ->join('sales_area_managers as sar','sar.sales_area_manager_id','=','rmso.sales_area_manager_id','left')
    //                                         ->where('rmso.user_id',$regionalData[0]->users_orn_id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->users_orn_id)
    //                                         ->select('z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name','ar.area_manager_name','sar.sales_area_manager_name')
    //                                         ->get();
    //                         if (count($regionalDeputation)>0) {
    //                             $regionalData[0]->Deputation = $regionalDeputation;
    //                         } 
    //                         else 
    //                         {
    //                             $regionalData[0]->Deputation = [];
    //                         }              
    //                         //getting products
    //                         $regionalProdut = DB::table('user_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.user_id',$regionalData[0]->users_orn_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->users_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editbdo',array('regionalData'=>$regionalData));
    //                     }
    //                     else
    //                     {   
    //                         $checkpass = DB::table('users')
    //                                      ->where('id',$regionalData[0]->id)
    //                                      ->select('dash')
    //                                      ->get();
    //                         if (count($checkpass)>0) {
    //                             if ($regionalData[0]->dash == $checkpass[0]->dash) 
    //                             {
    //                                 $regionalData[0]->changepass = '0';
    //                             }
    //                             else
    //                             {
    //                                 $regionalData[0]->changepass = '1';
    //                             }
    //                         }
    //                         else
    //                         {
    //                             $regionalData[0]->changepass = '0';
    //                         }
                            
    //                         //getting deputation
    //                         $regionalDeputation = DB::table('user_deputations_orn as rmso')
    //                                         ->join('zonal_managers as z','z.zonal_manager_id','=','rmso.zonal_manager_id','left')
    //                                         ->join('regional_managers as r','r.regional_manager_id','=','rmso.regional_manager_id','left')
    //                                         ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmso.sales_regional_manager_id','left')
    //                                         ->join('area_managers as ar','ar.area_manager_id','=','rmso.area_manager_id','left')
    //                                         ->join('sales_area_managers as sar','sar.sales_area_manager_id','=','rmso.sales_area_manager_id','left')
    //                                         ->where('rmso.user_id',$regionalData[0]->id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->users_orn_id)
    //                                         ->select('z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name','ar.area_manager_name','sar.sales_area_manager_name')
    //                                         ->get();
    //                         if (count($regionalDeputation)>0) {
    //                             $regionalData[0]->Deputation = $regionalDeputation;
    //                         } 
    //                         else 
    //                         {
    //                             $regionalData[0]->Deputation = [];
    //                         }              
    //                         //getting products
    //                         $regionalProdut = DB::table('user_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.user_id',$regionalData[0]->id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->users_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editbdo',array('regionalData'=>$regionalData));
    //                     }
                        
    //                 }

    //             }
    //             else {
    //                 return redirect('/admin/organogram-list');
    //             }
    //         } 
    //         elseif ($user_type == 'DD')
    //         {
    //             if($user_id!='') {
    //                 $regionalData = DB::table('dhanuka_doctor_orn as rmo')
    //                                 ->join('zonal_managers as z','z.zonal_manager_id','=','rmo.zonal_manager_id','left')
    //                                 ->join('regional_managers as r','r.regional_manager_id','=','rmo.regional_manager_id','left')
    //                                 ->join('sales_regional_managers as sr','sr.sales_regional_manager_id','=','rmo.sales_regional_manager_id','left')
    //                                 ->join('area_managers as ar','ar.area_manager_id','=','rmo.area_manager_id','left')
    //                                 ->join('sales_area_managers as sar','sar.sales_area_manager_id','=','rmo.sales_area_manager_id','left')
    //                                 ->join('users as u','u.id','=','rmo.bdo_id','left')
    //                                 ->join('states as s','s.state_id','=','rmo.state_id','left')
    //                                 ->join('districts as d','d.district_id','=','rmo.district_id','left')
    //                                 ->join('tehsils as t','t.tehsil_id','=','rmo.tehsil_id','left')
    //                                 ->join('languages as l','l.id','=','rmo.language_id','left')
    //                                 ->where('rmo.dhanuka_doctor_orn_id',$user_id)
    //                                 ->select('rmo.*','z.zonal_manager_name','r.regional_manager_name','sr.sales_regional_manager_name','ar.area_manager_name','sar.sales_area_manager_name','s.state','d.district','t.tehsil','l.language','u.name as bdoname')
    //                                 ->get();
    //                 if (count($regionalData)>0) {
    //                     $regionalData[0]->changepass = '0';
    //                     if ($regionalData[0]->dd_id == 0) {
    //                         //getting village
    //                         $regionalVillage = DB::table('assigned_village_orn as rmso')
    //                                         ->join('villages as z','z.village_id','=','rmso.village_id','left')
    //                                         ->where('rmso.assigned_to_dd_id',$regionalData[0]->dhanuka_doctor_orn_id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->dhanuka_doctor_orn_id)
    //                                         ->select('z.village')
    //                                         ->get();
    //                         if (count($regionalVillage)>0) {
    //                             $regionalData[0]->villages = $regionalVillage;
    //                         } 
    //                         else 
    //                         {
    //                             $regionalData[0]->villages = [];
    //                         }              
    //                         //getting products
    //                         $regionalProdut = DB::table('dd_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.dd_id',$regionalData[0]->dhanuka_doctor_orn_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->dhanuka_doctor_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editDD',array('regionalData'=>$regionalData));
    //                     }
    //                     else
    //                     {   
    //                         $checkpass = DB::table('dhanuka_doctor')
    //                                      ->where('dd_id',$regionalData[0]->dd_id)
    //                                      ->select('dash')
    //                                      ->get();
    //                         if (count($checkpass)>0) {
    //                             if ($regionalData[0]->dash == $checkpass[0]->dash) 
    //                             {
    //                                 $regionalData[0]->changepass = '0';
    //                             }
    //                             else
    //                             {
    //                                 $regionalData[0]->changepass = '1';
    //                             }
    //                         }
    //                         else
    //                         {
    //                             $regionalData[0]->changepass = '0';
    //                         }
                            
    //                         //getting village
    //                         $regionalVillage = DB::table('assigned_village_orn as rmso')
    //                                         ->join('villages as z','z.village_id','=','rmso.village_id','left')
    //                                         ->where('rmso.assigned_to_dd_id',$regionalData[0]->dd_id)
    //                                         ->where('rmso.orn_id',$regionalData[0]->dhanuka_doctor_orn_id)
    //                                         ->select('z.village')
    //                                         ->get();
    //                         if (count($regionalVillage)>0) {
    //                             $regionalData[0]->villages = $regionalVillage;
    //                         } 
    //                         else 
    //                         {
    //                             $regionalData[0]->villages = [];
    //                         }              
    //                         //getting products
    //                         $regionalProdut = DB::table('dd_product_categories_orn as rmpco')
    //                                         ->join('product_crop_categories as p','p.product_crop_category_id','=','rmpco.product_crop_category_id','left')
    //                                         ->where('rmpco.dd_id',$regionalData[0]->dd_id)
    //                                         ->where('rmpco.orn_id',$regionalData[0]->dhanuka_doctor_orn_id)
    //                                         ->select('p.category_name')
    //                                         ->get();
    //                         if (count($regionalProdut)>0) {
    //                             $regionalData[0]->products = $regionalProdut;
    //                         }
    //                         else 
    //                         {
    //                             $regionalData[0]->products = [];
    //                         }
    //                         return view::make('backend.adminOrganogram.editDD',array('regionalData'=>$regionalData));
    //                     }
                        
    //                 }

    //             }
    //             else {
    //                 return redirect('/admin/organogram-list');
    //             }
    //         }
    //     }
    // }
}
