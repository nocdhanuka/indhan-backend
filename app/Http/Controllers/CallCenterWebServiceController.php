<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Input;
use Redirect;
use Response;
use Mail;
use Session;
use Cookie;
use Auth;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use Illuminate\Cookie\CookieJar;
use Illuminate\Routing\Route;
use App\Traits\ExampleCode;
use App\models\notification;
use App\models\dailyrecommendation;
use App\models\profilestrength;
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
use App\Product;
use App\Crop;
use App\ProductCropRelation;
use App\ProductCropCategory;
use App\Ticket;
use App\Farmer;
use App\PlanActivities;
use App\PlanAtp;
use App\SelfParticipation;
use App\Testimonial;
use App\CogentRecord;
use App\CustomerQuery;
use DateTime;
use \Illuminate\Http\Response as Res;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use App\Repository\Transformers\UserTransformer;

ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);

class CallCenterWebServiceController extends ApiController {

    protected $userTransformer;
    protected $api_token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjlmZGIwZGM0MzgyZjI4MzNjZTJkMzk5M2M2NzBmYWZiNWE3ZTdiODhhZGE4NWY0OTBhYmI5MGFjMjExODAyNzIwYTBmYzczOTJjM2YyZTdjIn0.eyJhdWQiOiIyIiwianRpIjoiOWZkYjBkYzQzODJmMjgzM2NlMmQzOTkzYzY3MGZhZmI1YTdlN2I4OGFkYTg1ZjQ5MGFiYjkwYWMyMTE4MDI3MjBhMGZjNzM5MmMzZjJlN2MiLCJpYXQiOjE0ODc4NTU2MTYsIm5iZiI6MTQ4Nzg1NTYxNiwiZXhwIjoxNDg3OTQyMDE2LCJzdWIiOiIxIiwic2NvcGVzIjpbIioiXX0.sdHNQ_bDGtO3lGBwtmlZfkSdYkaUsYSabaY93894Fw4l3z_wU5d7xsfNj6LoK8lPXBTFAhoFf7SjtuQ0F_T6lst0ADwEmc_fLTH1dsTkHq6BdOLr3Ur6dLrbYOqihHG0FhJ6HaUtanbRBBeuK33aVUwuUkCwVkrLgxK6OYEwcq97aU1Cuy3jHOfOB88VAuJ42kwtScpXzi0Oo6zlCxyGAmBlEMWdcSBEkI4vRR-cJdaWsRVZ--671i1Gw5jOhXtN0HS_HMNSf9WWgiUDAyhe1mvlW4eZbTDfg2Al34YauJ_rpPSCw6vwnvpqp9Yvh9Vl0r1k8o_bm_2-NjiVINqdRALmaORbFGu2S9CJso5Jcio_jrWSzznIkf6Jopw9ar8Ca_Cf_KV6cmLOOx2XgWXV7WMCkSIPQXFXrtxE4hnfXEd8K5S5HLgch0hPgJPpgL91x1i73MP1gQyYoWNFCip9cDDlDvPO1zG28O12c4H4bsdy1jaaOOOv34yECfgA87RIFCbGtrzd_9QBXDse51L1IFJu8B6sotyk_4QcGcKSWzOPjI8L2_xhPIG54qk0lzqNWeVHo2UbNpBB6u2OJnszKC44BH5hneYoTbqMGK_1Uo2jR14XixvBlZydXXquuTmDVoBj2ygfjlnZ6UWJenQIO_bLEkF2iaWcgbOhJ6S197k";

    public function __construct(userTransformer $userTransformer) {
        $this->userTransformer = $userTransformer;
    }

    public function ticketNumberList(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'cc_token' => 'required|string',
                'closed_date' => 'date',
                'start_date'  => 'date',
                'end_date'    => 'date'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $curTime = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "cc/getTicketNumbers",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);

                $cc_token = $data['cc_token'];
                $closed_date = isset($data['closed_date']) ? $data['closed_date'] : '';
                $start_date = isset($data['start_date']) ? $data['start_date'] : '';
                $end_date = isset($data['end_date']) ? $data['end_date'] : '';
                if ($this->api_token === $cc_token) {
                    if ($closed_date != '') {
                        $valid_user_plan = array();
                        $current_month = date('m');
                        $current_year = date('Y');
                        $get_ticket_query = "
                                                SELECT T.ticket_no AS ticket_id, T.user_id, T.user_type, COUNT(F.farmer_id) AS registered_numbers
                                                FROM        tickets     T
                                                LEFT JOIN   farmers     F   ON T.ticket_id=F.ticket_id
                                                WHERE (T.status='2'     AND
                                                    T.ticket_status='2') AND
                                                    F.ticket_status='1' AND 
                                                    DATE_FORMAT(T.closing_datetime,'%Y-%m-%d') = '" . $closed_date . "'
                                                GROUP BY
                                                    F.ticket_id
                                                ORDER BY 
                                                    T.ticket_no ASC
                                                "; 
                                                //echo $get_ticket_query;die;
                                                //DATE(T.closing_datetime)=DATE('" . $closed_date . "')
                        // T.status='2'        AND
                        // DATE(T.created_at)=DATE('" . $closed_date . "')
                        $get_ticket_data = DB::select(DB::raw($get_ticket_query));
                        foreach ($get_ticket_data as $key => $value) {
                            if($get_ticket_data[$key]->user_type == 'bdo')
                            {
                                $bdodata = DB::table('users')
                                           ->where('id',$get_ticket_data[$key]->user_id)
                                           ->select('name')
                                           ->get();
                                
                                if (count($bdodata)>0) {
                                  $get_ticket_data[$key]->name = $bdodata[0]->name;
                                }else{
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'dhanuka_doctor') {
                                $dddata = DB::table('dhanuka_doctor')
                                           ->join('users','users.id','=','dhanuka_doctor.bdo_id','left')
                                           ->where('dd_id',$get_ticket_data[$key]->user_id)
                                           ->select('users.name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->name;
                                }else{
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'zonal') {
                                $dddata = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('zonal_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->zonal_manager_name;
                                }else{
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'area') {
                                $dddata = DB::table('area_managers')
                                           ->where('area_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('area_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->area_manager_name;
                                }else{
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'sales_regional') {
                                $dddata = DB::table('sales_regional_managers')
                                           ->where('sales_regional_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('sales_regional_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->sales_regional_manager_name;
                                }else{
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'sales_area') {
                                $dddata = DB::table('sales_area_managers')
                                           ->where('sales_area_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('sales_area_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->sales_area_manager_name;
                                }else{
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'regional') {
                                $dddata = DB::table('regional_managers')
                                           ->where('regional_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('regional_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->regional_manager_name;
                                }else{
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'marketing_supervisor') {
                                $dddata = DB::table('marketing_supervisor')
                                           ->where('marketing_supervisor_id',$get_ticket_data[$key]->user_id)
                                           ->select('marketing_supervisor_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->marketing_supervisor_name;
                                }else{
                                  $get_ticket_data[$key]->name = "";
                                }
                            }

                        }

                        foreach ($get_ticket_data as $key => $value) {
                           unset($get_ticket_data[$key]->user_type);
                           unset($get_ticket_data[$key]->user_id);
                        }

                        $get_ticket_data_list = array_map(function ($value) {
                            return (array) $value;
                        }, $get_ticket_data);
                
                        if ($get_ticket_data_list) {
                            $data = array('ticket_data_list' => $get_ticket_data_list);
                            $email = \Config::get('constants.options.option_email');
                            $message = \Config::get('constants.options.option_config');

                            $log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Ticket List Found!',
                                        'data' => $data
                            ]);
                        } else {
                            return $this->respondWithError("No Ticket Found!");
                        }
                    }
                    elseif ($start_date != '' && $end_date != '') 
                    {
                        $valid_user_plan = array();
                        $current_month = date('m');
                        $current_year = date('Y');
                        $get_ticket_query = "
                                                SELECT T.ticket_no AS ticket_id, T.user_id, T.user_type, COUNT(F.farmer_id) AS registered_numbers
                                                FROM        tickets     T
                                                LEFT JOIN   farmers     F   ON T.ticket_id=F.ticket_id
                                                WHERE (T.status='2'        AND
                                                    T.ticket_status='2') AND
                                                    F.ticket_status='1' AND
                                                    DATE_FORMAT(T.closing_datetime,'%Y-%m-%d') Between '" . $start_date . "' AND '" . $end_date . "'
                                                GROUP BY
                                                    F.ticket_id
                                                ORDER BY 
                                                    T.ticket_no ASC
                                                ";
                                                //echo $get_ticket_query;die;
                                                //DATE(T.closing_datetime)=DATE('" . $closed_date . "')
                        // T.status='2'        AND
                        // DATE(T.created_at)=DATE('" . $closed_date . "')
                        $get_ticket_data = DB::select(DB::raw($get_ticket_query));
                        foreach ($get_ticket_data as $key => $value) {
                            if($get_ticket_data[$key]->user_type == 'bdo')
                            {
                                $bdodata = DB::table('users')
                                           ->where('id',$get_ticket_data[$key]->user_id)
                                           ->select('name')
                                           ->get();
                                if (count($bdodata)>0) {
                                  $get_ticket_data[$key]->name = $bdodata[0]->name;
                                }else{
                                  $get_ticket_data[$key]->name = "";
                                }
                                
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'dhanuka_doctor') {
                                $dddata = DB::table('dhanuka_doctor')
                                           ->join('users','users.id','=','dhanuka_doctor.bdo_id','left')
                                           ->where('dd_id',$get_ticket_data[$key]->user_id)
                                           ->select('users.name')
                                           ->get();
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'zonal') {
                                $dddata = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('zonal_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                 $get_ticket_data[$key]->name = $dddata[0]->zonal_manager_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'area') {
                                $dddata = DB::table('area_managers')
                                           ->where('area_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('area_manager_name')
                                           ->get();
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->area_manager_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'sales_regional') {
                                $dddata = DB::table('sales_regional_managers')
                                           ->where('sales_regional_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('sales_regional_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->sales_regional_manager_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'sales_area') {
                                $dddata = DB::table('sales_area_managers')
                                           ->where('sales_area_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('sales_area_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->sales_area_manager_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'regional') {
                                $dddata = DB::table('regional_managers')
                                           ->where('regional_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('regional_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->regional_manager_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'marketing_supervisor') {
                                $dddata = DB::table('marketing_supervisor')
                                           ->where('marketing_supervisor_id',$get_ticket_data[$key]->user_id)
                                           ->select('marketing_supervisor_name')
                                           ->get();
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->marketing_supervisor_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }

                        }

                        foreach ($get_ticket_data as $key => $value) {
                           unset($get_ticket_data[$key]->user_type);
                           unset($get_ticket_data[$key]->user_id);
                        }

                        $get_ticket_data_list = array_map(function ($value) {
                            return (array) $value;
                        }, $get_ticket_data);
                
                        if ($get_ticket_data_list) {
                            $data = array('ticket_data_list' => $get_ticket_data_list);
                            $email = \Config::get('constants.options.option_email');
                            $message = \Config::get('constants.options.option_config');

                            $log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Ticket List Found!',
                                        'data' => $data
                            ]);
                        } else {
                            return $this->respondWithError("No Ticket Found!");
                        }
                    } 
                    else 
                    {
                        $valid_user_plan = array();
                        $current_month = date('m');
                        $current_year = date('Y');
                        $get_ticket_query = "
                                                SELECT T.ticket_no AS ticket_id, T.user_id, T.user_type, COUNT(F.farmer_id) AS registered_numbers
                                                FROM        tickets     T
                                                LEFT JOIN   farmers     F   ON T.ticket_id=F.ticket_id
                                                WHERE (T.status='2'        AND
                                                    T.ticket_status='2') AND
                                                    F.ticket_status='1' AND
                                                    (
                                                    DATE(T.closing_datetime)=DATE(CURRENT_DATE())  OR
                                                    DATE(T.closing_datetime)=DATE(CURRENT_DATE()-1)
                                                    )


                                                GROUP BY
                                                    F.ticket_id
                                                ORDER BY 
                                                    T.ticket_no ASC
                                                ";
                        $get_ticket_data = DB::select(DB::raw($get_ticket_query));
                        foreach ($get_ticket_data as $key => $value) {
                            if($get_ticket_data[$key]->user_type == 'bdo')
                            {
                                $bdodata = DB::table('users')
                                           ->where('id',$get_ticket_data[$key]->user_id)
                                           ->select('name')
                                           ->get();
                                if (count($bdodata)>0) {
                                  $get_ticket_data[$key]->name = $bdodata[0]->name;
                                }else{
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'dhanuka_doctor') {
                                $dddata = DB::table('dhanuka_doctor')
                                           ->join('users','users.id','=','dhanuka_doctor.bdo_id','left')
                                           ->where('dd_id',$get_ticket_data[$key]->user_id)
                                           ->select('users.name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'zonal') {
                                $dddata = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('zonal_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->zonal_manager_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'area') {
                                $dddata = DB::table('area_managers')
                                           ->where('area_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('area_manager_name')
                                           ->get();
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->area_manager_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'sales_regional') {
                                $dddata = DB::table('sales_regional_managers')
                                           ->where('sales_regional_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('sales_regional_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->sales_regional_manager_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'sales_area') {
                                $dddata = DB::table('sales_area_managers')
                                           ->where('sales_area_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('sales_area_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->sales_area_manager_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'regional') {
                                $dddata = DB::table('regional_managers')
                                           ->where('regional_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('regional_manager_name')
                                           ->get();
                                
                                if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->regional_manager_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'marketing_supervisor') {
                                $dddata = DB::table('marketing_supervisor')
                                           ->where('marketing_supervisor_id',$get_ticket_data[$key]->user_id)
                                           ->select('marketing_supervisor_name')
                                           ->get();
                                
                                 if (count($dddata)>0) {
                                  $get_ticket_data[$key]->name = $dddata[0]->marketing_supervisor_name;
                                }
                                else
                                {
                                  $get_ticket_data[$key]->name = "";
                                }
                            }

                        }

                        foreach ($get_ticket_data as $key => $value) {
                           unset($get_ticket_data[$key]->user_type);
                           unset($get_ticket_data[$key]->user_id);
                        }
                        $get_ticket_data_list = array_map(function ($value) {
                            return (array) $value;
                        }, $get_ticket_data);

                        if ($get_ticket_data_list) {
                            $data = array('ticket_data_list' => $get_ticket_data_list);

                            $log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Ticket List Found!',
                                        'data' => $data
                            ]);
                        } else {
                            return $this->respondWithError("No Ticket Found!");
                        }
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function otherTicketNumberList(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'cc_token' => 'required|string',
                'closed_date' => 'date',
                'start_date'  => 'date',
                'end_date'    => 'date'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $curTime = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "cc/getOtherTicketNumbers",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);

                $cc_token   = $data['cc_token'];
                $closed_date= isset($data['closed_date']) ? $data['closed_date'] : '';
                $start_date = isset($data['start_date']) ? $data['start_date'] : '';
                $end_date   = isset($data['end_date']) ? $data['end_date'] : '';

                if ($this->api_token === $cc_token) {
                    if ($closed_date != '') {
                        $valid_user_plan = array();
                        $current_month = date('m');
                        $current_year = date('Y');
                        $get_ticket_query = "
                                                SELECT T.ticket_no AS ticket_id, COUNT(F.farmer_id) AS registered_numbers
                                                FROM        tickets     T
                                                LEFT JOIN   farmers     F   ON T.ticket_id=F.ticket_id
                                                WHERE (T.status='2' AND T.dd_ticket_role='0' AND
                                                    T.ticket_status='2') OR (T.status='0'        AND
                                                    T.ticket_status='0') AND
                                                    DATE(T.closing_datetime)=DATE('" . $closed_date . "')
                                                GROUP BY
                                                    F.ticket_id
                                                ORDER BY 
                                                    T.ticket_no ASC
                                                ";
                        $get_ticket_data = DB::select(DB::raw($get_ticket_query));
                        $get_ticket_data_list = array_map(function ($value) {
                            return (array) $value;
                        }, $get_ticket_data);
                
                        if ($get_ticket_data_list) {
                            $data = array('ticket_data_list' => $get_ticket_data_list);
                            $email = \Config::get('constants.options.option_email');
                            $message = \Config::get('constants.options.option_config');

                            $log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Ticket List Found!',
                                        'data' => $data
                            ]);
                        } else {
                            return $this->respondWithError("No Ticket Found!");
                        }
                    } elseif ($start_date != '' && $end_date != '') {
                        $end_date       = date("Y-m-d", strtotime($end_date . " +1 day"));
                        $valid_user_plan= array();
                        $current_month  = date('m');
                        $current_year   = date('Y');
                        $get_ticket_query = "
                                            SELECT T.ticket_no AS ticket_id, COUNT(F.farmer_id) AS registered_numbers
                                            FROM        tickets     T
                                            LEFT JOIN   farmers     F   ON T.ticket_id=F.ticket_id
                                            WHERE T.status IN('2', '0') AND T.dd_ticket_role='0' AND
                                                T.ticket_status IN('2', '0') AND
                                                T.closing_datetime >= '" . $start_date . "' AND
                                                T.closing_datetime < '" . $end_date . "'
                                            GROUP BY
                                                F.ticket_id
                                            ORDER BY 
                                                T.closing_datetime DESC";
                        $get_ticket_data = DB::select(DB::raw($get_ticket_query));
                        $get_ticket_data_list = array_map(function ($value) {
                            return (array) $value;
                        }, $get_ticket_data);
                
                        if ($get_ticket_data_list) {
                            $data = array('ticket_data_list' => $get_ticket_data_list);
                            $email = \Config::get('constants.options.option_email');
                            $message = \Config::get('constants.options.option_config');

                            $log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Ticket List Found!',
                                        'data' => $data
                            ]);
                        } else {
                            return $this->respondWithError("No Ticket Found!");
                        }
                    } else {
                        $valid_user_plan = array();
                        $current_month = date('m');
                        $current_year = date('Y');
                        $get_ticket_query = "
                                                SELECT T.ticket_no AS ticket_id, COUNT(F.farmer_id) AS registered_numbers
                                                FROM        tickets     T
                                                LEFT JOIN   farmers     F   ON T.ticket_id=F.ticket_id
                                                WHERE
                                                    (T.status='2'        AND T.dd_ticket_role='0' AND
                                                    T.ticket_status='2') OR (T.status='0'        AND
                                                    T.ticket_status='0') AND

                                                    (T.ticket_id in  (select DISTINCT(ticket_id) from farmers where DATE(datetime) < DATE(created_at)  and  DATE(T.closing_datetime) > DATE_SUB(NOW(), INTERVAL 7 DAY) ))


                                                GROUP BY
                                                    F.ticket_id
                                                ORDER BY 
                                                    T.ticket_no ASC
                                                ";
                        $get_ticket_data = DB::select(DB::raw($get_ticket_query));
                        $get_ticket_data_list = array_map(function ($value) {
                            return (array) $value;
                        }, $get_ticket_data);

                        if ($get_ticket_data_list) {
                            $data = array('ticket_data_list' => $get_ticket_data_list);

                            $log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);
                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Ticket List Found!',
                                        'data' => $data
                            ]);
                        } else {
                            return $this->respondWithError("No Ticket Found!");
                        }
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function farmerNumberList(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'cc_token' => 'required|string',
                'ticket_id' => 'required|string'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $curTime = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "cc/getFarmerNumbers",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);

                $cc_token = $data['cc_token'];
                $ticket_id = $data['ticket_id'];
                if ($this->api_token === $cc_token) {
                    $ticket_data = Ticket::where('ticket_no', $ticket_id)->first();
                    
                    if ($ticket_data) {
                        $data_source_id = $ticket_data->data_source_id;
                        if($data_source_id == 6) {
                            $get_ticket_query = "
                                                SELECT F.farmer_id AS registered_number_id, F.mobile AS registered_number, IFC.farmer_name AS farmer_name, F.datetime as created_at, F.created_at as uploded_at, T.user_type, T.user_id 
                                                FROM        tickets     T
                                                LEFT JOIN   farmers     F   ON T.ticket_id=F.ticket_id
                                                LEFT JOIN   ifc_farmer  IFC ON F.mobile=IFC.mobile
                                                WHERE (T.status='2'        AND
                                                    T.ticket_status='2' AND
                                                    -- T.dd_ticket_role='0' AND
                                                    T.ticket_id='" . $ticket_data->ticket_id . "') 
                                                    AND F.ticket_status='1' 
                                                GROUP BY F.farmer_id
                                                ORDER BY 
                                                    T.ticket_no ASC
                                                ";
                        } else {
                            $get_ticket_query = "
                                                    SELECT F.farmer_id AS registered_number_id, F.mobile AS registered_number, F.datetime as created_at, F.created_at as uploded_at, T.user_type, T.user_id 
                                                    FROM        tickets     T
                                                    LEFT JOIN   farmers     F   ON T.ticket_id=F.ticket_id
                                                    WHERE (T.status='2'        AND
                                                        T.ticket_status='2' AND
                                                        -- T.dd_ticket_role='0' AND
                                                        T.ticket_id='" . $ticket_data->ticket_id . "') 
                                                        AND F.ticket_status='1' 
                                                    ORDER BY 
                                                        T.ticket_no ASC";
                                                //OR (
                                                    // T.status='0'        AND
                                                    // T.ticket_status='0' AND
                                                    // T.ticket_id='" . $ticket_data->ticket_id . "')
                        }
                        $get_ticket_data = DB::select(DB::raw($get_ticket_query));
                        foreach ($get_ticket_data as $key => $value) {
                            if($get_ticket_data[$key]->user_type == 'bdo')
                            {
                                $bdodata = DB::table('users')
                                           ->where('id',$get_ticket_data[$key]->user_id)
                                           ->select('name')
                                           ->get();
                                $get_ticket_data[$key]->name = $bdodata[0]->name;
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'dhanuka_doctor') {
                                $dddata = DB::table('dhanuka_doctor')
                                           ->join('users','users.id','=','dhanuka_doctor.bdo_id','left')
                                           ->where('dd_id',$get_ticket_data[$key]->user_id)
                                           ->select('users.name')
                                           ->get();
                                $get_ticket_data[$key]->name = $dddata[0]->name;
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'zonal') {
                                $dddata = DB::table('zonal_managers')
                                           ->where('zonal_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('zonal_manager_name')
                                           ->get();
                                $get_ticket_data[$key]->name = $dddata[0]->zonal_manager_name;
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'area') {
                                $dddata = DB::table('area_managers')
                                           ->where('area_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('area_manager_name')
                                           ->get();
                                $get_ticket_data[$key]->name = $dddata[0]->area_manager_name;
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'sales_regional') {
                                $dddata = DB::table('sales_regional_managers')
                                           ->where('sales_regional_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('sales_regional_manager_name')
                                           ->get();
                                $get_ticket_data[$key]->name = $dddata[0]->sales_regional_manager_name;
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'sales_area') {
                                $dddata = DB::table('sales_area_managers')
                                           ->where('sales_area_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('sales_area_manager_name')
                                           ->get();
                                $get_ticket_data[$key]->name = $dddata[0]->sales_area_manager_name;
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'regional') {
                                $dddata = DB::table('regional_managers')
                                           ->where('regional_manager_id',$get_ticket_data[$key]->user_id)
                                           ->select('regional_manager_name')
                                           ->get();
                                $get_ticket_data[$key]->name = $dddata[0]->regional_manager_name;
                            }
                            elseif ($get_ticket_data[$key]->user_type == 'marketing_supervisor') {
                                $dddata = DB::table('marketing_supervisor')
                                           ->where('marketing_supervisor_id',$get_ticket_data[$key]->user_id)
                                           ->select('marketing_supervisor_name')
                                           ->get();
                                $get_ticket_data[$key]->name = $dddata[0]->marketing_supervisor_name;
                            }
                        }

                        foreach ($get_ticket_data as $key => $value) {
                           unset($get_ticket_data[$key]->user_type);
                           unset($get_ticket_data[$key]->user_id);
                        }
                        $get_ticket_data_list = array_map(function ($value) {
                            return (array) $value;
                        }, $get_ticket_data);
                        if ($get_ticket_data_list) {
                            $user_id = $ticket_data->user_id;
                            $user_type = $ticket_data->user_type;
                            switch ($user_type) {
                                case "zonal" : $table = "zonal_managers";
                                    $key = "zonal_manager_id";
                                    $column = "zonal_manager_name";
                                    $bdo_language = DB::table('users AS U')
                                            ->select('L.language')
                                            ->leftJoin('languages AS L', 'L.id', '=', 'U.language_id')
                                            ->where('U.zonal_manager_id', $user_id)
                                            ->first();
                                    break;
                                case "regional" : $table = "regional_managers";
                                    $key = "regional_manager_id";
                                    $column = "regional_manager_name";
                                    $bdo_language = DB::table('users AS U')
                                            ->select('L.language')
                                            ->leftJoin('languages AS L', 'L.id', '=', 'U.language_id')
                                            ->where('U.regional_manager_id', $user_id)
                                            ->first();
                                    break;
                                case "area" : $table = "area_managers";
                                    $key = "area_manager_id";
                                    $column = "area_manager_name";
                                    $bdo_language = DB::table('users AS U')
                                            ->select('L.language')
                                            ->leftJoin('languages AS L', 'L.id', '=', 'U.language_id')
                                            ->where('U.area_manager_id', $user_id)
                                            ->first();
                                    break;
                                case "sales_regional" : $table = "sales_regional_managers";
                                    $key = "sales_regional_manager_id";
                                    $column = "sales_regional_manager_name";
                                    $bdo_language = DB::table('users AS U')
                                            ->select('L.language')
                                            ->leftJoin('languages AS L', 'L.id', '=', 'U.language_id')
                                            ->where('U.sales_regional_manager_id', $user_id)
                                            ->first();
                                    break;
                                case "sales_area" : $table = "sales_area_managers";
                                    $key = "sales_area_manager_id";
                                    $column = "sales_area_manager_name";
                                    $bdo_language = DB::table('users AS U')
                                            ->select('L.language')
                                            ->leftJoin('languages AS L', 'L.id', '=', 'U.language_id')
                                            ->where('U.sales_area_manager_id', $user_id)
                                            ->first();
                                    break;
                                case "bdo" : $table = "users";
                                    $key = "id";
                                    $column = "name";
                                    $bdo_language = DB::table('users AS U')
                                            ->select('L.language')
                                            ->leftJoin('languages AS L', 'L.id', '=', 'U.language_id')
                                            ->where('U.id', $user_id)
                                            ->first();
                                    break;
                                case "dhanuka_doctor" : $table = "dhanuka_doctor";
                                    $key = "dd_id";
                                    $column = "name";
                                    $bdo_language = DB::table('dhanuka_doctor AS dd')
                                            ->select('L.language')
                                            ->leftJoin('languages AS L', 'L.id', '=', 'dd.language_id')
                                            ->where('dd.dd_id', $user_id)
                                            ->first();
                                    break;
                            }

                            $language = $bdo_language->language;
                            $ticket_detail_query = "
                                                        SELECT
                                                            u." . $column . ",
                                                            u.ECN,
                                                            lan.language,
                                                            ut.ticket_no as ticket, 
                                                            ds.data_source, 
                                                            s.state, 
                                                            d.district, 
                                                            t.tehsil,
                                                            ut.village,
                                                            p1.product  AS product_1,
                                                            p2.product  AS product_2,
                                                            p3.product  AS product_3,
                                                            c1.crop     AS crop_1,
                                                            c2.crop     AS crop_2,
                                                            c3.crop     AS crop_3,
                                                            ut.created_at
                                                        FROM        tickets             ut
                                                        LEFT JOIN   data_sources        ds  ON ut.data_source_id=ds.data_source_id
                                                        LEFT JOIN   states              s   ON ut.state_id=s.state_id
                                                        LEFT JOIN   districts           d   ON ut.district_id=d.district_id
                                                        LEFT JOIN   tehsils             t   ON ut.tehsil_id=t.tehsil_id

                                                        LEFT JOIN   " . $table . "          u   ON ut.user_id=u." . $key . "

                                                        LEFT JOIN products      p1  ON ut.product_1_id=p1.product_id
                                                        LEFT JOIN products      p2  ON ut.product_2_id=p2.product_id
                                                        LEFT JOIN products      p3  ON ut.product_3_id=p3.product_id

                                                        LEFT JOIN crops         c1  ON ut.crop_1_id=c1.crop_id
                                                        LEFT JOIN crops         c2  ON ut.crop_2_id=c2.crop_id
                                                        LEFT JOIN crops         c3  ON ut.crop_3_id=c3.crop_id
                                                        LEFT JOIN languages  lan   ON lan.id=ut.language_id

                                                        WHERE
                                                            (ut.status='2'      AND 
                                                            -- ut.dd_ticket_role='0' AND
                                                            ut.ticket_status='2' AND
                                                            ut.ticket_no='" . $ticket_id . "') OR (
                                                            ut.status='0'        AND
                                                            ut.ticket_status='0' AND
                                                            ut.ticket_no='" . $ticket_id . "')
                                                            
                                                        LIMIT 0,1
                                                        ";
                                                        //ut.status       = '0' AND ut.ticket_no    = '" . $ticket_id . "'
                            $ticket_details = DB::select(DB::raw($ticket_detail_query));
                            $ticket_details_data = array_map(function ($value) {
                                return (array) $value;
                            }, $ticket_details);

                            $data = array('ticket_details' => $ticket_details_data, 'ticket_data_list' => $get_ticket_data_list);

                            $log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Registered Numbers Found!',
                                        'data' => $data
                            ]);
                        } else {
                            return $this->respondWithError("No Registered Number Found!");
                        }
                    } else {
                        return $this->respondWithConflict("No Ticket Found!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function verificationStatus(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'cc_token' => 'required|string',
                'registered_number_id' => 'required|string',
                'TicketNumber' => 'required|string',
                'Mobile' => 'required|string'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $curTime = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "cc/setValidatedFarmerNumber",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);

                $cc_token = $data['cc_token'];
                $registered_number_id = $data['registered_number_id'];
                $TicketNumber = $data['TicketNumber'];
                $Mobile = $data['Mobile'];
                $BDOHQCode = $data['BDOHQCode'];
                $Farmer = $data['FarmerName'];
                $Acreage1 = $data['Acreage1'];
                $Beegha1 = $data['Acre_Beegha1'];
                $Acreage2 = $data['Acreage2'];
                $Beegha2 = $data['Acre_Beegha2'];
                $Acreage3 = $data['Acreage3'];
                $Beegha3 = $data['Acre_Beegha3'];
                $Areyousatisfiedwiththeproduct = $data['Areyousatisfiedwiththeproduct'];
                $Reason_Areyousatisfiedwiththeproduct = $data['Reason_Areyousatisfiedwiththeproduct'];
                $Willyoubuyanduseourproduct = $data['Willyoubuyanduseourproduct'];
                $Reason_Willyoubuyanduseourproduct = $data['Reason_Willyoubuyanduseourproduct'];
                $contactstatus = $data['contactstatus'];
                $validationtype = $data['validationtype'];
                $Disposition = $data['Disposition'];
                $IfFollowUp = $data['IfFollowUp'];
                $WarmLeadDateTime = $data['WarmLeadDateTime'];
                $AllReadyPurchaser_ProductName = $data['AllReadyPurchaser_ProductName'];
                $AllReadyPurchaser_HowManyAcrePurchased = $data['AllReadyPurchaser_HowManyAcrePurchased'];
                $AllReadyPurchaser_PurchsedFrom = $data['AllReadyPurchaser_PurchsedFrom'];
                $AllReadyPurchaser_Amount = $data['AllReadyPurchaser_Amount'];
                $AllReadyPurchaser_Boxcolour = $data['AllReadyPurchaser_Boxcolour'];
                $HotLeadDateTime = $data['HotLeadDateTime'];
                $PermissionforHotleadchasercall = $data['PermissionforHotleadchasercall'];
                $IFhotlead_HasFarmerPurchasetheProduct1 = $data['IFhotlead_HasFarmerPurchasetheProduct1'];
                $IFhotleadOptionYes_ProductName1 = $data['IFhotleadOptionYes_ProductName1'];
                $IFhotleadOptionYes_HowManyAcrePurchased1 = $data['IFhotleadOptionYes_HowManyAcrePurchased1'];
                $IFhotleadOptionYes_PurchsedFrom1 = $data['IFhotleadOptionYes_PurchsedFrom1'];
                $IFhotleadOptionYes_Amount1 = $data['IFhotleadOptionYes_Amount1'];
                $IFhotleadOptionYes_Boxcolour1 = $data['IFhotleadOptionYes_Boxcolour1'];
                $IFhotleadOptionYes_DidyouBoughtanyotherProduct2 = $data['IFhotleadOptionYes_DidyouBoughtanyotherProduct2'];
                $IFhotleadOptionYes_ProductName2 = $data['IFhotleadOptionYes_ProductName2'];
                $IFhotleadOptionYes_HowManyAcrePurchased2 = $data['IFhotleadOptionYes_HowManyAcrePurchased2'];
                $IFhotleadOptionYes_PurchsedFrom2 = $data['IFhotleadOptionYes_PurchsedFrom2'];
                $IFhotleadOptionYes_Amount2 = $data['IFhotleadOptionYes_Amount2'];
                $IFhotleadOptionYes_Boxcolour2 = $data['IFhotleadOptionYes_Boxcolour2'];
                $IFhotleadOptionYes_DidyouBoughtanyotherProduct3 = $data['IFhotleadOptionYes_DidyouBoughtanyotherProduct3'];
                $IFhotleadOptionYes_ProductName3 = $data['IFhotleadOptionYes_ProductName3'];
                $IFhotleadOptionYes_HowManyAcrePurchased3 = $data['IFhotleadOptionYes_HowManyAcrePurchased3'];
                $IFhotleadOptionYes_PurchsedFrom3 = $data['IFhotleadOptionYes_PurchsedFrom3'];
                $IFhotleadOptionYes_Amount3 = $data['IFhotleadOptionYes_Amount3'];
                $IFhotleadOptionYes_Boxcolour3 = $data['IFhotleadOptionYes_Boxcolour3'];
                $IFhotleadOptionNo_ReasonforNonpurchase = $data['IFhotleadOptionNo_ReasonforNonpurchase'];
                $IFhotleadOptionNo_WhenFarmerwillPurchasetheproduct = $data['IFhotleadOptionNo_WhenFarmerwillPurchasetheproduct'];
                $HotLeadDisposition = $data['HotLeadDisposition'];
                $Remark = $data['Remark'];

                if ($this->api_token === $cc_token) {
                    $ticket_data = Ticket::where('ticket_no', $TicketNumber)->first();
                    if ($ticket_data) {
                        $cogent_data = array(
                            'registered_number_id' => $registered_number_id,
                            'TicketNumber' => $TicketNumber,
                            'Mobile' => $Mobile,
                            'BDOHQCode' => $BDOHQCode,
                            'FarmerName' => $Farmer,
                            'Acreage1' => $Acreage1,
                            'Acre_Beegha1' => $Beegha1,
                            'Acreage2' => $Acreage2,
                            'Acre_Beegha2' => $Beegha2,
                            'Acreage3' => $Acreage3,
                            'Acre_Beegha3' => $Beegha3,
                            'Areyousatisfiedwiththeproduct' => $Areyousatisfiedwiththeproduct,
                            'Reason_Areyousatisfiedwiththeproduct' => $Reason_Areyousatisfiedwiththeproduct,
                            'Willyoubuyanduseourproduct' => $Willyoubuyanduseourproduct,
                            'Reason_Willyoubuyanduseourproduct' => $Reason_Willyoubuyanduseourproduct,
                            'contactstatus' => $contactstatus,
                            'validationtype' => $validationtype,
                            'Disposition' => $Disposition,
                            'IfFollowUp' => $IfFollowUp,
                            'WarmLeadDateTime' => $WarmLeadDateTime,
                            'AllReadyPurchaser_ProductName' => $AllReadyPurchaser_ProductName,
                            'AllReadyPurchaser_HowManyAcrePurchased' => $AllReadyPurchaser_HowManyAcrePurchased,
                            'AllReadyPurchaser_PurchsedFrom' => $AllReadyPurchaser_PurchsedFrom,
                            'AllReadyPurchaser_Amount' => $AllReadyPurchaser_Amount,
                            'AllReadyPurchaser_Boxcolour' => $AllReadyPurchaser_Boxcolour,
                            'HotLeadDateTime' => $HotLeadDateTime,
                            'PermissionforHotleadchasercall' => $PermissionforHotleadchasercall,
                            'IFhotlead_HasFarmerPurchasetheProduct1' => $IFhotlead_HasFarmerPurchasetheProduct1,
                            'IFhotleadOptionYes_ProductName1' => $IFhotleadOptionYes_ProductName1,
                            'IFhotleadOptionYes_HowManyAcrePurchased1' => $IFhotleadOptionYes_HowManyAcrePurchased1,
                            'IFhotleadOptionYes_PurchsedFrom1' => $IFhotleadOptionYes_PurchsedFrom1,
                            'IFhotleadOptionYes_Amount1' => $IFhotleadOptionYes_Amount1,
                            'IFhotleadOptionYes_Boxcolour1' => $IFhotleadOptionYes_Boxcolour1,
                            'IFhotleadOptionYes_DidyouBoughtanyotherProduct2' => $IFhotleadOptionYes_DidyouBoughtanyotherProduct2,
                            'IFhotleadOptionYes_ProductName2' => $IFhotleadOptionYes_ProductName2,
                            'IFhotleadOptionYes_HowManyAcrePurchased2' => $IFhotleadOptionYes_HowManyAcrePurchased2,
                            'IFhotleadOptionYes_PurchsedFrom2' => $IFhotleadOptionYes_PurchsedFrom2,
                            'IFhotleadOptionYes_Amount2' => $IFhotleadOptionYes_Amount2,
                            'IFhotleadOptionYes_Boxcolour2' => $IFhotleadOptionYes_Boxcolour2,
                            'IFhotleadOptionYes_DidyouBoughtanyotherProduct3' => $IFhotleadOptionYes_DidyouBoughtanyotherProduct3,
                            'IFhotleadOptionYes_ProductName3' => $IFhotleadOptionYes_ProductName3,
                            'IFhotleadOptionYes_HowManyAcrePurchased3' => $IFhotleadOptionYes_HowManyAcrePurchased3,
                            'IFhotleadOptionYes_PurchsedFrom3' => $IFhotleadOptionYes_PurchsedFrom3,
                            'IFhotleadOptionYes_Amount3' => $IFhotleadOptionYes_Amount3,
                            'IFhotleadOptionYes_Boxcolour3' => $IFhotleadOptionYes_Boxcolour3,
                            'IFhotleadOptionNo_ReasonforNonpurchase' => $IFhotleadOptionNo_ReasonforNonpurchase,
                            'IFhotleadOptionNo_WhenFarmerwillPurchasetheproduct' => $IFhotleadOptionNo_WhenFarmerwillPurchasetheproduct,
                            'HotLeadDisposition' => $HotLeadDisposition,
                            'Remark' => $Remark
                        );
                        $insert_cogent_data = CogentRecord::firstOrCreate($cogent_data);

                        if ($validationtype == 'Validated' || $Disposition == 'Validated' || $Disposition == 'Hot Lead' || $Disposition == 'Warm Lead' || $Disposition == 'All Ready Purchaser' || $Disposition == 'Validated/Not sure to Buy') {
                            $validation_status = 'Validated';

                        } else if ($Disposition == 'NADA' || $Disposition == 'Not Attended Dhanuka Activity' || $Disposition == 'NADA - Not Attended Dhanuka Activity' || $Disposition == 'NADA-Not Attended Dhanuka Activity') {
                            $validation_status = 'NADA';
                        } else if ($Disposition == 'PNC' || $Disposition == 'Product Not Confirm' || $Disposition == 'PNC - Product Not Confirm' || $Disposition == 'PNC-Product Not Confirm') {
                            $validation_status = 'PNC';
                        } else if ($Disposition == 'Bad Number' || $Disposition == 'Abusive Language' || $Disposition == 'FHTU - Farmer hard to Understand' || $Disposition == 'None Service State' || $Disposition == 'Not Interested / DNC' || $Disposition == 'NFC-Non Farmer Contact' || $Disposition == 'Non Potential Farmer') {
                            $validation_status = 'BN';
                        } else {
                            $validation_status = '';
                        }

                        $regFarmerName = "";
                        if ($Farmer != '') {
                          $regFarmerName = $Farmer;
                        }
                        else
                        {
                          $regFarmerName = "";
                        }

                        $update_verification_status = Farmer::where('farmer_id', $registered_number_id)->update(['validation_status' => $validation_status, 'farmer_name' => $regFarmerName]);

                        $log_firstTime  = strtotime($log_start_time);
                        $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                        $log_time_diff  = $log_lastTime-$log_firstTime;
                        $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                        DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                        if ($update_verification_status) {
                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode()
                            ]);
                        } else {
                            return $this->respondWithError("Something went wrong. Please try again.!");
                        }
                    } else {
                        return $this->respondWithConflict("No Ticket Found!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function getBdoList(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'cc_token' => 'required|string',
                'district' => 'required|string'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $curTime = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "cc/bdolist",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);

                $cc_token = $data['cc_token'];
                $district = $data['district'];
                if ($this->api_token === $cc_token) {
                    $district_query = " SELECT 
                                                district_id
                                            FROM    districts
                                            WHERE   districts.status='1' AND districts.district = '" . $district . "' ";
                    $district_detail = DB::select(DB::raw($district_query));
                    $district_list = array_map(function ($value) {
                        return (array) $value;
                    }, $district_detail);

                    $districts = (isset($district_list[0]['district_id']) ? $district_list[0]['district_id'] : '');

                    if ($districts) {
                        $query = "
                                        SELECT u.id, u.name, d.district
                                        FROM users  u
                                        LEFT JOIN districts d ON u.district_id=d.district_id
                                        WHERE
                                            u.district_id  = '" . $districts . "' AND
                                            u.status  = '1'
                                        ";
                        $bdo_list = DB::select(DB::raw($query));
                        $result = array_map(function ($value) {
                            return (array) $value;
                        }, $bdo_list);
                        if (count($result) > 0) {
                            $log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'data' => $result
                            ]);
                        } else {
                            $district_query = " SELECT  state_id
                                                    FROM    districts
                                                    WHERE   districts.status='1' AND districts.district = '" . $district . "' ";
                            $district_detail = DB::select(DB::raw($district_query));
                            $district_list = array_map(function ($value) {
                                return (array) $value;
                            }, $district_detail);
                            $states = (isset($district_list[0]['state_id']) ? $district_list[0]['state_id'] : '');
                            $query = "
                                            SELECT u.id, u.name, d.district
                                            FROM users  u
                                            LEFT JOIN districts d ON u.district_id=d.district_id
                                            WHERE
                                                u.state_id    = '" . $states . "' AND
                                                u.status      = '1'
                                            ";
                            $bdo_list = DB::select(DB::raw($query));
                            $result = array_map(function ($value) {
                                return (array) $value;
                            }, $bdo_list);
                            if (count($result) > 0) {
                                $log_firstTime  = strtotime($log_start_time);
                                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                                $log_time_diff  = $log_lastTime-$log_firstTime;
                                $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);
                                return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'data' => $result
                                ]);
                            } else {
                                return $this->respondWithError("Something went wrong. Please try again.!");
                            }
                        }
                    } else {
                        return $this->respondWithConflict("No District Found!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function getCustomerQuery(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'cc_token' => 'required|string',
                'TokenNumber' => 'required|string',
                'Mobile' => 'required|string',
                'farmername' => 'required|string',
                'State' => 'required|string',
                'District' => 'required|string',
                'Tehsil' => 'required|string',
                'Village' => 'required|string',
                'ProductName' => 'required|string',
                'CropName' => 'required|string',
                'Disease' => 'required|string',
                'Pincode' => 'required|string',
                'TypeofCall' => 'required|string',
                'IsFollowUp' => 'required|string',
                'BDO_Id' => 'required|string',
                'Remark' => 'required|string',
                'query_datetime' => 'required|date_format:Y-m-d H:i:s'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $curTime = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "cc/customerQuery",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);

                $cc_token = $data['cc_token'];
                $token_number = $data['TokenNumber'];
                $mobile = $data['Mobile'];
                $farmername = $data['farmername'];
                $state = $data['State'];
                $district = $data['District'];
                $tehsil = $data['Tehsil'];
                $village = $data['Village'];
                $product = $data['ProductName'];
                $crop = $data['CropName'];
                $disease = $data['Disease'];
                $pincode = $data['Pincode'];
                $type_of_call = $data['TypeofCall'];
                $is_follow_up = $data['IsFollowUp'];
                $bdo_id = $data['BDO_Id'];
                $remark = $data['Remark'];
                $query_datetime = $data['query_datetime'];
                $status = 1;
                $created_at = date('Y-m-d');
                if ($this->api_token === $cc_token) {
                    $check_token = CustomerQuery::where('token_number', $token_number)->first();
                    if ($check_token) {
                        if ($check_token->status != '3') {
                            $remark = $remark . "__" . $query_datetime . "\n\n" . $check_token->remark . "__" . $check_token->query_datetime;
                            $customer_data = array(
                                'mobile' => $mobile,
                                'farmername' => $farmername,
                                'state' => $state,
                                'district' => $district,
                                'tehsil' => $tehsil,
                                'village' => $village,
                                'product' => $product,
                                'crop' => $crop,
                                'disease' => $disease,
                                'pincode' => $pincode,
                                'type_of_call' => $type_of_call,
                                'is_follow_up' => $is_follow_up,
                                'bdo_id' => $bdo_id,
                                'remark' => $remark,
                                'query_datetime' => $query_datetime,
                                'status' => $status,
                                'created_at' => $created_at
                            );
                            $update_customer_data = CustomerQuery::where('token_number', $token_number)->update($customer_data);
                            if ($update_customer_data) {
                                $log_firstTime  = strtotime($log_start_time);
                                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                                $log_time_diff  = $log_lastTime-$log_firstTime;
                                $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                                return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Query Succesfully Inserted!',
                                ]);
                            } else {
                                return $this->respondWithError("Something went wrong. Please try again.!");
                            }
                        } else {
                            return $this->respondWithError("Please create a new ticket as the ticket is closed by BDO.!");
                        }
                    } else {
                        $customer_data = array(
                            'token_number' => $token_number,
                            'mobile' => $mobile,
                            'farmername' => $farmername,
                            'state' => $state,
                            'district' => $district,
                            'tehsil' => $tehsil,
                            'village' => $village,
                            'product' => $product,
                            'crop' => $crop,
                            'disease' => $disease,
                            'pincode' => $pincode,
                            'type_of_call' => $type_of_call,
                            'is_follow_up' => $is_follow_up,
                            'bdo_id' => $bdo_id,
                            'remark' => $remark,
                            'query_datetime' => $query_datetime,
                            'status' => $status,
                            'created_at' => $created_at
                        );
                        $insert_customer_data = CustomerQuery::firstOrCreate($customer_data);
                        if ($insert_customer_data) {
                            $log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);
                            return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Query Succesfully Inserted!',
                            ]);
                        } else {
                            return $this->respondWithError("Something went wrong. Please try again.!");
                        }
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }
}