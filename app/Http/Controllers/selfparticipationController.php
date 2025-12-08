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
use App\DataSource;
use App\State;
use App\District;
use App\Tehsil;
use App\Product;
use App\Crop;
use App\Ticket;
use App\Farmer;
use App\Language;
use App\CustomerQuery;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class selfparticipationController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getParticipationList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.selfpaticipation.participationlist');
    }
    
    public function postAllquerylist__Old(Request $request) {
        $sess = \Session::get('adminid');

        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }else{
            
            $userData = \DB::table('branch_admin')->where(['id' => $sess])->first();

            if($userData) {
                $zonal_manager_id = $userData->zonal_manager_id;
                if($zonal_manager_id != '' && !empty($zonal_manager_id)){
                    $zonalq = " user_id = '".$zonal_manager_id."' and user_type = 'zonal'";
                }
                else{
                    $zonalq = "";
                }
                //rsm
                $rsmData = DB::table('sales_regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
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
                    $rsmresult1 = implode("','", $rsmresult);
                    if($rsmresult1 != '' && !empty($rsmresult1)){
                        $rsmresultq = " or user_id in ('".$rsmresult1."') and user_type = 'sales_regional'";
                    }
                    else{
                        $rsmresultq = "";
                    }
                }

                //asm
                $asmData = DB::table('sales_area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
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
                    $asmresult1 = implode("','", $asmresult);
                    if($asmresult1 != '' && !empty($asmresult1)){
                        $asmresultq = " or user_id in ('".$asmresult1."') and user_type = 'sales_area'";
                    }
                    else{
                        $asmresultq = "";
                    }
                }

                //regional
                $rData = DB::table('regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('regional_manager_id')
                          ->get();
                $rresult = array(); 
                if (isset($rData) && !empty($rData)) 
                {
                    $rDatass = json_decode(json_encode($rData),true);
                    foreach ($rDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $rresult = array_merge($rresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                            $rresult[$key] = $value; 
                        } 
                    } 
                    $rresult1 = implode("','", $rresult);
                    if($rresult1 != '' && !empty($rresult1)){
                        $rresultq = " or user_id in ('".$rresult1."') and user_type = 'regional' ";
                    }
                    else{
                        $rresultq = "";
                    }
                }

                //area
                $aData = DB::table('area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('area_manager_id')
                          ->get();
                $aresult = array(); 
                if (isset($aData) && !empty($aData)) 
                {
                    $aDatass = json_decode(json_encode($aData),true);
                    foreach ($aDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $aresult = array_merge($aresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                            $aresult[$key] = $value; 
                        } 
                    } 
                    $aresult1 = implode("','", $aresult);
                    if($aresult1 != '' && !empty($aresult1)){
                        $aresultq = " or user_id in ('".$aresult1."') and user_type = 'area' ";
                    }
                    else{
                        $aresultq = "";
                    }
                }
            }
            else{
                $zonalq     = " (1=1) ";
                $rsmresultq = "";
                $asmresultq = "";
                $rresultq   = "";
                $aresultq   = "";
            }
        }

        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        $test = $request::all();
        $start_date     = isset($test['start_date']) ? $test['start_date'] :'';
        $end_date       = isset($test['end_date']) ?  $test['end_date']:'';

        $self_participations = "SELECT MAX(self_participation_id) AS self_participation_id
                                FROM self_participations
                                WHERE ".$zonalq.' '.$rsmresultq.' '.$asmresultq.' '.$rresultq.' '.$aresultq." 
                                GROUP BY ticket_id,user_id,user_type";

        $self_participationData = DB::select($self_participations);

        $self_participationresult = [];

        if (!empty($self_participationData)) {
            // Extract the MAX(self_participation_id) values directly
            $self_participationresult = array_map(function($item) {
                return $item->self_participation_id;
            }, $self_participationData);
        }

        // Implode the IDs into a single string
        $self_participation_ids = implode("','", $self_participationresult);

        /*$self_participationData = collect(DB::select($self_participations));

        $self_participationresult = array(); 
        if (isset($self_participationData) && !empty($self_participationData))  {
            $self_participationDatass = json_decode(json_encode($self_participationData),true);
            foreach ($self_participationDatass as $key => $value) { 
                if (is_array($value)) { 
                    $self_participationresult = array_merge($self_participationresult, array_flatten($value)); 
                } else  {
                    $self_participationresult[$key] = $value;
                }
            }
        }
        $self_participation_ids = implode("','", $self_participationresult);*/

        // echo "<pre>"; print_r($self_participationData); exit();
        // echo $self_participation_ids; die;
        // echo "<pre>"; print_r($self_participationData); exit();
        $search_query=" (1=1) ";

        if($start_date!='' && $end_date!='' && $start_date != $end_date) {
            if($self_participation_ids != '' && !empty($self_participation_ids)){
                $search_query.= " and DATE(t.created_at) >= DATE('".$start_date."') AND DATE(t.created_at) <= DATE('".$end_date."') and self_participation_id IN ('".$self_participation_ids."')";
            }
            else{
                $search_query.= " and DATE(t.created_at) >= DATE('".$start_date."') AND DATE(t.created_at) <= DATE('".$end_date."')";
            }
            
        }elseif($start_date!='' && $end_date!='' && $start_date == $end_date) {
            if($self_participation_ids != '' && !empty($self_participation_ids)){
                $search_query.= " and DATE(t.created_at) = DATE('".$start_date."') and self_participation_id IN ('".$self_participation_ids."')";
            }
            else{
                $search_query.= " and DATE(t.created_at) = DATE('".$start_date."')";
            }
        }
        else if($start_date == ''){
            $search_query.= " and self_participation_id IN ('".$self_participation_ids."')";
        }
        
        if(isset($search_array['value']) && $search_array['value']!=''){
            $search = $search_array['value']; 

            $search_query.=" and (zm.ECN                LIKE '%".$search."%'    OR
                    rm.ECN                              LIKE '%".$search."%'    OR
                    am.ECN                              LIKE '%".$search."%'    OR
                    sam.ECN                             LIKE '%".$search."%'    OR
                    srm.ECN                             LIKE '%".$search."%'    OR
                    u.ECN                               LIKE '%".$search."%'    OR
                    ms.ECN                              LIKE '%".$search."%'    OR
                    zm.zonal_manager_name               LIKE '%".$search."%'    OR
                    rm.regional_manager_name            LIKE '%".$search."%'    OR
                    am.area_manager_name                LIKE '%".$search."%'    OR
                    sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                    srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                    ms.marketing_supervisor_name        LIKE '%".$search."%'    OR
                    u.name                              LIKE '%".$search."%'    OR
                    sp.description                      LIKE '%".$search."%'   OR
                    t.ticket_no                         LIKE '%".$search."%'
                ) ";
        }
        
        //$base_url   =   url('/');                     
        $base_url   =   'https://dhanuka-app.s3.ap-south-1.amazonaws.com';                     
        $query      = " 
                    SELECT                                 
                        @a:=@a+1  as serial_number,
                        A.*                                
                    FROM
                        (
                        SELECT     
                            t.ticket_no,
                            t.village_id,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(zm.ECN, ''),
                                        NULLIF(rm.ECN, ''),
                                        NULLIF(am.ECN, ''),
                                        NULLIF(srm.ECN, ''),
                                        NULLIF(sam.ECN, ''),
                                        NULLIF(u.ECN, ''),
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
                                        NULLIF(ms.marketing_supervisor_name, '')
                                    ) AS name,
                            UPPER(sp.user_type) AS role,
                            sp.arival_time,
                            sp.description,
                            CONCAT('".$base_url."/', sp.uploaded_file),
                            sp.latitude,
                            sp.longitude,
                            sp.departure_time,
                            sp.departure_description,
                            CONCAT('".$base_url."/', sp.departure_uploaded_file),
                            sp.departure_latitude,
                            sp.departure_longitude,
                            sp.duration,
                            DATE_FORMAT(t.created_at, '%Y-%m-%d') as self_date,
                            DATE_FORMAT(sp.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                            '".$base_url."/' as base_url
                        FROM        self_participations     sp
                        LEFT JOIN   tickets                 t ON t.ticket_id=sp.ticket_id                            
                        LEFT JOIN   users                   u   ON sp.user_id=u.id AND sp.user_type='bdo'
                        LEFT JOIN   zonal_managers          zm  ON sp.user_id=zm.zonal_manager_id AND sp.user_type='zonal'
                        LEFT JOIN   regional_managers       rm  ON sp.user_id=rm.regional_manager_id AND sp.user_type='regional'
                        LEFT JOIN   area_managers           am  ON sp.user_id=am.area_manager_id AND sp.user_type='area'
                        LEFT JOIN   sales_regional_managers srm ON sp.user_id=srm.sales_regional_manager_id AND sp.user_type='sales_regional'
                        LEFT JOIN   sales_area_managers     sam ON sp.user_id=sam.sales_area_manager_id AND sp.user_type='sales_area'
                        LEFT JOIN   marketing_supervisor    ms   ON sp.user_id=ms.marketing_supervisor_id AND sp.user_type='marketing_supervisor'
                        WHERE ".$search_query." 
                    )   A,
                    (select @a:=0) initvars";
        // return $query;
        $participants   = collect(DB::select($query));
        return $participants;
        // echo "<pre>";
        // print_r($participants);
        // exit();
        foreach ($participants as $key => $value) {
            if($participants[$key]->role == 'SALES_AREA'){ 
                $sales_area_managers = DB::table('sales_area_managers')
                                       ->where('ECN',$participants[$key]->ecn)
                                       ->get();
                $zonal_managers      = DB::table('zonal_managers')
                                       ->where('zonal_manager_id',$sales_area_managers[0]->zonal_manager_id)
                                       ->get();
                $regional_managers   = DB::table('regional_managers')
                                       ->where('regional_manager_id',$sales_area_managers[0]->regional_manager_id)
                                       ->get();
                
                $sales_regional_managers = DB::table('sales_regional_managers')
                                           ->where('sales_regional_manager_id',$sales_area_managers[0]->sales_regional_manager_id)
                                           ->get();
                $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                $participants[$key]->area_ecn =  "";
                $participants[$key]->area_name =  ""; 
                $participants[$key]->sales_area_ecn =  $sales_area_managers[0]->ECN;
                $participants[$key]->sales_area_name =  $sales_area_managers[0]->sales_area_manager_name;                          
            } else if($participants[$key]->role == 'AREA'){
                $area_managers       = DB::table('area_managers')
                                       ->where('ECN',$participants[$key]->ecn)
                                       ->get();

                $zonal_managers      = DB::table('zonal_managers')
                                       ->where('zonal_manager_id',$area_managers[0]->zonal_manager_id)
                                       ->get();
                $regional_managers   = DB::table('regional_managers')
                                       ->where('regional_manager_id',$area_managers[0]->regional_manager_id)
                                       ->get();
                
                $sales_regional_managers = DB::table('sales_regional_managers')
                                           ->where('sales_regional_manager_id',$area_managers[0]->sales_regional_manager_id)
                                           ->get(); 

                $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                $participants[$key]->area_ecn =  $area_managers[0]->ECN;
                $participants[$key]->area_name =  $area_managers[0]->area_manager_name; 
                $participants[$key]->sales_area_ecn =  "";
                $participants[$key]->sales_area_name =  "";                      
            }
            else if($participants[$key]->role == 'SALES_REGIONAL'){
                $sales_regional_managers = DB::table('sales_regional_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get(); 
                $zonal_managers      = DB::table('zonal_managers')
                                       ->where('zonal_manager_id',$sales_regional_managers[0]->zonal_manager_id)
                                       ->get();
                $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                $participants[$key]->regional_ecn =  "";
                $participants[$key]->regional_name =  "";  
                $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                $participants[$key]->area_ecn =  "";
                $participants[$key]->area_name =  ""; 
                $participants[$key]->sales_regional_ecn =  "";
                $participants[$key]->sales_regional_name =  "";
            }
            else if ($participants[$key]->role == 'REGIONAL'){
                $regional_managers   = DB::table('regional_managers')
                                       ->where('ECN',$participants[$key]->ecn)
                                       ->get();

                $zonal_managers      = DB::table('zonal_managers')
                                       ->where('zonal_manager_id',$regional_managers[0]->zonal_manager_id)
                                       ->get();

                $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                $participants[$key]->sales_regional_ecn =  "";
                $participants[$key]->sales_regional_name =  "";
                $participants[$key]->area_ecn =  "";
                $participants[$key]->area_name =  ""; 
                $participants[$key]->sales_area_ecn =  "";
                $participants[$key]->sales_area_name =  ""; 
            }
            else if ($participants[$key]->role == 'ZONAL'){
                $zonal_managers      = DB::table('zonal_managers')
                                       ->where('ECN',$participants[$key]->ecn)
                                       ->get();
                $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                $participants[$key]->regional_ecn =  "";
                $participants[$key]->regional_name =  "";  
                $participants[$key]->sales_regional_ecn =  "";
                $participants[$key]->sales_regional_name =  "";
                $participants[$key]->area_ecn =  "";
                $participants[$key]->area_name =  ""; 
                $participants[$key]->sales_area_ecn =  "";
                $participants[$key]->sales_area_name =  ""; 
            }
            else{
                $participants[$key]->zonal_ecn =  "";
                $participants[$key]->zonal_name =  "";
                $participants[$key]->regional_ecn =  "";
                $participants[$key]->regional_name =  "";  
                $participants[$key]->sales_regional_ecn =  "";
                $participants[$key]->sales_regional_name =  "";
                $participants[$key]->area_ecn =  "";
                $participants[$key]->area_name =  ""; 
                $participants[$key]->sales_area_ecn =  "";
                $participants[$key]->sales_area_name =  ""; 
            }
            $village_id = $value->village_id;
            if($village_id) {
                $lat = $value->latitude;
                $lon = $value->longitude;
                if($lat && $lon) {
                    $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                    if($distanceData !==0) {
                        $expDistanceData = explode("~", $distanceData);
                        $participants[$key]->isValidate = $expDistanceData[1];
                        $participants[$key]->distance = $expDistanceData[0];
                    } else {
                        $participants[$key]->isValidate = "No";
                        $participants[$key]->distance = "0";
                    }
                } else {
                    $participants[$key]->isValidate = "No";
                    $participants[$key]->distance = "0";
                }
            } else {
                $participants[$key]->isValidate = "No";
                $participants[$key]->distance = "0";
            }
        }
        return Datatables::of($participants)->make();            
    }

    public function postAllquerylist(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }else{
            $userData = \DB::table('branch_admin')->where(['id' => $sess])->first();
            if($userData) {
                $zonal_manager_id = $userData->zonal_manager_id;
                if($zonal_manager_id != '' && !empty($zonal_manager_id)){
                    $zonalq = " user_id = '".$zonal_manager_id."' and user_type = 'zonal'";
                } else{
                    $zonalq = "";
                }
                //rsm
                $rsmData = DB::table('sales_regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('sales_regional_manager_id')
                          ->get();
                $rsmresult = array(); 
                if (isset($rsmData) && !empty($rsmData)) {
                    $rsmDatass = json_decode(json_encode($rsmData),true);
                    foreach ($rsmDatass as $key => $value) {
                        if (is_array($value)) {
                            $rsmresult = array_merge($rsmresult, array_flatten($value));
                        } else {
                            $rsmresult[$key] = $value;
                        }
                    }
                    $rsmresult1 = implode("','", $rsmresult);
                    if($rsmresult1 != '' && !empty($rsmresult1)){
                        $rsmresultq = " or user_id in ('".$rsmresult1."') and user_type = 'sales_regional'";
                    } else {
                        $rsmresultq = "";
                    }
                }
                //asm
                $asmData = DB::table('sales_area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('sales_area_manager_id')
                          ->get();
                $asmresult = array();
                if (isset($asmData) && !empty($asmData)) {
                    $asmDatass = json_decode(json_encode($asmData),true);
                    foreach ($asmDatass as $key => $value) {
                        if (is_array($value)) {
                            $asmresult = array_merge($asmresult, array_flatten($value));
                        } else {
                            $asmresult[$key] = $value;
                        }
                    }
                    $asmresult1 = implode("','", $asmresult);
                    if($asmresult1 != '' && !empty($asmresult1)){
                        $asmresultq = " or user_id in ('".$asmresult1."') and user_type = 'sales_area'";
                    } else{
                        $asmresultq = "";
                    }
                }
                //regional
                $rData = DB::table('regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('regional_manager_id')
                          ->get();
                $rresult = array();
                if (isset($rData) && !empty($rData)) {
                    $rDatass = json_decode(json_encode($rData),true);
                    foreach ($rDatass as $key => $value) {
                        if (is_array($value)) {
                            $rresult = array_merge($rresult, array_flatten($value));
                        } else {
                            $rresult[$key] = $value;
                        }
                    }
                    $rresult1 = implode("','", $rresult);
                    if($rresult1 != '' && !empty($rresult1)) {
                        $rresultq = " or user_id in ('".$rresult1."') and user_type = 'regional' ";
                    } else {
                        $rresultq = "";
                    }
                }
                //area
                $aData = DB::table('area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('area_manager_id')
                          ->get();
                $aresult = array();
                if (isset($aData) && !empty($aData)) {
                    $aDatass = json_decode(json_encode($aData),true);
                    foreach ($aDatass as $key => $value) {
                        if (is_array($value)) {
                            $aresult = array_merge($aresult, array_flatten($value));
                        } else {
                            $aresult[$key] = $value; 
                        } 
                    } 
                    $aresult1 = implode("','", $aresult);
                    if($aresult1 != '' && !empty($aresult1)){
                        $aresultq = " or user_id in ('".$aresult1."') and user_type = 'area' ";
                    }
                    else{
                        $aresultq = "";
                    }
                }
            } else{
                $zonalq     = " (1=1) ";
                $rsmresultq = "";
                $asmresultq = "";
                $rresultq   = "";
                $aresultq   = "";
            }
        }
        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        $test = $request::all();
        $start_date     = isset($test['start_date']) ? $test['start_date'] :'';
        $end_date       = isset($test['end_date']) ?  $test['end_date']:'';

        $self_participations = "SELECT MAX(self_participation_id) AS self_participation_id FROM self_participations WHERE ".$zonalq.' '.$rsmresultq.' '.$asmresultq.' '.$rresultq.' '.$aresultq." GROUP BY ticket_id,user_id,user_type";
        $self_participationData = DB::select($self_participations);
        $self_participationresult = [];
        if (!empty($self_participationData)) {
            $self_participationresult = array_map(function($item) {
                return $item->self_participation_id;
            }, $self_participationData);
        }
        $self_participation_ids = implode("','", $self_participationresult);

        $search_query=" (1=1) ";
        if($start_date!='' && $end_date!='' && $start_date != $end_date) {
            if($self_participation_ids != '' && !empty($self_participation_ids)){
                $search_query.= " and DATE(t.created_at) >= DATE('".$start_date."') AND DATE(t.created_at) <= DATE('".$end_date."') and self_participation_id IN ('".$self_participation_ids."')";
            } else {
                $search_query.= " and DATE(t.created_at) >= DATE('".$start_date."') AND DATE(t.created_at) <= DATE('".$end_date."')";
            }
        } elseif($start_date!='' && $end_date!='' && $start_date == $end_date) {
            if($self_participation_ids != '' && !empty($self_participation_ids)) {
                $search_query.= " and DATE(t.created_at) = DATE('".$start_date."') and self_participation_id IN ('".$self_participation_ids."')";
            } else {
                $search_query.= " and DATE(t.created_at) = DATE('".$start_date."')";
            }
        } else if($start_date == '') {
            $search_query.= " and self_participation_id IN ('".$self_participation_ids."')";
        }
        if(isset($search_array['value']) && $search_array['value']!='') {
            $search = $search_array['value']; 
            $search_query.=" and (zm.ECN                LIKE '%".$search."%'    OR
                    rm.ECN                              LIKE '%".$search."%'    OR
                    am.ECN                              LIKE '%".$search."%'    OR
                    sam.ECN                             LIKE '%".$search."%'    OR
                    srm.ECN                             LIKE '%".$search."%'    OR
                    u.ECN                               LIKE '%".$search."%'    OR
                    ms.ECN                              LIKE '%".$search."%'    OR
                    zm.zonal_manager_name               LIKE '%".$search."%'    OR
                    rm.regional_manager_name            LIKE '%".$search."%'    OR
                    am.area_manager_name                LIKE '%".$search."%'    OR
                    sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                    srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                    ms.marketing_supervisor_name        LIKE '%".$search."%'    OR
                    u.name                              LIKE '%".$search."%'    OR
                    sp.description                      LIKE '%".$search."%'   OR
                    t.ticket_no                         LIKE '%".$search."%'
                ) ";
        }
        //$base_url   =   url('/');                     
        $base_url   =   'https://dhanuka-app.s3.ap-south-1.amazonaws.com';                     
        $query = " 
            SELECT                                 
                @a:=@a+1 AS serial_number,
                A.*                                
            FROM (
                SELECT     
                    t.ticket_no,
                    t.village_id,
                    CONCAT_WS(
                        '\n', 
                        NULLIF(zm.ECN, ''),
                        NULLIF(rm.ECN, ''),
                        NULLIF(am.ECN, ''),
                        NULLIF(srm.ECN, ''),
                        NULLIF(sam.ECN, ''),
                        NULLIF(u.ECN, ''),
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
                        NULLIF(ms.marketing_supervisor_name, '')
                    ) AS name,
                    UPPER(sp.user_type) AS role,
                    sp.arival_time,
                    sp.description,
                    CONCAT('$base_url/', sp.uploaded_file) AS uploaded_file,
                    sp.latitude,
                    sp.longitude,
                    sp.departure_time,
                    sp.departure_description,
                    CONCAT('$base_url/', sp.departure_uploaded_file) AS departure_uploaded_file,
                    sp.departure_latitude,
                    sp.departure_longitude,
                    sp.duration,
                    DATE_FORMAT(t.created_at, '%Y-%m-%d') AS self_date,
                    DATE_FORMAT(sp.created_at, '%Y-%m-%d, %h:%i %p') AS created_at,
                    '$base_url/' AS base_url
                FROM self_participations sp
                LEFT JOIN tickets t ON t.ticket_id = sp.ticket_id                            
                LEFT JOIN users u ON sp.user_id = u.id AND sp.user_type = 'bdo'
                LEFT JOIN zonal_managers zm ON sp.user_id = zm.zonal_manager_id AND sp.user_type = 'zonal'
                LEFT JOIN regional_managers rm ON sp.user_id = rm.regional_manager_id AND sp.user_type = 'regional'
                LEFT JOIN area_managers am ON sp.user_id = am.area_manager_id AND sp.user_type = 'area'
                LEFT JOIN sales_regional_managers srm ON sp.user_id = srm.sales_regional_manager_id AND sp.user_type = 'sales_regional'
                LEFT JOIN sales_area_managers sam ON sp.user_id = sam.sales_area_manager_id AND sp.user_type = 'sales_area'
                LEFT JOIN marketing_supervisor ms ON sp.user_id = ms.marketing_supervisor_id AND sp.user_type = 'marketing_supervisor'
                WHERE $search_query
            ) A,
            (SELECT @a := 0) initvars";

        $participants = collect(DB::select($query));

        // Bulk query to fetch related data
        /*$sales_area_managers    = DB::table('sales_area_managers')->get()->keyBy('ECN');
        $zonal_managers         = DB::table('zonal_managers')->get()->keyBy('zonal_manager_id');
        $regional_managers      = DB::table('regional_managers')->get()->keyBy('regional_manager_id');
        $sales_regional_managers= DB::table('sales_regional_managers')->get()->keyBy('sales_regional_manager_id');*/

        /*$area_managers          = DB::table('area_managers')->get()->keyBy('ECN');
        $sales_area_managers    = DB::table('sales_area_managers')->get()->keyBy('ECN');
        $zonal_managers         = DB::table('zonal_managers')->get()->keyBy('zonal_manager_id');
        $regional_managers      = DB::table('regional_managers')->get()->keyBy('regional_manager_id');
        $sales_regional_managers= DB::table('sales_regional_managers')->get()->keyBy('sales_regional_manager_id');*/

        // Loop through participants to populate additional data
        foreach ($participants as $key => $participant) {
            $ecn = $participant->ecn;

            switch ($participant->role) {
                case 'SALES_AREA':
                    $sales_area_managers = DB::table('sales_area_managers')->where('ECN',$participants[$key]->ecn)->get();
                    $zonal_managers      = DB::table('zonal_managers')->where('zonal_manager_id',$sales_area_managers[0]->zonal_manager_id)->get();
                    $regional_managers   = DB::table('regional_managers')->where('regional_manager_id',$sales_area_managers[0]->regional_manager_id)->get();
                    $sales_regional_managers = DB::table('sales_regional_managers')->where('sales_regional_manager_id',$sales_area_managers[0]->sales_regional_manager_id)->get();
                    $participants[$key]->zonal_ecn      = $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name     = $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn   = $regional_managers[0]->ECN;
                    $participants[$key]->regional_name  = $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn = $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name= $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn       = "";
                    $participants[$key]->area_name      = ""; 
                    $participants[$key]->sales_area_ecn = $sales_area_managers[0]->ECN;
                    $participants[$key]->sales_area_name= $sales_area_managers[0]->sales_area_manager_name; 
                    break;
                case 'AREA':
                    $area_managers      = DB::table('area_managers')->where('ECN',$participants[$key]->ecn)->get();
                    $zonal_managers     = DB::table('zonal_managers')->where('zonal_manager_id',$area_managers[0]->zonal_manager_id)->get();
                    $regional_managers  = DB::table('regional_managers')->where('regional_manager_id',$area_managers[0]->regional_manager_id)->get();
                    $sales_regional_managers = DB::table('sales_regional_managers')->where('sales_regional_manager_id',$area_managers[0]->sales_regional_manager_id)->get(); 
                    $participants[$key]->zonal_ecn      = $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name     = $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn   = $regional_managers[0]->ECN;
                    $participants[$key]->regional_name  = $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn  = $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name = $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn       = $area_managers[0]->ECN;
                    $participants[$key]->area_name      = $area_managers[0]->area_manager_name; 
                    $participants[$key]->sales_area_ecn = "";
                    $participants[$key]->sales_area_name= "";
                    break;

                case 'SALES_REGIONAL':
                    $sales_regional_managers = DB::table('sales_regional_managers')->where('ECN',$participants[$key]->ecn)->get();
                    $zonal_managers          = DB::table('zonal_managers')->where('zonal_manager_id',$sales_regional_managers[0]->zonal_manager_id)->get();
                    $participants[$key]->zonal_ecn      = $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name     = $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn   = "";
                    $participants[$key]->regional_name  = "";  
                    $participants[$key]->sales_regional_ecn = $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name= $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn           = "";
                    $participants[$key]->area_name          = ""; 
                    $participants[$key]->sales_regional_ecn = "";
                    $participants[$key]->sales_regional_name= "";
                    break;
                case 'REGIONAL':
                    $regional_managers  = DB::table('regional_managers')->where('ECN',$participants[$key]->ecn)->get();
                    $zonal_managers     = DB::table('zonal_managers')->where('zonal_manager_id',$regional_managers[0]->zonal_manager_id)->get();
                    $participants[$key]->zonal_ecn      = $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name     = $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn   = $regional_managers[0]->ECN;
                    $participants[$key]->regional_name  = $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name=  "";
                    $participants[$key]->area_ecn       = "";
                    $participants[$key]->area_name      = ""; 
                    $participants[$key]->sales_area_ecn = "";
                    $participants[$key]->sales_area_name= "";
                    break;
                case 'ZONAL':
                    $zonal_managers = DB::table('zonal_managers')->where('ECN',$participants[$key]->ecn)->get();
                    $participants[$key]->zonal_ecn      = $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name     = $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn   = "";
                    $participants[$key]->regional_name  = "";  
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name=  "";
                    $participants[$key]->area_ecn       = "";
                    $participants[$key]->area_name      = ""; 
                    $participants[$key]->sales_area_ecn = "";
                    $participants[$key]->sales_area_name= "";
                    break;
                default:
                    $participant->zonal_ecn         = "";
                    $participant->zonal_name        = "";
                    $participant->regional_ecn      = "";
                    $participant->regional_name     = "";
                    $participant->sales_regional_ecn  = "";
                    $participant->sales_regional_name = "";
                    $participant->area_ecn          = "";
                    $participant->area_name         = "";
                    $participant->sales_area_ecn    = "";
                    $participant->sales_area_name   = "";
                    break;
            }
            $village_id = $participant->village_id;
            if($village_id) {
                $lat = $participant->latitude;
                $lon = $participant->longitude;
                if($lat && $lon) {
                    $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                    if($distanceData !==0) {
                        $expDistanceData = explode("~", $distanceData);
                        $participants[$key]->isValidate = $expDistanceData[1];
                        $participants[$key]->distance = $expDistanceData[0];
                    } else {
                        $participants[$key]->isValidate = "No";
                        $participants[$key]->distance = "0";
                    }
                } else {
                    $participants[$key]->isValidate = "No";
                    $participants[$key]->distance = "0";
                }
            } else {
                $participants[$key]->isValidate = "No";
                $participants[$key]->distance = "0";
            }
        }
        return Datatables::of($participants)->make();
    }

    public function CalculateDistance($village_id,$lat2, $lon2) {
        if($village_id >0 ) {
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
        } else {
            return 0;
        }
    }

    public function postExportAllParticipants(Request $request) {          
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        $base_url   =   url('/');
        
            if($userData && count($userData)>0) {
                $zonal_manager_id = $userData->zonal_manager_id;
                if($zonal_manager_id != '' && !empty($zonal_manager_id)){
                    $zonalq = " user_id = '".$zonal_manager_id."' and user_type = 'zonal'";
                }
                else{
                    $zonalq = "";
                }
                //rsm
                $rsmData = DB::table('sales_regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
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
                    $rsmresult1 = implode("','", $rsmresult);
                    if($rsmresult1 != '' && !empty($rsmresult1)){
                        $rsmresultq = " or user_id in ('".$rsmresult1."') and user_type = 'sales_regional'";
                    }
                    else{
                        $rsmresultq = "";
                    }
                }

                //asm
                $asmData = DB::table('sales_area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
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
                    $asmresult1 = implode("','", $asmresult);
                    if($asmresult1 != '' && !empty($asmresult1)){
                        $asmresultq = " or user_id in ('".$asmresult1."') and user_type = 'sales_area'";
                    }
                    else{
                        $asmresultq = "";
                    }
                }

                //regional
                $rData = DB::table('regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('regional_manager_id')
                          ->get();
                $rresult = array(); 
                if (isset($rData) && !empty($rData)) 
                {
                    $rDatass = json_decode(json_encode($rData),true);
                    foreach ($rDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $rresult = array_merge($rresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                            $rresult[$key] = $value; 
                        } 
                    } 
                    $rresult1 = implode("','", $rresult);
                    if($rresult1 != '' && !empty($rresult1)){
                        $rresultq = " or user_id in ('".$rresult1."') and user_type = 'regional' ";
                    }
                    else{
                        $rresultq = "";
                    }
                }

                //area
                $aData = DB::table('area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('area_manager_id')
                          ->get();
                $aresult = array(); 
                if (isset($aData) && !empty($aData)) 
                {
                    $aDatass = json_decode(json_encode($aData),true);
                    foreach ($aDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $aresult = array_merge($aresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                            $aresult[$key] = $value; 
                        } 
                    } 
                    $aresult1 = implode("','", $aresult);
                    if($aresult1 != '' && !empty($aresult1)){
                        $aresultq = " or user_id in ('".$aresult1."') and user_type = 'area' ";
                    }
                    else{
                        $aresultq = "";
                    }
                }
            }
            else{
                $zonalq     = " (1=1) ";
                $rsmresultq = "";
                $asmresultq = "";
                $rresultq   = "";
                $aresultq   = "";
            }
            $my_data    =   Input::all(); 
            $search     =   $my_data['search'];
            $dataSearch =   '';

            $self_participations = "SELECT MAX(self_participation_id)
                                    FROM self_participations
                                    WHERE ".$zonalq.' '.$rsmresultq.' '.$asmresultq.' '.$rresultq.' '.$aresultq."
                                    GROUP BY ticket_id,user_id,user_type";
            $self_participationData = collect(DB::select($self_participations));
            $self_participationresult = array(); 
            if (isset($self_participationData) && !empty($self_participationData)) 
            {
                $self_participationDatass = json_decode(json_encode($self_participationData),true);
                
                foreach ($self_participationDatass as $key => $value) { 
                    if (is_array($value)) 
                    { 
                        $self_participationresult = array_merge($self_participationresult, array_flatten($value)); 
                    } 
                    else 
                    { 
                        $self_participationresult[$key] = $value; 
                    } 
                } 
            }
            $self_participation_ids = implode("','", $self_participationresult);

            $test = $request::all();
            $start_date     = isset($test['start_date']) ? $test['start_date'] :'';
            $end_date       = isset($test['end_date']) ?  $test['end_date']:'';

            $search_query=" (1=1) ";

            if($start_date!='' && $end_date!='' && $start_date != $end_date) {
                if($self_participation_ids != '' && !empty($self_participation_ids)){
                    $search_query.= " and DATE(t.created_at) >= DATE('".$start_date."') AND DATE(t.created_at) <= DATE('".$end_date."') and self_participation_id IN ('".$self_participation_ids."')";
                }
                else{
                    $search_query.= " and DATE(t.created_at) >= DATE('".$start_date."') AND DATE(t.created_at) <= DATE('".$end_date."')";
                }
                
            }elseif($start_date!='' && $end_date!='' && $start_date == $end_date) {
                if($self_participation_ids != '' && !empty($self_participation_ids)){
                    $search_query.= " and DATE(sp.created_at) = DATE('".$start_date."') and self_participation_id IN ('".$self_participation_ids."')";
                }
                else{
                    $search_query.= " and DATE(t.created_at) = DATE('".$start_date."')";
                }
            }
            else if($start_date == ''){
                $search_query.= " and self_participation_id IN ('".$self_participation_ids."')";
            }


            if($search!='') {
                $search_query .=   "
                              
                                AND
                                (
                                    u.ECN           LIKE '%".$search."%'    OR
                                    am.ECN          LIKE '%".$search."%'    OR
                                    rm.ECN          LIKE '%".$search."%'    OR
                                    sam.ECN         LIKE '%".$search."%'    OR
                                    srm.ECN         LIKE '%".$search."%'    OR
                                    zm.ECN          LIKE '%".$search."%'    OR
                                    ms.ECN          LIKE '%".$search."%'    OR
                                    t.ticket_no     LIKE '%".$search."%'    OR
                                    u.name          LIKE '%".$search."%'    OR
                                    am.name         LIKE '%".$search."%'    OR
                                    rm.name         LIKE '%".$search."%'    OR
                                    sam.name        LIKE '%".$search."%'    OR
                                    srm.name        LIKE '%".$search."%'    OR
                                    zm.name         LIKE '%".$search."%'    OR
                                    ms.name         LIKE '%".$search."%'    OR

                                    sp.description  LIKE '%".$search."%'
                                )             
                                ";
            }

            
            
            $query      =   " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    t.ticket_no,
                                    CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.ECN, ''),
                                                NULLIF(rm.ECN, ''),
                                                NULLIF(am.ECN, ''),
                                                NULLIF(srm.ECN, ''),
                                                NULLIF(sam.ECN, ''),
                                                NULLIF(u.ECN, ''),
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
                                                NULLIF(ms.marketing_supervisor_name, '')
                                            ) AS name,
                                    UPPER(sp.user_type) AS role,
                                    sp.arival_time,
                                    sp.description,
                                    CONCAT('".$base_url."/', sp.uploaded_file),
                                    sp.latitude,
                                    sp.longitude,
                                    sp.departure_time,
                                    sp.departure_description,
                                    CONCAT('".$base_url."/', sp.departure_uploaded_file),
                                    sp.departure_latitude,
                                    sp.departure_longitude,
                                    sp.duration,
                                    DATE_FORMAT(t.created_at, '%Y-%m-%d') as self_date,
                                    DATE_FORMAT(sp.created_at, '%Y-%m-%d, %h:%i %p') as created_at
                                FROM        self_participations     sp
                                LEFT JOIN   tickets                 t ON t.ticket_id=sp.ticket_id                            
                                LEFT JOIN   users                   u   ON sp.user_id=u.id AND sp.user_type='bdo'
                                LEFT JOIN   zonal_managers          zm  ON sp.user_id=zm.zonal_manager_id AND sp.user_type='zonal'
                                LEFT JOIN   regional_managers       rm  ON sp.user_id=rm.regional_manager_id AND sp.user_type='regional'
                                LEFT JOIN   area_managers           am  ON sp.user_id=am.area_manager_id AND sp.user_type='area'
                                LEFT JOIN   sales_regional_managers srm ON sp.user_id=srm.sales_regional_manager_id AND sp.user_type='sales_regional'
                                LEFT JOIN   sales_area_managers     sam ON sp.user_id=sam.sales_area_manager_id AND sp.user_type='sales_area'
                                LEFT JOIN   marketing_supervisor    ms   ON sp.user_id=ms.marketing_supervisor_id AND sp.user_type='marketing_supervisor'
                                WHERE ".$search_query."
                               
                                )   A,
                                (select @a:=0) initvars";
            //echo $query; exit;
        
            $participants = DB::select(DB::raw($query));
            foreach ($participants as $key => $value) { 
                if($participants[$key]->role == 'SALES_AREA'){ 
                    $sales_area_managers = DB::table('sales_area_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();
                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$sales_area_managers[0]->zonal_manager_id)
                                           ->get();
                    $regional_managers   = DB::table('regional_managers')
                                           ->where('regional_manager_id',$sales_area_managers[0]->regional_manager_id)
                                           ->get();
                    
                    $sales_regional_managers = DB::table('sales_regional_managers')
                                               ->where('sales_regional_manager_id',$sales_area_managers[0]->sales_regional_manager_id)
                                               ->get();
                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                    $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  $sales_area_managers[0]->ECN;
                    $participants[$key]->sales_area_name =  $sales_area_managers[0]->sales_area_manager_name;                          
                }
                else if($participants[$key]->role == 'AREA'){
                    $area_managers       = DB::table('area_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();

                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$area_managers[0]->zonal_manager_id)
                                           ->get();
                    $regional_managers   = DB::table('regional_managers')
                                           ->where('regional_manager_id',$area_managers[0]->regional_manager_id)
                                           ->get();
                    
                    $sales_regional_managers = DB::table('sales_regional_managers')
                                               ->where('sales_regional_manager_id',$area_managers[0]->sales_regional_manager_id)
                                               ->get(); 

                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                    $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn =  $area_managers[0]->ECN;
                    $participants[$key]->area_name =  $area_managers[0]->area_manager_name; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  "";                      
                }
                else if($participants[$key]->role == 'SALES_REGIONAL'){
                    $sales_regional_managers = DB::table('sales_regional_managers')
                                               ->where('ECN',$participants[$key]->ecn)
                                               ->get(); 
                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$sales_regional_managers[0]->zonal_manager_id)
                                           ->get();
                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  "";
                    $participants[$key]->regional_name =  "";  
                    $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                }
                else if ($participants[$key]->role == 'REGIONAL'){
                    $regional_managers   = DB::table('regional_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();

                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$regional_managers[0]->zonal_manager_id)
                                           ->get();

                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                    $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  ""; 
                }
                else if ($participants[$key]->role == 'ZONAL'){
                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();
                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  "";
                    $participants[$key]->regional_name =  "";  
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  ""; 
                }
                else{
                    $participants[$key]->zonal_ecn =  "";
                    $participants[$key]->zonal_name =  "";
                    $participants[$key]->regional_ecn =  "";
                    $participants[$key]->regional_name =  "";  
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  ""; 
                }
            }
            $result         = array_map(function ($value) {
                                return (array)$value;
                            }, $participants);
            $ticket_column  = array(                
                                    'Id',
                                    'Ticket Number',
                                    'ECN',
                                    'Username',
                                    'User Role',
                                    'Arival DateTime',
                                    'Arival Description',
                                    'Arival Image URL',
                                    'Arival Latitude',
                                    'Arival Longitude',
                                    'Departure DateTime',
                                    'Departure Description',
                                    'Departure Image URL',
                                    'Departure Latitude',
                                    'Departure Longitude',
                                    'Activity Duration',
                                    'Ticket Date',
                                    'Created At',
                                    'Zonal ECN',
                                    'Zonal Manager',
                                    'Regional ECN',
                                    'Regional Manager',
                                    'Sales Regional ECN',
                                    'Sales Regional Manager',
                                    'Area ECN',
                                    'Area Manager',
                                    'Sales Area ECN',
                                    'Sales Area Manager'
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
                                'name' => "Self Participation List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                            );
            return response()->json($response);
    } 
    
    public function getMyatpplan() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.selfpaticipation.myatpplan');
    }
    
    public function postAllmyatpplan(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }else{
            
            $userData = \DB::table('branch_admin')->where(['id' => $sess])->first();
            if(count($userData)>0) {
                $zonal_manager_id = $userData->zonal_manager_id;
                if($zonal_manager_id != '' && !empty($zonal_manager_id)){
                    $zonalq = " sp.user_id = '".$zonal_manager_id."' and sp.user_type = 'zonal'";
                }
                else{
                    $zonalq = "";
                }
                //rsm
                $rsmData = DB::table('sales_regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
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
                    $rsmresult1 = implode("','", $rsmresult);
                    if($rsmresult1 != '' && !empty($rsmresult1)){
                        $rsmresultq = " or sp.user_id in ('".$rsmresult1."') and sp.user_type = 'sales_regional'";
                    }
                    else{
                        $rsmresultq = "";
                    }
                }

                //asm
                $asmData = DB::table('sales_area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
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
                    $asmresult1 = implode("','", $asmresult);
                    if($asmresult1 != '' && !empty($asmresult1)){
                        $asmresultq = " or sp.user_id in ('".$asmresult1."') and sp.user_type = 'sales_area'";
                    }
                    else{
                        $asmresultq = "";
                    }
                }

                //regional
                $rData = DB::table('regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('regional_manager_id')
                          ->get();
                $rresult = array(); 
                if (isset($rData) && !empty($rData)) 
                {
                    $rDatass = json_decode(json_encode($rData),true);
                    foreach ($rDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $rresult = array_merge($rresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                            $rresult[$key] = $value; 
                        } 
                    } 
                    $rresult1 = implode("','", $rresult);
                    if($rresult1 != '' && !empty($rresult1)){
                        $rresultq = " or sp.user_id in ('".$rresult1."') and sp.user_type = 'regional' ";
                    }
                    else{
                        $rresultq = "";
                    }
                }

                //area
                $aData = DB::table('area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('area_manager_id')
                          ->get();
                $aresult = array(); 
                if (isset($aData) && !empty($aData)) 
                {
                    $aDatass = json_decode(json_encode($aData),true);
                    foreach ($aDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $aresult = array_merge($aresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                            $aresult[$key] = $value; 
                        } 
                    } 
                    $aresult1 = implode("','", $aresult);
                    if($aresult1 != '' && !empty($aresult1)){
                        $aresultq = " or sp.user_id in ('".$aresult1."') and sp.user_type = 'area' ";
                    }
                    else{
                        $aresultq = "";
                    }
                }
            }
            else{
                $zonalq     = "";
                $rsmresultq = "";
                $asmresultq = "";
                $rresultq   = "";
                $aresultq   = "";
            }
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

        $search_query="";
        if($zonalq == ''){
            $search_query=" (1=1) ";
        }
        
        if($start_date!='' && $end_date!='' && $start_date != $end_date) {
            $search_query.= $zonalq." and DATE(sp.created_at) >= DATE('".$start_date."') AND DATE(sp.created_at) <= DATE('".$end_date."')".$rsmresultq." and DATE(sp.created_at) >= DATE('".$start_date."') AND DATE(sp.created_at) <= DATE('".$end_date."')".$asmresultq." and DATE(sp.created_at) >= DATE('".$start_date."') AND DATE(sp.created_at) <= DATE('".$end_date."')".$rresultq." and DATE(sp.created_at) >= DATE('".$start_date."') AND DATE(sp.created_at) <= DATE('".$end_date."')".$aresultq." and DATE(sp.created_at) >= DATE('".$start_date."') AND DATE(sp.created_at) <= DATE('".$end_date."')";
        }elseif($start_date!='' && $end_date!='' && $start_date == $end_date) {
            $search_query.= $zonalq." and DATE(sp.created_at) >= DATE('".$start_date."')".$rsmresultq." and DATE(sp.created_at) >= DATE('".$start_date."')".$asmresultq." and DATE(sp.created_at) >= DATE('".$start_date."')".$rresultq." and DATE(sp.created_at) >= DATE('".$start_date."')".$aresultq." and DATE(sp.created_at) >= DATE('".$start_date."')";
        }
        else if($start_date == ''){
            $search_query.= $zonalq." ".$rsmresultq." ".$asmresultq." ".$rresultq." ".$aresultq;
        }
        
        if(isset($search_array['value']) && $search_array['value']!=''){
                $search = $search_array['value']; 

                $search_query.=" and  (                                
                            zm.ECN                              LIKE '%".$search."%'    OR
                            rm.ECN                              LIKE '%".$search."%'    OR
                            am.ECN                              LIKE '%".$search."%'    OR
                            sam.ECN                             LIKE '%".$search."%'    OR
                            srm.ECN                             LIKE '%".$search."%'    OR
                            u.ECN                               LIKE '%".$search."%'    OR
                            ms.ECN                              LIKE '%".$search."%'    OR
                            zm.zonal_manager_name               LIKE '%".$search."%'    OR
                            rm.regional_manager_name            LIKE '%".$search."%'    OR
                            am.area_manager_name                LIKE '%".$search."%'    OR
                            sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                            srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                            ms.marketing_supervisor_name        LIKE '%".$search."%'    OR
                            u.name                              LIKE '%".$search."%'    OR
                            p.product                           LIKE '%".$search."%'    OR
                            c.crop                              LIKE '%".$search."%'    OR
                            s.state                             LIKE '%".$search."%'    OR
                            d.district                          LIKE '%".$search."%'    OR
                            t.tehsil                            LIKE '%".$search."%'    OR
                            ut.ticket_no                        LIKE '%".$search."%'
                            ) ";
        }
            $query= " 
                        SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*                                
                        FROM
                            (
                            SELECT
                                CONCAT_WS(
                                            '\n', 
                                            NULLIF(zm.ECN, ''),
                                            NULLIF(rm.ECN, ''),
                                            NULLIF(am.ECN, ''),
                                            NULLIF(srm.ECN, ''),
                                            NULLIF(sam.ECN, ''),
                                            NULLIF(u.ECN, ''),
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
                                            NULLIF(ms.marketing_supervisor_name, '')
                                        ) AS name,
                                UPPER(sp.user_type) AS role,
                                pa.plan_atp_id,
                                DATE_FORMAT(pa.plan_date, '%Y-%m-%d') as plan_date,
                                p.product,
                                c.crop,
                                ds.data_source,
                                s.state,
                                d.district,
                                t.tehsil,
                                pa.village,
                                pa.expected_farmer,
                                pa.expected_pda,
                                DATE_FORMAT(sp.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                ut.ticket_no
                            FROM        my_atp    sp
                            LEFT JOIN   plan_atp                    pa  ON sp.plan_atp_id=pa.plan_atp_id                            
                            LEFT JOIN   users                       u   ON sp.user_id=u.id AND sp.user_type='bdo'
                            LEFT JOIN   zonal_managers              zm  ON sp.user_id=zm.zonal_manager_id AND sp.user_type='zonal'
                            LEFT JOIN   regional_managers           rm  ON sp.user_id=rm.regional_manager_id AND sp.user_type='regional'
                            LEFT JOIN   area_managers               am  ON sp.user_id=am.area_manager_id AND sp.user_type='area'
                            LEFT JOIN   sales_regional_managers     srm ON sp.user_id=srm.sales_regional_manager_id AND sp.user_type='sales_regional'
                            LEFT JOIN   sales_area_managers         sam ON sp.user_id=sam.sales_area_manager_id AND sp.user_type='sales_area'
                            LEFT JOIN   marketing_supervisor    ms   ON sp.user_id=ms.marketing_supervisor_id AND sp.user_type='marketing_supervisor'

                            LEFT JOIN   products                    p   ON pa.product_id=p.product_id
                            LEFT JOIN   crops                       c   ON pa.crop_id=c.crop_id
                            LEFT JOIN   data_sources                ds  ON pa.data_source_id=ds.data_source_id
                            LEFT JOIN   states                      s   ON pa.state_id=s.state_id
                            LEFT JOIN   districts                   d   ON pa.district_id=d.district_id
                            LEFT JOIN   tehsils                     t   ON pa.tehsil_id=t.tehsil_id
                            LEFT JOIN   tickets                     ut  ON pa.plan_atp_id=ut.plan_atp_id
                            WHERE ".$search_query."
                            ORDER BY sp.my_atp_id DESC
                            )   A,
                            (select @a:=0) initvars";
            $participants   = collect(DB::select($query));
            return Datatables::of($participants)->make(); 
    }

    public function getParticipationPlanList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.selfpaticipation.participationplanlist');
    }
    
    public function postAllqueryplan(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }else{
            
            $userData = \DB::table('branch_admin')->where(['id' => $sess])->first();
            if(($userData) && (count($userData)>0)) {
                $zonal_manager_id = $userData->zonal_manager_id;
                if($zonal_manager_id != '' && !empty($zonal_manager_id)){
                    $zonalq = " user_id = '".$zonal_manager_id."' and user_type = 'zonal'";
                }
                else{
                    $zonalq = "";
                }
                //rsm
                $rsmData = DB::table('sales_regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
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
                    $rsmresult1 = implode("','", $rsmresult);
                    if($rsmresult1 != '' && !empty($rsmresult1)){
                        $rsmresultq = " or user_id in ('".$rsmresult1."') and user_type = 'sales_regional'";
                    }
                    else{
                        $rsmresultq = "";
                    }
                }

                //asm
                $asmData = DB::table('sales_area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
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
                    $asmresult1 = implode("','", $asmresult);
                    if($asmresult1 != '' && !empty($asmresult1)){
                        $asmresultq = " or user_id in ('".$asmresult1."') and user_type = 'sales_area'";
                    }
                    else{
                        $asmresultq = "";
                    }
                }

                //regional
                $rData = DB::table('regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('regional_manager_id')
                          ->get();
                $rresult = array(); 
                if (isset($rData) && !empty($rData)) 
                {
                    $rDatass = json_decode(json_encode($rData),true);
                    foreach ($rDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $rresult = array_merge($rresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                            $rresult[$key] = $value; 
                        } 
                    } 
                    $rresult1 = implode("','", $rresult);
                    if($rresult1 != '' && !empty($rresult1)){
                        $rresultq = " or user_id in ('".$rresult1."') and user_type = 'regional' ";
                    }
                    else{
                        $rresultq = "";
                    }
                }

                //area
                $aData = DB::table('area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('area_manager_id')
                          ->get();
                $aresult = array(); 
                if (isset($aData) && !empty($aData)) 
                {
                    $aDatass = json_decode(json_encode($aData),true);
                    foreach ($aDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $aresult = array_merge($aresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                            $aresult[$key] = $value; 
                        } 
                    } 
                    $aresult1 = implode("','", $aresult);
                    if($aresult1 != '' && !empty($aresult1)){
                        $aresultq = " or user_id in ('".$aresult1."') and user_type = 'area' ";
                    }
                    else{
                        $aresultq = "";
                    }
                }
            }
            else{
                $zonalq     = " (1=1) ";
                $rsmresultq = "";
                $asmresultq = "";
                $rresultq   = "";
                $aresultq   = "";
            }
        }

        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        
        $self_participation_plans = "SELECT MAX(self_participation_plan_id)
                                    FROM self_participation_plans
                                    WHERE ".$zonalq.' '.$rsmresultq.' '.$asmresultq.' '.$rresultq.' '.$aresultq."
                                    GROUP BY plan_atp_id,user_id";
        $self_participationData = collect(DB::select($self_participation_plans));
        $self_participationresult = array(); 
        if (isset($self_participationData) && !empty($self_participationData)) 
        {
            $self_participationDatass = json_decode(json_encode($self_participationData),true);
            
            foreach ($self_participationDatass as $key => $value) { 
                if (is_array($value)) 
                { 
                    $self_participationresult = array_merge($self_participationresult, array_flatten($value)); 
                } 
                else 
                { 
                    $self_participationresult[$key] = $value; 
                } 
            } 
        }
        $self_participation_ids = implode("','", $self_participationresult);

        $test = $request::all();
        $start_date     = isset($test['start_date']) ? $test['start_date'] :'';
        $end_date       = isset($test['end_date']) ?  $test['end_date']:'';
        
        $search_query=" (1=1) ";

        if($start_date!='' && $end_date!='' && $start_date != $end_date) {
            if($self_participation_ids != '' && !empty($self_participation_ids)){
                $search_query.= " and DATE(sp.plan_date) >= DATE('".$start_date."') AND DATE(sp.plan_date) <= DATE('".$end_date."') and self_participation_plan_id IN ('".$self_participation_ids."')";
            }
            else{
                $search_query.= " and DATE(sp.plan_date) >= DATE('".$start_date."') AND DATE(sp.plan_date) <= DATE('".$end_date."')";
            }
            
        }elseif($start_date!='' && $end_date!='' && $start_date == $end_date) {
            if($self_participation_ids != '' && !empty($self_participation_ids)){
                $search_query.= " and DATE(sp.plan_date) = DATE('".$start_date."') and self_participation_plan_id IN ('".$self_participation_ids."')";
            }
            else{
                $search_query.= " and DATE(sp.plan_date) = DATE('".$start_date."')";
            }
        }
        else if($start_date == ''){
            $search_query.= " and self_participation_plan_id IN ('".$self_participation_ids."')";
        }

        if(isset($search_array['value']) && $search_array['value']!=''){
                $search = $search_array['value']; 

                $search_query.=" and  (                                
                            zm.ECN                              LIKE '%".$search."%'    OR
                            rm.ECN                              LIKE '%".$search."%'    OR
                            am.ECN                              LIKE '%".$search."%'    OR
                            sam.ECN                             LIKE '%".$search."%'    OR
                            srm.ECN                             LIKE '%".$search."%'    OR
                            u.ECN                               LIKE '%".$search."%'    OR
                            ms.ECN                              LIKE '%".$search."%'    OR
                            zm.zonal_manager_name               LIKE '%".$search."%'    OR
                            rm.regional_manager_name            LIKE '%".$search."%'    OR
                            am.area_manager_name                LIKE '%".$search."%'    OR
                            sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                            srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                            ms.marketing_supervisor_name        LIKE '%".$search."%'    OR
                            u.name                              LIKE '%".$search."%'    OR
                            p.product                           LIKE '%".$search."%'    OR
                            c.crop                              LIKE '%".$search."%'    OR
                            s.state                             LIKE '%".$search."%'    OR
                            d.district                          LIKE '%".$search."%'    OR
                            t.tehsil                            LIKE '%".$search."%'    OR
                            ut.ticket_no                        LIKE '%".$search."%'
                            ) ";
        }
            

            $query= " 
                        SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*                                
                        FROM
                            (
                            SELECT
                                CONCAT_WS(
                                            '\n', 
                                            NULLIF(zm.ECN, ''),
                                            NULLIF(rm.ECN, ''),
                                            NULLIF(am.ECN, ''),
                                            NULLIF(srm.ECN, ''),
                                            NULLIF(sam.ECN, ''),
                                            NULLIF(u.ECN, ''),
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
                                            NULLIF(ms.marketing_supervisor_name, '')
                                        ) AS name,
                                UPPER(sp.user_type) AS role,
                                sp.plan_date,
                                p.product,
                                c.crop,
                                ds.data_source,
                                s.state,
                                d.district,
                                t.tehsil,
                                pa.village,
                                pa.expected_farmer,
                                pa.expected_pda,
                                DATE_FORMAT(sp.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                sp.reason,
                                ut.ticket_no
                            FROM        self_participation_plans    sp
                            LEFT JOIN   plan_atp                    pa  ON sp.plan_atp_id=pa.plan_atp_id                            
                            LEFT JOIN   users                       u   ON sp.user_id=u.id AND sp.user_type='bdo'
                            LEFT JOIN   zonal_managers              zm  ON sp.user_id=zm.zonal_manager_id AND sp.user_type='zonal'
                            LEFT JOIN   regional_managers           rm  ON sp.user_id=rm.regional_manager_id AND sp.user_type='regional'
                            LEFT JOIN   area_managers               am  ON sp.user_id=am.area_manager_id AND sp.user_type='area'
                            LEFT JOIN   sales_regional_managers     srm ON sp.user_id=srm.sales_regional_manager_id AND sp.user_type='sales_regional'
                            LEFT JOIN   sales_area_managers         sam ON sp.user_id=sam.sales_area_manager_id AND sp.user_type='sales_area'
                            LEFT JOIN   marketing_supervisor    ms   ON sp.user_id=ms.marketing_supervisor_id AND sp.user_type='marketing_supervisor'

                            LEFT JOIN   products                    p   ON pa.product_id=p.product_id
                            LEFT JOIN   crops                       c   ON pa.crop_id=c.crop_id
                            LEFT JOIN   data_sources                ds  ON pa.data_source_id=ds.data_source_id
                            LEFT JOIN   states                      s   ON pa.state_id=s.state_id
                            LEFT JOIN   districts                   d   ON pa.district_id=d.district_id
                            LEFT JOIN   tehsils                     t   ON pa.tehsil_id=t.tehsil_id
                            LEFT JOIN   tickets                     ut  ON pa.plan_atp_id=ut.plan_atp_id
                            WHERE ".$search_query." 
                            
                            )   A,
                            (select @a:=0) initvars";
            $participants   = collect(DB::select($query));
            foreach ($participants as $key => $value) { 
                if($participants[$key]->role == 'SALES_AREA'){ 
                    $sales_area_managers = DB::table('sales_area_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();
                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$sales_area_managers[0]->zonal_manager_id)
                                           ->get();
                    $regional_managers   = DB::table('regional_managers')
                                           ->where('regional_manager_id',$sales_area_managers[0]->regional_manager_id)
                                           ->get();
                    
                    $sales_regional_managers = DB::table('sales_regional_managers')
                                               ->where('sales_regional_manager_id',$sales_area_managers[0]->sales_regional_manager_id)
                                               ->get();
                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                    $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  $sales_area_managers[0]->ECN;
                    $participants[$key]->sales_area_name =  $sales_area_managers[0]->sales_area_manager_name;                          
                }
                else if($participants[$key]->role == 'AREA'){
                    $area_managers       = DB::table('area_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();

                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$area_managers[0]->zonal_manager_id)
                                           ->get();
                    $regional_managers   = DB::table('regional_managers')
                                           ->where('regional_manager_id',$area_managers[0]->regional_manager_id)
                                           ->get();
                    
                    $sales_regional_managers = DB::table('sales_regional_managers')
                                               ->where('sales_regional_manager_id',$area_managers[0]->sales_regional_manager_id)
                                               ->get(); 

                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                    $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn =  $area_managers[0]->ECN;
                    $participants[$key]->area_name =  $area_managers[0]->area_manager_name; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  "";                      
                }
                else if($participants[$key]->role == 'SALES_REGIONAL'){
                    $sales_regional_managers = DB::table('sales_regional_managers')
                                               ->where('ECN',$participants[$key]->ecn)
                                               ->get(); 
                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$sales_regional_managers[0]->zonal_manager_id)
                                           ->get();
                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  "";
                    $participants[$key]->regional_name =  "";  
                    $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                }
                else if ($participants[$key]->role == 'REGIONAL'){
                    $regional_managers   = DB::table('regional_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();

                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$regional_managers[0]->zonal_manager_id)
                                           ->get();

                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                    $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  ""; 
                }
                else if ($participants[$key]->role == 'ZONAL'){
                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();
                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  "";
                    $participants[$key]->regional_name =  "";  
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  ""; 
                }
                else{
                    $participants[$key]->zonal_ecn =  "";
                    $participants[$key]->zonal_name =  "";
                    $participants[$key]->regional_ecn =  "";
                    $participants[$key]->regional_name =  "";  
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  ""; 
                }
            }
            return Datatables::of($participants)->make(); 
    }
    
    public function postExportAllParticipantsPlan(Request $request) {          
        $sess       =   \Session::get('adminid');
        // $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        // $base_url   =   url('/');
        // if ($userData) {
        //     $my_data    =   Input::all(); 
        //     $search     =   $my_data['search'];
        //     $dataSearch = '';

        //     $test = $request::all();
        //     $start_date     = isset($test['start_date']) ? $test['start_date'] :'';
        //     $end_date       = isset($test['end_date']) ?  $test['end_date']:'';

        //     $self_participation_plans = "SELECT MAX(self_participation_plan_id)
        //                             FROM self_participation_plans
        //                             GROUP BY plan_atp_id,user_id";
        //     $self_participationData = collect(DB::select($self_participation_plans));
        //     $self_participationresult = array(); 
        //     if (isset($self_participationData) && !empty($self_participationData)) 
        //     {
        //         $self_participationDatass = json_decode(json_encode($self_participationData),true);
                
        //         foreach ($self_participationDatass as $key => $value) { 
        //             if (is_array($value)) 
        //             { 
        //                 $self_participationresult = array_merge($self_participationresult, array_flatten($value)); 
        //             } 
        //             else 
        //             { 
        //                 $self_participationresult[$key] = $value; 
        //             } 
        //         } 
        //     }
        //     $self_participation_ids = implode("','", $self_participationresult);

        //     $search_query=" (1=1) ";

        //     if($start_date!='' && $end_date!='' && $start_date != $end_date) {
        //         $search_query.= " and DATE(sp.plan_date) >= DATE('".$start_date."') AND DATE(sp.plan_date) <= DATE('".$end_date."')";
        //     }elseif($start_date!='' && $end_date!='' && $start_date == $end_date) {
        //         $search_query.= " and DATE(sp.plan_date) >= DATE('".$start_date."')";
        //     }
        //     else if($start_date == ''){
        //         $search_query.= " and self_participation_plan_id IN ('".$self_participation_ids."')";
        //     }


        //     if(isset($search_array['value']) && $search_array['value']!=''){
        //         $search = $search_array['value']; 


            
        //         $search_query.=   "
        //                         (1=1)
        //                         AND
        //                         (
        //                             zm.ECN                              LIKE '%".$search."%'    OR
        //                             rm.ECN                              LIKE '%".$search."%'    OR
        //                             am.ECN                              LIKE '%".$search."%'    OR
        //                             sam.ECN                             LIKE '%".$search."%'    OR
        //                             srm.ECN                             LIKE '%".$search."%'    OR
        //                             u.ECN                               LIKE '%".$search."%'    OR
        //                             ms.ECN                              LIKE '%".$search."%'    OR
        //                             zm.zonal_manager_name               LIKE '%".$search."%'    OR
        //                             rm.regional_manager_name            LIKE '%".$search."%'    OR
        //                             am.area_manager_name                LIKE '%".$search."%'    OR
        //                             sam.sales_area_manager_name         LIKE '%".$search."%'    OR
        //                             srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
        //                             ms.marketing_supervisor_name        LIKE '%".$search."%'    OR
        //                             u.name                              LIKE '%".$search."%'    OR
        //                             p.product                           LIKE '%".$search."%'    OR
        //                             c.crop                              LIKE '%".$search."%'    OR
        //                             s.state                             LIKE '%".$search."%'    OR
        //                             d.district                          LIKE '%".$search."%'    OR
        //                             t.tehsil                            LIKE '%".$search."%'    OR
        //                             ut.ticket_no                        LIKE '%".$search."%'
        //                         )             
        //                         ";
        //     }
            
            
            
        //     $query      =   " 
        //                     SELECT                                 
        //                         @a:=@a+1  as serial_number,
        //                         A.*                                
        //                     FROM
        //                         (
        //                         SELECT
        //                             CONCAT_WS(
        //                                         '\n', 
        //                                         NULLIF(zm.ECN, ''),
        //                                         NULLIF(rm.ECN, ''),
        //                                         NULLIF(am.ECN, ''),
        //                                         NULLIF(srm.ECN, ''),
        //                                         NULLIF(sam.ECN, ''),
        //                                         NULLIF(u.ECN, ''),
        //                                         NULLIF(ms.ECN, '')
        //                                     ) AS ecn,
        //                             CONCAT_WS(
        //                                         '\n', 
        //                                         NULLIF(zm.zonal_manager_name, ''),
        //                                         NULLIF(rm.regional_manager_name, ''),
        //                                         NULLIF(am.area_manager_name, ''),
        //                                         NULLIF(srm.sales_regional_manager_name, ''),
        //                                         NULLIF(sam.sales_area_manager_name, ''),
        //                                         NULLIF(u.name, ''),
        //                                         NULLIF(ms.marketing_supervisor_name, '')
        //                                     ) AS name,
        //                             UPPER(sp.user_type) AS role,
        //                             sp.plan_date,
        //                             p.product,
        //                             c.crop,
        //                             ds.data_source,
        //                             s.state,
        //                             d.district,
        //                             t.tehsil,
        //                             pa.village,
        //                             pa.expected_farmer,
        //                             pa.expected_pda,
        //                             DATE_FORMAT(sp.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
        //                             sp.reason,
        //                             ut.ticket_no
        //                         FROM        self_participation_plans    sp
        //                         LEFT JOIN   plan_atp                    pa  ON sp.plan_atp_id=pa.plan_atp_id                            
        //                         LEFT JOIN   users                       u   ON sp.user_id=u.id AND sp.user_type='bdo'
        //                         LEFT JOIN   zonal_managers              zm  ON sp.user_id=zm.zonal_manager_id AND sp.user_type='zonal'
        //                         LEFT JOIN   regional_managers           rm  ON sp.user_id=rm.regional_manager_id AND sp.user_type='regional'
        //                         LEFT JOIN   area_managers               am  ON sp.user_id=am.area_manager_id AND sp.user_type='area'
        //                         LEFT JOIN   sales_regional_managers     srm ON sp.user_id=srm.sales_regional_manager_id AND sp.user_type='sales_regional'
        //                         LEFT JOIN   sales_area_managers         sam ON sp.user_id=sam.sales_area_manager_id AND sp.user_type='sales_area'
        //                         LEFT JOIN   marketing_supervisor    ms   ON sp.user_id=ms.marketing_supervisor_id AND sp.user_type='marketing_supervisor'

        //                         LEFT JOIN   products                    p   ON pa.product_id=p.product_id
        //                         LEFT JOIN   crops                       c   ON pa.crop_id=c.crop_id
        //                         LEFT JOIN   data_sources                ds  ON pa.data_source_id=ds.data_source_id
        //                         LEFT JOIN   states                      s   ON pa.state_id=s.state_id
        //                         LEFT JOIN   districts                   d   ON pa.district_id=d.district_id
        //                         LEFT JOIN   tehsils                     t   ON pa.tehsil_id=t.tehsil_id
        //                         LEFT JOIN   tickets                     ut  ON pa.plan_atp_id=ut.plan_atp_id
        //                         WHERE 
        //                                 ".$search_query." 
                               
        //                         )   A,
        //                         (select @a:=0) initvars";
        //     //echo $query; exit;
        
        //     $participants = DB::select(DB::raw($query));
        //     foreach ($participants as $key => $value) { 
        //         if($participants[$key]->role == 'SALES_AREA'){ 
        //             $sales_area_managers = DB::table('sales_area_managers')
        //                                    ->where('ECN',$participants[$key]->ecn)
        //                                    ->get();
        //             $zonal_managers      = DB::table('zonal_managers')
        //                                    ->where('zonal_manager_id',$sales_area_managers[0]->zonal_manager_id)
        //                                    ->get();
        //             $regional_managers   = DB::table('regional_managers')
        //                                    ->where('regional_manager_id',$sales_area_managers[0]->regional_manager_id)
        //                                    ->get();
                    
        //             $sales_regional_managers = DB::table('sales_regional_managers')
        //                                        ->where('sales_regional_manager_id',$sales_area_managers[0]->sales_regional_manager_id)
        //                                        ->get();
        //             $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
        //             $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
        //             $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
        //             $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
        //             $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
        //             $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
        //             $participants[$key]->area_ecn =  "";
        //             $participants[$key]->area_name =  ""; 
        //             $participants[$key]->sales_area_ecn =  $sales_area_managers[0]->ECN;
        //             $participants[$key]->sales_area_name =  $sales_area_managers[0]->sales_area_manager_name;                          
        //         }
        //         else if($participants[$key]->role == 'AREA'){
        //             $area_managers       = DB::table('area_managers')
        //                                    ->where('ECN',$participants[$key]->ecn)
        //                                    ->get();

        //             $zonal_managers      = DB::table('zonal_managers')
        //                                    ->where('zonal_manager_id',$area_managers[0]->zonal_manager_id)
        //                                    ->get();
        //             $regional_managers   = DB::table('regional_managers')
        //                                    ->where('regional_manager_id',$area_managers[0]->regional_manager_id)
        //                                    ->get();
                    
        //             $sales_regional_managers = DB::table('sales_regional_managers')
        //                                        ->where('sales_regional_manager_id',$area_managers[0]->sales_regional_manager_id)
        //                                        ->get(); 

        //             $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
        //             $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
        //             $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
        //             $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
        //             $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
        //             $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
        //             $participants[$key]->area_ecn =  $area_managers[0]->ECN;
        //             $participants[$key]->area_name =  $area_managers[0]->area_manager_name; 
        //             $participants[$key]->sales_area_ecn =  "";
        //             $participants[$key]->sales_area_name =  "";                      
        //         }
        //         else if($participants[$key]->role == 'SALES_REGIONAL'){
        //             $sales_regional_managers = DB::table('sales_regional_managers')
        //                                        ->where('ECN',$participants[$key]->ecn)
        //                                        ->get(); 
        //             $zonal_managers      = DB::table('zonal_managers')
        //                                    ->where('zonal_manager_id',$sales_regional_managers[0]->zonal_manager_id)
        //                                    ->get();
        //             $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
        //             $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
        //             $participants[$key]->regional_ecn =  "";
        //             $participants[$key]->regional_name =  "";  
        //             $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
        //             $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
        //             $participants[$key]->area_ecn =  "";
        //             $participants[$key]->area_name =  ""; 
        //             $participants[$key]->sales_regional_ecn =  "";
        //             $participants[$key]->sales_regional_name =  "";
        //         }
        //         else if ($participants[$key]->role == 'REGIONAL'){
        //             $regional_managers   = DB::table('regional_managers')
        //                                    ->where('ECN',$participants[$key]->ecn)
        //                                    ->get();

        //             $zonal_managers      = DB::table('zonal_managers')
        //                                    ->where('zonal_manager_id',$regional_managers[0]->zonal_manager_id)
        //                                    ->get();

        //             $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
        //             $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
        //             $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
        //             $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
        //             $participants[$key]->sales_regional_ecn =  "";
        //             $participants[$key]->sales_regional_name =  "";
        //             $participants[$key]->area_ecn =  "";
        //             $participants[$key]->area_name =  ""; 
        //             $participants[$key]->sales_area_ecn =  "";
        //             $participants[$key]->sales_area_name =  ""; 
        //         }
        //         else if ($participants[$key]->role == 'ZONAL'){
        //             $zonal_managers      = DB::table('zonal_managers')
        //                                    ->where('ECN',$participants[$key]->ecn)
        //                                    ->get();
        //             $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
        //             $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
        //             $participants[$key]->regional_ecn =  "";
        //             $participants[$key]->regional_name =  "";  
        //             $participants[$key]->sales_regional_ecn =  "";
        //             $participants[$key]->sales_regional_name =  "";
        //             $participants[$key]->area_ecn =  "";
        //             $participants[$key]->area_name =  ""; 
        //             $participants[$key]->sales_area_ecn =  "";
        //             $participants[$key]->sales_area_name =  ""; 
        //         }
        //         else{
        //             $participants[$key]->zonal_ecn =  "";
        //             $participants[$key]->zonal_name =  "";
        //             $participants[$key]->regional_ecn =  "";
        //             $participants[$key]->regional_name =  "";  
        //             $participants[$key]->sales_regional_ecn =  "";
        //             $participants[$key]->sales_regional_name =  "";
        //             $participants[$key]->area_ecn =  "";
        //             $participants[$key]->area_name =  ""; 
        //             $participants[$key]->sales_area_ecn =  "";
        //             $participants[$key]->sales_area_name =  ""; 
        //         }
        //     }
        //     $result         = array_map(function ($value) {
        //                         return (array)$value;
        //                     }, $participants);
            
        //     $ticket_column  = array(                
        //                             'Id',
        //                             'ECN',
        //                             'Username',
        //                             'User Role',
        //                             'Plan Date',
        //                             'Product',
        //                             'Crop',
        //                             'Data Source',
        //                             'State',
        //                             'District',
        //                             'Tehsil',
        //                             'Village',
        //                             'Expected Farmers',
        //                             'Expected PDA',
        //                             'Created At',
        //                             'Reason',
        //                             'Generated Ticket',
        //                             'Zonal ECN',
        //                             'Zonal Manager',
        //                             'Regional ECN',
        //                             'Regional Manager',
        //                             'Sales Regional ECN',
        //                             'Sales Regional Manager',
        //                             'Area ECN',
        //                             'Area Manager',
        //                             'Sales Area ECN',
        //                             'Sales Area Manager'
        //                             );
        //     array_unshift($result, $ticket_column);
            
        //     $primarycolor   =   "rgb(255, 0, 0)";
        //     $myFile         =   Excel::create("filename", function($excel) use($result, $primarycolor) {
        //                             $excel->setTitle('title');
        //                             $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
        //                                 $sheet->fromArray($result, null, 'A1', false, false);
        //                                 $sheet->row(1, function($row) use ($primarycolor) {
        //                                     $row->setFontSize(12)
        //                                         ->setFontWeight('bold')
        //                                         ->setBackground('#62bdb9')
        //                                         ->setAlignment('left')
        //                                         ->setFontColor('#000000');
        //                                 });
        //                             });
        //                         });
                 
        //     $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
        //     $response   = array(
        //                         'name' => "Self Participation List.xlsx", //no extention needed
        //                         'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
        //                     );
        //     return response()->json($response);
        // }
            $userData   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
            $base_url   =   url('/');
        
            if(count($userData)>0) {
                $zonal_manager_id = $userData->zonal_manager_id;
                if($zonal_manager_id != '' && !empty($zonal_manager_id)){
                    $zonalq = " user_id = '".$zonal_manager_id."' and user_type = 'zonal'";
                }
                else{
                    $zonalq = "";
                }
                //rsm
                $rsmData = DB::table('sales_regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
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
                    $rsmresult1 = implode("','", $rsmresult);
                    if($rsmresult1 != '' && !empty($rsmresult1)){
                        $rsmresultq = " or user_id in ('".$rsmresult1."') and user_type = 'sales_regional'";
                    }
                    else{
                        $rsmresultq = "";
                    }
                }

                //asm
                $asmData = DB::table('sales_area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
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
                    $asmresult1 = implode("','", $asmresult);
                    if($asmresult1 != '' && !empty($asmresult1)){
                        $asmresultq = " or user_id in ('".$asmresult1."') and user_type = 'sales_area'";
                    }
                    else{
                        $asmresultq = "";
                    }
                }

                //regional
                $rData = DB::table('regional_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('regional_manager_id')
                          ->get();
                $rresult = array(); 
                if (isset($rData) && !empty($rData)) 
                {
                    $rDatass = json_decode(json_encode($rData),true);
                    foreach ($rDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $rresult = array_merge($rresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                            $rresult[$key] = $value; 
                        } 
                    } 
                    $rresult1 = implode("','", $rresult);
                    if($rresult1 != '' && !empty($rresult1)){
                        $rresultq = " or user_id in ('".$rresult1."') and user_type = 'regional' ";
                    }
                    else{
                        $rresultq = "";
                    }
                }

                //area
                $aData = DB::table('area_managers')
                          ->where('zonal_manager_id',$zonal_manager_id)
                          ->select('area_manager_id')
                          ->get();
                $aresult = array(); 
                if (isset($aData) && !empty($aData)) 
                {
                    $aDatass = json_decode(json_encode($aData),true);
                    foreach ($aDatass as $key => $value) { 
                        if (is_array($value)) 
                        { 
                            $aresult = array_merge($aresult, array_flatten($value)); 
                        } 
                        else 
                        { 
                            $aresult[$key] = $value; 
                        } 
                    } 
                    $aresult1 = implode("','", $aresult);
                    if($aresult1 != '' && !empty($aresult1)){
                        $aresultq = " or user_id in ('".$aresult1."') and user_type = 'area' ";
                    }
                    else{
                        $aresultq = "";
                    }
                }
            }
            else{
                $zonalq     = " (1=1) ";
                $rsmresultq = "";
                $asmresultq = "";
                $rresultq   = "";
                $aresultq   = "";
            }
        
            $my_data    =   Input::all(); 
            $search     =   $my_data['search'];
            $dataSearch = '';

            $test = $request::all();
            $start_date     = isset($test['start_date']) ? $test['start_date'] :'';
            $end_date       = isset($test['end_date']) ?  $test['end_date']:'';

            $self_participation_plans = "SELECT MAX(self_participation_plan_id)
                                    FROM self_participation_plans
                                    WHERE ".$zonalq.' '.$rsmresultq.' '.$asmresultq.' '.$rresultq.' '.$aresultq."
                                    GROUP BY plan_atp_id,user_id";
            $self_participationData = collect(DB::select($self_participation_plans));
            $self_participationresult = array(); 
            if (isset($self_participationData) && !empty($self_participationData)) 
            {
                $self_participationDatass = json_decode(json_encode($self_participationData),true);
                
                foreach ($self_participationDatass as $key => $value) { 
                    if (is_array($value)) 
                    { 
                        $self_participationresult = array_merge($self_participationresult, array_flatten($value)); 
                    } 
                    else 
                    { 
                        $self_participationresult[$key] = $value; 
                    } 
                } 
            }
            $self_participation_ids = implode("','", $self_participationresult);

            $search_query=" (1=1) ";

            if($start_date!='' && $end_date!='' && $start_date != $end_date) {
                if($self_participation_ids != '' && !empty($self_participation_ids)){
                    $search_query.= " and DATE(sp.plan_date) >= DATE('".$start_date."') AND DATE(sp.plan_date) <= DATE('".$end_date."') and self_participation_plan_id IN ('".$self_participation_ids."')";
                }
                else{
                    $search_query.= " and DATE(sp.plan_date) >= DATE('".$start_date."') AND DATE(sp.plan_date) <= DATE('".$end_date."')";
                }
                
            }elseif($start_date!='' && $end_date!='' && $start_date == $end_date) {
                if($self_participation_ids != '' && !empty($self_participation_ids)){
                    $search_query.= " and DATE(sp.plan_date) = DATE('".$start_date."') and self_participation_plan_id IN ('".$self_participation_ids."')";
                }
                else{
                    $search_query.= " and DATE(sp.plan_date) = DATE('".$start_date."')";
                }
            }
            else if($start_date == ''){
                $search_query.= " and self_participation_plan_id IN ('".$self_participation_ids."')";
            }


            if(isset($search_array['value']) && $search_array['value']!=''){
                $search = $search_array['value']; 


            
                $search_query.=   "
                                (1=1)
                                AND
                                (
                                    zm.ECN                              LIKE '%".$search."%'    OR
                                    rm.ECN                              LIKE '%".$search."%'    OR
                                    am.ECN                              LIKE '%".$search."%'    OR
                                    sam.ECN                             LIKE '%".$search."%'    OR
                                    srm.ECN                             LIKE '%".$search."%'    OR
                                    u.ECN                               LIKE '%".$search."%'    OR
                                    ms.ECN                              LIKE '%".$search."%'    OR
                                    zm.zonal_manager_name               LIKE '%".$search."%'    OR
                                    rm.regional_manager_name            LIKE '%".$search."%'    OR
                                    am.area_manager_name                LIKE '%".$search."%'    OR
                                    sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                                    srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                                    ms.marketing_supervisor_name        LIKE '%".$search."%'    OR
                                    u.name                              LIKE '%".$search."%'    OR
                                    p.product                           LIKE '%".$search."%'    OR
                                    c.crop                              LIKE '%".$search."%'    OR
                                    s.state                             LIKE '%".$search."%'    OR
                                    d.district                          LIKE '%".$search."%'    OR
                                    t.tehsil                            LIKE '%".$search."%'    OR
                                    ut.ticket_no                        LIKE '%".$search."%'
                                )             
                                ";
            }
            
            
            
            $query      =   " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT
                                    CONCAT_WS(
                                                '\n', 
                                                NULLIF(zm.ECN, ''),
                                                NULLIF(rm.ECN, ''),
                                                NULLIF(am.ECN, ''),
                                                NULLIF(srm.ECN, ''),
                                                NULLIF(sam.ECN, ''),
                                                NULLIF(u.ECN, ''),
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
                                                NULLIF(ms.marketing_supervisor_name, '')
                                            ) AS name,
                                    UPPER(sp.user_type) AS role,
                                    sp.plan_date,
                                    p.product,
                                    c.crop,
                                    ds.data_source,
                                    s.state,
                                    d.district,
                                    t.tehsil,
                                    pa.village,
                                    pa.expected_farmer,
                                    pa.expected_pda,
                                    DATE_FORMAT(sp.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    sp.reason,
                                    ut.ticket_no
                                FROM        self_participation_plans    sp
                                LEFT JOIN   plan_atp                    pa  ON sp.plan_atp_id=pa.plan_atp_id                            
                                LEFT JOIN   users                       u   ON sp.user_id=u.id AND sp.user_type='bdo'
                                LEFT JOIN   zonal_managers              zm  ON sp.user_id=zm.zonal_manager_id AND sp.user_type='zonal'
                                LEFT JOIN   regional_managers           rm  ON sp.user_id=rm.regional_manager_id AND sp.user_type='regional'
                                LEFT JOIN   area_managers               am  ON sp.user_id=am.area_manager_id AND sp.user_type='area'
                                LEFT JOIN   sales_regional_managers     srm ON sp.user_id=srm.sales_regional_manager_id AND sp.user_type='sales_regional'
                                LEFT JOIN   sales_area_managers         sam ON sp.user_id=sam.sales_area_manager_id AND sp.user_type='sales_area'
                                LEFT JOIN   marketing_supervisor    ms   ON sp.user_id=ms.marketing_supervisor_id AND sp.user_type='marketing_supervisor'

                                LEFT JOIN   products                    p   ON pa.product_id=p.product_id
                                LEFT JOIN   crops                       c   ON pa.crop_id=c.crop_id
                                LEFT JOIN   data_sources                ds  ON pa.data_source_id=ds.data_source_id
                                LEFT JOIN   states                      s   ON pa.state_id=s.state_id
                                LEFT JOIN   districts                   d   ON pa.district_id=d.district_id
                                LEFT JOIN   tehsils                     t   ON pa.tehsil_id=t.tehsil_id
                                LEFT JOIN   tickets                     ut  ON pa.plan_atp_id=ut.plan_atp_id
                                WHERE 
                                        ".$search_query." 
                               
                                )   A,
                                (select @a:=0) initvars";
            //echo $query; exit;
        
            $participants = DB::select(DB::raw($query));
            foreach ($participants as $key => $value) { 
                if($participants[$key]->role == 'SALES_AREA'){ 
                    $sales_area_managers = DB::table('sales_area_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();
                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$sales_area_managers[0]->zonal_manager_id)
                                           ->get();
                    $regional_managers   = DB::table('regional_managers')
                                           ->where('regional_manager_id',$sales_area_managers[0]->regional_manager_id)
                                           ->get();
                    
                    $sales_regional_managers = DB::table('sales_regional_managers')
                                               ->where('sales_regional_manager_id',$sales_area_managers[0]->sales_regional_manager_id)
                                               ->get();
                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                    $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  $sales_area_managers[0]->ECN;
                    $participants[$key]->sales_area_name =  $sales_area_managers[0]->sales_area_manager_name;                          
                }
                else if($participants[$key]->role == 'AREA'){
                    $area_managers       = DB::table('area_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();

                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$area_managers[0]->zonal_manager_id)
                                           ->get();
                    $regional_managers   = DB::table('regional_managers')
                                           ->where('regional_manager_id',$area_managers[0]->regional_manager_id)
                                           ->get();
                    
                    $sales_regional_managers = DB::table('sales_regional_managers')
                                               ->where('sales_regional_manager_id',$area_managers[0]->sales_regional_manager_id)
                                               ->get(); 

                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                    $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn =  $area_managers[0]->ECN;
                    $participants[$key]->area_name =  $area_managers[0]->area_manager_name; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  "";                      
                }
                else if($participants[$key]->role == 'SALES_REGIONAL'){
                    $sales_regional_managers = DB::table('sales_regional_managers')
                                               ->where('ECN',$participants[$key]->ecn)
                                               ->get(); 
                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$sales_regional_managers[0]->zonal_manager_id)
                                           ->get();
                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  "";
                    $participants[$key]->regional_name =  "";  
                    $participants[$key]->sales_regional_ecn =  $sales_regional_managers[0]->ECN;
                    $participants[$key]->sales_regional_name =  $sales_regional_managers[0]->sales_regional_manager_name;
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                }
                else if ($participants[$key]->role == 'REGIONAL'){
                    $regional_managers   = DB::table('regional_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();

                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$regional_managers[0]->zonal_manager_id)
                                           ->get();

                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  $regional_managers[0]->ECN;
                    $participants[$key]->regional_name =  $regional_managers[0]->regional_manager_name;  
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  ""; 
                }
                else if ($participants[$key]->role == 'ZONAL'){
                    $zonal_managers      = DB::table('zonal_managers')
                                           ->where('ECN',$participants[$key]->ecn)
                                           ->get();
                    $participants[$key]->zonal_ecn =  $zonal_managers[0]->ECN;
                    $participants[$key]->zonal_name =  $zonal_managers[0]->zonal_manager_name;
                    $participants[$key]->regional_ecn =  "";
                    $participants[$key]->regional_name =  "";  
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  ""; 
                }
                else{
                    $participants[$key]->zonal_ecn =  "";
                    $participants[$key]->zonal_name =  "";
                    $participants[$key]->regional_ecn =  "";
                    $participants[$key]->regional_name =  "";  
                    $participants[$key]->sales_regional_ecn =  "";
                    $participants[$key]->sales_regional_name =  "";
                    $participants[$key]->area_ecn =  "";
                    $participants[$key]->area_name =  ""; 
                    $participants[$key]->sales_area_ecn =  "";
                    $participants[$key]->sales_area_name =  ""; 
                }
            }
            $result         = array_map(function ($value) {
                                return (array)$value;
                            }, $participants);
            
            $ticket_column  = array(                
                                    'Id',
                                    'ECN',
                                    'Username',
                                    'User Role',
                                    'Plan Date',
                                    'Product',
                                    'Crop',
                                    'Data Source',
                                    'State',
                                    'District',
                                    'Tehsil',
                                    'Village',
                                    'Expected Farmers',
                                    'Expected PDA',
                                    'Created At',
                                    'Reason',
                                    'Generated Ticket',
                                    'Zonal ECN',
                                    'Zonal Manager',
                                    'Regional ECN',
                                    'Regional Manager',
                                    'Sales Regional ECN',
                                    'Sales Regional Manager',
                                    'Area ECN',
                                    'Area Manager',
                                    'Sales Area ECN',
                                    'Sales Area Manager'
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
                                'name' => "Self Participation List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                            );
            return response()->json($response);
        
    } 

    public function getHighlightDetail($ticket_no){
        $base_url = url('/');
        $query ="SELECT
                ut.ticket_no,
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
                c1.crop     AS crop_1,
                p1.product  AS product_1, 
                c2.crop     AS crop_2,
                p2.product  AS product_2,
                c3.crop     AS crop_3,
                p3.product  AS product_3,
                ut.expected_farmers,
                ut.farmer_count,
                ut.invited_farmer_count,
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
                END),ut.ticket_id
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
                LEFT JOIN   ticket_highlights   th  ON ut.ticket_id=th.ticket_id
                                                    
                LEFT JOIN   users           u   ON ut.user_id=u.id AND ut.user_type='bdo'

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
                LEFT JOIN   dhanuka_doctor as ddss ON ddss.dd_id=ut.uploadedBy AND ut.uploadedByType='dhanuka_doctor'
                

                LEFT JOIN   dhanuka_doctor dds ON ut.assign_to_dd=dds.dd_id 
                LEFT JOIN   demo_request der ON ut.demo_request_id=der.demo_request_id
                
                LEFT JOIN   languages               l   ON ut.language_id=l.id
                
                WHERE ticket_no = '".$ticket_no."'

                GROUP BY ut.ticket_id";   
    
                $top_open_ticket    = collect(DB::select($query));
                // $base_url1       = url('/assets/atpfeedbackimage');
                $base_url1 = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                foreach ($top_open_ticket as $key => $value) {
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
                        $atpfeedback1 = "select ".$name." as name, CONCAT('".$base_url1."/', sp.uploaded_file) as feedbackImage, description, user_type from self_participations as sp LEFT JOIN ".$supervisorTable." as su ON sp.user_id = ".$id." where sp.ticket_id = '".$top_open_ticket[$key]->ticket_id."' ".$Suser_type." order by self_participation_id desc limit 0,1";
                        $atpfeedbackSupervisor  = collect(DB::select($atpfeedback1));
                        $top_open_ticket[$key]->feedbackData = $atpfeedbackSupervisor;
                    } else {
                        $top_open_ticket[$key]->feedbackData = [];
                    }
                
                    //total high lights
                    $query2 =   "select th.*,
                                CONCAT_WS(
                                        '\n', 
                                        NULLIF(zm.zonal_manager_name, ''),
                                        NULLIF(rm.regional_manager_name, ''),
                                        NULLIF(am.area_manager_name, ''),
                                        NULLIF(srm.sales_regional_manager_name, ''),
                                        NULLIF(sam.sales_area_manager_name, '')
                                        ) AS username
                                FROM    ticket_highlights as th
                                LEFT JOIN   zonal_managers          zm  ON th.user_id=zm.zonal_manager_id AND th.user_type='zonal'
                                LEFT JOIN   regional_managers       rm  ON th.user_id=rm.regional_manager_id AND th.user_type='regional'
                                LEFT JOIN   area_managers           am  ON th.user_id=am.area_manager_id AND th.user_type='area'
                                LEFT JOIN   sales_regional_managers srm ON th.user_id=srm.sales_regional_manager_id AND th.user_type='sales_regional'
                                LEFT JOIN   sales_area_managers     sam ON th.user_id=sam.sales_area_manager_id AND th.user_type='sales_area'
                                where th.ticket_id = ".$top_open_ticket[$key]->ticket_id."
                                ";
                    $highlightdata    = collect(DB::select($query2));
                    if (isset($highlightdata) && !empty($highlightdata) && count($highlightdata) > 0) {
                        foreach ($highlightdata as $datakey => $datavalue) {
                            if (!empty($highlightdata[$datakey]->highlightImage) && isset($highlightdata[$datakey]->highlightImage)) {
                                $highlightdata[$datakey]->highlightImage = $base_url. "/assets/highlightImage/".$highlightdata[$datakey]->highlightImage;
                            }
                        }
                        $top_open_ticket[$key]->highlightdata = $highlightdata;
                    } else {
                        $top_open_ticket[$key]->highlightdata = [];
                    }
                }
                //echo "<pre>"; print_r($top_open_ticket);die;
                return View::make('backend.selfpaticipation.unplanhighlight',compact('top_open_ticket'));
    }
}