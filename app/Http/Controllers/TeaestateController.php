<?php

namespace App\Http\Controllers;
use Request as ImportRequest;
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

use App\TeaEstate;
use App\TeaEstateContact;
use App\EstateCategory;
use App\EstateCompany;
use App\EstateDesignation;
use App\TeaEstateSalesReport;
use App\TeaEstateATP;
use App\TeaEstateTarget;
use App\TeaEstateTestimonial;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Storage;

class TeaestateController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function getListTeaestate() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $estatecategory = EstateCategory::where('status',1)->get();
        $estatecompany  = EstateCompany::where('status',1)->get();
        return View::make('backend.teaestate.list-teaestate',array('estatecategory'=>$estatecategory, 'estatecompany'=>$estatecompany));
    }

    public function postAllTeaestateList(Request $request) {
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
        $estate_category_id = $value['estate_category_id'];
        $estate_company_id  = $value['estate_company_id'];

        $adQuery = "(1=1)";
        if($start_date!='' && $end_date!='') {
            $adQuery .= " AND DATE(t.created_at) >= DATE('".$start_date."') AND DATE(t.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($estate_category_id)) { $adQuery .= " AND t.estate_category_id = '".$estate_category_id."'"; }
        if(!empty($estate_company_id)) { $adQuery .= " AND t.estate_company_id = '".$estate_company_id."'"; }

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
                                t.tea_estate_id,
                                (CASE WHEN t.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN t.tea_estate_type='1' THEN 'Tea Estate' ELSE 'STG' END) as tea_estate_type,
                                t.parent_tea_estate_id as parent_tea_estate,
                                t.name,
                                t.turn_over,
                                t.land_area,
                                ec.company_type,
                                ect.company_category,
                                s.state,
                                d.district,
                                t.address,
                                t.latitude,
                                t.longitude,
                                '0' as contacts,
                                t.image,
                                t.fl_ecn,
                                t.addedby_ecn,
                                (CASE WHEN t.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(t.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format
                            FROM tea_estate t
                            LEFT JOIN states s ON t.state_id=s.state_id
                            LEFT JOIN districts d ON t.district_id=d.district_id
                            LEFT JOIN estate_company ec ON t.estate_company_id=ec.estate_company_id
                            LEFT JOIN estate_category ect ON t.estate_category_id=ect.estate_category_id
                            WHERE $adQuery
                            GROUP BY t.tea_estate_id
                            ORDER BY
                                t.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $tea_estate_list    = collect(DB::select($query));
        foreach ($tea_estate_list as $key => $value) {
            $parent_tea_estate  = $value->parent_tea_estate;
            $tea_estate_id      = $value->tea_estate_id;
            if($parent_tea_estate!=0) {
                $tea_estate = TeaEstate::where('tea_estate_id',$parent_tea_estate)->get();
                if(count($tea_estate)>0) {
                    $tea_estate_list[$key]->parent_tea_estate = $tea_estate[0]->name;
                } else {
                    $tea_estate_list[$key]->parent_tea_estate = "";
                }
            } else {
                $tea_estate_list[$key]->parent_tea_estate = "";
            }
            if($tea_estate_id) {
                $tea_estate_contact_count = TeaEstateContact::where('tea_estate_id',$tea_estate_id)->count();
                if($tea_estate_contact_count) {
                    $tea_estate_list[$key]->contacts = $tea_estate_contact_count;
                } else {
                    $tea_estate_list[$key]->contacts = 0;
                }
            }
        }
        //print_r($tea_estate_list);
        return Datatables::of($tea_estate_list)->make();
    }

    public function getListContact($tea_estate_id='') {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        if(($tea_estate_id!='') && ($tea_estate_id!=0)) {
            $tea_estate = TeaEstate::where('tea_estate_id',$tea_estate_id)->get();
            if(count($tea_estate)>0) {
                return View::make('backend.teaestate.list-teaestatecontact',array('tea_estate'=> $tea_estate,'tea_estate_id'=>$tea_estate_id,'show_all'=>0));
            } else {
                return redirect('/teaestate/list-teaestate');
            }
        } else {
            // return redirect('/teaestate/list-teaestate');
            $tea_estate = TeaEstate::get();
            return View::make('backend.teaestate.list-teaestatecontact',array('tea_estate'=> $tea_estate,'tea_estate_id'=>$tea_estate_id,'show_all'=>1));
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
            $adQuery .= " AND DATE(tc.created_at) >= DATE('".$start_date."') AND DATE(tc.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['tea_estate_id'])) {
            $tea_estate_id = $value['tea_estate_id'];
            $adQuery .= " AND tc.tea_estate_id = '".$tea_estate_id."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.tea_estate_name LIKE '%".$search."%' OR A.contact_name LIKE '%".$search."%' OR A.wa_number LIKE '%".$search."%' OR A.mobile_number LIKE '%".$search."%' OR A.email LIKE '%".$search."%')";
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
                                tc.tea_estate_contact_id,
                                t.tea_estate_id,
                                (CASE WHEN t.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN t.tea_estate_type='1' THEN 'Tea Estate' ELSE 'STG' END) as tea_estate_type,
                                t.parent_tea_estate_id as parent_tea_estate,
                                t.name as tea_estate_name,
                                ec.company_type,
                                ect.company_category,
                                tc.level,
                                tc.designation,
                                tc.name as contact_name,
                                tc.wa_number,
                                tc.mobile_number,
                                tc.email,
                                (CASE WHEN tc.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(tc.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format
                            FROM tea_estate_contact tc
                            LEFT JOIN tea_estate t ON tc.tea_estate_id=t.tea_estate_id
                            LEFT JOIN estate_company ec ON t.estate_company_id=ec.estate_company_id
                            LEFT JOIN estate_category ect ON t.estate_category_id=ect.estate_category_id
                            WHERE $adQuery
                            GROUP BY tc.tea_estate_contact_id
                            ORDER BY
                                tc.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $tea_estate_contact_list = collect(DB::select($query));
        foreach ($tea_estate_contact_list as $key => $value) {
            $parent_tea_estate  = $value->parent_tea_estate;
            $tea_estate_id      = $value->tea_estate_id;
            if($parent_tea_estate!=0) {
                $tea_estate = TeaEstate::where('tea_estate_id',$parent_tea_estate)->get();
                if(count($tea_estate)>0) {
                    $tea_estate_contact_list[$key]->parent_tea_estate = $tea_estate[0]->name;
                } else {
                    $tea_estate_contact_list[$key]->parent_tea_estate = "";
                }
            } else {
                $tea_estate_contact_list[$key]->parent_tea_estate = "";
            }
        }
        //print_r($tea_estate_contact_list);
        return Datatables::of($tea_estate_contact_list)->make();
    }

    public function getListAtp() {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $tea_estate = TeaEstate::get();
        return View::make('backend.teaestate.list-teaestateatp',array('tea_estate'=> $tea_estate));
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
            $adQuery .= " AND DATE(ea.created_at) >= DATE('".$start_date."') AND DATE(ea.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['tea_estate_id'])) {
            $tea_estate_id = $value['tea_estate_id'];
            $adQuery .= " AND ea.tea_estate_id = '".$tea_estate_id."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.ticket_number LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.address LIKE '%".$search."%' OR A.tea_estate_name LIKE '%".$search."%')";
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
                                ea.estate_atp_id,
                                DATE_FORMAT(ea.date_time, '%Y-%m-%d, %h:%i %p') as date_time,
                                ea.ticket_number,
                                (CASE WHEN ea.is_manager='0' THEN 'FL' ELSE 'Both' END) as is_manager,
                                ea.fl_ecn,
                                ea.manager_ecn,
                                t.tea_estate_id,
                                t.name as tea_estate_name,
                                (CASE WHEN t.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN t.tea_estate_type='1' THEN 'Tea Estate' ELSE 'STG' END) as tea_estate_type,
                                ect.company_category,
                                s.state,
                                d.district,
                                t.address,
                                (CASE WHEN ea.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(ea.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format
                            FROM estate_atp ea
                            LEFT JOIN tea_estate t ON ea.tea_estate_id=t.tea_estate_id
                            LEFT JOIN states s ON t.state_id=s.state_id
                            LEFT JOIN districts d ON t.district_id=d.district_id
                            LEFT JOIN estate_category ect ON t.estate_category_id=ect.estate_category_id
                            WHERE $adQuery
                            GROUP BY ea.estate_atp_id
                            ORDER BY
                                ea.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $estate_atp_list = collect(DB::select($query));
        //print_r($estate_atp_list);
        return Datatables::of($estate_atp_list)->make();
    }

    public function getListArivalDeparture() {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $tea_estate = TeaEstate::get();
        return View::make('backend.teaestate.list-teaestatearivaldeparture',array('tea_estate'=> $tea_estate));
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
            $adQuery .= " AND DATE(ea.created_at) >= DATE('".$start_date."') AND DATE(ea.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['tea_estate_id'])) {
            $tea_estate_id = $value['tea_estate_id'];
            $adQuery .= " AND ea.tea_estate_id = '".$tea_estate_id."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.ticket_number LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.address LIKE '%".$search."%' OR A.tea_estate_name LIKE '%".$search."%')";
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
                                ad.estate_atp_arrival_departure_id,
                                DATE_FORMAT(ea.date_time, '%Y-%m-%d, %h:%i %p') as date_time,
                                ea.ticket_number,
                                (CASE WHEN ea.is_manager='0' THEN 'FL' ELSE 'Both' END) as is_manager,
                                ea.fl_ecn,
                                -- ea.manager_ecn,
                                -- ad.user_ecn as fl_ecn,
                                ad.user_ecn as manager_ecn,
                                t.tea_estate_id,
                                t.name as tea_estate_name,
                                (CASE WHEN t.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN t.tea_estate_type='1' THEN 'Tea Estate' ELSE 'STG' END) as tea_estate_type,
                                ect.company_category,
                                s.state,
                                d.district,
                                t.address,
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
                            FROM estate_atp_arrival_departure ad
                            LEFT JOIN estate_atp ea ON ad.estate_atp_id=ea.estate_atp_id
                            LEFT JOIN tea_estate t ON ea.tea_estate_id=t.tea_estate_id
                            LEFT JOIN states s ON t.state_id=s.state_id
                            LEFT JOIN districts d ON t.district_id=d.district_id
                            LEFT JOIN estate_category ect ON t.estate_category_id=ect.estate_category_id
                            WHERE $adQuery
                            GROUP BY ad.estate_atp_arrival_departure_id
                            ORDER BY
                                ea.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $estate_atp_list = collect(DB::select($query));
        //print_r($estate_atp_list);
        return Datatables::of($estate_atp_list)->make();
    }

    public function getListSalesReport() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $teaestate = TeaEstate::get();
        $crr_year       = date('Y');
        $years          = date('Y', strtotime('+1 years'));
        $months         = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
        return View::make('backend.teaestate.list-salesreport',array('teaestate'=>$teaestate, 'months'=>$months, 'years'=>$years, 'crr_year'=>$crr_year));
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
        if(!empty($value['tea_estate_id'])) {
            $tea_estate_id = $value['tea_estate_id'];
            $adQuery .= " AND sr.tea_estate_id = '".$tea_estate_id."'";
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
            $adSearch = " WHERE (A.ticket_number LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.address LIKE '%".$search."%' OR A.tea_estate_name LIKE '%".$search."%')";
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
                                sr.estate_sales_report_id,
                                t.name as tea_estate_name,
                                (CASE WHEN t.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN t.tea_estate_type='1' THEN 'Tea Estate' ELSE 'STG' END) as tea_estate_type,
                                ect.company_category,
                                s.state,
                                d.district,
                                t.address,
                                sr.month,
                                sr.year,
                                sr.sales_turnover,
                                (CASE WHEN sr.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(sr.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format
                            FROM estate_sales_report sr
                            LEFT JOIN tea_estate t ON sr.tea_estate_id=t.tea_estate_id
                            LEFT JOIN states s ON t.state_id=s.state_id
                            LEFT JOIN districts d ON t.district_id=d.district_id
                            LEFT JOIN estate_category ect ON t.estate_category_id=ect.estate_category_id
                            WHERE $adQuery
                            GROUP BY sr.estate_sales_report_id
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

    public function postImportSalesReport(ImportRequest $request) {   
        $my_data  = Input::all();      
        $rules  = array(
                    'salesdata' => 'required'
                );
        $validator  = Validator::make($my_data, $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        if($my_data['salesdata']){
            $path1 = ImportRequest::file('salesdata')->getRealPath();
            //echo "<pre>"; print_r($path1); die;
            // $data = Excel::load($path)->get();
            $data = Excel::load($path1)->get();
            // echo "<pre>";print_r($data);die;
            $ExcelArr = array();
            if($data->count() > 0) {
                 foreach ($data as $key => $value) {
                    //echo "<pre>"; print_r($value->tea_estate_id); die;
                    $ExcelArr['tea_estate_id']  = $value->tea_estate_id;
                    $ExcelArr['month']          = $value->month;
                    $ExcelArr['year']           = $value->year;
                    $ExcelArr['sales_turnover'] = $value->sales_turnover;
                    $ExcelArr['status']     = 1;
                    $ExcelArr['created_at'] = date('Y-m-d H:i:s');
                    $ExcelArr['updated_at'] = date('Y-m-d H:i:s');
                    //echo "<pre>"; print_r($ExcelArr); die;
                    $village = DB::table('estate_sales_report')
                                ->where('estate_sales_report.tea_estate_id',$value->tea_estate_id)
                                ->where('estate_sales_report.month',$value->month)
                                ->where('estate_sales_report.year',$value->year)
                                ->where('estate_sales_report.sales_turnover',$value->sales_turnover)
                                ->where('estate_sales_report.status',1)
                                ->select('estate_sales_report.*')
                                ->count();
                    if ($village == 0) { 
                        TeaEstateSalesReport::insert($ExcelArr);
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
        $crr_year       = '2021';
        $years          = date('Y', strtotime('+1 years'));
        $months         = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
        $user_types = array('marketing_supervisor'=>'Marketing Supervisor','zonal'=>'Zonal','regional'=>'Regional','sales_regional'=>'Sales Regional','area'=>'Area','sales_area'=>'Sales Area','bdo'=>'FL');
        return View::make('backend.teaestate.list-target',array('user_types'=>$user_types, 'months'=>$months, 'years'=>$years, 'crr_year'=>$crr_year));
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
                            FROM estate_target t
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
                        $check_target = TeaEstateTarget::where('target_month',$target_month)->where('target_year',$target_year)->where('user_ecn',$user_ecn)->where('user_type',$user_type)->get();
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
                            $insert_target = TeaEstateTarget::firstOrCreate($targetdata);
                            $result        = $insert_target->target_id;
                        } else {
                            $target_id     = $check_target[0]->target_id;
                            $update_target = DB::table('estate_target')
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
                return redirect('teaestate/list-target');
            }
        }
    }

    public function getEditTarget($target_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($target_id!='') {
                $targetdetail   = TeaEstateTarget::where('target_id', $target_id)->first();
                if($targetdetail) {
                    $crr_year       = date('Y');
                    $years          = date('Y', strtotime('+1 years'));
                    $months         = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
                    $user_types = array('marketing_supervisor'=>'Marketing Supervisor','zonal'=>'Zonal','regional'=>'Regional','sales_regional'=>'Sales Regional','area'=>'Area','sales_area'=>'Sales Area','bdo'=>'FL');
                    return View::make('backend.teaestate.edit-target', array('user_types'=>$user_types,'targetdetail' => $targetdetail));
                } else {
                    return redirect('teaestate/list-target');
                }
            } else {
                return redirect('teaestate/list-target');
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

                $update_target = DB::table('estate_target')
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
                    return redirect('teaestate/list-target');
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
        }
        // return $users;
    }

    public function getListVisitsummary() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $crr_year       = '2021';
        $years          = date('Y', strtotime('+1 years'));
        $months         = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
        return View::make('backend.teaestate.list-visitsummary',array('months'=>$months, 'years'=>$years, 'crr_year'=>$crr_year));
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
                            FROM estate_target t
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

            $cat_a_plan     = TeaEstateATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('estate_category',$cat_a_id)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $cat_a_visit    = TeaEstateATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('estate_category',$cat_a_id)->where('is_arrival_departure',1)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $visitsummary_list[$key]->category_a_plan = $cat_a_plan;
            $visitsummary_list[$key]->category_a_visit = $cat_a_visit;

            $cat_b_plan     = TeaEstateATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('estate_category',$cat_b_id)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $cat_b_visit    = TeaEstateATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('estate_category',$cat_b_id)->where('is_arrival_departure',1)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $visitsummary_list[$key]->category_b_plan = $cat_b_plan;
            $visitsummary_list[$key]->category_b_visit = $cat_b_visit;

            $cat_c_plan     = TeaEstateATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('estate_category',$cat_c_id)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $cat_c_visit    = TeaEstateATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('estate_category',$cat_c_id)->where('is_arrival_departure',1)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $visitsummary_list[$key]->category_c_plan = $cat_c_plan;
            $visitsummary_list[$key]->category_c_visit = $cat_c_visit;
        }
        //print_r($visitsummary_list);
        return Datatables::of($visitsummary_list)->make();
    }

    public function getListTestimonials() {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $tea_estate = TeaEstate::get();
        return View::make('backend.teaestate.list-testimonials',array('tea_estate'=> $tea_estate));
    }

    public function postAllTestimonialList(Request $request) {
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
            $adQuery .= " AND DATE(et.created_at) >= DATE('".$start_date."') AND DATE(et.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['tea_estate_id'])) {
            $tea_estate_id = $value['tea_estate_id'];
            $adQuery .= " AND et.tea_estate_id = '".$tea_estate_id."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.contact_number LIKE '%".$search."%' OR A.wa_contact_number LIKE '%".$search."%' OR A.contact_name LIKE '%".$search."%' OR A.wa_number LIKE '%".$search."%' OR A.mobile_number LIKE '%".$search."%' OR A.email LIKE '%".$search."%' OR A.designation LIKE '%".$search."%' OR A.name LIKE '%".$search."%')";
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
                                et.estate_testimonial_id,
                                DATE_FORMAT(et.date_time, '%Y-%m-%d, %h:%i %p') as date_time,
                                t.name as tea_estate_name,
                                (CASE WHEN t.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN t.tea_estate_type='1' THEN 'Tea Estate' ELSE 'STG' END) as tea_estate_type,
                                ect.company_category,
                                et.contact_name,
                                et.contact_designation,
                                et.contact_number,
                                et.wa_contact_number,
                                (CASE WHEN et.can_be_shared='1' THEN 'Yes' ELSE 'No' END) as can_be_shared,
                                et.image_video,
                                et.latitude,
                                et.longitude,
                                et.testimonial_name,
                                et.description,
                                (CASE WHEN et.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(et.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format
                            FROM estate_testimonial et
                            LEFT JOIN tea_estate t ON et.tea_estate_id=t.tea_estate_id
                            LEFT JOIN estate_category ect ON t.estate_category_id=ect.estate_category_id
                            WHERE $adQuery
                            GROUP BY et.estate_testimonial_id
                            ORDER BY
                                et.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $testimonial_list = collect(DB::select($query));
        //print_r($testimonial_list);
        return Datatables::of($testimonial_list)->make();
    }

    public function getEditTestimonial($estate_testimonial_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($estate_testimonial_id!='') {
                $testimonialdetail   = TeaEstateTestimonial::where('estate_testimonial_id', $estate_testimonial_id)->first();
                if($testimonialdetail) {
                    return View::make('backend.teaestate.edit-testimonial', array('testimonialdetail' => $testimonialdetail));
                } else {
                    return redirect('teaestate/list-testimonials');
                }
            } else {
                return redirect('teaestate/list-testimonials');
            }
        } else {
            return redirect('/');
        }
    }

    public function postUpdateTestimonial(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();
            $rules      = array(
                            'estate_testimonial_id' => 'required',
                            'testimonial_name'      => 'required',
                            // 'image_video'   => 'required',
                            'description'   => 'required',
                            'date_time'     => 'required'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $estate_testimonial_id = $my_data['estate_testimonial_id'];
                $testimonial_name   = $my_data['testimonial_name'];
                $date_time          = $my_data['date_time'];
                $description        = $my_data['description'];

                $update_image       = "";
                if($request->file('image_video')) {
                    $file=$request->file('image_video');
                    if(!empty($file)) {
                        //s3Upload
                        $fileName= "Tea_".time().rand(111,999).".".$file->getClientOriginalExtension();
                        // $sTempFileName = 'assets/tea_estate_testimonial/' . $fileName;
                        $sTempFileName = 'assets/testimonials/' . $fileName;
                        Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                        $update_image = TeaEstateTestimonial::where('estate_testimonial_id',$estate_testimonial_id)
                            ->update([
                                'image_video'     => $sTempFileName,
                                'updated_at'=> date('Y-m-d H:i:s')
                            ]);
                    }
                }

                $update_testimonial = TeaEstateTestimonial::
                                    where('estate_testimonial_id',$estate_testimonial_id)
                                    ->update([
                                        'testimonial_name'  => $testimonial_name,
                                        'date_time'   => $date_time,
                                        'description' => $description,
                                        'updated_at'  => date('Y-m-d H:i:s')
                                    ]);
                // return $update_testimonial;
                if($update_testimonial || $update_image) {
                    Session::flash('message', "Testimonial Successfully Updated.");
                    return redirect('teaestate/list-testimonials');
                } else {
                    Session::flash('message', "An error occurret. Please try again later.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            }
        }
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
