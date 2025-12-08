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
use App\AppMenu;

use App\Sugarmill;
use App\SugarmillContact;
use App\SugarmillCategory;
use App\SugarmillCompany;
use App\SugarmillDesignation;
use App\SugarmillATP;
use App\SugarmillSalesReport;
use App\SugarmillTarget;

use App\TeaEstateTestimonial;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Storage;

class SugarmillController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function getListSugarmill() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $sugarmillcategory = SugarmillCategory::where('status',1)->get();
        $sugarmillcompany  = SugarmillCompany::where('status',1)->get();
        return View::make('backend.sugarmill.list-sugarmill',array('sugarmillcategory'=>$sugarmillcategory, 'sugarmillcompany'=>$sugarmillcompany));
    }

    public function postAllSugarmillList(Request $request) {
        // echo "string"; exit();
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

        $start_date     = $value['start_date'];
        $end_date       = $value['end_date'];
        $sugarmill_category_id = $value['sugarmill_category_id'];
        $sugarmill_company_id  = $value['sugarmill_company_id'];

        $adQuery = "(1=1)";
        if($start_date!='' && $end_date!='') {
            $adQuery .= " AND DATE(sm.created_at) >= DATE('".$start_date."') AND DATE(sm.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($sugarmill_category_id)) { $adQuery .= " AND sm.sugarmill_category_id = '".$sugarmill_category_id."'"; }
        if(!empty($sugarmill_company_id)) { $adQuery .= " AND sm.sugarmill_company_id = '".$sugarmill_company_id."'"; }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.name LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.address LIKE '%".$search."%')";
        } else {
            $adSearch = "";
        }
        $sorting    = '';
        $query      = " 
                        SELECT
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                sm.sugarmill_id,
                                (CASE WHEN sm.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN sm.sugarmill_type='1' THEN 'Sugarmill' ELSE 'SSM' END) as sugarmill_type,
                                sm.parent_sugarmill_id as parent_sugarmill,
                                sm.name,
                                sm.turn_over,
                                sm.land_area,
                                sc.company_type,
                                sct.company_category,
                                s.state,
                                d.district,
                                sm.address,
                                sm.latitude,
                                sm.longitude,
                                '0' as contacts,
                                sm.image,
                                sm.fl_ecn,
                                sm.addedby_ecn,
                                (CASE WHEN sm.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(sm.created_at, '%Y-%m-%d, %h:%i %p') as created_at
                            FROM sugarmill sm
                            LEFT JOIN states s ON sm.state_id=s.state_id
                            LEFT JOIN districts d ON sm.district_id=d.district_id
                            LEFT JOIN sugarmill_company sc ON sm.sugarmill_company_id=sc.sugarmill_company_id
                            LEFT JOIN sugarmill_category sct ON sm.sugarmill_category_id=sct.sugarmill_category_id
                            WHERE $adQuery
                            GROUP BY sm.sugarmill_id
                            ORDER BY
                                sm.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $sugarmill_list    = collect(DB::select($query));
        foreach ($sugarmill_list as $key => $value) {
            $parent_sugarmill  = $value->parent_sugarmill;
            $sugarmill_id      = $value->sugarmill_id;
            if($parent_sugarmill!=0) {
                $sugarmill = Sugarmill::where('sugarmill_id',$parent_sugarmill)->get();
                if(count($sugarmill)>0) {
                    $sugarmill_list[$key]->parent_sugarmill = $sugarmill[0]->name;
                } else {
                    $sugarmill_list[$key]->parent_sugarmill = "";
                }
            } else {
                $sugarmill_list[$key]->parent_sugarmill = "";
            }
            if($sugarmill_id) {
                $sugarmill_contact_count = SugarmillContact::where('sugarmill_id',$sugarmill_id)->count();
                if($sugarmill_contact_count) {
                    $sugarmill_list[$key]->contacts = $sugarmill_contact_count;
                } else {
                    $sugarmill_list[$key]->contacts = 0;
                }
            }
        }
        //print_r($sugarmill_list);
        return Datatables::of($sugarmill_list)->make();
    }

    public function getListContact($sugarmill_id='') {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        if(($sugarmill_id!='') && ($sugarmill_id!=0)) {
            $sugarmill = Sugarmill::where('sugarmill_id',$sugarmill_id)->get();
            if(count($sugarmill)>0) {
                return View::make('backend.sugarmill.list-sugarmillcontact',array('sugarmill'=> $sugarmill, 'sugarmill_id'=>$sugarmill_id,'show_all'=>0));
            } else {
                return redirect('/sugarmill/list-sugarmill');
            }
        } else {
            $sugarmill = Sugarmill::get();
            return View::make('backend.sugarmill.list-sugarmillcontact',array('sugarmill'=> $sugarmill, 'sugarmill_id'=>$sugarmill_id,'show_all'=>1));
        }
    }

    public function postAllContactList(Request $request) {
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

        $start_date     = $value['start_date'];
        $end_date       = $value['end_date'];

        $adQuery = "(1=1)";
        if($start_date!='' && $end_date!='') {
            $adQuery .= " AND DATE(sc.created_at) >= DATE('".$start_date."') AND DATE(sc.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['sugarmill_id'])) {
            $sugarmill_id = $value['sugarmill_id'];
            $adQuery .= " AND sc.sugarmill_id = '".$sugarmill_id."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.sugarmill_name LIKE '%".$search."%' OR A.contact_name LIKE '%".$search."%' OR A.wa_number LIKE '%".$search."%' OR A.mobile_number LIKE '%".$search."%' OR A.email LIKE '%".$search."%')";
        } else {
            $adSearch = "";
        }
        $sorting    = '';
        $query      = " 
                        SELECT
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                sc.sugarmill_contact_id,
                                s.sugarmill_id,
                                (CASE WHEN s.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN s.sugarmill_type='1' THEN 'sugarmill' ELSE 'SSM' END) as sugarmill_type,
                                s.parent_sugarmill_id as parent_sugarmill,
                                s.name as sugarmill_name,
                                smc.company_type,
                                sct.company_category,
                                sc.level,
                                sc.designation,
                                sc.name as contact_name,
                                sc.wa_number,
                                sc.mobile_number,
                                sc.email,
                                (CASE WHEN sc.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(sc.created_at, '%Y-%m-%d, %h:%i %p') as created_at
                            FROM sugarmill_contact sc
                            LEFT JOIN sugarmill s ON sc.sugarmill_id=s.sugarmill_id
                            LEFT JOIN sugarmill_company smc ON s.sugarmill_company_id=smc.sugarmill_company_id
                            LEFT JOIN sugarmill_category sct ON s.sugarmill_category_id=sct.sugarmill_category_id
                            WHERE $adQuery
                            GROUP BY sc.sugarmill_contact_id
                            ORDER BY
                                sc.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $sugarmill_contact_list = collect(DB::select($query));
        foreach ($sugarmill_contact_list as $key => $value) {
            $parent_sugarmill  = $value->parent_sugarmill;
            $sugarmill_id      = $value->sugarmill_id;
            if($parent_sugarmill!=0) {
                $sugarmill = Sugarmill::where('sugarmill_id',$parent_sugarmill)->get();
                if(count($sugarmill)>0) {
                    $sugarmill_contact_list[$key]->parent_sugarmill =$sugarmill[0]->name;
                } else {
                    $sugarmill_contact_list[$key]->parent_sugarmill = "";
                }
            } else {
                $sugarmill_contact_list[$key]->parent_sugarmill = "";
            }
        }
        //print_r($sugarmill_contact_list);
        return Datatables::of($sugarmill_contact_list)->make();
    }

    public function getListAtp() {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $sugarmill = Sugarmill::get();
        return View::make('backend.sugarmill.list-sugarmillatp',array('sugarmill'=> $sugarmill));
    }

    public function postAllAtpList(Request $request) {
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

        $start_date     = $value['start_date'];
        $end_date       = $value['end_date'];

        $adQuery = "(1=1)";
        if($start_date!='' && $end_date!='') {
            $adQuery .= " AND DATE(sa.created_at) >= DATE('".$start_date."') AND DATE(sa.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['sugarmill_id'])) {
            $sugarmill_id = $value['sugarmill_id'];
            $adQuery .= " AND sa.sugarmill_id = '".$sugarmill_id."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.ticket_number LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.address LIKE '%".$search."%' OR A.sugarmill_name LIKE '%".$search."%')";
        } else {
            $adSearch = "";
        }
        $sorting    = '';
        $query      = " 
                        SELECT
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                sa.sugarmill_atp_id,
                                DATE_FORMAT(sa.date_time, '%Y-%m-%d, %h:%i %p') as date_time,
                                sa.ticket_number,
                                (CASE WHEN sa.is_manager='0' THEN 'FL' ELSE 'Both' END) as is_manager,
                                sa.fl_ecn,
                                sa.manager_ecn,
                                sa.sugarmill_id,
                                sm.name as sugarmill_name,
                                (CASE WHEN sm.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN sm.sugarmill_type='1' THEN 'Sugarmill' ELSE 'SSM' END) as sugarmill_type,
                                sct.company_category,
                                s.state,
                                d.district,
                                sm.address,
                                (CASE WHEN sa.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(sa.created_at, '%Y-%m-%d, %h:%i %p') as created_at
                            FROM sugarmill_atp sa
                            LEFT JOIN sugarmill sm ON sa.sugarmill_id=sm.sugarmill_id
                            LEFT JOIN states s ON sm.state_id=s.state_id
                            LEFT JOIN districts d ON sm.district_id=d.district_id
                            LEFT JOIN sugarmill_category sct ON sm.sugarmill_category_id=sct.sugarmill_category_id
                            WHERE $adQuery
                            GROUP BY sa.sugarmill_atp_id
                            ORDER BY
                                sa.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $sugarmill_atp_list = collect(DB::select($query));
        //print_r($sugarmill_atp_list);
        return Datatables::of($sugarmill_atp_list)->make();
    }

    public function getListArivalDeparture() {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $sugarmill = Sugarmill::get();
        return View::make('backend.sugarmill.list-sugarmillarivaldeparture',array('sugarmill'=> $sugarmill));
    }

    public function postAllArivalDepartureList(Request $request) {
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

        $start_date     = $value['start_date'];
        $end_date       = $value['end_date'];

        $adQuery = "(1=1)";
        if($start_date!='' && $end_date!='') {
            $adQuery .= " AND DATE(sa.created_at) >= DATE('".$start_date."') AND DATE(sa.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['sugarmill_id'])) {
            $sugarmill_id = $value['sugarmill_id'];
            $adQuery .= " AND sa.sugarmill_id = '".$sugarmill_id."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.ticket_number LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.address LIKE '%".$search."%' OR A.sugarmill_name LIKE '%".$search."%')";
        } else {
            $adSearch = "";
        }
        $sorting    = '';
        $query      = " 
                        SELECT
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                ad.sugarmill_atp_arrival_departure_id,
                                DATE_FORMAT(sa.date_time, '%Y-%m-%d, %h:%i %p') as date_time,
                                sa.ticket_number,
                                (CASE WHEN sa.is_manager='0' THEN 'FL' ELSE 'Both' END) as is_manager,
                                sa.fl_ecn,
                                sa.manager_ecn,
                                sm.sugarmill_id,
                                sm.name as sugarmill_name,
                                (CASE WHEN sm.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN sm.sugarmill_type='1' THEN 'Sugarmill' ELSE 'SSM' END) as sugarmill_type,
                                sct.company_category,
                                s.state,
                                d.district,
                                sm.address,
                                ad.arrival_image,
                                DATE_FORMAT(ad.arrival_time, '%Y-%m-%d, %h:%i %p') as arrival_time,
                                ad.arrival_comment,
                                ad.arrival_latitude,
                                ad.arrival_longitude,
                                ad.departure_image,
                                DATE_FORMAT(ad.departure_time, '%Y-%m-%d, %h:%i %p') as departure_time,
                                ad.departure_comment,
                                ad.departure_latitude,
                                ad.departure_longitude,
                                ad.duration,
                                ad.distance,
                                (CASE WHEN ad.is_validate='1' THEN 'YES' ELSE 'NO' END) as is_validate,
                                (CASE WHEN ad.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(ad.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format
                            FROM sugarmill_atp_arrival_departure ad
                            LEFT JOIN sugarmill_atp sa ON ad.sugarmill_atp_id=sa.sugarmill_atp_id
                            LEFT JOIN sugarmill sm ON sa.sugarmill_id=sm.sugarmill_id
                            LEFT JOIN states s ON sm.state_id=s.state_id
                            LEFT JOIN districts d ON sm.district_id=d.district_id
                            LEFT JOIN sugarmill_category sct ON sm.sugarmill_category_id=sct.sugarmill_category_id
                            WHERE $adQuery
                            GROUP BY ad.sugarmill_atp_arrival_departure_id
                            ORDER BY sa.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $sugarmill_atp_list = collect(DB::select($query));
        //print_r($sugarmill_atp_list);
        return Datatables::of($sugarmill_atp_list)->make();
    }

    public function getListSalesReport() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $sugarmill = Sugarmill::get();
        // $crr_year   = date('Y');
        $crr_year   = '2021';
        $years      = date('Y', strtotime('+1 years'));
        $months     = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
        return View::make('backend.sugarmill.list-salesreport',array('sugarmill'=>$sugarmill, 'months'=>$months, 'years'=>$years, 'crr_year'=>$crr_year));
    }

    public function postAllSalesReport(Request $request) {
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

        $start_date     = (!empty($value['start_date'])) ? $value['start_date'] : '';
        $end_date       = (!empty($value['end_date'])) ? $value['end_date'] : '';

        $adQuery = "(1=1)";
        if($start_date!='' && $end_date!='') {
            $adQuery .= " AND DATE(sr.created_at) >= DATE('".$start_date."') AND DATE(sr.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['sugarmill_id'])) {
            $sugarmill_id = $value['sugarmill_id'];
            $adQuery .= " AND sr.sugarmill_id = '".$sugarmill_id."'";
        }
        if(!empty($value['year'])) {
            $year = $value['year'];
            $adQuery .= " AND sr.year = '".$year."'";
        }
        if(!empty($value['month'])) {
            $month = $value['month'];
            $adQuery .= " AND sr.month = '".$month."'";
        }
        // echo $adQuery; exit();

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.ticket_number LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.address LIKE '%".$search."%' OR A.sugarmill_name LIKE '%".$search."%')";
        } else {
            $adSearch = "";
        }
        $sorting    = '';
        $query      = " 
                        SELECT
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                sr.sugarmill_sales_report_id,
                                sm.name as sugarmill_name,
                                (CASE WHEN sm.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN sm.sugarmill_type='1' THEN 'Sugarmill' ELSE 'SSM' END) as sugarmill_type,
                                sct.company_category,
                                s.state,
                                d.district,
                                sm.address,
                                sr.month,
                                sr.year,
                                sr.sales_turnover,
                                (CASE WHEN sr.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(sr.created_at, '%Y-%m-%d, %h:%i %p') as created_at
                            FROM sugarmill_sales_report sr
                            LEFT JOIN sugarmill sm ON sr.sugarmill_id=sm.sugarmill_id
                            LEFT JOIN states s ON sm.state_id=s.state_id
                            LEFT JOIN districts d ON sm.district_id=d.district_id
                            LEFT JOIN sugarmill_category sct ON sm.sugarmill_category_id=sct.sugarmill_category_id
                            WHERE $adQuery
                            GROUP BY sr.sugarmill_sales_report_id
                            ORDER BY
                                sr.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $sales_report_list = collect(DB::select($query));
        //print_r($sales_report_list);
        return Datatables::of($sales_report_list)->make();
    }

    public function postImportSalesReport(Request $request) {   
        $my_data  = Input::all();      
        $rules  = array(
                    'salesdata' => 'required'
                );
        $validator  = Validator::make($my_data, $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        if($my_data['salesdata']){
            $path1 = Request::file('salesdata')->getRealPath();
            //echo "<pre>"; print_r($path1); die;
            // $data = Excel::load($path)->get();
            $data = Excel::load($path1)->get();
            echo "<pre>";print_r($data);die;
            $ExcelArr = array();
            if($data->count() > 0) {
                 foreach ($data as $key => $value) {
                    //echo "<pre>"; print_r($value->sugarmill_id); die;
                    $ExcelArr['sugarmill_id']  = $value->sugarmill_id;
                    $ExcelArr['month']          = $value->month;
                    $ExcelArr['year']           = $value->year;
                    $ExcelArr['sales_turnover'] = $value->sales_turnover;
                    $ExcelArr['status']     = 1;
                    $ExcelArr['created_at'] = date('Y-m-d H:i:s');
                    $ExcelArr['updated_at'] = date('Y-m-d H:i:s');
                    //echo "<pre>"; print_r($ExcelArr); die;
                    $village = DB::table('sugarmill_sales_report')
                                ->where('sugarmill_sales_report.sugarmill_id',$value->sugarmill_id)
                                ->where('sugarmill_sales_report.month',$value->month)
                                ->where('sugarmill_sales_report.year',$value->year)
                                ->where('sugarmill_sales_report.sales_turnover',$value->sales_turnover)
                                ->where('sugarmill_sales_report.status',1)
                                ->select('sugarmill_sales_report.*')
                                ->count();
                    if ($village == 0) { 
                        SugarmillSalesReport::insert($ExcelArr);
                    }
                }
            }
        }
        return back()->with('success','Excel Data imported successfully');
    }

    public function getListTarget() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $crr_year   = '2021';
        $years      = date('Y', strtotime('+1 years'));
        $months     = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
        $user_types = array('marketing_supervisor'=>'Marketing Supervisor','zonal'=>'Zonal','regional'=>'Regional','sales_regional'=>'Sales Regional','area'=>'Area','sales_area'=>'Sales Area','bdo'=>'FL');
        return View::make('backend.sugarmill.list-target',array('user_types'=>$user_types, 'months'=>$months, 'years'=>$years, 'crr_year'=>$crr_year));
    }

    public function postAllTarget(Request $request) {
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

        $start_date     = (!empty($value['start_date'])) ? $value['start_date'] : '';
        $end_date       = (!empty($value['end_date'])) ? $value['end_date'] : '';

        $adQuery = "(1=1)";
        if($start_date!='' && $end_date!='') {
            $adQuery .= " AND DATE(t.created_at) >= DATE('".$start_date."') AND DATE(t.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['user_type'])) {
            $user_type = $value['user_type'];
            $adQuery .= " AND t.user_type = '".$user_type."'";
        }
        if(!empty($value['user_name'])) {
            $user_ecn = $value['user_name'];
            $adQuery .= " AND t.user_ecn = '".$user_ecn."'";
        }

        if(!empty($value['year'])) {
            $year = $value['year'];
            $adQuery .= " AND t.target_year = '".$year."'";
        }
        if(!empty($value['month'])) {
            $month = $value['month'];
            $adQuery .= " AND t.target_month = '".$month."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.category_a LIKE '%".$search."%' OR A.category_b LIKE '%".$search."%' OR A.category_c LIKE '%".$search."%')";
        } else {
            $adSearch = "";
        }
        $sorting    = '';
        $query      = " 
                        SELECT
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                t.target_id,
                                t.user_name,
                                t.user_ecn,
                                t.user_type,
                                (CASE WHEN t.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                t.target_month,
                                t.target_year,
                                t.category_a_target,
                                t.category_b_target,
                                t.category_c_target,
                                (CASE WHEN t.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(t.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,
                                t.category_a,
                                t.category_b,
                                t.category_c
                            FROM sugarmill_target t
                            WHERE $adQuery
                            GROUP BY t.target_id
                            ORDER BY
                                t.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $target_list = collect(DB::select($query));
        //print_r($target_list);
        return Datatables::of($target_list)->make();
    }

    /*public function postCreateTarget(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data    = Input::all();
            $rules      = array(
                            'month'  => 'required',
                            'year'   => 'required',
                            'user_name'     => 'required',
                            // 'user_ecn'      => 'required',
                            'user_type'     => 'required',
                            'category_a_target' => 'required',
                            'category_b_target' => 'required',
                            'category_c_target' => 'required'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $target_month   = $my_data['month'];
                $target_year    = $my_data['year'];
                $user_name      = $my_data['user_name_title'];
                $user_ecn       = $my_data['user_name'];
                $user_type      = $my_data['user_type'];
                $category_a_target  = $my_data['category_a_target'];
                $category_b_target  = $my_data['category_b_target'];
                $category_c_target  = $my_data['category_c_target'];

                $check_target = SugarmillTarget::where('target_month',$target_month)->where('target_year',$target_year)->where('user_ecn',$user_ecn)->get();
                if(count($check_target)==0) {
                    $targetdata = array(
                                    'target_month'  => $target_month,
                                    'target_year'   => $target_year,
                                    'user_name'     => $user_name,
                                    'user_ecn'      => $user_ecn,
                                    'user_type'     => $user_type,
                                    'category_a_target' => $category_a_target,
                                    'category_a'    => 'A',
                                    'category_b_target' => $category_b_target,
                                    'category_b'    => 'B',
                                    'category_c_target' => $category_c_target,
                                    'category_c'    => 'C',
                                    'status'        => '1',
                                    'created_at'    => date('Y-m-d H:i:s'),
                                    'updated_at'    => date('Y-m-d H:i:s')
                                );
                    $insert_target  = SugarmillTarget::firstOrCreate($targetdata);
                    $result         = $insert_target->target_id;
                    if($result) {
                        Session::flash('message', "Target Successfully Created.");
                        return redirect('sugarmill/list-target');             
                    } else {
                        return redirect('sugarmill/list-target');
                    }
                } else {
                    Session::flash('message',"Target Already Added for this User.");
                }
            }
        }
    }*/

    public function postCreateTarget(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data    = Input::all();
            $rules      = array(
                            'months'    => 'required',
                            'years'     => 'required',
                            'user_ecns' => 'required',
                            'user_types'=> 'required',
                            'category_a_target' => 'required',
                            'category_b_target' => 'required',
                            'category_c_target' => 'required',
                            'office_type' => 'required'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $user_type      = $my_data['user_types'];
                $user_ecns      = $my_data['user_ecns'];
                $target_months  = $my_data['months'];
                $target_year    = $my_data['years'];
                $category_a_target  = $my_data['category_a_target'];
                $category_b_target  = $my_data['category_b_target'];
                $category_c_target  = $my_data['category_c_target'];
                $office_type    = $my_data['office_type'];

                foreach ($user_ecns as $user_ecn) {
                    foreach ($target_months as $target_month) {
                        $check_target = SugarmillTarget::where('target_month',$target_month)->where('target_year',$target_year)->where('user_ecn',$user_ecn)->where('user_type',$user_type)->get();
                        if(count($check_target)==0) {
                            $user_detail = $this->findUserDetails($user_type, $user_ecn);
                            $user_name  = $user_detail[0]->manager_name;
                            $targetdata = array(
                                    'target_month'  => $target_month,
                                    'target_year'   => $target_year,
                                    'user_name'     => $user_name,
                                    'user_ecn'      => $user_ecn,
                                    'user_type'     => $user_type,
                                    'office_type'   => $office_type,
                                    'category_a_target' => $category_a_target,
                                    'category_a'    => 'A',
                                    'category_b_target' => $category_b_target,
                                    'category_b'    => 'B',
                                    'category_c_target' => $category_c_target,
                                    'category_c'    => 'C',
                                    'status'        => '1',
                                    'created_at'    => date('Y-m-d H:i:s'),
                                    'updated_at'    => date('Y-m-d H:i:s')
                                );
                            $insert_target = SugarmillTarget::firstOrCreate($targetdata);
                            $result        = $insert_target->target_id;
                        } else {
                            $target_id     = $check_target[0]->target_id;
                            $update_target = DB::table('sugarmill_target')
                                    ->where([
                                        'target_id' => $target_id,
                                        'user_ecn'  => $user_ecn,
                                        'user_type' => $user_type
                                    ])
                                    ->update([
                                        'target_month'  => $target_month,
                                        'target_year'   => $target_year,
                                        'office_type'   => $office_type,
                                        'category_a_target' => $category_a_target,
                                        'category_b_target' => $category_b_target,
                                        'category_c_target' => $category_c_target,
                                        'status'        => '1',
                                        'updated_at'    => date('Y-m-d H:i:s')
                                    ]);
                        }
                    }
                }
                Session::flash('message', "Target Successfully Created.");
                return redirect('sugarmill/list-target');
            }
        }
    }

    public function getEditTarget($target_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($target_id!='') {
                $targetdetail   = SugarmillTarget::where('target_id', $target_id)->first();
                if($targetdetail) {
                    $crr_year   = date('Y');
                    $years      = date('Y', strtotime('+1 years'));
                    $months     = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
                    $user_types = array('marketing_supervisor'=>'Marketing Supervisor','zonal'=>'Zonal','regional'=>'Regional','sales_regional'=>'Sales Regional','area'=>'Area','sales_area'=>'Sales Area','bdo'=>'FL');
                    return View::make('backend.sugarmill.edit-target', array('user_types'=>$user_types,'targetdetail' => $targetdetail));
                } else {
                    return redirect('sugarmill/list-target');
                }
            } else {
                return redirect('sugarmill/list-target');
            }
        } else {
            return redirect('/');
        }
    }

    public function postUpdateTarget(Request $request) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();
            $rules      = array(
                            'target_id'     => 'required',
                            'month'         => 'required',
                            'year'          => 'required',
                            'year'          => 'required',
                            'category_a_target' => 'required',
                            'category_b_target' => 'required',
                            'category_c_target' => 'required',
                            'office_type'   => 'required'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                // return "Error";
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $target_id      = $my_data['target_id'];
                $target_month   = $my_data['month'];
                $target_year    = $my_data['year'];
                $category_a_target  = $my_data['category_a_target'];
                $category_b_target  = $my_data['category_b_target'];
                $category_c_target  = $my_data['category_c_target'];
                $office_type    = $my_data['office_type'];

                $update_target = DB::table('sugarmill_target')
                                    ->where('target_id', $target_id)
                                    ->update([
                                        'target_month'  => $target_month,
                                        'target_year'   => $target_year,
                                        'category_a_target' => $category_a_target,
                                        'category_b_target' => $category_b_target,
                                        'category_c_target' => $category_c_target,
                                        'office_type'   => $office_type,
                                        'status'        => '1',
                                        'updated_at'    => date('Y-m-d H:i:s')
                                    ]);
                // return $update_target;
                if($update_target) {
                    Session::flash('message', "Target Successfully Updated.");
                    return redirect('sugarmill/list-target');
                } else {
                    Session::flash('message', "An error occurret. Please try again later.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            }
        }
    }

    public function postUserDetails(Request $request) {
        $my_data    = Input::all();
        $user_type  = $my_data['user_type'];
        switch ($user_type) {
            case "marketing_supervisor" : $table = "marketing_supervisor";
                $select_field = "marketing_supervisor_name AS name,ECN";
                break;
            case "zonal" : $table = "zonal_managers";
                $select_field = "zonal_manager_name AS name,ECN";
                break;
            case "regional" : $table = "regional_managers";
                $select_field = "regional_manager_name AS name,ECN";
                break;
            case "area" : $table = "area_managers";
                $select_field = "area_manager_name AS name,ECN";
                break;
            case "sales_regional" : $table = "sales_regional_managers";
                $select_field = "sales_regional_manager_name AS name,ECN";
                break;
            case "sales_area" : $table = "sales_area_managers";
                $select_field = "sales_area_manager_name AS name,ECN";
                break;
            case "bdo" : $table = "users";
                $select_field = "name AS name,ECN";
                break;
            default: $table = "users";
                $select_field = "name AS name,ECN";
                break;
        }
        $query = " SELECT ".$select_field." FROM ".$table." WHERE status='1'";
        $users = DB::select(DB::raw($query));
        if(count($users)>0) {
            echo json_encode($users, true);
        } else {
            echo 0;
        }
        // return $users;
    }

    public function getListVisitsummary() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $crr_year   = '2021';
        $years      = date('Y', strtotime('+1 years'));
        $months     = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
        return View::make('backend.sugarmill.list-visitsummary',array('months'=>$months, 'years'=>$years, 'crr_year'=>$crr_year));
    }

    public function postAllVisitsummary(Request $request) {
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

        $start_date     = (!empty($value['start_date'])) ? $value['start_date'] : '';
        $end_date       = (!empty($value['end_date'])) ? $value['end_date'] : '';

        $adQuery = "(1=1)";
        if(!empty($value['year'])) {
            $year = $value['year'];
            $adQuery .= " AND t.target_year = '".$year."'";
        }
        if(!empty($value['month'])) {
            $month = $value['month'];
            $adQuery .= " AND t.target_month = '".$month."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.user_name LIKE '%".$search."%' OR A.user_ecn LIKE '%".$search."%' OR A.user_type LIKE '%".$search."%')";
        } else {
            $adSearch = "";
        }
        $sorting    = '';
        $query      = " 
                        SELECT
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                t.user_name,
                                t.user_ecn,
                                t.user_type,
                                t.target_month,
                                t.target_year,
                                t.category_a_target,
                                0 as category_a_plan,
                                0 as category_a_visit,
                                t.category_b_target,
                                0 as category_b_plan,
                                0 as category_b_visit,
                                t.category_c_target,
                                0 as category_c_plan,
                                0 as category_c_visit
                            FROM sugarmill_target t
                            WHERE $adQuery
                            GROUP BY t.target_id
                            ORDER BY
                                t.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $visitsummary_list = collect(DB::select($query));
        foreach ($visitsummary_list as $key => $value) {
            $target_month   = $value->target_month;
            $target_year    = $value->target_year;
            $user_type      = $value->user_type;
            $user_ecn       = $value->user_ecn;
            $cat_a_id       = 1;
            $cat_b_id       = 2;
            $cat_c_id       = 3;

            $plan_date_start= $target_year.'-'.$target_month.'-01T00:00:00';
            $plan_date_end  = $target_year.'-'.$target_month.'-31T00:00:00';

            $cat_a_plan     = SugarmillATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('sugarmill_category',$cat_a_id)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $cat_a_visit    = SugarmillATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('sugarmill_category',$cat_a_id)->where('is_arrival_departure',1)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $visitsummary_list[$key]->category_a_plan = $cat_a_plan;
            $visitsummary_list[$key]->category_a_visit = $cat_a_visit;

            $cat_b_plan     = SugarmillATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('sugarmill_category',$cat_b_id)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $cat_b_visit    = SugarmillATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('sugarmill_category',$cat_b_id)->where('is_arrival_departure',1)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $visitsummary_list[$key]->category_b_plan = $cat_b_plan;
            $visitsummary_list[$key]->category_b_visit = $cat_b_visit;

            $cat_c_plan     = SugarmillATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('sugarmill_category',$cat_c_id)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $cat_c_visit    = SugarmillATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('sugarmill_category',$cat_c_id)->where('is_arrival_departure',1)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $visitsummary_list[$key]->category_c_plan   = $cat_c_plan;
            $visitsummary_list[$key]->category_c_visit  = $cat_c_visit;
        }
        //print_r($visitsummary_list);
        return Datatables::of($visitsummary_list)->make();
    }

    private function findUserDetails($user_type, $user_ecn) {
        switch ($user_type) {
            case "marketing_supervisor" : $table = "marketing_supervisor";
                $select_field = "marketing_supervisor_name AS manager_name";
                break;
            case "zonal" : $table = "zonal_managers";
                $select_field = "zonal_manager_name AS manager_name";
                break;
            case "regional" : $table = "regional_managers";
                $select_field = "regional_manager_name AS manager_name";
                break;
            case "area" : $table = "area_managers";
                $select_field = "area_manager_name AS manager_name";
                break;
            case "sales_regional" : $table = "sales_regional_managers";
                $select_field = "sales_regional_manager_name AS manager_name";
                break;
            case "sales_area" : $table = "sales_area_managers";
                $select_field = "sales_area_manager_name AS manager_name";
                break;
            case "bdo" : $table = "users";
                $select_field = "name AS manager_name";
                break;
            case "dhanuka_doctor" : $table = "dhanuka_doctor";
                $select_field = "name AS manager_name";
                break;
        case "general" : $table = "general";
                $select_field = "general_user_name AS manager_name";
                break; 
        }
        $query = " SELECT " . $select_field . " FROM " . $table . " WHERE status='1' AND ECN ='" . $user_ecn . "' LIMIT 0,1 ";
        $user = DB::select(DB::raw($query));
        return $user;
    }
}
