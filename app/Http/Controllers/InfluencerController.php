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

use App\Influencer;
use App\InfluencerContact;
use App\InfluencerCategory;
use App\InfluencerCompany;
use App\InfluencerDesignation;
use App\InfluencerATP;
use App\InfluencerSalesReport;
use App\InfluencerTarget;

use App\TeaEstateTestimonial;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Storage;

class InfluencerController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function getListInfluencer() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $influencercategory = InfluencerCategory::where('status',1)->get();
        $influencercompany  = InfluencerCompany::where('status',1)->get();
        return View::make('backend.influencer.list-influencer',array('influencercategory'=>$influencercategory, 'influencercompany'=>$influencercompany));
    }

    public function postAllInfluencerList(Request $request) {
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
        $influencer_category_id = $value['influencer_category_id'];
        $influencer_company_id  = $value['influencer_company_id'];

        $adQuery = "(1=1)";
        if($start_date!='' && $end_date!='') {
            $adQuery .= " AND DATE(i.created_at) >= DATE('".$start_date."') AND DATE(i.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($influencer_category_id)) { $adQuery .= " AND i.influencer_category_id = '".$influencer_category_id."'"; }
        if(!empty($influencer_company_id)) { $adQuery .= " AND i.influencer_company_id = '".$influencer_company_id."'"; }

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
                                i.influencer_id,
                                (CASE WHEN i.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN i.influencer_type='1' THEN 'Influencer' ELSE 'SIG' END) as influencer_type,
                                i.parent_influencer_id as parent_influencer,
                                i.name,
                                i.turn_over,
                                i.land_area,
                                ic.company_type,
                                ict.company_category,
                                s.state,
                                d.district,
                                i.address,
                                i.latitude,
                                i.longitude,
                                '0' as contacts,
                                i.image,
                                i.fl_ecn,
                                i.addedby_ecn,
                                (CASE WHEN i.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(i.created_at, '%Y-%m-%d, %h:%i %p') as created_at
                            FROM influencer i
                            LEFT JOIN states s ON i.state_id=s.state_id
                            LEFT JOIN districts d ON i.district_id=d.district_id
                            LEFT JOIN influencer_company ic ON i.influencer_company_id=ic.influencer_company_id
                            LEFT JOIN influencer_category ict ON i.influencer_category_id=ict.influencer_category_id
                            WHERE $adQuery
                            GROUP BY i.influencer_id
                            ORDER BY
                                i.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $influencer_list    = collect(DB::select($query));
        foreach ($influencer_list as $key => $value) {
            $parent_influencer  = $value->parent_influencer;
            $influencer_id      = $value->influencer_id;
            if($parent_influencer!=0) {
                $influencer = Influencer::where('influencer_id',$parent_influencer)->get();
                if(count($influencer)>0) {
                    $influencer_list[$key]->parent_influencer = $influencer[0]->name;
                } else {
                    $influencer_list[$key]->parent_influencer = "";
                }
            } else {
                $influencer_list[$key]->parent_influencer = "";
            }
            if($influencer_id) {
                $influencer_contact_count = InfluencerContact::where('influencer_id',$influencer_id)->count();
                if($influencer_contact_count) {
                    $influencer_list[$key]->contacts = $influencer_contact_count;
                } else {
                    $influencer_list[$key]->contacts = 0;
                }
            }
        }
        //print_r($influencer_list);
        return Datatables::of($influencer_list)->make();
    }

    public function getListContact($influencer_id='') {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        if(($influencer_id!='') && ($influencer_id!=0)) {
            $influencer = Influencer::where('influencer_id',$influencer_id)->get();
            if(count($influencer)>0) {
                return View::make('backend.influencer.list-influencercontact',array('influencer'=> $influencer, 'influencer_id'=>$influencer_id,'show_all'=>0));
            } else {
                return redirect('/influencer/list-influencer');
            }
        } else {
            $influencer = Influencer::get();
            return View::make('backend.influencer.list-influencercontact',array('influencer'=> $influencer, 'influencer_id'=>$influencer_id,'show_all'=>1));
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
            $adQuery .= " AND DATE(ic.created_at) >= DATE('".$start_date."') AND DATE(ic.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['influencer_id'])) {
            $influencer_id = $value['influencer_id'];
            $adQuery .= " AND ic.influencer_id = '".$influencer_id."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.influencer_name LIKE '%".$search."%' OR A.contact_name LIKE '%".$search."%' OR A.wa_number LIKE '%".$search."%' OR A.mobile_number LIKE '%".$search."%' OR A.email LIKE '%".$search."%')";
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
                                ic.influencer_contact_id,
                                i.influencer_id,
                                (CASE WHEN i.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN i.influencer_type='1' THEN 'Influencer' ELSE 'SIG' END) as influencer_type,
                                i.parent_influencer_id as parent_influencer,
                                i.name as influencer_name,
                                inc.company_type,
                                ict.company_category,
                                ic.level,
                                ic.designation,
                                ic.name as contact_name,
                                ic.wa_number,
                                ic.mobile_number,
                                ic.email,
                                (CASE WHEN ic.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(ic.created_at, '%Y-%m-%d, %h:%i %p') as created_at
                            FROM influencer_contact ic
                            LEFT JOIN influencer i ON ic.influencer_id=i.influencer_id
                            LEFT JOIN influencer_company inc ON i.influencer_company_id=inc.influencer_company_id
                            LEFT JOIN influencer_category ict ON i.influencer_category_id=ict.influencer_category_id
                            WHERE $adQuery
                            GROUP BY ic.influencer_contact_id
                            ORDER BY
                                ic.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $influencer_contact_list = collect(DB::select($query));
        foreach ($influencer_contact_list as $key => $value) {
            $parent_influencer  = $value->parent_influencer;
            $influencer_id      = $value->influencer_id;
            if($parent_influencer!=0) {
                $influencer = Influencer::where('influencer_id',$parent_influencer)->get();
                if(count($influencer)>0) {
                    $influencer_contact_list[$key]->parent_influencer = $influencer[0]->name;
                } else {
                    $influencer_contact_list[$key]->parent_influencer = "";
                }
            } else {
                $influencer_contact_list[$key]->parent_influencer = "";
            }
        }
        //print_r($influencer_contact_list);
        return Datatables::of($influencer_contact_list)->make();
    }

    public function getListAtp() {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $influencer = Influencer::get();
        return View::make('backend.influencer.list-influenceratp',array('influencer'=> $influencer));
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
            $adQuery .= " AND DATE(ia.created_at) >= DATE('".$start_date."') AND DATE(ia.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['influencer_id'])) {
            $influencer_id = $value['influencer_id'];
            $adQuery .= " AND ia.influencer_id = '".$influencer_id."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.ticket_number LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.address LIKE '%".$search."%' OR A.influencer_name LIKE '%".$search."%')";
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
                                ia.influencer_atp_id,
                                DATE_FORMAT(ia.date_time, '%Y-%m-%d, %h:%i %p') as date_time,
                                ia.ticket_number,
                                (CASE WHEN ia.is_manager='0' THEN 'FL' ELSE 'Both' END) as is_manager,
                                ia.fl_ecn,
                                ia.manager_ecn,
                                ia.influencer_id,
                                i.name as influencer_name,
                                (CASE WHEN i.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN i.influencer_type='1' THEN 'Influencer' ELSE 'SIG' END) as influencer_type,
                                ict.company_category,
                                s.state,
                                d.district,
                                i.address,
                                (CASE WHEN ia.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(ia.created_at, '%Y-%m-%d, %h:%i %p') as created_at
                            FROM influencer_atp ia
                            LEFT JOIN influencer i ON ia.influencer_id=i.influencer_id
                            LEFT JOIN states s ON i.state_id=s.state_id
                            LEFT JOIN districts d ON i.district_id=d.district_id
                            LEFT JOIN influencer_category ict ON i.influencer_category_id=ict.influencer_category_id
                            WHERE $adQuery
                            GROUP BY ia.influencer_atp_id
                            ORDER BY
                                ia.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $influencer_atp_list = collect(DB::select($query));
        //print_r($influencer_atp_list);
        return Datatables::of($influencer_atp_list)->make();
    }

    public function getListArivalDeparture() {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $influencer = Influencer::get();
        return View::make('backend.influencer.list-influencerarivaldeparture',array('influencer'=> $influencer));
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
            $adQuery .= " AND DATE(ia.created_at) >= DATE('".$start_date."') AND DATE(ia.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['influencer_id'])) {
            $influencer_id = $value['influencer_id'];
            $adQuery .= " AND ia.influencer_id = '".$influencer_id."'";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.ticket_number LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.address LIKE '%".$search."%' OR A.influencer_name LIKE '%".$search."%')";
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
                                ad.influencer_atp_arrival_departure_id,
                                DATE_FORMAT(ia.date_time, '%Y-%m-%d, %h:%i %p') as date_time,
                                ia.ticket_number,
                                (CASE WHEN ia.is_manager='0' THEN 'FL' ELSE 'Both' END) as is_manager,
                                ia.fl_ecn,
                                ia.manager_ecn,
                                i.influencer_id,
                                i.name as influencer_name,
                                (CASE WHEN i.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN i.influencer_type='1' THEN 'Influencer' ELSE 'SI' END) as influencer_type,
                                ict.company_category,
                                s.state,
                                d.district,
                                i.address,
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
                            FROM influencer_atp_arrival_departure ad
                            LEFT JOIN influencer_atp ia ON ad.influencer_atp_id=ia.influencer_atp_id
                            LEFT JOIN influencer i ON ia.influencer_id=i.influencer_id
                            LEFT JOIN states s ON i.state_id=s.state_id
                            LEFT JOIN districts d ON i.district_id=d.district_id
                            LEFT JOIN influencer_category ict ON i.influencer_category_id=ict.influencer_category_id
                            WHERE $adQuery
                            GROUP BY ad.influencer_atp_arrival_departure_id
                            ORDER BY ia.created_at DESC
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
        $influencer = Influencer::get();
        // $crr_year   = date('Y');
        $crr_year   = '2021';
        $years      = date('Y', strtotime('+1 years'));
        $months     = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
        return View::make('backend.influencer.list-salesreport',array('influencer'=>$influencer, 'months'=>$months, 'years'=>$years, 'crr_year'=>$crr_year));
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
        if(!empty($value['influencer_id'])) {
            $influencer_id = $value['influencer_id'];
            $adQuery .= " AND sr.influencer_id = '".$influencer_id."'";
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
            $adSearch = " WHERE (A.ticket_number LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.address LIKE '%".$search."%' OR A.influencer_name LIKE '%".$search."%')";
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
                                sr.influencer_sales_report_id,
                                i.name as influencer_name,
                                (CASE WHEN i.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN i.influencer_type='1' THEN 'Influencer' ELSE 'SIG' END) as influencer_type,
                                ict.company_category,
                                s.state,
                                d.district,
                                i.address,
                                sr.month,
                                sr.year,
                                sr.sales_turnover,
                                (CASE WHEN sr.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(sr.created_at, '%Y-%m-%d, %h:%i %p') as created_at
                            FROM influencer_sales_report sr
                            LEFT JOIN influencer i ON sr.influencer_id=i.influencer_id
                            LEFT JOIN states s ON i.state_id=s.state_id
                            LEFT JOIN districts d ON i.district_id=d.district_id
                            LEFT JOIN influencer_category ict ON i.influencer_category_id=ict.influencer_category_id
                            WHERE $adQuery
                            GROUP BY sr.influencer_sales_report_id
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
                    //echo "<pre>"; print_r($value->influencer_id); die;
                    $ExcelArr['influencer_id']  = $value->influencer_id;
                    $ExcelArr['month']          = $value->month;
                    $ExcelArr['year']           = $value->year;
                    $ExcelArr['sales_turnover'] = $value->sales_turnover;
                    $ExcelArr['status']     = 1;
                    $ExcelArr['created_at'] = date('Y-m-d H:i:s');
                    $ExcelArr['updated_at'] = date('Y-m-d H:i:s');
                    //echo "<pre>"; print_r($ExcelArr); die;
                    $village = DB::table('influencer_sales_report')
                                ->where('influencer_sales_report.influencer_id',$value->influencer_id)
                                ->where('influencer_sales_report.month',$value->month)
                                ->where('influencer_sales_report.year',$value->year)
                                ->where('influencer_sales_report.sales_turnover',$value->sales_turnover)
                                ->where('influencer_sales_report.status',1)
                                ->select('influencer_sales_report.*')
                                ->count();
                    if ($village == 0) { 
                        InfluencerSalesReport::insert($ExcelArr);
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
        return View::make('backend.influencer.list-target',array('user_types'=>$user_types, 'months'=>$months, 'years'=>$years, 'crr_year'=>$crr_year));
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
                            FROM influencer_target t
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
                        $check_target = InfluencerTarget::where('target_month',$target_month)->where('target_year',$target_year)->where('user_ecn',$user_ecn)->where('user_type',$user_type)->get();
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
                            $insert_target = InfluencerTarget::firstOrCreate($targetdata);
                            $result        = $insert_target->target_id;
                        } else {
                            $target_id     = $check_target[0]->target_id;
                            $update_target = DB::table('influencer_target')
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
                return redirect('influencer/list-target');
            }
        }
    }

    public function getEditTarget($target_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($target_id!='') {
                $targetdetail   = InfluencerTarget::where('target_id', $target_id)->first();
                if($targetdetail) {
                    $crr_year       = date('Y');
                    $years          = date('Y', strtotime('+1 years'));
                    $months         = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
                    $user_types = array('marketing_supervisor'=>'Marketing Supervisor','zonal'=>'Zonal','regional'=>'Regional','sales_regional'=>'Sales Regional','area'=>'Area','sales_area'=>'Sales Area','bdo'=>'FL');
                    return View::make('backend.influencer.edit-target', array('user_types'=>$user_types,'targetdetail' => $targetdetail));
                } else {
                    return redirect('influencer/list-target');
                }
            } else {
                return redirect('influencer/list-target');
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

                $update_target = DB::table('influencer_target')
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
                if($update_target) {
                    Session::flash('message', "Target Successfully Updated.");
                    return redirect('influencer/list-target');
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
    }

    public function getListVisitsummary() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $crr_year       = '2021';
        $years          = date('Y', strtotime('+1 years'));
        $months         = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
        return View::make('backend.influencer.list-visitsummary',array('months'=>$months, 'years'=>$years, 'crr_year'=>$crr_year));
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
                            FROM influencer_target t
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

            $cat_a_plan     = InfluencerATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('influencer_category',$cat_a_id)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $cat_a_visit    = InfluencerATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('influencer_category',$cat_a_id)->where('is_arrival_departure',1)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $visitsummary_list[$key]->category_a_plan = $cat_a_plan;
            $visitsummary_list[$key]->category_a_visit = $cat_a_visit;

            $cat_b_plan     = InfluencerATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('influencer_category',$cat_b_id)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $cat_b_visit    = InfluencerATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('influencer_category',$cat_b_id)->where('is_arrival_departure',1)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $visitsummary_list[$key]->category_b_plan = $cat_b_plan;
            $visitsummary_list[$key]->category_b_visit = $cat_b_visit;

            $cat_c_plan     = InfluencerATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('influencer_category',$cat_c_id)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
            $cat_c_visit    = InfluencerATP::where('fl_type',$user_type)->where('fl_ecn',$user_ecn)->where('influencer_category',$cat_c_id)->where('is_arrival_departure',1)->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end)->count();
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
