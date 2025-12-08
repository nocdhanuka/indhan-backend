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
use App\SelfParticipationPlan;
use App\Testimonial;
use App\CustomerQuery;
use App\CustomerQuerySolution;
use App\AppVersion;
use App\ActiveLog;
use App\Efficiency;
use App\VillageFarmer;
use App\VillageFarmerCrop;
use App\DemoRequest;
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


ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);

class WebApiController extends ApiController {

    protected $userTransformer;

    public function __construct(userTransformer $userTransformer) {
        $this->userTransformer = $userTransformer;
        $this->croncloseticket();
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
                return $this->respondWithError("Invalid E       mail or Password");
            }
        }
    }

    
    private function findUser($api_token) {
        $query = "
                    (SELECT zonal_manager_id AS id, 'zonal' as user_type  FROM zonal_managers WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT regional_manager_id AS id, 'regional' as user_type FROM regional_managers WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT area_manager_id AS id, 'area' as user_type FROM area_managers WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT sales_regional_manager_id AS id, 'sales_regional' as user_type FROM sales_regional_managers WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT sales_area_manager_id AS id, 'sales_area' as user_type FROM sales_area_managers WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT id AS id, 'bdo' as user_type FROM users WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
                    (SELECT dd_id AS id, 'dhanuka_doctor' as user_type FROM dhanuka_doctor WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    ";
        $user = DB::select(DB::raw($query));
        return $user;
    }

    private function findUserDetails($user_type, $api_token) {
        switch ($user_type) {
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
                $select_field = "name AS dd_name";
                break;
        }
        $query = " SELECT " . $select_field . " FROM " . $table . " WHERE status='1' AND api_token ='" . $api_token . "' LIMIT 0,1 ";
        $user = DB::select(DB::raw($query));
        return $user;
    }

    private function authenticate($username, $password, $user_table = '', $user_states = '') {
        $credentials = ['ECN' => $username, 'password' => $password];

        $userType = '';
        switch ($user_table) {
            case "zonal_managers" :
                \Config::set('jwt.user', 'App\ZonalManager');
                \Config::set('jwt.identifier', 'zonal_manager_id');
                \Config::set('auth.providers.users.model', \App\ZonalManager::class);
                $userType = 'ZONAL';
                break;
            case "regional_managers" :
                \Config::set('jwt.user', 'App\RegionalManager');
                \Config::set('jwt.identifier', 'regional_manager_id');
                \Config::set('auth.providers.users.model', \App\RegionalManager::class);
                $userType = 'REGIONAL';
                break;
            case "area_managers" :
                \Config::set('jwt.user', 'App\AreaManager');
                \Config::set('jwt.identifier', 'area_manager_id');
                \Config::set('auth.providers.users.model', \App\AreaManager::class);
                $userType = 'AREA';
                break;
            case "sales_regional_managers" :
                \Config::set('jwt.user', 'App\SalesRegionalManager');
                \Config::set('jwt.identifier', 'sales_regional_manager_id');
                \Config::set('auth.providers.users.model', \App\SalesRegionalManager::class);
                $userType = 'REGIONAL_SALES';
                break;
            case "sales_area_managers" :
                \Config::set('jwt.user', 'App\SalesAreaManager');
                \Config::set('jwt.identifier', 'sales_area_manager_id');
                \Config::set('auth.providers.users.model', \App\SalesAreaManager::class);
                $userType = 'AREA_SALES';
                break;
            case "users" :
                \Config::set('jwt.user', 'App\User');
                \Config::set('jwt.identifier', 'id');
                \Config::set('auth.providers.users.model', \App\User::class);
                $userType = 'BDO';
                break;
            case "dhanuka_doctor" :
                \Config::set('jwt.user', 'App\DhanukaDoctor');
                \Config::set('jwt.identifier', 'dd_id');
                \Config::set('auth.providers.users.model', \App\DhanukaDoctor::class);
                $userType = 'dhanuka_doctor';
                break;
        }

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->respondWithConflict("Token Expired");
        }

        $user = JWTAuth::toUser($token);
        $user->api_token = $token;
        $user->save();
        return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'Login successful!',
                    'data' => $this->userTransformer->transform($user, $userType, $user_states)
        ]);
    }

    public function croncloseticket() {
        $current_day = date('Y-m-d');
        //$result = Ticket::where('created_at', '<', $current_day)->where('status', '1')->update(['status' => '0','closing_datetime'=>Carbon::now()]);
        $result = Ticket::where('created_at', '<', $current_day)->where('ticket_status', '1')->update(['status' => '2','ticket_status' => '2','closing_datetime'=>Carbon::now()]);
        $result1 = PlanAtp::where('plan_date', '<', $current_day)->where('status', '1')->update(['status' => '2','updated_at'=>date('Y-m-d H:i:s')]);
    }

    public function FarmerProducts(Request $request) {
        $rules = array(
            'apiToken' => 'required|string',
            'farmer_id' => 'required'
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
                $farmer_id = $request->input('farmer_id');
                
                $products = DB::table('village_farmer_crop')
                            ->join('products','products.product_id','=','village_farmer_crop.product_id','left')
                            ->where('village_farmer_crop.farmer_id',$farmer_id)
                            ->select('village_farmer_crop.product_id','products.product')
                            ->get();
                if ($products) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Products list.',
                                'data' =>$products
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'error',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No Product found',
                                'data' =>[]
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function DdAtpList(Request $request)
    {   
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
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $atp =  DB::table('plan_atp')
                        ->join('tehsils','tehsils.tehsil_id','=','plan_atp.tehsil_id')
                        ->join('districts','districts.district_id','=','plan_atp.district_id')
                        ->join('states','states.state_id','=','plan_atp.state_id')
                        ->join('products','products.product_id','=','plan_atp.product_id')
                        ->join('crops','crops.crop_id','=','plan_atp.crop_id')
                        ->join('tickets','tickets.plan_atp_id','=','plan_atp.plan_atp_id')
                        ->where('plan_atp.dd_id',$dd_id)
                        ->where('plan_atp.status','!=','2')
                        ->select('plan_atp.*','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','tickets.ticket_id','tickets.ticket_no')
                        ->get();
                if ($atp) {
                    foreach ($atp as $key => $value) {

                        $base_url = url('/');
                        if (!empty($atp[$key]->image)) {
                            $atp[$key]->image = $base_url . '/assets/atpimage/' . $atp[$key]->image;
                        }

                        $products = DB::table('products')
                                    ->where('product_id',$atp[$key]->product_id)
                                    ->select('product_id','product')
                                    ->get();

                        $crops =    DB::table('crops')
                                    ->where('crop_id',$atp[$key]->crop_id)
                                    ->select('crop_id','crop')
                                    ->get();
                        $atp[$key]->product = $products;
                        $atp[$key]->crop = $crops;

                        $totalFarmer = DB::table('registered_farmer')
                                        ->where('plan_atp_id', $atp[$key]->plan_atp_id)
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

    public function ChooseDemoNo(Request $request)
    {
        $rules = array(
            'apiToken' => 'required|string',
            'plan_atp_id' => 'required',
            'demo_request_id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $plan_atp_id = $request->input('plan_atp_id');
                $demo_request_id = $request->input('demo_request_id');
                $atpdemo = PlanAtp::where(['plan_atp_id' => $plan_atp_id])->update(['demo_request_id' => $demo_request_id]);
                if ($atpdemo) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Demo no. added successfully.'
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'error',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Please try again'
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function InviteFarmer(request $request){
        $rules = array(
            'apiToken' => 'required|string',
            'farmer_name' => 'required',
            'contact_number' => 'required',
            'plan_atp_id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $plan_atp_id = $request->input('plan_atp_id');
                $farmer_name = $request->input('farmer_name');
                $contact_number = $request->input('contact_number');
                $creation_time = date('Y-m-d H:i:s');

                $atpdemo = DB::table('invite_farmer')->insert(
                                ['farmer_name' => $farmer_name, 'contact_number' => $contact_number, 'creation_time' => $creation_time, 'plan_atp_id' => $plan_atp_id]
                            );
                if ($atpdemo) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Farmer invited successfully.'
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'error',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Please try again'
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function RegisterFarmer(request $request){
        $rules = array(
            'apiToken' => 'required|string',
            'contact_number' => 'required',
            'plan_atp_id' => 'required',
            'ticket_id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $plan_atp_id = $request->input('plan_atp_id');
                $ticket_id = $request->input('ticket_id');
                $contact_number = $request->input('contact_number');
                $datetime = date('Y-m-d H:i:s');

                $registerfarmer = DB::table('registered_farmer')->insert(
                                ['user_id' => $dd_id,'user_type' => $user_type,'mobile' => $contact_number, 'datetime' => $datetime, 'plan_atp_id' => $plan_atp_id, 'ticket_id' => $ticket_id]
                            );
                if ($registerfarmer) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Farmer registered successfully.'
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'error',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Please try again'
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function CloseAtp(Request $request){
        $rules = array(
            'apiToken' => 'required|string',
            'image' => 'required',
            'status' => 'required',
            'plan_atp_id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $plan_atp_id = $request->input('plan_atp_id');
                $status = $request->input('status');
                if($request->file('image')){
                    $file=$request->file('image');
                    $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                    
                    $file->move("assets/atpimage",$fileName);
                    $atpimage = PlanAtp::where(['plan_atp_id' => $plan_atp_id])->update(['image' => $fileName,'status' => $status]);
                    if ($atpimage) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Image Uploaded successfully.'
                        ]);
                    }
                    else
                    {
                        return $this->respond([
                                    'status' => 'error',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Please try again'
                        ]);
                    }
                }
                else
                {
                    return $this->respond([
                                'status' => 'error',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Please choose image'
                    ]);
                }

               
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    // public function CloseAtp(Request $request)
    // {
    //     $rules = array(
    //         'apiToken' => 'required|string',
    //         'plan_atp_id' => 'required',
    //         'status' => 'required',
    //         'reason' => 'required'
    //     );

    //     $validator = Validator::make($request->all(), $rules);
    //     if ($validator->fails()) {
    //         return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
    //     } else {
    //         $api_token = $request->input('apiToken');
    //         $user = $this->findUser($api_token);
    //         if ($user && count($user) == '1') {
    //             $dd_id = $user[0]->id;
    //             $user_type = $user[0]->user_type;
    //             $plan_atp_id = $request->input('plan_atp_id');
    //             $reason = $request->input('reason');
    //             $status = $request->input('status');
    //             $atpstatus = PlanAtp::where(['plan_atp_id' => $plan_atp_id])->update(['status' => $status,'reason' => $reason]);
    //             if ($atpstatus) {
    //                 return $this->respond([
    //                             'status' => 'success',
    //                             'status_code' => $this->getStatusCode(),
    //                             'message' => 'ATP closed successfully.'
    //                 ]);
    //             }
    //             else
    //             {
    //                 return $this->respond([
    //                             'status' => 'error',
    //                             'status_code' => $this->getStatusCode(),
    //                             'message' => 'Please try again'
    //                 ]);
    //             }
    //         } else {
    //             return $this->respondNotFound("Invalid Attempt !!");
    //         }
    //     }
    // }

    public function ActivateAtp(Request $request)
    {
        $rules = array(
            'apiToken' => 'required|string',
            'plan_atp_id' => 'required',
            'demo_request_id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $plan_atp_id = $request->input('plan_atp_id');
                $demo_request_id = $request->input('demo_request_id');
                $status = '1';

                $checkatp = "select `plan_atp`.* from `plan_atp` where `reason` = '' and `dd_id` = '".$dd_id."' and (status = '1' OR status = '2')";
                $checkatpstatus = DB::select(DB::raw($checkatp));
                $checkatpsts = array_map(function ($value) {
                    return (array) $value;
                }, $checkatpstatus);
                if (isset($checkatpsts) && !empty($checkatpsts)) {
                   return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Please ask your FL to give reason for non activated ATP’s.'
                        ]);
                }
                else
                {
                    $atpstatus = PlanAtp::where(['plan_atp_id' => $plan_atp_id])->update(['status' => $status, 'demo_request_id' => $demo_request_id]);
                    if ($atpstatus) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'ATP activated successfully.'
                        ]);
                    }
                    else
                    {
                        return $this->respond([
                                    'status' => 'error',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Please try again'
                        ]);
                    }
                }
                
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function getValidateFarmerList(Request $request) {
            $rules = array(
                'apiToken' => 'required|string',
                'plan_atp_id' => 'required',
                'type' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
              
                $user = $this->findUser($api_token);

                $plan_atp_id= $request->input('plan_atp_id');
                $type= $request->input('type');
               
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    if ($type == 'total_farmer') {
                        $totalFarmer = [];
                        $Vfarmer = "select `registered_farmer`.`registered_farmer_id` as farmer_id, `registered_farmer`.`ticket_id`, `registered_farmer`.`user_id`,`registered_farmer`.`user_type`, `registered_farmer`.`mobile`, `registered_farmer`.`datetime`, `registered_farmer`.`registration_type`, `registered_farmer`.`ticket_status`, `registered_farmer`.`validation_status`, `registered_farmer`.`status`, `registered_farmer`.`created_at`, `registered_farmer`.`updated_at`, `states`.`state`, `tehsils`.`tehsil`, `districts`.`district`, `tehsils`.`tehsil_id` as `tehsilId`, `districts`.`district_id` as `districtId`, `districts`.`state_id` as `stateId`, `tickets`.`village`, `tickets`.`product_1_id` as `product_id`,`tickets`.`crop_1_id` as `crop_id` from `registered_farmer` left join `tickets` on `tickets`.`ticket_id` = `registered_farmer`.`ticket_id` left join `tehsils` on `tehsils`.`tehsil_id` = `tickets`.`tehsil_id` left join `districts` on `districts`.`district_id` = `tickets`.`district_id` left join `states` on `states`.`state_id` = `tickets`.`state_id` where `registered_farmer`.`ticket_id` = '".$plan_atp_id."' and `registered_farmer`.`user_id` = '".$user_id."' and `registered_farmer`.`user_type` = 'dhanuka_doctor'";
                        $VFarmers = DB::select(DB::raw($Vfarmer));
                        $totalVFarmer = array_map(function ($value) {
                            return (array) $value;
                        }, $VFarmers);
                        //print_r($totalVFarmer);die;

                        $Ffarmer = "select `farmers`.*, `states`.`state`, `tehsils`.`tehsil`, `districts`.`district`, `tehsils`.`tehsil_id` as `tehsilId`, `districts`.`district_id` as `districtId`, `districts`.`state_id` as `stateId`, `tickets`.`village`, `tickets`.`product_1_id` as `product_id`,`tickets`.`crop_1_id` as `crop_id` from `farmers` left join `tickets` on `tickets`.`ticket_id` = `farmers`.`ticket_id` left join `tehsils` on `tehsils`.`tehsil_id` = `tickets`.`tehsil_id` left join `districts` on `districts`.`district_id` = `tickets`.`district_id` left join `states` on `states`.`state_id` = `tickets`.`state_id` where `farmers`.`ticket_id` = '".$plan_atp_id."' and `farmers`.`user_id` = '".$user_id."' and `farmers`.`user_type` = 'dhanuka_doctor'";
                        $FFarmers = DB::select(DB::raw($Ffarmer));
                        $totalFFarmer = array_map(function ($value) {
                            return (array) $value;
                        }, $FFarmers);

                        if (isset($totalVFarmer) && isset($totalFFarmer) && !empty($totalVFarmer) && !empty($totalFFarmer)) {
                            $totalFarmer = array_merge($totalVFarmer, $totalFFarmer);
                        }
                        elseif (isset($totalVFarmer) && !empty($totalVFarmer)) {
                            $totalFarmer = $totalVFarmer;
                        }
                        elseif (isset($totalFFarmer) && !empty($totalFFarmer)) {
                            $totalFarmer = $totalFFarmer;
                        }
                        //echo "<pre>";print_r($totalFarmer);die;
                        foreach ($totalFarmer as $key => $value) {
                            $totalFarmer[$key]['farmer_name'] = "NA";
                            $leaddata =  DB::table('leads')
                                        ->where('leads.farmer_id',$totalFarmer[$key]['farmer_id'])
                                        ->where('leads.farmer_type','registered_farmer')
                                        ->select('leads.lead_status')
                                        ->get();
                            if ($leaddata) {
                                $totalFarmer[$key]['lead_status'] = $leaddata[0]['lead_status'];
                            }
                            else
                            {
                                $totalFarmer[$key]['lead_status'] = "0";
                            }

                            $products = DB::table('products')
                            ->where('products.product_id',$totalFarmer[$key]['product_id'])
                            ->select('products.product_id','products.product')
                            ->get();

                            if ($products) {
                                $totalFarmer[$key]['products'] = $products;
                            }
                            else
                            {
                                $totalFarmer[$key]['products'] = [];
                            }

                            $crops = DB::table('crops')
                            ->where('crops.crop_id',$totalFarmer[$key]['crop_id'])
                            ->select('crops.crop_id','crops.crop')
                            ->get();

                            if ($crops) {
                                $totalFarmer[$key]['crops'] = $crops;
                            }
                            else
                            {
                                $totalFarmer[$key]['crops'] = [];
                            }
                        }
                    }
                    else
                    {
                        $Vfarmer = "select `registered_farmer`.`registered_farmer_id` as farmer_id, `registered_farmer`.`ticket_id`, `registered_farmer`.`user_id`,`registered_farmer`.`user_type`, `registered_farmer`.`mobile`, `registered_farmer`.`datetime`, `registered_farmer`.`registration_type`, `registered_farmer`.`ticket_status`, `registered_farmer`.`validation_status`, `registered_farmer`.`status`, `registered_farmer`.`created_at`, `registered_farmer`.`updated_at`, `states`.`state`, `tehsils`.`tehsil`, `districts`.`district`, `tehsils`.`tehsil_id` as `tehsilId`, `districts`.`district_id` as `districtId`, `districts`.`state_id` as `stateId`, `tickets`.`village`, `tickets`.`product_1_id` as `product_id`,`tickets`.`crop_1_id` as `crop_id` from `registered_farmer` left join `tickets` on `tickets`.`ticket_id` = `registered_farmer`.`ticket_id` left join `tehsils` on `tehsils`.`tehsil_id` = `tickets`.`tehsil_id` left join `districts` on `districts`.`district_id` = `tickets`.`district_id` left join `states` on `states`.`state_id` = `tickets`.`state_id` where `registered_farmer`.`ticket_id` = '".$plan_atp_id."' and `registered_farmer`.`user_id` = '".$user_id."' and `registered_farmer`.`validation_status` = '1' and `registered_farmer`.`user_type` = 'dhanuka_doctor'";
                        $VFarmers = DB::select(DB::raw($Vfarmer));
                        $totalVFarmer = array_map(function ($value) {
                            return (array) $value;
                        }, $VFarmers);

                        $Ffarmer = "select `farmers`.*, `states`.`state`, `tehsils`.`tehsil`, `districts`.`district`, `tehsils`.`tehsil_id` as `tehsilId`, `districts`.`district_id` as `districtId`, `districts`.`state_id` as `stateId`, `tickets`.`village`, `tickets`.`product_1_id` as `product_id`,`tickets`.`crop_1_id` as `crop_id` from `farmers` left join `tickets` on `tickets`.`ticket_id` = `farmers`.`ticket_id` left join `tehsils` on `tehsils`.`tehsil_id` = `tickets`.`tehsil_id` left join `districts` on `districts`.`district_id` = `tickets`.`district_id` left join `states` on `states`.`state_id` = `tickets`.`state_id` where `farmers`.`ticket_id` = '".$plan_atp_id."' and `farmers`.`user_id` = '".$user_id."' and `farmers`.`validation_status` = 'Validated' and `farmers`.`user_type` = 'dhanuka_doctor'";
                        $FFarmers = DB::select(DB::raw($Ffarmer));
                        $totalFFarmer = array_map(function ($value) {
                            return (array) $value;
                        }, $FFarmers);
                        
                        if (isset($totalVFarmer) && isset($totalFFarmer) && !empty($totalVFarmer) && !empty($totalFFarmer)) {
                            $totalFarmer = array_merge($totalVFarmer, $totalFFarmer);
                        }
                        elseif (isset($totalVFarmer) && !empty($totalVFarmer)) {
                            $totalFarmer = $totalVFarmer;
                        }
                        elseif (isset($totalFFarmer) && !empty($totalFFarmer)) {
                            $totalFarmer = $totalFFarmer;
                        }
                        foreach ($totalFarmer as $key => $value) {
                            $totalFarmer[$key]['farmer_name'] = "NA";
                            $leaddata =  DB::table('leads')
                                        ->where('leads.farmer_id',$totalFarmer[$key]['farmer_id'])
                                        ->where('leads.farmer_type','registered_farmer')
                                        ->select('leads.lead_status')
                                        ->get();
                            if ($leaddata) {
                                $totalFarmer[$key]['lead_status'] = $leaddata[0]['lead_status'];
                            }
                            else
                            {
                                $totalFarmer[$key]['lead_status'] = "0";
                            }

                            $products = DB::table('products')
                            ->where('products.product_id',$totalFarmer[$key]['product_id'])
                            ->select('products.product_id','products.product')
                            ->get();

                            if ($products) {
                                $totalFarmer[$key]['products'] = $products;
                            }
                            else
                            {
                                $totalFarmer[$key]['products'] = [];
                            }

                            $crops = DB::table('crops')
                            ->where('crops.crop_id',$totalFarmer[$key]['crop_id'])
                            ->select('crops.crop_id','crops.crop')
                            ->get();

                            if ($crops) {
                                $totalFarmer[$key]['crops'] = $crops;
                            }
                            else
                            {
                                $totalFarmer[$key]['crops'] = [];
                            }
                        }
                    }
                    
                    if ($totalFarmer) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Farmer list.',
                                    'data' =>$totalFarmer
                        ]);
                    }
                    else
                    {
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

    public function invitedFarmerList(Request $request)
    {
        $rules = array(
            'apiToken' => 'required|string',
            'plan_atp_id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $plan_atp_id = $request->input('plan_atp_id');
                $validatedFarmer =  DB::table('invite_farmer')
                                    ->where('plan_atp_id',$plan_atp_id)
                                    ->select('*')
                                    ->get();
                if ($validatedFarmer) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Validated farmer list.',
                                'data' => $validatedFarmer
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No validated farmer found',
                                'data' => []
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function ddStateList(Request $request)
    {
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
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $ddstate =  DB::table('dhanuka_doctor')
                                    ->join('states','states.state_id','=','dhanuka_doctor.state_id','left')
                                    ->where('dd_id',$dd_id)
                                    ->select('states.state_id as sId','states.state as Item')
                                    ->get();
                if ($ddstate) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'State List Found!',
                                'data' => $ddstate
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No state found',
                                'data' => []
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function allVillageList(Request $request) {

        $data = $request->json()->all();
        $base_url = url('/');
        if ($data) {
            $rules = array(
                'apiToken' => 'required|string',
                'tehsil_id' => 'required'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $user = $this->findUser($api_token);
                $tehsil_id= $request->input('tehsil_id');
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;

                    $user_type = $user[0]->user_type;
                    $ddData = DB::table('villages')
                                ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                                ->join('districts','districts.district_id','=','villages.district_id')
                                ->join('states','states.state_id','=','districts.state_id')
                                ->select('villages.village_id as villageId','villages.village as villageName','states.state','tehsils.tehsil','districts.district')
                                ->where('villages.status', '1')
                                ->where('villages.tehsil_id', $tehsil_id)
                                ->get();
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
                    
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'All Village List',
                                'data' => $ddData
                    ]);
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
        } else {
            return $this->respondWithError("Invalid Request !!");
        }
    }

    // public function Earningvallist(Request $request){
    //     $value = Input::all();
    //     $query      = " 
    //                 SELECT                                 
    //                 @a:=@a+1  as serial_number,
    //                 A.*
    //             FROM
    //                 (
    //                 SELECT
    //                     dev.dd_field_earning_value,
    //                     dev.dd_village_earning_value,
    //                     dev.creation_time
                        
    //                 FROM        dd_earning_value dev
    //                 WHERE dev.regional_manager_id = '".$value['regional_manager_id']."'
    //                 ORDER BY
    //                     dev.creation_time DESC
    //                 ) A,
    //                 (select @a:=0) initvars";//echo $query;exit();
    //     $Earningvalue  = collect(DB::select($query));echo "<pre>";print_r($Earningvalue);die;
    //     return Datatables::of($Earningvalue)->make();
    // }
}
