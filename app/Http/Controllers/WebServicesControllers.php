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
use App\MarkVisit;
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
use App\territories;
use App\markets;
use App\Tehsil;
use App\Product;
use App\Crop;
use App\ProductCropRelation;
use App\ProductCropCategory;
use App\Ticket;
use App\Farmer;
use App\Godown;
use App\PlanActivities;
use App\PlanAtp;
use App\SelfParticipation;
use App\SelfParticipationPlan;
use App\Testimonial;
use App\CustomerQuery;
use App\CustomerQuerySolution;
use App\AppVersion;
use App\ActiveLog;
use App\Efficiency;
use App\IFCVillageFarmer;
use App\VillageFarmer;
use App\VillageFarmerCrop;
use App\Village;
use App\DemoRequest;
use App\DemoMaterialRequest;
use App\DemoMaterialRequestQA;

use App\TeaEstate;
use App\TeaEstateContact;
use App\EstateCategory;
use App\EstateCompany;
use App\EstateDesignation;
use App\TeaEstateATP;
use App\TeaEstateATPArivalDeparture;
use App\InstitutionalTestimonial;

use App\DistributorAdvisor;
use App\DistributorAdvisorATP;

use App\Circular;
use App\CircularTestimonial;

use App\Influencer;
use App\InfluencerContact;
use App\InfluencerCategory;
use App\InfluencerCompany;
use App\InfluencerDesignation;
use App\InfluencerATP;
use App\InfluencerATPArivalDeparture;

use App\Sugarmill;
use App\SugarmillContact;
use App\SugarmillCategory;
use App\SugarmillCompany;
use App\SugarmillDesignation;
use App\SugarmillATP;
use App\SugarmillATPArivalDeparture;

use App\MarketingSupervisorProductCategory;
use App\MarketingSupervisor;
use App\MarketingSupervisorRSM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\Jobs\SendNotification;
use DateTime;
use \Illuminate\Http\Response as Res;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use App\Repository\Transformers\UserTransformer;
use App\DhanukaDoctor;
use Twilio;
use Illuminate\Support\Facades\Storage;
use App\GeneralUser;
use Illuminate\Support\Facades\Log;

ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);

class WebServicesControllers extends ApiController {

    protected $userTransformer;

    public function __construct(userTransformer $userTransformer) {
        $this->userTransformer = $userTransformer;
        //$this->croncloseticket();
        //$this->crondemorequest();
        //$this->cronddearning();
    }

    public function __authenticateaa(Request $request) {
        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        } else {
            $user = User::where('email', $request['email'])->first();
            if ($user) {
                $api_token = $user->api_token;
                if ($api_token == NULL) {
                    return $this->_login($request['email'], $request['password']);
                }
                try {
                    $user = JWTAuth::toUser($api_token);
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Already logged in',
                                'user' => $this->userTransformer->transform($user)
                    ]);
                } catch (JWTException $e) {
                    $user->api_token = NULL;
                    $user->save();
                    return $this->respondInternalError("Login Unsuccessful. An error occurred while performing an action!");
                }
            } else {
                return $this->respondWithError("Invalid Email or Password");
            }
        }
    }

    private function ___login($email, $password) {
        $credentials = ['email' => $email, 'password' => $password];

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->respondWithError("User does not exist!");
        }
        $user = JWTAuth::toUser($token);
        $user->api_token = $token;
        $user->save();
        return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Login successful!',
                    'data' => $this->userTransformer->transform($user)
        ]);
    }

    public function s1PlanActivity($api_token = '',$updated_date=0) {
        try {
            $curTime = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "v3/planSyllabus",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $crr_date = date('Y-m-d H:i:s');

                if ($user_type == 'marketing_supervisor') {   
                    //Marketing Supervisor RSM LIST
                    $rsmData = DB::table('marketing_supervisor_rsm')
                              ->where('marketing_supervisor_id',$user_id)
                              ->select('sales_regional_manager_id')
                              ->get();
                    $rsmresult = array();
                    if (isset($rsmData) && !empty($rsmData)) {
                        //RSM LIST
                        $rsmDatass = json_decode(json_encode($rsmData),true);
                        foreach ($rsmDatass as $key => $value) {
                            if (is_array($value)) {
                                $rsmresult = array_merge($rsmresult, array_flatten($value));
                            } else {
                                $rsmresult[$key] = $value;
                            }
                        }
                    }
                    $rsmDataMerge = implode("','", $rsmresult);
                    if($updated_date != 0) {
                        $addQry = 'users.updated_at > "'.$updated_date.'" OR users.updated_at IS NULL AND';
                    } else {
                        $addQry ='';
                    }
                    //bdo list
                    $bdo_query = " SELECT 
                                        users.id AS bdo_id,
                                        users.zonal_manager_id, 
                                        users.regional_manager_id, 
                                        users.sales_regional_manager_id, 
                                        users.area_manager_id, 
                                        users.sales_area_manager_id, 
                                        users.state_id, 
                                        users.district_id, 
                                        users.language_id, 
                                        users.name AS bdo_name, 
                                        users.bdo_code, 
                                        users.bdo_hq, 
                                        users.bdo_mobile_no, 
                                        users.department, 
                                        users.designation,
                                        users.updated_at,
                                        (CASE WHEN user_deputations.status='1' THEN 1 ELSE 0 END) as deputation_status
                                    FROM        users
                                    LEFT JOIN   user_deputations ON user_deputations.user_id=users.id
                                    WHERE $addQry users.status='1' AND users.sales_regional_manager_id in ('".$rsmDataMerge."')
                                    ORDER BY users.name ASC";
                    $bdo_detail = DB::select(DB::raw($bdo_query));
                    $bdo_list = array_map(function ($value) {
                        return (array) $value;
                    }, $bdo_detail);

                    //product list
                    $product_query = " SELECT P.product_id, P.product
                                        FROM sales_regional_manager_product_categories UPC
                                        LEFT JOIN product_crop_relations PCR ON UPC.product_crop_category_id=PCR.product_crop_category_id
                                        LEFT JOIN products P ON PCR.product_id=P.product_id
                                        WHERE P.product_id<>'' AND P.product<>'' and PCR.status=1 and P.status=1 and UPC.sales_regional_manager_id in ('".$rsmDataMerge."')
                                        GROUP BY P.product_id
                                        ORDER BY P.product ASC";
                                        //echo $product_query;exit();
                    $product_detail = DB::select(DB::raw($product_query));
                    $product_list = array_map(function ($value) {
                        return (array) $value;
                    }, $product_detail);

                    //crop list
                    $crop_query = " SELECT C.crop_id, C.crop
                                        FROM sales_regional_manager_product_categories UPC
                                        LEFT JOIN product_crop_relations    PCR ON UPC.product_crop_category_id=PCR.product_crop_category_id
                                        LEFT JOIN crops                     C   ON PCR.crop_id=C.crop_id
                                        WHERE C.crop_id<>'' AND C.crop<>'' AND C.status=1 
                                        GROUP BY C.crop_id
                                        ORDER BY C.crop ASC";
                    $crop_detail = DB::select(DB::raw($crop_query));
                    $crop_list = array_map(function ($value) {
                        return (array) $value;
                    }, $crop_detail);
                } else {
                    switch ($user_type) {
                        case "zonal" : $bdo_list = $this->categoryDepartmentWiseBdoList('zonal_manager_id', $user_id, $updated_date);
                            $product_list = $this->categoryDepartmentWiseProductList('zonal_manager_product_categories');
                            $crop_list = $this->categoryDepartmentWiseCropList('zonal_manager_product_categories');
                            break;
                        case "regional" : $bdo_list = $this->categoryDepartmentWiseBdoList('regional_manager_id', $user_id, $updated_date);
                            $product_list = $this->categoryDepartmentWiseProductList('regional_manager_product_categories');
                            $crop_list = $this->categoryDepartmentWiseCropList('regional_manager_product_categories');
                            break;
                        case "area" : $bdo_list = $this->categoryDepartmentWiseBdoList('area_manager_id', $user_id, $updated_date);
                            $product_list = $this->categoryDepartmentWiseProductList('area_manager_product_categories');
                            $crop_list = $this->categoryDepartmentWiseCropList('area_manager_product_categories');
                            break;
                        case "sales_regional" : $bdo_list = $this->categoryDepartmentWiseBdoList('sales_regional_manager_id', $user_id, $updated_date);
                            $product_list = $this->categoryDepartmentWiseProductList('sales_regional_manager_product_categories');
                            $crop_list = $this->categoryDepartmentWiseCropList('sales_regional_manager_product_categories');
                            break;
                        case "sales_area" : $bdo_list = $this->categoryDepartmentWiseBdoList('sales_area_manager_id', $user_id, $updated_date);
                            $product_list = $this->categoryDepartmentWiseProductList('sales_area_manager_product_categories');
                            $crop_list = $this->categoryDepartmentWiseCropList('sales_area_manager_product_categories');
                            break;
                        case "bdo" : $bdo_list = $this->categoryDepartmentWiseBdoList('id', $user_id, $updated_date);
                            $product_list = $this->categoryDepartmentWiseProductList('user_product_categories','user_id',$user_id);
                            $crop_list = $this->categoryDepartmentWiseCropList('user_product_categories');
                            break;
                        case "dhanuka_doctor" : $bdo_list = $this->categoryDepartmentWiseBdoList('id', $user_id, $updated_date);
                            $product_list = $this->categoryDepartmentWiseProductList('dd_product_categories','dd_id',$user_id);
                            $crop_list = $this->categoryDepartmentWiseCropList('dd_product_categories');
                            break;
                    }
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                $data = array('bdolist' => $bdo_list, 'productlist' => $product_list, 'croplist' => $crop_list);
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'crr_date' => $crr_date,
                    'message' => 'Plan Activity Initiated',
                    'data' => $data
                ]);
            } else {

            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function newAllVillageList(Request $request) {
        $rules = array(
            'apiToken' => 'required|string',
            'updated_date' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $curTime = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "v3/newAllVillageList",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $crr_date = date('Y-m-d H:i:s');
            $api_token = $request->input('apiToken');
            $updated_date = $request->input('updated_date');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                if($updated_date == 0) {
                    $Data = DB::table('villages')->select('*')->where('status', '1')->get();
                } else {
                    $Data = DB::table('villages')->select('*')->whereNull('updated_at')->orWhere('updated_at','>', $updated_date)->where('status', '1')->get();
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                if (count($Data)>0) {
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'All Villages List',
                        'crr_date' => $crr_date,
                        'data' => $Data
                    ]);
                } else {
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'No village found',
                        'crr_date' => $crr_date,
                        'data' => []
                    ]);
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt !!");
            }
        }
    }

    public function newAllStateList(Request $request) {
        $rules = array(
            'apiToken' => 'required|string',
            'updated_date' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $crr_date = date('Y-m-d H:i:s');
            $api_token = $request->input('apiToken');
            $updated_date = $request->input('updated_date');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                if($updated_date == 0) {
                    $states = DB::table('states')->select('*')->where('status', '1')->get();
                } else {
                    $states = DB::table('states')->select('*')->whereNull('updated_at')->orWhere('updated_at','>=', $updated_date)->where('status', '1')->get();
                }
                if (count($states)>0) {
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'State List Found!',
                        'crr_date' => $crr_date,
                        'data' => $states
                    ]);
                } else {
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'No state found',
                        'crr_date' => $crr_date,
                        'data' => []
                    ]);
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt !!");
            }
        }
    }

    public function newAllDistrictList(Request $request) {
        $rules = array(
            'apiToken' => 'required|string',
            'updated_date' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $curTime = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "v3/newAllDistrictList",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $crr_date = date('Y-m-d H:i:s');
            $api_token = $request->input('apiToken');
            $updated_date = $request->input('updated_date');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                if($updated_date == 0) {
                    $Data = DB::table('districts')->select('*')->where('status', '1')->get();
                } else {
                    $Data = DB::table('districts')->select('*')->whereNull('updated_at')->orWhere('updated_at','>=', $updated_date)->where('status', '1')->get();
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                if (count($Data)>0) {
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'All District List',
                        'crr_date' => $crr_date,
                        'data' => $Data
                    ]);
                } else {
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'No district found',
                        'crr_date' => $crr_date,
                        'data' => []
                    ]);
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt !!");
            }
        }
    }

    public function newAllTehshilList(Request $request) {
        $rules = array(
            'apiToken' => 'required|string',
            'updated_date' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $curTime = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "v3/newAllTehshilList",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);            
            $crr_date = date('Y-m-d H:i:s');
            $api_token = $request->input('apiToken');
            $updated_date = $request->input('updated_date');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                if($updated_date == 0) {
                    $Data = DB::table('tehsils')->select('*')->where('status', '1')->get();
                } else {
                    $Data = DB::table('tehsils')->select('*')->whereNull('updated_at')->orWhere('updated_at','>=', $updated_date)->where('status', '1')->get();
                }
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                if (count($Data)>0) {
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'All Tehsils List',
                        'crr_date' => $crr_date,
                        'data' => $Data
                    ]);
                } else {
                    return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'No tehsil found',
                            'crr_date' => $crr_date,
                            'data' => []
                    ]);
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt !!");
            }
        }
    }

    private function findUser($api_token) {
        $query = "  
                    (SELECT marketing_supervisor_id AS id, 'marketing_supervisor' as user_type, 'marketing_supervisor_name' as name, ECN  FROM marketing_supervisor WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT zonal_manager_id AS id, 'zonal' as user_type, 'zonal_manager_name' as name, ECN  FROM zonal_managers WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT regional_manager_id AS id, 'regional' as user_type, 'regional_manager_name' as name, ECN FROM regional_managers WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT area_manager_id AS id, 'area' as user_type, 'area_manager_name' as name, ECN FROM area_managers WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT sales_regional_manager_id AS id, 'sales_regional' as user_type, 'sales_regional_manager_name' as name, ECN FROM sales_regional_managers WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT sales_area_manager_id AS id, 'sales_area' as user_type, 'sales_area_manager_name' as name, ECN FROM sales_area_managers WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT id AS id, 'bdo' as user_type, name, ECN FROM users WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT dd_id AS id, 'dhanuka_doctor' as user_type, name, ECN FROM dhanuka_doctor WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
        UNION
                    (SELECT general_id AS id, 'general' as user_type, general_user_name, ECN FROM general WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    ";
        $user = DB::select(DB::raw($query));
        return $user;
    }

    private function categoryDepartmentWiseBdoList($key = '1', $value = '1', $updated_date=0) {
        if($updated_date != 0) {
            $addQry = 'users.updated_at > "'.$updated_date.'" OR users.updated_at IS NULL AND';
        } else {
            $addQry ='';
        }
        $bdo_query = " SELECT 
                        users.id AS bdo_id,
                        users.zonal_manager_id, 
                        users.regional_manager_id, 
                        users.sales_regional_manager_id, 
                        users.area_manager_id, 
                        users.sales_area_manager_id, 
                        users.state_id, 
                        users.district_id, 
                        users.language_id, 
                        users.name AS bdo_name, 
                        users.bdo_code, 
                        users.bdo_hq, 
                        users.bdo_mobile_no, 
                        users.department, 
                        users.designation,
                        users.updated_at,                                
                        (CASE WHEN user_deputations.status='1' THEN 1 ELSE 0 END) as deputation_status
                    FROM        users
                    LEFT JOIN   user_deputations ON user_deputations.user_id=users.id
                    WHERE $addQry users.status='1'    AND users." . $key . "='" . $value . "'
                    ORDER BY users.name ASC";
        $bdo_detail = DB::select(DB::raw($bdo_query));
        $bdo_list = array_map(function ($value) {
            return (array) $value;
        }, $bdo_detail);
        return $bdo_list;
    }

    private function categoryDepartmentWiseProductList($table, $column='', $column_val='') {
        $where = ($column!=''?" and UPC.". $column . "= '".$column_val ."'":" and 1=1");

        $product_query = " SELECT P.product_id, P.product
                            FROM " . $table . " UPC
                            LEFT JOIN product_crop_relations PCR ON UPC.product_crop_category_id=PCR.product_crop_category_id
                            LEFT JOIN products P ON PCR.product_id=P.product_id
                            WHERE P.product_id<>'' AND P.product<>'' and PCR.status=1 and P.status=1 ". $where ."
                            GROUP BY P.product_id
                            ORDER BY P.product ASC";
                            //echo $product_query;exit();
        $product_detail = DB::select(DB::raw($product_query));
        $product_list = array_map(function ($value) {
            return (array) $value;
        }, $product_detail);
        return $product_list;
    }

    private function categoryDepartmentWiseCropList($table) {
        $crop_query = " SELECT C.crop_id, C.crop
                            FROM " . $table . "                     UPC
                            LEFT JOIN product_crop_relations    PCR ON UPC.product_crop_category_id=PCR.product_crop_category_id
                            LEFT JOIN crops                     C   ON PCR.crop_id=C.crop_id
                            WHERE C.crop_id<>'' AND C.crop<>'' AND C.status=1
                            GROUP BY C.crop_id
                            ORDER BY C.crop ASC";
        $crop_detail = DB::select(DB::raw($crop_query));
        $crop_list = array_map(function ($value) {
            return (array) $value;
        }, $crop_detail);
        return $crop_list;
    }

    public function s1tickethistory($api_token = '', $updated_date=0, $offset = 0) { 
        try {
            $curTime = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "v3/ticketHistory",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                $data = array('offset' => $offset);
                $rules = array(
                    'offset' => 'numeric'
                );
                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
                } else {
                    $default_from_date = date("Y-m-d", strtotime("45 days ago"));
                    $default_to_date = date("Y-m-d");
                    
                    if($updated_date != 0) {
                        $addQry = ' AND t.updated_at > "'.$updated_date.'"';
                    } else {
                        $addQry ='';
                    }
                    $addFlQry   = "";
                    $crr_date   = date('Y-m-d H:i:s');
                    $offset     = $offset * 50;
                    $base_url   = url('/');
                    $base_url1  = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                    if ($user_type == 'dhanuka_doctor') {
                        $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.ticket_no as ticket,
                                        IF( pat.updated_at != '0000-00-00', DATE_FORMAT(pat.updated_at, '%Y-%m-%d %H:%i:%s'), t.created_at ) as datetime,
                                        IF( pat.updated_at != '0000-00-00', DATE_FORMAT(pat.updated_at, '%W, %M %e %Y, %h:%i %p'), DATE_FORMAT(t.created_at, '%W, %M %e %Y, %h:%i %p') ) as datetime_format,
                                        CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
                                        d.data_source,
                                        s.state,
                                        sd.district,
                                        sdt.tehsil,
                                        sdtr.territory,
                                        t.village,
                                        dr.demo_code as demo_request_id,
                                        p1.product  AS product_1,
                                        p2.product  AS product_2,
                                        p3.product  AS product_3,
                                        c1.crop     AS crop_1,
                                        c2.crop     AS crop_2,
                                        c3.crop     AS crop_3,
                                        t.expected_farmers,
                                        t.expected_pda,
                                        t.sp_name_1,
                                        t.sp_number_1,
                                        t.sp_name_2,
                                        t.sp_number_2,
                                        t.sp_name_3,
                                        t.sp_number_3,
                                        t.sp_name_4,
                                        t.sp_number_4,
                                        t.sp_name_5,
                                        t.sp_number_5,
                                        t.sp_name_6,
                                        t.sp_number_6,
                                        t.language_id,
                                        t.user_farmer,
                                        lan.language,
                                        dd.name  as dd_name,
                                        t.user_id as dd_id,
                                        IF( t.image_upload_type != '0', '" . $base_url1 . "/', '" . $base_url . "/' ) as base_url,
                                        t.uploaded_file, 
                                        t.uploadedByType,
                                        (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status,
                                        t.activity_duration,
                                        t.ifc_farmer_count,
                                        t.total_pda,
                                        t.total_activity_distance,
                                        t.distance,
                                        t.is_valid,
                                        t.radius,
                                        t.dd_ticket_role
                                    FROM tickets t
                                    LEFT JOIN data_sources  d   ON t.data_source_id=d.data_source_id
                                    LEFT JOIN plan_atp  pat   ON pat.plan_atp_id=t.plan_atp_id
                                    LEFT JOIN languages  lan   ON lan.id=t.language_id

                                    LEFT JOIN states        s   ON t.state_id=s.state_id
                                    LEFT JOIN districts     sd  ON t.district_id=sd.district_id
                                    LEFT JOIN tehsils       sdt ON t.tehsil_id=sdt.tehsil_id
                                    LEFT JOIN territories   sdtr ON t.territory_id=sdtr.territory_id
                                    LEFT JOIN products      p1  ON t.product_1_id=p1.product_id
                                    LEFT JOIN products      p2  ON t.product_2_id=p2.product_id
                                    LEFT JOIN products      p3  ON t.product_3_id=p3.product_id
                                    LEFT JOIN demo_request      dr  ON t.demo_request_id=dr.demo_request_id
                                    LEFT JOIN crops         c1  ON t.crop_1_id=c1.crop_id
                                    LEFT JOIN crops         c2  ON t.crop_2_id=c2.crop_id
                                    LEFT JOIN crops         c3  ON t.crop_3_id=c3.crop_id
                                    LEFT JOIN dhanuka_doctor  dd   ON t.user_id=dd.dd_id
                                    WHERE (t.user_id='" . $user_id . "'  AND t.user_type='" . $user_type . "' AND t.status != '0' AND t.ticket_status != '0' OR t.assign_to_dd='" . $user_id . "')   
                                    AND t.created_at BETWEEN '".$default_from_date." 00:00:00 ' and '".$default_to_date." 23:59:59 ' AND t.status != '0' AND t.ticket_status != '0' $addQry
                                    GROUP BY 
                                        t.ticket_id
                                    ORDER BY 
                                        FIELD(ticket_status, '1') DESC, t.ticket_id DESC
                                    LIMIT
                                        " . $offset . ", 50    
                                    ";
                    }
                    elseif ($user_type == 'bdo') 
                    {   

                        $checkbdotype = DB::table('users')
                                        ->where('id',$user_id)
                                        ->where('status','1')
                                        ->select('designation','id')
                                        ->get();
                        // Log::info($checkbdotype[0]->designation);
                        if ($checkbdotype[0]->designation == 'COUNTERPART-FL') {
                            $checkbdo = DB::table('users')
                                        ->where('counterpart_fl',$checkbdotype[0]->id)
                                        ->where('status','1')
                                        ->select('id')
                                        ->get();
                            $bdo_ids = array();
                            foreach ($checkbdo as $key => $value) {
                                $bdo_ids[] = $checkbdo[$key]->id;
                            }
                            // $addQry = 't.updated_at > "'.$updated_date.'" AND';
                            if(count($checkbdo)>0) {
                                $addFlQry = 'OR t.ticket_id in  (SELECT ticket_id FROM tickets where user_id ="'.$checkbdo[0]->id.'" and forwhom = 2)';
                            }
                            array_push($bdo_ids, $user_id);
                            // Log::info('IF==>');
                            // Log::info($bdo_ids);
                        }
                        else
                        {
                            $checkCPFL = DB::table('users')
                                            ->where('id',$user_id)
                                            ->select('counterpart_fl')
                                            ->get();
                            if (count($checkCPFL)>0 && isset($checkCPFL)) {
                                $bdo_ids = array($user_id);
                                array_push($bdo_ids, $checkCPFL[0]->counterpart_fl);
                            }
                            else
                            {
                                $bdo_ids = array($user_id);
                            }
                            // Log::info('Else-bdo_ids==>');
                            // Log::info($bdo_ids);
                        }

                        $ddData = DB::table('dhanuka_doctor')
                                  ->whereIn('bdo_id',$bdo_ids)
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
                        $dd_ids = implode("','", $ddresult);
                        if ($checkbdotype[0]->designation == 'COUNTERPART-FL') {
                            $bdo_ids = $user_id;
                        } else {
                            $bdo_ids = implode("','", $bdo_ids);
                        }
                        // Log::info("bdo_ids=>");
                        // Log::info($bdo_ids);
                        $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.ticket_no as ticket,
                                        IF( pat.updated_at != '0000-00-00', DATE_FORMAT(pat.updated_at, '%Y-%m-%d %H:%i:%s'), t.created_at ) as datetime,
                                        IF( pat.updated_at != '0000-00-00', DATE_FORMAT(pat.updated_at, '%W, %M %e %Y, %h:%i %p'), DATE_FORMAT(t.created_at, '%W, %M %e %Y, %h:%i %p') ) as datetime_format,
                                        CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
                                        d.data_source,
                                        s.state,
                                        sd.district,
                                        sdt.tehsil,
                                        sdtr.territory,
                                        t.village,
                                        dr.demo_code as demo_request_id,
                                        p1.product  AS product_1,
                                        p2.product  AS product_2,
                                        p3.product  AS product_3,
                                        c1.crop     AS crop_1,
                                        c2.crop     AS crop_2,
                                        c3.crop     AS crop_3,
                                        t.expected_farmers,
                                        t.expected_pda,
                                        t.sp_name_1,
                                        t.sp_number_1,
                                        t.sp_name_2,
                                        t.sp_number_2,
                                        t.sp_name_3,
                                        t.sp_number_3,
                                        t.sp_name_4,
                                        t.sp_number_4,
                                        t.sp_name_5,
                                        t.sp_number_5,
                                        t.sp_name_6,
                                        t.sp_number_6,
                                        t.language_id,
                                        t.user_farmer,
                                        lan.language,
                                        t.user_id,
                                        t.user_type,
                                        dd.name as dd_name,
                                        t.assign_to_dd as dd_id,
                                        IF( t.image_upload_type != '0', '" . $base_url1 . "/', '" . $base_url . "/' ) as base_url,
                                        t.uploaded_file, 
                                        t.uploadedByType,
                                        (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status,
                                        t.activity_duration,
                                        t.ifc_farmer_count,
                                        t.total_pda,
                                        t.total_activity_distance,
                                        t.distance,
                                        t.is_valid,
                                        t.radius,
                                        t.dd_ticket_role
                                    FROM tickets t
                                    LEFT JOIN data_sources  d   ON t.data_source_id=d.data_source_id
                                    LEFT JOIN plan_atp  pat   ON pat.plan_atp_id=t.plan_atp_id
                                    LEFT JOIN languages  lan   ON lan.id=t.language_id

                                    LEFT JOIN states        s   ON t.state_id=s.state_id
                                    LEFT JOIN districts     sd  ON t.district_id=sd.district_id
                                    LEFT JOIN tehsils       sdt ON t.tehsil_id=sdt.tehsil_id
                                    LEFT JOIN territories   sdtr ON t.territory_id=sdtr.territory_id
                                    LEFT JOIN products      p1  ON t.product_1_id=p1.product_id
                                    LEFT JOIN products      p2  ON t.product_2_id=p2.product_id
                                    LEFT JOIN products      p3  ON t.product_3_id=p3.product_id
                                    LEFT JOIN demo_request      dr  ON t.demo_request_id=dr.demo_request_id
                                    LEFT JOIN crops         c1  ON t.crop_1_id=c1.crop_id
                                    LEFT JOIN crops         c2  ON t.crop_2_id=c2.crop_id
                                    LEFT JOIN crops         c3  ON t.crop_3_id=c3.crop_id
                                    LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=t.assign_to_dd
                                    WHERE (t.user_id in ('" . $bdo_ids . "')  AND
                                        t.user_type='" . $user_type . "'  AND t.created_at BETWEEN '".$default_from_date." 00:00:00 ' and '".$default_to_date." 23:59:59 ' AND t.status != '0' AND t.ticket_status != '0' $addFlQry OR 
                                        t.ticket_id in  (SELECT ticket_id FROM tickets where user_id in ('".$dd_ids."') and user_type = 'dhanuka_doctor')) 
                                        AND t.created_at BETWEEN '".$default_from_date." 00:00:00 ' and '".$default_to_date." 23:59:59 ' AND t.ticket_status != '0' $addQry
                                    GROUP BY  
                                        t.ticket_id
                                    ORDER BY 
                                        FIELD(ticket_status, '1') DESC, t.ticket_id DESC
                                    LIMIT
                                        " . $offset . ", 50    
                                    "; 
                                    // echo $query;die;
                        /*Log::info($user_id."-s1ticketHistory query=>");
                        Log::info($query);*/
                    }
                    else
                    {
                        $asm_ids = "";
                        $rsm_ids = "";
                        $bdo_ids = "";
                        $dd_ids = "";

                        if ($user_type == 'zonal') {
                            //zonal manager RSM
                            $rsmData = DB::table('sales_regional_managers')
                                      ->where('zonal_manager_id',$user_id)
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
                            }

                            //sales regional manager ASM
                            $asmData = DB::table('sales_area_managers')
                                      ->where('zonal_manager_id',$user_id)
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
                            }

                            //zonal manager dd
                            $ddData = DB::table('dhanuka_doctor')
                                      ->where('zonal_manager_id',$user_id)
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

                            //zonal manager bdo
                            $bdoData = DB::table('users')
                                      ->where('zonal_manager_id',$user_id)
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
                            $dd_ids  = implode("','", $ddresult);
                            $bdo_ids = implode("','", $bdoresult);
                            $asm_ids = implode("','", $asmresult);
                            $rsm_ids = implode("','", $rsmresult);
                        }
                        elseif ($user_type == 'regional') {
                            //regional manager dd
                            $ddData = DB::table('dhanuka_doctor')
                                      ->where('regional_manager_id',$user_id)
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

                            //regional manager bdo
                            $bdoData = DB::table('users')
                                      ->where('regional_manager_id',$user_id)
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
                            $dd_ids = implode("','", $ddresult);
                            $bdo_ids = implode("','", $bdoresult);
                        }
                        elseif ($user_type == 'area') {
                            //area manager dd
                            $ddData = DB::table('dhanuka_doctor')
                                      ->where('area_manager_id',$user_id)
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

                            //area manager bdo
                            $bdoData = DB::table('users')
                                      ->where('area_manager_id',$user_id)
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
                            $dd_ids = implode("','", $ddresult);
                            $bdo_ids = implode("','", $bdoresult);
                        }
                        elseif ($user_type == 'sales_regional') {
                            //sales regional manager ASM
                            $asmData = DB::table('sales_area_managers')
                                      ->where('sales_regional_manager_id',$user_id)
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
                            }

                            //sales regional manager dd
                            $ddData = DB::table('dhanuka_doctor')
                                      ->where('sales_regional_manager_id',$user_id)
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
                                      ->where('sales_regional_manager_id',$user_id)
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
                            $dd_ids = implode("','", $ddresult);
                            $bdo_ids = implode("','", $bdoresult);
                            $asm_ids = implode("','", $asmresult);
                        }
                        elseif ($user_type == 'sales_area') 
                        {
                            //Sales area manager dd
                            $ddData = DB::table('dhanuka_doctor')
                                      ->where('sales_area_manager_id',$user_id)
                                      ->where('status','1')
                                      ->select('dd_id')
                                      ->get();
                            $ddresult = array(); 
                            if (isset($ddData) && !empty($ddData)) 
                            {
                                $ddDatass = json_decode(json_encode($ddData),true);
                                $ddresult = [];
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

                            //Sales area manager bdo
                            $bdoData = DB::table('users')
                                      ->where('sales_area_manager_id',$user_id)
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
                            $dd_ids = implode("','", $ddresult);
                            $bdo_ids = implode("','", $bdoresult);
                        }
                        elseif ($user_type == 'marketing_supervisor') 
                        {
                            //Marketing Supervisor RSM LIST
                            $rsmData = DB::table('marketing_supervisor_rsm')
                                      ->where('marketing_supervisor_id',$user_id)
                                      ->select('sales_regional_manager_id')
                                      ->get();
                            $rsmresult = array(); 
                            if (isset($rsmData) && !empty($rsmData)) 
                            {   
                                //RSM LIST
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

                                if (count($rsmresult) > 0) {
                                    //marketing supervisor bdo
                                    $bdoData = DB::table('users')
                                              ->whereIn('sales_regional_manager_id',$rsmresult)
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
                                        //marketing supervisor dd
                                        $ddData = DB::table('dhanuka_doctor')
                                                  ->whereIn('sales_regional_manager_id',$rsmresult)
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
                                    }
                                }
                                $dd_ids = implode("','", $ddresult);
                                $bdo_ids = implode("','", $bdoresult);
                            }
                        }
                        $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.ticket_no as ticket,
                                        IF( pat.updated_at != '0000-00-00', DATE_FORMAT(pat.updated_at, '%Y-%m-%d %H:%i:%s'), t.created_at ) as datetime,
                                        IF( pat.updated_at != '0000-00-00', DATE_FORMAT(pat.updated_at, '%W, %M %e %Y, %h:%i %p'), DATE_FORMAT(t.created_at, '%W, %M %e %Y, %h:%i %p') ) as datetime_format,
                                        CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
                                        d.data_source,
                                        s.state,
                                        sd.district,
                                        sdt.tehsil,
                                        sdtr.territory,
                                        t.village,
                                        dr.demo_code as demo_request_id,
                                        p1.product  AS product_1,
                                        p2.product  AS product_2,
                                        p3.product  AS product_3,
                                        c1.crop     AS crop_1,
                                        c2.crop     AS crop_2,
                                        c3.crop     AS crop_3,
                                        t.expected_farmers,
                                        t.expected_pda,
                                        t.sp_name_1,
                                        t.sp_number_1,
                                        t.sp_name_2,
                                        t.sp_number_2,
                                        t.sp_name_3,
                                        t.sp_number_3,
                                        t.sp_name_4,
                                        t.sp_number_4,
                                        t.sp_name_5,
                                        t.sp_number_5,
                                        t.sp_name_6,
                                        t.sp_number_6,
                                        t.language_id,
                                        t.user_farmer,
                                        lan.language,
                                        t.user_id,
                                        t.user_type,
                                        dd.name as dd_name,
                                        t.assign_to_dd as dd_id,
                                        IF( t.image_upload_type != '0', '" . $base_url1 . "/', '" . $base_url . "/' ) as base_url,
                                        t.uploaded_file, 
                                        t.uploadedByType,
                                        (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status,
                                        t.activity_duration,
                                        t.ifc_farmer_count,
                                        t.total_pda,
                                        t.total_activity_distance,
                                        t.distance,
                                        t.is_valid,
                                        t.radius,
                                        t.dd_ticket_role
                                    FROM tickets t
                                    LEFT JOIN data_sources  d   ON t.data_source_id=d.data_source_id
                                    LEFT JOIN plan_atp  pat   ON pat.plan_atp_id=t.plan_atp_id
                                    LEFT JOIN languages  lan   ON lan.id=t.language_id

                                    LEFT JOIN states        s   ON t.state_id=s.state_id
                                    LEFT JOIN districts     sd  ON t.district_id=sd.district_id
                                    LEFT JOIN tehsils       sdt ON t.tehsil_id=sdt.tehsil_id
                                    LEFT JOIN territories   sdtr ON t.territory_id=sdtr.territory_id
                                    LEFT JOIN products      p1  ON t.product_1_id=p1.product_id
                                    LEFT JOIN products      p2  ON t.product_2_id=p2.product_id
                                    LEFT JOIN products      p3  ON t.product_3_id=p3.product_id
                                    LEFT JOIN demo_request      dr  ON t.demo_request_id=dr.demo_request_id
                                    LEFT JOIN crops         c1  ON t.crop_1_id=c1.crop_id
                                    LEFT JOIN crops         c2  ON t.crop_2_id=c2.crop_id
                                    LEFT JOIN crops         c3  ON t.crop_3_id=c3.crop_id
                                    LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=t.assign_to_dd
                                    WHERE 
                                       (t.user_id in ('" . $user_id . "')  AND
                                        t.user_type='".$user_type."'  
                                        AND t.created_at BETWEEN '".$default_from_date." 00:00:00 ' and '".$default_to_date." 23:59:59 ' AND t.status != '0' AND t.ticket_status != '0' OR t.user_id in ('" . $bdo_ids . "')  AND

                                        t.user_type='bdo'  AND t.created_at BETWEEN '".$default_from_date." 00:00:00 ' and '".$default_to_date." 23:59:59 ' AND t.status != '0' AND t.ticket_status != '0' OR 
                                        t.ticket_id in  (SELECT ticket_id FROM tickets where user_id in ('".$dd_ids."') and user_type = 'dhanuka_doctor') 
                                        AND t.created_at BETWEEN '".$default_from_date." 00:00:00 ' and '".$default_to_date." 23:59:59 ' AND t.status != '0' AND t.ticket_status != '0' OR 
                                        t.ticket_id in  (SELECT ticket_id FROM tickets where user_id in ('".$asm_ids."') and user_type = 'sales_area') 
                                        AND t.created_at BETWEEN '".$default_from_date." 00:00:00 ' and '".$default_to_date." 23:59:59 ' AND t.status != '0' AND t.ticket_status != '0' OR 
                                        t.ticket_id in  (SELECT ticket_id FROM tickets where user_id in ('".$rsm_ids."') and user_type = 'sales_regional') 
                                        AND t.created_at BETWEEN '".$default_from_date." 00:00:00 ' and '".$default_to_date." 23:59:59 ' AND t.status != '0' AND t.ticket_status != '0') 
                                        $addQry
                                    GROUP BY  
                                        t.ticket_id
                                    ORDER BY 
                                        FIELD(ticket_status, '1') DESC, t.ticket_id DESC
                                    LIMIT
                                        " . $offset . ", 50    
                                    "; 
                    
                    }

                    $ticket_history = DB::select(DB::raw($query)); 
                    $result = array_map(function ($value) {
                        return (array) $value;
                    }, $ticket_history);
                    
                    $log_firstTime  = strtotime($log_start_time);
                    $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                    $log_time_diff  = $log_lastTime-$log_firstTime;
                    $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                    DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                    if ($ticket_history) {
                        return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Ticket History Found!',
                            'crr_date' => $crr_date,
                            'data' => $result,
                        ]);
                    } else {
                        return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'No Tickets Found!',
                            'crr_date' => $crr_date,
                            'data' => array()
                        ]);
                        // return $this->respondNotFound("No Record Found!");
                    }
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s1createTicket(Request $request) {
        try {
            $data = $request->json()->all();
            // echo "<pre>"; print_r($data); die;
            $rules = array(
                'api_token' => 'required|string',
                'dsId'      => 'required|numeric',
                'sId'       => 'required|numeric',
                'dId'       => 'required|numeric',
                'tId'       => 'required|numeric',
                'village'   => 'string',
                'expected_farmers'  => 'numeric',
                'expected_pda'      => 'numeric',
                'product_1' => 'numeric',
                'crop_1'    => 'numeric',
                'product_2' => 'numeric',
                'crop_2'    => 'numeric',
                'product_3' => 'numeric',
                'crop_3'    => 'numeric',
                'sp_name_1' => 'string',
                'sp_number_1'   => 'numeric',
                'sp_name_2'     => 'string',
                'sp_number_2'   => 'numeric',
                'sp_name_3'     => 'string',
                'sp_number_3'   => 'numeric',
                'sp_name_4'     => 'string',
                'sp_number_4'   => 'numeric',
                'sp_name_5'     => 'string',
                'sp_number_5'   => 'numeric',
                'sp_name_6'     => 'string',
                'sp_number_6'   => 'numeric',
                // 'latitude'      => 'string',
                // 'longitude'     => 'string'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['api_token'];
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    $data_source = DataSource::where('data_source_id', $data['dsId'])->first();
                    if (!$data_source) {
                        return $this->respondWithError("Invalid Data Source!");
                    }
                    $state = State::where('state_id', $data['sId'])->first();
                    if (!$state) {
                        return $this->respondWithError("Invalid State!");
                    }
                    $district = District::where('district_id', $data['dId'])->first();
                    if (!$district) {
                        return $this->respondWithError("Invalid District!");
                    }
                    $tehsil = Tehsil::where('tehsil_id', $data['tId'])->first();
                    if (!$tehsil) {
                        return $this->respondWithError("Invalid Tehsil!");
                    }
                    if ($data['product_1'] != '') {
                        $product = Product::where('product_id', $data['product_1'])->first();
                        if (!$product) {
                            return $this->respondWithError("Invalid Product 1!");
                        }
                        if ($data['crop_1'] == '') {
                            return $this->respondWithError("The crop 1 must be required!");
                        }
                    }
                    if ($data['product_2'] != '') {
                        $product = Product::where('product_id', $data['product_2'])->first();
                        if (!$product) {
                            return $this->respondWithError("Invalid Product 2!");
                        }
                        if ($data['crop_2'] == '') {
                            return $this->respondWithError("The crop 2 must be required!");
                        }
                    }
                    if ($data['product_3'] != '') {
                        $product = Product::where('product_id', $data['product_3'])->first();
                        if (!$product) {
                            return $this->respondWithError("Invalid Product 3!");
                        }
                        if ($data['crop_3'] == '') {
                            return $this->respondWithError("The crop 3 must be required!");
                        }
                    }
                    if ($data['crop_1'] != '') {
                        $crop = Crop::where('crop_id', $data['crop_1'])->first();
                        if (!$crop) {
                            return $this->respondWithError("Invalid Crop 1!");
                        }
                        if ($data['product_1'] == '') {
                            return $this->respondWithError("The product 1 must be required!");
                        }
                    }
                    if ($data['crop_2'] != '') {
                        $crop = Crop::where('crop_id', $data['crop_2'])->first();
                        if (!$crop) {
                            return $this->respondWithError("Invalid Crop 2!");
                        }
                        if ($data['product_2'] == '') {
                            return $this->respondWithError("The product 2 must be required!");
                        }
                    }
                    if ($data['crop_3'] != '') {
                        $crop = Crop::where('crop_id', $data['crop_3'])->first();
                        if (!$crop) {
                            return $this->respondWithError("Invalid Crop 3!");
                        }
                        if ($data['product_3'] == '') {
                            return $this->respondWithError("The product 3 must be required!");
                        }
                    }

                    if ($data['product_1'] == '' && $data['product_2'] == '' && $data['product_3'] == '') {
                        return $this->respondWithError("At least 1 product is required!");
                    }
                    if ($data['crop_1'] == '' && $data['crop_2'] == '' && $data['crop_3'] == '') {
                        return $this->respondWithError("At least 1 crop is required!");
                    }

                    // if ($data['latitude'] == '') {
                    //     return $this->respondWithError("Latitude is required!");
                    // }
                    // if ($data['longitude'] == '') {
                    //     return $this->respondWithError("Longitude is required!");
                    // }
                    
                    //Ticket::where('user_id', $user_id)->update(['status' => '0', 'ticket_status' => '0']);
                    if (isset($data['ticket_no']) && !empty($data['ticket_no'])) {
                        $ticket_no = $data['ticket_no'];
                        // $ticket_no   = $this->generateTicketNumber($user_id, 'SV2', $data['ticket_no']);
                        $ticketno_by = 2;
                    }
                    else
                    {
                        /*$count_ticket = Ticket::count();
                        $count_ticket = $count_ticket + $user_id;
                        $count_ticket = $count_ticket + 150 + rand(100,999) + rand(10,99);
                        $ticket_no    = rand(10,99).rand(10,99).rand(10,99).rand(1,10) + $count_ticket;
                        $ticket_no    = "SV2" . $ticket_no;*/
                        $ticket_no    = $this->generateTicketNumber($user_id, 'SV2');
                        $ticketno_by  = 1;
                    }

                    $validator = Validator::make(['ticket_no' => $ticket_no], ['ticket_no' => 'unique:tickets,ticket_no']);
                    if ($validator->fails()) {
                        //$this->randomId();
                        return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
                    }
                    try {
                        if (isset($data['dd_ticket_role']) && !empty($data['dd_ticket_role'])) {
                            $dd_ticket_role = $data['dd_ticket_role'];
                        } else {
                            $dd_ticket_role = 0;
                        }
                        if (!empty($data['activity_duration'])) {
                            $activity_duration = $data['activity_duration'];
                        } else {
                            $activity_duration = "";
                        }
                        $village_id = isset($data['village_id']) ? $data['village_id'] : 0;
                        $latitude   = isset($data['latitude']) ? $data['latitude'] : 0;
                        $longitude  = isset($data['longitude']) ? $data['longitude'] : 0;
                        if($village_id !=0 && $latitude !=0 && $longitude !=0 ) {
                            $distanceData = $this->CalculateDistance($village_id, $latitude, $longitude);
                            if($distanceData!=0) {
                                $expDistanceData = explode("~", $distanceData);
                                $distance = $expDistanceData[0];
                                $is_valid = $expDistanceData[1];
                                $radius   = $expDistanceData[2];
                            } else {
                                $distance = 0;
                                $radius   = 0;
                                $is_valid = 0;
                            }
                        } else {
                            $distance = 0;
                            $radius   = 0;
                            $is_valid = 0;
                        }
                        if ($user_type == 'bdo') {
                            if (!empty($data['dd_id']) && $data['dd_id'] != '0') {
                                Ticket::where('assign_to_dd',$data['dd_id'])->where('ticket_status','=','1')->update(['status' => '2','ticket_status' => '2','closing_datetime' => date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
                                PlanAtp::where('dd_id', $data['dd_id'])->where('status', '1')->update(['status' => '2','updated_at'=>date('Y-m-d H:i:s')]);
                            } else {
                                Ticket::where('user_id', $user_id)->where('user_type',$user_type)->where('assign_to_dd','0')->where('ticket_status','=','1')->where('forwhom','=','1')->update(['status' => '2','ticket_status' => '2','closing_datetime' => date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
                                PlanAtp::where('created_by_user_id', $user_id)->where('user_type',$user_type)->where('dd_id', '0')->where('status', '1')->where('forwhom','=','1')->update(['status' => '2','updated_at'=>date('Y-m-d H:i:s')]);
                            }
                            Ticket::create([
                                'ticket_no'     => $ticket_no,
                                'ticketno_by'   => $ticketno_by,
                                'user_id'       => $user_id,
                                'user_type'     => $user_type,
                                'data_source_id'=> $data['dsId'],
                                'state_id'      => $data['sId'],
                                'district_id'   => $data['dId'],
                                'tehsil_id'     => $data['tId'],
                                'territory_id'  => isset($data['territory_id']) ? $data['territory_id'] : 0 ,
                                'language_id'   => isset($data['language_id']) ? $data['language_id'] : 0 ,
                                'village'       => $data['village'],
                                'village_id'    => isset($data['village_id']) ? $data['village_id'] : 0,
                                'expected_farmers'  => $data['expected_farmers'],
                                'expected_pda'  => $data['expected_pda'],
                                'product_1_id'  => $data['product_1'],
                                'crop_1_id'     => $data['crop_1'],
                                'product_2_id'  => $data['product_2'],
                                'crop_2_id'     => $data['crop_2'],
                                'product_3_id'  => $data['product_3'],
                                'crop_3_id'     => $data['crop_3'],
                                'sp_name_1'     => $data['sp_name_1'],
                                'sp_number_1'   => $data['sp_number_1'],
                                'sp_name_2'     => $data['sp_name_2'],
                                'sp_number_2'   => $data['sp_number_2'],
                                'sp_name_3'     => $data['sp_name_3'],
                                'sp_number_3'   => $data['sp_number_3'],
                                'sp_name_4'     => $data['sp_name_4'],
                                'sp_number_4'   => $data['sp_number_4'],
                                'sp_name_5'     => $data['sp_name_5'],
                                'sp_number_5'   => $data['sp_number_5'],
                                'sp_name_6'     => $data['sp_name_6'],
                                'sp_number_6'   => $data['sp_number_6'],
                                'activity_duration' => $activity_duration,
                                'ticket_status' => '1',
                                'status'        => '1',
                                'demo_request_id'   => ($data['demo_request_id'])?$data['demo_request_id']:"",
                                'user_farmer'   => ($data['user_farmer'])?$data['user_farmer']:"",
                                'assign_to_dd'  => $data['dd_id'],
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s'),
                                'latitude'      => $data['latitude'],
                                'longitude'     => $data['longitude'],
                                'distance'      => $radius,
                                'is_valid'      => $is_valid,
                                'radius'        => $radius,
                                'dd_ticket_role'=> $dd_ticket_role
                            ]);
                        } elseif ($user_type == 'dhanuka_doctor') {
                            Ticket::where('assign_to_dd', $user_id)->where('ticket_status','=','1')->where('status','=','1')->update(['status' => '2','ticket_status' => '2', 'closing_datetime' => date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);                            
                            PlanAtp::where('dd_id', $user_id)->where('status', '1')->update(['status' => '2','updated_at'=>date('Y-m-d H:i:s')]);
                            Ticket::create([
                                'ticket_no'     => $ticket_no,
                                'ticketno_by'   => $ticketno_by,
                                'user_id'       => $user_id,
                                'user_type'     => $user_type,
                                'data_source_id'=> $data['dsId'],
                                'state_id'      => $data['sId'],
                                'district_id'   => $data['dId'],
                                'tehsil_id'     => $data['tId'],
                                'territory_id'  => isset($data['territory_id']) ? $data['territory_id'] : 0 ,
                                'language_id'   => isset($data['language_id']) ? $data['language_id'] : 0 ,
                                'village'       => $data['village'],
                                'village_id'    => isset($data['village_id']) ? $data['village_id'] : 0,
                                'expected_farmers' => $data['expected_farmers'],
                                'expected_pda'  => $data['expected_pda'],
                                'product_1_id'  => $data['product_1'],
                                'crop_1_id'     => $data['crop_1'],
                                'product_2_id'  => $data['product_2'],
                                'crop_2_id'     => $data['crop_2'],
                                'product_3_id'  => $data['product_3'],
                                'crop_3_id'     => $data['crop_3'],
                                'sp_name_1'     => $data['sp_name_1'],
                                'sp_number_1'   => $data['sp_number_1'],
                                'sp_name_2'     => $data['sp_name_2'],
                                'sp_number_2'   => $data['sp_number_2'],
                                'sp_name_3'     => $data['sp_name_3'],
                                'sp_number_3'   => $data['sp_number_3'],
                                'sp_name_4'     => $data['sp_name_4'],
                                'sp_number_4'   => $data['sp_number_4'],
                                'sp_name_5'     => $data['sp_name_5'],
                                'sp_number_5'   => $data['sp_number_5'],
                                'sp_name_6'     => $data['sp_name_6'],
                                'sp_number_6'   => $data['sp_number_6'],
                                'activity_duration' => $activity_duration,
                                'ticket_status' => '1',
                                'status'        => '1',
                                'demo_request_id' => ($data['demo_request_id'])?$data['demo_request_id']:"",
                                'user_farmer'   => ($data['user_farmer'])?$data['user_farmer']:"",
                                'assign_to_dd'  => $user_id,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s'),
                                'latitude'      => $data['latitude'],
                                'longitude'     => $data['longitude'],
                                'distance'      => $radius,
                                'is_valid'      => $is_valid,
                                'radius'        => $radius,
                                'dd_ticket_role'=> $dd_ticket_role
                            ]);
                        } else {
                            Ticket::where('user_id', $user_id)->where('user_type',$user_type)->where('assign_to_supervisor', $user_id)->where('ticket_status','=','1')->update(['status' => '2','ticket_status' => '2','closing_datetime' => date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
                            // PlanAtp::where('created_by_user_id', $user_id)->where('user_type',$user_type)->where('dd_id', '0')->where('status', '1')->update(['status' => '2','updated_at'=>date('Y-m-d H:i:s')]);
                            Ticket::create([
                                'ticket_no'     => $ticket_no,
                                'ticketno_by'   => $ticketno_by,
                                'user_id'       => $user_id,
                                'user_type'     => $user_type,
                                'assign_to_supervisor' => $user_id,
                                'data_source_id'=> $data['dsId'],
                                'state_id'      => $data['sId'],
                                'district_id'   => $data['dId'],
                                'tehsil_id'     => $data['tId'],
                                'territory_id'  => isset($data['territory_id']) ? $data['territory_id'] : 0 ,
                                'language_id'   => isset($data['language_id']) ? $data['language_id'] : 0 ,
                                'village'       => $data['village'],
                                'village_id'    => isset($data['village_id']) ? $data['village_id'] : 0,
                                'expected_farmers' => $data['expected_farmers'],
                                'expected_pda'  => $data['expected_pda'],
                                'product_1_id'  => $data['product_1'],
                                'crop_1_id'     => $data['crop_1'],
                                'product_2_id'  => $data['product_2'],
                                'crop_2_id'     => $data['crop_2'],
                                'product_3_id'  => $data['product_3'],
                                'crop_3_id'     => $data['crop_3'],
                                'sp_name_1'     => $data['sp_name_1'],
                                'sp_number_1'   => $data['sp_number_1'],
                                'sp_name_2'     => $data['sp_name_2'],
                                'sp_number_2'   => $data['sp_number_2'],
                                'sp_name_3'     => $data['sp_name_3'],
                                'sp_number_3'   => $data['sp_number_3'],
                                'sp_name_4'     => $data['sp_name_4'],
                                'sp_number_4'   => $data['sp_number_4'],
                                'sp_name_5'     => $data['sp_name_5'],
                                'sp_number_5'   => $data['sp_number_5'],
                                'sp_name_6'     => $data['sp_name_6'],
                                'sp_number_6'   => $data['sp_number_6'],
                                'activity_duration' => $activity_duration,
                                'ticket_status' => '1',
                                'status'        => '1',
                                'demo_request_id' => ($data['demo_request_id'])?$data['demo_request_id']:"",
                                'user_farmer'   => ($data['user_farmer'])?$data['user_farmer']:"",
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s'),
                                'latitude'      => $data['latitude'],
                                'longitude'     => $data['longitude'],
                                'distance'      => $radius,
                                'is_valid'      => $is_valid,
                                'radius'        => $radius,
                                'dd_ticket_role'=> $dd_ticket_role
                            ]);
                        }
                        $tid = DB::table('tickets')
                               ->where('ticket_no',$ticket_no)
                               ->select('ticket_id')
                               ->get();

                        return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Ticket Successfully Created!',
                            'data' => $ticket_no,
                            'ticket_id' => $tid[0]->ticket_id
                        ]);
                    } catch (Exception $e) {
                        return $this->respondInternalError("An error occurred while performing an action!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function villageList(Request $request) {
        $data = $request->json()->all();
        //$base_url = url('/');
        $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
        if ($data) {
            $rules = array(
                'apiToken' => 'required|string',
                'ECN' => 'required|string',
                'updated_date' => 'required'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $curTime = microtime(true);
                $log_start_time = date("Y-m-d H:i:s");
                $log_data = array(
                    'api_name'   => "v3/villageList",
                    'start_time' => $log_start_time,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                );
                $log_result = DB::table('query_log')->insertGetId($log_data);

                $crr_date = date('Y-m-d H:i:s');
                $updated_date = $data['updated_date'];
                $api_token = $data['apiToken'];
                $ecn = $data['ECN'];
                $user = $this->findUser($api_token);
                
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    //$ddData = DhanukaDoctor::where('dd_id', $user_id)->first();
                    if ($updated_date != 0) {
                        $ddData = DB::table('assigned_village')
                            ->join('villages','villages.village_id','=','assigned_village.village_id')
                            ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                            ->join('districts','districts.district_id','=','villages.district_id')
                            ->join('states','states.state_id','=','districts.state_id')
                            ->select('villages.village_id as villageId','villages.village as villageName','states.state','tehsils.tehsil','districts.district','villages.updated_at')
                            ->where('villages.status', '1')
                            ->where('assigned_village.assigned_to_dd_id', $user_id)
                            ->whereNull('villages.updated_at')
                            ->orWhere('villages.updated_at','>', $updated_date)
                            ->groupBy('assigned_village.village_id')
                            ->get();
                    } else {
                        $ddData = DB::table('assigned_village')
                            ->join('villages','villages.village_id','=','assigned_village.village_id')
                            ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                            ->join('districts','districts.district_id','=','villages.district_id')
                            ->join('states','states.state_id','=','districts.state_id')
                            ->select('villages.village_id as villageId','villages.village as villageName','states.state','tehsils.tehsil','districts.district','villages.updated_at')
                            ->where('villages.status', '1')
                            ->where('assigned_village.assigned_to_dd_id', $user_id)
                            ->groupBy('villages.village_id')
                            ->groupBy('assigned_village.village_id')
                            ->get();
                    }
                    for($i=0;$i<count($ddData);$i++){
                        $cropList=VillageFarmerCrop::join('crops','crops.crop_id','=','village_farmer_crop.crop_id')
                            ->where('village_id',$ddData[$i]->villageId)
                            ->groupBy('village_farmer_crop.crop_id')
                            ->select('crops.crop',DB::raw("sum(acres) as acres"),DB::raw("count(*) as farmersCount"))
                            ->get();
                        $farmers=VillageFarmer::where('village_id',$ddData[$i]->villageId)->count();
                        $ddData[$i]->farmers=$farmers;
                        $villageVisit=MarkVisit::where('village_id',$ddData[$i]->villageId)->count();
                        $ddData[$i]->avgVisit=$villageVisit;
                        $ddData[$i]->cropList=$cropList;
                    }
                    
                    $log_firstTime  = strtotime($log_start_time);
                    $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                    $log_time_diff  = $log_lastTime-$log_firstTime;
                    $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                    DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Village List',
                        'crr_date' => $crr_date,
                        'data' => $ddData
                    ]);
                } else {
                    return $this->respondWithConflict("Invalid Attempt !!");
                }
            }
        } else {
            return $this->respondWithError("Invalid Request !!");
        }
    }

    public function addIfcFarmer(Request $request) {
        $rules = array(
            'apiToken'        => 'required|string',
            'farmer_name'     => 'required',
            'mobile'          => 'required',
            'image'           => 'required',
            'image_date_time' => 'required',
            'latitude'        => 'required',
            'longitude'       => 'required',
            'acreage'         => 'required',
            'ticket_no'       => 'required',
            'activity_distance' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $request_data   = Input::all();
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data       = array(
                'api_name'   => "v3/addIfcFarmer",
                'start_time' => $log_start_time,
                'request_data' => json_encode($request_data),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            
            if ($user && count($user) == '1') {
                $user_id        = $user[0]->id;
                $upload_user_id = $user[0]->id;
                $user_type      = $user[0]->user_type;
                $userTypeVal    = $this->getUserType($user_type,$user_id);
                $mobile         = $request->input('mobile');
                $ticket_no      = $request->input('ticket_no');

                if ($user_type == 'dhanuka_doctor') {
                    $ticketData = Ticket::where('ticket_no', $ticket_no)->where('data_source_id', 6)->where('assign_to_dd', $user_id)->first();
                } else {
                    if($user_type == "bdo") {
                        $ticketData = Ticket::where('ticket_no', $ticket_no)->where('data_source_id', 6)->first();
                        if($ticketData->forwhom==2) {
                            $checkbdotype = DB::table('users')->where('id',$user_id)->where('status','1')->select('designation','id')->get();
                            if ($checkbdotype[0]->designation == 'COUNTERPART-FL') {
                                $fl_id    = $user_id;
                                $checkbdo = DB::table('users')->where('counterpart_fl',$checkbdotype[0]->id)->where('status','1')->select('id')->get();
                                if(count($checkbdo)>0) {
                                    $user_id = $checkbdo[0]->id;
                                }
                            } else {
                                $fl_id = $user_id;
                            }
                        } else {
                            $fl_id = $user_id;
                        }
                    } else {
                        $fl_id = $user_id;
                    }
                    $ticketData = Ticket::where('ticket_no', $ticket_no)
                                      //->where('created_by_user_id',$user_id)
                                      ->where('user_id',$fl_id)
                                      ->where('user_type',$user_type)
                                      ->where('assign_to_dd',0)
                                      ->first();
                }
                // $ticketData = Ticket::where(['ticket_no'=> $ticket_no,'user_id'=> $user_id,'user_type'=> $user_type])->first();
                if($ticketData) {
                    $ticket_id = $ticketData->ticket_id;
                    $farmer_name = $request->input('farmer_name');
                    $check_farmer = Farmer::where(['ticket_id'=> $ticket_id,'user_id'=> $user_id,'user_type'=> $user_type,'mobile'=>$mobile])->first();
                    if($check_farmer) {
                        Farmer::where(['ticket_id'=> $ticket_id,'user_id'=> $user_id,'user_type'=> $user_type])
                        ->update([
                            // 'farmer_name'=> $request->input('farmer_name'),
                            'mobile'=> $request->input('mobile'),
                            'updated_at'=> date('Y-m-d H:i:s')
                        ]);
                    } else {
                        $farmer_data_array = array(
                                                'ticket_id'   => $ticket_id,
                                                'user_id'     => $user_id,
                                                'user_type'   => $user_type,
                                                'mobile'      => $mobile,
                                                'farmer_type' => 2,
                                                'farmer_name' => $farmer_name,
                                                'datetime'    => date('Y-m-d H:i:s'),
                                                'registration_type' => 'manual',
                                                'ticket_status' => '1',
                                                'created_at'  => date('Y-m-d H:i:s'),
                                                'updated_at'  => date('Y-m-d H:i:s')
                                            );
                        $farmerData = Farmer::firstOrNew($farmer_data_array);
                        $farmerData->save();
                    }
                }
                $ifcfarmers = IFCVillageFarmer::where('mobile',$mobile)->where('ticket_no',$ticket_no)->get();
                $ifcfarmer = array();
                if(count($ifcfarmers) == 0) {
                    $ticket_no = $request->input('ticket_no');
                    $IFCFarmer = new IFCVillageFarmer;
                    $IFCFarmer->user_id     = $user_id;
                    $IFCFarmer->user_type   = $user_type;
                    $IFCFarmer->farmer_name = $request->input('farmer_name');
                    $IFCFarmer->mobile      = $request->input('mobile');
                    $IFCFarmer->image_date_time = $request->input('image_date_time');
                    $IFCFarmer->latitude    = $request->input('latitude');
                    $IFCFarmer->longitude   = $request->input('longitude');
                    $IFCFarmer->acreage     = $request->input('acreage');
                    $IFCFarmer->ticket_no   = $request->input('ticket_no');
                    $IFCFarmer->activity_distance = $request->input('activity_distance');

                    $sTempFileName = '';
                    if($request->file('image')) {
                        $file=$request->file('image');
                        //s3Upload
                        $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                        $sTempFileName = 'assets/farmer/' . $fileName;
                        Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                        $IFCFarmer->image= $fileName;
                    }
                    $IFCFarmer->save();
                    $ifcfarmer_id = $IFCFarmer->id;
                    if($ifcfarmer_id) {
                        $ifcFarmerCount = IFCVillageFarmer::where('ticket_no',$ticket_no)->count();
                        if($ifcFarmerCount == 1) {
                            Ticket::where(['ticket_no' => $ticket_no, 'data_source_id' => 6])->update(['image_upload_type'=>1,'uploaded_file' => $sTempFileName,'uploadedBy' => $upload_user_id,'uploadedByType' => $userTypeVal,'updated_at'=>date('Y-m-d H:i:s')]);
                        }
                        $ifc_farmer = IFCVillageFarmer::where('ticket_no',$ticket_no)->groupBy('mobile')->get();
                        $ifc_farmer_count = count($ifc_farmer);
                        Ticket::where(['ticket_no' => $ticket_no, 'data_source_id' => 6])->update(['ifc_farmer_count' => $ifc_farmer_count,'updated_at'=>date('Y-m-d H:i:s')]);
                        $ifcfarmer = IFCVillageFarmer::where('id',$ifcfarmer_id)->get();
                    }
                    $log_firstTime  = strtotime($log_start_time);
                    $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                    $log_time_diff  = $log_lastTime-$log_firstTime;
                    $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                    DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                    return $this->respond([
                        'status'      => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message'     => 'Farmer has been added Successfully.',
                        'image_path'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/',
                        'data'        => $ifcfarmer
                    ]);
                } else {
                    $ifcfarmer_id = $ifcfarmers[0]->id;
                    $imagename ='';
                    $sTempFileName ='';
                    if($request->file('image')) {
                        $file=$request->file('image');
                        //s3Upload
                        $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                        $sTempFileName = 'assets/farmer/' . $fileName;
                        Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                        $imagename = $fileName;
                    }
                    IFCVillageFarmer::where('id',$ifcfarmer_id)
                        ->update([
                            'user_id'    => $user_id,
                            'user_type'  => $user_type,
                            'farmer_name'=> $request->input('farmer_name'),
                            'mobile'     => $request->input('mobile'),
                            'image'      => $imagename,
                            'image_date_time'=> $request->input('image_date_time'),
                            'latitude'   => $request->input('latitude'),
                            'longitude'  => $request->input('longitude'),
                            'acreage'    => $request->input('acreage'),
                            'ticket_no'  => $ticket_no,
                            'activity_distance'=> $request->input('activity_distance'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                    $ifcFarmerCount = IFCVillageFarmer::where('ticket_no',$ticket_no)->count();
                    if($ifcFarmerCount == 1) {
                        Ticket::where(['ticket_no' => $ticket_no, 'data_source_id' => 6])->update(['image_upload_type'=>1,'uploaded_file' => $sTempFileName,'uploadedBy' => $upload_user_id,'uploadedByType' => $userTypeVal,'updated_at'=>date('Y-m-d H:i:s')]);
                    }
                    $ifcfarmer = IFCVillageFarmer::where('id',$ifcfarmer_id)->get();

                    $log_firstTime  = strtotime($log_start_time);
                    $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                    $log_time_diff  = $log_lastTime-$log_firstTime;
                    $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                    DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                    return $this->respond([
                        'status'      => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message'     => 'Farmer has been updated Successfully.',
                        'image_path'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/',
                        'data'        => $ifcfarmer
                    ]);
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt !!");
            }
        }
    }

    public function addIfcFarmerNew(Request $request) {
        $rules = array(
            'apiToken'        => 'required|string',
            'farmer_name'     => 'required',
            'mobile'          => 'required',
            'image'           => 'required',
            'image_date_time' => 'required',
            'latitude'        => 'required',
            'longitude'       => 'required',
            'acreage'         => 'required',
            'ticket_no'       => 'required',
            'activity_distance' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $request_data   = Input::all();
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data       = array(
                'api_name'   => "v3/addIfcFarmerNew",
                'start_time' => $log_start_time,
                'request_data' => json_encode($request_data),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $mobile  = $request->input('mobile');
                $ticket_no  = $request->input('ticket_no');

                $ticketData = Ticket::where(['ticket_no'=> $ticket_no,'user_id'=> $user_id,'user_type'=> $user_type, 'data_source_id' => 6])->first();
                try {
                    if($ticketData) {
                        $ticket_id = $ticketData->ticket_id;
                        $farmer_name = $request->input('farmer_name');
                        $check_farmer = Farmer::where(['ticket_id'=> $ticket_id,'user_id'=> $user_id,'user_type'=> $user_type,'mobile'=>$mobile])->first();
                        if($check_farmer) {
                            Farmer::where(['ticket_id'=> $ticket_id,'user_id'=> $user_id,'user_type'=> $user_type])
                            ->update([
                                // 'farmer_name'=> $request->input('farmer_name'),
                                'mobile'=> $request->input('mobile'),
                                'updated_at'=> date('Y-m-d H:i:s')
                            ]);
                        } else {
                            $farmer_data_array = array(
                                                    'ticket_id'   => $ticket_id,
                                                    'user_id'     => $user_id,
                                                    'user_type'   => $user_type,
                                                    'mobile'      => $mobile,
                                                    'farmer_type' => 2,
                                                    'farmer_name' => $farmer_name,
                                                    'datetime'    => date('Y-m-d H:i:s'),
                                                    'registration_type' => 'manual',
                                                    'ticket_status' => '1',
                                                    'created_at'  => date('Y-m-d H:i:s'),
                                                    'updated_at'  => date('Y-m-d H:i:s')
                                                );
                            // return $farmer_data_array;
                            $farmerData = Farmer::firstOrNew($farmer_data_array);
                            $farmerData->save();
                        }
                    }
                    $ifcfarmers = IFCVillageFarmer::where('mobile',$mobile)->where('ticket_no',$ticket_no)->get();
                    $ifcfarmer = array();
                    if(count($ifcfarmers) == 0) {
                        $ticket_no = $request->input('ticket_no');
                        $IFCFarmer = new IFCVillageFarmer;
                        $IFCFarmer->user_id     = $user_id;
                        $IFCFarmer->user_type   = $user_type;
                        $IFCFarmer->farmer_name = $request->input('farmer_name');
                        $IFCFarmer->mobile      = $request->input('mobile');
                        $IFCFarmer->image_date_time = $request->input('image_date_time');
                        $IFCFarmer->latitude    = $request->input('latitude');
                        $IFCFarmer->longitude   = $request->input('longitude');
                        $IFCFarmer->acreage     = $request->input('acreage');
                        $IFCFarmer->ticket_no   = $request->input('ticket_no');
                        $IFCFarmer->activity_distance = $request->input('activity_distance');

                        if($request->file('image')) {
                            $file=$request->file('image');
                            //s3Upload
                            $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                            $sTempFileName = 'assets/farmer/' . $fileName;
                            $sorage = Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            $IFCFarmer->image= $fileName;
                        }
                        $IFCFarmer->save();
                        $ifcfarmer_id = $IFCFarmer->id;
                        if($ifcfarmer_id) {
                            Ticket::where(['ticket_no' => $ticket_no, 'data_source_id' => 6])->update(['ifc_farmer_count' => DB::raw('ifc_farmer_count+1'),'updated_at'=>date('Y-m-d H:i:s')]);
                            $ifcfarmer = IFCVillageFarmer::where('id',$ifcfarmer_id)->get();
                        }

                        $log_firstTime  = strtotime($log_start_time);
                        $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                        $log_time_diff  = $log_lastTime-$log_firstTime;
                        $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                        DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                        return $this->respond([
                            'status'      => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message'     => 'Farmer has been added Successfully.',
                            'image_path'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/',
                            'data'        => $ifcfarmer
                        ]);
                    } else {
                        $ifcfarmer_id = $ifcfarmers[0]->id;
                        $imagename ='';

                        if($request->file('image')) {
                            $file=$request->file('image');
                            //s3Upload
                            $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                            $sTempFileName = 'assets/farmer/' . $fileName;
                            try {
                                Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            } catch(Exception $e) {
                                // return $e->getMessage();
                            }
                            $imagename = $fileName;
                            // $logFile = 'laravel.log';
                            // Log::useDailyFiles('/logs/'.$logFile);
                        }
                        IFCVillageFarmer::where('id',$ifcfarmer_id)
                            ->update([
                                'user_id'    => $user_id,
                                'user_type'  => $user_type,
                                'farmer_name'=> $request->input('farmer_name'),
                                'mobile'     => $request->input('mobile'),
                                'image'      => $imagename,
                                'image_date_time'=> $request->input('image_date_time'),
                                'latitude'   => $request->input('latitude'),
                                'longitude'  => $request->input('longitude'),
                                'acreage'    => $request->input('acreage'),
                                'ticket_no'  => $ticket_no,
                                'activity_distance'=> $request->input('activity_distance'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        $ifcfarmer = IFCVillageFarmer::where('id',$ifcfarmer_id)->get();

                        $log_firstTime  = strtotime($log_start_time);
                        $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                        $log_time_diff  = $log_lastTime-$log_firstTime;
                        $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                        DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                        return $this->respond([
                            'status'      => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message'     => 'Farmer has been updated Successfully.',
                            'image_path'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/',
                            'data'        => $ifcfarmer
                        ]);
                    }
                } catch (Exception $e) {
                    return $e;
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt !!");
            }
        }
    }

    public function s1openregisterifcfarmerNew($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                if ($user_type == 'dhanuka_doctor') {
                    $query = "
                        SELECT
                            ut.ticket_id as tID, 
                            ut.ticket_no as ticket_no,
                            ds.data_source as data_source,
                            ut.created_at as datetime,
                            DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format 
                        FROM tickets ut
                        INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                        LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                        WHERE ds.data_source = 'IFC'
                        AND ut.assign_to_dd = '" . $user_id . "'
                        AND ut.ticket_status = '1'";
                } else if ($user_type == 'bdo') {
                    $checkbdotype = DB::table('users')->where('id',$user_id)->where('status','1')->select('designation','id')->get();
                    if ($checkbdotype[0]->designation == 'COUNTERPART-FL') {
                        $checkbdo = DB::table('users')->where('counterpart_fl',$checkbdotype[0]->id)->where('status','1')->select('id')->get();
                        $bdo_ids = array();
                        foreach ($checkbdo as $key => $value) {
                            $bdo_ids[] = $checkbdo[$key]->id;
                        }
                        array_push($bdo_ids, $user_id);
                        $bdo_ids = implode("','", $bdo_ids);
                        $query = "
                            SELECT
                                ut.ticket_id as tID,
                                ut.ticket_no as ticket_no,
                                ds.data_source as data_source,
                                ut.created_at as datetime,
                                DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format
                            FROM tickets ut
                            INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                            LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                            WHERE IF(ut.user_id != '".$user_id."', ut.forwhom=2, ut.forwhom=1)
                            AND ds.data_source = 'IFC'
                            AND (ut.user_id in ('".$bdo_ids."'))
                            AND ut.user_type    = '" . $user_type . "'
                            AND ut.ticket_status = '1'";
                    } else {
                        $checkCPFL = DB::table('users')->where('id',$user_id)->select('counterpart_fl')->get();
                        if (count($checkCPFL)>0 && isset($checkCPFL)) {
                            $bdo_ids = array($user_id);
                            array_push($bdo_ids, $checkCPFL[0]->counterpart_fl);
                        } else {
                            $bdo_ids = array($user_id);
                        }
                        $bdo_ids = implode("','", $bdo_ids);
                        $query = "
                            SELECT
                                ut.ticket_id as tID,
                                ut.ticket_no as ticket_no,
                                ds.data_source as data_source,
                                ut.created_at as datetime,
                                DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format
                            FROM tickets ut
                            INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                            LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                            WHERE ds.data_source = 'IFC'
                            -- AND (ut.user_id in ('".$bdo_ids."'))
                            AND ut.user_id      = '" . $user_id . "'
                            AND ut.user_type    = '" . $user_type . "'
                            AND ut.ticket_status = '1'";
                    }
                } else {
                    $query = "
                        SELECT
                            ut.ticket_id as tID,
                            ut.ticket_no as ticket_no,
                            ds.data_source as data_source,
                            ut.created_at as datetime,
                            DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format
                        FROM tickets ut
                        INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                        LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                        WHERE ds.data_source = 'IFC'
                        AND ut.user_id      = '" . $user_id . "'
                        AND ut.user_type    = '" . $user_type . "'
                        AND ut.ticket_status = '1'";
                }
                // return $query;
                $open_ticket = DB::select(DB::raw($query));
                $result = array_map(function ($value) {
                    return (array) $value;
                }, $open_ticket);
                if ($result) {
                    //user_id = '" . $user_id . "' AND user_type = '" . $user_type . "' AND
                    $query1 = "SELECT mobile as number, DATE_FORMAT(datetime, '%h:%i %p') as time FROM farmers WHERE ticket_id ='".$result[0]['tID']."' AND registration_type = 'manual'";
                    $manual_data = DB::select(DB::raw($query1));
                    $result2 = array_map(function ($value) {
                        return (array) $value;
                    }, $manual_data);

                    $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/";
                    $query2 = "SELECT id as farmer_id, farmer_name, image, image_date_time, latitude, longitude, acreage, mobile as number, DATE_FORMAT(created_at, '%Y-%m-%d %h:%i %p') as time, ticket_no FROM ifc_farmer WHERE ticket_no ='".$result[0]['ticket_no']."'";
                    $ifc_farmer_data = DB::select(DB::raw($query2));

                    $result2 = array_map(function ($value) {
                        return (array) $value;
                    }, $manual_data);

                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'List Item Found!',
                        'base_url' => $base_url,
                        'data' => $open_ticket,
                        'manual_data' => $manual_data,
                        'ifc_farmer_data' => $ifc_farmer_data
                    ]);
                } else {
                    return $this->respondWithError("No Open Ticket Found!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s1openregisterifcfarmer($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                if ($user_type == 'dhanuka_doctor') {
                    $query = "
                        SELECT
                            ut.ticket_id as tID, 
                            ut.ticket_no as ticket_no,
                            ds.data_source as data_source,
                            ut.created_at as datetime,
                            DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format 
                        FROM tickets ut
                        INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                        LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                        WHERE ds.data_source = 'IFC'
                        AND ut.assign_to_dd = '" . $user_id . "'
                        AND ut.ticket_status = '1'";
                } else if ($user_type == 'bdo') {
                    $checkbdotype = DB::table('users')->where('id',$user_id)->where('status','1')->select('designation','id')->get();
                    if ($checkbdotype[0]->designation == 'COUNTERPART-FL') {
                        $checkbdo = DB::table('users')->where('counterpart_fl',$checkbdotype[0]->id)->where('status','1')->select('id')->get();
                        $bdo_ids = array();
                        foreach ($checkbdo as $key => $value) {
                            $bdo_ids[] = $checkbdo[$key]->id;
                        }
                        array_push($bdo_ids, $user_id);
                        $bdo_ids = implode("','", $bdo_ids);
                        $query = "
                            SELECT
                                ut.ticket_id as tID,
                                ut.ticket_no as ticket_no,
                                ds.data_source as data_source,
                                ut.created_at as datetime,
                                DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format
                            FROM tickets ut
                            INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                            LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                            WHERE IF(ut.user_id != '".$user_id."', ut.forwhom=2, ut.forwhom=1)
                            AND ds.data_source = 'IFC'
                            -- AND ut.assign_to_dd  = '0'
                            AND (ut.user_id in ('".$bdo_ids."'))
                            AND ut.user_type = '" . $user_type . "'
                            AND ut.ticket_status = '1'";
                        // Log::info($query);
                    } else {
                        $checkCPFL = DB::table('users')->where('id',$user_id)->select('counterpart_fl')->get();
                        if (count($checkCPFL)>0 && isset($checkCPFL)) {
                            $bdo_ids = array($user_id);
                            array_push($bdo_ids, $checkCPFL[0]->counterpart_fl);
                        } else {
                            $bdo_ids = array($user_id);
                        }
                        $bdo_ids = implode("','", $bdo_ids);
                        $query = "
                            SELECT
                                ut.ticket_id as tID,
                                ut.ticket_no as ticket_no,
                                ds.data_source as data_source,
                                ut.created_at as datetime,
                                DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format
                            FROM tickets ut
                            INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                            LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                            WHERE ds.data_source = 'IFC'
                            -- AND ut.assign_to_dd  = '0'
                            -- AND (ut.user_id in ('".$bdo_ids."'))
                            AND ut.user_id      = '" . $user_id . "'
                            AND ut.user_type    = '" . $user_type . "'
                            AND ut.forwhom = '1'
                            AND ut.ticket_status = '1'";
                    }
                } else {
                    $query = "
                        SELECT
                            ut.ticket_id as tID,
                            ut.ticket_no as ticket_no,
                            ds.data_source as data_source,
                            ut.created_at as datetime,
                            DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format
                        FROM tickets ut
                        INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                        LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                        WHERE ds.data_source = 'IFC'
                        -- AND ut.assign_to_dd  = '0'
                        AND ut.user_id      = '" . $user_id . "'
                        AND ut.user_type    = '" . $user_type . "'
                        AND ut.ticket_status       = '1'";
                }
                // return $query;
                $open_ticket = DB::select(DB::raw($query));
                $result = array_map(function ($value) {
                    return (array) $value;
                }, $open_ticket);
                if ($result) {
                    //user_id = '" . $user_id . "' AND user_type = '" . $user_type . "' AND
                    $query1 = "SELECT mobile as number, DATE_FORMAT(datetime, '%h:%i %p') as time FROM farmers WHERE ticket_id ='".$result[0]['tID']."' AND registration_type = 'manual'";
                    $manual_data = DB::select(DB::raw($query1));
                    $result2 = array_map(function ($value) {
                        return (array) $value;
                    }, $manual_data);

                    $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/";
                    $query2 = "SELECT id as farmer_id, farmer_name, image, image_date_time, latitude, longitude, acreage, mobile as number, DATE_FORMAT(created_at, '%Y-%m-%d %h:%i %p') as time, ticket_no FROM ifc_farmer WHERE ticket_no ='".$result[0]['ticket_no']."'";
                    $ifc_farmer_data = DB::select(DB::raw($query2));

                    $result2 = array_map(function ($value) {
                        return (array) $value;
                    }, $manual_data);

                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'List Item Found!',
                        'base_url' => $base_url,
                        'data' => $open_ticket,
                        'manual_data' => $manual_data,
                        'ifc_farmer_data' => $ifc_farmer_data
                    ]);
                } else {
                    return $this->respondWithError("No Open Ticket Found!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s1openregisterfarmer($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                if ($user_type == 'dhanuka_doctor') {
                    $query = "
                        SELECT
                            ut.ticket_id as tID,
                            ut.ticket_no as ticket_no,
                            ds.data_source as data_source,
                            ut.created_at as datetime,
                            DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format 
                        FROM tickets ut
                        INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                        LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                        WHERE ds.data_source != 'IFC'
                        AND ut.assign_to_dd = '".$user_id."'
                        AND ut.ticket_status       = '1'";
                } else if ($user_type == 'bdo') {
                    $checkbdotype = DB::table('users')->where('id',$user_id)->where('status','1')->select('designation','id')->get();
                    if ($checkbdotype[0]->designation == 'COUNTERPART-FL') {
                        $checkbdo = DB::table('users')->where('counterpart_fl',$checkbdotype[0]->id)->where('status','1')->select('id')->get();
                        $bdo_ids = array();
                        foreach ($checkbdo as $key => $value) {
                            $bdo_ids[] = $checkbdo[$key]->id;
                        }
                        array_push($bdo_ids, $user_id);
                        $bdo_ids = implode("','", $bdo_ids);
                        $query = "
                            SELECT
                                ut.ticket_id as tID,
                                ut.ticket_no as ticket_no,
                                ds.data_source as data_source,
                                ut.created_at as datetime,
                                DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format 
                            FROM tickets ut
                            INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                            LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                            WHERE IF(ut.user_id != '".$user_id."', ut.forwhom=2, ut.forwhom=1)
                            AND ds.data_source != 'IFC'
                            AND ut.user_id IN ('" . $bdo_ids . "')
                            AND ut.user_type    = '" . $user_type . "'
                            AND ut.assign_to_dd = '0'
                            AND ut.ticket_status= '1'";
                    } else {
                        $checkCPFL = DB::table('users')->where('id',$user_id)->select('counterpart_fl')->get();
                        if (count($checkCPFL)>0 && isset($checkCPFL)) {
                            $bdo_ids = array($user_id);
                            array_push($bdo_ids, $checkCPFL[0]->counterpart_fl);
                        } else {
                            $bdo_ids = array($user_id);
                        }
                        $bdo_ids = implode("','", $bdo_ids);
                        $query = "
                            SELECT
                                ut.ticket_id as tID,
                                ut.ticket_no as ticket_no,
                                ds.data_source as data_source,
                                ut.created_at as datetime,
                                DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format 
                            FROM tickets ut
                            INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                            LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                            WHERE ds.data_source != 'IFC'
                            -- AND ut.user_id IN ('" . $bdo_ids . "')
                            AND ut.user_id      = '" . $user_id . "'
                            AND ut.user_type    = '" . $user_type . "'
                            AND ut.assign_to_dd = '0'
                            AND ut.forwhom      = '1'
                            AND ut.ticket_status= '1'";
                    }
                } else {
                    $query = "
                        SELECT
                            ut.ticket_id as tID,
                            ut.ticket_no as ticket_no,
                            ds.data_source as data_source,
                            ut.created_at as datetime,
                            DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format 
                        FROM tickets ut
                        INNER JOIN data_sources ds ON ds.data_source_id=ut.data_source_id
                        LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=ut.assign_to_dd
                        WHERE ds.data_source != 'IFC'
                        AND ut.user_id      = '" . $user_id . "'
                        AND ut.user_type    = '" . $user_type . "'
                        AND ut.assign_to_dd = '0'
                        AND ut.ticket_status= '1'";
                }
                // return $query;
                $open_ticket = DB::select(DB::raw($query));
                $result = array_map(function ($value) {
                    return (array) $value;
                }, $open_ticket);
                if ($result) {
                    $query1 = "
                        SELECT
                            mobile as number, 
                            DATE_FORMAT(datetime, '%h:%i %p') as time 
                        FROM
                            farmers
                        WHERE
                            ticket_id         = '".$result[0]['tID']."' AND
                            registration_type = 'manual'"; 
                    $manual_data = DB::select(DB::raw($query1));
                    $result2 = array_map(function ($value) {
                        return (array) $value;
                    }, $manual_data);
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'List Item Found!',
                        'data' => $open_ticket,
                        'manual_data' => $manual_data
                    ]);
                } else {
                    return $this->respondWithError("No Open Ticket Found!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function updateIfcActivity(Request $request) {
        $rules = array(
            'ticket_no'       => 'required',
            'activity_duration' => 'required',
            'total_pda'             => 'required',
            'total_activity_distance' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $ticket_no = $request->input('ticket_no');
            $ticket = Ticket::where('ticket_no',$ticket_no)->where('data_source_id', 6)->count();
            if($ticket >0) {
                $activity_duration = $request->input('activity_duration');
                $total_pda = $request->input('total_pda');
                $total_activity_distance = $request->input('total_activity_distance');
                Ticket::where(['ticket_no' => $ticket_no, 'data_source_id' => 6])->update(['activity_duration' => $activity_duration,'total_pda'=> $total_pda, 'total_activity_distance' => $total_activity_distance, 'updated_at'=>date('Y-m-d H:i:s')]);
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Activity Duration Updated Successfully!'
                ]);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Invalid Ticket No'
                ]);
            }
        }
    }

    public function delteifcfarmer(Request $request) {
        $rules = array(
            'farmer_ids' => 'required|numeric',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $request_data   = Input::all();
            $curTime        = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data       = array(
                'api_name'   => "v3/delteifcfarmer",
                'start_time' => $log_start_time,
                'request_data' => json_encode($request_data),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $farmer_ids = $request->input('farmer_ids');
            if($farmer_ids) {
                $ifcfarmers = IFCVillageFarmer::where('id',$farmer_ids)->get();
                if(count($ifcfarmers) > 0){
                    $cnt_ifcfarmers = count($ifcfarmers);
                    $activity_distance = $ifcfarmers[0]->activity_distance;
                    $ticket_no = $ifcfarmers[0]->ticket_no;
                    $nextIfcfarmers = IFCVillageFarmer::where('ticket_no',$ticket_no)->where('id','>',$farmer_ids)->get();
                    if(count($nextIfcfarmers) > 0) {
                        $next_ifcfarmer_id = $nextIfcfarmers[0]->id;
                        $new_activity_distance = $activity_distance + $nextIfcfarmers[0]->activity_distance;
                        IFCVillageFarmer::where(['id' => $next_ifcfarmer_id, 'ticket_no' => $ticket_no])
                            ->update([
                                'activity_distance'=> $new_activity_distance,
                                'updated_at'=> date('Y-m-d H:i:s')
                            ]);
                    }
                    DB::table("ifc_farmer")->where('id',$farmer_ids)->delete();
                    Ticket::where(['ticket_no' => $ticket_no, 'data_source_id' => 6])->update(['ifc_farmer_count' => DB::raw('ifc_farmer_count-1'),'updated_at'=>date('Y-m-d H:i:s')]);
                    
                    $log_firstTime  = strtotime($log_start_time);
                    $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                    $log_time_diff  = $log_lastTime-$log_firstTime;
                    $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                    DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>0,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Farmer Deleted Successfully.'
                    ]);
                } else {
                    $log_firstTime  = strtotime($log_start_time);
                    $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                    $log_time_diff  = $log_lastTime-$log_firstTime;
                    $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                    DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>0,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Farmer Not Found.'
                    ]);
                }
            } else {
                return $this->respond([
                    'status' => 'error',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Please Enter farmer_ids'
                ]);
            }
        }
    }

    public function getValidateifcFarmerList(Request $request) {
        $rules = array(
            'apiToken' => 'required',
            'ticket_no' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            $ticket_no = $request->input('ticket_no');

            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                $ifcfarmers = IFCVillageFarmer::where('ticket_no',$ticket_no)->get();
                $base_url = 'https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/';
                
                if (isset($ifcfarmers) && !empty($ifcfarmers)) {
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'IFC Farmer list.',
                        'base_url'  => $base_url,
                        'data' =>$ifcfarmers
                    ]);
                } else {
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'No farmer found',
                        'data' =>[]
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function createATP(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'apiToken' => 'required|string',
                'planATP' => 'array'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $api_token  = $data['apiToken'];
                $planATP    = $data['planATP']; //echo "<pre>";print_r($planATP);die;
                $user       = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $userId     = $user[0]->id;
                    $userType   = $user[0]->user_type;
                    if (count($planATP) > 0) {
                        $valid_user_plan = array();
                        $userId = $user[0]->id;
                        //echo date('Y-m-d\TH:i:s');die;
                        foreach ($planATP as $plan) {
                            $dat = explode('.', $plan['plan_date']);
                            if ( $dat[0] <= date('Y-m-d\T23:59:00')) {
                                return $this->respondWithError("You can not plan atp on current and previous date.");
                            }
                        }
                        foreach ($planATP as $plan) {
                            $villagedata    = DB::table('villages')->where('village_id',$plan['village'])->select('villages.*')->get();

                            $plan_date      = $plan['plan_date'];
                            $product_id     = $plan['product_id'];
                            $product_id_2   = isset($plan['product_id_2'])? $plan['product_id_2'] : '';
                            $product_id_3   = isset($plan['product_id_3'])? $plan['product_id_3'] : '';
                            $crop_id        = $plan['crop_id'];
                            $language_id    = isset($plan['language_id']) ? $plan['language_id'] : '';
                            $other_crop_1   = isset($plan['other_crop_1']) ? $plan['other_crop_1'] : '';
                            $other_crop_2   = isset($plan['other_crop_2']) ? $plan['other_crop_2'] : '';
                            $state_id       = $plan['state_id'];
                            $district_id    = $plan['district_id'];
                            $tehsil_id      = $plan['tehsil_id'];
                            $territory_id   = isset($plan['territory_id']) ? $plan['territory_id'] : 0;
                            $village        = $villagedata[0]->village;
                            $village_id     = $villagedata[0]->village_id;
                            $expected_farmer= $plan['expected_farmer'];
                            $expected_pda   = $plan['expected_pda'];
                            $record_id      = $plan['record_id'];
                            $dd_id          = $plan['dd_id'];
                            $created_by_user_id = $userId;
                            $user_type      = $userType;
                            $forwhom        = $plan['forwhom'];
                            $data_source_id = isset($plan['data_source_id'])? $plan['data_source_id'] : '';
                            $presenter_id   = !empty($plan['presenter_id']) ? $plan['presenter_id'] : 0;
                            $presenter_name   = !empty($plan['presenter_name']) ? $plan['presenter_name'] : NULL;
                            $presenter_type = !empty($plan['presenter_type'])? $plan['presenter_type'] : NULL;
                            $monitor_id     = !empty($plan['monitor_id'])? $plan['monitor_id'] : 0;
                            $monitor_name   = !empty($plan['monitor_name'])? $plan['monitor_name'] : NULL;
                            $monitor_type   = !empty($plan['monitor_type'])? $plan['monitor_type'] : NULL;
                            $data_source    = DataSource::where('data_source_id', $data_source_id)->where('status', '1')->first();
                            $product        = Product::where('product_id', $product_id)->where('status', '1')->first();
                            $crop           = Crop::where('crop_id', $crop_id)->where('status', '1')->first();

                            if ($data_source && $product && $crop) {
                                $user_plan_atp = array(
                                    'plan_date'     => $plan_date,
                                    'product_id'    => $product_id,
                                    'product_id_2'  => $product_id_2,
                                    'product_id_3'  => $product_id_3,
                                    'crop_id'       => $crop_id,
                                    'other_crop_1'  => $other_crop_1,
                                    'other_crop_2'  => $other_crop_2,
                                    'data_source_id'=> $data_source_id,
                                    'state_id'      => $state_id,
                                    'district_id'   => $district_id,
                                    'tehsil_id'     => $tehsil_id,
                                    'territory_id'  => $territory_id,
                                    'village'       => $village,
                                    'village_id'    => $village_id,
                                    'expected_farmer' => $expected_farmer,
                                    'expected_pda'  => $expected_pda,
                                    'created_by_user_id' => $created_by_user_id,
                                    'user_type'     => $user_type,
                                    'record_id'     => $record_id,
                                    'language_id'   =>$language_id,
                                    'forwhom'       => $forwhom,
                                    'presenter_id'  => $presenter_id,
                                    'presenter_name'=> $presenter_name,
                                    'presenter_type'=> $presenter_type,
                                    'monitor_id'    => $monitor_id,
                                    'monitor_name'  => $monitor_name,
                                    'monitor_type'  => $monitor_type,
                                    'dd_id'         => ($dd_id)
                                );
                                $insert_plan_atp = PlanAtp::firstOrCreate($user_plan_atp);
                                if ($insert_plan_atp) {
                                    //creating ticket for plan atp
                                    /*$count_ticket = Ticket::count();
                                    $count_ticket = $count_ticket + $userId;
                                    $count_ticket = $count_ticket + 150 + rand(100,999) + rand(10,99);
                                    $ticket_no    = rand(10,99).rand(10,99).rand(10,99).rand(1,10) + $count_ticket;
                                    $ticket_no    = "PV2" . $ticket_no;*/
                                    $ticket_no    = $this->generateTicketNumber($userId, 'PV2');
                                    $ticketno_by  = 1;
                                    $checkTicket  = Ticket::where('plan_atp_id',$insert_plan_atp->plan_atp_id)->count();
                                    if($checkTicket==0) {
                                        Ticket::create([
                                            'ticket_no'     => $ticket_no,
                                            'ticketno_by'   => $ticketno_by,
                                            'user_id'       => $userId,
                                            'user_type'     => $userType,
                                            'plan_atp_id'   => $insert_plan_atp->plan_atp_id,
                                            'data_source_id'=> $data_source_id,
                                            'state_id'      => $state_id,
                                            'district_id'   => $district_id,
                                            'territory_id'  => $territory_id,
                                            'tehsil_id'     => $tehsil_id,
                                            'assign_to_dd'  => $dd_id,
                                            'language_id'   => isset($language_id) ? $language_id : 0 ,
                                            'village'       => $village,
                                            'village_id'    => $village_id,
                                            'expected_farmers' => $expected_farmer,
                                            'product_1_id'  => $product_id,
                                            'product_2_id'  => $product_id_2,
                                            'product_3_id'  => $product_id_3,
                                            'crop_1_id'     => $crop_id,
                                            'crop_2_id'     => $other_crop_1,
                                            'crop_3_id'     => $other_crop_2,
                                            'ticket_status' => '0',
                                            'status'        => '0',
                                            'forwhom'       => $forwhom,
                                            'created_at'    => $plan_date,
                                            'updated_at'    => date('Y-m-d H:i:s')
                                        ]);
                                    }
                                    $valid_user_plan[] = $user_plan_atp;
                                }
                            }
                        }
                        if (count($valid_user_plan) > 0) {
                            $user_data = User::where('id', $userId)->select('device_id')->first();
                            if ($user_data && $user_data->device_id != '') {
                                $manager_details = $this->findUserDetails($userType, $api_token);
                                $data = array(
                                    'device_id' => $user_data->device_id,
                                    'notification_title' => "BDO Plan Submitted",
                                    'notifiaction_mesaage' => $manager_details[0]->manager_name . " submitted his plan."
                                );
                                $this->dispatch(new SendNotification($data));
                            }
                            return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Plan ATP Successfully Uploaded!',
                                'data' => $valid_user_plan,
                            ]);
                        } else {
                            return $this->respondWithError("Invalid plan activity!");
                        }
                    } else {
                        return $this->respondWithError("Plan activity not found!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    private function findUserDetails($user_type, $api_token) {
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
        $query = " SELECT " . $select_field . " FROM " . $table . " WHERE status='1' AND api_token ='" . $api_token . "' LIMIT 0,1 ";
        $user = DB::select(DB::raw($query));
        return $user;
    }

    private function findUserEcn($ecn_by_id, $user_type, $user_id) {
        switch ($user_type) {
            case "marketing_supervisor" : $table = "marketing_supervisor";
                $select_field = "ECN AS ecn"; $ecn_by_id ="marketing_supervisor_id";
                break;
            case "zonal" : $table = "zonal_managers";
                $select_field = "ECN AS ecn";
                break;
            case "regional" : $table = "regional_managers";
                $select_field = "ECN AS ecn";
                break;
            case "area" : $table = "area_managers";
                $select_field = "ECN AS ecn";
                break;
            case "sales_regional" : $table = "sales_regional_managers";
                $select_field = "ECN AS ecn";
                break;
            case "sales_area" : $table = "sales_area_managers";
                $select_field = "ECN AS ecn";
                break;
            case "bdo" : $table = "users";
                $select_field = "ECN AS ecn";
                break;
            case "dhanuka_doctor" : $table = "dhanuka_doctor";
                $select_field = "ECN AS ecn";
                break;
        case "general" : $table = "general";
                $select_field = "ECN AS ecn";
                break; 
        }
        $query = " SELECT " . $select_field . " FROM " . $table . " WHERE status='1' AND ".$ecn_by_id." ='" . $user_id . "' LIMIT 0,1 ";
        $user = DB::select(DB::raw($query));
        return $user;
    }

    public function getFarmerList(Request $request) { 
        $rules = array(
            'apiToken' => 'required|string',            
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                if($user[0]->user_type == 'bdo') {
                    //check counter fl bdo
                    $bdolist = DB::table('users')
                              ->where('counterpart_fl',$user_id)
                              ->where('status','1')
                              ->select('id')
                              ->get();
                    $bdoresult = array();
                    if (isset($bdolist) && !empty($bdolist)) {
                        $bdolistDatass = json_decode(json_encode($bdolist),true);
                        foreach ($bdolistDatass as $key => $value) {
                            if (is_array($value)) {
                                $bdoresult=array_merge($bdoresult,array_flatten($value));
                            } else {
                                $bdoresult[$key] = $value;
                            }
                        }
                    }
                    if (count($bdoresult)>0) {
                        $ddArray = array();
                        $getDD = DhanukaDoctor::whereIn('bdo_id', $bdoresult)->where('status','1')->get()->toArray();
                        foreach ($getDD as $keyDD => $valueDD) {
                            array_push($ddArray, $valueDD['dd_id']);
                        }
                    } else {
                        $ddArray = array();
                        $getDD = DhanukaDoctor::where('bdo_id', $user_id)->where('status','1')->get()->toArray();
                        foreach ($getDD as $keyDD => $valueDD) {
                            array_push($ddArray, $valueDD['dd_id']);
                        }
                    }
                    // print_r($ddArray);die;
                    $villageFarmer11 =VillageFarmer::join('village_farmer_crop','village_farmer_crop.farmer_id','=','village_farmers.farmer_id')
                            ->join('villages','villages.village_id','=','village_farmers.village_id')
                            ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','village_farmers.dd_id')
                            ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                            ->join('districts','districts.district_id','=','villages.district_id')
                            ->join('states','states.state_id','=','districts.state_id')
                            ->whereIn('village_farmers.dd_id',$ddArray);
                            if($request->input('dd_id') != '') {
                                $villageFarmer11->where('village_farmers.dd_id',$request->input('dd_id'));
                            }
                            if($request->input('village') != '') {
                                $villageFarmer11->where('village_farmers.village_id',$request->input('village'));
                            }
                            if(!empty($request->input('from')) && !empty($request->input('to'))) {
                                $villageFarmer11->whereDate('village_farmers.created_at','>=',$request->input('from'))->whereDate('village_farmers.created_at','<=',$request->input('to'));
                            }
                            if($request->input('crop') != '') {
                                $villageFarmer11->where('village_farmer_crop.crop_id',$request->input('crop'));
                            }
                            if($request->input('acerage') != '') {
                                $villageFarmer11->where('village_farmer_crop.acres','<=',$request->input('acerage'));
                            }
                            if($request->has('updated_at')){
                                $updated_at = $request->input('updated_at');
                                if($updated_at!=0) {
                                    $villageFarmer11->where('village_farmer_crop.updated_at','>=',$updated_at);
                                }
                            }
                            $villageFarmer1 = $villageFarmer11->groupBy('village_farmers.contact_number')
                               ->select('village_farmers.farmer_id as farmerId','dhanuka_doctor.name','village_farmers.farmer_name as farmerName','village_farmers.father_name as fatherName',DB::raw('sum(acres) as acres'),'village_farmers.contact_number as contactNumber','village_farmers.alternate_contact_number as alternateContactNumber','village_farmers.farmer_image as farmarImage','states.state','tehsils.tehsil','districts.district','village_farmers.call_status','villages.tehsil_id as tehsilId','villages.district_id as districtId','districts.state_id as stateId','villages.village','villages.village_id','village_farmers.farmer_lat as farmerLat','village_farmers.farmer_long as farmerLong')->get();
                    // echo '<pre>'; print_r($villageFarmer1); die;
                    foreach ($villageFarmer1 as $key => $value) {
                        $markVisit=MarkVisit::where('farmer_id',$villageFarmer1[$key]->farmerId)->count();
                        $villageFarmer1[$key]->acpVisit=$markVisit;
                        $leaddata =  DB::table('leads')
                                    ->where('leads.farmer_id',$villageFarmer1[$key]->farmerId)
                                    ->select('leads.lead_status')
                                    ->get();
                        if ($leaddata) {
                            $villageFarmer1[$key]->lead_status = $leaddata[0]->lead_status;
                        } else {
                            $villageFarmer1[$key]->lead_status = "0";
                        }
                        $products = DB::table('village_farmer_crop')
                                ->join('products','products.product_id','=','village_farmer_crop.product_id','left')
                                ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                                ->where('village_farmer_crop.farmer_id',$villageFarmer1[$key]->farmerId)
                                ->select('village_farmer_crop.product_id','products.product','village_farmer_crop.crop_id','crop','acres')
                                ->get();
                        if ($products) {
                            $villageFarmer1[$key]->products = $products;
                        } else {
                            $villageFarmer1[$key]->products = [];
                        }
                        $crops = DB::table('village_farmer_crop')
                                ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                                ->where('village_farmer_crop.farmer_id',$villageFarmer1[$key]->farmerId)
                                ->select('village_farmer_crop.crop_id','crop')
                                ->get();
                        if ($crops) {
                            $villageFarmer1[$key]->crops = $crops;
                        } else {
                            $villageFarmer1[$key]->crops = [];
                        }
                        $leads = DB::table('leads')
                                ->where('farmer_id',$villageFarmer1[$key]->farmerId)
                                ->where('farmer_type','normal_farmer')
                                ->select('lead_id', 'lead_code', 'lead_status', 'created_at','expected_purchase_date','purchased_from','purchased_area')
                                ->get();
                        if ($leads) {
                            $villageFarmer1[$key]->leads = $leads;
                        } else {
                            $villageFarmer1[$key]->leads = [];
                        }
                    }
                    //Config('app.base_url')
                    for($i=0;$i<count($villageFarmer1);$i++){
                        $villageFarmer1[$i]->farmarImage="https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/".$villageFarmer1[$i]->farmarImage;
                        $markVisit=MarkVisit::where('farmer_id',$villageFarmer1[$i]->farmerId)->count();
                        $villageFarmer1[$i]->acpVisit=$markVisit;
                    }
                    $requestDD = $request->input('dd_id');
                    $requestVillage = $request->input('village');
                    $from = $request->input('from');
                    $to = $request->input('to');
                    $crop = $request->input('crop');
                    $acres = $request->input('acerage');
     
                    //Total ACP Famers
                    $villageFarmer22 =VillageFarmer::whereIn('village_farmers.dd_id',$ddArray)->where('status','1');
                    if($request->input('dd_id') != '') {
                        $villageFarmer22->where('village_farmers.dd_id',$request->input('dd_id'));
                    }
                    if($request->input('village') != '') {
                        $villageFarmer22->where('village_farmers.village_id',$request->input('village'));
                    }
                    if(!empty($from) && !empty($to)) {
                        $villageFarmer22->whereDate('village_farmers.created_at','>=',$from)->whereDate('village_farmers.created_at','<=',$to);
                    }
                    if(!empty($crop) || !empty($acres)) {
                        $villageFarmer22->rightJoin('village_farmer_crop', function($join){
                            $join->on('village_farmer_crop.farmer_id', '=', 'village_farmers.farmer_id');
                        });
                    }
                    if(!empty($crop)) {
                        $villageFarmer22->where('village_farmer_crop.crop_id',$crop);
                    }
                    if($acres != '') {
                        $villageFarmer22->where('village_farmer_crop.acres','<=',$acres);
                    }
                    $villageFarmer2 =   $villageFarmer22->count();
                    //Total Leads
                    $query12 = DB::table('village_farmers')
                                ->select('dd_id')
                                ->where('status', '1')
                                ->groupBy('dd_id');
                    if($requestDD != '') {
                        $query12->where('dd_id',$requestDD);
                    } else {
                        $query12->whereIn('dd_id', $ddArray);
                    }
                    if($requestVillage != '') {
                        $query12->where('village_id',$requestVillage);
                    }
                    if(!empty($from) && !empty($to)) {
                        $query12->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
                    }
                    /*if($request->has('updated_at')){
                        $updated_at = $request->input('updated_at');
                        if($updated_at!=0) {
                            $query12->where('updated_at','>=',$updated_at);
                        }
                    }*/
                    $getuserId2 =   $query12->get();
                    $userArray1 = array();
                    if (count($getuserId2) > 0) {
                        foreach ($getuserId2 as $keyDD => $valueDD) {
                            array_push($userArray1, $getuserId2[$keyDD]->dd_id);
                        }
                    }
                    $villageFarmer33 =DB::table('leads')
                        ->where('farmer_type','normal_farmer')
                        ->where('user_type','dhanuka_doctor');
                        // ->whereIn('user_id',$ddArray);
                        if (count($userArray1) > 0) {
                            $villageFarmer33->whereIn('user_id',$userArray1);
                        }
                        if(!empty($crop) || !empty($acres)) {
                            $villageFarmer33->rightJoin('village_farmer_crop', function($join) {
                                $join->on('village_farmer_crop.farmer_id', '=', 'leads.farmer_id');
                            });
                        }
                        if(!empty($crop)) {
                            $villageFarmer33->where('village_farmer_crop.crop_id',$crop);
                        }
                        if($acres != '') {
                            $villageFarmer33->where('village_farmer_crop.acres','<=',$acres);
                        }
                        $villageFarmer3 = $villageFarmer33->where(function ($query){
                            $query->where('lead_status','1')->orWhere('lead_status','2');
                        });
                        $totalLeads = $villageFarmer3->count();
                    //print_r($ddArray);die;
                    // totalAcres 
                    $query13 = DB::table('village_farmers')
                                ->select('dd_id')
                                ->where('status', '1')
                                ->groupBy('dd_id');
                            if($requestDD != '') {
                                $query13->where('dd_id',$requestDD);
                            } else {
                                $query13->whereIn('dd_id', $ddArray);
                            }
                            if($requestVillage != '') {
                                $query13->where('village_id',$requestVillage);
                            }
                            if(!empty($from) && !empty($to)) {
                                $query13->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
                            }
                            if($request->has('updated_at')){
                                $updated_at = $request->input('updated_at');
                                if($updated_at!=0) {
                                    $query13->where('updated_at','>=',$updated_at);
                                }
                            }
                    $getuserId1 =   $query13->get();
                    $userArray2 = array();
                    if (count($getuserId1) > 0) {
                        foreach ($getuserId1 as $keyDD => $valueDD) {
                            array_push($userArray2, $getuserId1[$keyDD]->dd_id);
                        }
                    }
                    $villageFarmer44 = DB::table('village_farmer_crop')
                                    ->select('acres')
                                    ->whereIn('farmer_id', function($query) use ($userArray2,$requestDD,$requestVillage,$from,$to){
                                        $query->from('leads')
                                        ->selectRaw('farmer_id')
                                        ->where('farmer_type','normal_farmer')
                                        ->where('user_type','dhanuka_doctor');
                                        if (isset($userArray2) && count($userArray2)>0) {
                                            $query->whereIn('user_id',$userArray2);
                                        }
                                        $query->whereIn('lead_status',[1,2,3]);
                                    });
                            if($crop != '') {
                                $villageFarmer44->where('crop_id',$crop);
                            }
                            if($acres != '') {
                                $villageFarmer44->where('village_farmer_crop.acres','<=',$acres);
                            }
                            /*if($request->has('updated_at')){
                                $updated_at = $request->input('updated_at');
                                if($updated_at!=0) {
                                    $villageFarmer44->where('updated_at','>=',$updated_at);
                                }
                            }*/
                    $villageFarmer4 =   $villageFarmer44->get();
                    $totalSumAcres = array();
                    foreach ($villageFarmer4 as $keyA => $valueA) {
                      array_push($totalSumAcres, $valueA->acres);
                    }
                    //  totalAcres CLosed 
                    $query14 = DB::table('village_farmers')
                        ->select('dd_id')
                        ->where('status', '1')
                        ->groupBy('dd_id');
                        if($requestDD != '') {
                            $query14->where('dd_id',$requestDD);
                        } else {
                            $query14->whereIn('dd_id', $ddArray);
                        }
                        if($requestVillage != '') {
                            $query14->where('village_id',$requestVillage);
                        }
                        if(!empty($from) && !empty($to)) {
                            $query14->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
                        }
                        if($request->has('updated_at')){
                            $updated_at = $request->input('updated_at');
                            if($updated_at!=0) {
                                $query14->where('updated_at','>=',$updated_at);
                            }
                        }
                    $getuserId4 =   $query14->get();
                    $userArray3 = array();
                    if (count($getuserId4) > 0) {
                        foreach ($getuserId4 as $keyDD => $valueDD) {
                            array_push($userArray3, $getuserId4[$keyDD]->dd_id);
                        }
                    }
                    $villageFarmer55 =DB::table('village_farmer_crop')
                            ->select('acres')
                            ->whereIn('farmer_id', function($query) use ($userArray3,$requestDD,$requestVillage,$from,$to){
                                $query->from('leads')
                                ->selectRaw('farmer_id')
                                ->where('farmer_type','normal_farmer')
                                ->where('user_type','dhanuka_doctor');
                                if (isset($userArray3) &&  count($userArray3) > 0) {
                                    $query->whereIn('user_id',$userArray3);
                                }
                                $query->where('lead_status','2');
                            });
                            if($crop != '') {
                                $villageFarmer55->where('crop_id',$crop);
                            }
                            if($acres != '') {
                                $villageFarmer55->where('village_farmer_crop.acres','<=',$acres);
                            }
                            /*if($request->has('updated_at')){
                                $updated_at = $request->input('updated_at');
                                if($updated_at!=0) {
                                    $villageFarmer55->where('updated_at','>=',$updated_at);
                                }
                            }*/
                    $villageFarmer5 =     $villageFarmer55->get();
                    // echo "<pre>"; print_r($villageFarmer5); die;
                    $totalCloseAcres = array();
                    foreach ($villageFarmer5 as $keyAC => $valueAC) {
                      array_push($totalCloseAcres, $valueAC->acres);
                    }
                    if (count($bdoresult)>0) {
                        $ddData = DB::table('dhanuka_doctor')->whereIn('bdo_id',$bdoresult)->where('status','1')->get();
                    } else {
                        $ddData = DB::table('dhanuka_doctor')->where('bdo_id',$user_id)->where('status','1')->get();
                    }
                    $ddListRecord = array();
                    foreach ($ddData as $keyDD1 => $valueDD1) {
                        $ddListRecord[$keyDD1]['dd_id'] = $valueDD1->dd_id;
                        $ddListRecord[$keyDD1]['ECN'] = $valueDD1->ECN;
                        $ddListRecord[$keyDD1]['name'] = $valueDD1->name;
                    }
                    $villageFarmer['totalACPFamer'] = $villageFarmer2;
                    $villageFarmer['total_leads'] = $totalLeads;
                    $villageFarmer['totalAcres'] = array_sum($totalSumAcres);
                    $villageFarmer['totalClosedAcres'] = array_sum($totalCloseAcres);
                    $villageFarmer['ACPDetail'] = $villageFarmer1;
                    $villageFarmer['DDList'] = $ddListRecord;
                } elseif ($user[0]->user_type == 'dhanuka_doctor') {
                    $villageId= $request->input('villageId');
                    if ($villageId == 'ddtype') {
                        $villageFarmerqry =VillageFarmer::join('village_farmer_crop','village_farmer_crop.farmer_id','=','village_farmers.farmer_id')
                                ->join('villages','villages.village_id','=','village_farmers.village_id')
                                ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                                ->join('districts','districts.district_id','=','villages.district_id')
                                ->join('states','states.state_id','=','districts.state_id');
                                // ->where('village_farmers.dd_id',$user_id);
                                if($request->has('updated_at')){
                                    $updated_at = $request->input('updated_at');
                                    if($updated_at!=0) {
                                        $villageFarmerqry->where('village_farmers.updated_at','>=',$updated_at);
                                    }
                                }
                                $villageFarmerqry->groupBy('village_farmers.contact_number')
                                ->select('village_farmers.farmer_id as farmerId','village_farmers.farmer_name as farmerName','village_farmers.father_name as fatherName',DB::raw('sum(acres) as acres'),'village_farmers.contact_number as contactNumber','village_farmers.alternate_contact_number as alternateContactNumber','village_farmers.farmer_image as farmarImage','states.state','tehsils.tehsil','districts.district','village_farmers.call_status','villages.tehsil_id as tehsilId','villages.district_id as districtId','districts.state_id as stateId','villages.village','villages.village_id','village_farmers.farmer_lat as farmerLat','village_farmers.farmer_long as farmerLong');
                        $villageFarmer = $villageFarmerqry->get();
                        foreach ($villageFarmer as $key => $value) {
                            $markVisit=MarkVisit::where('farmer_id',$villageFarmer[$key]->farmerId)->count();
                            $villageFarmer[$key]->acpVisit=$markVisit;
                            $leaddata = DB::table('leads')
                                        ->where('leads.farmer_id',$villageFarmer[$key]->farmerId)
                                        ->select('leads.lead_status')
                                        ->get();
                            if ($leaddata) {
                                $villageFarmer[$key]->lead_status = $leaddata[0]->lead_status;
                            } else {
                                $villageFarmer[$key]->lead_status = "0";
                            }
                            $products = DB::table('village_farmer_crop')
                                ->join('products','products.product_id','=','village_farmer_crop.product_id','left')
                                ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                                ->where('village_farmer_crop.farmer_id',$villageFarmer[$key]->farmerId)
                                ->select('village_farmer_crop.product_id','products.product','village_farmer_crop.crop_id','crop','acres')
                                ->get();
                            if ($products) {
                                $villageFarmer[$key]->products = $products;
                            } else {
                                $villageFarmer[$key]->products = [];
                            }
                            $crops = DB::table('village_farmer_crop')
                                ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                                ->where('village_farmer_crop.farmer_id',$villageFarmer[$key]->farmerId)
                                ->select('village_farmer_crop.crop_id','crop')
                                ->get();
                            if ($crops) {
                                $villageFarmer[$key]->crops = $crops;
                            } else {
                                $villageFarmer[$key]->crops = [];
                            }
                        }
                    } else {
                        $villageFarmerqry =VillageFarmer::join('village_farmer_crop','village_farmer_crop.farmer_id','=','village_farmers.farmer_id')
                        ->join('villages','villages.village_id','=','village_farmers.village_id')
                        ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                        ->join('districts','districts.district_id','=','villages.district_id')
                        ->join('states','states.state_id','=','districts.state_id')
                        ->where('village_farmers.village_id',$villageId);
                        if($request->has('updated_at')){
                            $updated_at = $request->input('updated_at');
                            if($updated_at!=0) {
                                $villageFarmerqry->where('village_farmers.updated_at','>=',$updated_at);
                            }
                        }
                        // $villageFarmerqry->where('village_farmers.dd_id',$user_id)
                        $villageFarmerqry->groupBy('village_farmers.contact_number')
                        ->select('village_farmers.farmer_id as farmerId','village_farmers.farmer_name as farmerName','village_farmers.father_name as fatherName',DB::raw('sum(acres) as acres'),'village_farmers.contact_number as contactNumber','village_farmers.alternate_contact_number as alternateContactNumber','village_farmers.farmer_image as farmarImage','states.state','tehsils.tehsil','districts.district','village_farmers.call_status','villages.tehsil_id as tehsilId','villages.district_id as districtId','districts.state_id as stateId','villages.village','villages.village_id','village_farmers.farmer_lat as farmerLat','village_farmers.farmer_long as farmerLong');
                        $villageFarmer = $villageFarmerqry->get();
                        foreach ($villageFarmer as $key => $value) {
                            $leaddata =  DB::table('leads')
                                        ->where('leads.farmer_id',$villageFarmer[$key]->farmer_id)
                                        ->select('leads.lead_status')
                                        ->get();
                            if ($leaddata) {
                                $villageFarmer[$key]->lead_status = $leaddata[0]->lead_status;
                            } else {
                                $villageFarmer[$key]->lead_status = "0";
                            }
                            $products = DB::table('village_farmer_crop')
                                ->join('products','products.product_id','=','village_farmer_crop.product_id','left')
                                ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                                ->where('village_farmer_crop.farmer_id',$villageFarmer[$key]->farmerId)
                                ->select('village_farmer_crop.product_id','products.product','village_farmer_crop.crop_id','crop','acres')->get();
                            if ($products) {
                                $villageFarmer[$key]->products = $products;
                            } else {
                                $villageFarmer[$key]->products = [];
                            }
                            $crops = DB::table('village_farmer_crop')
                                    ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                                    ->where('village_farmer_crop.farmer_id',$villageFarmer[$key]->farmerId)
                                    ->select('village_farmer_crop.crop_id','crop')
                                    ->get();
                            if ($crops) {
                                $villageFarmer[$key]->crops = $crops;
                            } else {
                                $villageFarmer[$key]->crops = [];
                            }
                        }
                    }
                    for($i=0;$i<count($villageFarmer);$i++){
                        $villageFarmer[$i]->farmarImage="https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/".$villageFarmer[$i]->farmarImage;
                        $markVisit=MarkVisit::where('farmer_id',$villageFarmer[$i]->farmerId)->count();
                        $villageFarmer[$i]->acpVisit=$markVisit;
                    }
                } else {
                    //Supervisor DD LIST
                    if ($user[0]->user_type == 'zonal') {
                        //zonal manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('zonal_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddArray = array(); 
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddArray = array_merge($ddArray, array_flatten($value)); 
                                } else {
                                    $ddArray[$key] = $value; 
                                }
                            }
                        }
                    } elseif ($user[0]->user_type == 'regional') {
                        //regional manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('regional_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddArray = array();
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddArray = array_merge($ddArray, array_flatten($value));
                                } else {
                                    $ddArray[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user[0]->user_type == 'area') {
                        //area manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('area_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddArray = array();
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddArray = array_merge($ddArray, array_flatten($value));
                                } else {
                                    $ddArray[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user[0]->user_type == 'sales_regional') {
                        //sales regional manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('sales_regional_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddArray = array(); 
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddArray = array_merge($ddArray, array_flatten($value));
                                } else {
                                    $ddArray[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user[0]->user_type == 'sales_area') {
                        //Sales area manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('sales_area_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddArray = array();
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) { 
                                if (is_array($value)) { 
                                    $ddArray = array_merge($ddArray, array_flatten($value));
                                } else {
                                    $ddArray[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user[0]->user_type == 'marketing_supervisor') {
                        //Marketing Supervisor RSM LIST
                        $rsmData = DB::table('marketing_supervisor_rsm')
                                  ->where('marketing_supervisor_id',$user_id)
                                  ->select('sales_regional_manager_id')
                                  ->get();
                        $rsmresult = array();
                        if (isset($rsmData) && !empty($rsmData)) {
                            //RSM LIST
                            $rsmDatass = json_decode(json_encode($rsmData),true);
                            foreach ($rsmDatass as $key => $value) {
                                if (is_array($value)) {
                                    $rsmresult = array_merge($rsmresult, array_flatten($value));
                                } else {
                                    $rsmresult[$key] = $value; 
                                }
                            }
                            if (count($rsmresult) > 0) {
                                //marketing supervisor dd
                                $ddData = DB::table('dhanuka_doctor')
                                          ->whereIn('sales_regional_manager_id',$rsmresult)
                                          ->where('status','1')
                                          ->select('dd_id')
                                          ->get();
                                $ddArray = array();
                                if (isset($ddData) && !empty($ddData)) {
                                    $ddDatass = json_decode(json_encode($ddData),true);
                                    foreach ($ddDatass as $key => $value) {
                                        if (is_array($value)) {
                                            $ddArray = array_merge($ddArray, array_flatten($value));
                                        } else {
                                            $ddArray[$key] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    /*Log::info("ddArray Start");
                    Log::info($ddArray);
                    Log::info("ddArray End");*/
                    //print_r($ddArray);die;
                    // DB::enableQueryLog();
                    $villageFarmer11 = VillageFarmer::join('village_farmer_crop','village_farmer_crop.farmer_id','=','village_farmers.farmer_id')
                                ->join('villages','villages.village_id','=','village_farmers.village_id')
                                ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','village_farmers.dd_id')
                                ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                                ->join('districts','districts.district_id','=','villages.district_id')
                                ->join('states','states.state_id','=','districts.state_id');
                        if($request->has('dd_id')) {   
                            $villageFarmer11->where('village_farmers.dd_id',$request->input('dd_id'));
                        } else {
                            $villageFarmer11->whereIn('village_farmers.dd_id',$ddArray);
                        }
                        if($request->input('village') != '') {
                            $villageFarmer11->where('village_farmers.village_id',$request->input('village'));
                        }
                        if(!empty($request->input('from')) && !empty($request->input('to'))) {
                            $villageFarmer11->whereDate('village_farmers.created_at','>=',$request->input('from'))->whereDate('village_farmers.created_at','<=',$request->input('to'));
                        }
                        if($request->input('crop') != '') {
                            $villageFarmer11->where('village_farmer_crop.crop_id',$request->input('crop'));
                        }
                        if($request->input('acerage') != '') {
                            $villageFarmer11->where('village_farmer_crop.acres','<=',$request->input('acerage'));
                        }
                        if($request->has('updated_at')){
                            $updated_at = $request->input('updated_at');
                            if($updated_at!=0) {
                                $villageFarmer11->where('updated_at','>=',$updated_at);
                            }
                        }
                    $villageFarmer1 = $villageFarmer11->groupBy('village_farmers.contact_number')
                       ->select('village_farmers.farmer_id as farmerId','dhanuka_doctor.name','village_farmers.farmer_name as farmerName','village_farmers.father_name as fatherName',DB::raw('sum(acres) as acres'),'village_farmers.contact_number as contactNumber','village_farmers.alternate_contact_number as alternateContactNumber','village_farmers.farmer_image as farmarImage','states.state','tehsils.tehsil','districts.district','village_farmers.call_status','villages.tehsil_id as tehsilId','villages.district_id as districtId','districts.state_id as stateId','villages.village','villages.village_id','village_farmers.farmer_lat as farmerLat','village_farmers.farmer_long as farmerLong')->get();
                    // $querylog = DB::getQueryLog();
                    // Log::info($querylog);
                         //echo '<pre>'; print_r($villageFarmer1); die;
                    foreach ($villageFarmer1 as $key => $value) {
                        $markVisit=MarkVisit::where('farmer_id',$villageFarmer1[$key]->farmerId)->count();
                        $villageFarmer1[$key]->acpVisit=$markVisit;
                        $leaddata =  DB::table('leads')
                                    ->where('leads.farmer_id',$villageFarmer1[$key]->farmerId)
                                    ->select('leads.lead_status')
                                    ->get();
                        if ($leaddata) {
                            $villageFarmer1[$key]->lead_status = $leaddata[0]->lead_status;
                        } else {
                            $villageFarmer1[$key]->lead_status = "0";
                        }
                        $products = DB::table('village_farmer_crop')
                            ->join('products','products.product_id','=','village_farmer_crop.product_id','left')
                            ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                            ->where('village_farmer_crop.farmer_id',$villageFarmer1[$key]->farmerId)
                            ->select('village_farmer_crop.product_id','products.product','village_farmer_crop.crop_id','crop','acres')
                            ->get();
                        if ($products) {
                            $villageFarmer1[$key]->products = $products;
                        } else {
                            $villageFarmer1[$key]->products = [];
                        }
                        $crops = DB::table('village_farmer_crop')
                            ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                            ->where('village_farmer_crop.farmer_id',$villageFarmer1[$key]->farmerId)
                            ->select('village_farmer_crop.crop_id','crop')
                            ->get();
                        if ($crops) {
                            $villageFarmer1[$key]->crops = $crops;
                        } else {
                            $villageFarmer1[$key]->crops = [];
                        }
                        $leads = DB::table('leads')
                            ->where('farmer_id',$villageFarmer1[$key]->farmerId)
                            ->where('farmer_type','normal_farmer')
                            ->select('lead_id', 'lead_code', 'lead_status', 'created_at','expected_purchase_date','purchased_from','purchased_area')
                            ->get();
                        if ($leads) {
                            $villageFarmer1[$key]->leads = $leads;
                        } else {
                            $villageFarmer1[$key]->leads = [];
                        }
                    }
                    for($i=0;$i<count($villageFarmer1);$i++){
                        $villageFarmer1[$i]->farmarImage="https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/".$villageFarmer1[$i]->farmarImage;
                        $markVisit=MarkVisit::where('farmer_id',$villageFarmer1[$i]->farmerId)->count();
                        $villageFarmer1[$i]->acpVisit=$markVisit;
                    }
                    $requestDD = $request->input('dd_id');
                    $requestVillage = $request->input('village');
                    $from = $request->input('from');
                    $to = $request->input('to');
                    $crop = $request->input('crop');
                    $acres = $request->input('acerage');
                    //Total ACP Famers
                    $villageFarmer22 =VillageFarmer::where('status','1');
                        if($request->input('dd_id') != '') {
                            $villageFarmer22->where('village_farmers.dd_id',$request->input('dd_id'));
                        } else {
                            $villageFarmer22->whereIn('village_farmers.dd_id',$ddArray);
                        }
                        if($request->input('village') != '') {
                            $villageFarmer22->where('village_farmers.village_id',$request->input('village'));
                        }
                        if(!empty($from) && !empty($to)) {
                            $villageFarmer22->whereDate('village_farmers.created_at','>=',$from)->whereDate('village_farmers.created_at','<=',$to);
                        }
                        if(!empty($crop) || !empty($acres)) {
                            $villageFarmer22->rightJoin('village_farmer_crop', function($join) {
                                $join->on('village_farmer_crop.farmer_id', '=', 'village_farmers.farmer_id');
                            });
                        }
                        if(!empty($crop)) {
                            $villageFarmer22->where('village_farmer_crop.crop_id',$crop);
                        }
                        if($acres != '')
                        {
                            $villageFarmer22->where('village_farmer_crop.acres','<=',$acres);
                        }
                    $villageFarmer2 = $villageFarmer22->count();
                    //Total Leads
                    $query12 = DB::table('village_farmers')
                                ->select('dd_id')
                                ->where('status', '1')
                                ->groupBy('dd_id');
                                if($requestDD != '') {
                                    $query12->where('dd_id',$requestDD);
                                } else {
                                    $query12->whereIn('dd_id', $ddArray);
                                }
                                if($requestVillage != '') {
                                    $query12->where('village_id',$requestVillage);
                                }
                                if(!empty($from) && !empty($to)) {
                                    $query12->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
                                }
                                /*if($request->has('updated_at')){
                                    $updated_at = $request->input('updated_at');
                                    if($updated_at!=0) {
                                        $query12->where('updated_at','>=',$updated_at);
                                    }
                                }*/
                    $getuserId2 =   $query12->get();
                    $userArray1 = array();
                    if (count($getuserId2) > 0) {
                       foreach ($getuserId2 as $keyDD => $valueDD) 
                        {
                            array_push($userArray1, $getuserId2[$keyDD]->dd_id);
                        } 
                    }
                    $villageFarmer33 = DB::table('leads')
                                    ->where('farmer_type','normal_farmer')
                                    ->where('user_type','dhanuka_doctor');
                                    // ->whereIn('user_id',$ddArray);
                                    if (count($userArray1) > 0) {
                                        $villageFarmer33->whereIn('user_id',$userArray1);
                                    }
                            if(!empty($crop) || !empty($acres)) {
                                $villageFarmer33->rightJoin('village_farmer_crop', function($join) {
                                    $join->on('village_farmer_crop.farmer_id', '=', 'leads.farmer_id');
                                });
                            }
                            if(!empty($crop)) {
                                $villageFarmer33->where('village_farmer_crop.crop_id',$crop);
                            }
                            if($acres != '') {
                                $villageFarmer33->where('village_farmer_crop.acres','<=',$acres);
                            }
                            $villageFarmer3 = $villageFarmer33->where(function ($query){
                                $query->where('lead_status','1')->orWhere('lead_status','2');
                            });
                    $totalLeads =   $villageFarmer3->count();
                    //  totalAcres 
                    $query13 = DB::table('village_farmers')
                                ->select('dd_id')
                                ->where('status', '1')
                                ->groupBy('dd_id');
                                if($requestDD != '') {
                                    $query13->where('dd_id',$requestDD);
                                } else {
                                    $query13->whereIn('dd_id', $ddArray);
                                }
                                if($requestVillage != '') {
                                    $query13->where('village_id',$requestVillage);
                                }
                                if(!empty($from) && !empty($to)) {
                                    $query13->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
                                }
                                if($request->has('updated_at')){
                                    $updated_at = $request->input('updated_at');
                                    if($updated_at!=0) {
                                        $query13->where('updated_at','>=',$updated_at);
                                    }
                                }
                    $getuserId1 =   $query13->get();
                    $userArray2 = array();
                    if (count($getuserId1) > 0) {
                        foreach ($getuserId1 as $keyDD => $valueDD) {
                            array_push($userArray2, $getuserId1[$keyDD]->dd_id);
                        } 
                    }
                    // DB::enableQueryLog();
                    // Log::info($ddArray);
                    // Log::info($getuserId1);
                    // Log::info($userArray2);
                    /*$villageFarmer44 = DB::table('village_farmer_crop')
                                       ->select('acres')
                                       ->whereIn('farmer_id', function($query) use ($ddArray,$requestDD,$requestVillage,$from,$to){
                                        $query->from('leads')
                                        ->selectRaw('farmer_id')
                                        ->where('farmer_type','normal_farmer')
                                        ->where('user_type','dhanuka_doctor')
                                        // ->whereIn('user_id',$ddArray);
                                        if (isset($userArray2) && count($userArray2)>0) {
                                            $query->whereIn('user_id',$userArray2);
                                        }
                                        $query->whereIn('lead_status',[1,2,3]);
                                    });*/
                        $villageFarmer44 = VillageFarmer::join('village_farmer_crop','village_farmer_crop.farmer_id','=','village_farmers.farmer_id')
                                ->join('villages','villages.village_id','=','village_farmers.village_id')
                                ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','village_farmers.dd_id')
                                ->select('village_farmer_crop.acres');
                            if($request->has('dd_id')) {   
                                $villageFarmer44->where('village_farmers.dd_id',$request->input('dd_id'));
                            } else {
                                $villageFarmer44->whereIn('village_farmers.dd_id',$ddArray);
                            }
                            if($crop != '') {
                                $villageFarmer44->where('village_farmer_crop.crop_id',$crop);
                            }
                            if($acres != '') {
                                $villageFarmer44->where('village_farmer_crop.acres','<=',$acres);
                            }
                            /*if($request->has('updated_at')){
                                $updated_at = $request->input('updated_at');
                                if($updated_at!=0) {
                                    $villageFarmer44->where('updated_at','>=',$updated_at);
                                }
                            }*/
                        $villageFarmer4 = $villageFarmer44->get();
                        // $querylog = DB::getQueryLog();
                        // Log::info($querylog);
                        $totalSumAcres = array();
                        foreach ($villageFarmer4 as $keyA => $valueA) {
                            array_push($totalSumAcres, $valueA->acres);
                        }
                        // totalAcres CLosed 
                        $query14 = DB::table('village_farmers')
                                ->select('dd_id')
                                ->where('status', '1');
                                if($requestDD != '') {
                                    $query14->where('dd_id',$requestDD);
                                } else {
                                    $query14->whereIn('dd_id', $ddArray);
                                }
                                if($requestVillage != '') {
                                    $query14->where('village_id',$requestVillage);
                                }
                                if(!empty($from) && !empty($to)) {
                                    $query14->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
                                }
                                /*if($request->has('updated_at')){
                                    $updated_at = $request->input('updated_at');
                                    if($updated_at!=0) {
                                        $query14->where('updated_at','>=',$updated_at);
                                    }
                                }*/
                    $getuserId4 =   $query14->get();
                    $userArray3 = array();
                    if (count($getuserId4) > 0) {
                        foreach ($getuserId4 as $keyDD => $valueDD) {
                            array_push($userArray3, $getuserId4[$keyDD]->dd_id);
                        }
                    }
                    /*$villageFarmer55 = DB::table('village_farmer_crop')
                                        ->select('acres')
                                        ->whereIn('farmer_id', function($query) use ($ddArray,$requestDD,$requestVillage,$from,$to){
                                            $query->from('leads')
                                            ->selectRaw('farmer_id')
                                            ->where('farmer_type','normal_farmer')
                                            ->where('user_type','dhanuka_doctor')
                                            // ->whereIn('user_id',$ddArray);
                                            if (isset($userArray3) &&  count($userArray3) > 0) {
                                                $query->whereIn('user_id',$userArray3);
                                            }
                                            $query->where('lead_status','2');
                                        });*/

                    // DB::enableQueryLog();
                    $getFarmer_id_c = DB::table('leads')->select('farmer_id')->where('farmer_type','normal_farmer')->where('user_type','dhanuka_doctor');
                    if($request->has('dd_id')) {   
                        $getFarmer_id_c->where('user_id',$request->input('dd_id'));
                    } else {
                        $getFarmer_id_c->whereIn('user_id',$ddArray);
                    }
                    $getFarmer_id_c->where('lead_status',2);
                    $listFarmer_ids = $getFarmer_id_c->get();
                    /*$querylog = DB::getQueryLog();
                    Log::info($querylog);*/
                    
                    if(count($listFarmer_ids)>0) {
                        $farmer_idsc = array();
                        foreach ($listFarmer_ids as $keyDD => $valueDD) {
                            array_push($farmer_idsc, $listFarmer_ids[$keyDD]->farmer_id);
                        }
                        $villageFarmer55 = VillageFarmer::join('village_farmer_crop','village_farmer_crop.farmer_id','=','village_farmers.farmer_id')
                                    ->join('villages','villages.village_id','=','village_farmers.village_id')
                                    ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','village_farmers.dd_id')
                                    ->where('village_farmer_crop.farmer_id',$farmer_idsc)
                                    ->select('village_farmer_crop.acres');
                                if($crop != '') {
                                    $villageFarmer55->where('village_farmer_crop.crop_id',$crop);
                                }
                                if($acres != '') {
                                    $villageFarmer55->where('village_farmer_crop.acres','<=',$acres);
                                }
                                /*if($request->has('updated_at')){
                                    $updated_at = $request->input('updated_at');
                                    if($updated_at!=0) {
                                        $villageFarmer55->where('updated_at','>=',$updated_at);
                                    }
                                }*/
                        $villageFarmer5 = $villageFarmer55->get();
                    } else {
                        $villageFarmer5 = array();
                    }
                    // echo "<pre>"; print_r($villageFarmer5); die;
                    $totalCloseAcres = array();
                    foreach ($villageFarmer5 as $keyAC => $valueAC) {
                        array_push($totalCloseAcres, $valueAC->acres);
                    }
                    $ddData = DB::table('dhanuka_doctor')->where('bdo_id',$user_id)->where('status','1')->get();
                    $ddListRecord = array();
                    foreach ($ddData as $keyDD1 => $valueDD1) {
                        $ddListRecord[$keyDD1]['dd_id'] = $valueDD1->dd_id;
                        $ddListRecord[$keyDD1]['ECN'] = $valueDD1->ECN;
                        $ddListRecord[$keyDD1]['name'] = $valueDD1->name;
                    }
                    $villageFarmer['totalACPFamer'] = $villageFarmer2;
                    $villageFarmer['total_leads'] = $totalLeads;
                    $villageFarmer['totalAcres'] = array_sum($totalSumAcres);
                    $villageFarmer['totalClosedAcres'] = array_sum($totalCloseAcres);
                    $villageFarmer['ACPDetail'] = $villageFarmer1;
                    $villageFarmer['DDList'] = $ddListRecord;
                }
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Farmer List.',
                    'data' =>$villageFarmer
                ]);
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function s1selfParticipationSubmit(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'arival_time'   => 'required',
            'tId'           => 'required|string',
            'desc'          => 'required|string',
            'photo'         => 'required|max:300000|mimes:jpg,png,jpeg'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $ticket_id    = $data['tId'];
            $arival_time  = $data['arival_time'];
            $description  = $data['desc'];
            $latitude     = (isset($data['latitude']) ? $data['latitude'] : '');
            $longitude    = (isset($data['longitude']) ? $data['longitude'] : '');

            $file = $request->file('photo');
            if ($request->hasFile('photo')) {
                if ($request->file('photo')->isValid()) {
                    $user = $this->findUser($api_token);
                    if ($user && count($user) == '1') {
                        $user_id = $user[0]->id;
                        $user_type = $user[0]->user_type;
                        $ticket_check = Ticket::where('ticket_id', $ticket_id)->first();
                        if ($ticket_check) {
                            //s3Upload
                            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $fileName = "S" . md5(time() . rand()) . str_replace($file->getClientOriginalName(), '', $file->getClientOriginalName()) . '.' . $extension;
                            $sTempFileName = 'assets/self_participation/' . $fileName;
                            Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            //
                            $check_self_participation = SelfParticipation::where(['status' => '1', 'ticket_id' => $ticket_id, 'user_id' => $user_id])->first();
                            if ($check_self_participation) {
                                $update_self_participation = SelfParticipation::where(['status' => '1', 'ticket_id' => $ticket_id, 'user_id' => $user_id])->update(['description' => $description, 'uploaded_file' => $sTempFileName, 'latitude' => $latitude, 'longitude' => $longitude, 'arival_time' => $arival_time, 'updated_at'=>date('Y-m-d H:i:s')]);
                                if ($update_self_participation) {
                                    $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                                    $data = array('url' => $base_url . '/' . $sTempFileName);
                                    return $this->respond([
                                        'status'  => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Self Participation Successfully Uploaded!',
                                        'data'    => $data
                                    ]);
                                } else {
                                    return $this->respondNotFound("Something went wrong. Please Try Again!!");
                                }
                            } else {
                                $self_participation_data = array(
                                    'ticket_id'     => $ticket_id,
                                    'user_id'       => $user_id,
                                    'user_type'     => $user_type,
                                    'arival_time'   => $arival_time,
                                    'description'   => $description,
                                    'uploaded_file' => $sTempFileName,
                                    'latitude'      => $latitude,
                                    'longitude'     => $longitude,
                                    'status'        => '1',
                                    'created_at'=>date('Y-m-d H:i:s'),
                                    'updated_at'=>date('Y-m-d H:i:s')
                                );
                                $result = SelfParticipation::firstOrNew($self_participation_data);
                                $result = $result->save();

                                $check_atp_in_ticket = Ticket::where(['ticket_id' => $ticket_id])->first();
                                if ($check_atp_in_ticket) {
                                    $atp_id = $check_atp_in_ticket->plan_atp_id;
                                    $update_self_participation_plan_status = SelfParticipationPlan::where(['plan_atp_id' => $atp_id, 'user_type' => $user_type, 'user_id' => $user_id])->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                                }
                                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                                $data = array('url' => $base_url . '/' . $sTempFileName);
                                return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Self Participation Successfully Uploaded!',
                                    'data' => $data
                                ]);
                            }
                        } else {
                            return $this->respondNotFound("Invalid Ticket !!");
                        }
                    } else {
                        return $this->respondWithError("Invalid Attempt !!");
                    }
                } else {
                    return $this->respondWithError("Invalid File !!");
                }
            } else {
                return $this->respondWithError("File Not Found !!");
            }
        }
    }

    public function s1selfParticipationDepartureSubmit(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'departure_time'=> 'required',
            'tId'           => 'required|string',
            'desc'          => 'required|string',
            'latitude'      => 'required',
            'longitude'     => 'required',
            'photo'         => 'required|max:300000|mimes:jpg,png,jpeg'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $ticket_id    = $data['tId'];
            $departure_time         = $data['departure_time'];
            $departure_description  = $data['desc'];
            $departure_latitude     = $data['latitude'];
            $departure_longitude    = $data['longitude'];
            $duration               = (isset($data['duration']) ? $data['duration'] : 0);

            $file = $request->file('photo');
            if ($request->hasFile('photo')) {
                if ($request->file('photo')->isValid()) {
                    $user = $this->findUser($api_token);
                    if ($user && count($user) == '1') {
                        $user_id = $user[0]->id;
                        $user_type = $user[0]->user_type;
                        $ticket_check = Ticket::where('ticket_id', $ticket_id)->first();
                        if ($ticket_check) {
                            //s3Upload
                            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $fileName = "S" . md5(time() . rand()) . str_replace($file->getClientOriginalName(), '', $file->getClientOriginalName()) . '.' . $extension;
                            $sTempFileName = 'assets/self_participation/' . $fileName;
                            Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            //
                            $check_self_participation = SelfParticipation::where(['status' => '1', 'ticket_id' => $ticket_id, 'user_id' => $user_id, 'user_type' => $user_type])->first();
                            if ($check_self_participation) {
                                $update_self_participation = SelfParticipation::where(['status' => '1', 'ticket_id' => $ticket_id, 'user_id' => $user_id, 'user_type' => $user_type])->update(['departure_description' => $departure_description, 'departure_uploaded_file' => $sTempFileName, 'departure_latitude' => $departure_latitude, 'departure_longitude' => $departure_longitude, 'departure_time' => $departure_time, 'duration' => $duration, 'updated_at'=>date('Y-m-d H:i:s')]);
                                if ($update_self_participation) {
                                    $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                                    $data = array('url' => $base_url . '/' . $sTempFileName);
                                    return $this->respond([
                                        'status'  => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Self Participation Successfully Uploaded!',
                                        'data'    => $data
                                    ]);
                                } else {
                                    return $this->respondNotFound("Something went wrong. Please Try Again!!");
                                }
                            } else {
                                $self_participation_data = array(
                                    'ticket_id'     => $ticket_id,
                                    'user_id'       => $user_id,
                                    'user_type'     => $user_type,
                                    'departure_time'=> $departure_time,
                                    'departure_description'   => $departure_description,
                                    'departure_uploaded_file' => $sTempFileName,
                                    'departure_latitude'      => $departure_latitude,
                                    'departure_longitude'     => $departure_longitude,
                                    'duration'      => $duration,
                                    'created_at'=>date('Y-m-d H:i:s'),
                                    'updated_at'=>date('Y-m-d H:i:s')
                                );
                                $result = SelfParticipation::firstOrNew($self_participation_data);
                                $result = $result->save();

                                $check_atp_in_ticket = Ticket::where(['ticket_id' => $ticket_id])->first();
                                if ($check_atp_in_ticket) {
                                    $atp_id = $check_atp_in_ticket->plan_atp_id;
                                    $update_self_participation_plan_status = SelfParticipationPlan::where(['plan_atp_id' => $atp_id, 'user_type' => $user_type, 'user_id' => $user_id])->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                                }
                                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                                $data = array('url' => $base_url . '/' . $sTempFileName);
                                return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Self Participation Successfully Uploaded!',
                                    'data' => $data
                                ]);
                            }
                        } else {
                            return $this->respondNotFound("Invalid Ticket !!");
                        }
                    } else {
                        return $this->respondWithError("Invalid Attempt !!");
                    }
                } else {
                    return $this->respondWithError("Invalid File !!");
                }
            } else {
                return $this->respondWithError("File Not Found !!");
            }
        }
    }

    public function s1selfParticipationHistory($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                //$base_url = url('/');
                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                switch ($user_type) {
                    case "zonal" : $bdo_list = $this->categoryWiseBdoList('zonal_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                        break;
                    case "regional" : $bdo_list = $this->categoryWiseBdoList('regional_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                        break;
                    case "area" : $bdo_list = $this->categoryWiseBdoList('area_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                        break;
                    case "sales_regional" : $bdo_list = $this->categoryWiseBdoList('sales_regional_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                        break;
                    case "sales_area" : $bdo_list = $this->categoryWiseBdoList('sales_area_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                        break;
                    case "bdo" : $bdo_list = $this->categoryWiseBdoList('id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                        break;
                }

                $users = (isset($bdo_list_query[0]['user_id']) ? trim($bdo_list_query[0]['user_id'], "'") : '');
                if ($users != '') {
                    $crr_date = date('Y-m-d H:i:s');
                    $query = "
                                SELECT
                                    u.name,
                                    ut.ticket_id as tID, 
                                    ut.ticket_no as ticket, 
                                    DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format,
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
                                    '" . $base_url . "/' as base_url,
                                    sp.uploaded_file,
                                    sp.description,
                                    sp.arival_time,
                                    sp.latitude,
                                    sp.longitude,

                                    sp.departure_time,
                                    sp.departure_description,
                                    sp.departure_uploaded_file,
                                    sp.departure_latitude,
                                    sp.departure_longitude,
                                    sp.duration,
                                    sp.updated_at
                                FROM        self_participations sp
                                LEFT JOIN   tickets             ut  ON sp.ticket_id=ut.ticket_id
                                LEFT JOIN   data_sources        ds  ON ut.data_source_id=ds.data_source_id
                                LEFT JOIN   states              s   ON ut.state_id=s.state_id
                                LEFT JOIN   districts           d   ON ut.district_id=d.district_id
                                LEFT JOIN   tehsils             t   ON ut.tehsil_id=t.tehsil_id
                                LEFT JOIN   users               u   ON ut.user_id=u.id

                                LEFT JOIN products      p1  ON ut.product_1_id=p1.product_id
                                LEFT JOIN products      p2  ON ut.product_2_id=p2.product_id
                                LEFT JOIN products      p3  ON ut.product_3_id=p3.product_id

                                LEFT JOIN crops         c1  ON ut.crop_1_id=c1.crop_id
                                LEFT JOIN crops         c2  ON ut.crop_2_id=c2.crop_id
                                LEFT JOIN crops         c3  ON ut.crop_3_id=c3.crop_id

                                WHERE
                                    sp.user_id      = '" . $user_id . "'    AND
                                    sp.user_type    = '" . $user_type . "'
                                ORDER BY
                                    sp.created_at DESC
                                ";
                    $open_ticket = DB::select(DB::raw($query));
                    $result = array_map(function ($value) {
                        return (array) $value;
                    }, $open_ticket);
                    if ($result) {
                        $data = array('bdo_list' => $bdo_list, 'ticket_list' => $result);
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'List Item Found!',
                                    'crr_date' => $crr_date,
                                    'data' => $data
                        ]);
                    } else {
                        return $this->respondWithError("Participation History Not Found !");
                    }
                } else {
                    return $this->respondWithError("No User List Found!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s1selfParticipationArivalDepartureSubmit(Request $request) {
        $data = $request->all();
        // Log::info($data);
        $rules = array(
            'apiToken'              => 'required|string',
            'tId'                   => 'required|string',
            'arival_time'           => 'required',
            // 'arival_desc'           => 'required|string',
            // 'arival_photo'          => 'required|mimes:jpg,png,jpeg',
            'arival_latitude'       => 'required',
            'arival_longitude'      => 'required',
            'departure_time'        => 'required',
            // 'departure_desc'        => 'required|string',
            'departure_latitude'    => 'required',
            'departure_longitude'   => 'required',
            // 'departure_photo'       => 'required|mimes:jpg,png,jpeg',
            'duration'              => 'required',
            'is_online'             => 'required'   // 1=> Online, 0=> Offline
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $arival_sTempFileName = '';
                $departure_sTempFileName = '';
                $is_online  = $data['is_online'];
                $tId        = $data['tId'];
                if($is_online == '1') {
                    $checkWhere = 'ticket_id';
                } else if($is_online == '0') {
                    $checkWhere = 'ticket_no';
                } else {
                    return $this->respondNotFound("Invalid in Online!!");
                }
                // return $checkWhere;
                $ticket_check = Ticket::where($checkWhere, $tId)->first();
                if ($ticket_check) {
                    $ticket_id          = $ticket_check->ticket_id;
                    $arival_time        = $data['arival_time'];
                    $arival_description = (!empty($data['arival_desc'])) ? $data['arival_desc'] : '';
                    $arival_latitude    = $data['arival_latitude'];
                    $arival_longitude   = $data['arival_longitude'];

                    $departure_time         = $data['departure_time'];
                    $departure_description  = (!empty($data['departure_desc'])) ? $data['departure_desc'] : '';
                    $departure_latitude     = $data['departure_latitude'];
                    $departure_longitude    = $data['departure_longitude'];
                    $duration               = (isset($data['duration']) ? $data['duration'] : 0);

                    $crr_date    = date('Y-m-d H:i:s');
                    $crrdateUser = $crr_date."-".$user_id."-".$ticket_id;
                    if ($request->hasFile('arival_photo')) {
                        $arival_file = $request->file('arival_photo');
                        if ($request->file('arival_photo')->isValid()) {
                            $fileSize =  $request->file('arival_photo')->getSize();
                            //s3Upload
                            if($fileSize > 100) {
                                $extension = pathinfo($arival_file->getClientOriginalName(), PATHINFO_EXTENSION);
                                $arival_fileName = "S" . md5(time() . rand()) . str_replace($arival_file->getClientOriginalName(), '', $arival_file->getClientOriginalName()) . '.' . $extension;
                                $arival_sTempFileName = 'assets/self_participation/' . $arival_fileName;
                                Storage::disk('s3')->put($arival_sTempFileName, fopen($arival_file, 'r+'),'public');
                                //
                                Log::info("arival_sTempFileName>=100-".$crrdateUser."=>".$arival_sTempFileName);
                            } else {
                                Log::info("arival_sTempFileName<100-".$crrdateUser."=>".$arival_sTempFileName);
                            }
                        }
                    }
                    if ($request->hasFile('departure_photo')) {
                        if ($request->file('departure_photo')->isValid()) {
                            $departure_file = $request->file('departure_photo');
                            $fileSize =  $request->file('departure_photo')->getSize();
                            //s3Upload
                            if($fileSize > 100) {
                                $departure_extension = pathinfo($departure_file->getClientOriginalName(), PATHINFO_EXTENSION);
                                $departure_fileName = "S" . md5(time() . rand()) . str_replace($departure_file->getClientOriginalName(), '', $departure_file->getClientOriginalName()) . '.' . $departure_extension;
                                $departure_sTempFileName = 'assets/self_participation/' . $departure_fileName;
                                Storage::disk('s3')->put($departure_sTempFileName, fopen($departure_file, 'r+'),'public');
                                Log::info("departure_sTempFileName>=100-".$crrdateUser."=>".$departure_sTempFileName);
                            } else {
                                Log::info("departure_sTempFileName<100-".$crrdateUser."=>".$departure_sTempFileName);
                            }
                        }
                    }
                    $village_id   = $ticket_check->village_id;
                    if($village_id) {
                        $distanceData = $this->CalculateDistance($village_id,$departure_latitude, $departure_longitude);
                        if($distanceData!=0) {
                            $expDistanceData = explode("~", $distanceData);
                            $distance = $expDistanceData[0];
                            $is_valid = $expDistanceData[1];
                            $radius   = $expDistanceData[2];
                        } else {
                            $distance = 0;
                            $radius   = 0;
                            $is_valid = 0;
                        }
                    } else {
                        $distance = 0;
                        $radius   = 0;
                        $is_valid = 0;
                    }
                    $check_self_participation = SelfParticipation::where(['status' => '1', 'ticket_id' => $ticket_id, 'user_id' => $user_id, 'user_type' => $user_type])->first();
                    $data = array();
                    if ($check_self_participation) {
                        $update_self_participation = SelfParticipation::where([
                                'status'    => '1',
                                'ticket_id' => $ticket_id,
                                'user_id'   => $user_id,
                                'user_type' => $user_type
                            ])->update([
                                'description'   => $arival_description,
                                'uploaded_file' => $arival_sTempFileName,
                                'latitude'      => $arival_latitude,
                                'longitude'     => $arival_longitude,
                                'arival_time'   => $arival_time,
                                'departure_time'=> $departure_time,
                                'departure_description'   => $departure_description,
                                'departure_uploaded_file' => $departure_sTempFileName,
                                'departure_latitude'      => $departure_latitude,
                                'departure_longitude'     => $departure_longitude,
                                'duration'      => $duration,
                                'distance'      => $distance,
                                'is_valid'      => $is_valid,
                                'radius'        => $radius,
                                'updated_at'    => date('Y-m-d H:i:s')
                            ]);
                        if ($update_self_participation) {
                            $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                            if($arival_sTempFileName!="" && $departure_sTempFileName!="") {
                                $data = array(
                                    'arival_image'      => $base_url.'/'.$arival_sTempFileName,
                                    'departure_image'   => $base_url.'/'.$departure_sTempFileName
                                );
                            }
                            return $this->respond([
                                'status'  => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Self Participation Successfully Uploaded!',
                                'data'    => $data
                            ]);
                        } else {
                            return $this->respondNotFound("Something went wrong. Please Try Again!!");
                        }
                    } else {
                        $self_participation_data = array(
                            'ticket_id'     => $ticket_id,
                            'user_id'       => $user_id,
                            'user_type'     => $user_type,
                            'description'   => $arival_description,
                            'uploaded_file' => $arival_sTempFileName,
                            'latitude'      => $arival_latitude,
                            'longitude'     => $arival_longitude,
                            'arival_time'   => $arival_time,
                            'departure_time'=> $departure_time,
                            'departure_description'   => $departure_description,
                            'departure_uploaded_file' => $departure_sTempFileName,
                            'departure_latitude'      => $departure_latitude,
                            'departure_longitude'     => $departure_longitude,
                            'duration'      => $duration,
                            'distance'      => $distance,
                            'is_valid'      => $is_valid,
                            'radius'        => $radius,
                            'status'        => '1',
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        );
                        $result = SelfParticipation::firstOrNew($self_participation_data);
                        $result = $result->save();

                        $check_atp_in_ticket = Ticket::where(['ticket_id' => $ticket_id])->first();
                        if ($check_atp_in_ticket) {
                            $atp_id = $check_atp_in_ticket->plan_atp_id;
                            $update_self_participation_plan_status = SelfParticipationPlan::where([
                                'plan_atp_id'   => $atp_id,
                                'user_type'     => $user_type,
                                'user_id'       => $user_id,
                                'user_type'     => $user_type
                            ])->update([
                                'status'    => '2',
                                'updated_at'=> date('Y-m-d H:i:s')
                            ]);
                        }
                        $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                        if($arival_sTempFileName!="" && $departure_sTempFileName!="") {
                            $data = array(
                                'arival_image'      => $base_url.'/'.$arival_sTempFileName,
                                'departure_image'   => $base_url.'/'.$departure_sTempFileName
                            );
                        }
                        return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message'   => 'Self Participation Successfully Uploaded!',
                            'data'      => $data
                        ]);
                    }
                } else {
                    return $this->respondNotFound("Invalid Ticket !!");
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function SupervisorActivatedTickets(Request $request) {   
        $rules = array(
            'apiToken' => 'required|string'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') 
            {   
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                //$base_url = url('/');
                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                if ($user_type == 'zonal') {
                    //zonal manager dd
                    $ddData = DB::table('dhanuka_doctor')
                              ->where('zonal_manager_id',$user_id)
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

                    //zonal manager bdo
                    $bdoData = DB::table('users')
                              ->where('zonal_manager_id',$user_id)
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
                    $dd_ids = implode("','", $ddresult);
                    $users  = implode("','", $bdoresult);
                }
                elseif ($user_type == 'regional') {
                    //regional manager dd
                    $ddData = DB::table('dhanuka_doctor')
                              ->where('regional_manager_id',$user_id)
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

                    //regional manager bdo
                    $bdoData = DB::table('users')
                              ->where('regional_manager_id',$user_id)
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
                    $dd_ids = implode("','", $ddresult);
                    $users  = implode("','", $bdoresult);
                }
                elseif ($user_type == 'area') {
                    //area manager dd
                    $ddData = DB::table('dhanuka_doctor')
                              ->where('area_manager_id',$user_id)
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

                    //area manager bdo
                    $bdoData = DB::table('users')
                              ->where('area_manager_id',$user_id)
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
                    $dd_ids = implode("','", $ddresult);
                    $users  = implode("','", $bdoresult);
                }
                elseif ($user_type == 'sales_regional') {
                    //sales regional manager dd
                    $ddData = DB::table('dhanuka_doctor')
                              ->where('sales_regional_manager_id',$user_id)
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
                        $dd_ids = implode("','", $ddresult);
                    }

                    //sales regional manager bdo
                    $bdoData = DB::table('users')
                              ->where('sales_regional_manager_id',$user_id)
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
                        $users  = implode("','", $bdoresult);
                    }
                }
                elseif ($user_type == 'sales_area') {
                    //Sales area manager dd
                    $ddData = DB::table('dhanuka_doctor')
                              ->where('sales_area_manager_id',$user_id)
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

                    //Sales area manager bdo
                    $bdoData = DB::table('users')
                              ->where('sales_area_manager_id',$user_id)
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
                    $dd_ids = implode("','", $ddresult);
                    $users  = implode("','", $bdoresult);
                }
                elseif ($user_type == 'marketing_supervisor') 
                {
                    //Marketing Supervisor RSM LIST
                    $rsmData = DB::table('marketing_supervisor_rsm')
                              ->where('marketing_supervisor_id',$user_id)
                              ->select('sales_regional_manager_id')
                              ->get();
                    $rsmresult = array(); 
                    if (isset($rsmData) && !empty($rsmData)) 
                    {   
                        //RSM LIST
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

                        if (count($rsmresult) > 0) {
                            //marketing supervisor bdo
                            $bdoData = DB::table('users')
                                      ->whereIn('sales_regional_manager_id',$rsmresult)
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
                                if (count($bdoresult) > 0) {
                                    array_unique($bdoresult);
                                    $users  = implode("','", $bdoresult);
                                }
                                else
                                {
                                    $users  = implode("','", $bdoresult);
                                }
                                
                            }
                            
                            if (count($bdoresult) > 0) {
                                //marketing supervisor dd
                                $ddData = DB::table('dhanuka_doctor')
                                          ->whereIn('sales_regional_manager_id',$rsmresult)
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
                                    if (count($ddresult) > 0) {
                                        array_unique($ddresult);
                                        $dd_ids = implode("','", $ddresult);
                                    }
                                    else
                                    {
                                        $dd_ids = implode("','", $ddresult);
                                    }
                                }
                            }
                        }
                    }
                }
                // IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%Y-%m-%d %H:%i:%s'), t.created_at ) as datetime,
                // IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%W, %M %e %Y, %h:%i %p'), DATE_FORMAT(t.created_at, '%W, %M %e %Y, %h:%i %p') ) as datetime_format,
                $query = "
                            SELECT 
                                t.ticket_id as tID,
                                t.ticket_no as ticket,
                                t.plan_atp_id,
                                t.created_at as datetime,
                                DATE_FORMAT(t.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format,
                                CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
                                t.invited_farmer_count,
                                d.data_source,
                                s.state,
                                sd.district,
                                sdt.tehsil,
                                t.village,
                                dr.demo_code as demo_request_id,
                                p1.product  AS product_1,
                                p2.product  AS product_2,
                                p3.product  AS product_3,
                                c1.crop     AS crop_1,
                                c2.crop     AS crop_2,
                                c3.crop     AS crop_3,
                                t.expected_farmers,
                                t.expected_pda,
                                t.sp_name_1,
                                t.sp_number_1,
                                t.sp_name_2,
                                t.sp_number_2,
                                t.sp_name_3,
                                t.sp_number_3,
                                t.sp_name_4,
                                t.sp_number_4,
                                t.sp_name_5,
                                t.sp_number_5,
                                t.sp_name_6,
                                t.sp_number_6,
                                t.language_id,
                                t.user_farmer,
                                lan.language,
                                t.user_id,
                                t.user_type,
                                dd.name as dd_name,
                                t.assign_to_dd as dd_id,
                                t.latitude,
                                t.longitude,
                                '" . $base_url . "/' as base_url,
                                t.uploaded_file, 
                                (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status,
                                (CASE WHEN matp.plan_atp_id != 'null' THEN 'my plan' ELSE '' END) as my_plan,
                                '0' as selfParticipation,
                                t.dd_ticket_role
                            FROM tickets t
                            LEFT JOIN data_sources  d   ON t.data_source_id=d.data_source_id
                            LEFT JOIN plan_atp  pat   ON pat.plan_atp_id=t.plan_atp_id
                            LEFT JOIN languages  lan   ON lan.id=t.language_id
                            LEFT JOIN states        s   ON t.state_id=s.state_id
                            LEFT JOIN districts     sd  ON t.district_id=sd.district_id
                            LEFT JOIN tehsils       sdt ON t.tehsil_id=sdt.tehsil_id
                            LEFT JOIN products      p1  ON t.product_1_id=p1.product_id
                            LEFT JOIN products      p2  ON t.product_2_id=p2.product_id
                            LEFT JOIN products      p3  ON t.product_3_id=p3.product_id
                            LEFT JOIN demo_request      dr  ON t.demo_request_id=dr.demo_request_id
                            LEFT JOIN crops         c1  ON t.crop_1_id=c1.crop_id
                            LEFT JOIN crops         c2  ON t.crop_2_id=c2.crop_id
                            LEFT JOIN crops         c3  ON t.crop_3_id=c3.crop_id
                            LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=t.assign_to_dd
                            LEFT JOIN my_atp  matp   ON t.plan_atp_id=matp.plan_atp_id
                            LEFT JOIN self_participations sp ON t.ticket_id=sp.ticket_id
                            WHERE t.status = '1' AND t.ticket_status = '1' ";
                            
                            if($request->has('from_date') && $request->has('to_date')){
                                $from_date=$request->input('from_date');
                                $to_date=$request->input('to_date');
                                $query .=" AND `t`.`created_at` Between '".$from_date." 00:00:00' and '".$to_date." 23:59:59'";
                            }
                            else
                            {   
                                $query .=" AND  t.created_at <= '".date('Y-m-d')." 23:59:00'";
                            }

                            if ($request->has('rsm') || $request->has('asm') || $request->has('bdo_id') || $request->has('dd_id')) 
                            {
                                if ($request->has('rsm') && !$request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                                {
                                    $query .=" AND t.ticket_id in  (SELECT ticket_id FROM tickets where user_id = '".$request->input('rsm')."' and user_type = 'sales_regional' AND status = '1' AND ticket_status = '1')";
                                }elseif ($request->has('rsm') && $request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                                {   
                                    $query .=" AND t.ticket_id in  (SELECT ticket_id FROM tickets where user_id = '".$request->input('asm')."' and user_type = 'sales_area' AND status = '1' AND ticket_status = '1')";
                                }
                                elseif (!$request->has('rsm') && $request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                                {   
                                    $query .=" AND t.ticket_id in  (SELECT ticket_id FROM tickets where user_id = '".$request->input('asm')."' and user_type = 'sales_area' AND status = '1' AND ticket_status = '1')";
                                }
                                elseif ($request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {
                                    $query .=" AND t.ticket_id in  (SELECT ticket_id FROM tickets where user_id = '".$request->input('bdo_id')."' and user_type = 'bdo' AND status = '1' AND ticket_status = '1')";
                                }
                                elseif (!$request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {
                                    $query .=" AND t.ticket_id in  (SELECT ticket_id FROM tickets where assign_to_dd = '".$request->input('dd_id')."' AND status = '1' AND ticket_status = '1')";
                                }
                                elseif (!$request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {
                                    $query .=" AND t.ticket_id in  (SELECT ticket_id FROM tickets where user_id = '".$request->input('bdo_id')."' and user_type = 'bdo' AND status = '1' AND ticket_status = '1')";
                                }
                                elseif (!$request->has('rsm') && !$request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {
                                    $query .=" AND t.ticket_id in  (SELECT ticket_id FROM tickets where user_id = '".$request->input('bdo_id')."' and user_type = 'bdo' AND status = '1' AND ticket_status = '1')";
                                }
                                elseif (!$request->has('rsm') && !$request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {
                                    $query .=" AND t.ticket_id in  (SELECT ticket_id FROM tickets where assign_to_dd = '".$request->input('dd_id')."' AND status = '1' AND ticket_status = '1')";
                                }
                                elseif (!$request->has('rsm') && $request->has('asm') && !$request->has('bdo_id') && $request->has('dd_id')) {
                                    $query .=" AND t.ticket_id in  (SELECT ticket_id FROM tickets where assign_to_dd = '".$request->input('dd_id')."' AND status = '1' AND ticket_status = '1')";
                                }
                                elseif ($request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {  
                                    $query .=" AND t.ticket_id in  (SELECT ticket_id FROM tickets where assign_to_dd = '".$request->input('dd_id')."' AND status = '1' AND ticket_status = '1')";
                                }
                                elseif (!$request->has('rsm') && !$request->has('asm') && !$request->has('bdo_id') && $request->has('dd_id')) {  
                                    $query .=" AND t.ticket_id in  (SELECT ticket_id FROM tickets where assign_to_dd = '".$request->input('dd_id')."' AND status = '1' AND ticket_status = '1')";
                                } 
                            }
                            else
                            {   
                                $query .= " AND t.user_id = '".$user_id."' AND t.user_type = '".$user_type."' ";
                                if (!empty($users) && isset($users)) {
                                    $query .=" OR t.ticket_id in  (SELECT ticket_id FROM tickets where user_id in ('".$users."') and user_type = 'bdo' and status = '1' AND ticket_status = '1')";
                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query .=" AND `t`.`created_at` Between '".$from_date." 00:00:00' and '".$to_date." 23:59:59'";
                                    }
                                    else
                                    {   
                                        $query .=" AND  t.created_at <= '".date('Y-m-d')." 23:59:00'";
                                    }
                                }
                                
                                if (!empty($dd_ids) && isset($dd_ids)) { 
                                    $query .=" OR t.ticket_id in  (SELECT ticket_id FROM tickets where assign_to_dd in ('".$dd_ids."') and status = '1' AND ticket_status = '1')";
                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query .=" AND `t`.`created_at` Between '".$from_date." 00:00:00' and '".$to_date." 23:59:59'";
                                    }
                                    else
                                    {   
                                        $query .=" AND  t.created_at <= '".date('Y-m-d')." 23:59:00'";
                                    }
                                }
                            }

                            if ($request->has('product')) {
                                $product=$request->input('product');
                                $query .=" AND (`t`.`product_1_id` = '".$product."' OR `t`.`product_2_id` = '".$product."' OR `t`.`product_3_id` = '".$product."')";
                            }

                            if ($request->has('crop')) {
                                $crop=$request->input('crop');
                                $query .=" AND (`t`.`crop_1_id` = '".$crop."' OR `t`.`crop_2_id` = '".$crop."' OR `t`.`crop_3_id` = '".$crop."')";
                            }

                            $query .=" GROUP BY  
                                t.ticket_id
                            ORDER BY
                                t.ticket_id DESC";
                        //echo $query;die;


                    $ticket_history = DB::select(DB::raw($query));
                    $result = array_map(function ($value) {
                        return (array) $value;
                    }, $ticket_history);

                    // return $ticket_history;
                    
                    if ($ticket_history) {
                        // DB::enableQueryLog();
                        foreach ($result as $key => $value) {
                            $self_participation = SelfParticipation::where('user_id',$user_id)->where('user_type',$user_type)->where('ticket_id',$result[$key]['tID'])->get();
                            if(count($self_participation)>0) {
                                $result[$key]['selfParticipation'] = 1;
                            }
                            /*$querylog = DB::getQueryLog();
                            Log::info($querylog);*/
                            

                            if (empty($result[$key]['longitude']) || is_null($result[$key]['longitude']) ) {
                                $result[$key]['longitude'] = 0;
                            }

                            if (empty($result[$key]['latitude']) || is_null($result[$key]['latitude']) ) {
                                $result[$key]['latitude'] = 0;
                            }

                            if (!empty($result[$key]['feedbackImage'])) {
                                $result[$key]['feedbackImage'] = $base_url . '/assets/self_participation/' . $result[$key]['feedbackImage'];
                            }
                        }
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Active Tickets Found!',
                                    'data' => $result,
                        ]);
                    } else {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'No Record Found!',
                                    'data' => [],
                        ]);
                        //return $this->respondNotFound("No Record Found!");
                    }
                
        
            }else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function SupervisorTicketHistory(Request $request) {
        $rules = array(
            'apiToken' => 'required|string'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";

                $default_from_date = date("Y-m-d", strtotime("45 days ago"));
                $default_to_date = date("Y-m-d");

                $query = " SELECT
                                t.ticket_id as tID,
                                t.ticket_no as ticket,
                                t.plan_atp_id,
                                IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%Y-%m-%d %H:%i:%s'), t.created_at ) as datetime,
                                IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%W, %M %e %Y, %h:%i %p'), DATE_FORMAT(t.created_at, '%W, %M %e %Y, %h:%i %p') ) as datetime_format,
                                CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
                                t.invited_farmer_count,
                                d.data_source,
                                s.state,
                                sd.district,
                                sdt.tehsil,
                                t.village,
                                dr.demo_code as demo_request_id,
                                p1.product  AS product_1,
                                p2.product  AS product_2,
                                p3.product  AS product_3,
                                c1.crop     AS crop_1,
                                c2.crop     AS crop_2,
                                c3.crop     AS crop_3,
                                t.expected_farmers,
                                t.expected_pda,
                                t.sp_name_1,
                                t.sp_number_1,
                                t.sp_name_2,
                                t.sp_number_2,
                                t.sp_name_3,
                                t.sp_number_3,
                                t.sp_name_4,
                                t.sp_number_4,
                                t.sp_name_5,
                                t.sp_number_5,
                                t.sp_name_6,
                                t.sp_number_6,
                                t.language_id,
                                t.user_farmer,
                                lan.language,
                                t.user_id,
                                t.user_type,
                                dd.name as dd_name,
                                t.assign_to_dd as dd_id,
                                t.latitude,
                                t.longitude,
                                '" . $base_url . "/' as base_url,
                                t.uploaded_file, 
                                t.uploadedByType,
                                (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status,
                                t.dd_ticket_role,
                                sp.uploaded_file as arival_uploaded_file,
                                sp.description as arival_description,
                                sp.arival_time as arival_time,
                                sp.latitude as arival_latitude,
                                sp.longitude as arival_longitude,

                                sp.departure_time,
                                sp.departure_description,
                                sp.departure_uploaded_file,
                                sp.departure_latitude,
                                sp.departure_longitude,
                                sp.duration
                            FROM self_participations sp
                            LEFT JOIN tickets  t   ON sp.ticket_id=t.ticket_id
                            LEFT JOIN data_sources  d   ON t.data_source_id=d.data_source_id
                            LEFT JOIN plan_atp  pat   ON pat.plan_atp_id=t.plan_atp_id
                            LEFT JOIN languages  lan   ON lan.id=t.language_id

                            LEFT JOIN states        s   ON t.state_id=s.state_id
                            LEFT JOIN districts     sd  ON t.district_id=sd.district_id
                            LEFT JOIN tehsils       sdt ON t.tehsil_id=sdt.tehsil_id
                            LEFT JOIN products      p1  ON t.product_1_id=p1.product_id
                            LEFT JOIN products      p2  ON t.product_2_id=p2.product_id
                            LEFT JOIN products      p3  ON t.product_3_id=p3.product_id
                            LEFT JOIN demo_request  dr  ON t.demo_request_id=dr.demo_request_id
                            LEFT JOIN crops         c1  ON t.crop_1_id=c1.crop_id
                            LEFT JOIN crops         c2  ON t.crop_2_id=c2.crop_id
                            LEFT JOIN crops         c3  ON t.crop_3_id=c3.crop_id
                            LEFT JOIN dhanuka_doctor  dd   ON dd.dd_id=t.assign_to_dd
                            Where ";
                            if ($request->has('rsm') || $request->has('asm') || $request->has('bdo_id') || $request->has('dd_id')) 
                            {  
                                if ($request->has('rsm') && !$request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                                {
                                    $query .=" sp.ticket_id in  (SELECT ticket_id FROM self_participations where user_id = '".$request->input('rsm')."' and user_type = 'sales_regional')";
                                }elseif ($request->has('rsm') && $request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                                {   
                                    $query .=" sp.ticket_id in  (SELECT ticket_id FROM self_participations where user_id = '".$request->input('asm')."' and user_type = 'sales_area' )";
                                }
                                elseif (!$request->has('rsm') && $request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                                {   
                                    $query .=" sp.ticket_id in  (SELECT ticket_id FROM self_participations where user_id = '".$request->input('asm')."' and user_type = 'sales_area' )";
                                }
                                elseif ($request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {
                                    $query .=" sp.ticket_id in  (SELECT ticket_id FROM tickets where user_id = '".$request->input('bdo_id')."' and user_type = 'bdo')";
                                }
                                elseif (!$request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {
                                    $query .=" sp.ticket_id in  (SELECT ticket_id FROM tickets where assign_to_dd = '".$request->input('dd_id')."')";
                                }
                                elseif (!$request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {
                                    $query .=" sp.ticket_id in  (SELECT ticket_id FROM tickets where user_id = '".$request->input('bdo_id')."' and user_type = 'bdo')";
                                }
                                elseif (!$request->has('rsm') && !$request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {
                                    $query .=" sp.ticket_id in  (SELECT ticket_id FROM tickets where user_id = '".$request->input('bdo_id')."' and user_type = 'bdo')";
                                }
                                elseif (!$request->has('rsm') && !$request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {
                                    $query .=" sp.ticket_id in  (SELECT ticket_id FROM tickets where assign_to_dd = '".$request->input('dd_id')."')";
                                }
                                elseif (!$request->has('rsm') && $request->has('asm') && !$request->has('bdo_id') && $request->has('dd_id')) {
                                    $query .=" sp.ticket_id in  (SELECT ticket_id FROM tickets where assign_to_dd = '".$request->input('dd_id')."')";
                                }
                                elseif ($request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {  
                                    $query .=" sp.ticket_id in  (SELECT ticket_id FROM tickets where assign_to_dd = '".$request->input('dd_id')."')";
                                }
                                elseif (!$request->has('rsm') && !$request->has('asm') && !$request->has('bdo_id') && $request->has('dd_id')) {  
                                    $query .=" sp.ticket_id in  (SELECT ticket_id FROM tickets where assign_to_dd = '".$request->input('dd_id')."')";
                                } 
                            } else {
                                $query .=" sp.user_id = '".$user_id."' AND sp.user_type = '".$user_type."' ";
                            }
                            if($request->has('from_date') && $request->has('to_date')){ 
                                $from_date=$request->input('from_date');
                                $to_date=$request->input('to_date');
                                $query .=" AND `sp`.`created_at` BETWEEN '".$from_date." 00:00:00 ' and '".$to_date." 23:59:59 '";
                            } else {
                                $query .=" AND `sp`.`created_at` BETWEEN '".$default_from_date." 00:00:00 ' and '".$default_to_date." 23:59:59 '";
                            }
                            if ($request->has('product')) {
                                $product=$request->input('product');
                                $query .=" AND (`t`.`product_1_id` = '".$product."' OR `t`.`product_2_id` = '".$product."' OR `t`.`product_3_id` = '".$product."')";
                            }
                            if ($request->has('crop')) {
                                $crop=$request->input('crop');
                                $query .=" AND (`t`.`crop_1_id` = '".$crop."' OR `t`.`crop_2_id` = '".$crop."' OR `t`.`crop_3_id` = '".$crop."')";
                            }
                            $query .=" GROUP BY  
                                t.ticket_id
                            ORDER BY
                                FIELD(ticket_status, '1') DESC, t.ticket_id DESC
                            ";
                // return $query;
                $ticket_history = DB::select(DB::raw($query));
                $result = array_map(function ($value) {
                    return (array) $value;
                }, $ticket_history);
                if ($ticket_history) {
                    foreach ($result as $key => $value) {
                        if (empty($result[$key]['longitude']) || is_null($result[$key]['longitude']) ) {
                            $result[$key]['longitude'] = 0;
                        }
                        if (empty($result[$key]['latitude']) || is_null($result[$key]['latitude']) ) {
                            $result[$key]['latitude'] = 0;
                        }
                        if (!empty($result[$key]['feedbackImage'])) {
                            $result[$key]['feedbackImage'] = $base_url . '/assets/self_participation/' . $result[$key]['feedbackImage'];
                        }
                        $heightLightCount = DB::table('ticket_highlights')
                            ->where('ticket_id',$value['tID'])
                            ->select('*')
                            ->count();
                        $result[$key]['heightLightCount'] = $heightLightCount;
                    }
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Ticket History Found!',
                        'data' => $result,
                    ]);
                } else {
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'No Record Found!',
                        'data' => [],
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function s1updateticketimagebytktno(Request $request) {
        $data = $request->all();
        $rules = array(
            'api_token' => 'required|string',
            'ticket_no' => 'required|string',
            'photo' => 'required|max:300000|mimes:jpg,png,jpeg',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['api_token'];
            $ticket_no = $data['ticket_no'];
            $file = $request->file('photo');
            $latitude = (isset($data['latitude']) ? $data['latitude'] : '');
            $longitude = (isset($data['longitude']) ? $data['longitude'] : '');
            $updated_at = (isset($data['updated_at'])?$data['updated_at']:'');

            if ($request->hasFile('photo')) {
                if ($request->file('photo')->isValid()) {
                    $user = $this->findUser($api_token);
                    if ($user && count($user) == '1') {
                        $user_id = $user[0]->id;
                        $user_type = $user[0]->user_type;
                        $ticket_check = Ticket::where('ticket_no', $ticket_no)->first();
                        if ($ticket_check) {
                            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $fileName = "F_" . $ticket_check->ticket_no . "_" . md5(time() . rand()) . str_replace($file->getClientOriginalName(), '', $file->getClientOriginalName()) . '.' . $extension;
                            $sTempFileName = 'assets/tickets/' . $fileName;
                            Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            //
                            $userType1 = "";
                            if ($user_type == 'bdo') {
                                $getUserDesignation = DB::table('users')
                                                      ->where('id',$user_id)
                                                      ->where('status','1')
                                                      ->select('designation')
                                                      ->get();
                                $userType1 = $getUserDesignation[0]->designation;

                            } elseif ($user_type == 'dhanuka_doctor') {
                                $getDdDesignation = DB::table('dhanuka_doctor')
                                                      ->where('dd_id',$user_id)
                                                      ->where('status','1')
                                                      ->select('designation')
                                                      ->get();
                                $userType1 = $getDdDesignation[0]->designation;
                            } elseif ($user_type == 'sales_regional') {
                                $userType1 = 'RSM';
                            } elseif ($user_type == 'sales_area') {
                                $userType1 = 'ASM';
                            } elseif ($user_type == 'marketing_supervisor') {
                                $userType1 = 'Marketing Supervisor';
                            } else {
                                $userType1 = $user_type;
                            }
                            $checkTicketUpload = DB::table('tickets')
                                                 ->where('ticket_no','=',$ticket_no)
                                                 ->where('uploadedByType','=','')
                                                 ->get();
                            if (count($checkTicketUpload) > 0) {
                                $village_id   = $ticket_check->village_id;
                                // echo "=>".$village_id."-=>".$latitude."-=>".$longitude; exit();
                                if($village_id && $latitude && $longitude) {
                                    // echo "Called-1"; exit();
                                    $distanceData = $this->CalculateDistance($village_id,$latitude,$longitude);
                                    if($distanceData!=0) {
                                        // echo "Called-3"; exit();
                                        $expDistanceData = explode("~", $distanceData);
                                        $distance = $expDistanceData[0];
                                        $is_valid = $expDistanceData[1];
                                        $radius   = $expDistanceData[2];
                                    } else {
                                        // echo "Called-4"; exit();
                                        $distance = 0;
                                        $radius   = 0;
                                        $is_valid = 0;
                                    }
                                } else {
                                    // echo "Called-2"; exit();
                                    $distance = 0;
                                    $radius   = 0;
                                    $is_valid = 0;
                                }
                                $updateImage = Ticket::where('ticket_no', $ticket_no)->update([
                                    'uploaded_file' => $sTempFileName,
                                    'image_upload_type' => '1',
                                    'latitude'      => $latitude,
                                    'longitude'     => $longitude,
                                    'distance'      => $distance,
                                    'is_valid'      => $is_valid,
                                    'radius'        => $radius,
                                    'uploadedBy'    => $user_id,
                                    'uploadedByType'=> $userType1,
                                    'updated_at'    => $updated_at
                                ]);
                                if ($updateImage) {
                                    //$base_url = url('/');
                                    $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                                    $imageUrl = $base_url . '/' . $sTempFileName;
                                    $data = array('url' => $imageUrl);
                                    return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'File Successfully Uploaded!',
                                        'data' => $data
                                    ]);
                                } else {
                                    return $this->respondInternalError("Failed to upload file!");
                                }
                            } else {
                                return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'File Already Uploaded!',
                                    'data' => ""
                                ]);
                            }                            
                        } else {
                            return $this->respondNotFound("Invalid Ticket !!");
                        }
                    } else {
                        return $this->respondWithError("Invalid Attempt !!");
                    }
                } else {
                    return $this->respondWithError("Invalid File !!");
                }
            } else {
                return $this->respondWithError("File Not Found !!");
            }
        }
    }

    public function NewAtpFeedbackTicket(Request $request) {
        try {
            $data = $request->all();//print_r($data);
            //echo "<pre>";print_r($data); die;
            //print_r($request->file('feedbackImage'));die;
            $rules = array(
                'ticketData' => 'required',
                'ticket_no' => 'required'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $ticket_no = $data['ticket_no'];
                $ticket_check = Ticket::where('ticket_no', $ticket_no)->first();
                if ($ticket_check) {
                    $ticket_id = $ticket_check->ticket_id;
                    $feedbackImage = [];
                    if ($request->hasFile('feedbackImage'))  {
                        $image = $request->file('feedbackImage');
                        for ($i=0; $i < count($image) ; $i++) {
                            $file = $request->file('feedbackImage')[$i];
                            // $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                            // $saveFile = $file->move("assets/atpfeedbackimage",$fileName);
                            //s3Upload
                            //$fileName = $file->getClientOriginalName();
                            $fileName = time().rand(111,999).".".$file->getClientOriginalExtension();
                            $sTempFileName = 'assets/atpfeedbackimage/' . $fileName;
                            Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            //
                            //$destinationPath = public_path('/assets/atpfeedbackimage');
                            //$saveFile = $file->move($destinationPath, $fileName);
                            $feedbackImage[] = $fileName;
                        }
                    }
                    $ticketData1 = json_decode($data['ticketData'],true);
                    $ticketData = json_decode($data['ticketData'],true);
                    foreach ($ticketData as $key => $value) {
                        $api_token = $ticketData[$key]['apiToken'];
                        $user = $this->findUser($api_token);
                        if ($user && count($user) == '1') {
                            $user_id = $user[0]->id;
                            $user_type = $user[0]->user_type;
                            $currentDat = date('Y-m-d H:i:s');
                            if(isset($ticketData[$key]['created_at']) && !empty($ticketData[$key]['created_at'])){
                                $currentDat = $ticketData[$key]['created_at'];
                            } else{
                                $currentDat = date('Y-m-d H:i:s');
                            }
                            $feedbackImagekey = (count($feedbackImage)>0) ? $feedbackImage[$key] : "";
                            if ($user_type == 'bdo') {
                                $atpfeedback = DB::table('atpfeedback')->insert(
                                                ['ticket_id' => $ticket_id,'user_id' => $user_id, 'user_type' => $user_type, 'description' => ($ticketData[$key]['description'])?$ticketData[$key]['description']:"", 'feedbackImage' => $feedbackImagekey, 'creation_at' => $currentDat]
                                                );
                            } else {
                                //date('Y-m-d H:i:s')
                                $atpfeedback = DB::table('atpfeedback')->insert(
                                                ['ticket_id' => $ticket_id,'user_id' => $user_id, 'user_type' => $user_type, 'description' => ($ticketData[$key]['description'])?$ticketData[$key]['description']:"", 'feedbackImage' => $feedbackImagekey, 'creation_at' => $currentDat]
                                                );
                                $ticketcheckdata = DB::table('tickets')
                                                   ->where('ticket_id', $ticket_id)
                                                   ->select('ticket_no','plan_atp_id')
                                                   ->get();
                                if (count($ticketcheckdata) > 0) {
                                    if ($ticketcheckdata[0]->plan_atp_id != 0) {
                                        $plandata = DB::table('plan_atp')
                                                    ->where('plan_atp_id',$ticketcheckdata[0]->plan_atp_id)
                                                    ->select('plan_atp_id', DB::raw('DATE_FORMAT(plan_date, "%Y-%m-%d") as Cplan_date'))
                                                    ->get();
                                        $description = ($ticketData[$key]['description'])?$ticketData[$key]['description']:"";
                                        if (count($plandata) > 0) {
                                            $insert_self_participation_plan = DB::table('self_participation_plans')->insert(['plan_atp_id' => $ticketcheckdata[0]->plan_atp_id,'user_id' => $user_id, 'user_type' => $user_type,'plan_date' => $plandata[0]->Cplan_date, 'reason' => $description, 'created_at' => $currentDat]);

                                            $latitude = ($ticketData[$key]['latitude'])?$ticketData[$key]['latitude']:"";
                                            $longitude = ($ticketData[$key]['longitude'])?$ticketData[$key]['longitude']:"";
                                            $feedbackImageData = ($feedbackImage[$key])? 'assets/atpfeedbackimage/' . $feedbackImage[$key] :"";
                                            $insert_self_participation = DB::table('self_participations')->insert(
                                            ['ticket_id' => $ticket_id,'user_id' => $user_id, 'user_type' => $user_type, 'description' => $description, 'uploaded_file' => $feedbackImageData,'latitude' => $latitude, 'longitude' => $longitude, 'created_at' => $currentDat ]);
                                        }
                                    } else {
                                        $latitude = ($ticketData[$key]['latitude'])?$ticketData[$key]['latitude']:"";
                                        $longitude = ($ticketData[$key]['longitude'])?$ticketData[$key]['longitude']:"";
                                        $feedbackImageData = ($feedbackImage[$key])? 'assets/atpfeedbackimage/' . $feedbackImage[$key] :"";
                                        $description = ($ticketData[$key]['description'])?$ticketData[$key]['description']:"";
                                        $insert_self_participation = DB::table('self_participations')->insert(
                                            ['ticket_id' => $ticket_id,'user_id' => $user_id, 'user_type' => $user_type, 'description' => $description, 'uploaded_file' => $feedbackImageData,'latitude' => $latitude, 'longitude' => $longitude, 'created_at' => $currentDat ]
                                        );
                                    }
                                }
                            }
                        } else {
                            return $this->respondWithConflict("Invalid Attempt!");
                        }
                    }
                    if ($atpfeedback > 0) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Feedback submitted successfully.'
                                ]);
                    } else {
                        return $this->respondNotFound("Something went wrong. Please Try Again!!");
                    }
                } else {
                    return $this->respondNotFound("Invalid Ticket !!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function getreportType(Request $request) {
        try{
            $crr_date = date('Y-m-d H:i:s');
            $reportType = DB::table('report_type')->where('status','=','1')->get();
            return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Report Type List Found!',
                        'crr_date' => $crr_date,
                        'base_url' => url('/').'/',
                        'data' => $reportType
                    ]);
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function getreportsList(Request $request) {
        $data = $request->all();
        $rules = array(
            'api_token' => 'required|string',
            'report_type' => 'required|numeric',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['api_token'];
            $user = $this->findUser($api_token);
            $crr_date = date('Y-m-d H:i:s');
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $report_type = $data['report_type'];

                $addQry = " WHERE 1=1 and report_type_id='".$report_type."'";
                if(!empty($data['year'])) {
                    $year = $data['year'];
                    $addQry .= " and year='".$year."'";
                }
                if(!empty($data['month'])) {
                    $month = $data['month'];
                    $addQry .= " and month='".$month."'";
                }
                if(!empty($data['updated_at'])) {
                    $updated_at = $data['updated_at'];
                    $addQry .= " and updated_at >= '".$updated_at."'";
                }
                if($user_type == 'zonal') {
                    $addQry .= " and zonal_manager_id= '".$user_id."'";
                }
                if($user_type == 'regional') {
                    $regional_list = RegionalManager::where('status', '1')->where('regional_manager_id',$user_id)->select('regional_manager_id', 'zonal_manager_id')->orderBy('regional_manager_name', 'asc')->get();
                    $zonal_manager_id = $regional_list[0]->zonal_manager_id;
                    $addQry .= " AND (
                                    zonal_manager_id = '".$zonal_manager_id."' AND
                                    CASE WHEN regional_manager_id IS NOT NULL 
                                        THEN regional_manager_id LIKE '%".$user_id."%'
                                        ELSE TRUE
                                    END
                                )";
                }
                if($user_type == 'sales_regional') {
                    $sales_regional_list = SalesRegionalManager::where('status', '1')->where('sales_regional_manager_id', $user_id)->select('sales_regional_manager_id', 'zonal_manager_id')->orderBy('sales_regional_manager_name', 'asc')->get();
                    $zonal_manager_id = $sales_regional_list[0]->zonal_manager_id;
                    $addQry .= " AND (
                                    zonal_manager_id = '".$zonal_manager_id."' AND
                                    CASE WHEN sales_regional_manager_id IS NOT NULL 
                                        THEN sales_regional_manager_id LIKE '%".$user_id."%'
                                        ELSE TRUE
                                    END
                                )";
                }
                if($user_type == 'area') {
                    $area_manager_list = AreaManager::where('status', '1')->where('area_manager_id', $user_id)->select('zonal_manager_id','regional_manager_id','sales_regional_manager_id','area_manager_id')->orderBy('area_manager_name', 'asc')->get();
                    $zonal_manager_id   = $area_manager_list[0]->zonal_manager_id;
                    $regional_manager_id = $area_manager_list[0]->regional_manager_id;
                    $sales_regional_manager_id = $area_manager_list[0]->sales_regional_manager_id;
                    $addQry .= " AND (
                                    zonal_manager_id = '".$zonal_manager_id."' AND
                                    CASE WHEN regional_manager_id IS NOT NULL 
                                        THEN regional_manager_id LIKE '%".$regional_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN sales_regional_manager_id IS NOT NULL 
                                        THEN sales_regional_manager_id LIKE '%".$sales_regional_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN area_manager_id IS NOT NULL 
                                        THEN area_manager_id LIKE '%".$user_id."%'
                                        ELSE TRUE
                                    END
                                )";
                }
                if($user_type == 'sales_area') {
                    $sales_area_manager_list = SalesAreaManager::where('status', '1')->where('sales_area_manager_id', $user_id)->select('zonal_manager_id','regional_manager_id','sales_regional_manager_id','sales_area_manager_id')->orderBy('sales_area_manager_name', 'asc')->get();
                    $zonal_manager_id   = $sales_area_manager_list[0]->zonal_manager_id;
                    $regional_manager_id = $sales_area_manager_list[0]->regional_manager_id;
                    $sales_regional_manager_id = $sales_area_manager_list[0]->sales_regional_manager_id;
                    $addQry .= " AND (
                                    zonal_manager_id = '".$zonal_manager_id."' AND
                                    CASE WHEN regional_manager_id IS NOT NULL 
                                        THEN regional_manager_id LIKE '%".$regional_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN sales_regional_manager_id IS NOT NULL 
                                        THEN sales_regional_manager_id LIKE '%".$sales_regional_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN sales_area_manager_id IS NOT NULL 
                                        THEN sales_area_manager_id LIKE '%".$user_id."%'
                                        ELSE TRUE
                                    END
                                )";
                }
                if($user_type == 'bdo') {
                    $user_list = User::where('status', '1')->where('id', $user_id)->select('zonal_manager_id','regional_manager_id','sales_regional_manager_id','area_manager_id','sales_area_manager_id')->orderBy('name', 'asc')->get();
                    $zonal_manager_id   = $user_list[0]->zonal_manager_id;
                    $regional_manager_id = $user_list[0]->regional_manager_id;
                    $sales_regional_manager_id = $user_list[0]->sales_regional_manager_id;
                    $area_manager_id = $user_list[0]->area_manager_id;
                    $sales_area_manager_id = $user_list[0]->sales_area_manager_id;
                    $addQry .= " AND (
                                    zonal_manager_id IN ('".$zonal_manager_id."') AND
                                    CASE WHEN regional_manager_id IS NOT NULL 
                                        THEN regional_manager_id LIKE '%".$regional_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN sales_regional_manager_id IS NOT NULL 
                                        THEN sales_regional_manager_id LIKE '%".$sales_regional_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN area_manager_id IS NOT NULL 
                                        THEN area_manager_id LIKE '%".$area_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN sales_area_manager_id IS NOT NULL 
                                        THEN sales_area_manager_id LIKE '%".$sales_area_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN user_id IS NOT NULL 
                                        THEN user_id LIKE '%".$user_id."%'
                                        ELSE TRUE
                                    END
                                )";
                }
                if($user_type == 'dhanuka_doctor') {
                    $sales_area_manager_list = DhanukaDoctor::where('status', '1')->where('dd_id', $user_id)->select('zonal_manager_id','regional_manager_id','sales_regional_manager_id','area_manager_id','sales_area_manager_id','bdo_id')->orderBy('name', 'asc')->get();
                    $zonal_manager_id   = $sales_area_manager_list[0]->zonal_manager_id;
                    $regional_manager_id = $sales_area_manager_list[0]->regional_manager_id;
                    $sales_regional_manager_id = $sales_area_manager_list[0]->sales_regional_manager_id;
                    $area_manager_id = $sales_area_manager_list[0]->area_manager_id;
                    $sales_area_manager_id = $sales_area_manager_list[0]->sales_area_manager_id;
                    $bdo_id = $sales_area_manager_list[0]->bdo_id;
                    $addQry .= " AND (
                                    zonal_manager_id = '".$zonal_manager_id."' AND
                                    CASE WHEN regional_manager_id IS NOT NULL 
                                        THEN regional_manager_id LIKE '%".$regional_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN sales_regional_manager_id IS NOT NULL 
                                        THEN sales_regional_manager_id LIKE '%".$sales_regional_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN area_manager_id IS NOT NULL 
                                        THEN area_manager_id LIKE '%".$area_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN sales_area_manager_id IS NOT NULL 
                                        THEN sales_area_manager_id LIKE '%".$sales_area_manager_id."%'
                                        ELSE TRUE
                                    END
                                    AND
                                    CASE WHEN user_id IS NOT NULL 
                                        THEN user_id LIKE '%".$bdo_id."%'
                                        ELSE TRUE
                                    END
                                )";
                } else { }
                $query = "SELECT `report_id`,`report_type_id`,`report_name`,`year`,`month`,`uploaded_file`,`status`,`created_at`,`updated_at` FROM report_guideline $addQry ORDER BY created_at DESC";
                // return $query;
                $reportsList = DB::select(DB::raw($query));
                return $this->respond([
                        'status'      => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message'     => 'Report List Found!',
                        'crr_date'    => $crr_date,
                        'base_url'    => "https://dhanuka-app.s3.ap-south-1.amazonaws.com/",
                        'data'        => $reportsList
                    ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt !!");
            }
        }
    }

    private function categoryWiseBdoList($key = '1', $value = '1') {
        $bdo_query = " SELECT users.id AS bdo_id, users.name AS bdo_name FROM users WHERE users.status='1'    AND users." . $key . "='" . $value . "' ORDER BY users.name ASC";
        $bdo_detail = DB::select(DB::raw($bdo_query));
        $bdo_list = array_map(function ($value) {
            return (array) $value;
        }, $bdo_detail);
        return $bdo_list;
    }

    private function categoryWiseBdoListForQuery($key = '1', $value = '1') {
        $bdo_query = " SELECT GROUP_CONCAT(CONCAT('''', users.id, '''' )) AS user_id FROM users WHERE users.status='1' AND users." . $key . "='" . $value . "' LIMIT 0,1 ";
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $bdo_detail = DB::select(DB::raw($bdo_query));
        $bdo_list = array_map(function ($value) {
            return (array) $value;
        }, $bdo_detail);
        return $bdo_list;
    }

    public function DdAtpList1(Request $request) {
        $rules = array(
            'apiToken' => 'required|string'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $offset = ($request->input('offset'))?$request->input('offset'):0;
            if ($offset != 0) {
                //$offset = $offset + 1;
                $newoffset = 30 * $offset;
            }
            else
            {
                $newoffset = 0;
            }
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                $default_from_date = date("Y-m-d", strtotime("7 days ago"));
                $default_to_date = date("Y-m-d");
                 
                if ($user_type == 'bdo') {
                    $ddData = DB::table('dhanuka_doctor')
                              ->where('bdo_id',$user_id)
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

                    //check counter fl bdo
                    $bdolist = DB::table('users')
                              ->where('counterpart_fl',$user_id)
                              ->where('status','1')
                              ->select('id')
                              ->get();
                    $bdoresult = array(); 
                    if (isset($bdolist) && !empty($bdolist)) 
                    {
                        $bdolistDatass = json_decode(json_encode($bdolist),true);
                        
                        foreach ($bdolistDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $bdoresult = array_merge($bdoresult, array_flatten($value)); 
                            } 
                            else 
                            { 
                                $bdoresult[$key] = $value; 
                            } 
                        } 
                        //array_push($bdoresult, $user_id);
                    }
                    // echo "<pre>";print_r($bdoresult);die;
                    $bdotype = DB::table('users')
                              ->where('id',$user_id)
                              ->where('status','1')
                              ->select('designation')
                              ->get();

                    $atp =  DB::table('plan_atp')
                            ->join('territories','territories.territory_id','=','plan_atp.territory_id','left')
                            ->join('tehsils','tehsils.tehsil_id','=','plan_atp.tehsil_id','left')
                            ->join('districts','districts.district_id','=','plan_atp.district_id','left')
                            ->join('states','states.state_id','=','plan_atp.state_id','left')
                            ->join('products','products.product_id','=','plan_atp.product_id','left')
                            ->join('crops','crops.crop_id','=','plan_atp.crop_id','left')
                            ->join('tickets','tickets.plan_atp_id','=','plan_atp.plan_atp_id','left')
                            ->join('languages','languages.id','=','plan_atp.language_id','left')
                            ->join('data_sources','data_sources.data_source_id','=','plan_atp.data_source_id','left')
                            ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','plan_atp.dd_id','left');
                        if ($request->has('dd_ids')) {
                            $dd_ids=explode(",",$request->input('dd_ids'));
                            $atp->whereIn('plan_atp.dd_id',$dd_ids);
                        }
                        else
                        {
                            // if (count($ddresult) > 0) {
                            //     $atp->whereIn('plan_atp.plan_atp_id',function($query) use ($ddresult,$request)
                            //     {
                            //         $query->select('plan_atp_id')->from('plan_atp')->whereIn('dd_id',$ddresult);
                            //         if($request->has('from_date') && $request->has('to_date')){
                            //             $from_date=$request->input('from_date');
                            //             $to_date=$request->input('to_date');
                            //             if ($from_date == $to_date) 
                            //             {
                            //                 $query->where(DB::raw('DATE(plan_date)'),$from_date);
                            //             }
                            //             else
                            //             {
                            //                 $query->whereBetween(DB::raw('DATE(plan_date)'),[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            //             }
                            //         }

                            //         if ($request->has('crop')) {
                            //             $crop=$request->input('crop');
                            //             $query->Where('crop_id',$crop);
                            //         }

                            //         if ($request->has('product')) {
                            //             $product=$request->input('product');
                            //             $query->Where('product_id',$product);
                            //         }
                            //     });
                            // }

                            if (count($bdoresult) > 0) {
                                $atp->whereIn('plan_atp.plan_atp_id',function($query) use ($bdoresult,$request,$bdotype,$default_from_date)
                                {   
                                    $query->select('plan_atp_id')->from('plan_atp')->whereIn('created_by_user_id',$bdoresult)->where('user_type','bdo');
                                    
                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query->whereBetween(DB::raw('DATE(plan_date)'),[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                    } else {
                                        $query->whereDate('plan_date', '>', $default_from_date." 00:00:00");
                                    }

                                    if ($request->has('crop')) {
                                        $crop=$request->input('crop');
                                        $query->Where('plan_atp.crop_id',$crop);
                                        //$query->orWhere('other_crop_1',$crop);
                                        //$query->orWhere('other_crop_2',$crop);
                                    }

                                    if ($request->has('product')) {
                                        $product=$request->input('product');
                                        $query->Where('plan_atp.product_id',$product);
                                        // $query->orWhere('product_id_2',$product);
                                        //$query->orWhere('product_id_3',$product);
                                    }


                                    if ($bdotype[0]->designation == 'COUNTERPART-FL') { 
                                        $query->Where('plan_atp.forwhom','=','2');
                                    }
                                });
                            }
                            
                            if (count($ddresult) == 0 && count($bdoresult) == 0) {
                                $atp->where(['plan_atp.created_by_user_id' => $user_id, 'plan_atp.user_type' => $user_type]);

                            }
                            else
                            {   
                                $atp->orWhere(['plan_atp.created_by_user_id' => $user_id, 'plan_atp.user_type' => $user_type]);


                                if ($bdotype[0]->designation == 'COUNTERPART-FL') { 
                                    $atp->Where('plan_atp.forwhom','=','2');
                                }
                            }
                        }
                        
                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            $atp->whereBetween(DB::raw('DATE(plan_atp.plan_date)'),[$from_date." 00:00:00", $to_date." 23:59:59"]);
                        } else {
                            // $atp->whereBetween(DB::raw('DATE(plan_atp.plan_date)'),[$default_from_date." 00:00:00", $default_to_date." 23:59:59"]);
                            $atp->whereDate('plan_atp.plan_date', '>', $default_from_date." 00:00:00");
                        }

                        if ($request->has('crop')) {
                            $crop=$request->input('crop');
                            $atp->Where('plan_atp.crop_id',$crop);
                        }

                        if ($request->has('product')) {
                            $product=$request->input('product');
                            $atp->Where('plan_atp.product_id',$product);
                        }

                        if ($bdotype[0]->designation == 'COUNTERPART-FL') { 
                            $atp->Where('plan_atp.forwhom','=','2');
                        }

                        $atp = $atp->select('plan_atp.*','states.state','tehsils.tehsil','territories.territory','districts.district','products.product','crops.crop','tickets.ticket_id','tickets.ticket_no','tickets.latitude','tickets.longitude','languages.language as language_id','data_sources.data_source as data_source_id','dhanuka_doctor.name as dd_name')
                        ->groupBy("plan_atp.plan_atp_id")
                        ->orderByRaw("plan_atp.plan_date DESC")
                        ->get(); 

                        //echo "<pre>";print_r($atp);die;
                        //->offset($offset)
                        //->limit(30)
                    if ($atp) {
                        foreach ($atp as $key => $value) {
                            if (empty($atp[$key]->longitude) || is_null($atp[$key]->longitude) ) {
                                $atp[$key]->longitude = 0;
                            }

                            if (empty($atp[$key]->latitude) || is_null($atp[$key]->latitude) ) {
                                $atp[$key]->latitude = 0;
                            }
                            $invitedFarmerlist = DB::table('invite_farmer')
                                                ->where('plan_atp_id', $atp[$key]->plan_atp_id)
                                                ->select('*')
                                                ->get();
                            if ($invitedFarmerlist) {
                                $atp[$key]->invitedFarmerlist = $invitedFarmerlist;
                            }
                            else
                            {
                                $atp[$key]->invitedFarmerlist = [];
                            }

                            $planDate = date('Y-m-d', strtotime($atp[$key]->plan_date));
                            if (date('Y-m-d') > $planDate && $atp[$key]->reason == '' && $atp[$key]->status == '0')
                            {
                                $atp[$key]->status = "3";
                            }
                        }
                    }
                }
                elseif ($user_type == 'dhanuka_doctor') 
                {
                    //dhanuka doctor atp list 
                    $atp =  DB::table('plan_atp')
                            ->join('territories','territories.territory_id','=','plan_atp.territory_id','left')
                            ->join('tehsils','tehsils.tehsil_id','=','plan_atp.tehsil_id','left')
                            ->join('districts','districts.district_id','=','plan_atp.district_id','left')
                            ->join('states','states.state_id','=','plan_atp.state_id','left')
                            ->join('products','products.product_id','=','plan_atp.product_id','left')
                            ->join('crops','crops.crop_id','=','plan_atp.crop_id','left')
                            ->join('tickets','tickets.plan_atp_id','=','plan_atp.plan_atp_id','left')
                            ->join('languages','languages.id','=','plan_atp.language_id','left')
                            ->join('data_sources','data_sources.data_source_id','=','plan_atp.data_source_id','left')
                            ->where('plan_atp.dd_id',$user_id);
                            if($request->has('from_date') && $request->has('to_date')){
                                $from_date=$request->input('from_date');
                                $to_date=$request->input('to_date');
                                if ($from_date == $to_date) 
                                {
                                    $atp->where(DB::raw('DATE(plan_date)'),$from_date);
                                }
                                else
                                {
                                    $atp->whereBetween(DB::raw('DATE(plan_date)'),[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }
                            } else {
                                // $atp->whereBetween(DB::raw('DATE(plan_date)'),[$default_from_date." 00:00:00", $default_to_date." 23:59:59"]);
                                $atp->whereDate('plan_date', '>', $default_from_date." 00:00:00");
                            }

                            if ($request->has('crop')) {
                                $crop=$request->input('crop');
                                $atp->Where('plan_atp.crop_id',$crop);
                            }

                            if ($request->has('product')) {
                                $product=$request->input('product');
                                $atp->Where('plan_atp.product_id',$product);
                            }

                    $atp = $atp->select('plan_atp.*','states.state','tehsils.tehsil','districts.district','territories.territory','products.product','crops.crop','tickets.ticket_id','tickets.ticket_no','tickets.latitude','tickets.longitude','languages.language as language_id','data_sources.data_source as data_source_id')
                            ->groupBy("plan_atp.plan_atp_id")
                            ->orderByRaw("plan_atp.plan_date DESC")
                            ->get();


                    if ($atp) {
                        foreach ($atp as $key => $value) {
                            if (empty($atp[$key]->longitude) || is_null($atp[$key]->longitude) ) {
                                $atp[$key]->longitude = 0;
                            }

                            if (empty($atp[$key]->latitude) || is_null($atp[$key]->latitude) ) {
                                $atp[$key]->latitude = 0;
                            }
                            $planDate = date('Y-m-d', strtotime($atp[$key]->plan_date));
                            if (date('Y-m-d') > $planDate && $atp[$key]->reason == '' && $atp[$key]->status == '0')
                            {
                                $atp[$key]->status = "3";
                            }

                            $atp[$key]->invitedFarmerlist = [];
                        }
                    }
                }
                else
                {   
                    if ($user_type == 'zonal') {
                        //zonal manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('zonal_manager_id',$user_id)
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

                        //zonal manager bdo
                        $bdoData = DB::table('users')
                                  ->where('zonal_manager_id',$user_id)
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
                    }
                    elseif ($user_type == 'regional') {
                        //regional manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('regional_manager_id',$user_id)
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

                        //regional manager bdo
                        $bdoData = DB::table('users')
                                  ->where('regional_manager_id',$user_id)
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
                    }
                    elseif ($user_type == 'area') {
                        //area manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('area_manager_id',$user_id)
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

                        //area manager bdo
                        $bdoData = DB::table('users')
                                  ->where('area_manager_id',$user_id)
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
                    }
                    elseif ($user_type == 'sales_regional') {
                        //sales regional manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('sales_regional_manager_id',$user_id)
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
                                  ->where('sales_regional_manager_id',$user_id)
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
                    }
                    elseif ($user_type == 'sales_area') 
                    {
                        //Sales area manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('sales_area_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddresult = array(); 
                        if (isset($ddData) && !empty($ddData)) 
                        {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            $ddresult = [];
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

                        //Sales area manager bdo
                        $bdoData = DB::table('users')
                                  ->where('sales_area_manager_id',$user_id)
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
                    }
                    elseif ($user_type == 'marketing_supervisor') 
                    {
                        //Marketing Supervisor RSM LIST
                        $rsmData = DB::table('marketing_supervisor_rsm')
                                  ->where('marketing_supervisor_id',$user_id)
                                  ->select('sales_regional_manager_id')
                                  ->get();
                        $rsmresult = array(); 
                        if (isset($rsmData) && !empty($rsmData)) 
                        {   
                            //RSM LIST
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

                            if (count($rsmresult) > 0) {
                                //marketing supervisor bdo
                                $bdoData = DB::table('users')
                                          ->whereIn('sales_regional_manager_id',$rsmresult)
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
                                    //marketing supervisor dd
                                    $ddData = DB::table('dhanuka_doctor')
                                              ->whereIn('sales_regional_manager_id',$rsmresult)
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
                                }
                            }
                        }
                    }
                    // echo "<pre>";print_r($bdoresult);die;
                    //plan atp
                    if (count($bdoresult) > 0) 
                    {
                        $plan_atp_id = DB::table('plan_atp')
                                       ->whereIn('plan_atp.created_by_user_id', $bdoresult)
                                       ->where('plan_atp.user_type', 'bdo');
                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            if ($from_date == $to_date) 
                            {
                                $plan_atp_id->where(DB::raw('DATE(plan_date)'),$from_date);
                            }
                            else
                            {
                                $plan_atp_id->whereBetween(DB::raw('DATE(plan_date)'),[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }
                        } else {
                            // $plan_atp_id->whereBetween(DB::raw('DATE(plan_date)'),[$default_from_date." 00:00:00", $default_to_date." 23:59:59"]);
                            $plan_atp_id->whereDate('plan_date', '>', $default_from_date." 00:00:00");
                        }

                        if ($request->has('crop')) {
                            $crop=$request->input('crop');
                            $plan_atp_id->Where('plan_atp.crop_id',$crop);
                        }

                        if ($request->has('product')) {
                            $product=$request->input('product');
                            $plan_atp_id->Where('plan_atp.product_id',$product);
                        }

                        $plan_atp_id = $plan_atp_id->groupBy('plan_atp.plan_atp_id')->select('plan_atp.plan_atp_id')->get();
                        $planResult = array(); 
                        if (isset($plan_atp_id) && !empty($plan_atp_id)) 
                        {
                            $palnDatass = json_decode(json_encode($plan_atp_id),true);
                            
                            foreach ($palnDatass as $key => $value) { 
                                if (is_array($value)) 
                                { 
                                    $planResult = array_merge($planResult, array_flatten($value)); 
                                } 
                                else 
                                { 
                                    $planResult[$key] = $value; 
                                } 
                            } 
                        }
                        // echo "<pre>";print_r($planResult);die;
                    }
                    //echo "<pre>";print_r($planResult);die;
                    //Supervisor atp list
                    if(count($planResult)>0) {
                        $atp =  DB::table('plan_atp')
                                ->join('territories','territories.territory_id','=','plan_atp.territory_id','left')
                                ->join('tehsils','tehsils.tehsil_id','=','plan_atp.tehsil_id','left')
                                ->join('districts','districts.district_id','=','plan_atp.district_id','left')
                                ->join('states','states.state_id','=','plan_atp.state_id','left')
                                ->join('products','products.product_id','=','plan_atp.product_id','left')
                                ->join('crops','crops.crop_id','=','plan_atp.crop_id','left')
                                ->join('tickets','tickets.plan_atp_id','=','plan_atp.plan_atp_id','left')
                                ->join('languages','languages.id','=','plan_atp.language_id','left')
                                ->join('data_sources','data_sources.data_source_id','=','plan_atp.data_source_id','left')
                                ->join('users','users.id','=','plan_atp.created_by_user_id','left')
                                ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','plan_atp.dd_id','left');
                                if ($request->has('rsm') || $request->has('asm') || $request->has('bdo_id') || $request->has('dd_id')) 
                                {
                                    if ($request->has('rsm') && !$request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                                    {
                                        $rsm=$request->input('rsm');
                                        $atp->where('plan_atp.created_by_user_id',$rsm);
                                        $atp->Where('plan_atp.user_type','sales_regional');
                                    }
                                    elseif ($request->has('rsm') && $request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                                    {
                                        $asm=$request->input('asm');
                                            $atp->Where('plan_atp.created_by_user_id',$asm);
                                            $atp->Where('plan_atp.user_type','sales_area');
                                    }
                                    elseif (!$request->has('rsm') && $request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                                    {
                                        $asm=$request->input('asm');
                                            $atp->Where('plan_atp.created_by_user_id',$asm);
                                            $atp->Where('plan_atp.user_type','sales_area');
                                    }
                                    elseif ($request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {
                                        $bdo_id=$request->input('bdo_id');
                                            $atp->Where('plan_atp.created_by_user_id',$bdo_id);
                                            $atp->Where('plan_atp.user_type','bdo');
                                    }
                                    elseif (!$request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {
                                        $bdo_id=$request->input('bdo_id');
                                            $atp->Where('plan_atp.created_by_user_id',$bdo_id);
                                            $atp->Where('plan_atp.user_type','bdo');
                                    }
                                    elseif (!$request->has('rsm') && !$request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {
                                        $bdo_id=$request->input('bdo_id');
                                            $atp->Where('plan_atp.created_by_user_id',$bdo_id);
                                            $atp->Where('plan_atp.user_type','bdo');
                                    }
                                    elseif ($request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {
                                        $dd_id=$request->input('dd_id');
                                            $atp->Where('plan_atp.dd_id',$dd_id);
                                    } 
                                    elseif (!$request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {
                                        $dd_id=$request->input('dd_id');
                                            $atp->Where('plan_atp.dd_id',$dd_id);
                                    }
                                    elseif (!$request->has('rsm') && !$request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {
                                        $dd_id=$request->input('dd_id');
                                            $atp->Where('plan_atp.dd_id',$dd_id);
                                    }
                                    elseif (!$request->has('rsm') && !$request->has('asm') && !$request->has('bdo_id') && $request->has('dd_id')) {
                                        $dd_id=$request->input('dd_id');
                                            $atp->Where('plan_atp.dd_id',$dd_id);
                                    }
                                }
                                else
                                {
                                    if (count($planResult) > 0) {
                                        $atp->whereIn('plan_atp.plan_atp_id',$planResult);
                                    }
                                }

                                if($request->has('from_date') && $request->has('to_date')){
                                    $from_date=$request->input('from_date');
                                    $to_date=$request->input('to_date');
                                    if ($from_date == $to_date) 
                                    {
                                        $atp->where(DB::raw('DATE(plan_date)'),$from_date);
                                    }
                                    else
                                    {
                                        $atp->whereBetween(DB::raw('DATE(plan_date)'),[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                    }
                                } else {
                                    // $atp->whereBetween(DB::raw('DATE(plan_date)'),[$default_from_date." 00:00:00", $default_to_date." 23:59:59"]);
                                    $atp->whereDate('plan_date', '>', $default_from_date." 00:00:00");
                                }

                                if ($request->has('product')) {
                                    $product=$request->input('product');
                                    $atp->Where('plan_atp.product_id',$product);
                                }

                                if ($request->has('crop')) {
                                    $crop=$request->input('crop');
                                    $atp->Where('plan_atp.crop_id',$crop);
                                }
                                
                                //'plan_atp.*','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','tickets.ticket_id','tickets.ticket_no','tickets.latitude','tickets.longitude','languages.language as language_id','data_sources.data_source as data_source_id','dhanuka_doctor.name as dd_name','users.name as bdo_name','users.designation as bdo_designation'
                                $atp = $atp->select('plan_atp.*','states.state','tehsils.tehsil','districts.district','territories.territory','products.product','crops.crop','tickets.ticket_id','tickets.ticket_no','tickets.latitude','tickets.longitude','languages.language as language_id','data_sources.data_source as data_source_id','dhanuka_doctor.name as dd_name','users.name as bdo_name','users.designation as bdo_designation')
                                ->groupBy("plan_atp.plan_atp_id")
                                ->orderBy('plan_atp.plan_date', 'DESC')
                                //->orderBy('plan_atp.plan_date', 'DESC')
                                //->orderByRaw("plan_atp.plan_date DESC")
                                ->offset($newoffset)
                                ->limit(30)
                                ->get();
                                // echo "<pre>";print_r($atp);die;
                                //echo $atp;die;

                        if ($atp) {
                            foreach ($atp as $key => $value) {
                                if($atp[$key]->forwhom == '2'){
                                    $userData = DB::table('users')
                                                ->where('id',$atp[$key]->created_by_user_id)
                                                ->select('counterpart_fl')
                                                ->get();
                                    if (count($userData)>0) {
                                        $userData1 = DB::table('users')
                                                    ->where('id',$userData[0]->counterpart_fl)
                                                    ->select('name')
                                                    ->get();
                                        $atp[$key]->bdo_name = (count($userData1)>0) ? $userData1[0]->name : '';
                                    }
                                }

                                if (empty($atp[$key]->longitude) || is_null($atp[$key]->longitude) ) {
                                    $atp[$key]->longitude = 0;
                                }

                                if (empty($atp[$key]->latitude) || is_null($atp[$key]->latitude) ) {
                                    $atp[$key]->latitude = 0;
                                }
                                $invitedFarmerlist = DB::table('invite_farmer')
                                                    ->where('plan_atp_id', $atp[$key]->plan_atp_id)
                                                    ->select('*')
                                                    ->get();
                                if ($invitedFarmerlist) {
                                    $atp[$key]->invitedFarmerlist = $invitedFarmerlist;
                                }
                                else
                                {
                                    $atp[$key]->invitedFarmerlist = [];
                                }

                                $planDate = date('Y-m-d', strtotime($atp[$key]->plan_date));
                                if (date('Y-m-d') > $planDate && $atp[$key]->reason == '' && $atp[$key]->status == '0')
                                {
                                    $atp[$key]->status = "3";
                                }
                            }
                        }
                    } else {
                        $atp= array();
                    }
                }

                if ($atp) {
                    foreach ($atp as $key => $value) {
                        //$base_url = url('/');
                        $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                        if (!empty($atp[$key]->image)) {
                            $atp[$key]->image = $base_url . '/assets/atpimage/' . $atp[$key]->image;
                        }

                        $products = DB::table('products')
                                    ->where('product_id',$atp[$key]->product_id)
                                    ->orWhere('product_id',$atp[$key]->product_id_2)
                                    ->orWhere('product_id',$atp[$key]->product_id_3)
                                    ->select('product_id','product')
                                    ->get();

                        $crops =    DB::table('crops')
                                    ->where('crop_id',$atp[$key]->crop_id)
                                    ->orWhere('crop_id',$atp[$key]->other_crop_1)
                                    ->orWhere('crop_id',$atp[$key]->other_crop_2)
                                    ->select('crop_id','crop')
                                    ->get();

                        $crops1 =    DB::table('crops')
                                    ->where('crop_id',$atp[$key]->other_crop_1)
                                    ->select('crop_id','crop')
                                    ->get();
                        if (count($crops1)>0) {
                            $crops11 = json_decode(json_encode($crops1,true));
                        }
                        else
                        {
                            $crops11 = [];
                        }
                        $crops2 =    DB::table('crops')
                                    ->where('crop_id',$atp[$key]->other_crop_2)
                                    ->select('crop_id','crop')
                                    ->get();
                        if (count($crops1)>0) {
                            $crops22 = json_decode(json_encode($crops2,true));
                        }
                        else
                        {
                            $crops22 = [];
                        }
                        $atp[$key]->product = $products;
                        $atp[$key]->crop = array_merge($crops,$crops11,$crops22);

                        $totalFarmer = DB::table('farmers')
                                        ->where('ticket_id', $atp[$key]->ticket_id)
                                        ->count();
                        $atp[$key]->total_farmers = $totalFarmer;

                        $invitedFarmer = DB::table('invite_farmer')
                                        ->where('plan_atp_id', $atp[$key]->plan_atp_id)
                                        ->count();
                        $atp[$key]->total_invited_farmers = $invitedFarmer;
                    }
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'ATP list.',
                                'data' =>$atp
                    ]);
                }
                else
                {   
                    return $this->respond([
                                'status' => 'error',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No ATP found',
                                'data' =>[]
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }
    
    /* Demo Submenu List */
    public function demosubmenu($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $base_url = url('/');

                $data = array(
                    array(
                        'item' => 'Field Demonstration',
                        'tag_icon' => 'assets/appmenu/demo_req.png',
                        'base_url' => $base_url
                    ),
                    array(
                        'item' => 'Utilization Summary',
                        'tag_icon' => 'assets/appmenu/demo_req.png',
                        'base_url' => $base_url
                    ),
                );
                if(($user_type == 'area') || ($user_type == 'sales_area')) {
                    $data[] = array(
                        'item' => 'Request New Material',
                        'tag_icon' => 'assets/appmenu/demo_req.png',
                        'base_url' => $base_url
                    );
                    $data[] = array(
                        'item' => 'My Demo Requests',
                        'tag_icon' => 'assets/appmenu/demo_req.png',
                        'base_url' => $base_url
                    );
                }
                if($user_type == 'sales_regional') {
                    $approval_data = array(
                        'item' => 'For My Approvals',
                        'tag_icon' => 'assets/appmenu/demo_req.png',
                        'base_url' => $base_url
                    );
                    $data[] = $approval_data;
                }
                if($user_type == 'zonal') {
                    $data[] = array(
                        'item' => 'Demo Material Requests',
                        'tag_icon' => 'assets/appmenu/demo_req.png',
                        'base_url' => $base_url
                    );
                }
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Menu Item Found!',
                    'data' => $data
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function listgodown($state_id,$updated_date=0) {
        if($state_id) {
            $curTime = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "v3/listgodown",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $crr_date = date('Y-m-d H:i:s');
            if($updated_date == 0) {
                $godown = Godown::where('state_id',$state_id)->where('status',1)->get();
            } else {
                $godown = Godown::where('state_id',$state_id)->where('updated_at','>=', $updated_date)->where('status',1)->get();
            }
            $log_firstTime  = strtotime($log_start_time);
            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
            $log_time_diff  = $log_lastTime-$log_firstTime;
            $timeConsumed = round(microtime(true) - $curTime,3)*1000;
            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> 0,'user_type'=>NULL,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'Godown List Found!',
                'crr_date' => $crr_date,
                'data' => $godown
            ]);
        } else {
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'state_id is required!',
                'data' => array()
            ]);
            
        }
    }

    /*public function listgodown($state_id='') {
        if($state_id) {
            $godown = Godown::where('state_id',$state_id)->where('status',1)->get();
        } else {
            $godown = Godown::where('status',1)->get();
        }
        return $this->respond([
            'status' => 'success',
            'status_code' => $this->getStatusCode(),
            'message' => 'Godown List Found!',
            'data' => $godown
        ]);
    }*/

    public function requestNewMaterial(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'          => 'required|string',
            'product_id'        => 'required',
            'product_name'      => 'required',
            'crop_id'           => 'required',
            'crop_name'         => 'required',
            'expected_demos'    => 'required',
            'need_by_date'      => 'required',
            'requested_units'   => 'required',
            'godown_id'         => 'required',
            'godown_name'       => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $s1_ecn_name  = $user_details[0]->manager_name;

                $product_id     = $data['product_id'];
                $product_name   = $data['product_name'];
                $crop_id        = $data['crop_id'];
                $crop_name      = $data['crop_name'];
                $expected_demos = $data['expected_demos'];
                $need_by_date   = $data['need_by_date'];
                $requested_units= $data['requested_units'];
                $godown_id      = $data['godown_id'];
                $godown_name    = $data['godown_name'];

                $demo_material_data = array(
                    'requested_by_ecn'  => $user_ecn,
                    's1_ecn_no'         => $user_ecn,
                    's1_ecn_type'       => $user_type,
                    's1_ecn_name'       => $s1_ecn_name,
                    'product_id'        => $product_id,
                    'product_name'      => $product_name,
                    'crop_id'           => $crop_id,
                    'crop_name'         => $crop_name,
                    'expected_demos'    => $expected_demos,
                    'need_by_date'      => $need_by_date,
                    'requested_units'   => $requested_units,
                    'godown_id'         => $godown_id,
                    'godown_name'       => $godown_name,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s')
                );
                $result = DemoMaterialRequest::firstOrNew($demo_material_data);
                $result = $result->save();

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Request Successfully Submitted!',
                    'data' => array()
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function listrequestedMaterial(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'updated_date'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $updated_date = $data['updated_date'];
            $crr_date = date('Y-m-d H:i:s');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $s1_user_ecn = array();
                $s1_user_type = array();
                if(($user_type == 'area') || ($user_type == 'sales_area')) {
                    $s1_user_ecn[] = $user_ecn;
                    $s1_user_type[] = $user_type;
                } else if($user_type == 'sales_regional') {
                    $qr_sales_area = DB::table('sales_area_managers')->where('sales_regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $qr_area = DB::table('area_managers')->where('sales_regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                } else if($user_type == 'regional') {
                    $qr_sales_area = DB::table('sales_area_managers')->where('regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $qr_area = DB::table('area_managers')->where('regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                } else if($user_type == 'zonal') {
                    $qr_sales_area = DB::table('sales_area_managers')->where('zonal_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $qr_area = DB::table('area_managers')->where('zonal_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                } else if($user_type == 'marketing_supervisor') {
                    $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->select('sales_regional_manager_id')->get();
                    $rsm_ids = array();
                    foreach ($marketing_supervisor_rsm as $key => $value) {
                        $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
                    }
                    $qr_sales_area = DB::table('sales_area_managers')->whereIn('sales_regional_manager_id',$rsm_ids)->where('status','1')->select('ECN')->get();
                    $qr_area = DB::table('area_managers')->whereIn('sales_regional_manager_id',$rsm_ids)->where('status','1')->select('ECN')->get();
                    $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                } else {
                    return $this->respondWithError("Invalid Attempt !!");
                }
                $s1_ids = array();
                if(($user_type == 'area') || ($user_type == 'sales_area')) {
                    $s1_ids[] = $user_ecn;
                } else {
                    foreach ($s1_user_ecn as $key => $value) {
                        $s1_ids[] = $s1_user_ecn[$key]->ECN;
                    }
                }
                if($updated_date==0) {
                    $result = DemoMaterialRequest::whereIn('s1_ecn_no',$s1_ids)->get();
                } else {
                    $result = DemoMaterialRequest::whereIn('s1_ecn_no',$s1_ids)->where('updated_at','>=', $updated_date)->get();
                }

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'List Request Material!',
                    'crr_date' => $crr_date,
                    'data' => $result
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function approveRequestedMaterial(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'                  => 'required|string',
            'demo_material_request_id'  => 'required',
            'rsm_approval_status'       => 'required',
            // 'rsm_approval_comment'      => 'required',
            'rsm_approved_unit'         => 'required',
            'rsm_approval_date'         => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn  = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $rsm_ecn_name = $user_details[0]->manager_name;

                if($user_type == 'sales_regional') {
                    $demo_material_request_id = $data['demo_material_request_id'];
                    $rsm_approval_status  = $data['rsm_approval_status'];
                    $rsm_approval_comment = ($data['rsm_approval_comment']) ? $data['rsm_approval_comment'] : '';
                    $rsm_approved_unit    = $data['rsm_approved_unit'];
                    $rsm_approval_date = $data['rsm_approval_date'];

                    DemoMaterialRequest::where('demo_material_request_id',$demo_material_request_id)
                        ->update([
                            'rsm_ecn_no'=> $user_ecn,
                            'rsm_ecn_name'=> $rsm_ecn_name,
                            'rsm_approval_status'=> $rsm_approval_status,
                            'rsm_approved_unit'=> $rsm_approved_unit,
                            'rsm_approval_comment'=> $rsm_approval_comment,
                            'rsm_approval_date'=> $rsm_approval_date,
                            'updated_at'=> date('Y-m-d H:i:s')
                        ]);

                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Material Request Approved!',
                        'data' => array()
                    ]);
                } else {
                   return $this->respondWithError("Unauthorized !!");
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function receivedRequestedMaterial(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'                  => 'required|string',
            'demo_material_request_id'  => 'required',
            'receiving_status'          => 'required',
            // 'received_comment'          => 'required',
            'received_unit'             => 'required',
            'receiving_date'            => 'required',
            'receiving_status_captured_date' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $demo_material_request_id   = $data['demo_material_request_id'];
                $receiving_status           = $data['receiving_status'];
                $received_comment           = ($data['received_comment']) ? $data['received_comment'] : '';
                $received_unit              = $data['received_unit'];
                $receiving_date             = $data['receiving_date'];
                $receiving_status_captured_date = $data['receiving_status_captured_date'];

                DemoMaterialRequest::where('demo_material_request_id',$demo_material_request_id)
                    ->update([
                        'receiving_status'  => $receiving_status,
                        'received_unit'     => $received_unit,
                        'received_comment'  => $received_comment,
                        'receiving_date'    => $receiving_date,
                        'receiving_status_captured_date' => $receiving_status_captured_date,
                        'updated_at'        => date('Y-m-d H:i:s')
                    ]);
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Material Request Received Status Updated!',
                    'data' => array()
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function queryRequestedMaterial(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'content'   => 'required',
            'demo_material_request_id' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $user_name  = $user_details[0]->manager_name;
                
                $content    = $data['content'];
                $demo_material_request_id = $data['demo_material_request_id'];

                $demo_material_data = array(
                    'user_id'   => $user_id,
                    'user_type' => $user_type,
                    'user_ecn'  => $user_ecn,
                    'user_name' => $user_name,
                    'content'   => $content,
                    'demo_material_request_id' => $demo_material_request_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                $result = DemoMaterialRequestQA::firstOrNew($demo_material_data);
                $result = $result->save();

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Message Added Successfully!'
                    // 'data' => array()
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function listQueryRequestedMaterial(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'updated_date'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $updated_date = $data['updated_date'];
            $crr_date     = date('Y-m-d H:i:s');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                if($updated_date==0) {
                    $result = DemoMaterialRequestQA::get();
                } else {
                    $result = DemoMaterialRequestQA::where('updated_at','>=', $updated_date)->get();
                }

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'List Request Material Query!',
                    'crr_date' => $crr_date,
                    'data' => $result
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function listUtilizationSummary(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken' => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $crr_date = date('Y-m-d H:i:s');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $s1_user_ecn = array();
                $s1_user_type = array();
                if(($user_type == 'AREA') || ($user_type == 'area') || ($user_type == 'sales_area')) {
                    $s1_user_ecn[]['ECN'] = $user_ecn;
                    $s1_user_type[]['user_type'] = $user_type;
                } else if($user_type == 'sales_regional') {
                    $qr_sales_area = DB::table('sales_area_managers')->where('sales_regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $qr_area = DB::table('area_managers')->where('sales_regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                } else if($user_type == 'regional') {
                    $qr_sales_area = DB::table('sales_area_managers')->where('regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $qr_area = DB::table('area_managers')->where('regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                } else if($user_type == 'zonal') {
                    $qr_sales_area = DB::table('sales_area_managers')->where('zonal_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $qr_area = DB::table('area_managers')->where('zonal_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                    $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                } else if($user_type == 'marketing_supervisor') {
                    $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->select('sales_regional_manager_id')->get();
                    $rsm_ids = array();
                    foreach ($marketing_supervisor_rsm as $key => $value) {
                        $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
                    }
                    $qr_sales_area = DB::table('sales_area_managers')->whereIn('sales_regional_manager_id',$rsm_ids)->where('status','1')->select('ECN')->get();
                    $qr_area = DB::table('area_managers')->whereIn('sales_regional_manager_id',$rsm_ids)->where('status','1')->select('ECN')->get();
                    $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                } else {
                    return $this->respondWithError("Invalid Attempt !!");
                }
                $s1_ids = array();
                if(($user_type == 'AREA') || ($user_type == 'area') || ($user_type == 'sales_area')) {
                    $s1_ids[] = $user_ecn;
                } else {
                    foreach ($s1_user_ecn as $key => $value) {
                        $s1_ids[] = $s1_user_ecn[$key]->ECN;
                    }
                }

                if(!empty($data['asm_ecn'])) {
                    $s1_ids = array();
                    $s1_ids[] = $data['asm_ecn'];
                }
                // return $s1_ids;
                $totalRequest = DemoMaterialRequest::whereIn('s1_ecn_no',$s1_ids)->count();
                $total_rsm_approval = DemoMaterialRequest::whereIn('s1_ecn_no',$s1_ids)->where('rsm_approval_status',1)->count();
                $total_admin_approval = DemoMaterialRequest::whereIn('s1_ecn_no',$s1_ids)->where('admin_approval_status',1)->count();
                $total_dispatch = DemoMaterialRequest::whereIn('s1_ecn_no',$s1_ids)->where('dispatch_status',1)->count();
                $total_received = DemoMaterialRequest::whereIn('s1_ecn_no',$s1_ids)->where('receiving_status',1)->count();
                $result = array(
                    'totalRequest'  => $totalRequest,
                    'total_rsm_approval'    => $total_rsm_approval,
                    'total_admin_approval'  => $total_admin_approval,
                    'total_dispatch' => $total_dispatch,
                    'total_received' => $total_received
                );
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'List Utilization Summary!',
                    'data' => $result
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function listasms(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken' => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $s1_users = array();
                if($user_type == 'sales_regional') {
                    $qr_sales_area = DB::table('sales_area_managers')->where('sales_regional_manager_id',$user_id)->where('status','1')->select('sales_area_manager_id as id','ECN','sales_area_manager_name as name',DB::raw("'sales_area' as user_type"))->get();
                    $qr_area = DB::table('area_managers')->where('sales_regional_manager_id',$user_id)->where('status','1')->select('area_manager_id as id','ECN','area_manager_name as name',DB::raw("'area' as user_type"))->get();
                    $s1_users = array_merge($qr_sales_area, $qr_area);
                } else if($user_type == 'regional') {
                    $qr_sales_area = DB::table('sales_area_managers')->where('regional_manager_id',$user_id)->where('status','1')->select('sales_area_manager_id as id','ECN','sales_area_manager_name as name',DB::raw("'sales_area' as user_type"))->get();
                    $qr_area = DB::table('area_managers')->where('regional_manager_id',$user_id)->where('status','1')->select('area_manager_id as id','ECN','area_manager_name as name',DB::raw("'area' as user_type"))->get();
                    $s1_users = array_merge($qr_sales_area, $qr_area);
                } else if($user_type == 'zonal') {
                    $qr_sales_area = DB::table('sales_area_managers')->where('zonal_manager_id',$user_id)->where('status','1')->select('sales_area_manager_id as id','ECN','sales_area_manager_name as name',DB::raw("'sales_area' as user_type"))->get();
                    $qr_area = DB::table('area_managers')->where('zonal_manager_id',$user_id)->where('status','1')->select('area_manager_id as id','ECN','area_manager_name as name',DB::raw("'area' as user_type"))->get();
                    $s1_users = array_merge($qr_sales_area, $qr_area);
                } else if($user_type == 'marketing_supervisor') {
                    $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->select('sales_regional_manager_id')->get();
                    $rsm_ids = array();
                    foreach ($marketing_supervisor_rsm as $key => $value) {
                        $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
                    }
                    $qr_sales_area = DB::table('sales_area_managers')->whereIn('sales_regional_manager_id',$rsm_ids)->where('status','1')->select('sales_area_manager_id as id','ECN','sales_area_manager_name as name',DB::raw("'sales_area' as user_type"))->get();
                    $qr_area = DB::table('area_managers')->whereIn('sales_regional_manager_id',$rsm_ids)->where('status','1')->select('area_manager_id as id','ECN','area_manager_name as name',DB::raw("'area' as user_type"))->get();
                    $s1_users = array_merge($qr_sales_area, $qr_area);
                } else {
                    return $this->respondWithError("Invalid Attempt !!");
                }
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'List Utilization Summary!',
                    'data' => $s1_users
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function listDemoUtilizations(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken' => 'required|string',
            'updated_date' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $updated_date = $data['updated_date'];
            $crr_date = date('Y-m-d H:i:s');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $addQry ='';
                if(!empty($data['asm_ecn'])) {
                    $s1_ecn_no = $data['asm_ecn'];
                    $addQry .= 's1_ecn_no="'.$s1_ecn_no.'"';
                } else {
                    $s1_user_ecn = array();
                    if(($user_type == 'area') || ($user_type == 'sales_area')) {
                        $s1_user_ecn[]['ECN'] = $user_ecn;
                    } else if($user_type == 'sales_regional') {
                        $qr_sales_area = DB::table('sales_area_managers')->where('sales_regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                        $qr_area = DB::table('area_managers')->where('sales_regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                        $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                    } else if($user_type == 'regional') {
                        $qr_sales_area = DB::table('sales_area_managers')->where('regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                        $qr_area = DB::table('area_managers')->where('regional_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                        $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                    } else if($user_type == 'zonal') {
                        $qr_sales_area = DB::table('sales_area_managers')->where('zonal_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                        $qr_area = DB::table('area_managers')->where('zonal_manager_id',$user_id)->where('status','1')->select('ECN')->get();
                        $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                    } else if($user_type == 'marketing_supervisor') {
                        $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->select('sales_regional_manager_id')->get();
                        $rsm_ids = array();
                        foreach ($marketing_supervisor_rsm as $key => $value) {
                            $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
                        }
                        $qr_sales_area = DB::table('sales_area_managers')->whereIn('sales_regional_manager_id',$rsm_ids)->where('status','1')->select('ECN')->get();
                        $qr_area = DB::table('area_managers')->whereIn('sales_regional_manager_id',$rsm_ids)->where('status','1')->select('ECN')->get();
                        $s1_user_ecn = array_merge($qr_sales_area, $qr_area);
                    } else {
                        return $this->respondWithError("Invalid Attempt !!");
                    }
                    $s1_ids = array();
                    if(($user_type == 'AREA') || ($user_type == 'area') || ($user_type == 'sales_area')) {
                        $s1_ids[] = $user_ecn;
                    } else {
                        foreach ($s1_user_ecn as $key => $value) {
                            $s1_ids[] = $s1_user_ecn[$key]->ECN;
                        }
                    }
                    $imp_s1_ecn = implode("','", $s1_ids);
                    $addQry .= "s1_ecn_no IN ('".$imp_s1_ecn."')";
                }
                if(!empty($data['product_id'])) {
                    $product_id = $data['product_id'];
                    $addQry .= ' AND product_id="'.$product_id.'"';
                }
                if(!empty($data['crop_id'])) {
                    $crop_id = $data['crop_id'];
                    $addQry .= ' AND crop_id="'.$crop_id.'"';
                }
                if($updated_date !=0) {
                    $addQry .= ' AND updated_at>="'.$updated_date.'"';
                }

                $query = "SELECT s1_ecn_no,s1_ecn_type,s1_ecn_name,product_id,product_name,crop_id,crop_name,SUM(received_unit) as received_materials FROM demo_material_request WHERE ".$addQry." GROUP BY s1_ecn_no, product_id, crop_id ORDER BY demo_material_request_id DESC";
                $totalRequests = DB::select(DB::raw($query));
                $getTeamDDs = $this->getTeamDDs($user_id,$user_type);
                $addReqQry = "";
                if($getTeamDDs !=0) {
                    $dd_ids = array();
                    foreach ($getTeamDDs as $key => $value) {
                        $dd_ids[] = $getTeamDDs[$key]->id;
                    }
                    $imp_dd_ids = implode("','", $dd_ids);
                    $addReqQry .= " AND dd_id IN ('".$imp_dd_ids."')";
                }
                foreach ($totalRequests as $key=>$value) {
                    $product_id = $value->product_id;
                    $crop_id = $value->crop_id;
                    $received_materials = $value->received_materials;
                    $qr_demo_done = "SELECT count(*) as demo_done FROM demo_request WHERE product_id='".$product_id."' AND crop_id='".$crop_id."' ".$addReqQry." ORDER BY demo_request_id DESC";
                    $rs_demo_done = DB::select(DB::raw($qr_demo_done));
                    $demo_done_count = $rs_demo_done[0]->demo_done;

                    $utilization = "SELECT utilization_Per_demo FROM product_crop_relations WHERE product_id='".$product_id."' AND crop_id='".$crop_id."' AND status='1'";
                    $rs_utilization = DB::select(DB::raw($utilization));
                    if(count($rs_utilization)>0) {
                        $utilization_Per_demo = $rs_utilization[0]->utilization_Per_demo;
                        $demo_applied = $demo_done_count*$utilization_Per_demo;
                        $total_waste = $received_materials-$demo_applied;
                    } else {
                        $demo_applied = 0;
                        $total_waste = 0;
                    }

                    $totalRequests[$key]->demo_done = $rs_demo_done[0]->demo_done;
                    $totalRequests[$key]->demo_applied = $demo_applied;
                    $totalRequests[$key]->waste = $total_waste;
                    // $totalRequests[$key]->count = $utilization;
                }
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'List Utilization Summary!',
                    'data' => $totalRequests
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function getTeamDDs($user_id, $user_type) {
        if($user_type == "zonal") {
            $query = "SELECT dd_id as id FROM dhanuka_doctor WHERE zonal_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "regional") {
            $query = " SELECT dd_id as id FROM dhanuka_doctor WHERE regional_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "sales_regional") {
            $query = " SELECT dd_id as id FROM dhanuka_doctor WHERE sales_regional_manager_id='".$user_id."' AND status='1'";
        } else if(($user_type == "area") || ($user_type == "AREA")) {
            $query = "SELECT dd_id as id FROM dhanuka_doctor WHERE area_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "sales_area") {
            $query = " SELECT dd_id as id FROM dhanuka_doctor WHERE sales_area_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "bdo") {
            $query = " SELECT dd_id as id FROM dhanuka_doctor WHERE bdo_id='".$user_id."' AND status='1'";
        } else if($user_type == "marketing_supervisor") {
            $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->select('sales_regional_manager_id')->get();
            $rsm_ids = array();
            foreach ($marketing_supervisor_rsm as $key => $value) {
                $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
            }
            $imp_rsm_ids = implode("','", $rsm_ids);
            $query = " SELECT dd_id as id FROM dhanuka_doctor WHERE sales_regional_manager_id IN ('".$imp_rsm_ids."') AND status='1'";
        } else {
            return 0;
        }
        $user = DB::select(DB::raw($query));
        return $user;
    }
    public function getUserType($user_type,$user_id) {
        if ($user_type == 'bdo') {
            $getUserDesignation = DB::table('users')
                                  ->where('id',$user_id)
                                  ->where('status','1')
                                  ->select('designation')
                                  ->get();
            $userType = $getUserDesignation[0]->designation;
        } elseif ($user_type == 'dhanuka_doctor') {
            $getDdDesignation = DB::table('dhanuka_doctor')
                                  ->where('dd_id',$user_id)
                                  ->where('status','1')
                                  ->select('designation')
                                  ->get();
            $userType = $getDdDesignation[0]->designation;
        } elseif ($user_type == 'sales_regional') {
            $userType = 'RSM';
        } elseif ($user_type == 'sales_area') {
            $userType = 'ASM';
        } elseif ($user_type == 'marketing_supervisor') {
            $userType = 'Marketing Supervisor';
        } else {
            $userType = $user_type;
        }
        return $userType;
    }

    public function uploadimage(Request $request) {
        $rules = array(
            'image'           => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            if($request->file('image')) {
                $file=$request->file('image');
                //s3Upload
                $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                $sTempFileName = 'assets/farmer/' . $fileName;
                Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                return $this->respond([
                    'status'      => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'     => 'File uploaded Successfully.',
                    'image_path'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com/assets/farmer/',
                    'data'        => $fileName
                ]);
            }
        }
    }

    public function createTeaEstate(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'          => 'required|string',
            'office_type'       => 'required',
            'tea_estate_type'   => 'required',
            'name'              => 'required',
            'latitude'          => 'required',
            'longitude'         => 'required',
            'state_id'          => 'required',
            'district_id'       => 'required',
            'address'           => 'required',
            'fl_name'           => 'required',
            'fl_type'           => 'required',
            'fl_ecn'            => 'required',
            'image'             => 'required',
            'contact_data'      => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $user_name    = $user_details[0]->manager_name;

                $office_type        = $data['office_type'];
                $tea_estate_type    = $data['tea_estate_type'];
                $parent_tea_estate_id = (!empty($data['parent_tea_estate_id'])) ? $data['parent_tea_estate_id'] : 0;
                $name               = $data['name'];
                $turn_over          = (!empty($data['turn_over'])) ? $data['turn_over'] : '';
                $land_area          = (!empty($data['land_area'])) ? $data['land_area'] : '';
                $estate_company_id  = (!empty($data['estate_company_id'])) ? $data['estate_company_id'] : 0;
                $parent_tea_estate_id = (!empty($data['parent_tea_estate_id'])) ? $data['parent_tea_estate_id'] : 0;
                if($parent_tea_estate_id == 0) {
                    $estate_category_id = $this->getEstateCategory($turn_over,$land_area,$estate_company_id);
                } else {
                    $parent_tea_estate = TeaEstate::where('tea_estate_id',$parent_tea_estate_id)->select('tea_estate_id','estate_category_id','estate_company_id')->get();
                    if(count($parent_tea_estate)>0) {
                        $estate_category_id = $parent_tea_estate[0]->estate_category_id;
                        $estate_company_ids = $parent_tea_estate[0]->estate_company_id;
                    } else {
                        $estate_category_id = 0;
                        $estate_company_ids = 0;
                    }
                }
                $estate_company_id = ($estate_company_id == 0) ? $estate_company_ids : $estate_company_id;
                $state_id           = $data['state_id'];
                $district_id        = $data['district_id'];
                $address            = $data['address'];
                $fl_name            = $data['fl_name'];
                $fl_ecn             = $data['fl_ecn'];
                $fl_type            = $data['fl_type'];
                $latitude           = ($data['latitude']) ? $data['latitude'] : '';
                $longitude          = ($data['longitude']) ? $data['longitude'] : '';
                $contact_data       = $data['contact_data'];

                $tea_estate = TeaEstate::where('office_type',$office_type)->where('tea_estate_type',$tea_estate_type)->where('estate_company_id',$estate_company_id)->where('state_id',$state_id)->where('district_id',$district_id)->where('addedby_ecn',$user_ecn)->where('addedby_id',$user_id)->where('name',$name)->get();

                if (isset($tea_estate) && (count($tea_estate)==0)) {
                    $tea_estate_data = array(
                        'office_type'       => $office_type,
                        'tea_estate_type'   => $tea_estate_type,
                        'parent_tea_estate_id' => $parent_tea_estate_id,
                        'name'              => $name,
                        'turn_over'         => $turn_over,
                        'land_area'         => $land_area,
                        'estate_company_id' => $estate_company_id,
                        'estate_category_id'=> $estate_category_id,
                        'state_id'          => $state_id,
                        'district_id'       => $district_id,
                        'address'           => $address,
                        'latitude'          => $latitude,
                        'longitude'         => $longitude,
                        'fl_name'           => $fl_name,
                        'fl_ecn'            => $fl_ecn,
                        'fl_type'           => $fl_type,
                        'addedby_id'        => $user_id,
                        'addedby_name'      => $user_name,
                        'addedby_ecn'       => $user_ecn,
                        'addedby_type'      => $user_type,                    
                        'status'            => 1,
                        'created_at'        => date('Y-m-d H:i:s'),
                        'updated_at'        => date('Y-m-d H:i:s')
                    );
                    // return $tea_estate_data;
                    $TeaEstate = TeaEstate::firstOrNew($tea_estate_data);
                    $TeaEstate = $TeaEstate->save();
                    $tea_estate_id = TeaEstate::all()->last()->tea_estate_id;

                    if($request->file('image')) {
                        $file=$request->file('image');
                        if(!empty($file)) {
                            //s3Upload
                            $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                            $sTempFileName = 'assets/tea_estate/' . $fileName;
                            Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            TeaEstate::where('tea_estate_id',$tea_estate_id)
                                ->update([
                                    'image'     => $sTempFileName,
                                    'updated_at'=> date('Y-m-d H:i:s')
                                ]);
                        }
                    }
                    $contact_data_obj = json_decode($contact_data,true);
                    foreach ($contact_data_obj as $key => $contact) {
                        $tea_estate_contact = array(
                            'tea_estate_id' => $tea_estate_id,
                            'state_id'      => $state_id,
                            'district_id'   => $district_id,
                            'level'         => $contact['level'],
                            'designation'   => $contact['designation'],
                            'name'          => $contact['name'],
                            'wa_number'     => $contact['wa_number'],
                            'mobile_number' => $contact['mobile_number'],
                            'email'         => $contact['email'],
                            'status'        => 1,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        );
                        $TeaEstateContact = TeaEstateContact::firstOrNew($tea_estate_contact);
                        $TeaEstateContact = $TeaEstateContact->save();
                    }
                    $data = TeaEstate::where('tea_estate_id',$tea_estate_id)->get();

                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Tea Estate Successfully Submitted!',
                        'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                        'data' => $data
                    ]);
                } else {
                    $tea_estate_id = $tea_estate[0]->tea_estate_id;
                    $data = TeaEstate::where('tea_estate_id',$tea_estate_id)->get();
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Tea Estate Already Added!',
                        'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                        'data' => $data
                    ]);
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function getEstateCategory($turn_over,$land_holding,$estate_company_id) {
        if(($turn_over=='') || ($land_holding=='') || ($estate_company_id=='')){
            return 0;
        } else {
            $query_turn_over = "SELECT turn_over_point FROM estate_category WHERE turn_over<=".$turn_over." AND status=1";
            $turn_over_data = DB::select(DB::raw($query_turn_over));
            if(count($turn_over_data)==0) {
                $query_turn_over = "SELECT turn_over_point FROM estate_category WHERE company_category='C' AND status=1";
                $turn_over_data = DB::select(DB::raw($query_turn_over));
            }
            $turn_over_point = $turn_over_data[0]->turn_over_point;

            $query_land_holding = "SELECT land_holding_point FROM estate_category WHERE land_holding<=".$land_holding." AND status=1";
            $land_holding_data = DB::select(DB::raw($query_land_holding));
            if(count($land_holding_data)==0) {
                $query_land_holding = "SELECT land_holding_point FROM estate_category WHERE company_category='C' AND status=1";
                $land_holding_data = DB::select(DB::raw($query_land_holding));
            }
            $land_holding_point = $land_holding_data[0]->land_holding_point;

            $query_company_point = "SELECT company_point FROM estate_category WHERE estate_company_id=".$estate_company_id." AND status=1";
            $company_point_data = DB::select(DB::raw($query_company_point));
            if(count($company_point_data)==0) {
                $query_company_point = "SELECT company_point FROM estate_category WHERE company_category='C' AND status=1";
                $company_point_data = DB::select(DB::raw($query_company_point));
            }
            $company_point = $company_point_data[0]->company_point;
            $total_point = $turn_over_point+$land_holding_point+$company_point;
            // echo "=>".$turn_over_point."-".$land_holding_point."-".$company_point; exit;
            $total_point = round($total_point);

            $query_category = "SELECT estate_category_id FROM estate_category WHERE category_point<=".$total_point." AND status=1";
            $category_point_data = DB::select(DB::raw($query_category));
            if(count($category_point_data)==0) {
                $query_category = "SELECT estate_category_id FROM estate_category WHERE company_category='C' AND status=1";
                $category_point_data = DB::select(DB::raw($query_category));
            }
            $estate_category_id = $category_point_data[0]->estate_category_id;
            return $estate_category_id;
        }
    }

    public function getTypeDesignationFL(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'updated_date'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $updated_date = $data['updated_date'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id   = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn  = $user[0]->ECN;
                $manager_details = $this->findUserDetails($user_type, $api_token);
                $manager_name = $manager_details[0]->manager_name;
                $fl_ids   = array();
                $fl_ids[] = $user_id;
                if(strtolower($user_type) =='marketing_supervisor') {
                    $fl_ids   = array();
                    $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->select('sales_regional_manager_id')->get();
                    $rsm_ids = array();
                    foreach ($marketing_supervisor_rsm as $key => $value) {
                        $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
                    }
                    $fl_by = 'sales_regional_manager_id';
                    $fl_ids = $rsm_ids;
                } else if(strtolower($user_type) =='zonal') {
                    $fl_by = 'zonal_manager_id';
                } else if(strtolower($user_type) =='regional') {
                    $fl_by = 'regional_manager_id';
                } else if(strtolower($user_type) =='sales_regional') {
                    $fl_by = 'sales_regional_manager_id';
                } else if(strtolower($user_type) =='sales_area') {
                    $fl_by = 'sales_area_manager_id';
                } else if(strtolower($user_type) =='area') {
                    $fl_by = 'area_manager_id';
                } else {
                    $fl_by = 'id';
                }
                $fl_data   = User::whereIn($fl_by,$fl_ids)->where('status',1)->select('id','ECN','name',DB::raw("'".$user_id."' as manager_id"),DB::raw("'".$manager_name."' as manager_name"));
                if($updated_date != 0) {
                    $fl_data->where('updated_at','>=', $updated_date);
                }
                $fl_data_list = $fl_data->groupBy('id')->get();

                $category_data = EstateCategory::where('status',1)->select('estate_category_id','company_category');
                if($updated_date != 0) {
                    $category_data->where('updated_at','>=', $updated_date);
                }
                $category_data_list = $category_data->get();

                $company_data = EstateCompany::where('status',1)->select('estate_company_id','company_type');
                if($updated_date != 0) {
                    $company_data->where('updated_at','>=', $updated_date);
                }
                $company_data_list = $company_data->get();

                $designation_data = EstateDesignation::where('status',1)->select('estate_designation_id','level','designation');
                if($updated_date != 0) {
                    $designation_data->where('updated_at','>=', $updated_date);
                }
                $designation_data_list = $designation_data->get();
                
                $data = array(
                    'category_data' => $category_data_list,
                    'designation_data' => $designation_data_list,
                    'company_data' => $company_data_list,
                    'fl_data' => $fl_data_list
                );

                return $this->respond([
                    'status'   => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'  => 'List Type Designation and FL',
                    'crr_date' => date('Y-m-d H:i:s'),
                    'data'     => $data
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function getListTeaEstate_old(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $tea_estate = array();
                $addQry = '1=1 AND ';
                $updated_date = $data['updated_at'];
                if($updated_date != 0) {
                    $addQry .= 't.updated_at > "'.$updated_date.'" AND ';
                    $parent_tea_estate = TeaEstate::where('parent_tea_estate_id',0)->where('updated_at','>=', $updated_date)->get();
                } else {
                    $parent_tea_estate = TeaEstate::where('parent_tea_estate_id',0)->get();
                }
                $a=0;
                foreach ($parent_tea_estate as $key => $parent) {
                    $parent_tea_estate_id = $parent->tea_estate_id;
                    $parent_tea_estate_query = " SELECT 
                                t.tea_estate_id,
                                (CASE WHEN t.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                (CASE WHEN t.tea_estate_type='1' THEN 'Tea Estate' ELSE 'STG' END) as tea_estate_type,
                                t.parent_tea_estate_id,
                                t.name,
                                t.turn_over,
                                t.land_area,
                                ec.estate_company_id,
                                ec.company_category,
                                s.state_id,
                                s.state,
                                d.district_id,
                                d.district,
                                t.address,
                                t.latitude,
                                t.longitude,
                                t.image,
                                t.fl_name,
                                t.fl_ecn,
                                t.fl_type,
                                t.status,
                                t.created_at,
                                t.updated_at
                            FROM tea_estate t
                            LEFT JOIN states s ON t.state_id=s.state_id
                            LEFT JOIN districts d ON t.district_id=d.district_id
                            LEFT JOIN estate_company ec ON t.estate_category_id=ec.estate_company_id
                            WHERE $addQry t.status='1' AND t.addedby_id='".$user_id."' AND t.addedby_type='".$user_type."' AND t.parent_tea_estate_id='0' AND tea_estate_id='".$parent_tea_estate_id."'
                            ORDER BY t.tea_estate_id ASC";
                    $tea_estate_detail = DB::select(DB::raw($parent_tea_estate_query));
                    $tea_estate[$a] = $tea_estate_detail;
                    if($updated_date != 0) {
                        $parent_tea_estate_contact = TeaEstateContact::where('tea_estate_id',$parent_tea_estate_id)->where('updated_at','>=', $updated_date)->where('status',1)->get();
                    } else {
                        $parent_tea_estate_contact = TeaEstateContact::where('tea_estate_id',$parent_tea_estate_id)->where('status',1)->get();
                    }
                    $tea_estate[$a]['contact_data'] = $parent_tea_estate_contact;
                    if($updated_date != 0) {
                        $child_tea_estate = TeaEstate::where('parent_tea_estate_id',$parent_tea_estate_id)->where('updated_at','>=', $updated_date)->get();
                    } else {
                        $child_tea_estate = TeaEstate::where('parent_tea_estate_id',$parent_tea_estate_id)->get();
                    }
                    // return $child_tea_estate;
                    $b=0;
                    foreach ($child_tea_estate as $key => $child) {
                        $child_tea_estate_id = $child->tea_estate_id;
                        $child_tea_estate_query = " SELECT 
                                    t.tea_estate_id,
                                    (CASE WHEN t.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                    (CASE WHEN t.tea_estate_type='1' THEN 'Tea Estate' ELSE 'STG' END) as tea_estate_type,
                                    t.parent_tea_estate_id,
                                    t.name,
                                    t.turn_over,
                                    t.land_area,
                                    ec.estate_company_id,
                                    ec.company_category,
                                    s.state_id,
                                    s.state,
                                    d.district_id,
                                    d.district,
                                    t.address,
                                    t.latitude,
                                    t.longitude,
                                    t.image,
                                    t.fl_name,
                                    t.fl_ecn,
                                    t.fl_type,
                                    t.status,
                                    t.created_at,
                                    t.updated_at
                                FROM tea_estate t
                                LEFT JOIN states s ON t.state_id=s.state_id
                                LEFT JOIN districts d ON t.district_id=d.district_id
                                LEFT JOIN estate_company ec ON t.estate_category_id=ec.estate_company_id
                                WHERE $addQry t.status='1' AND t.addedby_id='".$user_id."' AND t.addedby_type='".$user_type."' AND t.tea_estate_id='".$child_tea_estate_id."'
                                ORDER BY t.tea_estate_id ASC";
                        // return $child_tea_estate_query;
                        $child_tea_estate = DB::select(DB::raw($child_tea_estate_query));
                        $tea_estate[$a]['branch_detail'][$b] = $child_tea_estate;
                        if($updated_date != 0) {
                            $child_tea_estate_contact = TeaEstateContact::where('tea_estate_id',$child_tea_estate_id)->where('updated_at','>=', $updated_date)->where('status',1)->get();
                        } else {
                            $child_tea_estate_contact = TeaEstateContact::where('tea_estate_id',$child_tea_estate_id)->where('status',1)->get();
                        }
                        // return $child_tea_estate_contact;
                        $tea_estate[$a]['branch_detail'][$b]['contact_data'] = $child_tea_estate_contact;
                        $b++;
                    }
                    $a++;
                }
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Tea Estate List Found',
                    'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data' => $tea_estate
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function getListTeaEstate(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $tea_estate = array();
                $addQry = '1=1 AND ';
                $updated_date = $data['updated_at'];

                if($user_type=='bdo') {
                    $expFlecn = array($user_ecn);
                } else {
                    $getTeamFlecn = $this->getTeamFlecn($user_id, $user_type);
                    if(($getTeamFlecn !=0) && count($getTeamFlecn)>0) {
                        $Flecn = $getTeamFlecn[0]->ecn;
                        $expFlecn = explode(',', $Flecn);
                    } else {
                        $expFlecn = array();
                    }
                }
                $tea_estates = TeaEstate::whereIn('fl_ecn',$expFlecn)->where('fl_type','bdo')->where('status',1);
                if($updated_date != 0) {
                    $tea_estates->where('updated_at','>=',$updated_date);
                }
                $tea_estate_datas = $tea_estates->groupBy('tea_estate_id')->get();

                $tea_estate_data = array();
                foreach ($tea_estate_datas as $key => $value) {
                    $tea_estate_id = $value->tea_estate_id;
                    array_push($tea_estate_data, $tea_estate_id);
                    if($value->parent_tea_estate_id != 0) {
                        array_push($tea_estate_data, $value->parent_tea_estate_id);
                    }
                }
                $tea_estate_data = array_unique($tea_estate_data);

                $a=0;
                foreach ($tea_estate_data as $key => $value) {
                    $tea_estate_id    = $value;
                    $tea_estate_query = " SELECT 
                                t.tea_estate_id,
                                t.office_type,
                                (CASE WHEN t.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type_name,
                                t.tea_estate_type,
                                (CASE WHEN t.tea_estate_type='1' THEN 'Tea Estate' ELSE 'STG' END) as tea_estate_type_name,
                                t.parent_tea_estate_id,
                                t.name,
                                t.turn_over,
                                t.land_area,
                                ec.estate_company_id,
                                ec.company_category,
                                s.state_id,
                                s.state,
                                d.district_id,
                                d.district,
                                t.address,
                                t.latitude,
                                t.longitude,
                                t.image,
                                t.fl_name,
                                t.fl_ecn,
                                t.fl_type,
                                t.status,
                                t.created_at,
                                t.updated_at,
                                '' as contact_data
                            FROM tea_estate t
                            LEFT JOIN states s ON t.state_id=s.state_id
                            LEFT JOIN districts d ON t.district_id=d.district_id
                            LEFT JOIN estate_category ec ON t.estate_category_id=ec.estate_category_id
                            WHERE $addQry t.status='1' AND tea_estate_id='".$tea_estate_id."'
                            ORDER BY t.tea_estate_id ASC";
                    $tea_estate_detail = DB::select(DB::raw($tea_estate_query));
                    // $tea_estate[] = $tea_estate_detail;
                    if($updated_date != 0) {
                        $tea_estate_contact = TeaEstateContact::where('tea_estate_id',$tea_estate_id)->where('updated_at','>=', $updated_date)->where('status',1)->get();
                    } else {
                        $tea_estate_contact = TeaEstateContact::where('tea_estate_id',$tea_estate_id)->where('status',1)->get();
                    }
                    if(count($tea_estate_detail)>0) { 
                        $tea_estate_detail[0]->contact_data = $tea_estate_contact;
                        array_push($tea_estate,$tea_estate_detail[0]);
                    }
                }
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Tea Estate List Found',
                    'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data' => $tea_estate
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function teaEstateListing(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;
                $updated_date = $data['updated_at'];

                if($user_type=='bdo') {
                    $expFlecn = array($user_ecn);
                } else {
                    $getTeamFlecn = $this->getTeamFlecn($user_id, $user_type);
                    if(($getTeamFlecn !=0) && count($getTeamFlecn)>0) {
                        $Flecn = $getTeamFlecn[0]->ecn;
                        $expFlecn = explode(',', $Flecn);
                    } else {
                        $expFlecn = array();
                    }
                }
                $tea_estate_data = TeaEstate::whereIn('fl_ecn',$expFlecn)->where('fl_type','bdo')->where('status',1);
                if($updated_date != 0) {
                    $tea_estate_data->where('updated_at','>=',$updated_date);
                }
                $tea_estate_data_list = $tea_estate_data->select('tea_estate_id','name','estate_category_id','office_type','tea_estate_type as type','fl_ecn as user_ecn')->groupBy('tea_estate_id')->get();

                return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Tea Estate List Found',
                        'crr_date'  => date('Y-m-d H:i:s'),
                        'data' => $tea_estate_data_list
                    ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function teaEstateListingHeadOfc(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;
                $updated_date = $data['updated_at'];

                $tea_estate_data = TeaEstate::where('office_type',1)->where('status',1);
                if($updated_date != 0) {
                    $tea_estate_data->where('updated_at','>=',$updated_date);
                }
                $tea_estate_data_list = $tea_estate_data->select('tea_estate_id','name','estate_category_id','office_type','tea_estate_type as type','fl_ecn as user_ecn')->groupBy('tea_estate_id')->get();

                return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Tea Estate List Found',
                        'crr_date'  => date('Y-m-d H:i:s'),
                        'data' => $tea_estate_data_list
                    ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function addContactCompanyBranch(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'tea_estate_id' => 'required',
            'contact_data'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id        = $user[0]->id;
                $user_type      = $user[0]->user_type;
                $user_ecn       = $user[0]->ECN;
                $user_details   = $this->findUserDetails($user_type, $api_token);
                $user_name      = $user_details[0]->manager_name;
                $tea_estate_id  = $data['tea_estate_id'];
                $contact_data   = $data['contact_data'];
                $tea_estate     = TeaEstate::where('tea_estate_id', $tea_estate_id)->first();

                $contact_data_obj = json_decode($contact_data,true);
                foreach ($contact_data_obj as $key => $contact) {
                    $tea_estate_contact = array(
                        'tea_estate_id' => $tea_estate_id,
                        'state_id'      => $tea_estate->state_id,
                        'district_id'   => $tea_estate->district_id,
                        'level'         => $contact['level'],
                        'designation'   => $contact['designation'],
                        'name'          => $contact['name'],
                        'wa_number'     => $contact['wa_number'],
                        'mobile_number' => $contact['mobile_number'],
                        'email'         => $contact['email'],
                        'status'        => 1,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s')
                    );
                    $TeaEstateContact = TeaEstateContact::firstOrNew($tea_estate_contact);
                    $TeaEstateContact = $TeaEstateContact->save();
                }
                // $data = TeaEstateContact::where('tea_estate_id',$tea_estate_id)->get();
                $data = array();

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Tea Estate Contact Successfully Submitted!',
                    'data' => $data
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function getAllContactsById($tea_estate_id,$updated_date=0) {
        if($tea_estate_id) {
            $crr_date = date('Y-m-d H:i:s');
            $TeaEstateContact = TeaEstateContact::where('tea_estate_id',$tea_estate_id)->where('status',1);
            if($updated_date != 0) {
                $TeaEstateContact->where('updated_at','>=', $updated_date);
            }
            $TeaEstateContactList = $TeaEstateContact->get();

            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'Tea Estate Contact List Found!',
                'crr_date' => $crr_date,
                'data' => $TeaEstateContactList
            ]);
        } else {
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'tea_estate_id is required!',
                'data' => array()
            ]);
            
        }
    }

    public function teaEstatePlanAtp(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'apiToken' => 'required|string',
                'atp_data' => 'required|array'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $planATP = $data['atp_data'];
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id    = $user[0]->id;
                    $user_type  = $user[0]->user_type;
                    $user_ecn   = $user[0]->ECN;
                    $user_details = $this->findUserDetails($user_type, $api_token);
                    $user_name    = $user_details[0]->manager_name;

                    if (count($planATP) > 0) {
                        $valid_user_plan = array();
                        //echo date('Y-m-d\TH:i:s');die;
                        foreach ($planATP as $plan) {
                            $dat = explode('.', $plan['date_time']);
                            // return date('Y-m-d\T00:00:00');
                            if ( $dat[0] < date('Y-m-d\T00:00:00')) {
                                return $this->respondWithError("You can not plan atp on previous date.");
                            }
                        }
                        foreach ($planATP as $plan) {
                            $estate_category = $plan['estate_category'];
                            $tea_estate_id   = $plan['tea_estate_id'];
                            $ticket_number   = $plan['ticket_number'];
                            $date_time       = $plan['date_time'];
                            $is_manager      = $plan['is_manager'];
                            if($is_manager == 0) {
                                if($user_type == 'bdo') {
                                    $fl_name    = 'My Self';
                                    $fl_ecn     = $user_ecn;
                                    $fl_type    = $user_type;
                                } else {
                                    $fl_name    = NULL;
                                    $fl_ecn     = NULL;
                                    $fl_type    = NULL;
                                }
                            } else {
                                $tea_estate = TeaEstate::where('tea_estate_id',$tea_estate_id)->first();
                                $fl_name    = $tea_estate->fl_name;
                                $fl_ecn     = $tea_estate->fl_ecn;
                                $fl_type    = $tea_estate->fl_type;
                            }
                            $user_plan_atp = array(
                                'date_time'     => $date_time,
                                'fl_name'       => $fl_name,
                                'fl_ecn'        => $fl_ecn,
                                'fl_type'       => $fl_type,
                                'manager_name'  => $user_name,
                                'manager_ecn'   => $user_ecn,
                                'manager_type'  => $user_type,
                                'estate_category' => $estate_category,
                                'tea_estate_id' => $tea_estate_id,
                                'ticket_number' => $ticket_number,
                                'is_manager'    => $is_manager,
                                'status'        => 1,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s')
                            );
                            $insert_plan_atp = TeaEstateATP::firstOrCreate($user_plan_atp);
                            $valid_user_plan[] = $user_plan_atp;
                        }
                        if (count($valid_user_plan) > 0) {
                            return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'ATP Successfully Added!',
                                'data' => $valid_user_plan,
                            ]);
                        } else {
                            return $this->respondWithError("Invalid plan activity!");
                        }
                    } else {
                        return $this->respondWithError("Plan activity not found!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function teaEstateAtpListing(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_date' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                // $addQry = '1=1 AND ((`manager_ecn` IN ('76543') AND `manager_type`='sales_area') OR (`manager_ecn` IN ('12975') AND `manager_type`='zonal') OR (`manager_ecn` IN ('12974') AND `manager_type`='sales_regional'))';
                $addQry = '1=1 ';
                $addQrySub = '';
                $updated_date = $data['updated_date'];
                if($updated_date != 0) {
                    $addQry .= ' AND ea.updated_at > "'.$updated_date.'"';
                }
                if($user_type == 'bdo') {
                    $addQry .=' AND ea.fl_ecn="'.$user_ecn.'" AND ea.fl_type="'.$user_type.'"';
                } else {
                    $getTeamFlecn = $this->getTeamFlecn($user_id, $user_type);
                    if(($getTeamFlecn !=0) && count($getTeamFlecn)>0) {
                        $Flecn = $getTeamFlecn[0]->ecn;
                        $expFlecn = explode(',', $Flecn);
                        $impTeamFlecn = "'".implode("','",$expFlecn)."'";
                        $addFlQuery   = '(ea.`fl_ecn` IN ('.$impTeamFlecn.') AND ea.`fl_type`="bdo") OR ';
                    } else {
                        $addFlQuery = '';
                    }
                    if($user_type =='marketing_supervisor') {
                        $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->groupBy('sales_regional_manager_id')->select('sales_regional_manager_id')->get();
                        $rsm_ids = array();
                        foreach ($marketing_supervisor_rsm as $key => $value) {
                            $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
                        }
                        $imp_rsm_ids = "'".implode("','", $rsm_ids)."'";
                        $query_rsm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_regional_managers WHERE sales_regional_manager_id IN (".$imp_rsm_ids.") AND status='1'";
                        $sales_regional = DB::select(DB::raw($query_rsm));
                        $sales_regional_ecn = $sales_regional[0]->ecn;
                        $exprsmEcn = explode(',', $sales_regional_ecn);
                        $imprsmEcn = "'".implode("','",$exprsmEcn)."'";
                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE sales_regional_manager_id IN (".$imp_rsm_ids.") AND status='1'";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="marketing_supervisor") OR (`manager_ecn` IN ('.$imprsmEcn.') AND `manager_type`="sales_regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else if($user_type =='zonal') {
                        $query_rm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM regional_managers WHERE zonal_manager_id=".$user_id." AND status='1' GROUP BY regional_manager_id";
                        $rs_regional = DB::select(DB::raw($query_rm));
                        $rs_regional_ecn = $rs_regional[0]->ecn;
                        $exprmEcn = explode(',', $rs_regional_ecn);
                        $imprmEcn = "'".implode("','",$exprmEcn)."'";

                        $query_rsm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_regional_managers WHERE zonal_manager_id=".$user_id." AND status='1' GROUP BY sales_regional_manager_id";
                        $sales_regional = DB::select(DB::raw($query_rsm));
                        $sales_regional_ecn = $sales_regional[0]->ecn;
                        $exprsmEcn = explode(',', $sales_regional_ecn);
                        $imprsmEcn = "'".implode("','",$exprsmEcn)."'";

                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE zonal_manager_id=".$user_id." AND status='1' GROUP BY sales_area_manager_id";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="zonal") OR (`manager_ecn` IN ('.$imprmEcn.') AND `manager_type`="regional") OR (`manager_ecn` IN ('.$imprsmEcn.') AND `manager_type`="sales_regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else if($user_type =='regional') {
                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE regional_manager_id=".$user_id." AND status='1' GROUP BY sales_area_manager_id";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else if($user_type =='sales_regional') {
                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE sales_regional_manager_id=".$user_id." AND status='1' GROUP BY sales_area_manager_id";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="sales_regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else {
                        $addQry .= ' AND ('.$addFlQuery.' (`manager_ecn`="'.$user_ecn.'" AND `manager_type`="'.$user_type.'"))';
                    }
                }
                $atp_query = " SELECT
                            ea.estate_atp_id,
                            ea.date_time,
                            ea.fl_name,
                            ea.fl_ecn,
                            ea.fl_type,
                            ea.manager_name,
                            ea.manager_ecn,
                            ea.manager_type,
                            ec.estate_category_id,
                            ec.company_category,
                            t.tea_estate_id,
                            t.name as tea_estate_name,
                            t.office_type as tea_estate_office_type,
                            t.tea_estate_type,
                            s.state_id,
                            s.state,
                            d.district_id,
                            d.district,
                            ea.ticket_number,
                            ea.status,
                            ea.created_at,
                            ea.updated_at,
                            ad.user_id,
                            ad.user_type,
                            ad.user_ecn,
                            ad.user_name,
                            ad.arrival_image,
                            ad.arrival_time,
                            ad.arrival_comment,
                            ad.arrival_latitude,
                            ad.arrival_longitude,
                            ad.departure_image,
                            ad.departure_time,
                            ad.departure_comment,
                            ad.departure_latitude,
                            ad.departure_longitude,
                            ad.duration,
                            ad.distance,
                            ad.is_validate
                            FROM estate_atp ea
                            LEFT JOIN tea_estate t ON ea.tea_estate_id=t.tea_estate_id
                            LEFT JOIN estate_atp_arrival_departure ad ON ea.estate_atp_id=ad.estate_atp_id AND ad.user_id='".$user_id."' AND ad.user_type='".$user_type."'
                            LEFT JOIN states s ON t.state_id=s.state_id
                            LEFT JOIN districts d ON t.district_id=d.district_id
                            LEFT JOIN estate_category ec ON ea.estate_category=ec.estate_category_id
                            WHERE $addQry
                            GROUP BY estate_atp_id
                            ORDER BY ea.date_time ASC";
                // return $atp_query;
                $atp_detail = DB::select(DB::raw($atp_query));

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Tea Estate List Found',
                    'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data' => $atp_detail
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function teaEstateArrivalDeparture(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'              => 'required|string',
            'estate_atp_id'         => 'required|string',
            'arrival_time'          => 'required',
            'arrival_image'         => 'required|mimes:jpg,png,jpeg',
            'arrival_latitude'      => 'required',
            'arrival_longitude'     => 'required',
            'departure_time'        => 'required',
            'departure_image'       => 'required|mimes:jpg,png,jpeg',
            'departure_latitude'    => 'required',
            'departure_longitude'   => 'required',
            'duration'              => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $user_name    = $user_details[0]->manager_name;
                $arrival_sTempFileName = '';
                $departure_sTempFileName = '';
                $estate_atp_id  = $data['estate_atp_id'];
                $arrival_time   = $data['arrival_time'];
                $arrival_comment= (!empty($data['arrival_comment'])) ? $data['arrival_comment'] : '';
                $arrival_latitude    = $data['arrival_latitude'];
                $arrival_longitude   = $data['arrival_longitude'];
                $departure_time      = $data['departure_time'];
                $departure_comment   = (!empty($data['departure_comment'])) ? $data['departure_comment'] : '';
                $departure_latitude  = $data['departure_latitude'];
                $departure_longitude = $data['departure_longitude'];
                $duration            = $data['duration'];

                $atp_ticket_check = TeaEstateATP::where('estate_atp_id', $estate_atp_id)->first();
                if($atp_ticket_check) {
                    // Calculate Distance and Check Validate
                    $tea_estate_radius = DB::table('estate_radius')->where('status',1)->select('radius')->get();
                    if(count($tea_estate_radius)>0) {
                        $radius = $tea_estate_radius[0]->radius;
                    } else {
                        $radius = 0;
                    }
                    $tea_estate_id = $atp_ticket_check->tea_estate_id;
                    $tea_estate_data = TeaEstate::where('tea_estate_id',$tea_estate_id)->select('latitude','longitude')->first();
                    $lat1 = $tea_estate_data->latitude;
                    $lon1 = $tea_estate_data->longitude;
                    $lat2 = $departure_latitude;
                    $lon2 = $departure_longitude;

                    $rad = M_PI / 180;
                    $distance = acos(sin($lat2*$rad) * sin($lat1*$rad) + cos($lat2*$rad) * cos($lat1*$rad) * cos($lon2*$rad - $lon1*$rad)) * 6371;// Kilometers
                    $distance_meter = round($distance*1000,2);
                    // $radius_meter   = $radius*1000;
                    if($distance_meter <= $radius) {
                        $is_validate = 1;
                    } else {
                        $is_validate = 0;
                    }
                    // return $response = array($distance_meter,$is_validate);
                    if ($request->hasFile('arrival_image')) {
                        if ($request->file('arrival_image')->isValid()) {
                            $arrival_file = $request->file('arrival_image');
                            //s3Upload
                            $extension = pathinfo($arrival_file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $arrival_fileName = "A" . md5(time() . rand()) . str_replace($arrival_file->getClientOriginalName(), '', $arrival_file->getClientOriginalName()) . '.' . $extension;
                            $arrival_sTempFileName = 'assets/tea_estate_atp/'.$arrival_fileName;
                            Storage::disk('s3')->put($arrival_sTempFileName, fopen($arrival_file, 'r+'),'public');
                            //
                        }
                    }
                    if ($request->hasFile('departure_image')) {
                        if ($request->file('departure_image')->isValid()) {
                            $departure_file = $request->file('departure_image');
                            //s3Upload
                            $departure_extension = pathinfo($departure_file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $departure_fileName = "A" . md5(time() . rand()) . str_replace($departure_file->getClientOriginalName(), '', $departure_file->getClientOriginalName()) . '.' . $departure_extension;
                            $departure_sTempFileName = 'assets/tea_estate_atp/'.$departure_fileName;
                            Storage::disk('s3')->put($departure_sTempFileName, fopen($departure_file, 'r+'),'public');
                            //
                        }
                    }
                    $atp_check = TeaEstateATPArivalDeparture::where('estate_atp_id', $estate_atp_id)->where('user_id', $user_id)->where('user_type',$user_type)->first();
                    if ($atp_check) {
                        $update_atp = TeaEstateATPArivalDeparture::where([
                            'user_id'   => $user_id,
                            'user_type' => $user_type,
                            'estate_atp_id' => $estate_atp_id
                        ])->update([
                            'arrival_time'      => $arrival_time,
                            'arrival_image'     => $arrival_sTempFileName,
                            'arrival_comment'   => $arrival_comment,
                            'arrival_latitude'  => $arrival_latitude,
                            'arrival_longitude' => $arrival_longitude,
                            'departure_time'    => $departure_time,
                            'departure_comment' => $departure_comment,
                            'departure_image'   => $departure_sTempFileName,
                            'departure_latitude'=> $departure_latitude,
                            'departure_longitude' => $departure_longitude,
                            'is_arrival_departure'=> '1',
                            'duration'          => $duration,
                            'distance'          => $distance_meter,
                            'is_validate'       => $is_validate,
                            'updated_at'        => date('Y-m-d H:i:s')
                        ]);
                        if ($update_atp) {
                            $base_url ="https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                            $data = array(
                                'arrival_image' => $base_url.'/'.$arrival_sTempFileName,
                                'departure_image' => $base_url.'/'.$departure_sTempFileName
                            );
                            return $this->respond([
                                'status'  => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'ATP Successfully Updated!',
                                'data'    => $data
                            ]);
                        } else {
                            return $this->respondNotFound("Something went wrong. Please Try Again!!");
                        }
                    } else {
                        $user_plan_atp = array(
                            'estate_atp_id'     => $estate_atp_id,
                            'user_id'           => $user_id,
                            'user_type'         => $user_type,
                            'user_ecn'          => $user_ecn,
                            'user_name'         => $user_name,
                            'arrival_time'      => $arrival_time,
                            'arrival_image'     => $arrival_sTempFileName,
                            'arrival_comment'   => $arrival_comment,
                            'arrival_latitude'  => $arrival_latitude,
                            'arrival_longitude' => $arrival_longitude,
                            'departure_time'    => $departure_time,
                            'departure_comment' => $departure_comment,
                            'departure_image'   => $departure_sTempFileName,
                            'departure_latitude'=> $departure_latitude,
                            'departure_longitude' => $departure_longitude,
                            'is_arrival_departure'=> '1',
                            'duration'          => $duration,
                            'distance'          => $distance_meter,
                            'is_validate'       => $is_validate,
                            'created_at'        => date('Y-m-d H:i:s'),
                            'updated_at'        => date('Y-m-d H:i:s')
                        );
                        $insert_plan_atp = TeaEstateATPArivalDeparture::firstOrCreate($user_plan_atp);
                        if ($insert_plan_atp) {
                            return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'ATP Successfully Added!',
                                'data' => array(),
                            ]);
                        } else {
                            return $this->respondWithError("ATP Data Not Uploaded!");
                        }
                    }
                } else {
                    return $this->respondNotFound("Invalid ATP Ticket !!");
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function teaEstateVisitSummary_old(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;

                $targetData = DB::table('estate_target')->where('user_ecn',$user_ecn)->where('user_type',$user_type)->where('status',1)->select('*')->get();
                if(count($targetData)>0) {
                    $target_month = $targetData[0]->target_month;
                    $target_year  = $targetData[0]->target_year;
                    $category_a_target = $targetData[0]->category_a_target;
                    $category_a_month_target = $category_a_target;

                    $category_b_target = $targetData[0]->category_b_target;
                    $category_b_month_target = $category_b_target;

                    $category_c_target = $targetData[0]->category_c_target;
                    $category_c_month_target = $category_c_target;
                } else {
                    $category_a_month_target = $category_b_month_target = $category_c_month_target = $target_month = $target_month = 0;
                }
                // return $category_a_month_target."--".$category_b_month_target."--".$category_c_month_target;

                $visitSummary  = array(
                    'month' => $target_month,
                    'year'  => $target_year
                );
                $estateCategory = EstateCategory::where('status',1)->get();
                foreach ($estateCategory as $category) {
                    $estate_category_id = $category->estate_category_id;
                    $company_category = $category->company_category;
                    if($company_category == 'A') {
                        $target = $category_a_month_target;
                    } else if($company_category == 'B') {
                        $target = $category_b_month_target;
                    } else {
                        $target = $category_c_month_target;
                    }
                    if($user_type == 'bdo') {
                        $plan = TeaEstateATP::where('fl_type','bdo')->where('fl_ecn',$user_ecn)->where('fl_ecn',$user_ecn)->where('estate_category',$estate_category_id)->count();
                        $visit = TeaEstateATP::where('fl_type','bdo')->where('fl_ecn',$user_ecn)->where('fl_ecn',$user_ecn)->where('estate_category',$estate_category_id)->where('is_arrival_departure',1)->count();
                    } else {
                        $plan = TeaEstateATP::where('manager_type',$user_type)->where('manager_ecn',$user_ecn)->where('estate_category',$estate_category_id)->where('is_manager',1)->count();
                        $visit = TeaEstateATP::where('manager_type',$user_type)->where('manager_ecn',$user_ecn)->where('estate_category',$estate_category_id)->where('is_manager',1)->where('is_arrival_departure',1)->count();
                    }
                    $cat = "cat_".strtolower($company_category)."_";
                    $visitSummary[$cat.'target'] = $target;
                    $visitSummary[$cat.'plan'] = $plan;
                    $visitSummary[$cat.'visit'] = $visit;
                }
                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Tea Estate Visit History Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $visitSummary
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    /*public function teaEstateVisitSummary(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;

                if(!empty($data['month'])) {
                    $target_month = $data['month'];
                } else {
                    $target_month = 0;
                }
                if(!empty($data['year'])) {
                    $target_year = $data['year'];
                } else {
                    $target_year = 0;
                }
                $target = DB::table('estate_target')->where('status',1);
                if(!empty($data['userType'])) {
                    $userType = $data['userType'];
                    $target->where('user_type',$userType);
                    if(!empty($data['userId'])) {
                        $userId  = $data['userId'];
                        if($userType == 'bdo') {
                            $ecn_by_id = 'id';
                        } else {
                            $ecn_by_id = strtolower($userType).'_manager_id';
                        }
                        $userEcn = $this->findUserEcn($ecn_by_id, $userType, $userId);
                        $target->where('user_ecn',$userEcn[0]->ecn);
                    } else {
                        if($user_type == 'bdo') {
                            $user_search_by_id = 'user_id';
                        } else {
                            $user_search_by_id = strtolower($user_type).'_manager_id';
                        }
                        if($userType == 'bdo') {
                            $user_search_table = 'users';
                        } else {
                            $user_search_table = strtolower($user_type).'_managers';
                        }
                        $query_users = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM ".$user_search_table." WHERE ".$user_search_by_id."=".$user_id." AND status='1'";
                        $users = DB::select(DB::raw($query_users));
                        $users_ecn = $users[0]->ecn;
                        $expusersEcn = explode(',', $users_ecn);
                        $target->whereIn('user_ecn',$expusersEcn);
                    }
                } else {
                    $target->where('user_type',$user_type);
                    $target->where('user_ecn',$user_ecn);
                }
                if(!empty($data['year'])) {
                    $target_year = $data['year'];
                    $target->where('target_year',$target_year);
                }
                if(!empty($data['month'])) {
                    $target_month = $data['month'];
                    $target->where('target_month',$target_month);
                }
                $targetData = $target->select('*')->get();
                if(count($targetData)>0) {
                    $target_month = $target_month;
                    $target_year  = $target_year;
                    $category_a_target = $targetData[0]->category_a_target;
                    $category_a_month_target = $category_a_target;

                    $category_b_target = $targetData[0]->category_b_target;
                    $category_b_month_target = $category_b_target;

                    $category_c_target = $targetData[0]->category_c_target;
                    $category_c_month_target = $category_c_target;
                } else {
                    $category_a_month_target = $category_b_month_target = $category_c_month_target = $target_month = $target_month = 0;
                }
                // return $category_a_month_target."--".$category_b_month_target."--".$category_c_month_target;

                $visitSummary  = array(
                    'month' => $target_month,
                    'year'  => $target_year
                );
                if($target_month !=0 && $target_year !=0) {
                    if($target_month<10) {
                        $target_month = '0'.$target_month;
                    }
                    $plan_date_start = $target_year.'-'.$target_month.'-01T00:00:00';
                    $plan_date_end = $target_year.'-'.$target_month.'-31T00:00:00';
                    $plan_date = 1;
                } else {
                    $plan_date = 0;
                }
                $estateCategory = EstateCategory::where('status',1)->get();
                foreach ($estateCategory as $category) {
                    $estate_category_id = $category->estate_category_id;
                    $company_category = $category->company_category;
                    if($company_category == 'A') {
                        $target = $category_a_month_target;
                    } else if($company_category == 'B') {
                        $target = $category_b_month_target;
                    } else {
                        $target = $category_c_month_target;
                    }
                    $planData = TeaEstateATP::where('estate_category',$estate_category_id);
                    $visitData = TeaEstateATP::join('estate_atp_arrival_departure','estate_atp_arrival_departure.estate_atp_id','=','estate_atp.estate_atp_id')->where('estate_atp.estate_category',$estate_category_id)->where('estate_atp_arrival_departure.is_arrival_departure',1);
                    if(!empty($data['userType'])) {
                        $userType = $data['userType'];
                        $visitData->where('estate_atp_arrival_departure.user_type',$userType);
                        if($userType == 'bdo') {
                            $planData->where('fl_type',$userType);
                        } else {
                            $planData->where('manager_type',$userType);
                        }
                        if(!empty($data['userId'])) {
                            $userId = $data['userId'];
                            if($userType == 'bdo') {
                                $ecn_by_id = 'id';
                            } else {
                                $ecn_by_id = strtolower($userType).'_manager_id';
                            }
                            $userEcn = $this->findUserEcn($ecn_by_id,$userType,$userId);
                            $planData->where('manager_ecn',$userEcn[0]->ecn);
                            if($userType == 'bdo') {
                                $planData->where('fl_ecn',$userEcn[0]->ecn);
                            } else {
                                $planData->where('manager_ecn',$userEcn[0]->ecn);
                            }
                            $visitData->where('estate_atp_arrival_departure.user_id',$userId);
                        } else {
                            if($user_type == 'bdo') {
                                $user_search_by_id = 'id';
                            } else {
                                $user_search_by_id = strtolower($user_type).'_manager_id';
                            }
                            if($userType == 'bdo') {
                                $user_search_table = 'users';
                            } else {
                                $user_search_table = strtolower($user_type).'_managers';
                            }
                            $query_users = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM ".$user_search_table." WHERE ".$user_search_by_id."=".$user_id." AND status='1'";
                            $users = DB::select(DB::raw($query_users));
                            $users_ecn = $users[0]->ecn;
                            $expusersEcn = explode(',', $users_ecn);
                            if($userType == 'bdo') {
                                $planData->whereIn('fl_ecn',$expusersEcn);
                            } else {
                                $planData->whereIn('manager_ecn',$expusersEcn);
                            }
                            $visitData->whereIn('estate_atp_arrival_departure.user_ecn',$expusersEcn);
                        }
                    } else {
                        if($user_type == 'bdo') {
                            $planData->where('fl_type',$user_type);
                            $planData->where('fl_ecn',$user_ecn);
                        } else {
                            $planData->where('manager_type',$user_type);
                            $planData->where('manager_ecn',$user_ecn);
                        }
                        $visitData->where('estate_atp_arrival_departure.user_ecn',$user_ecn);
                    }
                    if($plan_date==1) {
                        $planData->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end);
                        $visitData->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end);
                    }
                    $plan   = $planData->count();
                    $visit  = $visitData->count();

                    $cat = "cat_".strtolower($company_category)."_";
                    $visitSummary[$cat.'target'] = $target;
                    $visitSummary[$cat.'plan'] = $plan;
                    $visitSummary[$cat.'visit'] = $visit;
                }
                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Tea Estate Visit History Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $visitSummary
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }*/

    public function teaEstateVisitSummary(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;

                if(!empty($data['month'])) {
                    $target_month = $data['month'];
                } else {
                    $target_month = 0;
                }
                if(!empty($data['year'])) {
                    $target_year = $data['year'];
                } else {
                    $target_year = 0;
                }
                $target = DB::table('estate_target')->where('status',1);
                if(!empty($data['userType'])) {
                    $userType = $data['userType'];
                    $target->where('user_type',$userType);
                    if(!empty($data['userId'])) {
                        $userId  = $data['userId'];
                        if($userType == 'bdo') {
                            $ecn_by_id = 'id';
                        } else {
                            $ecn_by_id = strtolower($userType).'_manager_id';
                        }
                        $userEcn = $this->findUserEcn($ecn_by_id, $userType, $userId);
                        $target->where('user_ecn',$userEcn[0]->ecn);
                    } else {
                        if($user_type == 'bdo') {
                            $user_search_by_id = 'user_id';
                        } else {
                            $user_search_by_id = strtolower($user_type).'_manager_id';
                        }
                        if($userType == 'bdo') {
                            $user_search_table = 'users';
                        } else {
                            $user_search_table = strtolower($user_type).'_managers';
                        }
                        $query_users = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM ".$user_search_table." WHERE ".$user_search_by_id."=".$user_id." AND status='1'";
                        $users = DB::select(DB::raw($query_users));
                        $users_ecn = $users[0]->ecn;
                        $expusersEcn = explode(',', $users_ecn);
                        $target->whereIn('user_ecn',$expusersEcn);
                    }
                } else {
                    $target->where('user_type',$user_type);
                    $target->where('user_ecn',$user_ecn);
                }
                if(!empty($data['year'])) {
                    $target_year = $data['year'];
                    $target->where('target_year',$target_year);
                }
                if(!empty($data['month'])) {
                    $target_month = $data['month'];
                    $target->where('target_month',$target_month);
                }
                $targetData = $target->select('*')->get();
                if(count($targetData)>0) {
                    $target_month = $target_month;
                    $target_year  = $target_year;
                    $category_a_target = $targetData[0]->category_a_target;
                    $category_a_month_target = $category_a_target;

                    $category_b_target = $targetData[0]->category_b_target;
                    $category_b_month_target = $category_b_target;

                    $category_c_target = $targetData[0]->category_c_target;
                    $category_c_month_target = $category_c_target;
                } else {
                    $category_a_month_target = $category_b_month_target = $category_c_month_target = $target_month = $target_month = 0;
                }
                // return $category_a_month_target."--".$category_b_month_target."--".$category_c_month_target;

                $visitSummary  = array(
                    'month' => $target_month,
                    'year'  => $target_year
                );
                if($target_month !=0 && $target_year !=0) {
                    if($target_month<10) {
                        $target_month = '0'.$target_month;
                    }
                    $plan_date_start = $target_year.'-'.$target_month.'-01T00:00:00';
                    $plan_date_end = $target_year.'-'.$target_month.'-31T00:00:00';
                    $plan_date = 1;
                } else {
                    $plan_date = 0;
                }
                $estateCategory = EstateCategory::where('status',1)->get();
                foreach ($estateCategory as $category) {
                    $estate_category_id = $category->estate_category_id;
                    $company_category = $category->company_category;
                    if($company_category == 'A') {
                        $target = $category_a_month_target;
                    } else if($company_category == 'B') {
                        $target = $category_b_month_target;
                    } else {
                        $target = $category_c_month_target;
                    }
                    $planData = TeaEstateATP::where('estate_category',$estate_category_id);
                    $visitData = TeaEstateATP::join('estate_atp_arrival_departure','estate_atp_arrival_departure.estate_atp_id','=','estate_atp.estate_atp_id')->where('estate_atp.estate_category',$estate_category_id);
                    if(!empty($data['userType'])) {
                        $userType = $data['userType'];
                        $visitData->where('estate_atp_arrival_departure.user_type',$userType);
                        if($userType == 'bdo') {
                            $planData->where('fl_type',$userType);
                        } else {
                            $planData->where('manager_type',$userType);
                        }
                        if(!empty($data['userId'])) {
                            $userId = $data['userId'];
                            if($userType == 'bdo') {
                                $ecn_by_id = 'id';
                            } else {
                                $ecn_by_id = strtolower($userType).'_manager_id';
                            }
                            $userEcn = $this->findUserEcn($ecn_by_id,$userType,$userId);
                            $planData->where('manager_ecn',$userEcn[0]->ecn);
                            if($userType == 'bdo') {
                                $planData->where('fl_ecn',$userEcn[0]->ecn);
                            } else {
                                $planData->where('manager_ecn',$userEcn[0]->ecn);
                            }
                            $visitData->where('estate_atp_arrival_departure.user_id',$userId);
                        } else {
                            if($user_type == 'bdo') {
                                $user_search_by_id = 'id';
                            } else {
                                $user_search_by_id = strtolower($user_type).'_manager_id';
                            }
                            if($userType == 'bdo') {
                                $user_search_table = 'users';
                            } else {
                                $user_search_table = strtolower($user_type).'_managers';
                            }
                            $query_users = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM ".$user_search_table." WHERE ".$user_search_by_id."=".$user_id." AND status='1'";
                            $users = DB::select(DB::raw($query_users));
                            $users_ecn = $users[0]->ecn;
                            $expusersEcn = explode(',', $users_ecn);
                            if($userType == 'bdo') {
                                $planData->whereIn('fl_ecn',$expusersEcn);
                            } else {
                                $planData->whereIn('manager_ecn',$expusersEcn);
                            }
                            $visitData->whereIn('estate_atp_arrival_departure.user_ecn',$expusersEcn);
                        }
                    } else {
                        if($user_type == 'bdo') {
                            $planData->where('fl_type',$user_type);
                            $planData->where('fl_ecn',$user_ecn);
                        } else {
                            $planData->where('manager_type',$user_type);
                            $planData->where('manager_ecn',$user_ecn);
                        }
                        $visitData->where('estate_atp_arrival_departure.user_ecn',$user_ecn);
                    }
                    if($plan_date==1) {
                        $planData->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end);
                        $visitData->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end);
                    }
                    $plan   = $planData->count();
                    $visit  = $visitData->count();

                    $cat = "cat_".strtolower($company_category)."_";
                    $visitSummary[$cat.'target'] = $target;
                    $visitSummary[$cat.'plan'] = $plan;
                    $visitSummary[$cat.'visit'] = $visit;
                }
                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Tea Estate Visit History Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $visitSummary
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function teaEstateSalesReport(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'updated_date'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $updated_date = $data['updated_date'];
                $addQry = ' 1=1';

                if(!empty($data['month'])) {
                    $month = $data['month'];
                    $addQry .= ' AND sr.month="'.$month.'"';
                }
                if(!empty($data['year'])) {
                    $year = $data['year'];
                    $addQry .= ' AND sr.year="'.$year.'"';
                }
                if($updated_date != 0) {
                    $addQry .= ' AND sr.updated_at >="'.$updated_date.'"';
                }
                if(!empty($data['tea_estate_id'])) {
                    $tea_estate_id = $data['tea_estate_id'];
                    $addQry .= ' AND sr.tea_estate_id ="'.$tea_estate_id.'"';
                } else {
                    if($user_type=='bdo') {
                        $expFlecn = array($user_ecn);
                    } else {
                        return $getTeamFlecn = $this->getTeamFlecn($user_id, $user_type);
                        if(($getTeamFlecn !=0) && count($getTeamFlecn)>0) {
                            $Flecn = $getTeamFlecn[0]->ecn;
                            $expFlecn = explode(',', $Flecn);
                        } else {
                            $expFlecn = array();
                        }
                    }
                    // return $expFlecn;
                    $tea_estate = TeaEstate::whereIn('fl_ecn',$expFlecn)->where('fl_type','bdo')->where('status',1)->select('tea_estate_id')->get();
                    $tea_estate_ids = array();
                    foreach ($tea_estate as $key => $value) {
                        $tea_estate_ids[] = $tea_estate[$key]->tea_estate_id;
                    }
                    $imp_tea_estate_ids = "'".implode("','", $tea_estate_ids)."'";
                    $addQry .= ' AND sr.tea_estate_id IN('.$imp_tea_estate_ids.')';
                    // return $imp_tea_estate_ids;
                }

                $sales_report = " SELECT 
                                    sr.estate_sales_report_id as id,
                                    t.tea_estate_id,
                                    t.name as tea_estate_name,
                                    sr.month,
                                    sr.year,
                                    sr.sales_turnover as sales_figure
                                FROM estate_sales_report sr
                                LEFT JOIN tea_estate t ON sr.tea_estate_id=t.tea_estate_id
                                WHERE $addQry 
                                ORDER BY sr.estate_sales_report_id ASC";
                // return $sales_report;
                $sales_report_detail = DB::select(DB::raw($sales_report));

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Tea Estate Sales Report List Found',
                    // 'crr_date'  => date('Y-m-d H:i:s'),
                    'data' => $sales_report_detail
                ]);
            }
        }
    }

    public function createInstitutionalTestimonial(Request $request) {
        $data = $request->all();
        $rules = array(
            'institutionalType'     => 'required',
            'institutional_name'    => 'required',
            'user_ecn'              => 'required',
            'office_type'           => 'required',
            'type'                  => 'required',
            'institutional_id'      => 'required',
            'institutional_contact_id'  => 'required',
            'contact_name'          => 'required',
            'contact_designation'   => 'required',
            'contact_number'        => 'required',
            'wa_contact_number'     => 'required',
            'can_be_shared'         => 'required',
            'image_video'           => 'required',
            'latitude'              => 'required',
            'longitude'             => 'required',
            // 'testimonial_name'      => 'required',
            'description'           => 'required',
            'date_time'             => 'required',
            'language_id'           => 'required',
            'language_name'         => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $institutionalType  = $data['institutionalType'];
            $institutional_name = $data['institutional_name'];
            $user_ecn           = $data['user_ecn'];
            $office_type        = $data['office_type'];
            $type               = $data['type'];
            $institutional_id   = $data['institutional_id'];
            $institutional_contact_id  = $data['institutional_contact_id'];
            $contact_name       = $data['contact_name'];
            $contact_designation= $data['contact_designation'];
            $contact_number     = $data['contact_number'];
            $wa_contact_number  = $data['wa_contact_number'];
            $can_be_shared      = $data['can_be_shared'];
            // $image_video        = $data['image_video'];
            $latitude           = $data['latitude'];
            $longitude          = $data['longitude'];
            $testimonial_name   = (!empty($data['testimonial_name'])) ? $data['testimonial_name'] : '';
            $description        = $data['description'];
            $date_time          = $data['date_time'];
            $language_id        = $data['language_id'];
            $language_name      = $data['language_name'];

            $testimonial_data = array(
                'institutionalType' => $institutionalType,
                'institutional_name'=> $institutional_name,
                'user_ecn'          => $user_ecn,
                'office_type'       => $office_type,
                'type'              => $type,
                'institutional_id'  => $institutional_id,
                'institutional_contact_id' => $institutional_contact_id,
                'contact_name'      => $contact_name,
                'contact_designation'   => $contact_designation,
                'contact_number'    => $contact_number,
                'wa_contact_number' => $wa_contact_number,
                'can_be_shared'     => $can_be_shared,
                'latitude'          => $latitude,
                'longitude'         => $longitude,
                'testimonial_name'  => $testimonial_name,
                'description'       => $description,
                'date_time'         => $date_time,
                'language_id'       => $language_id,
                'language_name'     => $language_name,
                'status'            => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s')
            );
            $InstitutionalTestimonial = InstitutionalTestimonial::firstOrNew($testimonial_data);
            $InstitutionalTestimonial = $InstitutionalTestimonial->save();

            $institutional_testimonial_id = InstitutionalTestimonial::all()->last()->institutional_testimonial_id;

            if($request->file('image_video')) {
                $file=$request->file('image_video');
                if(!empty($file)) {
                    //s3Upload
                    $fileName= "Tea_".time().rand(111,999).".".$file->getClientOriginalExtension();
                    // $sTempFileName = 'assets/tea_estate_testimonial/' . $fileName;
                    $sTempFileName = 'assets/testimonials/' . $fileName;
                    Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                    /*$extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                    $fileName = "Tea_" . md5(time() . rand()) . str_replace($file->getClientOriginalName(), '', $file->getClientOriginalName()) . '.' . $extension;
                    $sTempFileName = 'assets/testimonials/' . $fileName;
                    Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');*/
                    InstitutionalTestimonial::where('institutional_testimonial_id',$institutional_testimonial_id)
                        ->update([
                            'image_video'     => $sTempFileName,
                            'updated_at'=> date('Y-m-d H:i:s')
                        ]);
                }
            }
            $data = InstitutionalTestimonial::where('institutional_testimonial_id',$institutional_testimonial_id)->get();

            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'Testimonial Successfully Submitted!',
                'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                'data' => $data
            ]);
        }
    }

    public function testimonialListInstitutional($updated_date=0) {
        $crr_date    = date('Y-m-d H:i:s');
        $Testimonial = InstitutionalTestimonial::where('status',1);
        if($updated_date != 0) {
            $Testimonial->where('updated_at','>=', $updated_date);
        }
        $TestimonialData = $Testimonial->get();

        return $this->respond([
            'status'    => 'success',
            'status_code'   => $this->getStatusCode(),
            'message'   => 'Testimonial List Found!',
            'crr_date'  => $crr_date,
            'data'      => $TestimonialData
        ]);
    }

    public function getRatingTestimonialList($updated_date=0) {
        $crr_date    = date('Y-m-d H:i:s');
        $Testimonial = Testimonial::join('testimonial_rating','testimonial_rating.testimonial_id','=','testimonials.self_participation_id')->where('testimonial_rating.rating','>=',4)->where('testimonials.status',1);
        if($updated_date != 0) {
            $Testimonial->where('testimonial_rating.created_at','>=', $updated_date);
        }
        $TestimonialData = $Testimonial->select('testimonial','mobile','farmer','description','uploaded_file','product_id','crop_id','crop_ids','product_ids','status','rating','testimonial_id','testimonials.created_at')->groupBy('testimonial_rating.testimonial_id')->get();

        return $this->respond([
            'status'    => 'success',
            'status_code'   => $this->getStatusCode(),
            'message'   => 'Testimonial List Found!',
            'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
            'crr_date'  => $crr_date,
            'data'      => $TestimonialData
        ]);
    }

    public function getTeamFlecn($user_id, $user_type) {
        if($user_type == "zonal") {
            $query = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM users WHERE zonal_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "regional") {
            $query = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM users WHERE regional_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "sales_regional") {
            $query = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM users WHERE sales_regional_manager_id='".$user_id."' AND status='1'";
        } else if(($user_type == "area") || ($user_type == "AREA")) {
            $query = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM users WHERE area_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "sales_area") {
            $query = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM users WHERE sales_area_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "marketing_supervisor") {
            $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->select('sales_regional_manager_id')->get();
            $rsm_ids = array();
            foreach ($marketing_supervisor_rsm as $key => $value) {
                $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
            }
            $imp_rsm_ids = implode("','", $rsm_ids);
            $query = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM users WHERE sales_regional_manager_id IN ('".$imp_rsm_ids."') AND status='1'";
            // echo $query; exit(); 
        } else {
            return 0;
        }
        $user = DB::select(DB::raw($query));
        return $user;
    }

    public function getAllTeamFlecn($user_id, $user_type) {
        if($user_type == "zonal") {
            $query = "SELECT ECN FROM users WHERE zonal_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "regional") {
            $query = "SELECT ECN FROM users WHERE regional_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "sales_regional") {
            $query = "SELECT ECN FROM users WHERE sales_regional_manager_id='".$user_id."' AND status='1'";
        } else if(($user_type == "area") || ($user_type == "AREA")) {
            $query = "SELECT ECN FROM users WHERE area_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "sales_area") {
            $query = "SELECT ECN FROM users WHERE sales_area_manager_id='".$user_id."' AND status='1'";
        } else if($user_type == "marketing_supervisor") {
            $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->select('sales_regional_manager_id')->get();
            $rsm_ids = array();
            foreach ($marketing_supervisor_rsm as $key => $value) {
                $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
            }
            $imp_rsm_ids = implode("','", $rsm_ids);
            $query = "SELECT ECN FROM users WHERE sales_regional_manager_id IN ('".$imp_rsm_ids."') AND status='1'";
        } else {
            return 0;
        }
        $user = DB::select(DB::raw($query));
        $user_ecns = array();
        foreach ($user as $key => $value) {
            $user_ecns[] = $user[$key]->ECN;
        }
        return $user_ecns;
    }

    public function createDistributorAdvisor(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'state_id'      => 'required',
            'district_id'   => 'required',
            'territory_id'  => 'required',
            'market_id'     => 'required',
            'distributor_name'  => 'required',
            'distributor_type'  => 'required',
            'distributor_firm_name' => 'required',
            'mobile'        => 'required',
            'mobile2'       => 'required',
            'address'       => 'required',
            'pincode'       => 'required',
            'turnover'      => 'required',
            'crope'         => 'required',
            'brands'        => 'required',
            'is_distributor'=> 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $user_name    = $user_details[0]->manager_name;

                $state_id       = $data['state_id'];
                $district_id    = $data['district_id'];
                $territory_id   = $data['territory_id'];
                $market_id      = $data['market_id'];
                $distributor_name   = $data['distributor_name'];
                $distributor_type   = $data['distributor_type'];
                $distributor_firm_name  = $data['distributor_firm_name'];
                $mobile         = $data['mobile'];
                $mobile2        = $data['mobile2'];
                $address        = $data['address'];
                $pincode        = $data['pincode'];
                $turnover       = $data['turnover'];
                $crope          = $data['crope'];
                $brands         = $data['brands'];
                $is_distributor = $data['is_distributor'];
                // $is_visited     = $data['is_visited'];

                $distributor_advisor_data = array(
                    'user_id'       => $user_id,
                    'user_type'     => $user_type,
                    'user_name'     => $user_name,
                    'user_ecn'      => $user_ecn,
                    'state_id'      => $state_id,
                    'district_id'   => $district_id,
                    'territory_id'  => $territory_id,
                    'market_id'     => $market_id,
                    'distributor_name'  => $distributor_name,
                    'distributor_type'  => $distributor_type,
                    'distributor_firm_name'  => $distributor_firm_name,
                    'mobile'        => $mobile,
                    'mobile2'       => $mobile2,
                    'address'       => $address,
                    'pincode'       => $pincode,
                    'turnover'      => $turnover,
                    'crope'         => $crope,
                    'brands'        => $brands,
                    'is_distributor'=> $is_distributor,
                    'is_visited'    => 0,
                    'status'        => 1,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                );
                // return $distributor_advisor_data;
                $DistributorAdvisor = DistributorAdvisor::firstOrNew($distributor_advisor_data);
                $DistributorAdvisor = $DistributorAdvisor->save();
                $distributor_advisor_id = DistributorAdvisor::all()->last()->distributor_advisor_id;

                $data = DistributorAdvisor::where('distributor_advisor_id',$distributor_advisor_id)->get();

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Distributor Advisor Successfully Submitted!',
                    'data' => $data
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function getDistributorAdvisorList(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_date' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $tea_estate = array();
                $addQry = '1=1 AND ';
                $updated_date = $data['updated_date'];
                if($updated_date != 0) {
                    $addQry .= 'da.updated_at >= "'.$updated_date.'" AND';
                }
                $distributor_query = " SELECT 
                            da.distributor_advisor_id,
                            da.user_id,
                            da.user_type,
                            da.user_ecn,
                            da.user_name,
                            s.state_id,
                            s.state,
                            d.district_id,
                            d.district,
                            t.territory_id,
                            t.territory,
                            m.market_id,
                            m.market,
                            da.distributor_name,
                            da.distributor_type,
                            da.distributor_firm_name,
                            da.mobile,
                            da.mobile2,
                            da.address,
                            da.pincode,
                            da.turnover,
                            da.crope,
                            da.brands,
                            da.is_distributor,
                            da.is_visited,
                            da.status,
                            da.created_at,
                            da.updated_at
                        FROM distributor_advisor da
                        LEFT JOIN states s ON da.state_id=s.state_id
                        LEFT JOIN districts d ON da.district_id=d.district_id
                        LEFT JOIN territories t ON da.territory_id=t.territory_id
                        LEFT JOIN markets m ON da.market_id=m.market_id
                        WHERE $addQry da.status='1' AND da.user_id='".$user_id."' AND da.user_type='".$user_type."'
                        ORDER BY da.distributor_advisor_id ASC";
                $distributor_detail = DB::select(DB::raw($distributor_query));

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Distributor List Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data' => $distributor_detail
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function addDistributorArrivalDeparture(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'              => 'required|string',
            'distributor_advisor_id'=> 'required|string',
            'arrival_image'         => 'required|mimes:jpg,png,jpeg',
            'arrival_time'          => 'required',
            // 'arrival_comment'       => 'required|string',
            'arrival_latitude'      => 'required',
            'arrival_longitude'     => 'required',
            'departure_time'        => 'required',
            'departure_image'       => 'required|mimes:jpg,png,jpeg',
            // 'departure_comment'     => 'required|string'
            'departure_latitude'    => 'required',
            'departure_longitude'   => 'required',
            'duration'              => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $user_name    = $user_details[0]->manager_name;

                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                $arrival_sTempFileName   = '';
                $departure_sTempFileName = '';
                $distributor_advisor_id  = $data['distributor_advisor_id'];
                $arrival_time   = $data['arrival_time'];
                $arrival_comment= (!empty($data['arrival_comment'])) ? $data['arrival_comment'] : '';
                $arrival_latitude    = $data['arrival_latitude'];
                $arrival_longitude   = $data['arrival_longitude'];
                $departure_time      = $data['departure_time'];
                $departure_comment   = (!empty($data['departure_comment'])) ? $data['departure_comment'] : '';
                $departure_latitude  = $data['departure_latitude'];
                $departure_longitude = $data['departure_longitude'];
                $duration            = $data['duration'];

                $arrival_file = $request->file('arrival_image');
                if ($request->hasFile('arrival_image')) {
                    if ($request->file('arrival_image')->isValid()) {
                        //s3Upload
                        $extension = pathinfo($arrival_file->getClientOriginalName(), PATHINFO_EXTENSION);
                        $arrival_fileName = "A" . md5(time() . rand()) . str_replace($arrival_file->getClientOriginalName(), '', $arrival_file->getClientOriginalName()) . '.' . $extension;
                        $arrival_sTempFileName = 'assets/distributor_advisor/'.$arrival_fileName;
                        Storage::disk('s3')->put($arrival_sTempFileName, fopen($arrival_file, 'r+'),'public');
                        //
                    }
                }
                if ($request->hasFile('departure_image')) {
                    if ($request->file('departure_image')->isValid()) {
                        $departure_file = $request->file('departure_image');
                        //s3Upload
                        $departure_extension = pathinfo($departure_file->getClientOriginalName(), PATHINFO_EXTENSION);
                        $departure_fileName = "A" . md5(time() . rand()) . str_replace($departure_file->getClientOriginalName(), '', $departure_file->getClientOriginalName()) . '.' . $departure_extension;
                        $departure_sTempFileName = 'assets/distributor_advisor/'.$departure_fileName;
                        Storage::disk('s3')->put($departure_sTempFileName, fopen($departure_file, 'r+'),'public');
                        //
                    }
                }
                // $atp_check = DistributorAdvisorATP::where('distributor_advisor_id', $distributor_advisor_id)->where('user_id', $user_id)->where('user_type', $user_type)->first();
                $crr_date  = date('Y-m-d 00:00:00');
                $atp_check = DistributorAdvisorATP::where('distributor_advisor_id', $distributor_advisor_id)->where('user_id', $user_id)->where('user_type', $user_type)->where('created_at','>=',$crr_date)->first();
                if ($atp_check) {
                    $distributor_advisor_atp_id = $atp_check->distributor_advisor_atp_id;
                    $update_atp = DistributorAdvisorATP::where([
                        'distributor_advisor_atp_id' => $distributor_advisor_atp_id,
                        'distributor_advisor_id' => $distributor_advisor_id
                    ])->update([
                        'user_id'           => $user_id,
                        'user_type'         => $user_type,
                        'user_name'         => $user_name,
                        'user_ecn'          => $user_ecn,
                        'arrival_time'      => $arrival_time,
                        'arrival_image'     => $arrival_sTempFileName,
                        'arrival_comment'   => $arrival_comment,
                        'arrival_latitude'  => $arrival_latitude,
                        'arrival_longitude' => $arrival_longitude,
                        'departure_time'    => $departure_time,
                        'departure_comment' => $departure_comment,
                        'departure_image'   => $departure_sTempFileName,
                        'departure_latitude'=> $departure_latitude,
                        'departure_longitude' => $departure_longitude,
                        'is_arrival_departure'=> 1,
                        'duration'          => $duration,
                        'updated_at'        => date('Y-m-d H:i:s')
                    ]);
                    if ($update_atp) {
                        $data = DistributorAdvisorATP::where('distributor_advisor_atp_id',$distributor_advisor_atp_id)->get();
                        return $this->respond([
                            'status'  => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Distributor ATP Data Updated Successfully!',
                            'data'    => $data
                        ]);
                    } else {
                        return $this->respondNotFound("Something went wrong. Please Try Again!!");
                    }
                } else {
                    // return $this->respondNotFound("Invalid ATP !!");
                    $distributor_data = array(
                        'distributor_advisor_id' => $distributor_advisor_id,
                        'user_id'           => $user_id,
                        'user_type'         => $user_type,
                        'user_name'         => $user_name,
                        'user_ecn'          => $user_ecn,
                        'arrival_time'      => $arrival_time,
                        'arrival_image'     => $arrival_sTempFileName,
                        'arrival_comment'   => $arrival_comment,
                        'arrival_latitude'  => $arrival_latitude,
                        'arrival_longitude' => $arrival_longitude,
                        'departure_time'    => $departure_time,
                        'departure_comment' => $departure_comment,
                        'departure_image'   => $departure_sTempFileName,
                        'departure_latitude'=> $departure_latitude,
                        'departure_longitude' => $departure_longitude,
                        'is_arrival_departure'=> 1,
                        'duration'          => $duration,
                        'status'            => 1,
                        'created_at'        => date('Y-m-d H:i:s'),
                        'updated_at'        => date('Y-m-d H:i:s')
                    );
                    $DistributorAdvisorATP = DistributorAdvisorATP::firstOrNew($distributor_data);
                    $DistributorAdvisorATP = $DistributorAdvisorATP->save();

                    $distributor_advisor_atp_id = DistributorAdvisorATP::all()->last()->distributor_advisor_atp_id;
                    $data = DistributorAdvisorATP::where('distributor_advisor_atp_id',$distributor_advisor_atp_id)->get();
                    DistributorAdvisor::where([
                        'distributor_advisor_id' => $distributor_advisor_id
                    ])->update([
                        'is_visited' => 1,'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    return $this->respond([
                        'status'  => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Distributor ATP Data Added Successfully!',
                        'data'    => $data
                    ]);
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function distributorArrivalDepartureHistory(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_date' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $tea_estate = array();
                $addQry = '1=1 AND ';
                $updated_date = $data['updated_date'];
                if($updated_date != 0) {
                    $addQry .= 'daa.updated_at >= "'.$updated_date.'" AND';
                }
                $distributor_atp_query = " SELECT 
                            daa.distributor_advisor_atp_id,
                            da.distributor_advisor_id,
                            da.distributor_name,
                            da.distributor_type,
                            da.distributor_firm_name,
                            daa.user_id,
                            daa.user_type,
                            daa.user_ecn,
                            daa.user_name,
                            daa.arrival_image,
                            daa.arrival_time,
                            daa.arrival_comment,
                            daa.arrival_latitude,
                            daa.arrival_longitude,
                            daa.departure_image,
                            daa.departure_time,
                            daa.departure_comment,
                            daa.departure_latitude,
                            daa.departure_longitude,
                            daa.duration,
                            daa.is_arrival_departure,
                            daa.status,
                            daa.created_at,
                            daa.updated_at
                        FROM distributor_advisor_atp daa
                        LEFT JOIN distributor_advisor da ON daa.distributor_advisor_id=da.distributor_advisor_id
                        WHERE $addQry daa.status='1' AND daa.user_id='".$user_id."' AND daa.user_type='".$user_type."'
                        GROUP BY daa.distributor_advisor_atp_id
                        ORDER BY daa.distributor_advisor_atp_id ASC";
                // return $distributor_atp_query;
                $distributor_atp_detail = DB::select(DB::raw($distributor_atp_query));

                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Distributor Arival Departure List Found',
                    'base_url'  => $base_url,
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data' => $distributor_atp_detail
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function listdistricts($state_id) {
        if($state_id) {
            $districts = District::where('state_id',$state_id)->where('status',1)->get();
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'District List Found!',
                'data' => $districts
            ]);
        } else {
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'state_id is required!',
                'data' => array()
            ]);
        }
    }


    public function getAllterritories(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;
                $updated_date = $data['updated_at'];

                $territories = territories::where('status',1);
                if($updated_date != 0) {
                    $territories->where('updated_at','>=',$updated_date);
                }
                $territories_data = $territories->groupBy('territory_id')->get();

                return $this->respond([
                    'status'    => 'success',
                    'status_code'   => $this->getStatusCode(),
                    'message'   => 'Territories List Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $territories_data
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }


    public function listterritories($district_id) {
        if($district_id) {
            $territories = territories::where('district_id',$district_id)->where('status',1)->get();
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'Territories List Found!',
                'data' => $territories
            ]);
        } else {
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'district_id is required!',
                'data' => array()
            ]);
            
        }
    }

    public function listmarket($territory_id) {
        if($territory_id) {
            $markets = markets::where('territory_id',$territory_id)->where('status',1)->get();
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'Market List Found!',
                'data' => $markets
            ]);
        } else {
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'territory_id is required!',
                'data' => array()
            ]);
        }
    }

    public function createCircular(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'          => 'required|string',
            'circular_name'     => 'required',
            'published_date'    => 'required',
            'institutionalType' => 'required',
            'state_id'          => 'required',
            'district_id'       => 'required',
            'contact_ids'       => 'required',
            'whatsapp_message'  => 'required',
            'circular_data'     => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $user_name    = $user_details[0]->manager_name;

                $circular_name      = $data['circular_name'];
                $published_date     = $data['published_date'];
                $institutionalType  = $data['institutionalType'];
                $state_id           = $data['state_id'];
                $district_id        = $data['district_id'];
                $contact_ids        = $data['contact_ids'];
                $whatsapp_message   = $data['whatsapp_message'];
                $language_id        = (!empty($data['language_id'])) ? $data['language_id'] : '';
                $language_name      = (!empty($data['language_name'])) ? $data['language_name'] : '';
                $circular_data      = $data['circular_data'];

                if(!empty($data['circular_id'])) {
                    $circular_id    = $data['circular_id'];
                    $updateCircular = Circular::where([
                        'circular_id'       => $circular_id
                    ])->update([
                        'user_id'           => $user_id,
                        'user_type'         => $user_type,
                        'user_name'         => $user_name,
                        'user_ecn'          => $user_ecn,
                        'circular_name'     => $circular_name,
                        'published_date'    => $published_date,
                        'institutionalType' => $institutionalType,
                        'state_id'          => $state_id,
                        'district_id'       => $district_id,
                        'language_id'       => $language_id,
                        'language_name'     => $language_name,
                        'contact_ids'       => $contact_ids,
                        'whatsapp_message'  => $whatsapp_message,
                        'updated_at'        => date('Y-m-d H:i:s')
                    ]);
                    $circular_data_obj = json_decode($circular_data,true);
                    $CircularTestimonial = CircularTestimonial::where('circular_id',$circular_id)->get();
                    if(count($CircularTestimonial)>0) {
                        CircularTestimonial::where('circular_id',$circular_id)->delete();
                    }
                    foreach ($circular_data_obj as $key => $circulars) {
                        $testimonial_id      = $circulars['testimonial_id'];
                        $circular_testimonial = array(
                            'circular_id'   => $circular_id,
                            'testimonial_id'=> $testimonial_id,
                            'description'   => $circulars['text'],
                            'status'        => 1,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        );
                        $CircularTestimonial = CircularTestimonial::firstOrNew($circular_testimonial);
                        $CircularTestimonial = $CircularTestimonial->save();
                    }
                    $circular_path = $circular_name.'-'.$circular_id;
                    $enc_circular_path = base64_encode($circular_path);
                    Circular::where(['circular_id'=> $circular_id])->update([
                        'circular_path'     => $enc_circular_path,
                        'updated_at'        => date('Y-m-d H:i:s')
                    ]);
                    $data = Circular::where('circular_id',$circular_id)->get();
                    return $this->respond([
                        'status'    => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message'   => 'Circular Updated Successfully!',
                        'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                        'data'      => $data
                    ]);
                } else {
                    $circular_array = array(
                        'user_id'        => $user_id,
                        'user_type'      => $user_type,
                        'user_ecn'       => $user_ecn,
                        'user_name'      => $user_name,
                        'state_id'       => $state_id,
                        'district_id'    => $district_id,
                        'language_id'    => $language_id,
                        'language_name'  => $language_name,
                        'circular_name'  => $circular_name,
                        'published_date' => $published_date,
                        'institutionalType' => $institutionalType,
                        'contact_ids'    => $contact_ids,
                        'whatsapp_message'  => $whatsapp_message,
                        'created_at'     => date('Y-m-d H:i:s'),
                        'updated_at'     => date('Y-m-d H:i:s')
                    );
                    $Circular = Circular::firstOrNew($circular_array);
                    $Circular = $Circular->save();
                    $circular_id = Circular::all()->last()->circular_id;

                    $circular_data_obj = json_decode($circular_data,true);
                    foreach ($circular_data_obj as $key => $circulars) {
                        $circular_testimonial = array(
                            'circular_id'   => $circular_id,
                            'testimonial_id'=> $circulars['testimonial_id'],
                            'description'   => $circulars['text'],
                            'status'        => 1,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        );
                        $CircularTestimonial = CircularTestimonial::firstOrNew($circular_testimonial);
                        $CircularTestimonial = $CircularTestimonial->save();
                    }
                    $circular_path = $circular_name.'-'.$circular_id;
                    $enc_circular_path = base64_encode($circular_path);
                    Circular::where(['circular_id'=> $circular_id])->update([
                        'circular_path'     => $enc_circular_path,
                        'updated_at'        => date('Y-m-d H:i:s')
                    ]);
                    $data = Circular::where('circular_id',$circular_id)->get();
                    return $this->respond([
                        'status'    => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message'   => 'Circular Added Successfully!',
                        'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                        'data'      => $data
                    ]);
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function getCircular(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            // 'institutionalType' => 'required',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $circulars = array();
                $base_url  = url('/'); 
                $addQry = '1=1 AND ';
                $updated_date = $data['updated_at'];

                $Circular = Circular::where('user_id',$user_id)->where('user_type',$user_type);
                if(!empty($data['institutionalType'])) {
                    $institutionalType = $data['institutionalType'];
                    $Circular->where('institutionalType',$institutionalType);
                    $addQry .= 'c.institutionalType = "'.$institutionalType.'" AND ';
                }
                if(!empty($data['language_id'])) {
                    $language_id = $data['language_id'];
                    $Circular->where('language_id',$language_id);
                    $addQry .= 'c.language_id = "'.$language_id.'" AND ';
                }
                if($updated_date != 0) {
                    $addQry .= 'c.updated_at > "'.$updated_date.'" AND ';
                    $Circular->where('updated_at','>=', $updated_date);
                }
                $circular_data = $Circular->get();
                $a=0;
                foreach ($circular_data as $key => $value) {
                    $circular_id = $value->circular_id;
                    $contact_ids = $value->contact_ids;
                    $institutionalType = $value->institutionalType;
                    if($contact_ids){
                        $contact_id = explode(',', $value->contact_ids);
                    } else {
                        $contact_id = array();
                    }
                    $testimonial_qry = "SELECT count(circular_testimonial_id) as total,GROUP_CONCAT(testimonial_id) AS testimonial_ids FROM circular_testimonial WHERE circular_id='".$circular_id."' AND status=1 LIMIT 0,1 ";
                    $institutional_testimonial_ids = DB::select(DB::raw($testimonial_qry));
                    // return $institutional_testimonial_ids[0]->total;

                    $circular_query = " SELECT 
                                c.circular_id,
                                c.user_id,
                                c.user_type,
                                c.user_ecn,
                                c.user_name,
                                s.state_id,
                                s.state,
                                d.district_id,
                                d.district,
                                c.language_id,
                                c.language_name,
                                c.circular_name,
                                CONCAT('".$base_url."/circular/', c.circular_path) as circular_path,
                                c.published_date,
                                c.institutionalType,
                                c.whatsapp_message,
                                c.status,
                                c.created_at,
                                c.updated_at,
                                '' as circular_contacts,
                                '' as circular_data
                            FROM circular c
                            LEFT JOIN states s ON c.state_id=s.state_id
                            LEFT JOIN districts d ON c.district_id=d.district_id
                            WHERE $addQry c.user_id='".$user_id."' AND c.user_type='".$user_type."' AND c.circular_id='".$circular_id."'
                            ORDER BY c.circular_id ASC";
                    $circular_detail = DB::select(DB::raw($circular_query));
                    // $circulars[] = $circular_detail;
                    if(count($contact_id) > 0) {
                        if($institutionalType==1) {
                            $circular_contact_data = TeaEstateContact::whereIn('tea_estate_contact_id',$contact_id)->where('status',1);
                        } else if($institutionalType==2){
                            $circular_contact_data = SugarmillContact::whereIn('sugarmill_contact_id',$contact_id)->where('status',1);
                        } else {
                            $circular_contact_data = InfluencerContact::whereIn('influencer_contact_id',$contact_id)->where('status',1);
                        }
                        
                        /*if($updated_date != 0) {
                            $circular_contact_data->where('updated_at','>=', $updated_date);
                        }*/
                        $circular_contact = $circular_contact_data->get();
                        if(count($circular_contact)>0) {
                            $circular_detail[0]->circular_contacts = $circular_contact;
                            // array_push($circulars,$circular_detail[0]);
                        } else {
                            $circular_detail[0]->circular_contacts = array();
                        }
                    } else {
                        $circular_detail[0]->circular_contacts = array();
                    }
                    if($institutional_testimonial_ids[0]->total > 0) {
                        $institutional_testimonial_ids = $institutional_testimonial_ids[0]->testimonial_ids;
                        $institutional_testimonial_id = explode(',', $institutional_testimonial_ids);
                        if($institutionalType == 3) {
                            /*$testimonial_data = Testimonial::whereIn('self_participation_id',$institutional_testimonial_id)->where('status',1)->select('self_participation_id as institutional_testimonial_id',DB::raw("'3' as institutionalType"),'mobile as contact_number','mobile as wa_contact_number',DB::raw("'1' as can_be_shared"),'uploaded_file as image_video','testimonial as testimonial_name','description',DB::raw("'' as language_id"),DB::raw("'' as language_name"),'status','created_at','created_at as updated_at')->get();
                            $circular_testimonial = $testimonial_data;*/
                            $circular_testimonial = Testimonial::join('circular_testimonial','circular_testimonial.testimonial_id','=','testimonials.self_participation_id')
                            ->whereIn('testimonials.self_participation_id',$institutional_testimonial_id)
                            ->where('circular_testimonial.circular_id',$circular_id)
                            ->groupBy('circular_testimonial.circular_testimonial_id')
                            ->select('testimonials.self_participation_id  as institutional_testimonial_id',DB::raw("'3' as institutionalType"),'testimonials.farmer as testimonial_name','testimonials.mobile as contact_number','testimonials.mobile as wa_contact_number',DB::raw("'1' as can_be_shared"),'testimonials.uploaded_file as image_video','testimonials.description as testimonial_description','testimonials.status as testimonial_status',DB::raw("'' as language_id"),DB::raw("'' as language_name"),'circular_testimonial.circular_testimonial_id','circular_testimonial.circular_id','circular_testimonial.testimonial_id as estate_testimonial_id','circular_testimonial.description','circular_testimonial.status','circular_testimonial.created_at','circular_testimonial.updated_at')->get();
                        } else {
                            // $testimonial_data = InstitutionalTestimonial::join() whereIn('institutional_testimonial_id',$institutional_testimonial_id)->where('status',1);
                            /*if($updated_date != 0) {
                                $testimonial_data->where('updated_at','>=', $updated_date);
                            }*/
                            // $circular_testimonial = $testimonial_data->get();
                            $circular_testimonial = InstitutionalTestimonial::join('circular_testimonial','circular_testimonial.testimonial_id','=','institutional_testimonial.institutional_testimonial_id')
                            ->whereIn('institutional_testimonial.institutional_testimonial_id',$institutional_testimonial_id)
                            ->where('circular_testimonial.circular_id',$circular_id)
                            ->groupBy('circular_testimonial.circular_testimonial_id')
                            ->select('institutional_testimonial.institutional_testimonial_id','institutional_testimonial.testimonial_name','institutional_testimonial.institutional_id','institutional_testimonial.institutional_contact_id','institutional_testimonial.contact_name','institutional_testimonial.contact_designation','institutional_testimonial.contact_number','institutional_testimonial.wa_contact_number','institutional_testimonial.can_be_shared','institutional_testimonial.image_video','institutional_testimonial.description as testimonial_description','institutional_testimonial.date_time','institutional_testimonial.status as testimonial_status','circular_testimonial.circular_testimonial_id','circular_testimonial.circular_id','circular_testimonial.testimonial_id as estate_testimonial_id','circular_testimonial.description','circular_testimonial.status','circular_testimonial.created_at','circular_testimonial.updated_at')->get();
                        }
                        // Log::info($circular_testimonial);

                        if(count($circular_testimonial)>0) {
                            $circular_detail[0]->circular_data = $circular_testimonial;
                        } else {
                            $circular_detail[0]->circular_data = array();
                        }
                    } else {
                        $circular_detail[0]->circular_data = array();
                    }
                    array_push($circulars,$circular_detail[0]);
                }
                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Circular List Found',
                    'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $circulars
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }


    public function getAllContacts(Request $request) {
        $data = $request->all();
        $rules = array(
            'updated_date' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $crr_date = date('Y-m-d H:i:s');
            $TeaEstateContact = TeaEstateContact::where('status',1);
            if(!empty($data['tea_estate_id'])) {
                $tea_estate_id = $data['tea_estate_id'];
                $TeaEstateContact->where('tea_estate_id',$tea_estate_id);
            }
            $updated_date = $data['updated_date'];
            if($updated_date != 0) {
                $TeaEstateContact->where('updated_at','>=', $updated_date);
            }
            $TeaEstateContactList = $TeaEstateContact->get();
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'Contact List Found!',
                'crr_date' => $crr_date,
                'data' => $TeaEstateContactList
            ]);
        }
    }

    public function getAllSugarmillContacts(Request $request) {
        $data = $request->all();
        $rules = array(
            'updated_date' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $crr_date = date('Y-m-d H:i:s');
            $SugarmillContact = SugarmillContact::where('status',1);
            if(!empty($data['sugarmill_id'])) {
                $sugarmill_id = $data['sugarmill_id'];
                $SugarmillContact->where('sugarmill_id',$sugarmill_id);
            }
            $updated_date = $data['updated_date'];
            if($updated_date != 0) {
                $SugarmillContact->where('updated_at','>=', $updated_date);
            }
            $SugarmillContactList = $SugarmillContact->get();
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'Contact List Found!',
                'crr_date' => $crr_date,
                'data' => $SugarmillContactList
            ]);
        }
    }

    public function getAllInfluencerContacts(Request $request) {
        $data = $request->all();
        $rules = array(
            'updated_date' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $crr_date = date('Y-m-d H:i:s');
            $InfluencerContact = InfluencerContact::where('status',1);
            if(!empty($data['influencer_id'])) {
                $influencer_id = $data['influencer_id'];
                $InfluencerContact->where('influencer_id',$influencer_id);
            }
            $updated_date = $data['updated_date'];
            if($updated_date != 0) {
                $InfluencerContact->where('updated_at','>=', $updated_date);
            }
            $InfluencerContactList = $InfluencerContact->get();
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'Contact List Found!',
                'crr_date' => $crr_date,
                'data' => $InfluencerContactList
            ]);
        }
    }

    /* Influencers */
    public function createInfluencer(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'          => 'required|string',
            'office_type'       => 'required',
            'influencer_type'   => 'required',
            'name'              => 'required',
            'state_id'          => 'required',
            'district_id'       => 'required',
            'address'           => 'required',
            'fl_name'           => 'required',
            'fl_type'           => 'required',
            'fl_ecn'            => 'required',
            'image'             => 'required',
            'contact_data'      => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $user_name    = $user_details[0]->manager_name;

                $office_type        = $data['office_type'];
                $influencer_type    = $data['influencer_type'];
                $parent_influencer_id = (!empty($data['parent_influencer_id'])) ? $data['parent_influencer_id'] : 0;
                $name               = $data['name'];
                $turn_over          = (!empty($data['turn_over'])) ? $data['turn_over'] : '';
                $land_area          = (!empty($data['land_area'])) ? $data['land_area'] : '';
                $influencer_company_id  = (!empty($data['influencer_company_id'])) ? $data['influencer_company_id'] : 0;
                // $parent_influencer_id = (!empty($data['parent_influencer_id'])) ? $data['parent_influencer_id'] : 0;
                /*if($parent_influencer_id == 0) {
                    $influencer_category_id = $this->getInfluencerCategory($turn_over,$land_area,$influencer_company_id);
                } else {
                    $parent_influencer = Influencer::where('influencer_id',$parent_influencer_id)->select('influencer_id','influencer_category_id','influencer_company_id')->get();
                    if(count($parent_influencer)>0) {
                        $influencer_category_id = $parent_influencer[0]->influencer_category_id;
                        $influencer_company_ids = $parent_influencer[0]->influencer_company_id;
                    } else {
                        $influencer_category_id = 1;
                        $influencer_company_ids = 1;
                    }
                }
                $influencer_company_id = ($influencer_company_id == 0) ? $influencer_company_ids : $influencer_company_id;*/
                $influencer_category_id = 0;
                $state_id           = $data['state_id'];
                $district_id        = $data['district_id'];
                $address            = $data['address'];
                $fl_name            = $data['fl_name'];
                $fl_ecn             = $data['fl_ecn'];
                $fl_type            = $data['fl_type'];
                $latitude           = ($data['latitude']) ? $data['latitude'] : '';
                $longitude          = ($data['longitude']) ? $data['longitude'] : '';
                $contact_data       = $data['contact_data'];

                $influencer = Influencer::where('office_type',$office_type)->where('influencer_type',$influencer_type)->where('influencer_company_id',$influencer_company_id)->where('state_id',$state_id)->where('district_id',$district_id)->where('addedby_ecn',$user_ecn)->where('addedby_id',$user_id)->where('name',$name)->get();

                if (isset($influencer) && (count($influencer)==0)) {                    
                    $influencer_data = array(
                        'office_type'       => $office_type,
                        'influencer_type'   => $influencer_type,
                        'parent_influencer_id' => $parent_influencer_id,
                        'name'              => $name,
                        'turn_over'         => $turn_over,
                        'land_area'         => $land_area,
                        'influencer_company_id' => $influencer_company_id,
                        'influencer_category_id'=> $influencer_category_id,
                        'state_id'          => $state_id,
                        'district_id'       => $district_id,
                        'address'           => $address,
                        'latitude'          => $latitude,
                        'longitude'         => $longitude,
                        'fl_name'           => $fl_name,
                        'fl_ecn'            => $fl_ecn,
                        'fl_type'           => $fl_type,
                        'addedby_id'        => $user_id,
                        'addedby_name'      => $user_name,
                        'addedby_ecn'       => $user_ecn,
                        'addedby_type'      => $user_type,                    
                        'status'            => 1,
                        'created_at'        => date('Y-m-d H:i:s'),
                        'updated_at'        => date('Y-m-d H:i:s')
                    );
                    // return $influencer_data;
                    $Influencer = Influencer::firstOrNew($influencer_data);
                    $Influencer = $Influencer->save();
                    $influencer_id = Influencer::all()->last()->influencer_id;

                    if($request->file('image')) {
                        $file=$request->file('image');
                        if(!empty($file)) {
                            //s3Upload
                            $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                            $sTempFileName = 'assets/influencer/' . $fileName;
                            Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            Influencer::where('influencer_id',$influencer_id)
                                ->update([
                                    'image'     => $sTempFileName,
                                    'updated_at'=> date('Y-m-d H:i:s')
                                ]);
                        }
                    }
                    $contact_data_obj = json_decode($contact_data,true);
                    foreach ($contact_data_obj as $key => $contact) {
                        $influencer_contact = array(
                            'influencer_id' => $influencer_id,
                            'state_id'      => $state_id,
                            'district_id'   => $district_id,
                            'level'         => $contact['level'],
                            'designation'   => $contact['designation'],
                            'name'          => $contact['name'],
                            'wa_number'     => $contact['wa_number'],
                            'mobile_number' => $contact['mobile_number'],
                            'email'         => $contact['email'],
                            'status'        => 1,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        );
                        $InfluencerContact = InfluencerContact::firstOrNew($influencer_contact);
                        $InfluencerContact = $InfluencerContact->save();
                    }
                    $data = Influencer::where('influencer_id',$influencer_id)->get();

                    return $this->respond([
                        'status'    => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message'   => 'Influencer Successfully Submitted!',
                        'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                        'data'      => $data
                    ]);
                } else {
                    $influencer_id = $influencer[0]->influencer_id;
                    $data = Influencer::where('influencer_id',$influencer_id)->get();
                    return $this->respond([
                        'status'    => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message'   => 'Influencer Already Added!',
                        'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                        'data'      => $data
                    ]);
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    private function getInfluencerCategory($turn_over,$land_holding,$influencer_company_id) {
        if(($turn_over=='') || ($land_holding=='') || ($influencer_company_id=='')){
            return 0;
        } else {
            $query_turn_over = "SELECT turn_over_point FROM influencer_category WHERE turn_over<=".$turn_over." AND status=1";
            $turn_over_data = DB::select(DB::raw($query_turn_over));
            if(count($turn_over_data)==0) {
                $query_turn_over = "SELECT turn_over_point FROM influencer_category WHERE company_category='C' AND status=1";
                $turn_over_data = DB::select(DB::raw($query_turn_over));
            }
            $turn_over_point = $turn_over_data[0]->turn_over_point;

            $query_land_holding = "SELECT land_holding_point FROM influencer_category WHERE land_holding<=".$land_holding." AND status=1";
            $land_holding_data = DB::select(DB::raw($query_land_holding));
            if(count($land_holding_data)==0) {
                $query_land_holding = "SELECT land_holding_point FROM influencer_category WHERE company_category='C' AND status=1";
                $land_holding_data = DB::select(DB::raw($query_land_holding));
            }
            $land_holding_point = $land_holding_data[0]->land_holding_point;

            $query_company_point = "SELECT company_point FROM influencer_category WHERE influencer_company_id=".$influencer_company_id." AND status=1";
            $company_point_data = DB::select(DB::raw($query_company_point));
            if(count($company_point_data)==0) {
                $query_company_point = "SELECT company_point FROM influencer_category WHERE company_category='C' AND status=1";
                $company_point_data = DB::select(DB::raw($query_company_point));
            }
            $company_point = $company_point_data[0]->company_point;
            $total_point = $turn_over_point+$land_holding_point+$company_point;
            // echo "=>".$turn_over_point."-".$land_holding_point."-".$company_point; exit;
            $total_point = round($total_point);

            $query_category = "SELECT influencer_category_id FROM influencer_category WHERE category_point<=".$total_point." AND status=1";
            $category_point_data = DB::select(DB::raw($query_category));
            if(count($category_point_data)==0) {
                $query_category = "SELECT influencer_category_id FROM influencer_category WHERE company_category='C' AND status=1";
                $category_point_data = DB::select(DB::raw($query_category));
            }
            $influencer_category_id = $category_point_data[0]->influencer_category_id;
            return $influencer_category_id;
        }
    }

    public function getInfluencerTypeDesignationFL(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'updated_date'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $updated_date = $data['updated_date'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id   = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn  = $user[0]->ECN;
                $manager_details = $this->findUserDetails($user_type, $api_token);
                $manager_name = $manager_details[0]->manager_name;
                $fl_ids   = array();
                $fl_ids[] = $user_id;
                if(strtolower($user_type) =='marketing_supervisor') {
                    $fl_ids   = array();
                    $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->select('sales_regional_manager_id')->get();
                    $rsm_ids = array();
                    foreach ($marketing_supervisor_rsm as $key => $value) {
                        $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
                    }
                    $fl_by = 'sales_regional_manager_id';
                    $fl_ids = $rsm_ids;
                } else if(strtolower($user_type) =='zonal') {
                    $fl_by = 'zonal_manager_id';
                } else if(strtolower($user_type) =='regional') {
                    $fl_by = 'regional_manager_id';
                } else if(strtolower($user_type) =='sales_regional') {
                    $fl_by = 'sales_regional_manager_id';
                } else if(strtolower($user_type) =='sales_area') {
                    $fl_by = 'sales_area_manager_id';
                } else if(strtolower($user_type) =='area') {
                    $fl_by = 'area_manager_id';
                } else {
                    $fl_by = 'id';
                }
                $fl_data   = User::whereIn($fl_by,$fl_ids)->where('status',1)->select('id','ECN','name',DB::raw("'".$user_id."' as manager_id"),DB::raw("'".$manager_name."' as manager_name"));
                if($updated_date != 0) {
                    $fl_data->where('updated_at','>=', $updated_date);
                }
                $fl_data_list = $fl_data->groupBy('id')->get();

                $category_data = InfluencerCategory::where('status',1)->select('influencer_category_id','company_category');
                if($updated_date != 0) {
                    $category_data->where('updated_at','>=', $updated_date);
                }
                $category_data_list = $category_data->get();

                $company_data = InfluencerCompany::where('status',1)->select('influencer_company_id','company_type');
                if($updated_date != 0) {
                    $company_data->where('updated_at','>=', $updated_date);
                }
                $company_data_list = $company_data->get();

                $designation_data = InfluencerDesignation::where('status',1)->select('influencer_designation_id','level','designation');
                if($updated_date != 0) {
                    $designation_data->where('updated_at','>=', $updated_date);
                }
                $designation_data_list = $designation_data->get();
                
                $data = array(
                    'category_data' => $category_data_list,
                    'designation_data' => $designation_data_list,
                    'company_data' => $company_data_list,
                    'fl_data' => $fl_data_list
                );

                return $this->respond([
                    'status'   => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'  => 'List Type Designation and FL',
                    'crr_date' => date('Y-m-d H:i:s'),
                    'data'     => $data
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function getListInfluencer(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $influencer = array();
                $addQry     = '1=1 AND ';
                $updated_date = $data['updated_at'];

                if($user_type=='bdo') {
                    $expFlecn = array($user_ecn);
                } else {
                    $getTeamFlecn = $this->getAllTeamFlecn($user_id, $user_type);
                    if(count($getTeamFlecn)>0) {
                        $expFlecn    = $getTeamFlecn;
                    } else {
                        $expFlecn = array();
                    }
                }
                $influencers = Influencer::whereIn('fl_ecn',$expFlecn)->where('fl_type','bdo')->where('status',1);
                if($updated_date != 0) {
                    $influencers->where('updated_at','>=',$updated_date);
                }
                $influencer_datas = $influencers->groupBy('influencer_id')->get();

                $influencer_data = array();
                foreach ($influencer_datas as $key => $value) {
                    $influencer_id = $value->influencer_id;
                    array_push($influencer_data, $influencer_id);
                    if($value->parent_influencer_id != 0) {
                        array_push($influencer_data, $value->parent_influencer_id);
                    }
                }
                $influencer_data = array_unique($influencer_data);

                $a=0;
                foreach ($influencer_data as $key => $value) {
                    $influencer_id = $value;
                    $influencer_query = " SELECT 
                                i.influencer_id,
                                i.office_type,
                                (CASE WHEN i.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type_name,
                                i.influencer_type,
                                (CASE WHEN i.influencer_type='1' THEN 'Influencer' ELSE 'SIG' END) as influencer_type_name,
                                i.parent_influencer_id,
                                i.name,
                                i.turn_over,
                                i.land_area,
                                i.influencer_company_id,
                                ic.company_category,
                                s.state_id,
                                s.state,
                                d.district_id,
                                d.district,
                                i.address,
                                i.latitude,
                                i.longitude,
                                i.image,
                                i.fl_name,
                                i.fl_ecn,
                                i.fl_type,
                                i.status,
                                i.created_at,
                                i.updated_at,
                                '' as contact_data
                            FROM influencer i
                            LEFT JOIN states s ON i.state_id=s.state_id
                            LEFT JOIN districts d ON i.district_id=d.district_id
                            LEFT JOIN influencer_category ic ON i.influencer_category_id=ic.influencer_category_id
                            WHERE $addQry i.status='1' AND i.influencer_id='".$influencer_id."'
                            ORDER BY i.influencer_id ASC";
                    $influencer_detail = DB::select(DB::raw($influencer_query));
                    // $influencer[] = $influencer_detail;
                    if($updated_date != 0) {
                        $influencer_contact = InfluencerContact::where('influencer_id',$influencer_id)->where('updated_at','>=', $updated_date)->where('status',1)->get();
                    } else {
                        $influencer_contact = InfluencerContact::where('influencer_id',$influencer_id)->where('status',1)->get();
                    }
                    if(count($influencer_detail)>0) { 
                        $influencer_detail[0]->contact_data = $influencer_contact;
                        array_push($influencer,$influencer_detail[0]);
                    }
                }
                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Influencer List Found',
                    'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $influencer
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function influencerListing(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $updated_date = $data['updated_at'];
                if($user_type =='bdo') {
                    $expFlecn = array($user_ecn);
                } else {
                    $getTeamFlecn = $this->getAllTeamFlecn($user_id, $user_type);
                    if(count($getTeamFlecn)>0) {
                        $expFlecn    = $getTeamFlecn;
                    } else {
                        $expFlecn = array();
                    }
                }
                $influencer_data = Influencer::whereIn('fl_ecn',$expFlecn)->where('fl_type','bdo')->where('status',1);
                if($updated_date != 0) {
                    $influencer_data->where('updated_at','>=',$updated_date);
                }
                $influencer_data_list = $influencer_data->select('influencer_id','name','influencer_category_id','office_type','influencer_type as type','fl_ecn as user_ecn')->get();

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Influencer List Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data' => $influencer_data_list
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function influencerListingHeadOfc(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;
                $updated_date = $data['updated_at'];
                $influencer_data = Influencer::where('office_type',1)->where('status',1);
                if($updated_date != 0) {
                    $influencer_data->where('updated_at','>=',$updated_date);
                }
                $influencer_data_list = $influencer_data->select('influencer_id','name','influencer_category_id','office_type','influencer_type as type','fl_ecn as user_ecn')->get();

                return $this->respond([
                    'status'        => 'success',
                    'status_code'   => $this->getStatusCode(),
                    'message'       => 'Influencer List Found',
                    'crr_date'      => date('Y-m-d H:i:s'),
                    'data'          => $influencer_data_list
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function addInfluencerContactCompanyBranch(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'       => 'required|string',
            'Influencers_id' => 'required',
            'contact_data'   => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id        = $user[0]->id;
                $user_type      = $user[0]->user_type;
                $user_ecn       = $user[0]->ECN;
                $user_details   = $this->findUserDetails($user_type, $api_token);
                $user_name      = $user_details[0]->manager_name;
                $influencer_id  = $data['Influencers_id'];
                $contact_data   = $data['contact_data'];
                $influencer     = Influencer::where('influencer_id', $influencer_id)->first();
                $state_id       = $influencer->state_id;
                $district_id    = $influencer->district_id;

                $contact_data_obj = json_decode($contact_data,true);
                foreach ($contact_data_obj as $key => $contact) {
                    $influencer_contact = array(
                        'influencer_id' => $influencer_id,
                        'state_id'      => $state_id,
                        'district_id'   => $district_id,
                        'level'         => $contact['level'],
                        'designation'   => $contact['designation'],
                        'name'          => $contact['name'],
                        'wa_number'     => $contact['wa_number'],
                        'mobile_number' => $contact['mobile_number'],
                        'email'         => $contact['email'],
                        'status'        => 1,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s')
                    );
                    $InfluencerContact = InfluencerContact::firstOrNew($influencer_contact);
                    $InfluencerContact = $InfluencerContact->save();
                }
                // $data = InfluencerContact::where('influencer_id',$influencer_id)->get();
                $data = array();

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Influencer Contact Successfully Submitted!',
                    'data' => $data
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function getAllInfluencerContactsById($influencer_id,$updated_date=0) {
        if($influencer_id) {
            $crr_date = date('Y-m-d H:i:s');
            $InfluencerContact = InfluencerContact::where('influencer_id',$influencer_id)->where('status',1);
            if($updated_date != 0) {
                $InfluencerContact->where('updated_at','>=', $updated_date);
            }
            $InfluencerContactList = $InfluencerContact->get();

            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'Influencer Contact List Found!',
                'crr_date' => $crr_date,
                'data' => $InfluencerContactList
            ]);
        } else {
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'influencer_id is required!',
                'data' => array()
            ]);
            
        }
    }

    public function influencerPlanAtp(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'apiToken' => 'required|string',
                'atp_data' => 'required|array'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $planATP = $data['atp_data'];
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id    = $user[0]->id;
                    $user_type  = $user[0]->user_type;
                    $user_ecn   = $user[0]->ECN;
                    $user_details = $this->findUserDetails($user_type, $api_token);
                    $user_name    = $user_details[0]->manager_name;

                    if (count($planATP) > 0) {
                        $valid_user_plan = array();
                        //echo date('Y-m-d\TH:i:s');die;
                        foreach ($planATP as $plan) {
                            $dat = explode('.', $plan['date_time']);
                            // return date('Y-m-d\T00:00:00');
                            if ( $dat[0] < date('Y-m-d\T00:00:00')) {
                                return $this->respondWithError("You can not plan atp on previous date.");
                            }
                        }
                        foreach ($planATP as $plan) {
                            $influencer_category = $plan['influencer_category'];
                            $date_time       = $plan['date_time'];
                            $influencer_id   = $plan['influencer_id'];
                            $ticket_number   = $plan['ticket_number'];
                            $is_manager      = $plan['is_manager'];
                            /*if($is_manager == 0) {
                                $fl_name    = NULL;
                                $fl_ecn     = NULL;
                                $fl_type    = NULL;
                            } else {
                                $influencer = Influencer::where('influencer_id',$influencer_id)->first();
                                $fl_name    = $influencer->fl_name;
                                $fl_ecn     = $influencer->fl_ecn;
                                $fl_type    = $influencer->fl_type;
                            }*/
                            $influencer = Influencer::where('influencer_id',$influencer_id)->first();
                            $fl_name    = $influencer->fl_name;
                            $fl_ecn     = $influencer->fl_ecn;
                            $fl_type    = $influencer->fl_type;
                            $user_plan_atp = array(
                                'date_time'     => $date_time,
                                'fl_name'       => $fl_name,
                                'fl_ecn'        => $fl_ecn,
                                'fl_type'       => $fl_type,
                                'manager_name'  => $user_name,
                                'manager_ecn'   => $user_ecn,
                                'manager_type'  => $user_type,
                                'influencer_category' => $influencer_category,
                                'influencer_id' => $influencer_id,
                                'ticket_number' => $ticket_number,
                                'is_manager'    => $is_manager,
                                'status'        => 1,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s')
                            );
                            $insert_plan_atp = InfluencerATP::firstOrCreate($user_plan_atp);
                            $valid_user_plan[] = $user_plan_atp;
                        }
                        if (count($valid_user_plan) > 0) {
                            return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'ATP Successfully Added!',
                                'data' => $valid_user_plan,
                            ]);
                        } else {
                            return $this->respondWithError("Invalid plan activity!");
                        }
                    } else {
                        return $this->respondWithError("Plan activity not found!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function influencerAtpListing(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_date' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                // $addQry = '1=1 AND ((`manager_ecn` IN ('76543') AND `manager_type`='sales_area') OR (`manager_ecn` IN ('12975') AND `manager_type`='zonal') OR (`manager_ecn` IN ('12974') AND `manager_type`='sales_regional'))';
                $addQry = '1=1 ';
                $addQrySub = '';
                $updated_date = $data['updated_date'];
                if($updated_date != 0) {
                    $addQry .= ' AND ia.updated_at > "'.$updated_date.'"';
                }
                if($user_type == 'bdo') {
                    $addQry .=' AND ia.fl_ecn="'.$user_ecn.'" AND ia.fl_type="'.$user_type.'"';
                } else {
                    $getTeamFlecn = $this->getAllTeamFlecn($user_id, $user_type);
                    if(count($getTeamFlecn)>0) {
                        $expFlecn    = $getTeamFlecn;
                        $impTeamFlecn = "'".implode("','",$expFlecn)."'";
                        $addFlQuery   = '(ia.`fl_ecn` IN ('.$impTeamFlecn.') AND ia.`fl_type`="bdo") OR ';
                    } else {
                        $addFlQuery = '';
                    }
                    if($user_type =='marketing_supervisor') {
                        $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->groupBy('sales_regional_manager_id')->select('sales_regional_manager_id')->get();
                        $rsm_ids = array();
                        foreach ($marketing_supervisor_rsm as $key => $value) {
                            $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
                        }
                        $imp_rsm_ids = "'".implode("','", $rsm_ids)."'";
                        $query_rsm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_regional_managers WHERE sales_regional_manager_id IN (".$imp_rsm_ids.") AND status='1'";
                        $sales_regional = DB::select(DB::raw($query_rsm));
                        $sales_regional_ecn = $sales_regional[0]->ecn;
                        $exprsmEcn = explode(',', $sales_regional_ecn);
                        $imprsmEcn = "'".implode("','",$exprsmEcn)."'";
                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE sales_regional_manager_id IN (".$imp_rsm_ids.") AND status='1'";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="marketing_supervisor") OR (`manager_ecn` IN ('.$imprsmEcn.') AND `manager_type`="sales_regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else if($user_type =='zonal') {
                        $query_rm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM regional_managers WHERE zonal_manager_id=".$user_id." AND status='1' GROUP BY regional_manager_id";
                        $rs_regional = DB::select(DB::raw($query_rm));
                        $rs_regional_ecn = $rs_regional[0]->ecn;
                        $exprmEcn = explode(',', $rs_regional_ecn);
                        $imprmEcn = "'".implode("','",$exprmEcn)."'";

                        $query_rsm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_regional_managers WHERE zonal_manager_id=".$user_id." AND status='1' GROUP BY sales_regional_manager_id";
                        $sales_regional = DB::select(DB::raw($query_rsm));
                        $sales_regional_ecn = $sales_regional[0]->ecn;
                        $exprsmEcn = explode(',', $sales_regional_ecn);
                        $imprsmEcn = "'".implode("','",$exprsmEcn)."'";

                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE zonal_manager_id=".$user_id." AND status='1' GROUP BY sales_area_manager_id";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="zonal") OR (`manager_ecn` IN ('.$imprmEcn.') AND `manager_type`="regional") OR (`manager_ecn` IN ('.$imprsmEcn.') AND `manager_type`="sales_regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else if($user_type =='regional') {
                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE regional_manager_id=".$user_id." AND status='1' GROUP BY sales_area_manager_id";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else if($user_type =='sales_regional') {
                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE sales_regional_manager_id=".$user_id." AND status='1' GROUP BY sales_area_manager_id";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="sales_regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else {
                        $addQry .= ' AND ('.$addFlQuery.' (`manager_ecn`="'.$user_ecn.'" AND `manager_type`="'.$user_type.'"))';
                    }
                }                
                $atp_query = " SELECT
                            ia.influencer_atp_id,
                            ia.date_time,
                            ia.fl_name,
                            ia.fl_ecn,
                            ia.fl_type,
                            ia.manager_name,
                            ia.manager_ecn,
                            ia.manager_type,
                            ic.influencer_category_id,
                            ic.company_category,
                            i.influencer_id,
                            i.name as influencer_name,
                            i.office_type as influencer_office_type,
                            i.influencer_type,
                            s.state_id,
                            s.state,
                            d.district_id,
                            d.district,
                            ia.ticket_number,
                            ia.status,
                            ia.created_at,
                            ia.updated_at,
                            ad.user_id,
                            ad.user_type,
                            ad.user_ecn,
                            ad.user_name,
                            ad.arrival_image,
                            ad.arrival_time,
                            ad.arrival_comment,
                            ad.arrival_latitude,
                            ad.arrival_longitude,
                            ad.departure_image,
                            ad.departure_time,
                            ad.departure_comment,
                            ad.departure_latitude,
                            ad.departure_longitude,
                            ad.duration,
                            ad.distance,
                            ad.is_validate
                            FROM influencer_atp ia
                            LEFT JOIN influencer i ON ia.influencer_id=i.influencer_id
                            LEFT JOIN influencer_atp_arrival_departure ad ON ia.influencer_atp_id=ad.influencer_atp_id AND ad.user_id='".$user_id."' AND ad.user_type='".$user_type."'
                            LEFT JOIN states s ON i.state_id=s.state_id
                            LEFT JOIN districts d ON i.district_id=d.district_id
                            LEFT JOIN influencer_category ic ON ia.influencer_category=ic.influencer_category_id
                            WHERE $addQry
                            GROUP BY influencer_atp_id
                            ORDER BY ia.date_time ASC";
                // return $atp_query;
                $atp_detail = DB::select(DB::raw($atp_query));

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Influencer ATP List Found',
                    'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data' => $atp_detail
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function influencerArrivalDeparture(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'              => 'required|string',
            'influencer_atp_id'     => 'required|string',
            'arrival_time'          => 'required',
            'arrival_image'         => 'required|mimes:jpg,png,jpeg',
            'arrival_latitude'      => 'required',
            'arrival_longitude'     => 'required',
            'departure_time'        => 'required',
            'departure_image'       => 'required|mimes:jpg,png,jpeg',
            'departure_latitude'    => 'required',
            'departure_longitude'   => 'required',
            'duration'              => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $user_name    = $user_details[0]->manager_name;
                $arrival_sTempFileName = '';
                $departure_sTempFileName = '';
                $influencer_atp_id  = $data['influencer_atp_id'];
                $arrival_time       = $data['arrival_time'];
                $arrival_comment    = (!empty($data['arrival_comment'])) ? $data['arrival_comment'] : '';
                $arrival_latitude   = $data['arrival_latitude'];
                $arrival_longitude  = $data['arrival_longitude'];
                $departure_time     = $data['departure_time'];
                $departure_comment  = (!empty($data['departure_comment'])) ? $data['departure_comment'] : '';
                $departure_latitude = $data['departure_latitude'];
                $departure_longitude= $data['departure_longitude'];
                $duration           = $data['duration'];

                $atp_ticket_check = InfluencerATP::where('influencer_atp_id', $influencer_atp_id)->first();
                if($atp_ticket_check) {
                    // Calculate Distance and Check Validate
                    $influencer_radius = DB::table('influencer_radius')->where('status',1)->select('radius')->get();
                    if(count($influencer_radius)>0) {
                        $radius = $influencer_radius[0]->radius;
                    } else {
                        $radius = 0;
                    }
                    $influencer_id   = $atp_ticket_check->influencer_id;
                    $influencer_data = Influencer::where('influencer_id',$influencer_id)->select('latitude','longitude')->first();
                    $lat1 = $influencer_data->latitude;
                    $lon1 = $influencer_data->longitude;
                    $lat2 = $departure_latitude;
                    $lon2 = $departure_longitude;

                    $rad = M_PI / 180;
                    $distance = acos(sin($lat2*$rad) * sin($lat1*$rad) + cos($lat2*$rad) * cos($lat1*$rad) * cos($lon2*$rad - $lon1*$rad)) * 6371;// Kilometers
                    $distance_meter = round($distance*1000,2);
                    // $radius_meter   = $radius*1000;
                    if($distance_meter <= $radius) {
                        $is_validate = 1;
                    } else {
                        $is_validate = 0;
                    }
                    // return $response = array($distance_meter,$is_validate);
                    if ($request->hasFile('arrival_image')) {
                        if ($request->file('arrival_image')->isValid()) {
                            $arrival_file = $request->file('arrival_image');
                            //s3Upload
                            $extension = pathinfo($arrival_file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $arrival_fileName = "A" . md5(time() . rand()) . str_replace($arrival_file->getClientOriginalName(), '', $arrival_file->getClientOriginalName()) . '.' . $extension;
                            $arrival_sTempFileName = 'assets/influencer_atp/'.$arrival_fileName;
                            Storage::disk('s3')->put($arrival_sTempFileName, fopen($arrival_file, 'r+'),'public');
                            //
                        }
                    }
                    if ($request->hasFile('departure_image')) {
                        if ($request->file('departure_image')->isValid()) {
                            $departure_file = $request->file('departure_image');
                            //s3Upload
                            $departure_extension = pathinfo($departure_file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $departure_fileName = "A" . md5(time() . rand()) . str_replace($departure_file->getClientOriginalName(), '', $departure_file->getClientOriginalName()) . '.' . $departure_extension;
                            $departure_sTempFileName = 'assets/influencer_atp/'.$departure_fileName;
                            Storage::disk('s3')->put($departure_sTempFileName, fopen($departure_file, 'r+'),'public');
                            //
                        }
                    }
                    $atp_check = InfluencerATPArivalDeparture::where('influencer_atp_id', $influencer_atp_id)->where('user_id', $user_id)->where('user_type',$user_type)->first();
                    if ($atp_check) {
                        $update_atp = InfluencerATPArivalDeparture::where([
                            'user_id'   => $user_id,
                            'user_type' => $user_type,
                            'influencer_atp_id' => $influencer_atp_id
                        ])->update([
                            'arrival_time'      => $arrival_time,
                            'arrival_image'     => $arrival_sTempFileName,
                            'arrival_comment'   => $arrival_comment,
                            'arrival_latitude'  => $arrival_latitude,
                            'arrival_longitude' => $arrival_longitude,
                            'departure_time'    => $departure_time,
                            'departure_comment' => $departure_comment,
                            'departure_image'   => $departure_sTempFileName,
                            'departure_latitude'=> $departure_latitude,
                            'departure_longitude' => $departure_longitude,
                            'is_arrival_departure'=> '1',
                            'duration'          => $duration,
                            'distance'          => $distance_meter,
                            'is_validate'       => $is_validate,
                            'updated_at'        => date('Y-m-d H:i:s')
                        ]);
                        if ($update_atp) {
                            $base_url ="https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                            $data = array(
                                'arrival_image' => $base_url.'/'.$arrival_sTempFileName,
                                'departure_image' => $base_url.'/'.$departure_sTempFileName
                            );
                            return $this->respond([
                                'status'  => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'ATP Successfully Updated!',
                                'data'    => $data
                            ]);
                        } else {
                            return $this->respondNotFound("Something went wrong. Please Try Again!!");
                        }
                    } else {
                        $user_plan_atp = array(
                            'influencer_atp_id' => $influencer_atp_id,
                            'user_id'           => $user_id,
                            'user_type'         => $user_type,
                            'user_ecn'          => $user_ecn,
                            'user_name'         => $user_name,
                            'arrival_time'      => $arrival_time,
                            'arrival_image'     => $arrival_sTempFileName,
                            'arrival_comment'   => $arrival_comment,
                            'arrival_latitude'  => $arrival_latitude,
                            'arrival_longitude' => $arrival_longitude,
                            'departure_time'    => $departure_time,
                            'departure_comment' => $departure_comment,
                            'departure_image'   => $departure_sTempFileName,
                            'departure_latitude'=> $departure_latitude,
                            'departure_longitude' => $departure_longitude,
                            'is_arrival_departure'=> '1',
                            'duration'          => $duration,
                            'distance'          => $distance_meter,
                            'is_validate'       => $is_validate,
                            'created_at'        => date('Y-m-d H:i:s'),
                            'updated_at'        => date('Y-m-d H:i:s')
                        );
                        $insert_plan_atp = InfluencerATPArivalDeparture::firstOrCreate($user_plan_atp);
                        if ($insert_plan_atp) {
                            return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'ATP Successfully Added!',
                                'data' => array(),
                            ]);
                        } else {
                            return $this->respondWithError("ATP Data Not Uploaded!");
                        }
                    }
                } else {
                    return $this->respondNotFound("Invalid ATP Ticket !!");
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function influencerVisitSummary(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;

                if(!empty($data['month'])) {
                    $target_month = $data['month'];
                } else {
                    $target_month = 0;
                }
                if(!empty($data['year'])) {
                    $target_year = $data['year'];
                } else {
                    $target_year = 0;
                }
                $target = DB::table('influencer_target')->where('status',1);
                if(!empty($data['userType'])) {
                    $userType = $data['userType'];
                    $target->where('user_type',$userType);
                    if(!empty($data['userId'])) {
                        $userId  = $data['userId'];
                        if($userType == 'bdo') {
                            $ecn_by_id = 'id';
                        } else {
                            $ecn_by_id = strtolower($userType).'_manager_id';
                        }
                        $userEcn = $this->findUserEcn($ecn_by_id, $userType, $userId);
                        $target->where('user_ecn',$userEcn[0]->ecn);
                    } else {
                        if($user_type == 'bdo') {
                            $user_search_by_id = 'user_id';
                        } else {
                            $user_search_by_id = strtolower($user_type).'_manager_id';
                        }
                        if($userType == 'bdo') {
                            $user_search_table = 'users';
                        } else {
                            $user_search_table = strtolower($user_type).'_managers';
                        }
                        $query_users = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM ".$user_search_table." WHERE ".$user_search_by_id."=".$user_id." AND status='1'";
                        $users = DB::select(DB::raw($query_users));
                        $users_ecn = $users[0]->ecn;
                        $expusersEcn = explode(',', $users_ecn);
                        $target->whereIn('user_ecn',$expusersEcn);
                    }
                } else {
                    $target->where('user_type',$user_type);
                    $target->where('user_ecn',$user_ecn);
                }
                if(!empty($data['year'])) {
                    $target_year = $data['year'];
                    $target->where('target_year',$target_year);
                }
                if(!empty($data['month'])) {
                    $target_month = $data['month'];
                    $target->where('target_month',$target_month);
                }
                $targetData = $target->select('*')->get();
                if(count($targetData)>0) {
                    $target_month = $target_month;
                    $target_year  = $target_year;
                    $category_a_target = $targetData[0]->category_a_target;
                    $category_a_month_target = $category_a_target;

                    $category_b_target = $targetData[0]->category_b_target;
                    $category_b_month_target = $category_b_target;

                    $category_c_target = $targetData[0]->category_c_target;
                    $category_c_month_target = $category_c_target;
                } else {
                    $category_a_month_target = $category_b_month_target = $category_c_month_target = $target_month = $target_month = 0;
                }
                // return $category_a_month_target."--".$category_b_month_target."--".$category_c_month_target;

                $visitSummary  = array(
                    'month' => $target_month,
                    'year'  => $target_year
                );
                if($target_month !=0 && $target_year !=0) {
                    if($target_month<10) {
                        $target_month = '0'.$target_month;
                    }
                    $plan_date_start = $target_year.'-'.$target_month.'-01T00:00:00';
                    $plan_date_end = $target_year.'-'.$target_month.'-31T00:00:00';
                    $plan_date = 1;
                } else {
                    $plan_date = 0;
                }
                $influencerCategory = InfluencerCategory::where('status',1)->get();
                foreach ($influencerCategory as $category) {
                    $influencer_category_id = $category->influencer_category_id;
                    $company_category = $category->company_category;
                    if($company_category == 'A') {
                        $target = $category_a_month_target;
                    } else if($company_category == 'B') {
                        $target = $category_b_month_target;
                    } else {
                        $target = $category_c_month_target;
                    }
                    $planData = InfluencerATP::where('influencer_category',$influencer_category_id);
                    $visitData = InfluencerATP::join('influencer_atp_arrival_departure','influencer_atp_arrival_departure.influencer_atp_id','=','influencer_atp.influencer_atp_id')->where('influencer_atp.influencer_category',$influencer_category_id);
                    if(!empty($data['userType'])) {
                        $userType = $data['userType'];
                        $visitData->where('influencer_atp_arrival_departure.user_type',$userType);
                        if($userType == 'bdo') {
                            $planData->where('fl_type',$userType);
                        } else {
                            $planData->where('manager_type',$userType);
                        }
                        if(!empty($data['userId'])) {
                            $userId = $data['userId'];
                            if($userType == 'bdo') {
                                $ecn_by_id = 'id';
                            } else {
                                $ecn_by_id = strtolower($userType).'_manager_id';
                            }
                            $userEcn = $this->findUserEcn($ecn_by_id,$userType,$userId);
                            $planData->where('manager_ecn',$userEcn[0]->ecn);
                            if($userType == 'bdo') {
                                $planData->where('fl_ecn',$userEcn[0]->ecn);
                            } else {
                                $planData->where('manager_ecn',$userEcn[0]->ecn);
                            }
                            $visitData->where('influencer_atp_arrival_departure.user_id',$userId);
                        } else {
                            if($user_type == 'bdo') {
                                $user_search_by_id = 'id';
                            } else {
                                $user_search_by_id = strtolower($user_type).'_manager_id';
                            }
                            if($userType == 'bdo') {
                                $user_search_table = 'users';
                            } else {
                                $user_search_table = strtolower($user_type).'_managers';
                            }
                            $query_users = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM ".$user_search_table." WHERE ".$user_search_by_id."=".$user_id." AND status='1'";
                            $users = DB::select(DB::raw($query_users));
                            $users_ecn = $users[0]->ecn;
                            $expusersEcn = explode(',', $users_ecn);
                            if($userType == 'bdo') {
                                $planData->whereIn('fl_ecn',$expusersEcn);
                            } else {
                                $planData->whereIn('manager_ecn',$expusersEcn);
                            }
                            $visitData->whereIn('influencer_atp_arrival_departure.user_ecn',$expusersEcn);
                        }
                    } else {
                        if($user_type == 'bdo') {
                            $planData->where('fl_type',$user_type);
                            $planData->where('fl_ecn',$user_ecn);
                        } else {
                            $planData->where('manager_type',$user_type);
                            $planData->where('manager_ecn',$user_ecn);
                        }
                        $visitData->where('influencer_atp_arrival_departure.user_ecn',$user_ecn);
                    }
                    if($plan_date==1) {
                        $planData->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end);
                        $visitData->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end);
                    }
                    $plan   = $planData->count();
                    $visit  = $visitData->count();

                    $cat = "cat_".strtolower($company_category)."_";
                    $visitSummary[$cat.'target'] = $target;
                    $visitSummary[$cat.'plan'] = $plan;
                    $visitSummary[$cat.'visit'] = $visit;
                }
                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Influencer Visit History Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $visitSummary
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function influencerSalesReport(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'updated_date'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $updated_date = $data['updated_date'];
                $addQry = ' 1=1';

                if(!empty($data['month'])) {
                    $month = $data['month'];
                    $addQry .= ' AND sr.month="'.$month.'"';
                }
                if(!empty($data['year'])) {
                    $year = $data['year'];
                    $addQry .= ' AND sr.year="'.$year.'"';
                }
                if($updated_date != 0) {
                    $addQry .= ' AND sr.updated_at >="'.$updated_date.'"';
                }
                if(!empty($data['influencer_id'])) {
                    $influencer_id = $data['influencer_id'];
                    $addQry .= ' AND sr.influencer_id ="'.$influencer_id.'"';
                } else {
                    if($user_type=='bdo') {
                        $expFlecn = array($user_ecn);
                    } else {
                        $getTeamFlecn = $this->getTeamFlecn($user_id, $user_type);
                        if(($getTeamFlecn !=0) && count($getTeamFlecn)>0) {
                            $Flecn = $getTeamFlecn[0]->ecn;
                            $expFlecn = explode(',', $Flecn);
                        } else {
                            $expFlecn = array();
                        }
                    }
                    $influencer = Influencer::whereIn('fl_ecn',$expFlecn)->where('fl_type','bdo')->where('status',1)->select('influencer_id')->get();
                    $influencer_ids = array();
                    foreach ($influencer as $key => $value) {
                        $influencer_ids[] = $influencer[$key]->influencer_id;
                    }
                    $imp_influencer_ids = "'".implode("','", $influencer_ids)."'";
                    $addQry .= ' AND sr.influencer_id IN('.$imp_influencer_ids.')';
                    // return $imp_influencer_ids;
                }

                $sales_report = " SELECT
                                    sr.influencer_sales_report_id as id,
                                    i.influencer_id,
                                    i.name as influencer_name,
                                    sr.month,
                                    sr.year,
                                    sr.sales_turnover as sales_figure
                                FROM influencer_sales_report sr
                                LEFT JOIN influencer i ON sr.influencer_id=i.influencer_id
                                WHERE $addQry 
                                ORDER BY sr.influencer_sales_report_id ASC";
                // return $sales_report;
                $sales_report_detail = DB::select(DB::raw($sales_report));

                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Influencer Sales Report List Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $sales_report_detail
                ]);
            }
        }
    }

    /* Sugarmill */
    public function createSugarmill(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'          => 'required|string',
            'office_type'       => 'required',
            'sugarmill_type'    => 'required',
            'name'              => 'required',
            'state_id'          => 'required',
            'district_id'       => 'required',
            'address'           => 'required',
            'fl_name'           => 'required',
            'fl_type'           => 'required',
            'fl_ecn'            => 'required',
            'image'             => 'required',
            'contact_data'      => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $user_name    = $user_details[0]->manager_name;

                $office_type        = $data['office_type'];
                $sugarmill_type     = $data['sugarmill_type'];
                $parent_sugarmill_id = (!empty($data['parent_sugarmill_id'])) ? $data['parent_sugarmill_id'] : 0;
                $name               = $data['name'];
                $turn_over          = (!empty($data['turn_over'])) ? $data['turn_over'] : '';
                $land_area          = (!empty($data['land_area'])) ? $data['land_area'] : '';
                $sugarmill_company_id  = (!empty($data['sugarmill_company_id'])) ? $data['sugarmill_company_id'] : 0;
                // $parent_sugarmill_id = (!empty($data['parent_sugarmill_id'])) ? $data['parent_sugarmill_id'] : 0;
                if($parent_sugarmill_id == 0) {
                    $sugarmill_category_id = $this->getSugarmillCategory($turn_over,$land_area,$sugarmill_company_id);
                    $sugarmill_company_ids = 0;
                } else {
                    $parent_sugarmill = Sugarmill::where('sugarmill_id',$parent_sugarmill_id)->select('sugarmill_id','sugarmill_category_id','sugarmill_company_id')->get();
                    if(count($parent_sugarmill)>0) {
                        $sugarmill_category_id = $parent_sugarmill[0]->sugarmill_category_id;
                        $sugarmill_company_ids = $parent_sugarmill[0]->sugarmill_company_id;
                    } else {
                        $sugarmill_category_id = 0;
                        $sugarmill_company_ids = 0;
                    }
                }
                $sugarmill_company_id = ($sugarmill_company_id == 0) ? $sugarmill_company_ids : $sugarmill_company_id;
                $state_id           = $data['state_id'];
                $district_id        = $data['district_id'];
                $address            = $data['address'];
                $fl_name            = $data['fl_name'];
                $fl_ecn             = $data['fl_ecn'];
                $fl_type            = $data['fl_type'];
                $latitude           = ($data['latitude']) ? $data['latitude'] : '';
                $longitude          = ($data['longitude']) ? $data['longitude'] : '';
                $contact_data       = $data['contact_data'];

                $sugarmill = Sugarmill::where('office_type',$office_type)->where('sugarmill_type',$sugarmill_type)->where('sugarmill_company_id',$sugarmill_company_id)->where('state_id',$state_id)->where('district_id',$district_id)->where('addedby_ecn',$user_ecn)->where('addedby_type',$user_type)->where('name',$name)->get();

                if (isset($sugarmill) && (count($sugarmill)==0)) {
                    $sugarmill_data = array(
                        'office_type'       => $office_type,
                        'sugarmill_type'    => $sugarmill_type,
                        'parent_sugarmill_id' => $parent_sugarmill_id,
                        'name'              => $name,
                        'turn_over'         => $turn_over,
                        'land_area'         => $land_area,
                        'sugarmill_company_id' => $sugarmill_company_id,
                        'sugarmill_category_id'=> $sugarmill_category_id,
                        'state_id'          => $state_id,
                        'district_id'       => $district_id,
                        'address'           => $address,
                        'latitude'          => $latitude,
                        'longitude'         => $longitude,
                        'fl_name'           => $fl_name,
                        'fl_ecn'            => $fl_ecn,
                        'fl_type'           => $fl_type,
                        'addedby_id'        => $user_id,
                        'addedby_name'      => $user_name,
                        'addedby_ecn'       => $user_ecn,
                        'addedby_type'      => $user_type,                    
                        'status'            => 1,
                        'created_at'        => date('Y-m-d H:i:s'),
                        'updated_at'        => date('Y-m-d H:i:s')
                    );
                    // return $sugarmill_data;
                    $Sugarmill = Sugarmill::firstOrNew($sugarmill_data);
                    $Sugarmill = $Sugarmill->save();
                    $sugarmill_id = Sugarmill::all()->last()->sugarmill_id;

                    if($request->file('image')) {
                        $file=$request->file('image');
                        if(!empty($file)) {
                            //s3Upload
                            $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                            $sTempFileName = 'assets/sugarmill/' . $fileName;
                            Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            Sugarmill::where('sugarmill_id',$sugarmill_id)
                                ->update([
                                    'image'     => $sTempFileName,
                                    'updated_at'=> date('Y-m-d H:i:s')
                                ]);
                        }
                    }
                    $contact_data_obj = json_decode($contact_data,true);
                    foreach ($contact_data_obj as $key => $contact) {
                        $sugarmill_contact = array(
                            'sugarmill_id'  => $sugarmill_id,
                            'state_id'      => $state_id,
                            'district_id'   => $district_id,
                            'level'         => $contact['level'],
                            'designation'   => $contact['designation'],
                            'name'          => $contact['name'],
                            'wa_number'     => $contact['wa_number'],
                            'mobile_number' => $contact['mobile_number'],
                            'email'         => $contact['email'],
                            'status'        => 1,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        );
                        $SugarmillContact = SugarmillContact::firstOrNew($sugarmill_contact);
                        $SugarmillContact = $SugarmillContact->save();
                    }
                    $data = Sugarmill::where('sugarmill_id',$sugarmill_id)->get();

                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Sugarmill Successfully Submitted!',
                        'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                        'data' => $data
                    ]);
                } else {
                    $sugarmill_id = $sugarmill[0]->sugarmill_id;
                    $data = Sugarmill::where('sugarmill_id',$sugarmill_id)->get();
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Sugarmill Already Added!',
                        'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                        'data' => $data
                    ]);
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    private function getSugarmillCategory($turn_over,$land_holding,$sugarmill_company_id) {
        if(($turn_over=='') || ($land_holding=='') || ($sugarmill_company_id=='')){
            return 0;
        } else {
            $query_turn_over = "SELECT turn_over_point FROM sugarmill_category WHERE turn_over<=".$turn_over." AND status=1";
            $turn_over_data = DB::select(DB::raw($query_turn_over));
            if(count($turn_over_data)==0) {
                $query_turn_over = "SELECT turn_over_point FROM sugarmill_category WHERE company_category='C' AND status=1";
                $turn_over_data = DB::select(DB::raw($query_turn_over));
            }
            $turn_over_point = $turn_over_data[0]->turn_over_point;

            $query_land_holding = "SELECT land_holding_point FROM sugarmill_category WHERE land_holding<=".$land_holding." AND status=1";
            $land_holding_data = DB::select(DB::raw($query_land_holding));
            if(count($land_holding_data)==0) {
                $query_land_holding = "SELECT land_holding_point FROM sugarmill_category WHERE company_category='C' AND status=1";
                $land_holding_data = DB::select(DB::raw($query_land_holding));
            }
            $land_holding_point = $land_holding_data[0]->land_holding_point;

            $query_company_point = "SELECT company_point FROM sugarmill_category WHERE sugarmill_company_id=".$sugarmill_company_id." AND status=1";
            $company_point_data = DB::select(DB::raw($query_company_point));
            if(count($company_point_data)==0) {
                $query_company_point = "SELECT company_point FROM sugarmill_category WHERE company_category='C' AND status=1";
                $company_point_data = DB::select(DB::raw($query_company_point));
            }
            $company_point = $company_point_data[0]->company_point;
            $total_point = $turn_over_point+$land_holding_point+$company_point;
            // echo "=>".$turn_over_point."-".$land_holding_point."-".$company_point; exit;
            $total_point = round($total_point);

            $query_category = "SELECT sugarmill_category_id FROM sugarmill_category WHERE category_point<=".$total_point." AND status=1";
            $category_point_data = DB::select(DB::raw($query_category));
            if(count($category_point_data)==0) {
                $query_category = "SELECT sugarmill_category_id FROM sugarmill_category WHERE company_category='C' AND status=1";
                $category_point_data = DB::select(DB::raw($query_category));
            }
            $sugarmill_category_id = $category_point_data[0]->sugarmill_category_id;
            return $sugarmill_category_id;
        }
    }

    public function getSugarmillTypeDesignationFL(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'updated_date'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $updated_date = $data['updated_date'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id   = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn  = $user[0]->ECN;
                $manager_details = $this->findUserDetails($user_type, $api_token);
                $manager_name = $manager_details[0]->manager_name;
                $fl_ids   = array();
                $fl_ids[] = $user_id;
                if(strtolower($user_type) =='marketing_supervisor') {
                    $fl_ids   = array();
                    $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->select('sales_regional_manager_id')->get();
                    $rsm_ids = array();
                    foreach ($marketing_supervisor_rsm as $key => $value) {
                        $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
                    }
                    $fl_by = 'sales_regional_manager_id';
                    $fl_ids = $rsm_ids;
                } else if(strtolower($user_type) =='zonal') {
                    $fl_by = 'zonal_manager_id';
                } else if(strtolower($user_type) =='regional') {
                    $fl_by = 'regional_manager_id';
                } else if(strtolower($user_type) =='sales_regional') {
                    $fl_by = 'sales_regional_manager_id';
                } else if(strtolower($user_type) =='sales_area') {
                    $fl_by = 'sales_area_manager_id';
                } else if(strtolower($user_type) =='area') {
                    $fl_by = 'area_manager_id';
                } else {
                    $fl_by = 'id';
                }
                $fl_data   = User::whereIn($fl_by,$fl_ids)->where('status',1)->select('id','ECN','name',DB::raw("'".$user_id."' as manager_id"),DB::raw("'".$manager_name."' as manager_name"));
                if($updated_date != 0) {
                    $fl_data->where('updated_at','>=', $updated_date);
                }
                $fl_data_list = $fl_data->groupBy('id')->get();

                $category_data = SugarmillCategory::where('status',1)->select('sugarmill_category_id','company_category');
                if($updated_date != 0) {
                    $category_data->where('updated_at','>=', $updated_date);
                }
                $category_data_list = $category_data->get();

                $company_data = SugarmillCompany::where('status',1)->select('sugarmill_company_id','company_type');
                if($updated_date != 0) {
                    $company_data->where('updated_at','>=', $updated_date);
                }
                $company_data_list = $company_data->get();

                $designation_data = SugarmillDesignation::where('status',1)->select('sugarmill_designation_id','level','designation');
                if($updated_date != 0) {
                    $designation_data->where('updated_at','>=', $updated_date);
                }
                $designation_data_list = $designation_data->get();
                
                $data = array(
                    'category_data' => $category_data_list,
                    'designation_data' => $designation_data_list,
                    'company_data'  => $company_data_list,
                    'fl_data'       => $fl_data_list
                );

                return $this->respond([
                    'status'   => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'  => 'List Type Designation and FL',
                    'crr_date' => date('Y-m-d H:i:s'),
                    'data'     => $data
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function getListSugarmill(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $sugarmill  = array();
                $addQry     = '1=1 AND ';
                $updated_date = $data['updated_at'];

                if($user_type=='bdo') {
                    $expFlecn = array($user_ecn);
                } else {
                    $getTeamFlecn = $this->getTeamFlecn($user_id, $user_type);
                    if(($getTeamFlecn !=0) && count($getTeamFlecn)>0) {
                        $Flecn    = $getTeamFlecn[0]->ecn;
                        $expFlecn = explode(',', $Flecn);
                    } else {
                        $expFlecn = array();
                    }
                }
                $sugarmills = Sugarmill::whereIn('fl_ecn',$expFlecn)->where('fl_type','bdo')->where('status',1);
                if($updated_date != 0) {
                    $sugarmills->where('updated_at','>=',$updated_date);
                }
                $sugarmill_datas = $sugarmills->groupBy('sugarmill_id')->get();

                $sugarmill_data = array();
                foreach ($sugarmill_datas as $key => $value) {
                    $sugarmill_id = $value->sugarmill_id;
                    array_push($sugarmill_data, $sugarmill_id);
                    if($value->parent_sugarmill_id != 0) {
                        array_push($sugarmill_data, $value->parent_sugarmill_id);
                    }
                }
                $sugarmill_data = array_unique($sugarmill_data);
                $a=0;
                foreach ($sugarmill_data as $key => $value) {
                    $sugarmill_id = $value;
                    $sugarmill_query = " SELECT 
                                sm.sugarmill_id,
                                sm.office_type,
                                (CASE WHEN sm.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type_name,
                                sm.sugarmill_type,
                                (CASE WHEN sm.sugarmill_type='1' THEN 'Sugarmill' ELSE 'SSM' END) as sugarmill_type_name,
                                sm.parent_sugarmill_id,
                                sm.name,
                                sm.turn_over,
                                sm.land_area,
                                sc.sugarmill_company_id,
                                sc.company_category,
                                s.state_id,
                                s.state,
                                d.district_id,
                                d.district,
                                sm.address,
                                sm.latitude,
                                sm.longitude,
                                sm.image,
                                sm.fl_name,
                                sm.fl_ecn,
                                sm.fl_type,
                                sm.status,
                                sm.created_at,
                                sm.updated_at,
                                '' as contact_data
                            FROM sugarmill sm
                            LEFT JOIN states s ON sm.state_id=s.state_id
                            LEFT JOIN districts d ON sm.district_id=d.district_id
                            LEFT JOIN sugarmill_category sc ON sm.sugarmill_category_id=sc.sugarmill_category_id
                            WHERE $addQry sm.status='1' AND sm.sugarmill_id='".$sugarmill_id."'
                            ORDER BY sm.sugarmill_id ASC";
                    $sugarmill_detail = DB::select(DB::raw($sugarmill_query));
                    // $sugarmill[] = $sugarmill_detail;
                    if($updated_date != 0) {
                        $sugarmill_contact = SugarmillContact::where('sugarmill_id',$sugarmill_id)->where('updated_at','>=', $updated_date)->where('status',1)->get();
                    } else {
                        $sugarmill_contact = SugarmillContact::where('sugarmill_id',$sugarmill_id)->where('status',1)->get();
                    }
                    if(count($sugarmill_detail)>0) { 
                        $sugarmill_detail[0]->contact_data = $sugarmill_contact;
                        array_push($sugarmill,$sugarmill_detail[0]);
                    }
                }
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Sugarmill List Found',
                    'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data' => $sugarmill
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function sugarmillListing(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $updated_date = $data['updated_at'];
                if($user_type =='bdo') {
                    $expFlecn = array($user_ecn);
                } else {
                    $getTeamFlecn = $this->getTeamFlecn($user_id, $user_type);
                    if(($getTeamFlecn !=0) && count($getTeamFlecn)>0) {
                        $Flecn    = $getTeamFlecn[0]->ecn;
                        $expFlecn = explode(',', $Flecn);
                    } else {
                        $expFlecn = array();
                    }
                }
                $sugarmill_data = Sugarmill::whereIn('fl_ecn',$expFlecn)->where('fl_type','bdo')->where('status',1);
                if($updated_date != 0) {
                    $sugarmill_data->where('updated_at','>=',$updated_date);
                }
                $sugarmill_data_list = $sugarmill_data->select('sugarmill_id','name','sugarmill_category_id','office_type','sugarmill_type as type','fl_ecn as user_ecn')->get();

                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Sugarmill List Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $sugarmill_data_list
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function sugarmillListingHeadOfc(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_at' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;
                $updated_date = $data['updated_at'];
                
                $sugarmill_data = Sugarmill::where('office_type',1)->where('status',1);
                if($updated_date != 0) {
                    $sugarmill_data->where('updated_at','>=',$updated_date);
                }
                $sugarmill_data_list = $sugarmill_data->select('sugarmill_id','name','sugarmill_category_id','office_type','sugarmill_type as type','fl_ecn as user_ecn')->get();

                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Sugarmill List Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $sugarmill_data_list
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function addSugarmillContactCompanyBranch(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'sugarmill_id'  => 'required',
            'contact_data'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id        = $user[0]->id;
                $user_type      = $user[0]->user_type;
                $user_ecn       = $user[0]->ECN;
                $user_details   = $this->findUserDetails($user_type, $api_token);
                $user_name      = $user_details[0]->manager_name;
                $sugarmill_id   = $data['sugarmill_id'];
                $contact_data   = $data['contact_data'];
                $sugarmill      = Sugarmill::where('sugarmill_id', $sugarmill_id)->first();

                $contact_data_obj = json_decode($contact_data,true);
                foreach ($contact_data_obj as $key => $contact) {
                    $sugarmill_contact = array(
                        'sugarmill_id'  => $sugarmill_id,
                        'state_id'      => $state_id,
                        'district_id'   => $district_id,
                        'level'         => $contact['level'],
                        'designation'   => $contact['designation'],
                        'name'          => $contact['name'],
                        'wa_number'     => $contact['wa_number'],
                        'mobile_number' => $contact['mobile_number'],
                        'email'         => $contact['email'],
                        'status'        => 1,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s')
                    );
                    $SugarmillContact = SugarmillContact::firstOrNew($sugarmill_contact);
                    $SugarmillContact = $SugarmillContact->save();
                }
                // $data = SugarmillContact::where('sugarmill_id',$sugarmill_id)->get();
                $data = array();

                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Sugarmill Contact Successfully Submitted!',
                    'data' => $data
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function getAllSugarmillContactsById($sugarmill_id,$updated_date=0) {
        if($sugarmill_id) {
            $crr_date = date('Y-m-d H:i:s');
            $SugarmillContact = SugarmillContact::where('sugarmill_id',$sugarmill_id)->where('status',1);
            if($updated_date != 0) {
                $SugarmillContact->where('updated_at','>=', $updated_date);
            }
            $SugarmillContactList = $SugarmillContact->get();

            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'Sugarmill Contact List Found!',
                'crr_date' => $crr_date,
                'data' => $SugarmillContactList
            ]);
        } else {
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'sugarmill_id is required!',
                'data' => array()
            ]);
            
        }
    }

    public function sugarmillPlanAtp(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'apiToken' => 'required|string',
                'atp_data' => 'required|array'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $planATP = $data['atp_data'];
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id    = $user[0]->id;
                    $user_type  = $user[0]->user_type;
                    $user_ecn   = $user[0]->ECN;
                    $user_details = $this->findUserDetails($user_type, $api_token);
                    $user_name    = $user_details[0]->manager_name;

                    if (count($planATP) > 0) {
                        $valid_user_plan = array();
                        //echo date('Y-m-d\TH:i:s');die;
                        foreach ($planATP as $plan) {
                            $dat = explode('.', $plan['date_time']);
                            // return date('Y-m-d\T00:00:00');
                            if ( $dat[0] < date('Y-m-d\T00:00:00')) {
                                return $this->respondWithError("You can not plan atp on previous date.");
                            }
                        }
                        foreach ($planATP as $plan) {
                            $sugarmill_category = $plan['sugarmill_category'];
                            $date_time       = $plan['date_time'];
                            $sugarmill_id    = $plan['sugarmill_id'];
                            $ticket_number   = $plan['ticket_number'];
                            $is_manager      = $plan['is_manager'];
                            if($is_manager == 0) {
                                $fl_name    = NULL;
                                $fl_ecn     = NULL;
                                $fl_type    = NULL;
                            } else {
                                $sugarmill  = Sugarmill::where('sugarmill_id',$sugarmill_id)->first();
                                $fl_name    = $sugarmill->fl_name;
                                $fl_ecn     = $sugarmill->fl_ecn;
                                $fl_type    = $sugarmill->fl_type;
                            }
                            $user_plan_atp = array(
                                'date_time'     => $date_time,
                                'fl_name'       => $fl_name,
                                'fl_ecn'        => $fl_ecn,
                                'fl_type'       => $fl_type,
                                'manager_name'  => $user_name,
                                'manager_ecn'   => $user_ecn,
                                'manager_type'  => $user_type,
                                'sugarmill_category' => $sugarmill_category,
                                'sugarmill_id'  => $sugarmill_id,
                                'ticket_number' => $ticket_number,
                                'is_manager'    => $is_manager,
                                'status'        => 1,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s')
                            );
                            $insert_plan_atp = SugarmillATP::firstOrCreate($user_plan_atp);
                            $valid_user_plan[] = $user_plan_atp;
                        }
                        if (count($valid_user_plan) > 0) {
                            return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'ATP Successfully Added!',
                                'data' => $valid_user_plan,
                            ]);
                        } else {
                            return $this->respondWithError("Invalid plan activity!");
                        }
                    } else {
                        return $this->respondWithError("Plan activity not found!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function sugarmillAtpListing(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'  => 'required|string',
            'updated_date' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                // $addQry = '1=1 AND ((`manager_ecn` IN ('76543') AND `manager_type`='sales_area') OR (`manager_ecn` IN ('12975') AND `manager_type`='zonal') OR (`manager_ecn` IN ('12974') AND `manager_type`='sales_regional'))';
                $addQry = '1=1 ';
                $addQrySub = '';
                $updated_date = $data['updated_date'];
                if($updated_date != 0) {
                    $addQry .= ' AND sa.updated_at > "'.$updated_date.'"';
                }
                if($user_type == 'bdo') {
                    $addQry .=' AND sa.fl_ecn="'.$user_ecn.'" AND sa.fl_type="'.$user_type.'"';
                } else {
                    $getTeamFlecn = $this->getTeamFlecn($user_id, $user_type);
                    if(($getTeamFlecn !=0) && count($getTeamFlecn)>0) {
                        $Flecn = $getTeamFlecn[0]->ecn;
                        $expFlecn = explode(',', $Flecn);
                        $impTeamFlecn = "'".implode("','",$expFlecn)."'";
                        $addFlQuery   = '(sa.`fl_ecn` IN ('.$impTeamFlecn.') AND sa.`fl_type`="bdo") OR ';
                    } else {
                        $addFlQuery = '';
                    }
                    if($user_type =='marketing_supervisor') {
                        $marketing_supervisor_rsm = DB::table('marketing_supervisor_rsm')->where('marketing_supervisor_id',$user_id)->where('status','1')->groupBy('sales_regional_manager_id')->select('sales_regional_manager_id')->get();
                        $rsm_ids = array();
                        foreach ($marketing_supervisor_rsm as $key => $value) {
                            $rsm_ids[] = $marketing_supervisor_rsm[$key]->sales_regional_manager_id;
                        }
                        $imp_rsm_ids = "'".implode("','", $rsm_ids)."'";
                        $query_rsm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_regional_managers WHERE sales_regional_manager_id IN (".$imp_rsm_ids.") AND status='1'";
                        $sales_regional = DB::select(DB::raw($query_rsm));
                        $sales_regional_ecn = $sales_regional[0]->ecn;
                        $exprsmEcn = explode(',', $sales_regional_ecn);
                        $imprsmEcn = "'".implode("','",$exprsmEcn)."'";
                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE sales_regional_manager_id IN (".$imp_rsm_ids.") AND status='1'";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="marketing_supervisor") OR (`manager_ecn` IN ('.$imprsmEcn.') AND `manager_type`="sales_regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else if($user_type =='zonal') {
                        $query_rm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM regional_managers WHERE zonal_manager_id=".$user_id." AND status='1' GROUP BY regional_manager_id";
                        $rs_regional = DB::select(DB::raw($query_rm));
                        $rs_regional_ecn = $rs_regional[0]->ecn;
                        $exprmEcn = explode(',', $rs_regional_ecn);
                        $imprmEcn = "'".implode("','",$exprmEcn)."'";

                        $query_rsm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_regional_managers WHERE zonal_manager_id=".$user_id." AND status='1' GROUP BY sales_regional_manager_id";
                        $sales_regional = DB::select(DB::raw($query_rsm));
                        $sales_regional_ecn = $sales_regional[0]->ecn;
                        $exprsmEcn = explode(',', $sales_regional_ecn);
                        $imprsmEcn = "'".implode("','",$exprsmEcn)."'";

                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE zonal_manager_id=".$user_id." AND status='1' GROUP BY sales_area_manager_id";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="zonal") OR (`manager_ecn` IN ('.$imprmEcn.') AND `manager_type`="regional") OR (`manager_ecn` IN ('.$imprsmEcn.') AND `manager_type`="sales_regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else if($user_type =='regional') {
                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE regional_manager_id=".$user_id." AND status='1' GROUP BY sales_area_manager_id";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else if($user_type =='sales_regional') {
                        $query_asm = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM sales_area_managers WHERE sales_regional_manager_id=".$user_id." AND status='1' GROUP BY sales_area_manager_id";
                        $sales_area = DB::select(DB::raw($query_asm));
                        $sales_area_ecn = $sales_area[0]->ecn;
                        $expsalesEcn = explode(',', $sales_area_ecn);
                        $impSalesEcn = "'".implode("','",$expsalesEcn)."'";
                        $addQry .= ' AND ( '.$addFlQuery.' (`manager_ecn` IN ("'.$user_ecn.'") AND `manager_type`="sales_regional") OR (`manager_ecn` IN ('.$impSalesEcn.') AND `manager_type`="sales_area"))';
                    } else {
                        $addQry .= ' AND ('.$addFlQuery.' (`manager_ecn`="'.$user_ecn.'" AND `manager_type`="'.$user_type.'"))';
                    }
                }
                $atp_query = " SELECT
                            sa.sugarmill_atp_id,
                            sa.date_time,
                            sa.fl_name,
                            sa.fl_ecn,
                            sa.fl_type,
                            sa.manager_name,
                            sa.manager_ecn,
                            sa.manager_type,
                            sc.sugarmill_category_id,
                            sc.company_category,
                            sm.sugarmill_id,
                            sm.name as sugarmill_name,
                            sm.office_type as sugarmill_office_type,
                            sm.sugarmill_type,
                            s.state_id,
                            s.state,
                            d.district_id,
                            d.district,
                            sa.ticket_number,
                            sa.status,
                            sa.created_at,
                            sa.updated_at,
                            ad.user_id,
                            ad.user_type,
                            ad.user_ecn,
                            ad.user_name,
                            ad.arrival_image,
                            ad.arrival_time,
                            ad.arrival_comment,
                            ad.arrival_latitude,
                            ad.arrival_longitude,
                            ad.departure_image,
                            ad.departure_time,
                            ad.departure_comment,
                            ad.departure_latitude,
                            ad.departure_longitude,
                            ad.duration,
                            ad.distance,
                            ad.is_validate
                            FROM sugarmill_atp sa
                            LEFT JOIN sugarmill sm ON sa.sugarmill_id=sm.sugarmill_id
                            LEFT JOIN sugarmill_atp_arrival_departure ad ON sa.sugarmill_atp_id=ad.sugarmill_atp_id AND ad.user_id='".$user_id."' AND ad.user_type='".$user_type."'
                            LEFT JOIN states s ON sm.state_id=s.state_id
                            LEFT JOIN districts d ON sm.district_id=d.district_id
                            LEFT JOIN sugarmill_category sc ON sa.sugarmill_category=sc.sugarmill_category_id
                            WHERE $addQry
                            GROUP BY sugarmill_atp_id
                            ORDER BY sa.date_time ASC";
                // return $atp_query;
                $atp_detail = DB::select(DB::raw($atp_query));

                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Sugarmill ATP List Found',
                    'base_url'  => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $atp_detail
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function sugarmillArrivalDeparture(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'              => 'required|string',
            'sugarmill_atp_id'      => 'required|string',
            'arrival_time'          => 'required',
            'arrival_image'         => 'required|mimes:jpg,png,jpeg',
            'arrival_latitude'      => 'required',
            'arrival_longitude'     => 'required',
            'departure_time'        => 'required',
            'departure_image'       => 'required|mimes:jpg,png,jpeg',
            'departure_latitude'    => 'required',
            'departure_longitude'   => 'required',
            'duration'              => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;
                $user_details = $this->findUserDetails($user_type, $api_token);
                $user_name    = $user_details[0]->manager_name;
                $arrival_sTempFileName = '';
                $departure_sTempFileName = '';
                $sugarmill_atp_id   = $data['sugarmill_atp_id'];
                $arrival_time       = $data['arrival_time'];
                $arrival_comment    = (!empty($data['arrival_comment'])) ? $data['arrival_comment'] : '';
                $arrival_latitude   = $data['arrival_latitude'];
                $arrival_longitude  = $data['arrival_longitude'];
                $departure_time     = $data['departure_time'];
                $departure_comment  = (!empty($data['departure_comment'])) ? $data['departure_comment'] : '';
                $departure_latitude = $data['departure_latitude'];
                $departure_longitude= $data['departure_longitude'];
                $duration           = $data['duration'];

                $atp_ticket_check = SugarmillATP::where('sugarmill_atp_id', $sugarmill_atp_id)->first();
                if($atp_ticket_check) {
                    // Calculate Distance and Check Validate
                    $sugarmill_radius = DB::table('sugarmill_radius')->where('status',1)->select('radius')->get();
                    if(count($sugarmill_radius)>0) {
                        $radius = $sugarmill_radius[0]->radius;
                    } else {
                        $radius = 0;
                    }
                    $sugarmill_id   = $atp_ticket_check->sugarmill_id;
                    $sugarmill_data = Sugarmill::where('sugarmill_id',$sugarmill_id)->select('latitude','longitude')->first();
                    $lat1 = $sugarmill_data->latitude;
                    $lon1 = $sugarmill_data->longitude;
                    $lat2 = $departure_latitude;
                    $lon2 = $departure_longitude;

                    $rad = M_PI / 180;
                    $distance = acos(sin($lat2*$rad) * sin($lat1*$rad) + cos($lat2*$rad) * cos($lat1*$rad) * cos($lon2*$rad - $lon1*$rad)) * 6371;// Kilometers
                    $distance_meter = round($distance*1000,2);
                    // $radius_meter   = $radius*1000;
                    if($distance_meter <= $radius) {
                        $is_validate = 1;
                    } else {
                        $is_validate = 0;
                    }
                    // return $response = array($distance_meter,$is_validate);
                    if ($request->hasFile('arrival_image')) {
                        if ($request->file('arrival_image')->isValid()) {
                            $arrival_file = $request->file('arrival_image');
                            //s3Upload
                            $extension = pathinfo($arrival_file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $arrival_fileName = "A" . md5(time() . rand()) . str_replace($arrival_file->getClientOriginalName(), '', $arrival_file->getClientOriginalName()) . '.' . $extension;
                            $arrival_sTempFileName = 'assets/sugarmill_atp/'.$arrival_fileName;
                            Storage::disk('s3')->put($arrival_sTempFileName, fopen($arrival_file, 'r+'),'public');
                            //
                        }
                    }
                    if ($request->hasFile('departure_image')) {
                        if ($request->file('departure_image')->isValid()) {
                            $departure_file = $request->file('departure_image');
                            //s3Upload
                            $departure_extension = pathinfo($departure_file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $departure_fileName = "A" . md5(time() . rand()) . str_replace($departure_file->getClientOriginalName(), '', $departure_file->getClientOriginalName()) . '.' . $departure_extension;
                            $departure_sTempFileName = 'assets/sugarmill_atp/'.$departure_fileName;
                            Storage::disk('s3')->put($departure_sTempFileName, fopen($departure_file, 'r+'),'public');
                            //
                        }
                    }
                    $atp_check = SugarmillATPArivalDeparture::where('sugarmill_atp_id', $sugarmill_atp_id)->where('user_id', $user_id)->where('user_type',$user_type)->first();
                    if ($atp_check) {
                        $update_atp = SugarmillATPArivalDeparture::where([
                            'user_id'   => $user_id,
                            'user_type' => $user_type,
                            'sugarmill_atp_id' => $sugarmill_atp_id
                        ])->update([
                            'arrival_time'      => $arrival_time,
                            'arrival_image'     => $arrival_sTempFileName,
                            'arrival_comment'   => $arrival_comment,
                            'arrival_latitude'  => $arrival_latitude,
                            'arrival_longitude' => $arrival_longitude,
                            'departure_time'    => $departure_time,
                            'departure_comment' => $departure_comment,
                            'departure_image'   => $departure_sTempFileName,
                            'departure_latitude'=> $departure_latitude,
                            'departure_longitude' => $departure_longitude,
                            'is_arrival_departure'=> '1',
                            'duration'          => $duration,
                            'distance'          => $distance_meter,
                            'is_validate'       => $is_validate,
                            'updated_at'        => date('Y-m-d H:i:s')
                        ]);
                        if ($update_atp) {
                            $base_url ="https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                            $data = array(
                                'arrival_image' => $base_url.'/'.$arrival_sTempFileName,
                                'departure_image' => $base_url.'/'.$departure_sTempFileName
                            );
                            return $this->respond([
                                'status'  => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'ATP Successfully Updated!',
                                'data'    => $data
                            ]);
                        } else {
                            return $this->respondNotFound("Something went wrong. Please Try Again!!");
                        }
                    } else {
                        $user_plan_atp = array(
                            'sugarmill_atp_id'  => $sugarmill_atp_id,
                            'user_id'           => $user_id,
                            'user_type'         => $user_type,
                            'user_ecn'          => $user_ecn,
                            'user_name'         => $user_name,
                            'arrival_time'      => $arrival_time,
                            'arrival_image'     => $arrival_sTempFileName,
                            'arrival_comment'   => $arrival_comment,
                            'arrival_latitude'  => $arrival_latitude,
                            'arrival_longitude' => $arrival_longitude,
                            'departure_time'    => $departure_time,
                            'departure_comment' => $departure_comment,
                            'departure_image'   => $departure_sTempFileName,
                            'departure_latitude'=> $departure_latitude,
                            'departure_longitude' => $departure_longitude,
                            'is_arrival_departure'=> '1',
                            'duration'          => $duration,
                            'distance'          => $distance_meter,
                            'is_validate'       => $is_validate,
                            'created_at'        => date('Y-m-d H:i:s'),
                            'updated_at'        => date('Y-m-d H:i:s')
                        );
                        $insert_plan_atp = SugarmillATPArivalDeparture::firstOrCreate($user_plan_atp);
                        if ($insert_plan_atp) {
                            return $this->respond([
                                'status'    => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message'   => 'ATP Successfully Added!',
                                'data'      => array(),
                            ]);
                        } else {
                            return $this->respondWithError("ATP Data Not Uploaded!");
                        }
                    }
                } else {
                    return $this->respondNotFound("Invalid ATP Ticket !!");
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function sugarmillVisitSummary(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token    = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id    = $user[0]->id;
                $user_type  = $user[0]->user_type;
                $user_ecn   = $user[0]->ECN;

                if(!empty($data['month'])) {
                    $target_month = $data['month'];
                } else {
                    $target_month = 0;
                }
                if(!empty($data['year'])) {
                    $target_year = $data['year'];
                } else {
                    $target_year = 0;
                }
                $target = DB::table('sugarmill_target')->where('status',1);
                if(!empty($data['userType'])) {
                    $userType = $data['userType'];
                    $target->where('user_type',$userType);
                    if(!empty($data['userId'])) {
                        $userId  = $data['userId'];
                        if($userType == 'bdo') {
                            $ecn_by_id = 'id';
                        } else {
                            $ecn_by_id = strtolower($userType).'_manager_id';
                        }
                        $userEcn = $this->findUserEcn($ecn_by_id, $userType, $userId);
                        $target->where('user_ecn',$userEcn[0]->ecn);
                    } else {
                        if($user_type == 'bdo') {
                            $user_search_by_id = 'user_id';
                        } else {
                            $user_search_by_id = strtolower($user_type).'_manager_id';
                        }
                        if($userType == 'bdo') {
                            $user_search_table = 'users';
                        } else {
                            $user_search_table = strtolower($user_type).'_managers';
                        }
                        $query_users = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM ".$user_search_table." WHERE ".$user_search_by_id."=".$user_id." AND status='1'";
                        $users = DB::select(DB::raw($query_users));
                        $users_ecn = $users[0]->ecn;
                        $expusersEcn = explode(',', $users_ecn);
                        $target->whereIn('user_ecn',$expusersEcn);
                    }
                } else {
                    $target->where('user_type',$user_type);
                    $target->where('user_ecn',$user_ecn);
                }
                if(!empty($data['year'])) {
                    $target_year = $data['year'];
                    $target->where('target_year',$target_year);
                }
                if(!empty($data['month'])) {
                    $target_month = $data['month'];
                    $target->where('target_month',$target_month);
                }
                $targetData = $target->select('*')->get();
                if(count($targetData)>0) {
                    $target_month = $target_month;
                    $target_year  = $target_year;
                    $category_a_target = $targetData[0]->category_a_target;
                    $category_a_month_target = $category_a_target;

                    $category_b_target = $targetData[0]->category_b_target;
                    $category_b_month_target = $category_b_target;

                    $category_c_target = $targetData[0]->category_c_target;
                    $category_c_month_target = $category_c_target;
                } else {
                    $category_a_month_target = $category_b_month_target = $category_c_month_target = $target_month = $target_month = 0;
                }
                // return $category_a_month_target."--".$category_b_month_target."--".$category_c_month_target;

                $visitSummary  = array(
                    'month' => $target_month,
                    'year'  => $target_year
                );
                if($target_month !=0 && $target_year !=0) {
                    if($target_month<10) {
                        $target_month = '0'.$target_month;
                    }
                    $plan_date_start = $target_year.'-'.$target_month.'-01T00:00:00';
                    $plan_date_end = $target_year.'-'.$target_month.'-31T00:00:00';
                    $plan_date = 1;
                } else {
                    $plan_date = 0;
                }
                $sugarmillCategory = SugarmillCategory::where('status',1)->get();
                foreach ($sugarmillCategory as $category) {
                    $sugarmill_category_id = $category->sugarmill_category_id;
                    $company_category = $category->company_category;
                    if($company_category == 'A') {
                        $target = $category_a_month_target;
                    } else if($company_category == 'B') {
                        $target = $category_b_month_target;
                    } else {
                        $target = $category_c_month_target;
                    }
                    $planData   = SugarmillATP::where('sugarmill_category',$sugarmill_category_id);
                    $visitData  = SugarmillATP::join('sugarmill_atp_arrival_departure','sugarmill_atp_arrival_departure.sugarmill_atp_id','=','sugarmill_atp.sugarmill_atp_id')->where('sugarmill_atp.sugarmill_category',$sugarmill_category_id);
                    if(!empty($data['userType'])) {
                        $userType = $data['userType'];
                        $visitData->where('sugarmill_atp_arrival_departure.user_type',$userType);
                        if($userType == 'bdo') {
                            $planData->where('fl_type',$userType);
                        } else {
                            $planData->where('manager_type',$userType);
                        }
                        if(!empty($data['userId'])) {
                            $userId = $data['userId'];
                            if($userType == 'bdo') {
                                $ecn_by_id = 'id';
                            } else {
                                $ecn_by_id = strtolower($userType).'_manager_id';
                            }
                            $userEcn = $this->findUserEcn($ecn_by_id,$userType,$userId);
                            $planData->where('manager_ecn',$userEcn[0]->ecn);
                            if($userType == 'bdo') {
                                $planData->where('fl_ecn',$userEcn[0]->ecn);
                            } else {
                                $planData->where('manager_ecn',$userEcn[0]->ecn);
                            }
                            $visitData->where('sugarmill_atp_arrival_departure.user_id',$userId);
                        } else {
                            if($user_type == 'bdo') {
                                $user_search_by_id = 'id';
                            } else {
                                $user_search_by_id = strtolower($user_type).'_manager_id';
                            }
                            if($userType == 'bdo') {
                                $user_search_table = 'users';
                            } else {
                                $user_search_table = strtolower($user_type).'_managers';
                            }
                            $query_users = "SELECT GROUP_CONCAT(ECN SEPARATOR ',')  AS ecn FROM ".$user_search_table." WHERE ".$user_search_by_id."=".$user_id." AND status='1'";
                            $users = DB::select(DB::raw($query_users));
                            $users_ecn = $users[0]->ecn;
                            $expusersEcn = explode(',', $users_ecn);
                            if($userType == 'bdo') {
                                $planData->whereIn('fl_ecn',$expusersEcn);
                            } else {
                                $planData->whereIn('manager_ecn',$expusersEcn);
                            }
                            $visitData->whereIn('sugarmill_atp_arrival_departure.user_ecn',$expusersEcn);
                        }
                    } else {
                        if($user_type == 'bdo') {
                            $planData->where('fl_type',$user_type);
                            $planData->where('fl_ecn',$user_ecn);
                        } else {
                            $planData->where('manager_type',$user_type);
                            $planData->where('manager_ecn',$user_ecn);
                        }
                        $visitData->where('sugarmill_atp_arrival_departure.user_ecn',$user_ecn);
                    }
                    if($plan_date==1) {
                        $planData->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end);
                        $visitData->where('date_time','>=',$plan_date_start)->where('date_time','<=',$plan_date_end);
                    }
                    $plan   = $planData->count();
                    $visit  = $visitData->count();

                    $cat = "cat_".strtolower($company_category)."_";
                    $visitSummary[$cat.'target'] = $target;
                    $visitSummary[$cat.'plan'] = $plan;
                    $visitSummary[$cat.'visit'] = $visit;
                }
                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Sugarmill Visit History Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $visitSummary
                ]);
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function sugarmillSalesReport(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken'      => 'required|string',
            'updated_date'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $user_ecn = $user[0]->ECN;

                $updated_date = $data['updated_date'];
                $addQry = ' 1=1';

                if(!empty($data['month'])) {
                    $month = $data['month'];
                    $addQry .= ' AND sr.month="'.$month.'"';
                }
                if(!empty($data['year'])) {
                    $year = $data['year'];
                    $addQry .= ' AND sr.year="'.$year.'"';
                }
                if($updated_date != 0) {
                    $addQry .= ' AND sr.updated_at >="'.$updated_date.'"';
                }
                if(!empty($data['sugarmill_id'])) {
                    $sugarmill_id = $data['sugarmill_id'];
                    $addQry .= ' AND sr.sugarmill_id ="'.$sugarmill_id.'"';
                } else {
                    if($user_type=='bdo') {
                        $expFlecn = array($user_ecn);
                    } else {
                        $getTeamFlecn = $this->getTeamFlecn($user_id, $user_type);
                        if(($getTeamFlecn !=0) && count($getTeamFlecn)>0) {
                            $Flecn = $getTeamFlecn[0]->ecn;
                            $expFlecn = explode(',', $Flecn);
                        } else {
                            $expFlecn = array();
                        }
                    }
                    $sugarmill = Sugarmill::whereIn('fl_ecn',$expFlecn)->where('fl_type','bdo')->where('status',1)->select('sugarmill_id')->get();
                    $sugarmill_ids = array();
                    foreach ($sugarmill as $key => $value) {
                        $sugarmill_ids[] = $sugarmill[$key]->sugarmill_id;
                    }
                    $imp_sugarmill_ids = "'".implode("','", $sugarmill_ids)."'";
                    $addQry .= ' AND sr.sugarmill_id IN('.$imp_sugarmill_ids.')';
                    // return $imp_sugarmill_ids;
                }

                $sales_report = " SELECT
                                    sr.sugarmill_sales_report_id as id,
                                    s.sugarmill_id,
                                    s.name as sugarmill_name,
                                    sr.month,
                                    sr.year,
                                    sr.sales_turnover as sales_figure
                                FROM sugarmill_sales_report sr
                                LEFT JOIN sugarmill s ON sr.sugarmill_id=s.sugarmill_id
                                WHERE $addQry 
                                ORDER BY sr.sugarmill_sales_report_id ASC";
                // return $sales_report;
                $sales_report_detail = DB::select(DB::raw($sales_report));

                return $this->respond([
                    'status'    => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message'   => 'Sugarmill Sales Report List Found',
                    'crr_date'  => date('Y-m-d H:i:s'),
                    'data'      => $sales_report_detail
                ]);
            }
        }
    }

    public function getMarkVisit_old(Request $request) {
        $rules = array(
            'apiToken' => 'required|string'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);

            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                //$base_url = url('/');
                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                if ($user_type == 'bdo') {
                    //bdo dd list
                    $ddData = DB::table('dhanuka_doctor')
                              ->where('bdo_id',$user_id)
                              ->where('status','1')
                              ->select('dd_id')
                              ->get();
                    $ddresult = array();
                    if (isset($ddData) && !empty($ddData)) {
                        $ddDatass = json_decode(json_encode($ddData),true);
                        foreach ($ddDatass as $key => $value) {
                            if (is_array($value)) {
                                $ddresult = array_merge($ddresult,array_flatten($value));
                            } else {
                              $ddresult[$key] = $value;
                            }
                        }
                    }
                    $markVisit=MarkVisit::join('villages','villages.village_id','=','mark_visits.village_id','left')
                            ->join('village_farmers','village_farmers.farmer_id','=','mark_visits.farmer_id','left');
                            if (isset($ddresult) && !empty($ddresult)) {
                                $markVisit->whereIn('mark_visits.dd_id',$ddresult);
                            }
                            $markVisit->select('mark_visits.mark_visit_id as markVisitId','villages.village','mark_visits.crop_id','mark_visits.latitude','mark_visits.longitude','mark_visits.distance','mark_visits.is_valid','mark_visits.radius','village_farmers.farmer_name as farmerName','mark_visits.created_at','visit_image','village_farmers.call_status','village_farmers.farmer_id','village_farmers.contact_number');
                            if($request->has('farmerId')){
                                $farmerId = $request->input('farmerId');
                                $markVisit->where('mark_visits.farmer_id',$farmerId);
                            }
                            if($request->has('villageId')){
                                $villageId = $request->input('villageId');
                                $markVisit->where('mark_visits.village_id',$villageId);
                            }
                            if($request->has('from_date') && $request->has('to_date')){
                                $from_date = $request->input('from_date');
                                $to_date   = $request->input('to_date');
                                $markVisit->whereBetween('mark_visits.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }
                            if($request->has('updated_at')){
                                $updated_at = $request->input('updated_at');
                                if($updated_at!=0) {
                                    $markVisit->where('mark_visits.updated_at','>=',$updated_at);
                                }
                            }
                            $markVisit=$markVisit->orderBy('mark_visits.mark_visit_id', 'DESC')->get();
                    //echo $markVisit;die;
                    for($i=0;$i<count($markVisit);$i++){
                        $createdAt = Carbon::parse($markVisit[$i]->created_at);
                        $markVisit[$i]->createdDate=$createdAt->format('d-m-Y');
                        $markVisit[$i]->visitImage= $base_url.'/assets/visit/'.$markVisit[$i]->visit_image;
                        $lead=DB::table('leads')->where('farmer_id',$markVisit[$i]->farmer_id)->first();
                        $markVisit[$i]->lead_status=($lead)?$lead->lead_status:0;
                        $farmerCrop=VillageFarmerCrop::join('products','products.product_id','=','village_farmer_crop.product_id')
                                    ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id')
                                    ->where('village_farmer_crop.farmer_id',$markVisit[$i]->farmer_id)
                                    ->select('products.product_id','products.product','crops.crop_id','crops.crop')
                                    ->get();
                        $markVisit[$i]->farmerProducts=$farmerCrop;
                        $Crop=  DB::table('crops')
                                ->where('crops.crop_id',$markVisit[$i]->crop_id)
                                ->select('crops.crop')
                                ->get();
                        $markVisit[$i]->crop=$Crop[0]->crop;
                    }              
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Mark Visit List',
                        'data' =>$markVisit
                    ]);
                } elseif ($user_type == 'dhanuka_doctor') {
                    $markVisit=MarkVisit::join('villages','villages.village_id','=','mark_visits.village_id','left')
                               ->join('village_farmers','village_farmers.farmer_id','=','mark_visits.farmer_id','left')
                               ->where('mark_visits.dd_id',$user_id) 
                               ->select('mark_visits.mark_visit_id as markVisitId','villages.village','mark_visits.crop_id','mark_visits.latitude','mark_visits.longitude','mark_visits.distance','mark_visits.is_valid','mark_visits.radius','village_farmers.farmer_name as farmerName','mark_visits.created_at','visit_image','village_farmers.call_status','village_farmers.farmer_id','village_farmers.contact_number');
                        if($request->has('farmerId')){
                            $farmerId=$request->input('farmerId');
                            $markVisit->where('mark_visits.farmer_id',$farmerId);
                        }
                        if($request->has('villageId')){
                            $villageId=$request->input('villageId');
                            $markVisit->where('mark_visits.village_id',$villageId);
                        }
                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            $markVisit->whereBetween('mark_visits.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                        }
                        if($request->has('updated_at')){
                            $updated_at = $request->input('updated_at');
                            if($updated_at!=0) {
                                $markVisit->where('mark_visits.updated_at','>=',$updated_at);
                            }
                        }
                        $markVisit=$markVisit->orderBy('mark_visits.mark_visit_id', 'DESC')->get();
                    //echo $markVisit;die;
                    for($i=0;$i<count($markVisit);$i++){
                        $createdAt = Carbon::parse($markVisit[$i]->created_at);
                        $markVisit[$i]->createdDate=$createdAt->format('d-m-Y');
                        $markVisit[$i]->visitImage=$base_url."/assets/visit/".$markVisit[$i]->visit_image;
                        $lead=DB::table('leads')->where('farmer_id',$markVisit[$i]->farmer_id)->first();
                        $markVisit[$i]->lead_status=($lead)?$lead->lead_status:0;
                        $farmerCrop=VillageFarmerCrop::join('products','products.product_id','=','village_farmer_crop.product_id')
                            ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id')
                            ->where('village_farmer_crop.farmer_id',$markVisit[$i]->farmer_id)
                            ->select('products.product_id','products.product','crops.crop_id','crops.crop')
                            ->get();
                        $markVisit[$i]->farmerProducts=$farmerCrop;
                        $Crop=  DB::table('crops')
                                ->where('crops.crop_id',$markVisit[$i]->crop_id)
                                ->select('crops.crop')
                                ->get();
                        $markVisit[$i]->crop=$Crop[0]->crop;                        
                    }              
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Mark Visit List',
                        'data' =>$markVisit
                    ]);
                } else {
                    if ($user_type == 'zonal') {
                        //zonal manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('zonal_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddresult = array();
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddresult = array_merge($ddresult, array_flatten($value));
                                } else {
                                    $ddresult[$key] = $value; 
                                }
                            }
                        }
                    } elseif ($user_type == 'regional') {
                        //regional manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('regional_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddresult = array();
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                } else {
                                    $ddresult[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user_type == 'area') {
                        //area manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('area_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddresult = array();
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddresult = array_merge($ddresult, array_flatten($value));
                                } else {
                                    $ddresult[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user_type == 'sales_regional') {
                        //sales regional manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('sales_regional_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddresult = array(); 
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddresult = array_merge($ddresult, array_flatten($value));
                                } else {
                                    $ddresult[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user_type == 'sales_area') {
                        //Sales area manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('sales_area_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddresult = array();
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddresult = array_merge($ddresult, array_flatten($value));
                                } else {
                                    $ddresult[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user_type == 'marketing_supervisor') {
                        //Marketing Supervisor RSM LIST
                        $rsmData = DB::table('marketing_supervisor_rsm')
                                  ->where('marketing_supervisor_id',$user_id)
                                  ->select('sales_regional_manager_id')
                                  ->get();
                        $rsmresult = array();
                        if (isset($rsmData) && !empty($rsmData)) {
                            //RSM LIST
                            $rsmDatass = json_decode(json_encode($rsmData),true);
                            foreach ($rsmDatass as $key => $value) {
                                if (is_array($value)) {
                                    $rsmresult = array_merge($rsmresult, array_flatten($value));
                                } else {
                                    $rsmresult[$key] = $value;
                                }
                            }
                            if (count($rsmresult) > 0) {
                                //marketing supervisor dd
                                $ddData = DB::table('dhanuka_doctor')
                                          ->whereIn('sales_regional_manager_id',$rsmresult)
                                          ->where('status','1')
                                          ->select('dd_id')
                                          ->get();
                                $ddresult = array();
                                if (isset($ddData) && !empty($ddData)) {
                                    $ddDatass = json_decode(json_encode($ddData),true);
                                    foreach ($ddDatass as $key => $value) {
                                        if (is_array($value)) {
                                            $ddresult = array_merge($ddresult, array_flatten($value));
                                        } else {
                                            $ddresult[$key] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $markVisit=MarkVisit::join('villages','villages.village_id','=','mark_visits.village_id','left')
                            ->join('village_farmers','village_farmers.farmer_id','=','mark_visits.farmer_id','left');
                            if (isset($ddresult) && !empty($ddresult)) {
                                $markVisit->whereIn('mark_visits.dd_id',$ddresult);
                            } else {
                                $markVisit->where('mark_visits.dd_id','123asd');
                            }
                            $markVisit->select('mark_visits.mark_visit_id as markVisitId','villages.village','mark_visits.crop_id','mark_visits.latitude','mark_visits.longitude','mark_visits.distance','mark_visits.is_valid','mark_visits.radius','village_farmers.farmer_name as farmerName','mark_visits.created_at','visit_image','village_farmers.call_status','village_farmers.farmer_id','village_farmers.contact_number');
                            if($request->has('farmerId')){
                                $farmerId=$request->input('farmerId');
                                $markVisit->where('mark_visits.farmer_id',$farmerId);
                            }
                            if($request->has('villageId')){
                                $villageId=$request->input('villageId');
                                $markVisit->where('mark_visits.village_id',$villageId);
                            }
                            if($request->has('from_date') && $request->has('to_date')){
                                $from_date=$request->input('from_date');
                                $to_date=$request->input('to_date');
                                $markVisit->whereBetween('mark_visits.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }
                            if($request->has('updated_at')){
                                $updated_at = $request->input('updated_at');
                                if($updated_at!=0) {
                                    $markVisit->where('mark_visits.updated_at','>=',$updated_at);
                                }
                            }
                            $markVisit=$markVisit->orderBy('mark_visits.mark_visit_id', 'DESC')->get();
                    //echo $markVisit;die;
                    for($i=0;$i<count($markVisit);$i++){
                        $createdAt = Carbon::parse($markVisit[$i]->created_at);
                        $markVisit[$i]->createdDate=$createdAt->format('d-m-Y');
                        $markVisit[$i]->visitImage= $base_url.'/assets/visit/'.$markVisit[$i]->visit_image;
                        $lead=DB::table('leads')->where('farmer_id',$markVisit[$i]->farmer_id)->first();
                        $markVisit[$i]->lead_status=($lead)?$lead->lead_status:0;
                        $farmerCrop=VillageFarmerCrop::join('products','products.product_id','=','village_farmer_crop.product_id')
                                    ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id')
                                    ->where('village_farmer_crop.farmer_id',$markVisit[$i]->farmer_id)
                                    ->select('products.product_id','products.product','crops.crop_id','crops.crop')
                                    ->get();
                        $markVisit[$i]->farmerProducts=$farmerCrop;
                        $Crop=  DB::table('crops')
                                ->where('crops.crop_id',$markVisit[$i]->crop_id)
                                ->select('crops.crop')
                                ->get();
                        $markVisit[$i]->crop=$Crop[0]->crop;
                    }
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'Mark Visit List',
                        'data' =>$markVisit
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function getMarkVisit(Request $request) {
        $rules = array(
            'apiToken' => 'required|string'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);

            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                //$base_url = url('/');
                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                if ($user_type == 'bdo') {
                    //bdo dd list
                    $ddData = DB::table('dhanuka_doctor')
                              ->where('bdo_id',$user_id)
                              ->where('status','1')
                              ->select('dd_id')
                              ->get();
                    $ddresult = array();
                    if (isset($ddData) && !empty($ddData)) {
                        $ddDatass = json_decode(json_encode($ddData),true);
                        foreach ($ddDatass as $key => $value) {
                            if (is_array($value)) {
                                $ddresult = array_merge($ddresult,array_flatten($value));
                            } else {
                              $ddresult[$key] = $value;
                            }
                        }
                    }
                    $markVisit=MarkVisit::join('villages','villages.village_id','=','mark_visits.village_id','left')
                            ->join('village_farmers','village_farmers.farmer_id','=','mark_visits.farmer_id','left');
                            if (isset($ddresult) && !empty($ddresult)) {
                                $markVisit->whereIn('mark_visits.dd_id',$ddresult);
                            }
                            $markVisit->select('mark_visits.mark_visit_id as markVisitId','villages.village','mark_visits.crop_id','mark_visits.latitude','mark_visits.longitude','mark_visits.distance','mark_visits.is_valid','mark_visits.radius','village_farmers.farmer_name as farmerName','mark_visits.created_at','visit_image','village_farmers.call_status','village_farmers.farmer_id','village_farmers.contact_number');
                            $markVisit->whereNotNull('village_farmers.farmer_id');
                            if($request->has('farmerId')){
                                $farmerId = $request->input('farmerId');
                                $markVisit->where('mark_visits.farmer_id',$farmerId);
                            }
                            if($request->has('villageId')){
                                $villageId = $request->input('villageId');
                                $markVisit->where('mark_visits.village_id',$villageId);
                            }
                            if($request->has('from_date') && $request->has('to_date')){
                                $from_date = $request->input('from_date');
                                $to_date   = $request->input('to_date');
                                $markVisit->whereBetween('mark_visits.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }
                            if($request->has('updated_at')){
                                $updated_at = $request->input('updated_at');
                                if($updated_at!=0) {
                                    $markVisit->where('mark_visits.updated_at','>=',$updated_at);
                                }
                            }
                            $markVisit=$markVisit->orderBy('mark_visits.mark_visit_id', 'DESC')->get();
                    //echo $markVisit;die;
                    for($i=0;$i<count($markVisit);$i++){                        
                        $createdAt = Carbon::parse($markVisit[$i]->created_at);
                        $markVisit[$i]->createdDate=$createdAt->format('d-m-Y');
                        $markVisit[$i]->visitImage= $base_url.'/assets/visit/'.$markVisit[$i]->visit_image;
                        $lead=DB::table('leads')->where('farmer_id',$markVisit[$i]->farmer_id)->first();
                        $markVisit[$i]->lead_status=($lead)?$lead->lead_status:0;
                        $farmerCrop=VillageFarmerCrop::join('products','products.product_id','=','village_farmer_crop.product_id')
                                    ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id')
                                    ->where('village_farmer_crop.farmer_id',$markVisit[$i]->farmer_id)
                                    ->select('products.product_id','products.product','crops.crop_id','crops.crop')
                                    ->get();
                        $markVisit[$i]->farmerProducts=$farmerCrop;
                        $Crop=  DB::table('crops')
                                ->where('crops.crop_id',$markVisit[$i]->crop_id)
                                ->select('crops.crop')
                                ->get();
                        $markVisit[$i]->crop=$Crop[0]->crop;
                    }
                    $markVisitData      = json_decode($markVisit, true);
                    $markVisitNewData   = array_values($markVisitData);
                    return $this->respond([
                        'status'    => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message'   => 'Mark Visit List',
                        'data'      => $markVisitNewData
                        // 'data' =>$markVisit
                    ]);
                } elseif ($user_type == 'dhanuka_doctor') {
                    $markVisit=MarkVisit::join('villages','villages.village_id','=','mark_visits.village_id','left')
                               ->join('village_farmers','village_farmers.farmer_id','=','mark_visits.farmer_id','left')
                               ->where('mark_visits.dd_id',$user_id) 
                               ->select('mark_visits.mark_visit_id as markVisitId','villages.village','mark_visits.crop_id','mark_visits.latitude','mark_visits.longitude','mark_visits.distance','mark_visits.is_valid','mark_visits.radius','village_farmers.farmer_name as farmerName','mark_visits.created_at','visit_image','village_farmers.call_status','village_farmers.farmer_id','village_farmers.contact_number');
                        $markVisit->whereNotNull('village_farmers.farmer_id');
                        if($request->has('farmerId')){
                            $farmerId=$request->input('farmerId');
                            $markVisit->where('mark_visits.farmer_id',$farmerId);
                        }
                        if($request->has('villageId')){
                            $villageId=$request->input('villageId');
                            $markVisit->where('mark_visits.village_id',$villageId);
                        }
                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            $markVisit->whereBetween('mark_visits.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                        }
                        if($request->has('updated_at')){
                            $updated_at = $request->input('updated_at');
                            if($updated_at!=0) {
                                $markVisit->where('mark_visits.updated_at','>=',$updated_at);
                            }
                        }
                        $markVisit=$markVisit->orderBy('mark_visits.mark_visit_id', 'DESC')->get();
                    //echo $markVisit;die;
                    for($i=0;$i<count($markVisit);$i++){
                        $createdAt = Carbon::parse($markVisit[$i]->created_at);
                        $markVisit[$i]->createdDate=$createdAt->format('d-m-Y');
                        $markVisit[$i]->visitImage=$base_url."/assets/visit/".$markVisit[$i]->visit_image;
                        $lead=DB::table('leads')->where('farmer_id',$markVisit[$i]->farmer_id)->first();
                        $markVisit[$i]->lead_status=($lead)?$lead->lead_status:0;
                        $farmerCrop=VillageFarmerCrop::join('products','products.product_id','=','village_farmer_crop.product_id')
                            ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id')
                            ->where('village_farmer_crop.farmer_id',$markVisit[$i]->farmer_id)
                            ->select('products.product_id','products.product','crops.crop_id','crops.crop')
                            ->get();
                        $markVisit[$i]->farmerProducts=$farmerCrop;
                        $Crop=  DB::table('crops')
                                ->where('crops.crop_id',$markVisit[$i]->crop_id)
                                ->select('crops.crop')
                                ->get();
                        $markVisit[$i]->crop=$Crop[0]->crop;
                    }
                    $markVisitData      = json_decode($markVisit, true);
                    $markVisitNewData   = array_values($markVisitData);
                    // $markVisitNewData = json_encode($reindexedmarkVisitData, true);

                    return $this->respond([
                        'status'    => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message'   => 'Mark Visit List',
                        'data'      => $markVisitNewData
                        // 'data' => $markVisit
                    ]);
                } else {
                    if ($user_type == 'zonal') {
                        //zonal manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('zonal_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddresult = array();
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddresult = array_merge($ddresult, array_flatten($value));
                                } else {
                                    $ddresult[$key] = $value; 
                                }
                            }
                        }
                    } elseif ($user_type == 'regional') {
                        //regional manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('regional_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddresult = array();
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddresult = array_merge($ddresult, array_flatten($value)); 
                                } else {
                                    $ddresult[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user_type == 'area') {
                        //area manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('area_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddresult = array();
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddresult = array_merge($ddresult, array_flatten($value));
                                } else {
                                    $ddresult[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user_type == 'sales_regional') {
                        //sales regional manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('sales_regional_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddresult = array(); 
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddresult = array_merge($ddresult, array_flatten($value));
                                } else {
                                    $ddresult[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user_type == 'sales_area') {
                        //Sales area manager dd
                        $ddData = DB::table('dhanuka_doctor')
                                  ->where('sales_area_manager_id',$user_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        $ddresult = array();
                        if (isset($ddData) && !empty($ddData)) {
                            $ddDatass = json_decode(json_encode($ddData),true);
                            foreach ($ddDatass as $key => $value) {
                                if (is_array($value)) {
                                    $ddresult = array_merge($ddresult, array_flatten($value));
                                } else {
                                    $ddresult[$key] = $value;
                                }
                            }
                        }
                    } elseif ($user_type == 'marketing_supervisor') {
                        //Marketing Supervisor RSM LIST
                        $rsmData = DB::table('marketing_supervisor_rsm')
                                  ->where('marketing_supervisor_id',$user_id)
                                  ->select('sales_regional_manager_id')
                                  ->get();
                        $rsmresult = array();
                        if (isset($rsmData) && !empty($rsmData)) {
                            //RSM LIST
                            $rsmDatass = json_decode(json_encode($rsmData),true);
                            foreach ($rsmDatass as $key => $value) {
                                if (is_array($value)) {
                                    $rsmresult = array_merge($rsmresult, array_flatten($value));
                                } else {
                                    $rsmresult[$key] = $value;
                                }
                            }
                            if (count($rsmresult) > 0) {
                                //marketing supervisor dd
                                $ddData = DB::table('dhanuka_doctor')
                                          ->whereIn('sales_regional_manager_id',$rsmresult)
                                          ->where('status','1')
                                          ->select('dd_id')
                                          ->get();
                                $ddresult = array();
                                if (isset($ddData) && !empty($ddData)) {
                                    $ddDatass = json_decode(json_encode($ddData),true);
                                    foreach ($ddDatass as $key => $value) {
                                        if (is_array($value)) {
                                            $ddresult = array_merge($ddresult, array_flatten($value));
                                        } else {
                                            $ddresult[$key] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $markVisit=MarkVisit::join('villages','villages.village_id','=','mark_visits.village_id','left')
                            ->join('village_farmers','village_farmers.farmer_id','=','mark_visits.farmer_id','left');
                            if (isset($ddresult) && !empty($ddresult)) {
                                $markVisit->whereIn('mark_visits.dd_id',$ddresult);
                            } else {
                                $markVisit->where('mark_visits.dd_id','123asd');
                            }
                            $markVisit->select('mark_visits.mark_visit_id as markVisitId','villages.village','mark_visits.crop_id','mark_visits.latitude','mark_visits.longitude','mark_visits.distance','mark_visits.is_valid','mark_visits.radius','village_farmers.farmer_name as farmerName','mark_visits.created_at','visit_image','village_farmers.call_status','village_farmers.farmer_id','village_farmers.contact_number');
                            $markVisit->whereNotNull('village_farmers.farmer_id');
                            if($request->has('farmerId')){
                                $farmerId=$request->input('farmerId');
                                $markVisit->where('mark_visits.farmer_id',$farmerId);
                            }
                            if($request->has('villageId')){
                                $villageId=$request->input('villageId');
                                $markVisit->where('mark_visits.village_id',$villageId);
                            }
                            if($request->has('from_date') && $request->has('to_date')){
                                $from_date=$request->input('from_date');
                                $to_date=$request->input('to_date');
                                $markVisit->whereBetween('mark_visits.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }
                            if($request->has('updated_at')){
                                $updated_at = $request->input('updated_at');
                                if($updated_at!=0) {
                                    $markVisit->where('mark_visits.updated_at','>=',$updated_at);
                                }
                            }
                            $markVisit=$markVisit->orderBy('mark_visits.mark_visit_id', 'DESC')->get();
                    //echo $markVisit;die;
                    for($i=0;$i<count($markVisit);$i++){
                        $createdAt = Carbon::parse($markVisit[$i]->created_at);
                        $markVisit[$i]->createdDate=$createdAt->format('d-m-Y');
                        $markVisit[$i]->visitImage= $base_url.'/assets/visit/'.$markVisit[$i]->visit_image;
                        $lead=DB::table('leads')->where('farmer_id',$markVisit[$i]->farmer_id)->first();
                        $markVisit[$i]->lead_status=($lead)?$lead->lead_status:0;
                        $farmerCrop=VillageFarmerCrop::join('products','products.product_id','=','village_farmer_crop.product_id')
                                    ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id')
                                    ->where('village_farmer_crop.farmer_id',$markVisit[$i]->farmer_id)
                                    ->select('products.product_id','products.product','crops.crop_id','crops.crop')
                                    ->get();
                        $markVisit[$i]->farmerProducts=$farmerCrop;
                        $Crop=  DB::table('crops')
                                ->where('crops.crop_id',$markVisit[$i]->crop_id)
                                ->select('crops.crop')
                                ->get();
                        $markVisit[$i]->crop=$Crop[0]->crop;
                    }
                    $markVisitData      = json_decode($markVisit, true);
                    $markVisitNewData   = array_values($markVisitData);
                    return $this->respond([
                        'status'    => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message'   => 'Mark Visit List',
                        'data'      => $markVisitNewData
                        // 'data' =>$markVisit
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function appmenu($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $base_url = url('/');
                $ticket_data = "SELECT ticket_id as tID, ticket_no as ticket, created_at as datetime, DATE_FORMAT(created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format FROM tickets WHERE status = '1' AND user_id = '" . $user_id . "'LIMIT 0,1";
                $ticket_list = DB::select(DB::raw($ticket_data));

                $result = array_map(function ($value) {
                    return (array) $value;
                }, $ticket_list);

                $get_user_permission = DB::table('app_menu_permissions')
                        ->select('app_menu_permissions.app_menu_id as mID', 'app_menus.menu as Item', 'app_menus.slag as tagName', 'app_menus.image as tag_icon', DB::raw("'" . $base_url . "/' as base_url"))
                        ->leftJoin('app_menus', 'app_menus.menu_id', '=', 'app_menu_permissions.app_menu_id')
                        ->where('app_menu_permissions.user_type', $user_type)
                        ->where('app_menu_permissions.user_id', $user_id)
                        ->where('app_menu_permissions.status', '1')
                        ->where('app_menus.status', '1')
                        ->get();
                
                /*DB::enableQueryLog();
                $querylog = DB::getQueryLog();
                Log::info($querylog);
                Log::info("get_user_permission=>");
                Log::info($get_user_permission);*/

                $languages = DB::table('languages')->select('id','language')->where('status', 1)->get();

                $data['languages']=array();


                $get_deptartment_permission = DB::table('app_menu_permissions')
                        ->select('app_menu_permissions.app_menu_id as mID', 'app_menus.menu as Item', 'app_menus.slag as tagName', 'app_menus.image as tag_icon', DB::raw("'" . $base_url . "/' as base_url"))
                        ->leftJoin('app_menus', 'app_menus.menu_id', '=', 'app_menu_permissions.app_menu_id')
                        ->where('app_menu_permissions.department', $user_type)
                        ->where('app_menu_permissions.status', '1')
                        ->where('app_menus.status', '1')
                        ->get();

                /*DB::enableQueryLog();
                $querylog = DB::getQueryLog();
                Log::info($querylog);
                Log::info("get_user_permission=>");
                Log::info($get_user_permission);*/
                //echo "<pre>";print_r($get_user_permission);die;
                if ($get_user_permission) {
                    $menu_list = $get_user_permission;
                    // $data = array('menulist' => $get_deptartment_permission, 'ticketdata' => $result);
                } else {
                    $menu_list = $get_deptartment_permission;
                    // $data = array('menulist' => $get_user_permission, 'ticketdata' => $result);
                }
                $a=0;
                foreach ($menu_list as $menu) {
                    if($menu->Item == "Demo Requests") {
                        $menu_item = str_replace('Demo Requests', 'Create Demo', $menu->Item);
                        $menu_tagname = str_replace('demo_requests', 'create_demo', $menu->tagName);
                    } elseif ($menu->Item == "Demo Request") {
                        $menu_item = str_replace('Demo Request', 'Demo Summary', $menu->Item);
                        $menu_tagname = str_replace('demo_request', 'demo_summary', $menu->tagName);
                    } else {
                        $menu_item = $menu->Item;
                        $menu_tagname = $menu->tagName;
                    }
                    $menu_list[$a]->Item = $menu_item;
                    $menu_list[$a]->tagName = $menu_tagname;
                    $a++;
                }
                $data = array('menulist' => $menu_list, 'ticketdata' => $result);
                
                if($languages){
                    $data['languages']=$languages;
                }

                $active_log = ActiveLog::where(['user_id' => $user_id, 'user_type' => $user_type])->first();
                if ($active_log) {
                    $userlog = array(
                        'user_id' => $user_id,
                        'user_type' => $user_type
                    );
                    ActiveLog::where('user_id', $user_id)->where('user_type', $user_type)->update(['last_access_time' => date('Y-m-d H:i:s')]);
                } else {
                    $userlog = array(
                        'user_id' => $user_id,
                        'user_type' => $user_type
                    );
                    ActiveLog::firstOrCreate($userlog);
                }

                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Menu Item Found!',
                            'data' => $data
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function updatedappmenu($api_token = '', $device_id = '0') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $base_url = url('/');
                //$base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                $ticket_data = "SELECT ticket_id as tID, ticket_no as ticket, created_at as datetime, DATE_FORMAT(created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format FROM tickets WHERE status = '1' AND user_id = '" . $user_id . "' LIMIT 0,1";
                $ticket_list = DB::select(DB::raw($ticket_data));
                $result = array_map(function ($value) {
                    return (array) $value;
                }, $ticket_list);
                $get_user_permission = DB::table('app_menu_permissions')
                        ->select('app_menu_permissions.app_menu_id as mID', 'app_menus.menu as Item', 'app_menus.slag as tagName', 'app_menus.image as tag_icon', DB::raw("'" . $base_url . "/' as base_url"), 'dropbox_url')
                        ->leftJoin('app_menus', 'app_menus.menu_id', '=', 'app_menu_permissions.app_menu_id')
                        ->where('app_menu_permissions.user_type', $user_type)
                        ->where('app_menu_permissions.user_id', $user_id)
                        ->where('app_menu_permissions.status', '1')
                        ->where('app_menus.status', '1')
                        ->get();

                $get_deptartment_permission = DB::table('app_menu_permissions')
                        ->select('app_menu_permissions.app_menu_id as mID', 'app_menus.menu as Item', 'app_menus.slag as tagName', 'app_menus.image as tag_icon', DB::raw("'" . $base_url . "/' as base_url"), 'dropbox_url')
                        ->leftJoin('app_menus', 'app_menus.menu_id', '=', 'app_menu_permissions.app_menu_id')
                        ->where('app_menu_permissions.department', $user_type)
                        ->where('app_menu_permissions.status', '1')
                        ->where('app_menus.status', '1')
                        ->get();
                
                if ($get_user_permission) {
                    $menu_list = $get_user_permission;
                    // $data = array('menulist' => $get_user_permission, 'ticketdata' => $result);
                } else {
                    $menu_list = $get_deptartment_permission;
                    // $data = array('menulist' => $get_deptartment_permission, 'ticketdata' => $result);
                }
                $a=0;
                foreach ($menu_list as $menu) {
                    if($menu->Item == "Demo Requests") {
                        $menu_item = str_replace('Demo Requests', 'Create Demo', $menu->Item);
                        $menu_tagname = str_replace('demo_requests', 'create_demo', $menu->tagName);
                    } elseif ($menu->Item == "Demo Request") {
                        $menu_item = str_replace('Demo Request', 'Demo Summary', $menu->Item);
                        $menu_tagname = str_replace('demo_request', 'demo_summary', $menu->tagName);
                    } else {
                        $menu_item = $menu->Item;
                        $menu_tagname = $menu->tagName;
                    }
                    $menu_list[$a]->Item = $menu_item;
                    $menu_list[$a]->tagName = $menu_tagname;
                    $a++;
                }
                $data = array('menulist' => $menu_list, 'ticketdata' => $result);

                $languages = DB::table('languages')->select('id','language')->where('status', 1)->get();
                $data['languages']=array();
                if($languages){
                    $data['languages']=$languages;
                }

                $active_log = ActiveLog::where(['user_id' => $user_id, 'user_type' => $user_type])->first();
                if ($active_log) {
                    $userlog = array(
                        'user_id' => $user_id,
                        'user_type' => $user_type
                    );
                    ActiveLog::where('user_id', $user_id)->where('user_type', $user_type)->update(['last_access_time' => date('Y-m-d H:i:s')]);
                } else {
                    $userlog = array(
                        'user_id' => $user_id,
                        'user_type' => $user_type
                    );
                    ActiveLog::firstOrCreate($userlog);
                }

                $app_latest_version = DB::table('latest_app_version')
                                      ->select('app_version')
                                      ->get();
                if (count($app_latest_version)>0) {
                    $data['app_latest_version']= $app_latest_version[0]->app_version;
                }
                else{
                    $data['app_latest_version']="";
                }
                if($user_type == 'dhanuka_doctor') {
                    $activity_permission = DB::table('dhanuka_doctor')->where('dd_id',$user_id)->select('dd_id','activity_permission')->get();
                    if(count($activity_permission)>0) {
                        $data['activity_permission'] = $activity_permission[0]->activity_permission;
                    } else {
                        $data['activity_permission'] = 0;
                    }
                } else {
                    $data['activity_permission'] = 0;
                }
                if ($device_id != '0' && $device_id != '') {
                    switch ($user_type) {
                        case "zonal" : ZonalManager::where('api_token', $api_token)->update(['device_id' => $device_id]);
                            break;
                        case "regional" : RegionalManager::where('api_token', $api_token)->update(['device_id' => $device_id]);
                            break;
                        case "area" : AreaManager::where('api_token', $api_token)->update(['device_id' => $device_id]);
                            break;
                        case "sales_regional" : SalesRegionalManager::where('api_token', $api_token)->update(['device_id' => $device_id]);
                            break;
                        case "sales_area" : SalesAreaManager::where('api_token', $api_token)->update(['device_id' => $device_id]);
                            break;
                        case "bdo" : User::where('api_token', $api_token)->update(['device_id' => $device_id]);
                            break;
                        case "dhanuka_doctor" : DhanukaDoctor::where('api_token', $api_token)->update(['device_id' => $device_id]);
                            break;
                        case "general" : GeneralUser::where('api_token', $api_token)->update(['device_id' => $device_id]);
                            break;
                    }
                }
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Menu Item Found!',
                            'data' => $data
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function updateTicketIfcData() {
        $query = 'SELECT id, ticket_no, SUM( activity_distance ) AS total_distance FROM `ifc_farmer` GROUP BY ticket_no ORDER BY `id` DESC';
        $ifcData = DB::select(DB::raw($query));
        // return $ifcData;
        foreach ($ifcData as $key => $value) {
            $ticket_no = $value->ticket_no;
            // echo "'".$ticket_no."',";
            $total_distance = $value->total_distance;
            $ticket_query = "SELECT (SELECT image_date_time FROM ifc_farmer WHERE ticket_no='".$ticket_no."' ORDER BY id LIMIT 1) as 'first_image_time',
            (SELECT image_date_time FROM ifc_farmer WHERE ticket_no='".$ticket_no."' ORDER BY id DESC LIMIT 1) as 'last_image_time'";
            $ifcTicket = DB::select(DB::raw($ticket_query));
            
            $first_image_time = $ifcTicket[0]->first_image_time;
            $last_image_time = $ifcTicket[0]->last_image_time;
            $day1 = strtotime($first_image_time);
            $day2 = strtotime($last_image_time);
            $diffMinutes = round(($day2 - $day1) / 60);
            $diffHours = round(($day2 - $day1) / 3600);
            $activityMinites = $diffMinutes+60;
            $activityHours = $activityMinites/60;

            Ticket::where(['ticket_no' => $ticket_no, 'data_source_id' => 6])->update(['total_activity_distance' => $total_distance,'activity_duration' => $activityHours,'updated_at'=>date('Y-m-d H:i:s')]);
            // echo "ticket_no=>".$ticket_no." first_image_time=>".$ifcTicket[0]->first_image_time." last_image_time=>".$ifcTicket[0]->last_image_time."<br/>";
            echo "=>Updated Ticket =>".$ticket_no."--total_activity_distance =>".$total_distance."--activity_duration=>".$activityHours."<br/>";
        }
    }

    public function updateTicketIfcUpdateImage() {
        $query = 'SELECT id, ticket_no FROM `ifc_farmer` GROUP BY ticket_no ORDER BY `id` DESC';
        $ifcData = DB::select(DB::raw($query));
        foreach ($ifcData as $key => $value) {
            $ticket_no = $value->ticket_no;
            $ifc_query = "SELECT user_id,user_type,image FROM ifc_farmer WHERE ticket_no='".$ticket_no."' ORDER BY id ASC LIMIT 1";
            $ifcTicket = DB::select(DB::raw($ifc_query));
            $image = ($ifcTicket[0]->image) ? "assets/farmer/".$ifcTicket[0]->image : '';
            $user_id = $ifcTicket[0]->user_id;
            $user_type = $ifcTicket[0]->user_type;
            $userTypeVal = $this->getUserType($user_type,$user_id);

            Ticket::where(['ticket_no' => $ticket_no, 'data_source_id' => 6])->update(['image_upload_type'=>1,'uploaded_file' => $image,'uploadedBy' => $user_id,'uploadedByType' => $userTypeVal,'updated_at'=>date('Y-m-d H:i:s')]);

            echo "=>Updated Ticket =>".$ticket_no."--uploaded_file =>".$image."--uploadedBy =>".$user_id."--uploadedByType=>".$userTypeVal."<br/>";
        }
    }

    public function updatevillagelatlongByTicket(Request $request) {
        $data = $request->all();
        /*echo "<pre>";
        print_r($data);*/
        $error = '';
        $start_row = (isset($data['start_row'])) ? $data['start_row'] : 0;
        $end_row = (isset($data['end_row'])) ? $data['end_row'] : 0;

        $html = '<div style="width: 40%; border: 1px solid #d5d5d5; margin:2% 10% 1%; padding: 10px;">';
        if($error!='') {
            $html .= '<p style="color: red; font-size: 18px">'.$error.'</p>';
        }
        $html .= '<h3>Please enter start and end rows to update Latitude and Longitude</h3>';
        $html .= '<form name="updatevillage" id="updatevillage" action="" method="get">';
        $html .= '<p><lebel>Start Row: </lebel>';
        $html .= '<input type="text" name="start_row" id="start_row" value="'.$start_row.'" required="required"></p>';
        $html .= '<p><lebel>End Row: </lebel>';
        $html .= '<input type="text" name="end_row" id="end_row" value="'.$end_row.'" required="required"></p>';
        $html .= '<p style="padding-left: 30%;"><input type="submit" name="submit" value="Submit"></p>';
        $html .= '</form></div>';
        echo $html;

        if($start_row< 0 || $end_row==0) {
            $error = '*start and end row are required and end row must be greater than 0.';
            echo '<p style="color: red; font-size: 16px; margin:0% 10%;">'.$error.'</p>';
        } else {
            $start_row = $data['start_row'];
            $end_row = $data['end_row'];
            // $ticket_query = 'SELECT ticket_id,ticket_no,state_id,district_id,tehsil_id,village,village_id,latitude,longitude FROM `tickets` WHERE `latitude`!="" GROUP BY ticket_no ORDER BY `ticket_id` DESC LIMIT '.$start_row.','.$end_row;
            // echo "=>".$ticket_query; exit();
            $ticket_query = 'SELECT `ticket_id`,`ticket_no`,`state_id`,`district_id`,`tehsil_id`,`village`,`village_id`, COUNT(`village_id`) AS `villageCount`,`latitude`,`longitude` FROM `tickets` WHERE `latitude`!="" AND `longitude`!= "" AND `village_id`!=0 GROUP BY `village_id` ORDER BY `village_id` ASC LIMIT '.$start_row.','.$end_row;
            $ticketData = DB::select(DB::raw($ticket_query));
            echo "count=>".count($ticketData)."<br/>";
            $a=0;
            foreach ($ticketData as $key => $value) {
                ++$a;
                $district_id = $value->district_id;
                $tehsil_id   = $value->tehsil_id;
                $village     = $value->village;
                $latitude    = $value->latitude;
                $longitude   = $value->longitude;
                $village_id  = $value->village_id;

                $getVillages = Village::select('village_id','village')->where('village_id',$village_id)->whereNull('latitude')->orderBy('village_id', 'asc')->get();
                echo "getVillagesCount=>".count($getVillages);
                echo "<br/>";
                if(count($getVillages)>0) {
                    echo $a."-village_id=>".$village_id = $getVillages[0]->village_id."<br/>";
                    Village::where(['village_id' => $village_id])->update(['latitude' => $latitude,'longitude' => $longitude,'updated_at'=>date('Y-m-d H:i:s')]);
                    echo "Updated ".$village."-Latitude=>".$latitude."-Longitude=>".$longitude."<br/><br/>";
                } else {
                    echo $a."-village_id=>".$village_id = $value->village_id."<br/>";
                    continue;
                }
            }
        }
    }

    public function updatevillagelatlongByAddress(Request $request) {
        $data = $request->all();
        $error = '';
        $start_row = (isset($data['start_row'])) ? $data['start_row'] : 0;
        $end_row = (isset($data['end_row'])) ? $data['end_row'] : 0;

        $html = '<div style="width: 40%; border: 1px solid #d5d5d5; margin:2% 10% 1%; padding: 10px;">';
        if($error!='') {
            $html .= '<p style="color: red; font-size: 18px">'.$error.'</p>';
        }
        $html .= '<h3>Please enter start and end rows to update Latitude and Longitude</h3>';
        $html .= '<form name="updatevillage" id="updatevillage" action="" method="get">';
        $html .= '<p><lebel>Start Row: </lebel>';
        $html .= '<input type="text" name="start_row" id="start_row" value="'.$start_row.'" required="required"></p>';
        $html .= '<p><lebel>End Row: </lebel>';
        $html .= '<input type="text" name="end_row" id="end_row" value="'.$end_row.'" required="required"></p>';
        $html .= '<p style="padding-left: 30%;"><input type="submit" name="submit" value="Submit"></p>';
        $html .= '</form></div>';
        echo $html;

        if($start_row< 0 || $end_row==0) {
            $error = '*start and end row are required and end row must be greater than 0.';
            echo '<p style="color: red; font-size: 16px; margin:0% 10%;">'.$error.'</p>';
        } else {
            $start_row = $data['start_row'];
            $end_row = $data['end_row'];
            $village_query = '
                SELECT
                    v.village_id,
                    s.state_id,
                    s.state,
                    d.district_id,
                    d.district,
                    t.tehsil_id,
                    t.tehsil,
                    v.village,
                    v.latitude,
                    v.longitude
                FROM villages v
                LEFT JOIN districts d ON v.district_id=d.district_id
                LEFT JOIN states s ON d.state_id=s.state_id
                LEFT JOIN tehsils t ON v.tehsil_id=t.tehsil_id
                WHERE v.latitude IS NULL AND v.longitude IS NULL
                GROUP BY v.village_id
                ORDER BY v.village_id
                ASC LIMIT '.$start_row.','.$end_row;
            // echo $village_query; exit();
            $villageData = DB::select(DB::raw($village_query));
            echo "count=>".count($villageData)."<br/>";
            /*echo "<pre>";
            print_r($villageData);
            echo "</pre>";
            exit();*/
            $a=0;
            foreach ($villageData as $key => $value) {
                ++$a;
                $village     = $value->village;
                $tehsil      = $value->tehsil;
                $district    = $value->district;
                $state       = $value->state;
                $latitude    = $value->latitude;
                $longitude   = $value->longitude;
                $village_id  = $value->village_id;
                $address     = $village." ".$tehsil." ".$district." ".$state." India";
                echo "<br/>API Called: ".date("Y-m-d H:i:s")."<br/>";
                echo "Address=>".$address."<br/>";
                $queryString = http_build_query([
                    'access_key' => 'fccdb99c498780a22b6f7795036125cb',
                    'query' => $address,
                    'region' => $state,
                    'output' => 'json',
                    'limit' => 1,
                ]);
                $ch = curl_init(sprintf('%s?%s', 'http://api.positionstack.com/v1/forward', $queryString));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $json = curl_exec($ch);
                curl_close($ch);
                $apiResult = json_decode($json, true);
                /*echo "<pre>";
                print_r($apiResult);
                echo "</pre>";*/
                if(array_key_exists('data', $apiResult)) {
                    if(count($apiResult['data'])>0) {
                        if(array_key_exists('latitude', $apiResult['data'][0])) {
                            $latitude   = $apiResult['data'][0]['latitude'];
                        } else {
                            $logFile = getcwd()."/storage/village_info.txt";
                            $myfile = fopen($logFile, "a");
                            fwrite($myfile, json_encode($address)."\n");
                            fwrite($myfile, $village_id."\n");
                            fclose($myfile);
                            // $latitude   = NULL;
                            continue;
                        }
                        if(array_key_exists('longitude', $apiResult['data'][0])) {
                            $longitude   = $apiResult['data'][0]['longitude'];
                        } else {
                            $logFile = getcwd()."/storage/village_info.txt";
                            $myfile = fopen($logFile, "a");
                            fwrite($myfile, json_encode($address)."\n");
                            fwrite($myfile, $village_id."\n");
                            fclose($myfile);
                            continue;
                            // $longitude   = NULL;
                        }
                        echo $a."-village_id=>".$village_id."<br/>";
                        Village::where(['village_id' => $village_id])->update(['latitude' => $latitude,'longitude' => $longitude,'updated_at'=>date('Y-m-d H:i:s')]);
                        echo "Updated ".$village."-Latitude=>".$latitude."-Longitude=>".$longitude."<br/>";
                    } else {
                        $logFile = getcwd()."/storage/village_info.txt";
                        $myfile = fopen($logFile, "a");
                        fwrite($myfile, json_encode($address)."\n");
                        fwrite($myfile, $village_id."\n");
                        fclose($myfile);
                        echo $a."-village_id=>".$village_id."<br/>";
                        continue;
                    }
                } else {
                    $logFile = getcwd()."/storage/village_info.txt";
                    $myfile = fopen($logFile, "a");
                    fwrite($myfile, json_encode($address)."\n");
                    fwrite($myfile, $village_id."\n");
                    fclose($myfile);
                    echo $a."-village_id=>".$village_id."<br/>";
                    continue;
                }
            }
        }
    }

    public function removeduplicateassignedvillage() {
        $query = 'SELECT *, count(`assigned_to_dd_id`) as `total_dd_id`, count(`village_id`) as `total_village_id`,count(`created_at`) as `total_created_date` from assigned_village group by `assigned_to_dd_id`, `village_id`,`created_at` having `total_dd_id` >1 ORDER BY `total_dd_id` DESC';
        $villageData = DB::select(DB::raw($query));
        echo "<br/>Total Village Count =>".count($villageData);
        $x = 0;
        foreach ($villageData as $key => $value) {
            $dd_id      = $value->assigned_to_dd_id;
            $village_id = $value->village_id;
            $created_at = $value->created_at;
            echo "<br/>".$x."-dd_id =>".$dd_id."--village_id=>".$village_id."--created_at=>".$created_at;

            $village_query = "SELECT assigned_village_id FROM assigned_village WHERE assigned_to_dd_id='".$dd_id."' and village_id='".$village_id."' and created_at='".$created_at."'";
            $village_data = DB::select(DB::raw($village_query));
            echo "<br/>Total Village Count to Delete=>".count($village_data);
            $a=0;
            foreach ($village_data as $key => $village) {
                echo "<br/>assigned_village_id=>".$a."-".$assigned_village_id = $village->assigned_village_id;
                if($a!=0) {
                    DB::table("assigned_village")->where('assigned_village_id',$assigned_village_id)->delete();
                    echo "<br/>Deleted assigned_village_id =>".$a."-".$assigned_village_id;
                }
                $a++;
            }
            echo "<br/>Deleted<br/>";
            $x++;
        }
    }

    private function CalculateDistance($village_id,$lat2, $lon2) {
        $villageData = \DB::table('villages')->where(['village_id' => $village_id])->first();
        if ($villageData) {
            $lat1 = $villageData->latitude;
            $lon1 = $villageData->longitude;
            if($lat1 && $lon1) {
                $radius = ($villageData->radius) ? $villageData->radius : 0;
                $rad = M_PI / 180;
                $distance = acos(sin($lat2*$rad) * sin($lat1*$rad) + cos($lat2*$rad) * cos($lat1*$rad) * cos($lon2*$rad - $lon1*$rad)) * 6371;// Kilometers
                $distancemtr = round($distance*1000,2);
                if($distancemtr <= $radius) {
                    $validate = 1;
                } else {
                    $validate = 2;
                }
                $response = array($distancemtr,$validate,$radius);
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

    /* Generate unique 10 digit ticket Number */
    private function generateTicketNumber($user_id, $prefix, $ticket_no='') {
        if ($ticket_no == '') {
            $count_ticket   = Ticket::count();
            $count_ticket   = $count_ticket + $user_id + time();
            $random_part    = str_pad(rand(1, 9999999), 6, '0', STR_PAD_LEFT);
            $checksum       = $count_ticket % 10;
            $ticket_no      = $random_part . $checksum;
            $unique_ticket_no = $prefix . $ticket_no;
        } else {
            $unique_ticket_no = $ticket_no;
        }
        $ticket_count   = Ticket::where('ticket_no', $unique_ticket_no)->count();
        while ($ticket_count > 0) {
            $count_ticket   = Ticket::count();
            $count_ticket   = $count_ticket + $user_id + time();
            $random_part    = str_pad(rand(1, 9999999), 6, '0', STR_PAD_LEFT);
            $checksum       = $count_ticket % 10;
            $ticket_no      = $random_part . $checksum;
            $unique_ticket_no = $prefix . $ticket_no;
            $ticket_count   = Ticket::where('ticket_no', $unique_ticket_no)->count();
        }
        return $unique_ticket_no;
    }

    public function updateInstitutionalContact() {
        $institutional = TeaEstate::get();
        // echo json_encode($institutional); exit();
        foreach ($institutional as $key => $value) {
            $institutional_id   = $value->tea_estate_id;
            $state_id           = $value->state_id;
            $district_id        = $value->district_id;
            TeaEstateContact::where(['tea_estate_id' => $institutional_id])->update(['state_id' => $state_id, 'district_id' => $district_id, 'updated_at'=>date('Y-m-d H:i:s')]);
            echo "=>Updated institutional_id =>".$institutional_id."--state_id =>".$state_id."--district_id =>".$district_id."<br/>";
        }
    }

    /*public function updtemarkVisit() {
        $markvisit = MarkVisit::get();
        // echo json_encode($institutional); exit();
        foreach ($markvisit as $key => $value) {
            $mark_visit_id  = $value->mark_visit_id;
            $updated_date   = date('Y-m-d', strtotime($value->updated_at));
            DB::table('mark_visits')->where(['mark_visit_id' => $mark_visit_id])->update(['updated_date' => $updated_date]);
            echo "=>Updated mark_visit_id =>".$mark_visit_id."--updated_date =>".$updated_date."--updated_at =>".$value->updated_at."<br/>";
        }
    }*/

    public function updteFarmerdate() {
        // $max_update = $_GET['max_update'];
        // echo json_encode($institutional); exit();
        if(!empty($_GET['max_update']) && ($_GET['max_update']!=0)) {
            $a = $_GET['max_update'];
            $max_update = $_GET['max_update'];
        } else {
            $max_update = 0;
            $a = 0;
        }
        $farmer_data = Farmer::skip($a)->take(200000)->get();
        foreach ($farmer_data as $key => $value) {
            $farmer_id    = $value->farmer_id;
            $created_date = date('Y-m-d', strtotime($value->created_at));
            DB::table('farmers')->where(['farmer_id' => $farmer_id])->update(['created_date' => $created_date]);
            echo "=> max_update=>".$max_update." --a=>".$a." -- Updated farmer_id =>".$farmer_id."--created_date =>".$created_date."--created_at =>".$value->created_at."<br/>";
            $a++;
        }
    }

    public function updateCircularPath() {
        $circulars = DB::table('circular_new')->where('circular_path','')->get();
        // echo "=>".count($circulars);
        foreach ($circulars as $circular) {
            $circular_id = $circular->circular_id;
            $circular_name = $circular->circular_name;
            $circular_path = $circular_name.'-'.$circular_id;
            $enc_circular_path = base64_encode($circular_path);
            DB::table('circular_new')->where(['circular_id'=> $circular_id])->update([
                'circular_path' => $enc_circular_path
            ]);
        }
    }

    public function updateFarmerType() {
        $tickets = DB::table('tickets')->where('data_source_id',7)->get();
        echo "<br/>Total Ticket =>".count($tickets)."\n";
        $a=1;
        foreach ($tickets as $ticket) {
            $ticket_id = $ticket->ticket_id;
            echo "<br/>ticket_id=>".$ticket_id."\n";
            $ticket_no = $ticket->ticket_no;
            DB::table('farmers')->where(['ticket_id'=> $ticket_id])->update([
                'farmer_type' => 1
            ]);
            echo "<br/>".$a."-ticket_id=>".$ticket_id." updated "."\n";
            $a++;
        }
    }

    public function generateTicketNo() {
        // Old Script
        /*$count_ticket = Ticket::count();
        $count_ticket = $count_ticket + rand(100,999);
        $count_ticket = $count_ticket + rand(10,99);
        $count_ticket = $count_ticket + 150;
        $count_ticket = $count_ticket + rand(100,999);
        $count_ticket = $count_ticket + rand(10,99);
        $randomNumber = rand(1,10);
        $ticket_no    = ($count_ticket == 0 ? 1001 : 2100 + $count_ticket.$randomNumber);
        $ticket_no    = "SV2" . $ticket_no;*/


        /*echo "count_ticket=>".$count_ticket = Ticket::count();
        // $count_ticket = $count_ticket + 150;
        echo "<br/>count_ticket=>".$count_ticket = $count_ticket + rand(100,999);
        echo "<br/>ticket_no=>".$randomNumber = rand(1,10);
        echo "<br/>ticket_no=>".$ticket_no    = ($count_ticket == 0 ? 1001 : 2001 + $count_ticket.$randomNumber);
        echo "<br/>ticket_no=>".$ticket_no    = "PV2" . $ticket_no;*/

        /*echo "<br/>count_ticket=>".$count_ticket = Ticket::count();
        echo "<br/>count_ticket=>".$count_ticket = $count_ticket + rand(100,999);
        echo "<br/>count_ticket=>".$count_ticket = $count_ticket + rand(10,99);
        echo "<br/>count_ticket=>".$count_ticket = $count_ticket + 150;
        echo "<br/>count_ticket=>".$count_ticket = $count_ticket + rand(100,999);
        echo "<br/>count_ticket=>".$count_ticket = $count_ticket + rand(10,99);
        echo "<br/>count_ticket=>".$randomNumber = rand(1,10);
        echo "<br/>count_ticket=>".$ticket_no    = ($count_ticket == 0 ? 1001 : 2001 + $count_ticket.$randomNumber);
        echo "<br/>count_ticket=>".$ticket_no    = "PV2" . $ticket_no;*/

        $ticket_list    = array();
        $duplicate_list = array();
        for ($i=0; $i < 500 ; $i++) {
            $userId = $i;
            $count_ticket = Ticket::count();
            $count_ticket = $count_ticket + $userId;
            $count_ticket = $count_ticket + rand(100,999);
            $count_ticket = $count_ticket + rand(10,99);
            $count_ticket = $count_ticket + 150;
            $count_ticket = $count_ticket + rand(100,999);
            // $count_ticket = $count_ticket + rand(10,99);
            $randomNumber = rand(10,99);
            $ticket_no    = ($count_ticket == 0 ? 1001 : 2001 + $count_ticket.$randomNumber);
            // PV3${randomInt(2)}${randomInt(2)}${randomInt(2)}${randomInt(1)}"
            /*array_push($ticket_list, $ticket_no);
            if(in_array($ticket_no, $duplicate_list)) {
                array_push($duplicate_list, $ticket_no);
            }*/
        }
        /*echo "<pre>";
        print_r($duplicate_list);
        echo "</pre>";
        echo json_encode($ticket_list);*/
    }
}
