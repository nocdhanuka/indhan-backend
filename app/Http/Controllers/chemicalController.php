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

use App\Dcdistrictwise;
use App\Dccropwise;
use App\Dccompanywise;
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

ini_set('max_execution_time', 0);
// ini_set("memory_limit", "-1");
// set_time_limit(0);

class chemicalController extends Controller {

    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect(env('APP_URL'));
        }
    }

    public function getDistrictwisedata() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $states       = State::get();
        $districts    = District::get();
        return View::make('backend.chemical.listdistrictwisedata', array('states'=>$states,'districts'=>$districts));
    }
    
    public function postAlldistrictwisedata(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }

        $columns = array(
            1 =>'A.ecnnumber',
            2=> 'A.ecnname',
            3=> 'A.state',
            4=> 'A.district',
            5=> 'A.class',
            6=> 'A.formulation',
            7=> 'A.brand',
            8=> 'A.nrv',
            9=> 'A.volume',
            10=> 'A.value',
            11=> 'A.remarks'
        );
        // print_r(Request::all()); exit;
        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        $base_url       = url('/');
        
        $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
        $end_date       = isset($value['end_date']) ?  $value['end_date']:'';
        $state          = isset($value['state']) ?  $value['state']:'';
        $district       = isset($value['district']) ?  $value['district']:'';

        $addSearchQry = "";
        if($start_date!='' && $end_date!='') {
            $add2query = "DATE(dw.created_at) >= DATE('".$start_date."') AND DATE(dw.created_at) <= DATE('".$end_date."')";
            $addSearchQry .= "AND DATE(dw.created_at) >= DATE('".$start_date."') AND DATE(dw.created_at) <= DATE('".$end_date."')";
        } elseif($start_date!='') {
            $add2query = "DATE(dw.created_at) >= DATE('".$start_date."')";
            $addSearchQry .= "AND DATE(dw.created_at) >= DATE('".$start_date."')";
        } else {
            $add2query = "(1=1)";
        }
        if($state) {
            $add2query .= "AND dw.state_id = '".$state."'";
            $addSearchQry .= "AND dw.state_id = '".$state."'";
        }
        if($district) {
            $add2query .= "AND dw.district_id = '".$district."'";
            $addSearchQry .= "AND dw.district_id = '".$district."'";
        }

        if(empty($search_array['value'])) {
            $query = "
                SELECT
                    @a:=@a+1  as serial_number,
                    A.*
                        FROM
                            (
                                SELECT
                                    dw.ecnnumber,
                                    dw.ecnname,
                                    s.state,
                                    d.district,
                                    c.name as classname,
                                    f.name as formulationname,
                                    f.brand,
                                    f.nrv,
                                    dw.volume,
                                    dw.value,
                                    dw.remarks,                                        
                                    DATE_FORMAT(dw.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                                    (CASE   WHEN dw.updated_at<>'0000-00-00 00:00:00' 
                                            THEN 
                                                DATE_FORMAT(dw.updated_at, '%Y-%m-%d, %h:%i %p')
                                            ELSE 
                                                '-' 
                                    END)
                                FROM        dcdistrictwise         dw
                                LEFT JOIN   states          s   ON dw.state_id=s.state_id
                                LEFT JOIN   districts       d   ON dw.district_id=d.district_id
                                LEFT JOIN   class           c   ON dw.class_id=c.class_id
                                LEFT JOIN   formulation     f   ON dw.formulation_id=f.f_id
                                WHERE  ".$add2query."
                                ORDER BY c_datetime_format DESC
                            ) A,
                            (select @a:=0) initvars";
                // echo $query; exit;
            $districtwise_data = collect(DB::select($query));
            return Datatables::of($districtwise_data)->make();            
        } else {
            $search = $search_array['value'];
            $query  = "
                SELECT
                    @a:=@a+1  as serial_number,
                    A.*
                        FROM
                            (
                        SELECT
                            dw.ecnnumber,
                            dw.ecnname,
                            s.state,
                            d.district,
                            c.name as classname,
                            f.name as formulationname,
                            f.brand,
                            f.nrv,
                            dw.volume,
                            dw.value,
                            dw.remarks,                                        
                            DATE_FORMAT(dw.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                            (CASE   WHEN dw.updated_at<>'0000-00-00 00:00:00' 
                                    THEN 
                                        DATE_FORMAT(dw.updated_at, '%Y-%m-%d, %h:%i %p')
                                    ELSE 
                                        '-' 
                            END)
                        FROM        dcdistrictwise         dw
                        LEFT JOIN   states          s   ON dw.state_id=s.state_id
                        LEFT JOIN   districts       d   ON dw.district_id=d.district_id
                        LEFT JOIN   class           c   ON dw.class_id=c.class_id
                        LEFT JOIN   formulation     f   ON dw.formulation_id=f.f_id
                        WHERE (
                                dw.ecnnumber    LIKE '%".$search."%'   OR
                                dw.ecnname      LIKE '%".$search."%'   OR
                                c.name          LIKE '%".$search."%'   OR 
                                f.name          LIKE '%".$search."%'   OR 
                                f.brand         LIKE '%".$search."%'
                            )
                            $addSearchQry
                            ORDER BY c_datetime_format DESC
                        ) A,
                        (select @a:=0) initvars" ;
                        // return $query;
            $districtwise_data    = collect(DB::select($query));
            return Datatables::of($districtwise_data)->make();
        }
    }

    public function getCropwisedata() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $states       = State::get();
        $districts    = District::get();
        return View::make('backend.chemical.listcropwisedata', array('states'=>$states,'districts'=>$districts));
    }
    
    public function postAllcropwisedata(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }

        $columns = array(
            1 =>'A.ecnnumber',
            2=> 'A.ecnname',
            3=> 'A.state',
            4=> 'A.district',
            5=> 'A.class',
            6=> 'A.formulation',
            7=> 'A.volume',
            8=> 'A.value',
            9=> 'A.remarks',
            10=> 'maize',
            11=> 'rice',
            12=> 'wheat',
            13=> 'cotton',
            14=> 'grapes',
            15=> 'apple',
            16=> 'mango',
            17=> 'pomegranate',
            18=> 'citrus',
            19=> 'other_fruits',
            20=> 'groundnut',
            21=> 'soybean',
            22=> 'other_oilseeds',
            23=> 'sugarcane',
            24=> 'tea',
            25=> 'bengal_gram',
            26=> 'black_gram',
            27=> 'other_pulses',
            28=> 'cumin',
            29=> 'brinjal',
            30=> 'chillies',
            31=> 'cole_crops',
            32=> 'cucurbits_melons',
            33=> 'onion_garlic',
            34=> 'potato',
            35=> 'tomato',
            36=> 'other_veg',
            37=> 'others',
            38=> 'total',
            39=> 'percentage'
        );
        // print_r(Request::all()); exit;
        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        $base_url       = url('/');
        
        $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
        $end_date       = isset($value['end_date']) ?  $value['end_date']:'';
        $state          = isset($value['state']) ?  $value['state']:'';
        $district       = isset($value['district']) ?  $value['district']:'';

        $addSearchQry = "";
        if($start_date!='' && $end_date!='') {
            $add2query = "DATE(dc.created_at) >= DATE('".$start_date."') AND DATE(dc.created_at) <= DATE('".$end_date."')";
            $addSearchQry .= "AND DATE(dc.created_at) >= DATE('".$start_date."') AND DATE(dc.created_at) <= DATE('".$end_date."')";
        } elseif($start_date!='') {
            $add2query = "DATE(dc.created_at) >= DATE('".$start_date."')";
            $addSearchQry .= "AND DATE(dc.created_at) >= DATE('".$start_date."')";
        } else {
            $add2query = "(1=1)";
        }
        if($state) {
            $add2query .= "AND dc.state_id = '".$state."'";
            $addSearchQry .= "AND dc.state_id = '".$state."'";
        }
        if($district) {
            $add2query .= "AND dc.district_id = '".$district."'";
            $addSearchQry .= "AND dc.district_id = '".$district."'";
        }

        if(empty($search_array['value'])) {
            $query = "
                SELECT
                    @a:=@a+1  as serial_number,
                    A.*
                        FROM
                            (
                                SELECT
                                    dc.ecnnumber,
                                    dc.ecnname,
                                    s.state,
                                    d.district,
                                    c.name as classname,
                                    f.name as formulationname,
                                    dc.volume,
                                    dc.value,
                                    dc.maize,
                                    dc.rice,
                                    dc.wheat,
                                    dc.cotton,
                                    dc.grapes,
                                    dc.apple,
                                    dc.mango,
                                    dc.pomegranate,
                                    dc.citrus,
                                    dc.other_fruits,
                                    dc.groundnut,
                                    dc.soybean,
                                    dc.other_oilseeds,
                                    dc.sugarcane,
                                    dc.tea,
                                    dc.bengal_gram,
                                    dc.black_gram,
                                    dc.other_pulses,
                                    dc.cumin,
                                    dc.brinjal,
                                    dc.chillies,
                                    dc.cole_crops,
                                    dc.cucurbits_melons,
                                    dc.onion_garlic,
                                    dc.potato,
                                    dc.tomato,
                                    dc.other_veg,
                                    dc.others,
                                    dc.total,
                                    dc.percentage,
                                    DATE_FORMAT(dc.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                                    (CASE   WHEN dc.updated_at<>'0000-00-00 00:00:00' 
                                            THEN 
                                                DATE_FORMAT(dc.updated_at, '%Y-%m-%d, %h:%i %p')
                                            ELSE 
                                                '-' 
                                    END)
                                FROM        dccropwise      dc
                                LEFT JOIN   states          s   ON dc.state_id=s.state_id
                                LEFT JOIN   districts       d   ON dc.district_id=d.district_id
                                LEFT JOIN   class           c   ON dc.class_id=c.class_id
                                LEFT JOIN   formulation     f   ON dc.formulation_id=f.f_id
                                WHERE  ".$add2query."
                                ORDER BY c_datetime_format DESC
                            ) A,
                            (select @a:=0) initvars";
                // echo $query; exit;
            $districtwise_data = collect(DB::select($query));
            return Datatables::of($districtwise_data)->make();
        } else {
            $search = $search_array['value'];
            $query  = "
                SELECT
                    @a:=@a+1  as serial_number,
                    A.*
                        FROM
                            (
                                SELECT
                                    dc.ecnnumber,
                                    dc.ecnname,
                                    s.state,
                                    d.district,
                                    c.name as classname,
                                    f.name as formulationname,
                                    dc.volume,
                                    dc.value,
                                    dc.maize,
                                    dc.rice,
                                    dc.wheat,
                                    dc.cotton,
                                    dc.grapes,
                                    dc.apple,
                                    dc.mango,
                                    dc.pomegranate,
                                    dc.citrus,
                                    dc.other_fruits,
                                    dc.groundnut,
                                    dc.soybean,
                                    dc.other_oilseeds,
                                    dc.sugarcane,
                                    dc.tea,
                                    dc.bengal_gram,
                                    dc.black_gram,
                                    dc.other_pulses,
                                    dc.cumin,
                                    dc.brinjal,
                                    dc.chillies,
                                    dc.cole_crops,
                                    dc.cucurbits_melons,
                                    dc.onion_garlic,
                                    dc.potato,
                                    dc.tomato,
                                    dc.other_veg,
                                    dc.others,
                                    dc.total,
                                    dc.percentage,
                                    DATE_FORMAT(dc.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                                    (CASE   WHEN dc.updated_at<>'0000-00-00 00:00:00' 
                                            THEN 
                                                DATE_FORMAT(dc.updated_at, '%Y-%m-%d, %h:%i %p')
                                            ELSE 
                                                '-' 
                                    END)
                                FROM        dccropwise      dc
                                LEFT JOIN   states          s   ON dc.state_id=s.state_id
                                LEFT JOIN   districts       d   ON dc.district_id=d.district_id
                                LEFT JOIN   class           c   ON dc.class_id=c.class_id
                                LEFT JOIN   formulation     f   ON dc.formulation_id=f.f_id
                        WHERE (
                                dc.ecnnumber    LIKE '%".$search."%'   OR
                                dc.ecnname      LIKE '%".$search."%'   OR
                                c.name     LIKE '%".$search."%'   OR 
                                f.name  LIKE '%".$search."%'
                            )
                            $addSearchQry
                            ORDER BY c_datetime_format DESC
                        ) A,
                        (select @a:=0) initvars" ;
            $districtwise_data    = collect(DB::select($query));
            return Datatables::of($districtwise_data)->make();
        }
    }

    public function getCompanywisedata() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $states       = State::get();
        $districts    = District::get();
        return View::make('backend.chemical.listcompanywisedata', array('states'=>$states,'districts'=>$districts));
    }
    
    public function postAllcompanywisedata(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }

        $columns = array(
            1 =>'A.ecnnumber',
            2=> 'A.ecnname',
            3=> 'A.state',
            4=> 'A.district',
            5=> 'A.class',
            6=> 'A.formulation',
            7=> 'A.volume',
            8=> 'A.value',
            9=> 'A.adama',
            10=> 'A.atul',
            11=> 'A.bayer',
            12=> 'A.basf',
            13=> 'A.bharat_certis',
            14=> 'A.bio_stadt',
            15=> 'A.cheminova',
            16=> 'A.coromandal_inter_ltd',
            17=> 'A.coromandel_agri_co',
            18=> 'A.crystal',
            19=> 'A.corteva',
            20=> 'A.devidayal',
            21=> 'A.dhanuka_agritech',
            22=> 'A.excel_crop_care',
            23=> 'A.fil',
            24=> 'A.fmc',
            25=> 'A.gharda_chemicals',
            26=> 'A.godrej_agrovet_ltd',
            27=> 'A.gsp',
            28=> 'A.hindustan_insecticides_ltd',
            29=> 'A.hindustan_pulverising',
            30=> 'A.hpm',
            31=> 'A.heranba',
            32=> 'A.indofil',
            33=> 'A.indo_gulf',
            34=> 'A.insecticides_india_ltd',
            35=> 'A.ichiban',
            36=> 'A.ipl',
            37=> 'A.ju_agri_science',
            38=> 'A.jubilant',
            39=> 'A.krishi_rasayan',
            40=> 'A.meghmani',
            41=> 'A.nagarjuna_agri_chem',
            42=> 'A.nichino',
            43=> 'A.multiplex',
            44=> 'A.mahindra',
            45=> 'A.pi_industries',
            46=> 'A.parijat',
            47=> 'A.punjab_chemicals',
            48=> 'A.rallis',
            49=> 'A.safex',
            50=> 'A.shivalik_crop',
            51=> 'A.sudarshan_chemical',
            52=> 'A.sulphur_mills',
            53=> 'A.sumitomo',
            54=> 'A.swal_corporation',
            55=> 'A.syngenta',
            56=> 'A.upl_ltd',
            57=> 'A.willowood_crop_sciences',
            58=> 'A.zuari_agro_chemicals',
            59=> 'A.others',
            60=> 'A.grand_total',
            61=> 'A.check'
        );

        // print_r(Request::all()); exit;
        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        $base_url       = url('/');
        
        $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
        $end_date       = isset($value['end_date']) ?  $value['end_date']:'';
        $state          = isset($value['state']) ?  $value['state']:'';
        $district       = isset($value['district']) ?  $value['district']:'';

        $addSearchQry   = ""; 
        if($start_date!='' && $end_date!='') {
            $add2query = "DATE(dc.created_at) >= DATE('".$start_date."') AND DATE(dc.created_at) <= DATE('".$end_date."')";
            $addSearchQry .= "AND DATE(dc.created_at) >= DATE('".$start_date."') AND DATE(dc.created_at) <= DATE('".$end_date."')";
        } elseif($start_date!='') {
            $add2query = "DATE(dc.created_at) >= DATE('".$start_date."')";
            $addSearchQry .= "AND DATE(dc.created_at) >= DATE('".$start_date."')";
        } else {
            $add2query = "(1=1)";
        }
        if($state) {
            $add2query .= "AND dc.state_id = '".$state."'";
            $addSearchQry .= "AND dc.state_id = '".$state."'";
        }
        if($district) {
            $add2query .= "AND dc.district_id = '".$district."'";
            $addSearchQry .= "AND dc.district_id = '".$district."'";
        }

        if(empty($search_array['value'])) {
            $query = "
                SELECT
                    @a:=@a+1  as serial_number,
                    A.*
                        FROM
                            (
                                SELECT
                                    dc.ecnnumber,
                                    dc.ecnname,
                                    s.state,
                                    d.district,
                                    c.name as classname,
                                    f.name as formulationname,
                                    dc.volume,
                                    dc.value,
                                    dc.adama,
                                    dc.atul,
                                    dc.bayer,
                                    dc.basf,
                                    dc.bharat_certis,
                                    dc.bio_stadt,
                                    dc.cheminova,
                                    dc.coromandal_inter_ltd,
                                    dc.coromandel_agri_co,
                                    dc.crystal,
                                    dc.corteva,
                                    dc.devidayal,
                                    dc.dhanuka_agritech,
                                    dc.excel_crop_care,
                                    dc.fil,
                                    dc.fmc,
                                    dc.gharda_chemicals,
                                    dc.godrej_agrovet_ltd,
                                    dc.gsp,
                                    dc.hindustan_insecticides_ltd,
                                    dc.hindustan_pulverising,
                                    dc.hpm,
                                    dc.heranba,
                                    dc.indofil,
                                    dc.indo_gulf,
                                    dc.insecticides_india_ltd,
                                    dc.ichiban,
                                    dc.ipl,
                                    dc.ju_agri_science,
                                    dc.jubilant,
                                    dc.krishi_rasayan,
                                    dc.meghmani,
                                    dc.nagarjuna_agri_chem,
                                    dc.nichino,
                                    dc.multiplex,
                                    dc.mahindra,
                                    dc.pi_industries,
                                    dc.parijat,
                                    dc.punjab_chemicals,
                                    dc.rallis,
                                    dc.safex,
                                    dc.shivalik_crop,
                                    dc.sudarshan_chemical,
                                    dc.sulphur_mills,
                                    dc.sumitomo,
                                    dc.swal_corporation,
                                    dc.syngenta,
                                    dc.upl_ltd,
                                    dc.willowood_crop_sciences,
                                    dc.zuari_agro_chemicals,
                                    dc.others,
                                    dc.grand_total,
                                    dc.check,
                                    DATE_FORMAT(dc.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                                    (CASE   WHEN dc.updated_at<>'0000-00-00 00:00:00' 
                                            THEN 
                                                DATE_FORMAT(dc.updated_at, '%Y-%m-%d, %h:%i %p')
                                            ELSE 
                                                '-' 
                                    END)
                                FROM        dccompanywise   dc
                                LEFT JOIN   states          s   ON dc.state_id=s.state_id
                                LEFT JOIN   districts       d   ON dc.district_id=d.district_id
                                LEFT JOIN   class           c   ON dc.class_id=c.class_id
                                LEFT JOIN   formulation     f   ON dc.formulation_id=f.f_id
                                WHERE  ".$add2query."
                                ORDER BY c_datetime_format DESC
                            ) A,
                            (select @a:=0) initvars";
                // echo $query; exit;
            $companywise_data = collect(DB::select($query));
            return Datatables::of($companywise_data)->make();
        } else {
            $search = $search_array['value'];
            $query  = "
                SELECT
                    @a:=@a+1  as serial_number,
                    A.*
                        FROM
                            (
                                SELECT
                                    dc.ecnnumber,
                                    dc.ecnname,
                                    s.state,
                                    d.district,
                                    c.name as classname,
                                    f.name as formulationname,
                                    dc.volume,
                                    dc.value,
                                    dc.adama,
                                    dc.atul,
                                    dc.bayer,
                                    dc.basf,
                                    dc.bharat_certis,
                                    dc.bio_stadt,
                                    dc.cheminova,
                                    dc.coromandal_inter_ltd,
                                    dc.coromandel_agri_co,
                                    dc.crystal,
                                    dc.corteva,
                                    dc.devidayal,
                                    dc.dhanuka_agritech,
                                    dc.excel_crop_care,
                                    dc.fil,
                                    dc.fmc,
                                    dc.gharda_chemicals,
                                    dc.godrej_agrovet_ltd,
                                    dc.gsp,
                                    dc.hindustan_insecticides_ltd,
                                    dc.hindustan_pulverising,
                                    dc.hpm,
                                    dc.heranba,
                                    dc.indofil,
                                    dc.indo_gulf,
                                    dc.insecticides_india_ltd,
                                    dc.ichiban,
                                    dc.ipl,
                                    dc.ju_agri_science,
                                    dc.jubilant,
                                    dc.krishi_rasayan,
                                    dc.meghmani,
                                    dc.nagarjuna_agri_chem,
                                    dc.nichino,
                                    dc.multiplex,
                                    dc.mahindra,
                                    dc.pi_industries,
                                    dc.parijat,
                                    dc.punjab_chemicals,
                                    dc.rallis,
                                    dc.safex,
                                    dc.shivalik_crop,
                                    dc.sudarshan_chemical,
                                    dc.sulphur_mills,
                                    dc.sumitomo,
                                    dc.swal_corporation,
                                    dc.syngenta,
                                    dc.upl_ltd,
                                    dc.willowood_crop_sciences,
                                    dc.zuari_agro_chemicals,
                                    dc.others,
                                    dc.grand_total,
                                    dc.check,
                                    DATE_FORMAT(dc.created_at, '%Y-%m-%d, %h:%i %p') as c_datetime_format,
                                    (CASE   WHEN dc.updated_at<>'0000-00-00 00:00:00' 
                                            THEN 
                                                DATE_FORMAT(dc.updated_at, '%Y-%m-%d, %h:%i %p')
                                            ELSE 
                                                '-' 
                                    END)
                                FROM        dccompanywise   dc
                                LEFT JOIN   states          s   ON dc.state_id=s.state_id
                                LEFT JOIN   districts       d   ON dc.district_id=d.district_id
                                LEFT JOIN   class           c   ON dc.class_id=c.class_id
                                LEFT JOIN   formulation     f   ON dc.formulation_id=f.f_id
                        WHERE (
                                dc.ecnnumber    LIKE '%".$search."%'   OR
                                dc.ecnname      LIKE '%".$search."%'   OR
                                c.name          LIKE '%".$search."%'   OR 
                                f.name          LIKE '%".$search."%'
                            )
                            $addSearchQry
                            ORDER BY c_datetime_format DESC
                        ) A,
                        (select @a:=0) initvars" ;
            $companywise_data    = collect(DB::select($query));
            return Datatables::of($companywise_data)->make();
        }
    }
}