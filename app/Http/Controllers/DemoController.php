<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB,Session,Validator;
use Yajra\Datatables\Facades\Datatables;
use Input;
use View;
use App\State;
use App\District;
use App\Tehsil;
use App\Village;
use Excel;
class DemoController extends Controller
{
    public function __construct() {

        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getDemoRequest()
    {
		return view('backend.demo.demorequest');       
	}

    public function postDemoRequest(){
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }else{
            $role_sess      = \Session::get('login_role_id');
            $ddresult1 = "";
            if($role_sess == '8'){
                $userData = \DB::table('branch_admin')->where(['id' => $sess])->first();
                if(count($userData)>0) {
                    $zonal_manager_id = $userData->zonal_manager_id;
                    $ddData = DB::table('dhanuka_doctor')
                              ->where('zonal_manager_id',$zonal_manager_id)
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
                        $ddresult1 = implode("','", $ddresult);
                    }
                }
                else{
                    $ddresult1 = "";
                }
            }else{
                $ddresult1 = "";
            }
            // $userData = \DB::table('branch_admin')->where(['id' => $sess])->first();
            // if(count($userData)>0) {
            //     $zonal_manager_id = $userData->zonal_manager_id;
            //     $ddData = DB::table('dhanuka_doctor')
            //               ->where('zonal_manager_id',$zonal_manager_id)
            //               ->select('dd_id')
            //               ->get();
            //     $ddresult = array(); 
            //     if (isset($ddData) && !empty($ddData)) 
            //     {
            //         $ddDatass = json_decode(json_encode($ddData),true);
            //         foreach ($ddDatass as $key => $value) { 
            //             if (is_array($value)) 
            //             { 
            //                 $ddresult = array_merge($ddresult, array_flatten($value)); 
            //             } 
            //             else 
            //             { 
            //                 $ddresult[$key] = $value; 
            //             } 
            //         } 
            //         $ddresult1 = implode("','", $ddresult);
            //     }
            // }
            // else{
            //     $ddresult1 = "";
            // }
        }

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
        $sorting        = '';
        $start_date   = isset($value['start_date']) ? $value['start_date'] :'';
        $end_date       = isset($value['end_date']) ?  $value['end_date']:'';

        $search_query=" (1=1)  ";

        if($start_date!='' && $end_date!='') {
            if($ddresult1 != '' && !empty($ddresult1)){
                $search_query.= " and DATE(d.created_at) >= DATE('".$start_date."') AND DATE(d.created_at) <= DATE('".$end_date."') and d.dd_id in ('".$ddresult1."')";    
            }
            else{
                $search_query.=" and DATE(d.created_at) >= DATE('".$start_date."') AND DATE(d.created_at) <= DATE('".$end_date."')";
            }
        }elseif($start_date!='') {
            if($ddresult1 != '' && !empty($ddresult1)){
                $search_query.= " and DATE(d.created_at) >= DATE('".$start_date."') and d.dd_id in ('".$ddresult1."')";    
            }
            else{
                $search_query.=" and DATE(d.created_at) >= DATE('".$start_date."')";
            }
        }
        else{   
            if($ddresult1 != '' && !empty($ddresult1)){
                $search_query.= " and d.dd_id in ('".$ddresult1."')";    
            }
        }
        //$base_url1       = url('/assets/demoimage');
        $base_url1 = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/demoimage";
        $demoList   =   " 
                        SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*                                
                        FROM
                            (select 
                                d.demo_request_id,
                                dd.ECN,
                                d.created_at,
                                dd.name,
                                c.crop,
                                p.product,
                                d.farmer_name,
                                d.farmer_type,
                                v.village,
                                t.tehsil,
                                dis.district,
                                s.state,
                                d.pincode,
                                d.village_id,
                                d.latitude,
                                d.longitude,
                                CONCAT('".$base_url1."/', d.demoImage) as demoImage,
                                d.demo_code,
                                d.status,
                                total_demos.total_demo,
                                ticketNos.ticket_no,
                                DATE_FORMAT(d.updated_at,'%Y-%m-%d') as evaluation_date,
                                d.contact_number,
                                CASE 
                                    WHEN d.showing_date != 'N/A' THEN DATE_FORMAT(STR_TO_DATE(d.showing_date, '%d-%m-%Y'), '%Y-%m-%d')
                                    ELSE d.showing_date
                                END AS showing_date,
                                DATE_FORMAT( d.created_at, '%Y-%m' ) AS month_year
                            FROM demo_request as d 
                            LEFT JOIN crops as c ON d.crop_id = c.crop_id LEFT JOIN products as p ON d.product_id = p.product_id 
                            LEFT JOIN dhanuka_doctor as dd ON d.dd_id = dd.dd_id LEFT JOIN villages as v ON d.village_id = v.village_id 
                            LEFT JOIN states as s ON d.state_id = s.state_id LEFT JOIN tehsils as t ON d.tehsil_id = t.tehsil_id 
                            LEFT JOIN districts as dis ON d.district_id = dis.district_id
                            LEFT JOIN (SELECT demo_request_id, COUNT(*) AS total_demo FROM tickets GROUP BY demo_request_id ) AS  total_demos ON total_demos.demo_request_id = d.demo_request_id 
                            LEFT JOIN (SELECT GROUP_CONCAT(ticket_no SEPARATOR ' , ')  AS ticket_no, demo_request_id FROM tickets GROUP BY demo_request_id) AS  ticketNos ON ticketNos.demo_request_id = d.demo_request_id
                            WHERE 
                                ".$search_query." 
                            GROUP BY d.crop_id, d.product_id, d.dd_id, d.farmer_name, d.spray, month_year
                            ORDER BY d.demo_request_id DESC
                            )   A,
                            (select @a:=0) initvars";
        // $demoList="select d.demo_request_id,dd.name,c.crop,p.product,d.farmer_name, d.farmer_type,v.village,t.tehsil,dis.district,s.state,d.pincode, d.latitude, d.longitude, CONCAT('".$base_url1."/', d.demoImage) as demoImage, d.demo_code, d.status, total_demos.total_demo, ticketNos.ticket_no from demo_request as d LEFT JOIN crops as c ON d.crop_id = c.crop_id LEFT JOIN products as p ON d.product_id = p.product_id LEFT JOIN dhanuka_doctor as dd ON d.dd_id = dd.dd_id LEFT JOIN villages as v ON d.village_id = v.village_id LEFT JOIN states as s ON d.state_id = s.state_id LEFT JOIN tehsils as t ON d.tehsil_id = t.tehsil_id LEFT JOIN districts as dis ON d.district_id = dis.district_id             LEFT JOIN (SELECT demo_request_id, COUNT(*) AS total_demo FROM tickets GROUP BY demo_request_id ) AS  total_demos ON total_demos.demo_request_id = d.demo_request_id             LEFT JOIN (SELECT GROUP_CONCAT(ticket_no SEPARATOR ' , ')  AS ticket_no, demo_request_id FROM tickets GROUP BY demo_request_id) AS  ticketNos ON ticketNos.demo_request_id = d.demo_request_id";
         //echo $distributorList;exit;     
         //echo $demoList;die;             
         $demoList  = collect(DB::select($demoList));
         //$base_url       = url('/assets/demofeedback');
         $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/demofeedback";
         foreach ($demoList as $key => $value) {
            $village_id = $value->village_id;
            if($village_id) {
                $lat = $value->latitude;
                $lon = $value->longitude;
                if($lat && $lon) {
                    $distanceData = $this->CalculateDistance($village_id,$lat, $lon);
                    if($distanceData !==0) {
                        $expDistanceData = explode("~", $distanceData);
                        $demoList[$key]->isValidate = $expDistanceData[1];
                        $demoList[$key]->distance = $expDistanceData[0];
                    } else {
                        $demoList[$key]->isValidate = "No";
                        $demoList[$key]->distance = "0";
                    }
                } else {
                    $demoList[$key]->isValidate = "No";
                    $demoList[$key]->distance = "0";
                }
            } else {
                $demoList[$key]->isValidate = "No";
                $demoList[$key]->distance = "0";
            }
            //for bdo
            $demofeedback = "select u.name as name, CONCAT('".$base_url."/', df.feedbackImage) as feedbackImage, df.description, df.status from demofeedback as df LEFT JOIN users as u ON df.user_id = u.id where df.demo_request_id = '".$demoList[$key]->demo_request_id."' and df.user_type='bdo' order by demofeedback_id desc limit 0,1";
            $demofeedbackBdo  = collect(DB::select($demofeedback));
            if (count($demofeedbackBdo) > 0) {
                $demoList[$key]->bdo_name = $demofeedbackBdo[0]->name;
                $demoList[$key]->bdo_feedbackImage = $demofeedbackBdo[0]->feedbackImage;
                $demoList[$key]->bdo_description = $demofeedbackBdo[0]->description;
                $demoList[$key]->bdo_rating = $demofeedbackBdo[0]->status;
            }
            else
            {   
                $demoList[$key]->bdo_name = "";
                $demoList[$key]->bdo_feedbackImage = "";
                $demoList[$key]->bdo_description = "";
                $demoList[$key]->bdo_rating = "";
            }
            
            //for supervisor
            $demofeedback2 = "select user_type from demofeedback as df where df.demo_request_id = '".$demoList[$key]->demo_request_id."' and df.user_type!='bdo' order by demofeedback_id desc limit 0,1";
            $demofeedback22  = collect(DB::select($demofeedback2));
            $supervisorTable = 'marketing_supervisor';
            $name = 'su.marketing_supervisor_name';
            $id = 'su.marketing_supervisor_id';
            $Suser_type = 'marketing_supervisor';
            if (count($demofeedback22) > 0) {
                if ($demofeedback22[0]->user_type == 'sales_area') {
                   $supervisorTable = 'sales_area_managers'; 
                   $name = 'su.sales_area_manager_name';
                   $id = 'su.sales_area_manager_id';
                   $Suser_type = 'sales_area';
                }
                elseif ($demofeedback22[0]->user_type == 'sales_regional') {
                   $supervisorTable = 'sales_regional_managers'; 
                   $name = 'su.sales_regional_manager_name';
                   $id = 'su.sales_regional_manager_id';
                   $Suser_type = 'sales_regional';
                }
                elseif ($demofeedback22[0]->user_type == 'zonal') {
                   $supervisorTable = 'zonal_managers';
                   $name = 'su.zonal_manager_name'; 
                   $id = 'su.zonal_manager_id';
                   $Suser_type = 'zonal';
                }
                elseif ($demofeedback22[0]->user_type == 'regional') {
                   $supervisorTable = 'regional_managers'; 
                   $name = 'su.regional_manager_name';
                   $id = 'su.regional_manager_id';
                   $Suser_type = 'regional';
                }
                elseif ($demofeedback22[0]->user_type == 'area') {
                   $supervisorTable = 'area_managers'; 
                   $name = 'su.area_manager_name';
                   $id = 'su.area_manager_id';
                   $Suser_type = 'area';
                }
                elseif ($demofeedback22[0]->user_type == 'marketing_supervisor') {
                   $supervisorTable = 'marketing_supervisor'; 
                   $name = 'su.marketing_supervisor_name';
                   $id = 'su.marketing_supervisor_id';
                   $Suser_type = 'marketing_supervisor';
                }
            }
            else
            {
                $supervisorTable = 'marketing_supervisor';
                $name = 'su.marketing_supervisor_name';
                $id = 'su.marketing_supervisor_id';
                $Suser_type = 'marketing_supervisor';
            }
            
            
            $demofeedback1 = "select ".$name." as name, CONCAT('".$base_url."/', df.feedbackImage) as feedbackImage, df.description, df.status from demofeedback as df LEFT JOIN ".$supervisorTable." as su ON df.user_id = '".$id."' where df.demo_request_id = '".$demoList[$key]->demo_request_id."' and user_type = '".$Suser_type."' order by demofeedback_id desc limit 0,1";
            
            $demofeedbackSupervisor  = collect(DB::select($demofeedback1));
            if (count($demofeedbackSupervisor)) {
                $demoList[$key]->supervisor_name = $demofeedbackSupervisor[0]->name;
                $demoList[$key]->supervisor_feedbackImage = $demofeedbackSupervisor[0]->feedbackImage;
                $demoList[$key]->supervisor_description = $demofeedbackSupervisor[0]->description;
                $demoList[$key]->supervisor_rating = $demofeedbackSupervisor[0]->status;
            }
            else
            {
                $demoList[$key]->supervisor_name = "";
                $demoList[$key]->supervisor_feedbackImage = "";
                $demoList[$key]->supervisor_description = "";
                $demoList[$key]->supervisor_rating = "";
            }
            
         }
         // print_r($demofeedbackBdo);die;
         // echo "<pre>"; print_r($demoList);die;
         return Datatables::of($demoList)->make();                             
         //return $distributorList;                             
    }

    public function postExportDemo(Request $request) { 
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }else{
            $role_sess      = \Session::get('login_role_id');
            $ddresult1 = "";
            if($role_sess == '8'){
                $userData = \DB::table('branch_admin')->where(['id' => $sess])->first();
                if(count($userData)>0) {
                    $zonal_manager_id = $userData->zonal_manager_id;
                    $ddData = DB::table('dhanuka_doctor')
                              ->where('zonal_manager_id',$zonal_manager_id)
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
                        $ddresult1 = implode("','", $ddresult);
                    }
                }
                else{
                    $ddresult1 = "";
                }
            }else{
                $ddresult1 = "";
            }
            
        }         
        $value          = Input::all();
        $base_url       = url('/');
        $base_url1 = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";

        $datesearch     = '';
        $sorting        = '';
        $start_date   = isset($value['startdate']) ? $value['startdate'] :'';
        $end_date       = isset($value['enddate']) ?  $value['enddate']:'';
        $search         =   $value['search'];
        $search_query=" (1=1)  ";

        if($start_date!='' && $end_date!='') {
            if($ddresult1 != '' && !empty($ddresult1)){
                $search_query.= " and DATE(d.created_at) >= DATE('".$start_date."') AND DATE(d.created_at) <= DATE('".$end_date."') and d.dd_id in ('".$ddresult1."')";    
            }
            else{
                $search_query.=" and DATE(d.created_at) >= DATE('".$start_date."') AND DATE(d.created_at) <= DATE('".$end_date."')";
            }
        }elseif($start_date!='') {
            if($ddresult1 != '' && !empty($ddresult1)){
                $search_query.= " and DATE(d.created_at) >= DATE('".$start_date."') and d.dd_id in ('".$ddresult1."')";    
            }
            else{
                $search_query.=" and DATE(d.created_at) >= DATE('".$start_date."')";
            }
        }
        else{   
            if($ddresult1 != '' && !empty($ddresult1)){
                $search_query.= " and d.dd_id in ('".$ddresult1."')";    
            }
        }


        // if($start_date!='' && $end_date!='') {
        //         $search_query.=" and DATE(d.created_at) >= DATE('".$start_date."') AND DATE(d.created_at) <= DATE('".$end_date."')";
        // }elseif($start_date!='') {
        //         $search_query.= " and DATE(d.created_at) >= DATE('".$start_date."')";
        // }
        //$base_url1       = url('/assets/demoimage');
        $base_url1 = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/demoimage";
        $demoList   =   " 
                        SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*                                
                        FROM
                            (select d.demo_request_id,d.created_at,dd.ECN,dd.name,c.crop,p.product,d.farmer_name, d.farmer_type,d.contact_number,v.village,t.tehsil,dis.district,s.state,d.pincode, d.latitude, d.longitude, CONCAT('".$base_url1."/', d.demoImage) as demoImage, d.demo_code, d.status, CASE 
                                    WHEN d.showing_date != 'N/A' THEN DATE_FORMAT(STR_TO_DATE(d.showing_date, '%d-%m-%Y'), '%Y-%m-%d')
                                    ELSE d.showing_date
                                END AS showing_date, DATE_FORMAT(d.updated_at,'%Y-%m-%d') as evaluation_date, total_demos.total_demo, ticketNos.ticket_no,DATE_FORMAT( d.created_at, '%Y-%m' ) AS month_year from demo_request as d 
                            LEFT JOIN crops as c ON d.crop_id = c.crop_id LEFT JOIN products as p ON d.product_id = p.product_id 
                            LEFT JOIN dhanuka_doctor as dd ON d.dd_id = dd.dd_id LEFT JOIN villages as v ON d.village_id = v.village_id 
                            LEFT JOIN states as s ON d.state_id = s.state_id LEFT JOIN tehsils as t ON d.tehsil_id = t.tehsil_id 
                            LEFT JOIN districts as dis ON d.district_id = dis.district_id
                            LEFT JOIN (SELECT demo_request_id, COUNT(*) AS total_demo FROM tickets GROUP BY demo_request_id ) AS  total_demos ON total_demos.demo_request_id = d.demo_request_id 
                            LEFT JOIN (SELECT GROUP_CONCAT(ticket_no SEPARATOR ' , ')  AS ticket_no, demo_request_id FROM tickets GROUP BY demo_request_id) AS  ticketNos ON ticketNos.demo_request_id = d.demo_request_id
                            WHERE 
                                ".$search_query." 
                            GROUP BY d.crop_id, d.product_id, d.dd_id, d.farmer_name, d.spray, month_year
                            ORDER BY d.demo_request_id DESC
                            ) A,
                            (select @a:=0) initvars";
         $demoList  = DB::select(DB::raw($demoList));
         //$base_url       = url('/assets/demofeedback');
         $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/demofeedback";
         foreach ($demoList as $key => $value) {
            unset($demoList[$key]->month_year);
            //for bdo
            $demofeedback = "select u.name as name, CONCAT('".$base_url."/', df.feedbackImage) as feedbackImage, df.description, df.status from demofeedback as df LEFT JOIN users as u ON df.user_id = u.id where df.demo_request_id = '".$demoList[$key]->demo_request_id."' order by demofeedback_id desc limit 0,1";
            $demofeedbackBdo  = collect(DB::select($demofeedback));
            if (count($demofeedbackBdo) > 0) {
                $demoList[$key]->bdo_name = $demofeedbackBdo[0]->name;
                $demoList[$key]->bdo_feedbackImage = $demofeedbackBdo[0]->feedbackImage;
                $demoList[$key]->bdo_description = $demofeedbackBdo[0]->description;
                $demoList[$key]->bdo_rating = $demofeedbackBdo[0]->status;
            }
            else
            {   
                $demoList[$key]->bdo_name = "";
                $demoList[$key]->bdo_feedbackImage = "";
                $demoList[$key]->bdo_description = "";
                $demoList[$key]->bdo_rating = "";
            }
            
            //for supervisor
            $demofeedback2 = "select user_type from demofeedback as df where df.demo_request_id = '".$demoList[$key]->demo_request_id."' order by demofeedback_id desc limit 0,1";
            $demofeedback22  = collect(DB::select($demofeedback2));
            $supervisorTable = 'marketing_supervisor';
            $name = 'su.marketing_supervisor_name';
            $id = 'su.marketing_supervisor_id';
            $Suser_type = 'marketing_supervisor';
            if (count($demofeedback22) > 0) {
                if ($demofeedback22[0]->user_type == 'sales_area') {
                   $supervisorTable = 'sales_area_managers'; 
                   $name = 'su.sales_area_manager_name';
                   $id = 'su.sales_area_manager_id';
                }
                elseif ($demofeedback22[0]->user_type == 'sales_regional') {
                   $supervisorTable = 'sales_regional_managers'; 
                   $name = 'su.sales_regional_manager_name';
                   $id = 'su.sales_regional_manager_id';
                   $Suser_type = 'sales_regional';
                }
                elseif ($demofeedback22[0]->user_type == 'zonal') {
                   $supervisorTable = 'zonal_managers';
                   $name = 'su.zonal_manager_name'; 
                   $id = 'su.zonal_manager_id';
                   $Suser_type = 'zonal';
                }
                elseif ($demofeedback22[0]->user_type == 'regional') {
                   $supervisorTable = 'regional_managers'; 
                   $name = 'su.regional_manager_name';
                   $id = 'su.regional_manager_id';
                   $Suser_type = 'regional';
                }
                elseif ($demofeedback22[0]->user_type == 'area') {
                   $supervisorTable = 'area_managers'; 
                   $name = 'su.area_manager_name';
                   $id = 'su.area_manager_id';
                   $Suser_type = 'area';
                }
                elseif ($demofeedback22[0]->user_type == 'marketing_supervisor') {
                   $supervisorTable = 'marketing_supervisor'; 
                   $name = 'su.marketing_supervisor_name';
                   $id = 'su.marketing_supervisor_id';
                   $Suser_type = 'marketing_supervisor';
                }
            }
            else
            {
                $supervisorTable = 'marketing_supervisor';
                $name = 'su.marketing_supervisor_name';
                $id = 'su.marketing_supervisor_id';
                $Suser_type = 'marketing_supervisor';
            }
            
            $demofeedback1 = "select ".$name." as name, CONCAT('".$base_url."/', df.feedbackImage) as feedbackImage, df.description, df.status from demofeedback as df LEFT JOIN ".$supervisorTable." as su ON df.user_id = '".$id."' where df.demo_request_id = '".$demoList[$key]->demo_request_id."' and user_type = '".$Suser_type."' order by demofeedback_id desc limit 0,1";
            $demofeedbackSupervisor  = collect(DB::select($demofeedback1));
            if (count($demofeedbackSupervisor)) {
                $demoList[$key]->supervisor_name = $demofeedbackSupervisor[0]->name;
                $demoList[$key]->supervisor_feedbackImage = $demofeedbackSupervisor[0]->feedbackImage;
                $demoList[$key]->supervisor_description = $demofeedbackSupervisor[0]->description;
                $demoList[$key]->supervisor_rating = $demofeedbackSupervisor[0]->status;
            }
            else
            {
                $demoList[$key]->supervisor_name = "";
                $demoList[$key]->supervisor_feedbackImage = "";
                $demoList[$key]->supervisor_description = "";
                $demoList[$key]->supervisor_rating = "";
            }
            
         }
         // print_r($demofeedbackBdo);die;
         //print_r($demoList);die;
         //return Datatables::of($demoList)->make();     
            //print_r($result);
         $result         = array_map(function ($value) {
                                                    return (array)$value;
                                                }, $demoList);
                    
            $ticket_column  = array(
                                    'Sr. No',
                                    'demo_request_id',
                                    'Creation Time',
                                    'DD ECN',
                                    'DD Name',
                                    'Crop Name',
                                    'Product',
                                    'Farmer Name',
                                    'Farmer Type',
                                    'Contact Number',
                                    'Village',
                                    'Tehshil',
                                    'District',
                                    'State',
                                    'Pincode',
                                    'Latitude',
                                    'Longitude',
                                    'DD Demo Upload',
                                    'Demo Number',
                                    'Evaluation Status',
                                    'Results Expected By',
                                    'Evaluation Date',
                                    'Ticket Count',
                                    'Ticket Numbers',
                                    'FL Name',
                                    'FL Upload',
                                    'FL Feedback',
                                    'FL Rating',
                                    'Supervisor',
                                    'Supervisor Upload',
                                    'Supervisor Feedback',
                                    'Supervisor Rating',
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
                            'name' => "demo List.xlsx", //no extention needed
                            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                         );
            return response()->json($response);
    }

    public function getSeasonList()
    {
        return view('backend.demo.seasonlist');       
    }

    public function postSeasonList(){
        
        // $seasonList="SELECT id, season_name FROM seasons";
        //  $seasonList  = collect(DB::select($seasonList));
        //  return Datatables::of($seasonList)->make();

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
       
            $sorting    = '';
            $query      = "SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT
                                    s.season_name,s.id
                                FROM        
                                    seasons as s
                                WHERE s.status = '1'
                                )   A,
                                (select @a:=0) initvars";

            $seasonlist  = collect(DB::select($query)); 
            // echo "<pre>";  print_r($seasonlist); die;
            return Datatables::of($seasonlist)->make();
                           
    }


    //season-delete
    public function postSeasonDelete(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();
                $id     = $my_data['_ticket'];
                $update_product = DB::table('seasons')
                                    ->where('id', $id)
                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_product) {
                    echo 1;
                } else {
                    echo 2;
                }
           
        }
    } 

    public function getAddSeason()
    {
       $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $state_list     = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
        return View::make('backend.demo.addseason', array('state_list'=>$state_list));    
    }

    public function postCreateSeason(Request $request){
        $date = date('Y-m-d H:i:s');
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= $request->all();        
            
            $season_id = DB::table('seasons')->insertGetId(['season_name'=>$my_data['season_name'],'status'=>'1','updated_at'=>$date]);
            if ($season_id) 
            {   
                if (isset($my_data['village'])) {
                    $tehsil       = $my_data['tehsil'];
                    $village      = $my_data['village'];
                    $state_id     = $my_data['state'];
                    $district_id  = $my_data['district'];
                    $village_data = array();
                    foreach($village AS $village_id) {
                        $village_data[] = array(
                                                'season_id' => $season_id,
                                                'village_id' => $village_id,
                                                'state_id' => $state_id,
                                                'district_id' => $district_id,
                                                'tehsil_id' => $tehsil
                                                );
                    }
                    $insert_village_data = DB::table('season_villages')->insert($village_data); 
                }
                Session::flash('message', "Season Successfully Created.");
                return redirect('demo/add-season');
            }
            else
            {
                Session::flash('message', "Please try again.");
                return redirect('demo/add-season');
            }
        }else{
            return redirect('demo/add-season');
            
        }

    }

    public function getEditSeason($id){
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        $season = DB::table('seasons')->where('id', $id)->first();//print_r($season);die;
        if(isset($season) && !empty($season)) {
            $season_village = DB::table('season_villages')->select('state_id','tehsil_id','district_id')->where('season_id',$season->id)->first();
            if (isset($season_village) && !empty($season_village)) 
            {
                $state_list  = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                $district_list  = District::where('status', '1')->select('district_id', 'district')->where(['state_id' => $season_village->state_id])->orderBy('district', 'asc')->get();
                $tehsil_list  = Tehsil::where('status', '1')->select('tehsil_id', 'tehsil')->where(['district_id' => $season_village->district_id])->orderBy('tehsil', 'asc')->get();
                $village_list = Village::where('status', '1')->select('village_id', 'village')->where(['tehsil_id' => $season_village->tehsil_id])->orderBy('village', 'asc')->get();
                $season_village_list = DB::table('season_villages')->select(DB::raw("GROUP_CONCAT(village_id SEPARATOR ',')  AS ddvillage_id"))->where('season_id',$season->id)->first();
                return view('backend.demo.editseason', array(   'state_list'      => $state_list,
                                                                'district_list'   => $district_list,
                                                                'tehsil_list'     => $tehsil_list,
                                                                'village_list'    => $village_list,
                                                                'dd_village_list' => $season_village_list,
                                                                'userdata' => $season_village,
                                                                'season'          => $season )
                                                            );
            }
            else
            {   
                $state_list  = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
                return view('backend.demo.editseason', array(   'state_list'      => $state_list,
                                                                'district_list'   => [],
                                                                'tehsil_list'     => [],
                                                                'village_list'    => [],
                                                                'dd_village_list' => [],
                                                                'userdata' => [],
                                                                'season'          => $season )
                                                            );
            }
            
        }
        
    }

    public function postUpdateSeason(Request $request){
        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= $request->all();
            $seasonId=$request->input('seasonId');
            if ($seasonId) 
            {   
                $season_data = DB::table('seasons')->where('id',$seasonId)->update(['season_name'=>$my_data['season_name'],'updated_at'=>date("Y-m-d H:i:s")]);
                if (isset($season_data) && !empty($season_data)) {
                    if (isset($my_data['village'])) { 
                        $tehsil       = $my_data['tehsil'];
                        $village      = $my_data['village'];
                        $state_id     = $my_data['state'];
                        $district_id  = $my_data['district'];
                        DB::table('season_villages')->where('season_id',$seasonId)->delete();
                        $village_data = array();
                        foreach($village AS $village_id) {
                            $village_data[] = array(
                                                    'season_id' => $seasonId,
                                                    'village_id' => $village_id,
                                                    'state_id' => $state_id,
                                                    'district_id' => $district_id,
                                                    'tehsil_id' => $tehsil
                                                    );
                        }
                        $insert_village_data = DB::table('season_villages')->insert($village_data); 
                    }
                }
                
                Session::flash('message', "Season Successfully Updated.");
                return redirect('demo/edit-season/'.$seasonId);
            }
            else
            {
                Session::flash('message', "Please try again.");
                return redirect('demo/edit-season/'.$seasonId);
            }
        }else{
            return redirect('demo/edit-season/'.$seasonId);
            
        }

    }

    //add-message
	public function getMessage()
    {
       $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $cropList="SELECT crop_id,crop FROM  `crops` WHERE  `status` =  '1'"; 
        $cropList  = collect(DB::select($cropList));
        $language="SELECT id,language FROM  `languages`"; 
          $language  = collect(DB::select($language));
        $stateList="SELECT state_id,state FROM  `states`"; 
        $stateList  = collect(DB::select($stateList));
        return View::make('backend.message.addmessage', compact('cropList','language','stateList'));    
    }


    public function postCreateMessage(Request $request){
        $date = date('Y-m-d H:i:s');
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= $request->all();        
            
            DB::table('message_history')->insert(['crop'=>$my_data['crop'],'product'=>$my_data['product'],'state'=>$my_data['state'],'language'=>$my_data['language'],'message'=>$my_data['message'],'updated_at'=>$date]);
            Session::flash('message', "Message Successfully Created.");
            return redirect('demo/message');
            
        }else{
            return redirect('demo/message');
            
        }

    }


    //history-message
    public function getHistoryMessage()
    {
       $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $message="SELECT m.*,c.crop,p.product,l.language,s.state FROM  `message_history` as m LEFT JOIN crops as c ON m.crop = c.crop_id LEFT JOIN products as p ON m.product = p.product_id LEFT JOIN languages as l ON m.language = l.id LEFT JOIN states as s ON m.state = s.state_id ORDER BY m.id desc"; 
        $message  = collect(DB::select($message));
       
        return View::make('backend.message.historymessage', compact('message'));    
    }

    public function getHistoryFilter($search)
    {
        if($search == 'all')
        {
            $message = "SELECT m.*,c.crop,p.product,l.language FROM  `message_history` as m LEFT JOIN crops as c ON m.crop = c.crop_id LEFT JOIN products as p ON m.product = p.product_id LEFT JOIN languages as l ON m.language = l.id LEFT JOIN states as s ON m.state = s.state_id ORDER BY m.id desc";
        }
        else
        {
             $message = "SELECT m.*,c.crop,p.product,l.language FROM  `message_history` as m LEFT JOIN crops as c ON m.crop = c.crop_id LEFT JOIN products as p ON m.product = p.product_id LEFT JOIN languages as l ON m.language = l.id LEFT JOIN states as s ON m.state = s.state_id WHERE (c.crop LIKE '%$search%' OR p.product LIKE '%$search%' OR l.language LIKE '%$search%') ORDER BY m.id desc";
        }
      
       $message  = collect(DB::select($message));
       $data = '';
       foreach($message as $key => $value)
       {
        $normal_farmer = $value->normal_farmer;
                        $register_farmer = $value->register_farmer;
                        $normalfarmersArr = array();
                        $registerfarmersArr = array();
                        if($normal_farmer != ''){
                           $normalfarmersArr =  explode(',', $normal_farmer);
                        }
                         if($register_farmer != ''){
                           $registerfarmersArr =  explode(',', $register_farmer);
                        }
                        $totalFarmer = array_merge($normalfarmersArr,$registerfarmersArr);
                   $data  .=  '<div class="message_view"> 
                        <div class="row">
                            <div class="col-md-8">
                                <div class="col-md-3">
                                    <p><strong>Crop</strong></p>
                                    <p>'.$value->crop.'</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Product</strong></p>
                                    <p>'.$value->product.'</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>State</strong></p>
                                    <p>'.$value->state.'</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Language</strong></p>
                                    <p>'.$value->language.'</p>
                                </div>
                            </div>
                            </div>
        <div class="row">
        <div class="col-md-12">';
            if(count($totalFarmer) > 0)  {
              $data  .= '<h3>'.count($totalFarmer); 
                if(count($totalFarmer) == 1)
                {
               $data  .= ' Farmer';
                    }
                else
                {
                $data  .= ' Farmers';
               }
               $data  .= '<a target="_blank" href="'.url('demo/view-farmer-history').'/'.$value->id.'"> <i class="fa fa-share"> </i></a></h3>';
               }  
                             
        $data  .='</div>
        </div>
                             
                            <div class="row">
                                <div class="col-md-11">
                                    <p style="margin-left: 15px;">'.$value->message.'</p>
                                </div>
                                <div class="col-md-1">
                                <p style="text-align: right;">
                                    <a href="'.url('demo/edit-message').'/'.$value->id.'"><i class="fa fa-pencil"></i></a>
                                </p>
                                </div>
                            </div>
                    </div>';
                }
                echo $data;

    }

    public function getEditMessage($id)
    {
       $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $cropList="SELECT crop_id,crop FROM  `crops` WHERE  `status` =  '1'"; 
        $cropList  = collect(DB::select($cropList));
        $language="SELECT id,language FROM  `languages`"; 
          $language  = collect(DB::select($language));
        $stateList="SELECT state_id,state FROM  `states`"; 
        $stateList  = collect(DB::select($stateList));
         $message="SELECT m.*,p.* FROM  `message_history` as m LEFT JOIN products as p ON m.product = p.product_id  WHERE id = $id"; 
          $message  = collect(DB::select($message));
        return View::make('backend.message.editmessage', compact('cropList','language','stateList','message'));    
    }

    public function postUpdateMessage(Request $request){
        $date = date('Y-m-d H:i:s');
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= $request->all();  
           // echo "<pre>";       print_r($my_data); die;
            
            DB::table('message_history')->where('id', $my_data['id'])->update(['crop'=>$my_data['crop'],'product'=>$my_data['product'],'state'=>$my_data['state'],'language'=>$my_data['language'],'message'=>$my_data['message'],'updated_at'=>$date]);
            Session::flash('message', "Message Successfully Updated.");
            return redirect('demo/edit-message/'.$my_data['id']);
            
        }else{
            return redirect('demo/message/'.$my_data['id']);
            
        }

    }

    public function getViewFarmerHistory($id)
    {
        $totalFarmer = array();
        $farmer = "SELECT  m.id,vf.farmer_id as farmer_id,vf.contact_number as contact_number, vf.farmer_name as farmer FROM  message_history m  INNER JOIN village_farmers vf
            ON FIND_IN_SET(vf.farmer_id, m.normal_farmer) > 0 WHERE id = $id";
            $farmer  = collect(DB::select($farmer));

            foreach ($farmer as $key => $value) {
                array_push($totalFarmer, $value);
                
            }


            $farmer1 = "SELECT  m.id,vf.farmer_id as farmer_id,vf.mobile as contact_number, vf.farmer_name as farmer FROM  message_history m  INNER JOIN farmers vf ON FIND_IN_SET(vf.farmer_id, m.register_farmer) > 0 WHERE id = $id";
             $farmer1  = collect(DB::select($farmer1));
             foreach ($farmer1 as $key1 => $value1) {
                array_push($totalFarmer, $value1);
                
            }
            
             return View::make('backend.message.viewfarmer', compact('totalFarmer'));    
    }

    public function getDevices() {         
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.demo.devices');
    }  

    public function postDevicesprocess(Request $request)
    {
        $value = Input::all();
        $start_date     = $value['start_date'];
        $end_date       = $value['end_date'];
        $datesearch     = "(1=1)";

        if($start_date!='' && $end_date!='') {
            $datesearch .= "AND DATE(d.created_at) >= DATE('".$start_date." 00:00:00') AND DATE(d.created_at) <= DATE('".$end_date." 23:59:59')";
        }
        else {
            $datesearch = "(1=1)";
        }
        $devicelist="
                    SELECT CONCAT_WS(
                                        '\n', 
                                        NULLIF(zm.zonal_manager_name, ''),
                                        NULLIF(rm.regional_manager_name, ''),
                                        NULLIF(am.area_manager_name, ''),
                                        NULLIF(srm.sales_regional_manager_name, ''),
                                        NULLIF(sam.sales_area_manager_name, ''),
                                        NULLIF(u.name, ''),
                                        NULLIF(dd.name, '')
                                    ) AS name,
                                CONCAT_WS(
                                            '\n', 
                                            NULLIF(zm.ECN, ''),
                                            NULLIF(rm.ECN, ''),
                                            NULLIF(am.ECN, ''),
                                            NULLIF(srm.ECN, ''),
                                            NULLIF(sam.ECN, ''),
                                            NULLIF(u.ECN, ''),
                                            NULLIF(dd.ECN, '')
                                        ) AS ECN,
                                d.user_type,
                                d.device_id,
                                d.device_type, 
                                d.app_version,
                                CONCAT_WS(
                                            '\n', 
                                            NULLIF(d.lat_location, ''),
                                            NULLIF(d.long_location, '')
                                        ) AS location,
                                (CASE   WHEN d.updated_at<>'0000-00-00 00:00:00' 
                                                THEN 
                                                    DATE_FORMAT(d.updated_at, '%Y-%m-%d, %h:%i %p')
                                                ELSE 
                                                    DATE_FORMAT(d.created_at, '%Y-%m-%d, %h:%i %p') 
                                        END)
                                
                                
                                FROM device_info   d
                                LEFT JOIN   users  u   ON d.user_id=u.id AND d.user_type='bdo'
                                LEFT JOIN   zonal_managers          zm  ON d.user_id=zm.zonal_manager_id AND d.user_type='zonal'
                                LEFT JOIN   regional_managers       rm  ON d.user_id=rm.regional_manager_id AND d.user_type='regional'
                                LEFT JOIN   area_managers           am  ON d.user_id=am.area_manager_id AND d.user_type='area'
                                LEFT JOIN   sales_regional_managers srm ON d.user_id=srm.sales_regional_manager_id AND d.user_type='sales_regional'
                                LEFT JOIN   sales_area_managers     sam ON d.user_id=sam.sales_area_manager_id AND d.user_type='sales_area'
                                LEFT JOIN   dhanuka_doctor dd ON d.user_id=dd.dd_id AND d.user_type='dhanuka_doctor' WHERE 
                                ".$datesearch." order by d.id desc";
          //echo $devicelist;die;               
         $devicelist  = collect(DB::select($devicelist));
         
        //echo "<pre>";print_r($devicelist);die;
         return Datatables::of($devicelist)->make();                             
                                     
    
    }


    public function getDeviceFilter($type)
    {
        $device="SELECT * FROM  `device_info` WHERE user_type = '".$type."' ORDER BY id desc"; 
        $device  = collect(DB::select($device));
        $deviceArr = array(); 
        $html = ''; 
        if(count($device) > 0)
        {
          foreach ($device as $key => $value) {
            if($value->user_type == 'bdo')
            {
            $user = DB::table('users')->where('id',$value->user_id)->select('ECN','name as name')->get();
            }
            if($value->user_type == 'dhanuka_doctor')
            {
            $user = DB::table('dhanuka_doctor')->where('dd_id',$value->user_id)->select('ECN','name as name')->get();
            }
            if($value->user_type == 'sales_area')
            {
            $user = DB::table('sales_area_managers')->where('sales_area_manager_id',$value->user_id)->select('ECN','sales_area_manager_name as name')->get();
            } if($value->user_type == 'sales_regional')
            {
            $user = DB::table('sales_regional_managers')->where('sales_regional_manager_id',$value->user_id)->select('ECN','sales_regional_manager_name as name')->get();
            }if($value->user_type == 'area')
            {
            $user = DB::table('area_managers')->where('area_manager_id',$value->user_id)->select('ECN','area_manager_name as name')->get();
            }
            if($value->user_type == 'regional')
            {
            $user = DB::table('regional_managers')->where('regional_manager_id',$value->user_id)->select('ECN','regional_manager_name as name')->get();
            }
            if($value->user_type == 'zonal')
            {
            $user = DB::table('zonal_managers')->where('zonal_manager_id',$value->user_id)->select('ECN','zonal_manager_name as name')->get();
            }
            $name = $user[0]->name;
            $ECN = $user[0]->ECN;
            $sno = $key+1;

                    $html .= '<tr><td>'.$sno.'</td>
                    <td>'.$name.'</td>
                    <td>'.$ECN.'</td>
                    <td>'.$value->user_type.'</td>
                    <td>'.$value->device_id.'</td>
                    <td>'.$value->device_type.'</td>
                    <td>'.$value->lat_location.','.$value->long_location.'</td>
                    <td>'.$value->updated_at.'</td></tr>';
         }  
        }
        else
        {
              $html .= '<tr><td></td>
                    <td></td>
                    <td></td>
                    <td>No Record Found</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td></tr>';
        }
        $html .= '</tr>';
        echo $html; 
         
       
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

    
}
