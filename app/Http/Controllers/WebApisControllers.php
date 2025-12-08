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
use App\DemoFeedback;
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
use App\Myatp;
use App\MarketingSupervisorProductCategory;
use App\MarketingSupervisor;
use App\MarketingSupervisorRSM;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);

class WebApisControllers extends ApiController {

    protected $userTransformer;

    public function __construct(userTransformer $userTransformer) {
        $this->userTransformer = $userTransformer;
        //$this->croncloseticket();
        
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
                    (SELECT marketing_supervisor_id AS id, 'marketing_supervisor' as user_type  FROM marketing_supervisor WHERE status='1' AND ECN!='' AND password!='' AND api_token ='" . $api_token . "' LIMIT 0,1)
                    UNION
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
                $select_field = "name AS manager_name";
                break;
        }
        $query = " SELECT " . $select_field . " FROM " . $table . " WHERE status='1' AND api_token ='" . $api_token . "' LIMIT 0,1 ";
        $user = DB::select(DB::raw($query));
        return $user;
    }

    private function findUserOtherDetail($user_type, $api_token) {
        switch ($user_type) {
            case "marketing_supervisor" : $table = "marketing_supervisor";
                $select_field = "marketing_supervisor_name AS manager_name, marketing_supervisor_mobile AS mobile, ECN";
                break;
            case "zonal" : $table = "zonal_managers";
                $select_field = "zonal_manager_name AS manager_name, zonal_manager_mobile AS mobile, ECN";
                break;
            case "regional" : $table = "regional_managers";
                $select_field = "regional_manager_name AS manager_name, regional_manager_mobile AS mobile, ECN";
                break;
            case "area" : $table = "area_managers";
                $select_field = "area_manager_name AS manager_name, area_manager_mobile AS mobile, ECN";
                break;
            case "sales_regional" : $table = "sales_regional_managers";
                $select_field = "sales_regional_manager_name AS manager_name, sales_regional_manager_mobile AS mobile, ECN";
                break;
            case "sales_area" : $table = "sales_area_managers";
                $select_field = "sales_area_manager_name AS manager_name, sales_area_manager_mobile AS mobile, ECN";
                break;
            case "bdo" : $table = "users";
                $select_field = "name AS manager_name, bdo_mobile_no AS mobile, ECN";
                break;
            case "dhanuka_doctor" : $table = "dhanuka_doctor";
                $select_field = "name AS manager_name, dd_mobile_no AS mobile, ECN";
                break;
        case "general" : $table = "general";
                $select_field = "general_user_name AS manager_name, general_user_mobile AS mobile, ECN";
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

        if ($userType = 'dhanuka_doctor') {
            $userdata = DB::table('dhanuka_doctor')
                        ->where('dhanuka_doctor.dash',$password)
                        ->select('dhanuka_doctor.*')
                        ->count();
            if ($userdata > 0) { 
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
            else
            {
                return $this->respondWithConflict("Please enter correct password.");
            }
        }elseif ($userType = 'BDO') {
            $userdata = DB::table('users')
                        ->where('users.dash',$password)
                        ->where('status','1')
                        ->select('users.*')
                        ->count();
            if ($userdata > 0) {
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
            else
            {
                return $this->respondWithConflict("Please enter correct password.");
            }
        }
        else
        {
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
    }

    public function croncloseticket() {
        $current_day = date('Y-m-d');
        $result = Ticket::where('created_at', '<', $current_day)->where('ticket_status', '1')->where('status', '1')->update(['status' => '2','ticket_status' => '2','closing_datetime'=>Carbon::now()]);
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
                                $atp->whereIn('plan_atp.plan_atp_id',function($query) use ($bdoresult,$request,$bdotype)
                                {   
                                    $query->select('plan_atp_id')->from('plan_atp')->whereIn('created_by_user_id',$bdoresult)->where('user_type','bdo');
                                    
                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query->whereBetween(DB::raw('DATE(plan_date)'),[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                    }

                                    if ($request->has('crop')) {
                                        $crop=$request->input('crop');
                                        $query->Where('crop_id',$crop);
                                        //$query->orWhere('other_crop_1',$crop);
                                        //$query->orWhere('other_crop_2',$crop);
                                    }

                                    if ($request->has('product')) {
                                        $product=$request->input('product');
                                        $query->Where('product_id',$product);
                                        // $query->orWhere('product_id_2',$product);
                                        //$query->orWhere('product_id_3',$product);
                                    }


                                    //if ($bdotype[0]->designation == 'COUNTERPART-FL') { 
                                        //$query->Where('plan_atp.forwhom','=','2');
                                    //}
                                });
                            }
                            
                            if (count($ddresult) == 0 && count($bdoresult) == 0) {
                                $atp->where(['plan_atp.created_by_user_id' => $user_id, 'plan_atp.user_type' => $user_type]);

                            }
                            else
                            {   
                                $atp->orWhere(['plan_atp.created_by_user_id' => $user_id, 'plan_atp.user_type' => $user_type]);


                                // if ($bdotype[0]->designation == 'COUNTERPART-FL') { 
                                //     //$atp->Where('plan_atp.forwhom','=','2');
                                // }
                            }
                        }
                        
                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            $atp->whereBetween(DB::raw('DATE(plan_atp.plan_date)'),[$from_date." 00:00:00", $to_date." 23:59:59"]);
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
                            //$atp->Where('plan_atp.forwhom','=','2');
                        }

                        $atp = $atp->select('plan_atp.*','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','tickets.ticket_id','tickets.ticket_no','tickets.latitude','tickets.longitude','languages.language as language_id','data_sources.data_source as data_source_id','dhanuka_doctor.name as dd_name')
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
                            }

                            if ($request->has('crop')) {
                                $crop=$request->input('crop');
                                $atp->Where('crop_id',$crop);
                            }

                            if ($request->has('product')) {
                                $product=$request->input('product');
                                $atp->Where('product_id',$product);
                            }

                    $atp = $atp->select('plan_atp.*','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','tickets.ticket_id','tickets.ticket_no','tickets.latitude','tickets.longitude','languages.language as language_id','data_sources.data_source as data_source_id')
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
                    //echo "<pre>";print_r($bdoresult);die;
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
                        }

                        if ($request->has('crop')) {
                            $crop=$request->input('crop');
                            $plan_atp_id->Where('crop_id',$crop);
                        }

                        if ($request->has('product')) {
                            $product=$request->input('product');
                            $plan_atp_id->Where('product_id',$product);
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
                            //echo "<pre>";print_r($planResult);die;
                        }
                    }
                    //echo "<pre>";print_r($planResult);die;
                    //Supervisor atp list
                    $atp =  DB::table('plan_atp')
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
                            $atp = $atp->select('plan_atp.*','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','tickets.ticket_id','tickets.ticket_no','tickets.latitude','tickets.longitude','languages.language as language_id','data_sources.data_source as data_source_id','dhanuka_doctor.name as dd_name','users.name as bdo_name','users.designation as bdo_designation')->orderBy('plan_atp.plan_date', 'DESC')
                            //->orderBy('plan_atp.plan_date', 'DESC')
                            //->orderByRaw("plan_atp.plan_date DESC")
                            ->offset($newoffset)
                            ->limit(30)
                            ->get();
                            //echo "<pre>";print_r($atp);die;
                            //echo $atp;die;
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
            'farmerData' => 'required',
            'plan_atp_id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $curTime = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "v2/invitedFarmer",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $plan_atp_id = $request->input('plan_atp_id');
                $farmerData = json_decode($request->input('farmerData'),true);
               
                // $farmer_name = $request->input('farmer_name');
                // $contact_number = $request->input('contact_number');
                $creation_time = date('Y-m-d H:i:s');
                foreach ($farmerData as $key => $value) {
                   $atpdemo = DB::table('invite_farmer')->insert(
                                ['farmer_name' => $value['farmer_name'], 'contact_number' => $value['contact_number'], 'creation_time' => $creation_time, 'plan_atp_id' => $plan_atp_id]
                            );
                }
                
                if ($atpdemo) {
                    $countfarmer = DB::table('invite_farmer')
                                   ->where('plan_atp_id', $plan_atp_id)
                                   ->count();
                    if ($countfarmer > 0) 
                    {   
                       $totalinvitedfarmer = $countfarmer; 
                    }
                    else
                    {
                       $totalinvitedfarmer = count($farmerData); 
                    }
                    
                    $updateticket = Ticket::where('plan_atp_id', $plan_atp_id)->update(['invited_farmer_count' => $totalinvitedfarmer]);

                    $log_firstTime  = strtotime($log_start_time);
                    $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                    $log_time_diff  = $log_lastTime-$log_firstTime;
                    $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                    DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $dd_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);
                    
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
                    //twilio
                    //fetching ticket
                    // $getTickets = DB::table('tickets')->where('ticket_id',$ticket_id)->get();
                    // if (count($getTickets)>0) {
                    //     //get admin messages
                    //     $getmessages = DB::table('message_history');
                    //                    if (!empty($getTickets[0]->crop_1_id)) {
                    //                        $getmessages->where('crop',$getTickets[0]->crop_1_id); 
                    //                    }

                    //                    if (!empty($getTickets[0]->crop_2_id)) {
                    //                        $getmessages->orWhere('crop',$getTickets[0]->crop_2_id); 
                    //                    }

                    //                    if (!empty($getTickets[0]->crop_3_id)) {
                    //                        $getmessages->orWhere('crop',$getTickets[0]->crop_3_id); 
                    //                    }

                    //                    if (!empty($getTickets[0]->product_1_id)) {
                    //                        $getmessages->where('product',$getTickets[0]->product_1_id); 
                    //                    }

                    //                    if (!empty($getTickets[0]->product_2_id)) {
                    //                       $getmessages->orWhere('product',$getTickets[0]->product_2_id); 
                    //                    }

                    //                    if (!empty($getTickets[0]->product_3_id)) {
                    //                        $getmessages->orWhere('product',$getTickets[0]->product_3_id); 
                    //                    }
                    //     $getmessages = $getmessages->where('state',$getTickets[0]->state_id)
                    //                    ->select('message_history.*')
                    //                    ->get();
                        
                    //     if (count($getmessages) > 0) {
                    //         foreach( $getmessages as $key => $value)
                    //         {   
                    //             $contactnumber = '+91'.$contact_number;
                    //             Twilio::message($contactnumber, $getmessages[$key]->message);
                    //         }
                    //     }
                    //}
                    
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
                if ($user_type == 'dhanuka_doctor') 
                {
                    $checkPlanAtpDD = PlanAtp::where('plan_atp_id',$plan_atp_id)
                                      ->where('dd_id',$dd_id)
                                      ->get();
                }
                else
                {
                    $checkPlanAtpDD = PlanAtp::where('plan_atp_id',$plan_atp_id)
                                      ->where('created_by_user_id',$dd_id)
                                      ->where('user_type',$user_type)
                                      ->where('dd_id',0)
                                      ->get();
                }

                if (count($checkPlanAtpDD) > 0) 
                {
                    if($request->file('image')){
                        $file=$request->file('image');
                        // $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                        // $file->move("assets/atpimage",$fileName);
                        //s3Uploads
                        //$fileName = $file->getClientOriginalName();
                        $fileName = time().rand(111,999).".".$file->getClientOriginalExtension();
                        $sTempFileName = 'assets/atpimage/' . $fileName;
                        Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                        //
                        $atpimage = PlanAtp::where(['plan_atp_id' => $plan_atp_id])->update(['image' => $fileName,'status' => $status]);
                        if ($atpimage) {
                            
                            $ticket1status   = DB::update("UPDATE  tickets SET `ticket_status` = '2', `status` = '2' WHERE `plan_atp_id` ='".$plan_atp_id."'");

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
                }
                else
                {   
                    return $this->respondWithError("You are not authorised to close ATP.");
                }
                
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    
    public function ActivateAtp(Request $request)
    {
        $rules = array(
            'apiToken' => 'required|string',
            'plan_atp_id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $curTime = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "v2/ActivateAtp",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $plan_atp_id = $request->input('plan_atp_id');

                if ($user_type == 'dhanuka_doctor') 
                {
                    $checkPlanAtpDD = PlanAtp::where('plan_atp_id',$plan_atp_id)
                                      ->where('dd_id',$dd_id)
                                      ->get();
                }
                else
                {
                    $user = User::Where('id',$dd_id)->get();
                    $designation = $user[0]->designation;
                    if($designation == 'COUNTERPART-FL') {
                        $cpUser = User::Where('counterpart_fl',$dd_id)->Where('status',1)->get();
                        if(count($cpUser)) {
                            $bdo_id = $cpUser[0]->id;
                            $checkPlanAtpDD = PlanAtp::where('plan_atp_id',$plan_atp_id)
                                              ->where('created_by_user_id',$bdo_id)
                                              ->where('user_type',$user_type)
                                              ->where('forwhom',2)
                                              ->get();
                        } else {
                            $checkPlanAtpDD = PlanAtp::where('plan_atp_id',$plan_atp_id)
                                              ->where('created_by_user_id',$dd_id)
                                              ->where('user_type',$user_type)
                                              ->where('dd_id',0)
                                              ->get();
                        }
                    } else {
                        $checkPlanAtpDD = PlanAtp::where('plan_atp_id',$plan_atp_id)
                                          ->where('created_by_user_id',$dd_id)
                                          ->where('user_type',$user_type)
                                          ->where('dd_id',0)
                                          ->get();
                    }
                }

                if (count($checkPlanAtpDD) > 0) {
                    $demo_request_id = (!empty($request->input('demo_request_id')))?$request->input('demo_request_id'):"";
                    $user_farmer = (!empty($request->input('user_farmer')))?$request->input('user_farmer'):"";
                    $status = '1';

                    if ($user_type == 'dhanuka_doctor') {
                        $checkatp = "select `plan_atp`.* from `plan_atp` where `reason` = '' and `dd_id` = '".$dd_id."' and status = '0' and  `plan_date` < '".date('Y-m-d 23:59:00')."'";
                        //(status = '1' OR status = '2')
                        $checkatpstatus = DB::select(DB::raw($checkatp));
                        $checkatpsts = array_map(function ($value) {
                            return (array) $value;
                        }, $checkatpstatus);
                    }
                    else
                    {
                        $user = User::Where('id',$dd_id)->get();
                        $designation = $user[0]->designation;
                        $crr_date = date('Y-m-d 23:59:00', strtotime("+1 day"));
                        if($designation == 'COUNTERPART-FL') {
                            $cpUser = User::Where('counterpart_fl',$dd_id)->Where('status',1)->get();
                            $bdo_id = $cpUser[0]->id;
                            $checkatp = "select `plan_atp`.* from `plan_atp` where `reason` = '' and `forwhom` = '2' and created_by_user_id = '".$bdo_id."' and `user_type` = '".$user_type."' and status = '0' and   `plan_date` < '".$crr_date."'";
                        } else {
                            $checkatp = "select `plan_atp`.* from `plan_atp` where `reason` = '' and `dd_id` = '0' and created_by_user_id = '".$dd_id."' and `user_type` = '".$user_type."' and status = '0' and   `plan_date` < '".$crr_date."'";
                        }
                        $checkatpstatus = DB::select(DB::raw($checkatp));
                        $checkatpsts = array_map(function ($value) {
                            return (array) $value;
                        }, $checkatpstatus);
                    }
                    if (isset($checkatpsts) && !empty($checkatpsts)) {
                        $atpstatus = PlanAtp::where(['plan_atp_id' => $plan_atp_id])->update(['status' => $status, 'demo_request_id' => $demo_request_id, 'user_farmer' => $user_farmer]);
                        if ($atpstatus) {

                            if ($user_type == 'dhanuka_doctor') {
                                $ticket1status   = DB::update("UPDATE  tickets SET `ticket_status` = '2', `status` = '2' WHERE `assign_to_dd` = '".$dd_id."' and `ticket_status` = '1'");

                                $atp1status = PlanAtp::where('dd_id',$dd_id)
                                                ->where('status', '1')
                                                ->where('plan_atp_id','!=',$plan_atp_id)
                                                ->update(['status' => '2']);

                                $ticket12status   = DB::update("UPDATE  tickets SET `created_at` = '".date('Y-m-d H:i:s')."', `updated_at` = '".date('Y-m-d H:i:s')."' WHERE `plan_atp_id` = '".$plan_atp_id."'");
                                
                                $atp12status = PlanAtp::where('dd_id',$dd_id)
                                              ->where('plan_atp_id','=',$plan_atp_id)
                                              ->update(['updated_at' => date('Y-m-d H:i:s')]);
                            }
                            else
                            {
                                $ticket1status   = DB::update("UPDATE  tickets SET `ticket_status` = '2', `status` = '2' WHERE `user_id` = '".$dd_id."' and `user_type` = '".$user_type."' and  `assign_to_dd` = 0 and `forwhom`='1' and `ticket_status` = '1'");

                                $atp1status = PlanAtp::where('created_by_user_id', $dd_id)
                                                ->where('user_type', $user_type)
                                                // ->where('dd_id', 0)
                                                ->where('forwhom', 1)
                                                ->where('status', '1')
                                                ->where('plan_atp_id','!=',$plan_atp_id)
                                                ->update(['status' => '2']);

                                if($designation == 'COUNTERPART-FL') {
                                    $cpUserDetail = User::Where('counterpart_fl',$dd_id)->Where('status',1)->get();
                                    $bdo_id       = $cpUserDetail[0]->id;
                                    $atp1status = PlanAtp::where('created_by_user_id', $bdo_id)
                                                ->where('user_type', $user_type)
                                                ->where('forwhom', 2)
                                                ->where('status', '1')
                                                ->where('plan_atp_id','!=',$plan_atp_id)
                                                ->update(['status' => '2']);

                                    $ticket1status   = DB::update("UPDATE  tickets SET `ticket_status` = '2', `status` = '2' WHERE `user_id` = '".$dd_id."' and `user_type` = '".$user_type."' and  `assign_to_dd` = 0 and `forwhom`='2' and `ticket_status` = '1'");
                                }

                                $ticket12status   = DB::update("UPDATE  tickets SET `updated_at` = '".date('Y-m-d H:i:s')."' WHERE `plan_atp_id` = '".$plan_atp_id."'");
                                
                                $atp12status = PlanAtp::where('plan_atp_id','=',$plan_atp_id)
                                              ->update(['updated_at' => date('Y-m-d H:i:s')]);
                            }

                            $ticketstatus   = DB::update("UPDATE  tickets SET `ticket_status` = '".$status."', `status` = '".$status."', `demo_request_id` = '".$demo_request_id."', `user_farmer` = '".$user_farmer."' WHERE `plan_atp_id` ='".$plan_atp_id."'");

                            $log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $dd_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

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
                                        'status_code' => 400,
                                        'message' => 'Please try again'
                            ]);
                        }
                    }
                    else
                    {   
                        $atpstatus = PlanAtp::where(['plan_atp_id' => $plan_atp_id])->update(['status' => $status, 'demo_request_id' => $demo_request_id, 'user_farmer' => $user_farmer]);
                        if ($atpstatus) {

                            if ($user_type == 'dhanuka_doctor') {
                                $ticket1status   = DB::update("UPDATE  tickets SET `ticket_status` = '2', `status` = '2' WHERE `assign_to_dd` = '".$dd_id."' and `ticket_status` = '1'");

                                $atp1status = PlanAtp::where('dd_id',$dd_id)
                                                ->where('status', '1')
                                                ->where('plan_atp_id','!=',$plan_atp_id)
                                                ->update(['status' => '2']);

                                $ticket12status   = DB::update("UPDATE  tickets SET `created_at` = '".date('Y-m-d H:i:s')."', `updated_at` = '".date('Y-m-d H:i:s')."' WHERE `plan_atp_id` = '".$plan_atp_id."'");
                                
                                $atp12status = PlanAtp::where('dd_id',$dd_id)
                                              ->where('plan_atp_id','=',$plan_atp_id)
                                              ->update(['updated_at' => date('Y-m-d H:i:s')]);
                            }
                            else
                            {
                                $ticket1status   = DB::update("UPDATE  tickets SET `ticket_status` = '2', `status` = '2' WHERE `user_id` = '".$dd_id."' and `user_type` = '".$user_type."' and  `assign_to_dd` = 0 and `forwhom`='1' and `ticket_status` = '1'");

                                $atp1status = PlanAtp::where('created_by_user_id', $dd_id)
                                                ->where('user_type', $user_type)
                                                ->where('dd_id', 0)
                                                ->where('forwhom', 1)
                                                ->where('status', '1')
                                                ->where('plan_atp_id','!=',$plan_atp_id)
                                                ->update(['status' => '2']);

                                if($designation == 'COUNTERPART-FL') {
                                    $cpUserDetail = User::Where('counterpart_fl',$dd_id)->Where('status',1)->get();
                                    $bdo_id       = $cpUserDetail[0]->id;
                                    $atp1status = PlanAtp::where('created_by_user_id', $bdo_id)
                                                ->where('user_type', $user_type)
                                                ->where('forwhom', 2)
                                                ->where('status', '1')
                                                ->where('plan_atp_id','!=',$plan_atp_id)
                                                ->update(['status' => '2']);

                                    $ticket1status   = DB::update("UPDATE  tickets SET `ticket_status` = '2', `status` = '2' WHERE `user_id` = '".$dd_id."' and `user_type` = '".$user_type."' and  `assign_to_dd` = 0 and `forwhom`='2' and `ticket_status` = '1'");
                                }

                                $ticket12status   = DB::update("UPDATE  tickets SET `created_at` = '".date('Y-m-d H:i:s')."', `updated_at` = '".date('Y-m-d H:i:s')."' WHERE `plan_atp_id` = '".$plan_atp_id."'");
                                
                                $atp12status = PlanAtp::where('dd_id',$dd_id)
                                              ->where('plan_atp_id','=',$plan_atp_id)
                                              ->update(['updated_at' => date('Y-m-d H:i:s')]);
                            }
                            $ticketstatus   = DB::update("UPDATE  tickets SET `ticket_status` = '".$status."', `status` = '".$status."', `demo_request_id` = '".$demo_request_id."', `user_farmer` = '".$user_farmer."' WHERE `plan_atp_id` ='".$plan_atp_id."'");

                            $log_firstTime  = strtotime($log_start_time);
                            $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                            $log_time_diff  = $log_lastTime-$log_firstTime;
                            $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                            DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $dd_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

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
                                'status_code' => 400,
                                'message' => 'Please try again'
                            ]);
                        }
                    }
                }
                else
                {
                    return $this->respond([
                        'status' => 'error',
                        'status_code' => 400,
                        'message' => 'You are not authorised to active ATP.'
                    ]);
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
                if ($request->has('offset')) {
                    $offset=$request->input('offset') * 50;
                }
                else
                {
                    $offset=0;
                } 
                $api_token = $request->input('apiToken');
                $user = $this->findUser($api_token);
                $plan_atp_id= $request->input('plan_atp_id');
                $type= $request->input('type');
               
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;

                    if ($user_type == 'bdo') {
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
                        }

                        if (count($bdoresult) > 0) {
                            $ddData = DB::table('dhanuka_doctor')
                                      ->whereIn('bdo_id',$bdoresult)
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
                        else
                        {
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
                        }
                        

                        if ($type == 'total_farmer') {
                            $totalFarmer = [];
                            $Ffarmer = "select `farmers`.*, `states`.`state`, `tehsils`.`tehsil`, `districts`.`district`, `tehsils`.`tehsil_id` as `tehsilId`, `districts`.`district_id` as `districtId`, `districts`.`state_id` as `stateId`, `tickets`.`village`, `tickets`.`product_1_id` as `product_id`,`tickets`.`crop_1_id` as `crop_id` from `farmers` left join `tickets` on `tickets`.`ticket_id` = `farmers`.`ticket_id` left join `tehsils` on `tehsils`.`tehsil_id` = `tickets`.`tehsil_id` left join `districts` on `districts`.`district_id` = `tickets`.`district_id` left join `states` on `states`.`state_id` = `tickets`.`state_id` where `farmers`.`ticket_id` = '".$plan_atp_id."' ORDER BY 
                                        `farmers`.`farmer_id` ASC 
                                    LIMIT
                                        " . $offset . ", 50";
                            // if (count($bdoresult) == 0) {
                            //     $Ffarmer .= " AND `farmers`.`user_id` = '".$user_id."' and `farmers`.`user_type` = 'bdo' and `farmers`.`ticket_status` != '0'";
                            // }
                            
                            // if (count($ddresult) > 0) {
                            //     $ddresult = implode(',', $ddresult);

                            //     $Ffarmer .=" OR `farmers`.`user_id` in('".$ddresult."') and `farmers`.`user_type` = 'dhanuka_doctor' AND `farmers`.`ticket_id` = '".$plan_atp_id."'";
                            // }

                            // if ( isset($bdoresult) && count($bdoresult) > 0) {
                            //     $bdoresult = implode(',', $bdoresult);

                            //     $Ffarmer .=" OR `farmers`.`user_id` in('".$bdoresult."') and `farmers`.`user_type` = 'bdo' AND `farmers`.`ticket_id` = '".$plan_atp_id."'";
                            // }
                           // echo $Ffarmer;die;
                            $FFarmers = DB::select(DB::raw($Ffarmer));
                            $totalFarmer = array_map(function ($value) {
                                return (array) $value;
                            }, $FFarmers);
                            //print_r($totalFarmer);die;
                            if (isset($totalFarmer) && !empty($totalFarmer)) {
                                foreach ($totalFarmer as $key => $value) {
                                    $leaddata =  DB::table('leads')
                                                ->where('leads.farmer_id',$totalFarmer[$key]['farmer_id'])
                                                ->where('leads.farmer_type','register_farmer')
                                                ->select('leads.lead_status')
                                                ->get();
                                    if ($leaddata) { 
                                        $totalFarmer[$key]['lead_status'] = $leaddata[0]->lead_status;
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
                        }
                        else
                        {
                            
                            $Ffarmer = "select `farmers`.*, `states`.`state`, `tehsils`.`tehsil`, `districts`.`district`, `tehsils`.`tehsil_id` as `tehsilId`, `districts`.`district_id` as `districtId`, `districts`.`state_id` as `stateId`, `tickets`.`village`, `tickets`.`product_1_id` as `product_id`,`tickets`.`crop_1_id` as `crop_id` from `farmers` left join `tickets` on `tickets`.`ticket_id` = `farmers`.`ticket_id` left join `tehsils` on `tehsils`.`tehsil_id` = `tickets`.`tehsil_id` left join `districts` on `districts`.`district_id` = `tickets`.`district_id` left join `states` on `states`.`state_id` = `tickets`.`state_id` where `farmers`.`ticket_id` = '".$plan_atp_id."' ORDER BY 
                                        `farmers`.`farmer_id` ASC 
                                    LIMIT
                                        " . $offset . ", 50";
                            // if (count($bdoresult) == 0) {
                            //     $Ffarmer .= " AND `farmers`.`user_id` = '".$user_id."' and `farmers`.`user_type` = 'bdo' AND `farmers`.`validation_status` = 'Validated' and `farmers`.`ticket_status` != '0'";
                            // }
                            
                            // if (count($ddresult) > 0) {
                            //     $ddresult = implode(',', $ddresult);
                            //     $Ffarmer .=" OR `farmers`.`user_id` in('".$ddresult."') and `farmers`.`user_type` = 'dhanuka_doctor' AND `farmers`.`ticket_id` = '".$plan_atp_id."' AND `farmers`.`validation_status` = 'Validated'";
                            // }
                            $FFarmers = DB::select(DB::raw($Ffarmer));
                            $totalFarmer = array_map(function ($value) {
                                return (array) $value;
                            }, $FFarmers);
                            
                            if (isset($totalFarmer) && !empty($totalFarmer)) {
                                foreach ($totalFarmer as $key => $value) {
                                    $totalFarmer[$key]['farmer_name'] = "NA";
                                    $leaddata =  DB::table('leads')
                                                ->where('leads.farmer_id',$totalFarmer[$key]['farmer_id'])
                                                ->where('leads.farmer_type','register_farmer')
                                                ->select('leads.lead_status')
                                                ->get();
                                    if ($leaddata) {
                                        $totalFarmer[$key]['lead_status'] = $leaddata[0]->lead_status;
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
                        }
                    }
                    elseif ($user_type == 'dhanuka_doctor') {
                        if ($type == 'total_farmer') {
                            $totalFarmer = [];
                            $Ffarmer = "select `farmers`.*, `states`.`state`, `tehsils`.`tehsil`, `districts`.`district`, `tehsils`.`tehsil_id` as `tehsilId`, `districts`.`district_id` as `districtId`, `districts`.`state_id` as `stateId`, `tickets`.`village`, `tickets`.`product_1_id` as `product_id`,`tickets`.`crop_1_id` as `crop_id` from `farmers` left join `tickets` on `tickets`.`ticket_id` = `farmers`.`ticket_id` left join `tehsils` on `tehsils`.`tehsil_id` = `tickets`.`tehsil_id` left join `districts` on `districts`.`district_id` = `tickets`.`district_id` left join `states` on `states`.`state_id` = `tickets`.`state_id` where `farmers`.`ticket_id` = '".$plan_atp_id."' ORDER BY 
                                        `farmers`.`farmer_id` ASC 
                                    LIMIT
                                        " . $offset . ", 50";
                            //and `farmers`.`user_id` = '".$user_id."' and `farmers`.`user_type` = 'dhanuka_doctor' and `farmers`.`ticket_status` != '0'
                            $FFarmers = DB::select(DB::raw($Ffarmer));
                            $totalFarmer = array_map(function ($value) {
                                return (array) $value;
                            }, $FFarmers);

                            if (isset($totalFarmer) && !empty($totalFarmer)) {
                                foreach ($totalFarmer as $key => $value) {
                                    $leaddata =  DB::table('leads')
                                                ->where('leads.farmer_id',$totalFarmer[$key]['farmer_id'])
                                                ->where('leads.farmer_type','register_farmer')
                                                ->select('leads.lead_status')
                                                ->get();
                                    if ($leaddata) { 
                                        $totalFarmer[$key]['lead_status'] = $leaddata[0]->lead_status;
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
                        }
                        else
                        {
                            
                            $Ffarmer = "select `farmers`.*, `states`.`state`, `tehsils`.`tehsil`, `districts`.`district`, `tehsils`.`tehsil_id` as `tehsilId`, `districts`.`district_id` as `districtId`, `districts`.`state_id` as `stateId`, `tickets`.`village`, `tickets`.`product_1_id` as `product_id`,`tickets`.`crop_1_id` as `crop_id` from `farmers` left join `tickets` on `tickets`.`ticket_id` = `farmers`.`ticket_id` left join `tehsils` on `tehsils`.`tehsil_id` = `tickets`.`tehsil_id` left join `districts` on `districts`.`district_id` = `tickets`.`district_id` left join `states` on `states`.`state_id` = `tickets`.`state_id` where `farmers`.`ticket_id` = '".$plan_atp_id."' ORDER BY 
                                        `farmers`.`farmer_id` ASC 
                                    LIMIT
                                        " . $offset . ", 50";
                            // and `farmers`.`user_id` = '".$user_id."' and `farmers`.`validation_status` = 'Validated' and `farmers`.`user_type` = 'dhanuka_doctor' and `farmers`.`ticket_status` != '0'
                            $FFarmers = DB::select(DB::raw($Ffarmer));
                            $totalFarmer = array_map(function ($value) {
                                return (array) $value;
                            }, $FFarmers);
                            
                            if (isset($totalFarmer) && !empty($totalFarmer)) {
                                foreach ($totalFarmer as $key => $value) {
                                    $totalFarmer[$key]['farmer_name'] = "NA";
                                    $leaddata =  DB::table('leads')
                                                ->where('leads.farmer_id',$totalFarmer[$key]['farmer_id'])
                                                ->where('leads.farmer_type','register_farmer')
                                                ->select('leads.lead_status')
                                                ->get();
                                    if ($leaddata) {
                                        $totalFarmer[$key]['lead_status'] = $leaddata[0]->lead_status;
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
                                $ddresult1 = implode("','", $ddresult);
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
                                $bdoresult1 = implode("','", $bdoresult);
                            }
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
                                $ddresult1 = implode("','", $ddresult);
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
                                $bdoresult1 = implode("','", $bdoresult);
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
                                $ddresult1 = implode("','", $ddresult);
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
                                $bdoresult1 = implode("','", $bdoresult);
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
                                $ddresult1 = implode("','", $ddresult);
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
                                $bdoresult1 = implode("','", $bdoresult);
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
                                $ddresult1 = implode("','", $ddresult);
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
                                $bdoresult1 = implode("','", $bdoresult); 
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
                                        $bdoresult1 = implode("','", $bdoresult);
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
                                            $ddresult1 = implode("','", $ddresult);
                                        }
                                    }
                                }
                            }
                        }
                        
                        if ($type == 'total_farmer') {
                            $totalFarmer = [];
                            $Ffarmer = "select `farmers`.*, `states`.`state`, `tehsils`.`tehsil`, `districts`.`district`, `tehsils`.`tehsil_id` as `tehsilId`, `districts`.`district_id` as `districtId`, `districts`.`state_id` as `stateId`, `tickets`.`village`, `tickets`.`product_1_id` as `product_id`,`tickets`.`crop_1_id` as `crop_id` from `farmers` left join `tickets` on `tickets`.`ticket_id` = `farmers`.`ticket_id` left join `tehsils` on `tehsils`.`tehsil_id` = `tickets`.`tehsil_id` left join `districts` on `districts`.`district_id` = `tickets`.`district_id` left join `states` on `states`.`state_id` = `tickets`.`state_id` where `farmers`.`ticket_id` = '".$plan_atp_id."' ORDER BY 
                                        `farmers`.`farmer_id` ASC 
                                    LIMIT
                                        " . $offset . ", 50";
                            //AND `farmers`.`user_id` = '".$user_id."' and `farmers`.`user_type` = '".$user_type."' and `farmers`.`ticket_status` != '0' AND `farmers`.`ticket_id` = '".$plan_atp_id."'
                            // if (count($bdoresult) > 0) {
                            //     $Ffarmer .= " OR `farmers`.`user_id` in('".$bdoresult1."') and `farmers`.`user_type` = 'bdo' and `farmers`.`ticket_status` != '0'";
                            // }
                            // else
                            // {
                            //     $Ffarmer .= " AND `farmers`.`user_id` = '".$user_id."' and `farmers`.`user_type` = '".$user_type."' and `farmers`.`ticket_status` != '0'";
                            // }
                            
                            // if (count($ddresult) > 0) {
                            //     $Ffarmer .=" OR `farmers`.`user_id` in('".$ddresult1."') and `farmers`.`user_type` = 'dhanuka_doctor' AND `farmers`.`ticket_id` = '".$plan_atp_id."'";
                            // }
                            //echo $Ffarmer;die;
                            // if ( isset($bdoresult) && count($bdoresult) > 0) {
                            //     $bdoresult = implode(',', $bdoresult);

                            //     $Ffarmer .=" OR `farmers`.`user_id` in('".$bdoresult."') and `farmers`.`user_type` = 'bdo' AND `farmers`.`ticket_id` = '".$plan_atp_id."'";
                            // }
                            //echo $Ffarmer;die;
                            $FFarmers = DB::select(DB::raw($Ffarmer));
                            $totalFarmer = array_map(function ($value) {
                                return (array) $value;
                            }, $FFarmers);
                            //print_r($totalFarmer);die;
                            if (isset($totalFarmer) && !empty($totalFarmer)) {
                                foreach ($totalFarmer as $key => $value) {
                                    $leaddata =  DB::table('leads')
                                                ->where('leads.farmer_id',$totalFarmer[$key]['farmer_id'])
                                                ->where('leads.farmer_type','register_farmer')
                                                ->select('leads.lead_status')
                                                ->get();
                                    if ($leaddata) { 
                                        $totalFarmer[$key]['lead_status'] = $leaddata[0]->lead_status;
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
                        }
                        else
                        {
                            
                            $Ffarmer = "select `farmers`.*, `states`.`state`, `tehsils`.`tehsil`, `districts`.`district`, `tehsils`.`tehsil_id` as `tehsilId`, `districts`.`district_id` as `districtId`, `districts`.`state_id` as `stateId`, `tickets`.`village`, `tickets`.`product_1_id` as `product_id`,`tickets`.`crop_1_id` as `crop_id` from `farmers` left join `tickets` on `tickets`.`ticket_id` = `farmers`.`ticket_id` left join `tehsils` on `tehsils`.`tehsil_id` = `tickets`.`tehsil_id` left join `districts` on `districts`.`district_id` = `tickets`.`district_id` left join `states` on `states`.`state_id` = `tickets`.`state_id` where `farmers`.`ticket_id` = '".$plan_atp_id."' ORDER BY 
                                        `farmers`.`farmer_id` ASC 
                                    LIMIT
                                        " . $offset . ", 50";
                            // AND `farmers`.`user_id` = '".$user_id."' and `farmers`.`user_type` = '".$user_type."' AND `farmers`.`validation_status` = 'Validated' and `farmers`.`ticket_status` != '0'
                            // if (count($bdoresult) > 0) {
                            //     $Ffarmer .= " OR `farmers`.`user_id` in('".$bdoresult1."') and `farmers`.`user_type` = 'bdo' AND `farmers`.`validation_status` = 'Validated' and `farmers`.`ticket_status` != '0'";
                            // }
                            // else
                            // {
                            //     $Ffarmer .= " AND `farmers`.`user_id` = '".$user_id."' and `farmers`.`user_type` = '".$user_type."' AND `farmers`.`validation_status` = 'Validated' and `farmers`.`ticket_status` != '0'";
                            // }
                            
                            // if (count($ddresult) > 0) {
                            //     $Ffarmer .=" OR `farmers`.`user_id` in('".$ddresult1."') and `farmers`.`user_type` = 'dhanuka_doctor' AND `farmers`.`ticket_id` = '".$plan_atp_id."' AND `farmers`.`validation_status` = 'Validated'";
                            // }
                            $FFarmers = DB::select(DB::raw($Ffarmer));
                            $totalFarmer = array_map(function ($value) {
                                return (array) $value;
                            }, $FFarmers);
                            
                            if (isset($totalFarmer) && !empty($totalFarmer)) {
                                foreach ($totalFarmer as $key => $value) {
                                    $totalFarmer[$key]['farmer_name'] = "NA";
                                    $leaddata =  DB::table('leads')
                                                ->where('leads.farmer_id',$totalFarmer[$key]['farmer_id'])
                                                ->where('leads.farmer_type','register_farmer')
                                                ->select('leads.lead_status')
                                                ->get();
                                    if ($leaddata) {
                                        $totalFarmer[$key]['lead_status'] = $leaddata[0]->lead_status;
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
                        }
                    }
                    
                    
                    if (isset($totalFarmer) && !empty($totalFarmer)) {
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
        //$base_url = url('/');
        $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
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
                    // if ($user_type == 'dhanuka_doctor') {
                    //     $ddData1 = DB::table('assigned_village')
                    //                ->where('assigned_village.assigned_to_dd_id', $user_id)
                    //                ->select('assigned_village.village_id')
                    //                ->get();
                    //     $ddData = DB::table('villages')
                    //               ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                    //               ->join('districts','districts.district_id','=','villages.district_id')
                    //               ->join('states','states.state_id','=','districts.state_id')
                    //               ->select('villages.village_id as villageId','villages.village as villageName','states.state','tehsils.tehsil','districts.district')
                    //               ->where('villages.status', '1')
                    //               ->where('villages.village_id', $ddData1[0]->village_id)
                    //               ->get();
                    // }
                    // else
                    // {
                        $ddData = DB::table('villages')
                                ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                                ->join('districts','districts.district_id','=','villages.district_id')
                                ->join('states','states.state_id','=','districts.state_id')
                                ->select('villages.village_id as villageId','villages.village as villageName','states.state','tehsils.tehsil','districts.district')
                                ->where('villages.status', '1')
                                ->where('villages.tehsil_id', $tehsil_id)
                                ->get();
                    //}
                    
                    
                    
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

    public function ddVillageList(Request $request) {

        $data = $request->json()->all();
        //$base_url = url('/');
        $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
        if ($data) {
            $rules = array(
                'apiToken' => 'required|string'
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

                    if ($user_type == 'bdo') {
                        $ddData = DB::table('assigned_village')
                                    ->join('villages','villages.village_id','=','assigned_village.village_id')
                                    ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                                    ->join('districts','districts.district_id','=','villages.district_id')
                                    ->join('states','states.state_id','=','districts.state_id')
                                    ->select('villages.village_id as villageId','villages.village as villageName','states.state','tehsils.tehsil','districts.district')
                                    ->where('villages.status', '1')
                                    ->where('assigned_village.assigned_by_bdo_id', $user_id)
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
                    }
                    else
                    {
                        $ddData = DB::table('assigned_village')
                                    ->join('villages','villages.village_id','=','assigned_village.village_id')
                                    ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                                    ->join('districts','districts.district_id','=','villages.district_id')
                                    ->join('states','states.state_id','=','districts.state_id')
                                    ->select('villages.village_id as villageId','villages.village as villageName','states.state','tehsils.tehsil','districts.district')
                                    ->where('villages.status', '1')
                                    ->where('assigned_village.assigned_to_dd_id', $user_id)
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
                    }
                              
                    
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Dhanuka Doctor Village List',
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

    public function distributorList(Request $request)
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
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $distributor =  DB::table('distributor')
                                    ->select('distributor_id','name')
                                    ->get();
                if ($distributor) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Distributor List!',
                                'data' => $distributor
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No distributor found!',
                                'data' => []
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function retailerList(Request $request)
    {
        $rules = array(
            'apiToken' => 'required|string',
            'distributor_id' => 'required'
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
                $distributor_id= $request->input('distributor_id');
                $retailer =  DB::table('retailer')
                                    ->whereRaw("find_in_set($distributor_id,distributor)")
                                    ->select('retailer_id','name')
                                    ->get();
                if ($retailer) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Retailer List!',
                                'data' => $retailer
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No retailer found!',
                                'data' => []
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function cropProductList(Request $request)
    {
        $rules = array(
            'apiToken' => 'required|string',
            'crop_id' => 'required'
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
                $crop_id= $request->input('crop_id');
                $crop =  DB::table('product_crop_relations')
                            ->join('products AS pcr', 'pcr.product_id', '=', 'product_crop_relations.product_id')
                            ->where('crop_id',$crop_id)
                            ->where('pcr.status', 1)
                            ->select('pcr.product_id','pcr.product')
                            ->groupBy('pcr.product_id')
                            ->get();
                if ($crop) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Product List!',
                                'data' => $crop
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No prouct found!',
                                'data' => []
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function BdoLeads(Request $request) {
        $rules = array(
            'apiToken' => 'required|string',
            'type' => 'required',
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
                $type = $request->input('type');
                
                if ($user_type == 'bdo') 
                {
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
                    
                    if ($type == '1') { 

                        $myleads = DB::table('leads')
                                ->join('products','products.product_id','=','leads.product_id','left')
                                ->join('village_farmers','village_farmers.farmer_id','=','leads.farmer_id')
                                ->join('villages','villages.village_id','=','village_farmers.village_id')
                                ->join('distributor','distributor.distributor_id','=','leads.distributor_id','left')
                                ->join('retailer','retailer.retailer_id','=','leads.purchased_from','left');
                                if ($request->has('dd_ids')) {
                                   $myleads->whereIn('leads.user_id',$request->input('dd_ids'));
                                   $myleads->where('leads.user_type','dhanuka_doctor');
                                   $myleads->where('leads.lead_status','1');
                                }
                                else
                                {
                                    $myleads->whereIn('leads.lead_id',function($query) use ($ddresult)
                                        {
                                            $query->select('lead_id')->from('leads')->whereIn('user_id',$ddresult)->where('user_type','dhanuka_doctor')->where('lead_status','1');
                                        });

                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $myleads->whereBetween('leads.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                    }

                                    if ($request->has('product_ids')) {
                                       $myleads->whereIn('leads.product_id',$request->input('product_ids'));
                                    }

                                    $myleads->orWhere(['leads.user_id' => $user_id, 'leads.user_type' => $user_type, 'leads.lead_status' => $type]);
                                }

                                if($request->has('from_date') && $request->has('to_date')){
                                    $from_date=$request->input('from_date');
                                    $to_date=$request->input('to_date');
                                    $myleads->whereBetween('leads.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }

                                if ($request->has('product_ids')) {
                                   $myleads->whereIn('leads.product_id',$request->input('product_ids'));
                                }
                                
                            $myleads = $myleads->orderBy('created_at', 'DESC')
                                ->select('leads.*','products.product','village_farmers.farmer_name as leadName','village_farmers.contact_number as contact_number','villages.village as villageName','village_farmers.call_status','distributor.name as distributor_name','retailer.name as purchased_from')
                                ->get();
                            // echo  $myleads;die;
                        foreach ($myleads as $key => $value) {
                            $cropdata =  DB::table('village_farmer_crop')
                                        ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                                        ->where('village_farmer_crop.farmer_id',$myleads[$key]->farmer_id)
                                        ->select('village_farmer_crop.*','crops.crop')
                                        ->get();
                            if ($cropdata) {
                                $myleads[$key]->cropdata = $cropdata;
                            }
                            else
                            {
                                $myleads[$key]->cropdata = [];
                            }
                        }
                        
                    }
                    elseif ($type == '2') 
                    {
                        $myleads = DB::table('leads')
                                    ->join('products','products.product_id','=','leads.product_id','left')
                                    ->join('village_farmers','village_farmers.farmer_id','=','leads.farmer_id')
                                    ->join('villages','villages.village_id','=','village_farmers.village_id')
                                    ->join('distributor','distributor.distributor_id','=','leads.distributor_id','left')
                                    ->join('retailer','retailer.retailer_id','=','leads.purchased_from','left');
                                if ($request->has('dd_ids')) {
                                   $myleads->whereIn('leads.user_id',$request->input('dd_ids'));
                                   $myleads->where('leads.user_type','dhanuka_doctor');
                                   $myleads->whereIn('leads.lead_status',['2','3']);
                                }
                                else
                                {    
                                    $myleads->whereIn('leads.lead_id',function($query) use ($ddresult)
                                        {
                                            $query->select('lead_id')->from('leads')->whereIn('user_id',$ddresult)->where('user_type','dhanuka_doctor')->whereIn('lead_status',['2','3']);
                                        });
                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $myleads->whereBetween('leads.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                    }

                                    if ($request->has('product_ids')) {
                                       $myleads->whereIn('leads.product_id',$request->input('product_ids'));
                                    }
                                    $myleads->orWhere(['leads.user_id' => $user_id, 'leads.user_type' => $user_type, 'leads.lead_status' => $type]);
                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $myleads->whereBetween('leads.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                    }

                                    if ($request->has('product_ids')) {
                                       $myleads->whereIn('leads.product_id',$request->input('product_ids'));
                                    }

                                    $myleads->orWhere(['leads.user_id' => $user_id, 'leads.user_type' => $user_type, 'leads.lead_status' => '3']);
                                }

                                if($request->has('from_date') && $request->has('to_date')){
                                    $from_date=$request->input('from_date');
                                    $to_date=$request->input('to_date');
                                    $myleads->whereBetween('leads.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }

                                if ($request->has('product_ids')) {
                                   $myleads->whereIn('leads.product_id',$request->input('product_ids'));
                                }
                                $myleads = $myleads->orderBy('created_at', 'DESC')
                                    ->select('leads.*','products.product','village_farmers.farmer_name as leadName','village_farmers.contact_number as contact_number','villages.village as villageName','village_farmers.call_status','distributor.name as distributor_name','retailer.name as purchased_from')
                                    ->get();

                        foreach ($myleads as $key => $value) {
                            $cropdata =  DB::table('village_farmer_crop')
                                        ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                                        ->where('village_farmer_crop.farmer_id',$myleads[$key]->farmer_id)
                                        ->select('village_farmer_crop.*','crops.crop')
                                        ->get();
                            if ($cropdata) {
                                $myleads[$key]->cropdata = $cropdata;
                            }
                            else
                            {
                                $myleads[$key]->cropdata = [];
                            }
                        }
                    }
                }
                else
                {
                    if ($type == '1') { 
                        $myleads = DB::table('leads')
                                    ->join('products','products.product_id','=','leads.product_id','left')
                                    ->join('village_farmers','village_farmers.farmer_id','=','leads.farmer_id','left')
                                    ->join('farmers','farmers.farmer_id','=','leads.farmer_id','left')
                                    ->join('villages','villages.village_id','=','village_farmers.village_id','left')
                                    ->join('tickets','tickets.ticket_id','=','farmers.ticket_id','left')
                                    ->join('distributor','distributor.distributor_id','=','leads.distributor_id','left')
                                    ->join('retailer','retailer.retailer_id','=','leads.purchased_from','left')
                                    ->where('leads.user_id',$user_id)
                                    ->where('leads.user_type',$user_type)
                                    ->where('leads.lead_status',$type)
                                    ->orderBy('created_at', 'DESC')
                                    ->select('leads.*','products.product','distributor.name as distributor_name','retailer.name as purchased_from', DB::raw("(CASE WHEN village_farmers.farmer_name != 'null' THEN village_farmers.farmer_name ELSE farmers.farmer_name END) as leadName"), DB::raw("(CASE WHEN village_farmers.contact_number != 'null' THEN village_farmers.contact_number ELSE farmers.mobile END) as contact_number"), DB::raw("(CASE WHEN village_farmers.call_status != 'null' THEN village_farmers.call_status ELSE farmers.call_status END) as call_status"), DB::raw("(CASE WHEN villages.village != 'null' THEN villages.village ELSE tickets.village END) as villageName"))
                                    ->get();
                        //echo $myleads;die;
                        foreach ($myleads as $key => $value) {
                            $cropdata =  DB::table('village_farmer_crop')
                                        ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                                        ->where('village_farmer_crop.farmer_id',$myleads[$key]->farmer_id)
                                        ->select('village_farmer_crop.*','crops.crop')
                                        ->get();
                            if ($cropdata) {
                                $myleads[$key]->cropdata = $cropdata;
                            }
                            else
                            {
                                $myleads[$key]->cropdata = [];
                            }
                        }
                        
                    }
                    elseif ($type == '2') 
                    {
                        $myleads = DB::table('leads')
                                    ->join('products','products.product_id','=','leads.product_id','left')
                                    ->join('village_farmers','village_farmers.farmer_id','=','leads.farmer_id','left')
                                    ->join('farmers','farmers.farmer_id','=','leads.farmer_id','left')
                                    ->join('villages','villages.village_id','=','village_farmers.village_id','left')
                                    ->join('tickets','tickets.ticket_id','=','farmers.ticket_id','left')
                                    ->join('distributor','distributor.distributor_id','=','leads.distributor_id','left')
                                    ->join('retailer','retailer.retailer_id','=','leads.purchased_from','left')
                                    ->where('leads.user_id',$user_id)
                                    ->where('leads.user_type',$user_type)
                                    ->where('leads.lead_status',$type)
                                    ->orWhere(['leads.user_id' => $user_id, 'leads.user_type' => $user_type, 'leads.lead_status' => '3'])
                                    ->orderBy('created_at', 'DESC')
                                    ->select('leads.*','products.product','distributor.name as distributor_name','retailer.name as purchased_from', DB::raw("(CASE WHEN village_farmers.farmer_name != 'null' THEN village_farmers.farmer_name ELSE farmers.farmer_name END) as leadName"), DB::raw("(CASE WHEN village_farmers.contact_number != 'null' THEN village_farmers.contact_number ELSE farmers.mobile END) as contact_number"), DB::raw("(CASE WHEN village_farmers.call_status != 'null' THEN village_farmers.call_status ELSE farmers.call_status END) as call_status"), DB::raw("(CASE WHEN villages.village != 'null' THEN villages.village ELSE tickets.village END) as villageName"))
                                    ->get();

                        foreach ($myleads as $key => $value) {
                            $cropdata =  DB::table('village_farmer_crop')
                                        ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                                        ->where('village_farmer_crop.farmer_id',$myleads[$key]->farmer_id)
                                        ->select('village_farmer_crop.*','crops.crop')
                                        ->get();
                            if ($cropdata) {
                                $myleads[$key]->cropdata = $cropdata;
                            }
                            else
                            {
                                $myleads[$key]->cropdata = [];
                            }
                        }
                    }
                }

                if ($myleads) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'lead list.',
                                'data' =>$myleads
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'error',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No lead found',
                                'data' =>[]
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function BdoDemoRequestList(Request $request) {
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
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                if ($user_type == 'bdo') {
                    $ddData = DB::table('dhanuka_doctor')
                              ->where('bdo_id',$dd_id)
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
                    
                    $demoRequest =DemoRequest::join('villages','villages.village_id','=','demo_request.village_id','left')
                            ->join('tehsils','tehsils.tehsil_id','=','demo_request.tehsil_id','left')
                            ->join('districts','districts.district_id','=','demo_request.district_id','left')
                            ->join('states','states.state_id','=','demo_request.state_id','left')
                            ->join('products','products.product_id','=','demo_request.product_id','left')
                            ->join('crops','crops.crop_id','=','demo_request.crop_id','left')
                            ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','demo_request.dd_id','left');
                            if ($request->has('dd_ids')) {
                                $demoRequest->whereIn('demo_request.dd_id',$request->input('dd_ids'));
                            }
                            else
                            {
                                $demoRequest->whereIn('demo_request.dd_id',$ddresult); 
                            }

                            if ($request->has('product')) {
                            $demoRequest->whereIn('demo_request.product_id',$request->input('product'));
                            }
                            if ($request->has('crop')) {
                            $demoRequest->whereIn('demo_request.crop_id',$request->input('crop'));
                            }
                            if($request->has('from_date') && $request->has('to_date')){
                                $from_date=$request->input('from_date');
                                $to_date=$request->input('to_date');
                                $demoRequest->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }
                            
                            if ($request->has('status')) {
                                $demoRequest->where('demo_request.status',$request->input('status'));
                            }
                            $demoRequest = $demoRequest->orderBy('demo_request.demo_request_id', 'DESC')
                            ->select('demo_request.*','villages.village','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','dhanuka_doctor.name as dd_name')
                            ->get();
                    //echo $demoRequest;die;
                    if (isset($demoRequest) && !empty($demoRequest)) {
                        foreach ($demoRequest as $key => $value) {
                            $demofeedback=  DB::table('demofeedback')
                                            ->where('demofeedback.demo_request_id',$demoRequest[$key]->demo_request_id)
                                            ->select('demofeedback.creation_at','demofeedback.status')
                                            ->orderBy('creation_at', 'DESC')
                                            ->get();
                            $demoRequest[$key]->demo_feedback = ($demofeedback)?$demofeedback:[];
                        }
                    }

                }
                else
                {
                    $approval = $request->input('approval');
                    $product_id = ($request->input('product'))?$request->input('product'):"";
                    $crop_id = ($request->input('crop'))?$request->input('crop'):"";
                    $village_id = ($request->input('village_id'))?$request->input('village_id'):"";
                    $status = ($request->input('status'))?$request->input('status'):"";
                    if ($approval == 'approved') {
                        $demoRequest =DemoRequest::join('villages','villages.village_id','=','demo_request.village_id','left')
                                ->join('tehsils','tehsils.tehsil_id','=','demo_request.tehsil_id','left')
                                ->join('districts','districts.district_id','=','demo_request.district_id','left')
                                ->join('states','states.state_id','=','demo_request.state_id','left')
                                ->join('products','products.product_id','=','demo_request.product_id','left')
                                ->join('crops','crops.crop_id','=','demo_request.crop_id','left')
                                ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','demo_request.dd_id','left')
                                ->where('demo_request.dd_id',$dd_id)
                                ->whereNotIn('demo_request.status', ['Pending','Overdue']);
                                //->where('demo_request.status',$approval);
                                if (isset($product_id) && !empty($product_id)) {
                                $demoRequest->where('demo_request.product_id',$product_id);
                                }
                                if (isset($crop_id) && !empty($crop_id)) {
                                $demoRequest->where('demo_request.crop_id',$crop_id);
                                }
                                if($request->has('from_date') && $request->has('to_date')){
                                    $from_date=$request->input('from_date');
                                    $to_date=$request->input('to_date');
                                    $demoRequest->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }
                                if (isset($village_id) && !empty($village_id)) {
                                $demoRequest->where('demo_request.village_id',$village_id);
                                }
                                if (isset($status) && !empty($status)) {
                                $demoRequest->where('demo_request.status',$status);
                                }
                        $demoRequest = $demoRequest->orderBy('demo_request_id', 'DESC')
                                ->select('demo_request.*','villages.village','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','dhanuka_doctor.name as dd_name')
                                ->get();
                    }
                    else
                    {
                        $demoRequest =DemoRequest::join('villages','villages.village_id','=','demo_request.village_id','left')
                                ->join('tehsils','tehsils.tehsil_id','=','demo_request.tehsil_id','left')
                                ->join('districts','districts.district_id','=','demo_request.district_id','left')
                                ->join('states','states.state_id','=','demo_request.state_id','left')
                                ->join('products','products.product_id','=','demo_request.product_id','left')
                                ->join('crops','crops.crop_id','=','demo_request.crop_id','left')
                                ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','demo_request.dd_id','left')
                                ->where('demo_request.dd_id',$dd_id);
                                if (isset($product_id) && !empty($product_id)) {
                                $demoRequest->where('demo_request.product_id',$product_id);
                                }
                                if (isset($crop_id) && !empty($crop_id)) {
                                $demoRequest->where('demo_request.crop_id',$crop_id);
                                }
                                if($request->has('from_date') && $request->has('to_date')){
                                    $from_date=$request->input('from_date');
                                    $to_date=$request->input('to_date');
                                    $demoRequest->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }
                                if (isset($village_id) && !empty($village_id)) {
                                $demoRequest->where('demo_request.village_id',$village_id);
                                }
                                if (isset($status) && !empty($status)) {
                                $demoRequest->where('demo_request.status',$status);
                                }
                        $demoRequest = $demoRequest->orderBy('demo_request_id', 'DESC')
                                ->select('demo_request.*','villages.village','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','dhanuka_doctor.name as dd_name')
                                ->get();
                    }

                }

                if (isset($demoRequest) && !empty($demoRequest)) {
                    $base_url = url('/');
                    foreach ($demoRequest as $key => $value) {
                       if (!empty($demoRequest[$key]->demoImage)) {
                          $demoRequest[$key]->demoImage = $base_url . '/assets/demoimage/' . $demoRequest[$key]->demoImage;
                       }

                       if (!empty($demoRequest[$key]->farmer_image)) {
                          $demoRequest[$key]->farmer_image = $base_url . '/assets/farmer/' . $demoRequest[$key]->farmer_image;
                       }
                    }

                    if ($user_type == 'bdo') {
                        //pending demo count
                        $demoPending =DemoRequest::where('demo_request.status','Pending');
                        if ($request->has('dd_ids')) {
                            $demoPending->whereIn('demo_request.dd_id',$request->input('dd_ids'));
                        }
                        else
                        {
                            $demoPending->whereIn('demo_request.dd_id',$ddresult); 
                        }

                        if ($request->has('product')) {
                        $demoPending->whereIn('demo_request.product_id',$request->input('product'));
                        }
                        if ($request->has('crop')) {
                        $demoevaluated->whereIn('demo_request.crop_id',$request->input('crop'));
                        }
                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            $demoPending->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                        }
                        
                        if ($request->has('status')) {
                            $demoPending->where('demo_request.status',$request->input('status'));
                        }
                        $demoPending = $demoPending->count();

                        //Excellent demo count
                        $demoExcellent =DemoRequest::where('demo_request.status','Excellent');
                        if ($request->has('dd_ids')) {
                            $demoExcellent->whereIn('demo_request.dd_id',$request->input('dd_ids'));
                        }
                        else
                        {
                            $demoExcellent->whereIn('demo_request.dd_id',$ddresult); 
                        }

                        if ($request->has('product')) {
                        $demoExcellent->whereIn('demo_request.product_id',$request->input('product'));
                        }
                        if ($request->has('crop')) {
                        $demoExcellent->whereIn('demo_request.crop_id',$request->input('crop'));
                        }
                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            $demoExcellent->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                        }
                        
                        if ($request->has('status')) {
                            $demoExcellent->where('demo_request.status',$request->input('status'));
                        }
                        $demoExcellent = $demoExcellent->count();

                        //Average demo count
                        $demoAverage =DemoRequest::where('demo_request.status','Average');
                        if ($request->has('dd_ids')) {
                            $demoAverage->whereIn('demo_request.dd_id',$request->input('dd_ids'));
                        }
                        else
                        {
                            $demoAverage->whereIn('demo_request.dd_id',$ddresult); 
                        }

                        if ($request->has('product')) {
                        $demoAverage->whereIn('demo_request.product_id',$request->input('product'));
                        }
                        if ($request->has('crop')) {
                        $demoExcellent->whereIn('demo_request.crop_id',$request->input('crop'));
                        }
                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            $demoAverage->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                        }
                        
                        if ($request->has('status')) {
                            $demoAverage->where('demo_request.status',$request->input('status'));
                        }
                        $demoAverage = $demoAverage->count();

                        //Poor demo count
                        $demoPoor =DemoRequest::where('demo_request.status','Poor');
                        if ($request->has('dd_ids')) {
                            $demoPoor->whereIn('demo_request.dd_id',$request->input('dd_ids'));
                        }
                        else
                        {
                            $demoPoor->whereIn('demo_request.dd_id',$ddresult); 
                        }

                        if ($request->has('product')) {
                        $demoPoor->whereIn('demo_request.product_id',$request->input('product'));
                        }
                        if ($request->has('crop')) {
                        $demoPoor->whereIn('demo_request.crop_id',$request->input('crop'));
                        }
                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            $demoPoor->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                        }
                        
                        if ($request->has('status')) {
                            $demoPoor->where('demo_request.status',$request->input('status'));
                        }
                        $demoPoor = $demoPoor->count();

                        //Overdue demo count
                        $demoOverdue =DemoRequest::where('demo_request.status','Overdue');
                        if ($request->has('dd_ids')) {
                            $demoOverdue->whereIn('demo_request.dd_id',$request->input('dd_ids'));
                        }
                        else
                        {
                            $demoOverdue->whereIn('demo_request.dd_id',$ddresult); 
                        }

                        if ($request->has('product')) {
                        $demoOverdue->whereIn('demo_request.product_id',$request->input('product'));
                        }
                        if ($request->has('crop')) {
                        $demoOverdue->whereIn('demo_request.crop_id',$request->input('crop'));
                        }
                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            $demoOverdue->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                        }
                        
                        if ($request->has('status')) {
                            $demoOverdue->where('demo_request.status',$request->input('status'));
                        }
                        $demoOverdue = $demoOverdue->count();

                        //Awaited demo count
                        $demoAwaited =DemoRequest::where('demo_request.status','Awaited');
                        if ($request->has('dd_ids')) {
                            $demoAwaited->whereIn('demo_request.dd_id',$request->input('dd_ids'));
                        }
                        else
                        {
                            $demoAwaited->whereIn('demo_request.dd_id',$ddresult); 
                        }

                        if ($request->has('product')) {
                        $demoAwaited->whereIn('demo_request.product_id',$request->input('product'));
                        }
                        if ($request->has('crop')) {
                        $demoAwaited->whereIn('demo_request.crop_id',$request->input('crop'));
                        }
                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            $demoAwaited->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                        }
                        
                        if ($request->has('status')) {
                            $demoAwaited->where('demo_request.status',$request->input('status'));
                        }
                        $demoAwaited = $demoAwaited->count();

                        $resultdata = array();
                        $resultdata['Pending'] = $demoPending;
                        $resultdata['Excellent'] = $demoExcellent;
                        $resultdata['Average'] = $demoAverage;
                        $resultdata['Poor'] = $demoPoor;
                        $resultdata['Overdue'] = $demoOverdue;
                        $resultdata['Awaited'] = $demoAwaited;
                        $resultdata['demolist'] = $demoRequest;

                        return $this->respond([
                                            'status' => 'success',
                                            'status_code' => $this->getStatusCode(),
                                            'message' => 'Demo request list.',
                                            'data' => $resultdata
                                        ]);

                    }
                    else
                    {
                        return $this->respond([
                                            'status' => 'success',
                                            'status_code' => $this->getStatusCode(),
                                            'message' => 'Demo request list.',
                                            'data' =>$demoRequest
                                ]);
                    }

                }
                else
                {
                    return $this->respond([
                                            'status' => 'success',
                                            'status_code' => $this->getStatusCode(),
                                            'message' => 'No data found.',
                                            'data' =>array()
                                ]);
                }
                
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function DemoFeedback(Request $request) {
        try {
            $data = $request->all();
            $rules = array(
                'apiToken' => 'required',
                'status' => 'required',
                'description' => 'required',
                'demo_request_id' => 'required'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $fileName = "";
                $api_token = $request->input('apiToken');
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    
                    if($request->file('feedbackImage')){
                        $file=$request->file('feedbackImage');
                        // $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                        
                        // $file->move("assets/demofeedback",$fileName);
                        $fileSize =  $request->file('feedbackImage')->getSize();
                        if($fileSize > 100) {
                            //s3Uploads
                            $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                            $sTempFileName = 'assets/demofeedback/' . $fileName;
                            Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            //
                        }
                    }
                    $demofeedback = DB::table('demofeedback')->insert(
                                    ['demo_request_id' => $request->input('demo_request_id'),'user_id' => $user_id, 'user_type' => $user_type, 'description' => $request->input('description'), 'status' => $request->input('status'), 'feedbackImage' => ($fileName)?$fileName:"", 'creation_at' => date('Y-m-d H:i:s')]
                                  );

                    if ($demofeedback > 0) 
                    {
                        $demoStatus = DB::update("update `demo_request` set `status` = '".$request->input('status')."', `updated_at` = '".date('Y-m-d H:i:s')."' where `demo_request_id` = '".$request->input('demo_request_id')."'");

                        return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Feedback submitted successfully.'
                            ]);
                    }
                    else 
                    {
                        return $this->respondNotFound("Something went wrong. Please Try Again!!");
                    }

                        

                    
                    
                    
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function BdoLeadDetail(Request $request) {
        $rules = array(
            'apiToken' => 'required|string',
            'lead_id' => 'required',
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
                $lead_id = $request->input('lead_id');
                $mylead = DB::table('leads')
                            ->join('products','products.product_id','=','leads.product_id','left')
                            ->join('village_farmers','village_farmers.farmer_id','=','leads.farmer_id','left')
                            ->join('farmers','farmers.farmer_id','=','leads.farmer_id','left')
                            ->join('villages','villages.village_id','=','village_farmers.village_id','left')
                            ->join('tickets','tickets.ticket_id','=','farmers.ticket_id','left')
                            ->join('distributor','distributor.distributor_id','=','leads.distributor_id','left')
                            ->join('retailer','retailer.retailer_id','=','leads.purchased_from','left')
                            ->where('leads.lead_id',$lead_id)
                            ->orderBy('created_at', 'DESC')
                            ->select('leads.*','products.product',DB::raw("(CASE WHEN village_farmers.farmer_name != 'null' THEN village_farmers.farmer_name ELSE farmers.farmer_name END) as leadName"),DB::raw("(CASE WHEN village_farmers.contact_number != 'null' THEN village_farmers.contact_number ELSE farmers.mobile END) as contact_number"),DB::raw("(CASE WHEN villages.village != 'null' THEN villages.village ELSE tickets.village END) as villageName"),DB::raw("(CASE WHEN village_farmers.call_status != 'null' THEN village_farmers.call_status ELSE farmers.call_status END) as call_status"),'distributor.name as distributor_name','retailer.name as purchased_from')
                            ->get();
                            // ->select('leads.*','products.product','village_farmers.farmer_name as leadName','village_farmers.contact_number as contact_number','villages.village as villageName','village_farmers.call_status','distributor.name as distributor_name','retailer.name as purchased_from')
                $cropdata =  DB::table('village_farmer_crop')
                             ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                             ->where('village_farmer_crop.farmer_id',$mylead[0]->farmer_id)
                             ->select('village_farmer_crop.*','crops.crop')
                             ->get();
                if ($cropdata) {
                    $mylead[0]->cropdata = $cropdata;
                }
                else
                {
                    //$mylead[0]->cropdata = [];
                    $getfarmercrop = DB::table('leads as l')
                                     ->join('farmers as f','f.farmer_id','=','l.farmer_id')
                                     ->join('tickets as t','t.ticket_id','=','f.ticket_id','left')
                                     ->join('crops as c','c.crop_id','=','t.crop_1_id','left')
                                     ->where('l.lead_id',$mylead[0]->lead_id)
                                     ->where('l.farmer_type','register_farmer')
                                     ->select('c.crop','c.crop_id')
                                     ->groupBy('c.crop_id')
                                     ->get(); 
                    if (count($getfarmercrop)>0) {
                        $getfarmercrop[0]->village_farmer_crop_id = "";
                        $getfarmercrop[0]->village_id = "";
                        $getfarmercrop[0]->farmer_id = $mylead[0]->farmer_id;
                        $getfarmercrop[0]->product_id = $mylead[0]->product_id;
                        $getfarmercrop[0]->acres = "";
                        $getfarmercrop[0]->created_at = "";
                        $getfarmercrop[0]->updated_at = "";

                        $mylead[0]->cropdata = $getfarmercrop;
                    }else{
                        $mylead[0]->cropdata = [];
                    }
                }
                
                $farmerleadhistory =  DB::table('leads')
                            ->join('products','products.product_id','=','leads.product_id','left')
                            ->join('village_farmers','village_farmers.farmer_id','=','leads.farmer_id')
                            ->join('farmers','farmers.farmer_id','=','leads.farmer_id','left')
                            ->join('villages','villages.village_id','=','village_farmers.village_id','left')
                            ->join('tickets','tickets.ticket_id','=','farmers.ticket_id','left')
                            ->join('distributor','distributor.distributor_id','=','leads.distributor_id','left')
                            ->join('retailer','retailer.retailer_id','=','leads.purchased_from','left')
                            ->where('leads.farmer_id',$mylead[0]->farmer_id)
                            ->where('leads.farmer_type',$mylead[0]->farmer_type)
                            ->where('leads.lead_id','!=',$mylead[0]->lead_id)
                            ->orderBy('created_at', 'DESC')
                            ->select('leads.*','products.product',DB::raw("(CASE WHEN village_farmers.farmer_name != 'null' THEN village_farmers.farmer_name ELSE farmers.farmer_name END) as leadName"),DB::raw("(CASE WHEN village_farmers.contact_number != 'null' THEN village_farmers.contact_number ELSE farmers.mobile END) as contact_number"),DB::raw("(CASE WHEN villages.village != 'null' THEN villages.village ELSE tickets.village END) as villageName"),DB::raw("(CASE WHEN village_farmers.call_status != 'null' THEN village_farmers.call_status ELSE farmers.call_status END) as call_status"),'distributor.name as distributor_name','retailer.name as purchased_from')
                            ->get();
                if ($farmerleadhistory) {
                    $Fcropdata =  DB::table('village_farmer_crop')
                                 ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                                 ->where('village_farmer_crop.farmer_id',$farmerleadhistory[0]->farmer_id)
                                 ->select('village_farmer_crop.*','crops.crop')
                                 ->get();
                    if ($Fcropdata) {
                        $farmerleadhistory[0]->cropdata = $Fcropdata;
                    }
                    else
                    {
                        $farmerleadhistory[0]->cropdata = [];
                    }

                    $mylead[0]->farmerleadhistory = $farmerleadhistory;

                }
                else
                {
                    $mylead[0]->farmerleadhistory = [];
                }

                if ($mylead[0]) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'lead detail.',
                                'data' =>$mylead[0]
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'error',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No lead found',
                                'data' =>[]
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function BdoActivityList(Request $request) {
        try 
        {
            $rules = array(
                'apiToken' => 'required|string'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } 
            else 
            {
                $api_token=$request->input('apiToken');
                if ($request->has('offset')) {
                    $offset=$request->input('offset');
                }
                else
                {
                    $offset=0;
                }
                
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    // $base_url = url('/');
                    $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                    if ($user_type == 'bdo') 
                    {
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
                        $dd_ids = implode(',', $ddresult);
                        $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.ticket_no as ticket,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%Y-%m-%d %H:%i:%s'), t.created_at ) as datetime,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%M %e %Y'), DATE_FORMAT(t.created_at, '%M %e %Y') ) as datetime_format,
                                        -- IF( t.closing_datetime != '0000-00-00 00:00:00', DATE_FORMAT(t.closing_datetime, '%M %e %Y'), 'NA' ) as close_date,
                                        IF( t.closing_datetime != '0000-00-00 00:00:00', t.closing_datetime, 'NA' ) as close_date,
                                        CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
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
                                        t.validated_farmers,
                                        t.farmer_count,
                                        t.plan_atp_id,
                                        '" . $base_url . "/' as base_url,
                                        t.uploaded_file, 
                                        (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status
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
                                    WHERE t.status != '0'  AND t.ticket_status != '0' ";

                                    if($request->has('dd_ids')){

                                        $DDs=$request->input('dd_ids');
                                        $query .=" AND t.user_id in ('".$DDs."') AND t.user_type = 'dhanuka_doctor'";
                                    }


                                    if($request->has('fl_ids')){
                                        $flIds = $request->input('fl_ids');
                                        $query .=" AND t.user_id in ('".$flIds."') AND t.user_type = 'bdo'";
                                    }
                                    
                                    
                                    if (!$request->has('dd_ids') && !$request->has('fl_ids')) 
                                    {   
                                        $query .=" AND t.user_id='" . $user_id . "'        AND
                                        t.user_type='" . $user_type . "'   OR 
                                        t.ticket_id in  (SELECT ticket_id FROM tickets where user_id in ('".$dd_ids."') AND user_type = 'dhanuka_doctor')";
                                    }
                                    
                                    if($request->has('data_source_id')){
                                        $data_source_id=$request->input('data_source_id');
                                        $query .=" AND t.data_source_id = '".$data_source_id."'";
                                    }

                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query .=" AND t.created_at  Between '".$from_date." 00:00:00' and '".$to_date." 23:59:59' ";
                                    }   

                                    if($request->has('crop')){
                                        $crop=$request->input('crop');
                                        $query .=" AND t.crop_1_id = '".$crop."' OR t.crop_2_id = '".$crop."' OR t.crop_3_id = '".$crop."' AND t.status != '0'  AND t.ticket_status != '0'";
                                    }

                                    if($request->has('product')){
                                        $product=$request->input('product');
                                        $query .=" AND t.product_1_id = '".$product."' OR t.product_2_id = '".$product."' OR t.product_3_id = '".$product."' AND t.status != '0'  AND t.ticket_status != '0'";
                                    }


                                    $query .=" GROUP BY  
                                        t.ticket_id
                                    ORDER BY 
                                        FIELD(ticket_status, '1') DESC, t.ticket_id DESC
                                    LIMIT
                                        " . $offset . ", 50    
                                    ";
                    }
                    else
                    {

                        if ($user_type == 'zonal') {
                            //zonal manager RSM
                            $rsmData = DB::table('sales_regional_managers')
                                      ->where('zonal_manager_id',$user_id)
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
                                $rsmresult1 = implode(',', $rsmresult);
                            }
                            //zonal manager ASM
                            $asmData = DB::table('sales_area_managers')
                                      ->where('zonal_manager_id',$user_id)
                                      ->select('sales_area_manager_id','sales_area_manager_name')
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
                                $asmresult1 = implode(',', $asmresult);
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
                                $ddresult1 = implode(',', $ddresult);
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
                                $bdoresult1 = implode(',', $bdoresult);
                            }
                        }
                        elseif ($user_type == 'regional') {
                            //regional manager RSM
                            $rsmresult1 = "";
                            //regional manager ASM
                            $asmData = DB::table('sales_area_managers')
                                      ->where('regional_manager_id',$user_id)
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
                                $asmresult1 = implode(',', $asmresult);
                            }
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
                                $ddresult1 = implode(',', $ddresult);
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
                                $bdoresult1 = implode(',', $bdoresult);
                            }
                        }
                        elseif ($user_type == 'area') {
                            $rsmresult1 = "";
                            $asmresult1 = "";
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
                                $ddresult1 = implode(',', $ddresult);
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
                                $bdoresult1 = implode(',', $bdoresult);
                            }
                        }
                        elseif ($user_type == 'sales_regional') {
                            //regional manager RSM
                            $rsmresult1 = "";
                            //regional manager ASM
                            $asmData = DB::table('sales_area_managers')
                                      ->where('sales_regional_manager_id',$user_id)
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
                                $asmresult1 = implode(',', $asmresult);
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
                                $ddresult1 = implode(',', $ddresult);
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
                                $bdoresult1 = implode(',', $bdoresult);
                            }
                        }
                        elseif ($user_type == 'sales_area') {
                            $rsmresult = "";
                            $asmresult = "";
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
                                $ddresult1 = implode(',', $ddresult);
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
                                $bdoresult1 = implode(',', $bdoresult); 
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
                                    //marketing supervisor ASM
                                    $asmData = DB::table('sales_area_managers')
                                              ->whereIn('sales_regional_manager_id',$rsmresult)
                                              ->select('sales_area_manager_id','sales_area_manager_name')
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
                                        $asmresult1 = implode(',', $asmresult); 
                                    }

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
                                        $bdoresult1 = implode(',', $bdoresult);
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
                                            $ddresult1 = implode(',', $ddresult);
                                        }
                                    }

                                    $rsmresult1 = implode(',', $rsmresult);
                                }
                            }
                        }
                        $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.ticket_no as ticket,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%Y-%m-%d %H:%i:%s'), t.created_at ) as datetime,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%M %e %Y'), DATE_FORMAT(t.created_at, '%M %e %Y') ) as datetime_format,
                                        -- IF( t.closing_datetime != '0000-00-00 00:00:00', DATE_FORMAT(t.closing_datetime, '%M %e %Y'), 'NA' ) as close_date,
                                         IF( t.closing_datetime != '0000-00-00 00:00:00', t.closing_datetime, 'NA' ) as close_date,
                                        CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
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
                                        t.validated_farmers,
                                        t.farmer_count,
                                        t.plan_atp_id,
                                        '" . $base_url . "/' as base_url,
                                        t.uploaded_file, 
                                        (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status
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
                                    WHERE t.status != '0'  AND t.ticket_status != '0'";

                                    if (!$request->has('rsm') && !$request->has('asm') && !$request->has('fl_ids') && !$request->has('dd_ids')) {
                                        $query .=" AND t.user_id = '".$user_id."' AND t.user_type = '".$user_type."' ";

                                        if (!empty($bdoresult1)) {
                                           $query .=" OR t.user_id in ('".$bdoresult1."') AND t.user_type = 'bdo'";
                                        }

                                        if (!empty($ddresult1)) {
                                            if (!empty($bdoresult1)) {
                                                $query .=" OR t.assign_to_dd in ('".$ddresult1."') AND t.status != '0'  AND t.ticket_status != '0'";
                                            }
                                            else
                                            {
                                                $query .=" OR t.assign_to_dd in ('".$ddresult1."')";
                                            }
                                        }
                                    }
                                    
                                    if($request->has('rsm') && !$request->has('asm') && !$request->has('fl_ids') && !$request->has('dd_ids'))
                                    {
                                        $rsm=$request->input('rsm');
                                        $query .=" AND t.user_id = '".$rsm."' AND t.user_type = 'sales_regional'";
                                    }

                                    if ($request->has('rsm') && $request->has('asm') && !$request->has('fl_ids') && !$request->has('dd_ids')) {
                                        // $asm=$request->input('asm');
                                        // if ($request->has('rsm')) {
                                        //     $query .=" OR t.user_id = '".$asm."' AND t.user_type = 'sales_area' AND t.status != '0'  AND t.ticket_status != '0'";
                                        // }
                                        // else
                                        // {
                                            $query .=" AND t.user_id = '".$request->has('asm')."' AND t.user_type = 'sales_area'";
                                        //}
                                    }

                                    if ($request->has('rsm') && $request->has('asm') && $request->has('fl_ids') && !$request->has('dd_ids')) {
                                        // $bdo_id=$request->input('fl_ids');
                                        // if ($request->has('asm')) {
                                        //     $query .=" OR t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' AND t.status != '0'  AND t.ticket_status != '0'";
                                        // }
                                        // else
                                        // {
                                            $query .=" AND t.user_id = '".$request->has('fl_ids')."' AND t.user_type = 'bdo'";
                                        //}
                                    }

                                    if ($request->has('rsm') && $request->has('asm') && $request->has('fl_ids') && $request->has('dd_ids')) {
                                        // $dd_id=$request->input('dd_ids');
                                        // if ($request->has('fl_ids')) {
                                        //     $query .=" OR t.user_id = '".$dd_id."' AND t.user_type = 'bdo' AND t.status != '0'  AND t.ticket_status != '0'";
                                        // }
                                        // else
                                        // {
                                            $query .=" AND t.assign_to_dd = '".$request->has('dd_ids')."'";
                                        //}
                                    }

                                    
                                    if($request->has('data_source_id')){
                                        $data_source_id=$request->input('data_source_id');
                                        $query .=" AND t.data_source_id = '".$data_source_id."'";
                                    }

                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query .=" AND t.created_at  Between '".$from_date." 00:00:00' and '".$to_date." 23:59:59' ";
                                    }   

                                    if($request->has('crop')){
                                        $crop=$request->input('crop');
                                        $query .=" AND t.crop_1_id = '".$crop."' OR t.crop_2_id = '".$crop."' OR t.crop_3_id = '".$crop."'";
                                    }

                                    if($request->has('product')){
                                        $product=$request->input('product');
                                        $query .=" AND t.product_1_id = '".$product."' OR t.product_2_id = '".$product."' OR t.product_3_id = '".$product."'";
                                    }


                                    $query .=" GROUP BY  
                                        t.ticket_id
                                    ORDER BY 
                                        t.ticket_id DESC";
                                       // echo $query;die;
                    } 
                    $ticket_history = DB::select(DB::raw($query));
                    $result = array_map(function ($value) {
                        return (array) $value;
                    }, $ticket_history);
                    
                    if ($ticket_history) {
                        foreach ($result as $key => $value) {
                            if(is_null($result[$key]['validated_farmers']) && empty($result[$key]['validated_farmers']))
                            {   
                                $result[$key]['validated_farmers'] = 0;
                            }
                            else
                            {
                                $result[$key]['validated_farmers'] = $result[$key]['validated_farmers'] * $result[$key]['farmer_count'] / 100;
                            }

                            $feedbackdata = DB::table('atpfeedback')
                                            ->where('atpfeedback.ticket_id',$result[$key]['tID'])
                                            ->select('atpfeedback.*')
                                            ->get();
                            if (isset($feedbackdata) && !empty($feedbackdata)) {
                                foreach ($feedbackdata as $datakey => $datavalue) {
                                    if (!empty($feedbackdata[$datakey]->feedbackImage) && isset($feedbackdata[$datakey]->feedbackImage)) {
                                        $feedbackdata[$datakey]->feedbackImage = $base_url. "/assets/atpfeedbackimage/".$feedbackdata[$datakey]->feedbackImage;
                                    }
                                }
                                $result[$key]['atpfeedback'] = $feedbackdata;
                            }
                            else
                            {
                                $result[$key]['atpfeedback'] = [];
                            }
                            
                        }
                        //die;
                        if ($user_type == 'bdo') {
                            //total farmer
                            $TotalFarmer = DB::table('farmers')
                                           ->join('tickets','tickets.ticket_id','=','farmers.ticket_id','left');

                            if($request->has('dd_ids')){
                                //explode(',', $request->input('dd_id'));
                                $DDs=explode(',', $request->input('dd_ids'));
                                $TotalFarmer->whereIn('farmers.farmer_id',$DDs);
                                $TotalFarmer->where('farmers.user_type','dhanuka_doctor');
                            }
                            else
                            {   
                                $TotalFarmer->whereIn('farmers.farmer_id',function($query) use ($ddresult)
                                    {
                                        $query->select('farmer_id')->from('farmers')->whereIn('user_id',$ddresult)->where('user_type','dhanuka_doctor');
                                    });
                                $TotalFarmer->orWhere(['farmers.user_id' => $user_id, 'farmers.user_type' => $user_type]);
                            }
                           
                            if($request->has('from_date') && $request->has('to_date')){
                                $from_date=$request->input('from_date');
                                $to_date=$request->input('to_date');
                                $TotalFarmer->whereBetween('farmers.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }

                            if($request->has('crop')){
                                $crop=$request->input('crop');
                                $TotalFarmer->orWhere('tickets.crop_1_id',$crop);
                                $TotalFarmer->orWhere('tickets.crop_2_id',$crop);
                                $TotalFarmer->orWhere('tickets.crop_3_id',$crop);
                            }

                            if($request->has('product')){
                                $product=$request->input('product');
                                $TotalFarmer->orWhere('tickets.product_1_id',$product);
                                $TotalFarmer->orWhere('tickets.product_2_id',$product);
                                $TotalFarmer->orWhere('tickets.product_3_id',$product);
                            }

                            if($request->has('data_source_id')){
                                $TotalFarmer->where('tickets.data_source_id',$data_source_id);
                            }

                            $TotalFarmer =   $TotalFarmer->select('farmers.farmer_id')->count();
                            //echo $TotalFarmer;die;

                            //total villages
                            $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.village 
                                    FROM tickets t
                                    WHERE t.status != '0'  AND t.ticket_status != '0' ";

                                    if($request->has('dd_ids')){

                                        $DDs=$request->input('dd_ids');
                                        $query .=" AND t.user_id in ('".$DDs."') AND t.user_type = 'dhanuka_doctor'";
                                    }


                                    if($request->has('fl_ids')){
                                        $flIds = $request->input('fl_ids');
                                        $query .=" AND t.user_id in ('".$flIds."') AND t.user_type = 'bdo'";
                                    }
                                    
                                    
                                    if (!$request->has('dd_ids') && !$request->has('fl_ids')) 
                                    {   
                                        $query .=" AND t.user_id='" . $user_id . "'        AND
                                        t.user_type='" . $user_type . "'   OR 
                                        t.ticket_id in  (SELECT ticket_id FROM tickets where user_id in ('".$dd_ids."') AND user_type = 'dhanuka_doctor')";
                                    }
                                    
                                    if($request->has('data_source_id')){
                                        $data_source_id=$request->input('data_source_id');
                                        $query .=" AND t.data_source_id = '".$data_source_id."'";
                                    }

                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query .=" AND t.created_at  Between '".$from_date." 00:00:00' and '".$to_date." 23:59:59' ";
                                    }   

                                    if($request->has('crop')){
                                        $crop=$request->input('crop');
                                        $query .=" AND t.crop_1_id = '".$crop."' OR t.crop_2_id = '".$crop."' OR t.crop_3_id = '".$crop."' AND t.status != '0'  AND t.ticket_status != '0'";
                                    }

                                    if($request->has('product')){
                                        $product=$request->input('product');
                                        $query .=" AND t.product_1_id = '".$product."' OR t.product_2_id = '".$product."' OR t.product_3_id = '".$product."' AND t.status != '0'  AND t.ticket_status != '0'";
                                    }


                                    $query .=" GROUP BY  
                                        t.village
                                    ORDER BY 
                                        FIELD(ticket_status, '1') DESC, t.ticket_id DESC
                                    LIMIT
                                        " . $offset . ", 50    
                                    ";
                            $TotalVillage = count(DB::select(DB::raw($query)));
                        }
                        else
                        {
                            //total farmer
                            $TotalFarmer = DB::table('farmers')
                                           ->join('tickets','tickets.ticket_id','=','farmers.ticket_id','left');
                            if($request->has('fl_ids')){
                                //explode(',', $request->input('dd_id'));
                                //$DDs=explode(',', $request->input('dd_ids'));
                                $bdo_id=$request->input('fl_ids');
                                $TotalFarmer->where('farmers.farmer_id',$bdo_id);
                                $TotalFarmer->where('farmers.user_type','bdo');
                            }
                            else
                            {   
                                if (count($bdoresult) > 0) {
                                    $TotalFarmer->whereIn('farmers.farmer_id',$bdoresult);
                                    $TotalFarmer->where('farmers.user_type','bdo');
                                }
                            }

                            if($request->has('dd_ids')){
                                //explode(',', $request->input('dd_id'));
                                //$DDs=explode(',', $request->input('dd_ids'));
                                if ($request->has('fl_ids')) {
                                    $DDs=$request->input('dd_ids');
                                    $TotalFarmer->orWhere('farmers.farmer_id',$DDs);
                                    $TotalFarmer->where('farmers.user_type','dhanuka_doctor');
                                }
                                else
                                {
                                    $DDs=$request->input('dd_ids');
                                    $TotalFarmer->where('farmers.farmer_id',$DDs);
                                    $TotalFarmer->where('farmers.user_type','dhanuka_doctor');
                                }
                            }
                            else
                            {   
                                if (count($ddresult) > 0) {
                                    $TotalFarmer->whereIn('farmers.farmer_id',$ddresult);
                                    $TotalFarmer->where('farmers.user_type','dhanuka_doctor');
                                }
                            }

                            if($request->has('from_date') && $request->has('to_date')){
                                $from_date=$request->input('from_date');
                                $to_date=$request->input('to_date');
                                $TotalFarmer->whereBetween('farmers.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }

                            if($request->has('crop')){
                                $crop=$request->input('crop');
                                $TotalFarmer->where('tickets.crop_1_id',$crop);
                                $TotalFarmer->orWhere('tickets.crop_2_id',$crop);
                                $TotalFarmer->orWhere('tickets.crop_3_id',$crop);
                            }

                            if($request->has('product')){
                                $product=$request->input('product');
                                $TotalFarmer->where('tickets.product_1_id',$product);
                                $TotalFarmer->orWhere('tickets.product_2_id',$product);
                                $TotalFarmer->orWhere('tickets.product_3_id',$product);
                            }

                            if($request->has('data_source_id')){
                                $TotalFarmer->where('tickets.data_source_id',$data_source_id);
                            }

                            $TotalFarmer =   $TotalFarmer->select('farmers.farmer_id')->count();
                            //echo $TotalFarmer;die;

                            //total villages
                            $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.village
                                    FROM tickets t
                                    WHERE t.status != '0'  AND t.ticket_status != '0' ";

                                    if (!$request->has('rsm') && !$request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) {
                                        if (!empty($bdoresult1)) {
                                           $query .=" AND t.user_id in ('".$bdoresult1."') AND t.user_type = 'bdo'";
                                        }

                                        if (!empty($ddresult1)) {
                                            if (!empty($bdoresult1)) {
                                                $query .=" OR t.user_id in ('".$ddresult1."') AND t.user_type = 'dhanuka_doctor' AND t.status != '0'  AND t.ticket_status != '0'";
                                            }
                                            else
                                            {
                                                $query .=" AND t.user_id in ('".$ddresult1."') AND t.user_type = 'dhanuka_doctor'";
                                            }
                                        }
                                    }
                                    
                                    if($request->has('rsm'))
                                    {
                                        $rsm=$request->input('rsm');
                                        $query .=" AND t.user_id = '".$rsm."' AND t.user_type = 'sales_regional'";
                                    }

                                    if ($request->has('asm')) {
                                        $asm=$request->input('asm');
                                        if ($request->has('rsm')) {
                                            $query .=" OR t.user_id = '".$asm."' AND t.user_type = 'sales_area' AND t.status != '0'  AND t.ticket_status != '0'";
                                        }
                                        else
                                        {
                                            $query .=" AND t.user_id = '".$asm."' AND t.user_type = 'sales_area'";
                                        }
                                    }

                                    if ($request->has('fl_ids')) {
                                        $bdo_id=$request->input('fl_ids');
                                        if ($request->has('asm')) {
                                            $query .=" OR t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' AND t.status != '0'  AND t.ticket_status != '0'";
                                        }
                                        else
                                        {
                                            $query .=" AND t.user_id = '".$bdo_id."' AND t.user_type = 'bdo'";
                                        }
                                    }

                                    if ($request->has('dd_ids')) {
                                        $dd_id=$request->input('dd_ids');
                                        if ($request->has('fl_ids')) {
                                            $query .=" OR t.user_id = '".$dd_id."' AND t.user_type = 'bdo' AND t.status != '0'  AND t.ticket_status != '0'";
                                        }
                                        else
                                        {
                                            $query .=" AND t.user_id = '".$dd_id."' AND t.user_type = 'bdo'";
                                        }
                                    }

                                    
                                    if($request->has('data_source_id')){
                                        $data_source_id=$request->input('data_source_id');
                                        $query .=" AND t.data_source_id = '".$data_source_id."'";
                                    }

                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query .=" AND t.created_at  Between '".$from_date." 00:00:00' and '".$to_date." 23:59:59' ";
                                    }   

                                    if($request->has('crop')){
                                        $crop=$request->input('crop');
                                        $query .=" AND t.crop_1_id = '".$crop."' OR t.crop_2_id = '".$crop."' OR t.crop_3_id = '".$crop."'";
                                    }

                                    if($request->has('product')){
                                        $product=$request->input('product');
                                        $query .=" AND t.product_1_id = '".$product."' OR t.product_2_id = '".$product."' OR t.product_3_id = '".$product."'";
                                    }


                                            $query .=" GROUP BY  
                                                t.village
                                            ORDER BY 
                                                t.ticket_id DESC";
                                                //echo $query;die;
                            $TotalVillage = count(DB::select(DB::raw($query)));
                        }

                        $totaldata = array();
                        $totaldata['TotalFarmer'] = $TotalFarmer;
                        $totaldata['TotalVillage'] = $TotalVillage;
                        $totaldata['TotalActivity'] = count($result);
                        $totaldata['Activities'] = $result;
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Activities Found!',
                                    'data' => $totaldata,
                        ]);
                    } else {
                        return $this->respondNotFound("No Record Found!");
                    }
                }
                else 
                {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
           } 
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function AtpFeedback(Request $request) {
        try {
            $data = $request->all();
            $rules = array(
                'apiToken' => 'required',
                'ticket_id' => 'required'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    
                    // $ticket_check = DB::table('atpfeedback')
                    //                 ->where('ticket_id', $request->input('ticket_id'))
                    //                 ->get();
                    // if (count($ticket_check) > 0) {
                    //     return $this->respond([
                    //                         'status' => 'success',
                    //                         'status_code' => $this->getStatusCode(),
                    //                         'message' => 'Feedback already submitted.'
                    //             ]);
                    // }
                    // else
                    // {
                    if ($user_type == 'bdo') {
                        if($request->file('feedbackImage')){
                            $file=$request->file('feedbackImage');
                            // $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                            
                            // $file->move("assets/atpfeedbackimage",$fileName);
                            //s3Uploads
                            //$fileName = $file->getClientOriginalName();
                            $fileName = time().rand(111,999).".".$file->getClientOriginalExtension();
                            $sTempFileName = 'assets/atpfeedbackimage/' . $fileName;
                            Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            //
                        }
                        $atpfeedback = DB::table('atpfeedback')->insert(
                                        ['ticket_id' => $request->input('ticket_id'),'user_id' => $user_id, 'user_type' => $user_type, 'description' => ($request->input('description'))?$request->input('description'):"", 'feedbackImage' => ($fileName)?$fileName:"", 'creation_at' => date('Y-m-d H:i:s')]
                                      );
                    }
                    else
                    {
                        if($request->file('feedbackImage')){
                            $file=$request->file('feedbackImage');
                            // $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                            
                            // $file->move("assets/atpfeedbackimage",$fileName);
                            //s3Uploads
                            //$fileName = $file->getClientOriginalName();
                            $fileName = time().rand(111,999).".".$file->getClientOriginalExtension();
                            $sTempFileName = 'assets/atpfeedbackimage/' . $fileName;
                            Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            //
                        }
                        $atpfeedback = DB::table('atpfeedback')->insert(
                                        ['ticket_id' => $request->input('ticket_id'),'user_id' => $user_id, 'user_type' => $user_type, 'description' => ($request->input('description'))?$request->input('description'):"", 'feedbackImage' => ($fileName)?$fileName:"", 'creation_at' => date('Y-m-d H:i:s')]
                                      );

                        if ($atpfeedback > 0) 
                        {   
                            $ticketcheckdata = DB::table('tickets')
                                               ->where('ticket_id', $request->input('ticket_id'))
                                               ->select('ticket_no','plan_atp_id')
                                               ->get(); 
                            if (count($ticketcheckdata) > 0) {
                                if ($ticketcheckdata[0]->plan_atp_id != 0) {

                                    $plandata = DB::table('plan_atp')
                                                ->where('plan_atp_id',$ticketcheckdata[0]->plan_atp_id)
                                                ->select('plan_atp_id', DB::raw('DATE_FORMAT(plan_date, "%Y-%m-%d") as Cplan_date'))
                                                ->get();
                                    if (count($plandata) > 0) {
                                        $insert_self_participation_plan = DB::table('self_participation_plans')->insert(
                                        ['plan_atp_id' => $ticketcheckdata[0]->plan_atp_id,'user_id' => $user_id, 'user_type' => $user_type,'plan_date' => $plandata[0]->Cplan_date, 'reason' => ($request->input('description'))?$request->input('description'):"", 'created_at' => date('Y-m-d H:i:s')]
                                      );

                                        $insert_self_participation = DB::table('self_participations')->insert(
                                        ['ticket_id' => $request->input('ticket_id'),'user_id' => $user_id, 'user_type' => $user_type, 'description' => ($request->input('description'))?$request->input('description'):"", 'uploaded_file' => ($fileName)? 'assets/atpfeedbackimage/' . $fileName :"", 'created_at' => date('Y-m-d H:i:s')]
                                      );
                                    }
                                }
                                else
                                {   
                                    $insert_self_participation = DB::table('self_participations')->insert(
                                        ['ticket_id' => $request->input('ticket_id'),'user_id' => $user_id, 'user_type' => $user_type, 'description' => ($request->input('description'))?$request->input('description'):"", 'uploaded_file' => ($fileName)? 'assets/atpfeedbackimage/' . $fileName :"", 'created_at' => date('Y-m-d H:i:s')]
                                      );
                                }
                            } 
                            
                        }
                    }
                        

                        if ($atpfeedback > 0) 
                        {
                            return $this->respond([
                                            'status' => 'success',
                                            'status_code' => $this->getStatusCode(),
                                            'message' => 'Feedback submitted successfully.'
                                ]);
                        }
                        else 
                        {
                            return $this->respondNotFound("Something went wrong. Please Try Again!!");
                        }
                    //}
                    
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }



    public function FarmerActivityList(Request $request) {
        try 
        {
            $rules = array(
                'apiToken' => 'required|string',
                'contact_number' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } 
            else 
            {
                $api_token=$request->input('apiToken');
                if ($request->has('offset')) {
                    $offset=$request->input('offset') * 50;
                }
                else
                {
                    $offset=0;
                }
                
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id        = $user[0]->id;
                    $user_type      = $user[0]->user_type;
                    //$base_url       = url('/');
                    $base_url       = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                    $contact_number = $request->input('contact_number');
                    if ($request->has('farmer_id')) {
                       $farmer_id      = $request->input('farmer_id');
                    }
                    
                    if ($user_type == 'bdo') 
                    {   
                        if ($request->has('demo_request_id') && !empty($request->input('demo_request_id'))) 
                        {
                            $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.ticket_no as ticket,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%Y-%m-%d %H:%i:%s'), t.created_at ) as datetime,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%W, %M %e %Y, %h:%i %p'), DATE_FORMAT(t.created_at, '%W, %M %e %Y, %h:%i %p') ) as datetime_format,
                                        CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
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
                                        '" . $base_url . "/' as base_url,
                                        t.uploaded_file, 
                                        (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status
                                    FROM tickets  t
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
                                    WHERE t.demo_request_id ='".$request->input('demo_request_id')."' GROUP BY  
                                        t.ticket_id
                                    ORDER BY 
                                        FIELD(t.ticket_status, '1') DESC, t.ticket_id DESC
                                    LIMIT
                                        " . $offset . ", 50    
                                    ";
                        }
                        else
                        {
                            $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.ticket_no as ticket,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%Y-%m-%d %H:%i:%s'), t.created_at ) as datetime,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%W, %M %e %Y, %h:%i %p'), DATE_FORMAT(t.created_at, '%W, %M %e %Y, %h:%i %p') ) as datetime_format,
                                        CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
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
                                        '" . $base_url . "/' as base_url,
                                        t.uploaded_file, 
                                        (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status
                                    FROM farmers far
                                    LEFT JOIN tickets  t   ON t.ticket_id=far.ticket_id
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
                                    WHERE far.mobile LIKE '%".$contact_number."%' GROUP BY  
                                        t.ticket_id
                                    ORDER BY 
                                        FIELD(t.ticket_status, '1') DESC, t.ticket_id DESC
                                    LIMIT
                                        " . $offset . ", 50    
                                    ";
                        }
                    } 
                    else
                    {
                        if ($request->has('demo_request_id') && !empty($request->input('demo_request_id'))) 
                        {
                            $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.ticket_no as ticket,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%Y-%m-%d %H:%i:%s'), t.created_at ) as datetime,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%W, %M %e %Y, %h:%i %p'), DATE_FORMAT(t.created_at, '%W, %M %e %Y, %h:%i %p') ) as datetime_format,
                                        CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
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
                                        '" . $base_url . "/' as base_url,
                                        t.uploaded_file, 
                                        (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status
                                    FROM tickets  t
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
                                    WHERE t.demo_request_id ='".$request->input('demo_request_id')."' GROUP BY  
                                        t.ticket_id
                                    ORDER BY 
                                        FIELD(t.ticket_status, '1') DESC, t.ticket_id DESC
                                    LIMIT
                                        " . $offset . ", 50    
                                    ";
                        }
                        else
                        {
                            $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.ticket_no as ticket,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%Y-%m-%d %H:%i:%s'), t.created_at ) as datetime,
                                        IF( pat.plan_date != 'null', DATE_FORMAT(pat.plan_date, '%W, %M %e %Y, %h:%i %p'), DATE_FORMAT(t.created_at, '%W, %M %e %Y, %h:%i %p') ) as datetime_format,
                                        CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
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
                                        '" . $base_url . "/' as base_url,
                                        t.uploaded_file, 
                                        (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status
                                    FROM farmers far
                                    LEFT JOIN tickets  t   ON t.ticket_id=far.ticket_id
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
                                    WHERE far.mobile LIKE '%".$contact_number."%' GROUP BY  
                                        t.ticket_id
                                    ORDER BY 
                                        FIELD(t.ticket_status, '1') DESC, t.ticket_id DESC
                                    LIMIT
                                        " . $offset . ", 50    
                                    ";
                        }
                    }
                    $ticket_history = DB::select(DB::raw($query));
                    $result = array_map(function ($value) {
                        return (array) $value;
                    }, $ticket_history);
                    
                    if ($ticket_history) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Activities Found!',
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
                }
                else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
           } 
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function addMyAtp(Request $request)
    {
        $rules = array(
            'apiToken' => 'required|string',
            'plan_atp_id' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } 
        else 
        {   
            $api_token=$request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id        = $user[0]->id;
                $user_type      = $user[0]->user_type;
                //$base_url       = url('/');
                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                $currentDate = date('Y-m-d\TH:i:s.uP');
                $checkMyAtp = DB::table('my_atp')
                              ->where('plan_atp_id',$request->input('plan_atp_id'))
                              ->where('user_id',$user_id)
                              ->where('user_type',$user_type)
                              ->get();
                if (count($checkMyAtp) > 0) {
                    return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'ATP already added.'
                            ]);
                }
                else
                {
                    $checkPlanDate = DB::table('plan_atp')
                                     ->where('plan_atp_id',$request->input('plan_atp_id'))
                                     ->select('plan_date')
                                     ->get();
                    $currentDatesub = substr($currentDate, 0, 19);
                    $atpPlanDate = substr($checkPlanDate[0]->plan_date,0,19);
                    $datetime1 = date_create($currentDatesub);
                    $datetime2 = date_create($atpPlanDate);
                    $interval = date_diff($datetime1, $datetime2);
                     //print_r($datetime1);
                    // print_r($datetime2);die;
                    $atpPlanDate1 = substr($checkPlanDate[0]->plan_date,0,10);
                    //echo $interval->format('%d');die;
                    //echo $atpPlanDate1;die;
                    //$interval->format('%d') > 0 && 
                    if ($atpPlanDate1 != date('Y-m-d') && $atpPlanDate1 > date('Y-m-d')) 
                    {  
                        $myatp = DB::table('my_atp')->insert(
                                ['plan_atp_id' => $request->input('plan_atp_id'),'user_id' => $user_id, 'user_type' => $user_type, 'status' => '1', 'created_at' => date('Y-m-d H:i:s')]
                                      );
                        if ($myatp > 0) 
                        {
                            return $this->respond([
                                            'status' => 'success',
                                            'status_code' => $this->getStatusCode(),
                                            'message' => 'ATP added successfully.'
                                ]);
                        }
                        else 
                        {
                            return $this->respondNotFound("Something went wrong. Please Try Again!!");
                        } 
                    }
                    else
                    {
                        return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Adding of ATP within 24 hours is not allowed'
                                ]);
                    }
                    //ATP only add before 24 hours.
                }
            }
            else 
            {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function MyAtp(Request $request)
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
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                if ($request->has('rsm') || $request->has('asm') || $request->has('bdo_id') || $request->has('dd_id')) 
                    {
                        

                        if ($request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {  
                            $bdo_id=$request->input('bdo_id');
                            $planAtpData = DB::table('plan_atp')
                                            ->select('plan_atp_id')
                                            ->where('created_by_user_id',$bdo_id)
                                            ->where('user_type','bdo')
                                            ->where('dd_id','0')
                                            ->get();
                            $planAtpIds = array(); 
                            if (isset($planAtpData) && !empty($planAtpData)) 
                            {
                                $planAtpDatass = json_decode(json_encode($planAtpData),true);
                                foreach ($planAtpDatass as $key => $value) { 
                                    if (is_array($value)) 
                                    { 
                                        $planAtpIds = array_merge($planAtpIds, array_flatten($value)); 
                                    } 
                                    else 
                                    { 
                                        $planAtpIds[$key] = $value; 
                                    } 
                                } 
                            }
                        }

                        if (!$request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {
                            $bdo_id=$request->input('bdo_id');
                            $planAtpData = DB::table('plan_atp')
                                            ->select('plan_atp_id')
                                            ->where('created_by_user_id',$bdo_id)
                                            ->where('user_type','bdo')
                                            ->where('dd_id','0')
                                            ->get();
                            $planAtpIds = array(); 
                            if (isset($planAtpData) && !empty($planAtpData)) 
                            {
                                $planAtpDatass = json_decode(json_encode($planAtpData),true);
                                foreach ($planAtpDatass as $key => $value) { 
                                    if (is_array($value)) 
                                    { 
                                        $planAtpIds = array_merge($planAtpIds, array_flatten($value)); 
                                    } 
                                    else 
                                    { 
                                        $planAtpIds[$key] = $value; 
                                    } 
                                } 
                            }
                        }

                        if ($request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {
                            $dd_id=$request->input('dd_id');
                            $planAtpData = DB::table('plan_atp')
                                            ->select('plan_atp_id')
                                            ->where('dd_id',$dd_id)
                                            ->get();
                            $planAtpIds = array(); 
                            if (isset($planAtpData) && !empty($planAtpData)) 
                            {
                                $planAtpDatass = json_decode(json_encode($planAtpData),true);
                                foreach ($planAtpDatass as $key => $value) { 
                                    if (is_array($value)) 
                                    { 
                                        $planAtpIds = array_merge($planAtpIds, array_flatten($value)); 
                                    } 
                                    else 
                                    { 
                                        $planAtpIds[$key] = $value; 
                                    } 
                                } 
                            }
                        } 

                        if (!$request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {
                            $dd_id=$request->input('dd_id');
                            $planAtpData = DB::table('plan_atp')
                                            ->select('plan_atp_id')
                                            ->where('dd_id',$dd_id)
                                            ->get();
                            $planAtpIds = array(); 
                            if (isset($planAtpData) && !empty($planAtpData)) 
                            {
                                $planAtpDatass = json_decode(json_encode($planAtpData),true);
                                foreach ($planAtpDatass as $key => $value) { 
                                    if (is_array($value)) 
                                    { 
                                        $planAtpIds = array_merge($planAtpIds, array_flatten($value)); 
                                    } 
                                    else 
                                    { 
                                        $planAtpIds[$key] = $value; 
                                    } 
                                } 
                            }
                        } 
                    }
                
                //Current Supervisor added Atp list 
                $atp =  DB::table('my_atp')
                        ->join('plan_atp','plan_atp.plan_atp_id','=','my_atp.plan_atp_id')
                        ->join('tehsils','tehsils.tehsil_id','=','plan_atp.tehsil_id','left')
                        ->join('districts','districts.district_id','=','plan_atp.district_id','left')
                        ->join('states','states.state_id','=','plan_atp.state_id','left')
                        ->join('products','products.product_id','=','plan_atp.product_id','left')
                        ->join('crops','crops.crop_id','=','plan_atp.crop_id','left')
                        ->join('tickets','tickets.plan_atp_id','=','plan_atp.plan_atp_id','left')
                        ->join('languages','languages.id','=','plan_atp.language_id','left')
                        ->join('data_sources','data_sources.data_source_id','=','plan_atp.data_source_id','left')
                        ->join('users','users.id','=','plan_atp.created_by_user_id','left');
                        if ($request->has('rsm') || $request->has('asm') || $request->has('bdo_id') || $request->has('dd_id')) 
                        {
                            if ($request->has('rsm') && !$request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                            {
                                $rsm=$request->input('rsm');
                                $atp->where('my_atp.user_id',$rsm);
                                $atp->Where('my_atp.user_type','sales_regional');
                            }

                            if (!$request->has('rsm') && $request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                            {
                                $asm=$request->input('asm');
                                $atp->Where('my_atp.user_id',$asm);
                                $atp->Where('my_atp.user_type','sales_area');
                            }
                            
                            if ($request->has('rsm') && $request->has('asm') && !$request->has('bdo_id') && !$request->has('dd_id')) 
                            {
                                $asm=$request->input('asm');
                                $atp->Where('my_atp.user_id',$asm);
                                $atp->Where('my_atp.user_type','sales_area');
                            }

                            if ($request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {  
                                $bdo_id=$request->input('bdo_id');
                                $atp->where('my_atp.user_id',$user_id);
                                $atp->Where('my_atp.user_type',$user_type);
                                $atp->whereIn('my_atp.plan_atp_id', $planAtpIds);
                            }

                            if (!$request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && !$request->has('dd_id')) {
                                $bdo_id=$request->input('bdo_id');
                                $atp->where('my_atp.user_id',$user_id);
                                $atp->Where('my_atp.user_type',$user_type);
                                $atp->whereIn('my_atp.plan_atp_id', $planAtpIds);
                            }

                            if ($request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {
                                $dd_id=$request->input('dd_id');
                                $atp->where('my_atp.user_id',$user_id);
                                $atp->Where('my_atp.user_type',$user_type);
                                $atp->whereIn('my_atp.plan_atp_id', $planAtpIds);
                            } 

                            if (!$request->has('rsm') && $request->has('asm') && $request->has('bdo_id') && $request->has('dd_id')) {
                                $dd_id=$request->input('dd_id');
                                $atp->where('my_atp.user_id',$user_id);
                                $atp->Where('my_atp.user_type',$user_type);
                                $atp->whereIn('my_atp.plan_atp_id', $planAtpIds);
                            } 
                        }
                        else
                        {
                            $atp->where('my_atp.user_id',$user_id);
                            $atp->Where('my_atp.user_type',$user_type);
                        }
                        // if ($request->has('dd_ids')) {
                        //     $dd_ids=explode(",",$request->input('dd_ids'));
                        //     $atp->whereIn('plan_atp.dd_id',$dd_ids);
                        // }

                        if($request->has('from_date') && $request->has('to_date')){
                            $from_date=$request->input('from_date');
                            $to_date=$request->input('to_date');
                            $atp->whereBetween('plan_atp.plan_date',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                        }

                        if ($request->has('product')) {
                            $product=$request->input('product');
                            $atp->Where('plan_atp.product_id',$product);
                            $atp->orWhere('plan_atp.product_id_2',$product);
                            $atp->orWhere('plan_atp.product_id_3',$product);
                        }

                        if ($request->has('crop')) {
                            $crop=$request->input('crop');
                            $atp->Where('plan_atp.crop_id',$crop);
                            $atp->orWhere('plan_atp.other_crop_1',$crop);
                            $atp->orWhere('plan_atp.other_crop_2',$crop);
                        }
                    $atp = $atp->orderBy('plan_atp.plan_atp_id','DESC')
                            ->select('plan_atp.*','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','tickets.ticket_id','tickets.ticket_no','tickets.latitude','tickets.longitude','languages.language as language_id','data_sources.data_source as data_source_id','users.name as bdo_name')
                            ->get();
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
                                if(count($userData1)>0){
                                    $atp[$key]->bdo_name = $userData1[0]->name;
                                }
                                else{
                                    $atp[$key]->bdo_name = "";
                                }
                                
                            }
                        }
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
                                    ->select('product_id','product')
                                    ->get();

                        $crops =    DB::table('crops')
                                    ->where('crop_id',$atp[$key]->crop_id)
                                    ->select('crop_id','crop')
                                    ->get();
                        $atp[$key]->product = $products;
                        $atp[$key]->crop = $crops;

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
                                'message' => 'My ATP list.',
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

    public function SupervisorActivatedTickets(Request $request)
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
                                max(afb.atpfeedback_id) as atpfeedback_id,
                                (select feedbackImage from atpfeedback where atpfeedback_id = max(afb.atpfeedback_id)) as feedbackImage,
                                (select description from atpfeedback where atpfeedback_id = max(afb.atpfeedback_id)) as description,
                                '" . $base_url . "/' as base_url,
                                t.uploaded_file, 
                                (CASE WHEN t.ticket_status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status,
                                (CASE WHEN matp.plan_atp_id != 'null' THEN 'my plan' ELSE '' END) as my_plan
                            FROM tickets t
                            LEFT JOIN atpfeedback  afb   ON t.ticket_id=afb.ticket_id
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
                    
                    if ($ticket_history) {
                        foreach ($result as $key => $value) { 
                            if (empty($result[$key]['longitude']) || is_null($result[$key]['longitude']) ) {
                                $result[$key]['longitude'] = 0;
                            }

                            if (empty($result[$key]['latitude']) || is_null($result[$key]['latitude']) ) {
                                $result[$key]['latitude'] = 0;
                            }

                            if (!empty($result[$key]['feedbackImage'])) {
                                $result[$key]['feedbackImage'] = $base_url . '/assets/atpfeedbackimage/' . $result[$key]['feedbackImage'];
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

    public function departmentlist(Request $request)
    {   
        $data = $request->json()->all();
        $rules = array(
            'apiToken' => 'required|string'

        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                if ($request->has('type') && $request->has('id')) {
                    if ($request->input('type') == 'sales_regional') {
                        //Sales area manager 
                        $asmData = DB::table('sales_area_managers')
                                  ->where('sales_regional_manager_id',$request->input('id'))
                                  ->where('status','1')
                                  ->select('sales_area_manager_id as id','sales_area_manager_name as name')
                                  ->get();
                        if (count($asmData) > 0) {
                            foreach ($asmData as $key => $value) {
                                $asmData[$key]->type = 'sales_area';
                            }
                            $result = array('RSM_list' => [], 'ASM_list' => $asmData, 'BDO_list' => [], 'DD_list' => []);
                        }
                        else
                        {
                            $result = array('RSM_list' => [], 'ASM_list' => [], 'BDO_list' => [], 'DD_list' => []);
                        }
                    }
                    elseif ($request->input('type') == 'sales_area') {
                        //Sales area manager bdo
                        $bdoData = DB::table('users')
                                  ->where('sales_area_manager_id',$request->input('id'))
                                  ->where('status','1')
                                  ->select('id','name')
                                  ->get();
                        if (count($bdoData) > 0) {
                            foreach ($bdoData as $key => $value) {
                                $bdoData[$key]->type = 'bdo';
                            }
                            $result = array('RSM_list' => [], 'ASM_list' => [], 'BDO_list' => $bdoData, 'DD_list' => []);
                        }
                        else
                        {
                            $result = array('RSM_list' => [], 'ASM_list' => [], 'BDO_list' => [], 'DD_list' => []);
                        }
                    }
                    elseif ($request->input('type') == 'bdo') 
                    {
                        //bdo dd
                        $checkbdotype = DB::table('users')
                                        ->where('id',$request->input('id'))
                                        ->where('status','1')
                                        ->select('designation','id')
                                        ->get();
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
                            array_push($bdo_ids, $request->input('id'));
                            //print_r($bdo_ids);die;
                            $ddData = DB::table('dhanuka_doctor')
                                  ->whereIn('bdo_id',$bdo_ids)
                                  ->where('status','1')
                                  ->select('dd_id as id','name')
                                  ->get();
                        }
                        else
                        {
                            $ddData = DB::table('dhanuka_doctor')
                                  ->where('bdo_id',$request->input('id'))
                                  ->where('status','1')
                                  ->select('dd_id as id','name')
                                  ->get();
                        }

                        if (count($ddData) > 0) {
                            foreach ($ddData as $key => $value) {
                                $ddData[$key]->type = 'dhanuka_doctor';
                            }
                            $result = array('RSM_list' => [], 'ASM_list' => [], 'BDO_list' => [], 'DD_list' => $ddData);
                        }
                        else
                        {
                            $result = array('RSM_list' => [], 'ASM_list' => [], 'BDO_list' => [], 'DD_list' => []);
                        }
                    }
                }
                else
                {   
                    if ($user_type == 'zonal') {
                        $rsmData = DB::table('sales_regional_managers')->select('sales_regional_manager_id as id','sales_regional_manager_name as name')
                            ->where('zonal_manager_id',$user_id)
                            ->where('status','1')
                                  ->get();
                        if (count($rsmData) > 0) {
                            foreach ($rsmData as $key => $value) {
                                $rsmData[$key]->type = 'sales_regional';
                            }
                            $result = array('RSM_list' => $rsmData, 'ASM_list' => [], 'BDO_list' => [], 'DD_list' => []);
                        }
                        else
                        {
                            $result = array('RSM_list' => [], 'ASM_list' => [], 'BDO_list' => [], 'DD_list' => []);
                        }
                    }
                    else
                    {
                        $rsmData = DB::table('sales_regional_managers')->select('sales_regional_manager_id as id','sales_regional_manager_name as name')
                            ->where('status','1')
                                  ->get();
                        if (count($rsmData) > 0) {
                            foreach ($rsmData as $key => $value) {
                                $rsmData[$key]->type = 'sales_regional';
                            }
                            $result = array('RSM_list' => $rsmData, 'ASM_list' => [], 'BDO_list' => [], 'DD_list' => []);
                        }
                        else
                        {
                            $result = array('RSM_list' => [], 'ASM_list' => [], 'BDO_list' => [], 'DD_list' => []);
                        }
                    }
                }

                return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Department list!',
                                    'data' => $result,
                        ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        }
    }

    public function supervisorDD(Request $request)
    {
        $data = $request->json()->all();
        $rules = array(
            'apiToken' => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                if ($user_type == 'marketing_supervisor') 
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
                            //marketing supervisor dd
                            $ddData = DB::table('dhanuka_doctor')
                                      ->whereIn('sales_regional_manager_id',$rsmresult)
                                      ->select('dd_id','name','ECN')
                                      ->get();
                            
                            
                        }
                    }
                    $result = array('DD_list' => $ddData);
                }
                else
                {
                    $result = array('DD_list' => []);
                }
                return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Supervisor DD list!',
                                    'data' => $result,
                        ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        }
    
    }

    public function addHighlight(Request $request){

        try {
            $data = $request->all();
            $rules = array(
                'apiToken' => 'required',
                'ticket_id' => 'required',
                'description' => 'required'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    
                    if($request->file('highlightImage')){
                        $file=$request->file('highlightImage');
                        
                        
                        // $file->move("assets/highlightImage",$fileName1);
                        //s3Uploads
                        $fileName1=time().rand(111,999).".".$file->getClientOriginalExtension();
                        $sTempFileName = 'assets/highlightImage/' . $fileName1;
                        Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                        //
                    }
                    $ticketHighlights = DB::table('ticket_highlights')->insert(
                                    ['ticket_id' => $request->input('ticket_id'),'user_id' => $user_id, 'user_type' => $user_type, 'description' => ($request->input('description'))?$request->input('description'):"", 'highlightImage' => ($fileName1)?$fileName1:"", 'creation_at' => date('Y-m-d H:i:s')]
                                  );

                    if ($ticketHighlights > 0) 
                    {
                        return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Highlight submitted successfully.'
                            ]);
                    }
                    else 
                    {
                        return $this->respondNotFound("Something went wrong. Please Try Again!!");
                    }

                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    
    }

    public function highlightList(Request $request){
        try {
            $data = $request->all();
            $rules = array(
                'apiToken' => 'required',
                'ticket_id' => 'required',
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    //$base_url = url('/');
                    $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    
                    // LEFT JOIN   zonal_managers          zm  ON ut.user_id=zm.zonal_manager_id AND ut.user_type='zonal'
                    //                     LEFT JOIN   regional_managers       rm  ON ut.user_id=rm.regional_manager_id AND ut.user_type='regional'
                    //                     LEFT JOIN   area_managers           am  ON ut.user_id=am.area_manager_id AND ut.user_type='area'
                    //                     LEFT JOIN   sales_regional_managers srm ON ut.user_id=srm.sales_regional_manager_id AND ut.user_type='sales_regional'
                    //                     LEFT JOIN   sales_area_managers     sam ON ut.user_id=sam.sales_area_manager_id AND ut.user_type='sales_area'
                    //                     LEFT JOIN   dhanuka_doctor dd ON ut.user_id=dd.dd_id AND ut.user_type='dhanuka_doctor'

                    $query = "select th.*,
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
                                        where th.ticket_id = ".$request->input('ticket_id')."
                                     ";
                    $highlightdata    = collect(DB::select($query));
                    // $highlightdata = DB::table('ticket_highlights')
                    //                 ->where('ticket_highlights.ticket_id',$request->input('ticket_id'))
                    //                 ->select('ticket_highlights.*')
                    //                 ->get();
                    if (isset($highlightdata) && !empty($highlightdata)) {
                        foreach ($highlightdata as $datakey => $datavalue) {
                            if (!empty($highlightdata[$datakey]->highlightImage) && isset($highlightdata[$datakey]->highlightImage)) {
                                $highlightdata[$datakey]->highlightImage = $base_url. "/assets/highlightImage/".$highlightdata[$datakey]->highlightImage;
                            }
                        }
                        return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Highlight submitted successfully.',
                                        'result' => $highlightdata
                            ]);
                    }
                    else
                    {   
                        return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'No record found!',
                                        'result' => []
                            ]);
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    

    public function dataSourceList($api_token = '')
    {
        try {
            $curTime = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "v2/dataSourceList",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                $data_source_list = DataSource::where('status', '1')->select('data_source_id as dsID', 'data_source as Item')->whereRaw("find_in_set('".$user_type."',user_type)")->orderBy('data_source', 'ASC')->get()->toArray();
                
                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                if (count($data_source_list) > 0) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Data source list!',
                                'data' => $data_source_list
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No data source found!',
                                'data' => []
                    ]);
                }
                
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    
    }

    public function supervisorReport(Request $request){
        try {
            $data = $request->all();
            $rules = array(
                'apiToken' => 'required'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    //$base_url = url('/');
                    $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    if ($user_type == 'zonal') {
                        //zonal manager RSM
                        $rsmData = DB::table('sales_regional_managers')
                                  ->where('zonal_manager_id',$user_id)
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
                        //zonal manager ASM
                        $asmData = DB::table('sales_area_managers')
                                  ->where('zonal_manager_id',$user_id)
                                  ->select('sales_area_manager_id','sales_area_manager_name')
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
                    }
                    elseif ($user_type == 'regional') {
                        //regional manager RSM
                        $rsmresult = [];
                        //regional manager ASM
                        $asmData = DB::table('sales_area_managers')
                                  ->where('regional_manager_id',$user_id)
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
                    }
                    elseif ($user_type == 'area') {
                        $rsmresult = [];
                        $asmresult = [];
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
                        //regional manager RSM
                        $rsmresult = [];
                        //regional manager ASM
                        $asmData = DB::table('sales_area_managers')
                                  ->where('sales_regional_manager_id',$user_id)
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
                    }
                    elseif ($user_type == 'sales_area') {
                        $rsmresult = [];
                        $asmresult = [];
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
                    }
                    elseif ($user_type == 'marketing_supervisor') {
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
                                //marketing supervisor ASM
                                $asmData = DB::table('sales_area_managers')
                                          ->whereIn('sales_regional_manager_id',$rsmresult)
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
                    $data = array();

                    //====demo request
                    $checkdemoquery = "select tickets.demo_request_id from tickets join farmers on farmers.ticket_id = tickets.ticket_id where tickets.demo_request_id !=0 group by tickets.demo_request_id limit 10";
                    $demoids = DB::select(DB::raw($checkdemoquery));
                    $demoIdsData=array();
                    if (isset($demoids) && !empty($demoids)) 
                    {
                        $demoDatass = json_decode(json_encode($demoids),true);
                        
                        foreach ($demoDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $demoIdsData = array_merge($demoIdsData, array_flatten($value)); 
                            } 
                            else 
                            { 
                                $demoIdsData[$key] = $value; 
                            } 
                        } 
                    }
                  
                    if(count($demoIdsData) > 0)
                    {  
                        $demoquery = DB::table('demo_request')
                                    ->join('products','products.product_id',' = ', 'demo_request.product_id','left')
                                    ->join('crops','crops.crop_id',' = ', 'demo_request.crop_id','left')
                                    ->join('villages','villages.village_id','=','demo_request.village_id','left')
                                    ->join('tehsils','tehsils.tehsil_id','=','demo_request.tehsil_id','left')
                                    ->join('districts','districts.district_id','=','demo_request.district_id','left')
                                    ->join('states','states.state_id','=','demo_request.state_id','left')
                                    ->whereIn('demo_request.demo_request_id',$demoIdsData);
                                    if ($request->has('dd_id')) {
                                        $dd_id=$request->input('dd_id');
                                        $demoquery->where('demo_request.dd_id',$dd_id);
                                    }
                                    else
                                    {
                                        if (count($ddresult)>0) {
                                          $demoquery->whereIn('dd_id',$ddresult);
                                              
                                        }
                                        // else
                                        // {
                                        //    $demoquery->where('1','!=','1'); 
                                        // }
                                    }

                                    if ($request->has('crop')) {
                                        $crop=$request->input('crop');
                                        $demoquery->Where('demo_request.crop_id',$crop);
                                    }

                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $demoquery->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                    }
                                    $demoquery = $demoquery->select('demo_request.*','villages.village','states.state','tehsils.tehsil','districts.district','products.product','crops.crop')->get();
                        $demolist = $demoquery;            
                    }
                    else
                    {
                        $demolist = [];
                    }
                    
                    if (count($demolist) > 0) {
                       //$base_url = url('/');
                       $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                        foreach ($demolist as $key => $value) {
                           if (!empty($demolist[$key]->demoImage)) {
                              $demolist[$key]->demoImage = $base_url . '/assets/demoimage/' . $demolist[$key]->demoImage;
                           }

                           if (!empty($demolist[$key]->farmer_image)) {
                              $demolist[$key]->farmer_image = $base_url . '/assets/farmer/' . $demolist[$key]->farmer_image;
                           }
                        }
                    }
                    
                    //====cutomer care query
                    if (count($bdoresult) > 0) {
                        $bdo_ids = implode(',', $bdoresult);
                    }

                    if (count($rsmresult) > 0) {
                        $rsm_ids = implode(',', $rsmresult);
                    }

                    if (count($asmresult) > 0) {
                        $asm_ids = implode(',', $asmresult);
                    }

                    if (count($ddresult) > 0) {
                        $dd_ids = implode(',', $ddresult);
                    }
                    $customer_care_query = " SELECT                                                         
                                                COUNT(CASE WHEN status=1 THEN 1 ELSE NULL END) as 'active',
                                                COUNT(CASE WHEN status=2 THEN 1 ELSE NULL END) as 'follow_up',
                                                COUNT(CASE WHEN status=3 THEN 1 ELSE NULL END) as 'closed'
                                            FROM    customer_queries
                                            WHERE   ";
                                            if ($request->has('bdo_id')) {
                                                $bdo_id=$request->input('bdo_id');
                                                $customer_care_query .="bdo_id IN (" . $bdo_id . ") 
                                                    LIMIT 0,1 ";
                                            }
                                            else
                                            {
                                                if (count($bdoresult) > 0) {
                                                   $customer_care_query .="bdo_id IN (" . $bdo_ids . ") 
                                                    LIMIT 0,1 ";
                                                }
                                                else
                                                {
                                                    $customer_care_query .="1 != 1";
                                                }
                                            }

                                            // if ($request->has('crop')) {
                                            //     $crop=$request->input('crop');
                                            //     $demoquery->Where('demo_request.crop_id',$crop);
                                            // }

                                            if($request->has('from_date') && $request->has('to_date')){
                                                $from_date=$request->input('from_date');
                                                $to_date=$request->input('to_date');
                                                $customer_care_query.=" AND created_at BETWEEN ".$from_date." 00:00:00 AND ".$to_date." 23:59:59";
                                            }
                                            
                    $customer_care_count = DB::select(DB::raw($customer_care_query));

                    $active_count = $customer_care_count[0]->active;
                    $follow_up_count = $customer_care_count[0]->follow_up;
                    $closed_count = $customer_care_count[0]->closed;

                    //testimonial ratings
                    $testimonialRates = "   SELECT                                                         
                                                COUNT(CASE WHEN testimonial_rating.rating=1 THEN 1 ELSE NULL END) as 'rating1',
                                                COUNT(CASE WHEN testimonial_rating.rating=2 THEN 1 ELSE NULL END) as 'rating2',
                                                COUNT(CASE WHEN testimonial_rating.rating=3 THEN 1 ELSE NULL END) as 'rating3',
                                                COUNT(CASE WHEN testimonial_rating.rating=4 THEN 1 ELSE NULL END) as 'rating4',
                                                COUNT(CASE WHEN testimonial_rating.rating=5 THEN 1 ELSE NULL END) as 'rating5'
                                            FROM    testimonial_rating
                                            inner join testimonials on `testimonials`.`self_participation_id` = `testimonial_rating`.`testimonial_id`
                                            WHERE   `testimonial_rating`.`supervisor_id` = '".$user_id."' AND `testimonial_rating`.`supervisor_type` = '".$user_type."'";
                                            if (!$request->has('rsm') || !$request->has('asm') || !$request->has('bdo_id') || !$request->has('dd_id')) 
                                            {
                                                if (count($rsmresult) > 0) {
                                                   $testimonialRates .=" OR `testimonial_rating`.`supervisor_id` IN (" . $rsm_ids . ") AND `testimonial_rating`.`supervisor_type` = 'sales_regional'";
                                                }

                                                if (count($asmresult) > 0) {
                                                   $testimonialRates .=" OR `testimonial_rating`.`supervisor_id` IN (" . $asm_ids . ") AND `testimonial_rating`.`supervisor_type` = 'sales_area'";
                                                }

                                                if (count($bdoresult) > 0) {
                                                   $testimonialRates .=" OR `testimonial_rating`.`supervisor_id` IN (" . $bdo_ids . ") AND `testimonial_rating`.`supervisor_type` = 'bdo'";
                                                }
                                            }
                                            else
                                            {
                                                if ($request->has('rsm')) 
                                                {   
                                                    $rsm=$request->input('rsm');
                                                    $testimonialRates .=" OR `testimonial_rating`.`supervisor_id` =" . $rsm . " AND `testimonial_rating`.`supervisor_type` = 'sales_regional'";
                                                }

                                                if ($request->has('asm')) 
                                                {
                                                    $asm=$request->input('asm');
                                                    $testimonialRates .=" OR `testimonial_rating`.`supervisor_id` = " . $asm . " AND `testimonial_rating`.`supervisor_type` = 'sales_area'";
                                                }

                                                if ($request->has('bdo_id')) 
                                                {
                                                    $bdo_id=$request->input('bdo_id');
                                                    $testimonialRates .=" OR `testimonial_rating`.`supervisor_id` = " . $bdo_id . " AND `testimonial_rating`.`supervisor_type` = 'bdo'";
                                                }

                                                // if ($request->has('dd_id')) 
                                                // {
                                                //     $dd_id=$request->input('dd_id');
                                                //     $testimonialRates .=" OR `testimonials`.`user_id` = " . $bdo_id . " AND `testimonials`.`user_type` = 'dhanuka_doctor'";
                                                // }
                                            }

                                            if ($request->has('crop')) 
                                            {
                                                $crop=$request->input('crop');
                                                $testimonialRates .=" AND `testimonials`.`crop_id` = '".$crop."'";
                                            }

                                            if($request->has('from_date') && $request->has('to_date')){
                                                $from_date=$request->input('from_date');
                                                $to_date=$request->input('to_date');
                                                $testimonialRates.=" AND testimonial_rating.created_at BETWEEN ".$from_date." 00:00:00 AND ".$to_date." 23:59:59";
                                            }
                                            $testimonialRates .=" LIMIT 0,1";
                                   
                    $testimonialRates = DB::select(DB::raw($testimonialRates));
                    
                    //activity count
                    $query = "
                                    SELECT 
                                        t.ticket_id as tID 
                                    FROM tickets t
                                    WHERE t.status != '0'  AND t.ticket_status != '0' ";
                                    if($request->has('rsm'))
                                    {
                                        $rsm=$request->input('rsm');
                                        $query.=" AND t.user_id = '".$rsm."' AND t.user_type = 'sales_regional'";
                                    }

                                    if ($request->has('asm')) {
                                        $asm=$request->input('asm');
                                        if ($request->has('rsm')) {
                                            $query.=" OR t.user_id = '".$asm."' AND t.user_type = 'sales_area' ";
                                        }
                                        else
                                        {
                                            $query.=" AND t.user_id = '".$asm."' AND t.user_type = 'sales_area' ";
                                        }
                                    }

                                    if ($request->has('bdo_id')) {
                                        $bdo_id=$request->input('bdo_id');
                                        if ($request->has('asm')) {
                                            $query.=" OR t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' ";
                                        }
                                        else
                                        {
                                            $query.=" AND t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' ";
                                        }
                                        
                                    }

                                    if ($request->has('dd_id')) {
                                        $dd_id=$request->input('dd_id');
                                        if ($request->has('bdo_id')) {
                                            $query.=" OR t.user_id = '".$dd_id."' AND t.user_type = 'dhanuka_doctor' ";
                                        }
                                        else
                                        {
                                            $query.=" AND t.user_id = '".$dd_id."' AND t.user_type = 'dhanuka_doctor' ";
                                        }
                                    }

                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query.=" AND t.created_at BETWEEN '".$from_date."' 00:00:00 AND ".$to_date." 23:59:59";
                                    }

                                    if ($request->has('crop')) {
                                        $crop=$request->input('crop');
                                        $query .=" AND (`t`.`crop_1_id` = '".$crop."' OR `t`.`crop_2_id` = '".$crop."' OR `t`.`crop_3_id` = '".$crop."')";
                                    }


                                    $query .=" GROUP BY  
                                        t.ticket_id
                                    ";
                    $ticket_history = DB::select(DB::raw($query)); 
                    $activity = array_map(function ($value) {
                        return (array) $value;
                    }, $ticket_history);
                    $totalActivity = count($activity);

                    //village visit
                    $query = "
                                    SELECT 
                                        t.ticket_id as tID 
                                    FROM tickets t
                                    WHERE t.status != '0'  AND t.ticket_status != '0' AND t.data_source_id = 2 ";
                                    if (!$request->has('rsm') || !$request->has('asm') || !$request->has('bdo_id') || !$request->has('dd_id')) 
                                    {
                                        if (count($rsmresult) > 0) {
                                           $query .=" AND `t`.`user_id` IN (" . $rsm_ids . ") AND `t`.`user_type` = 'sales_regional'";
                                        }

                                        if (count($asmresult) > 0) {
                                           $query .=" OR `t`.`user_id` IN (" . $asm_ids . ") AND `t`.`user_type` = 'sales_area'";
                                        }

                                        if (count($bdoresult) > 0) {
                                           $query .=" OR `t`.`user_id` IN (" . $bdo_ids . ") AND `t`.`user_type` = 'bdo'";
                                        }

                                        if (count($ddresult) > 0) {
                                           $query .=" OR `t`.`user_id` IN (" . $dd_ids . ") AND `t`.`user_type` = 'bdo'";
                                        }
                                    }
                                    else
                                    {
                                        if($request->has('rsm'))
                                        {
                                            $rsm=$request->input('rsm');
                                            $query.=" AND t.user_id = '".$rsm."' AND t.user_type = 'sales_regional'";
                                        }

                                        if ($request->has('asm')) {
                                            $asm=$request->input('asm');
                                            if ($request->has('rsm')) {
                                                $query.=" OR t.user_id = '".$asm."' AND t.user_type = 'sales_area' ";
                                            }
                                            else
                                            {
                                                $query.=" AND t.user_id = '".$asm."' AND t.user_type = 'sales_area' ";
                                            }
                                        }

                                        if ($request->has('bdo_id')) {
                                            $bdo_id=$request->input('bdo_id');
                                            if ($request->has('asm')) {
                                                $query.=" OR t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' ";
                                            }
                                            else
                                            {
                                                $query.=" AND t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' ";
                                            }
                                            
                                        }

                                        if ($request->has('dd_id')) {
                                            $dd_id=$request->input('dd_id');
                                            if ($request->has('bdo_id')) {
                                                $query.=" OR t.user_id = '".$dd_id."' AND t.user_type = 'dhanuka_doctor' ";
                                            }
                                            else
                                            {
                                                $query.=" AND t.user_id = '".$dd_id."' AND t.user_type = 'dhanuka_doctor' ";
                                            }
                                        }
                                    }
                                    

                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query.=" AND t.created_at BETWEEN '".$from_date."' 00:00:00 AND ".$to_date." 23:59:59";
                                    }

                                    if ($request->has('crop')) {
                                        $crop=$request->input('crop');
                                        $query .=" AND (`t`.`crop_1_id` = '".$crop."' OR `t`.`crop_2_id` = '".$crop."' OR `t`.`crop_3_id` = '".$crop."')";
                                    }


                                    $query .=" GROUP BY  
                                        t.ticket_id
                                    ";
                    $ticket_village = DB::select(DB::raw($query)); 
                    $villageMeeting = array_map(function ($value) {
                        return (array) $value;
                    }, $ticket_village);
                    $totalVillageMeeting = count($villageMeeting);

                    //field day
                    $query = "
                                    SELECT 
                                        t.ticket_id as tID 
                                    FROM tickets t
                                    WHERE t.status != '0'  AND t.ticket_status != '0' AND t.data_source_id = 1 ";
                                    if (!$request->has('rsm') || !$request->has('asm') || !$request->has('bdo_id') || !$request->has('dd_id')) 
                                    {
                                        if (count($rsmresult) > 0) {
                                           $query .=" AND `t`.`user_id` IN (" . $rsm_ids . ") AND `t`.`user_type` = 'sales_regional'";
                                        }

                                        if (count($asmresult) > 0) {
                                           $query .=" OR `t`.`user_id` IN (" . $asm_ids . ") AND `t`.`user_type` = 'sales_area'";
                                        }

                                        if (count($bdoresult) > 0) {
                                           $query .=" OR `t`.`user_id` IN (" . $bdo_ids . ") AND `t`.`user_type` = 'bdo'";
                                        }

                                        if (count($ddresult) > 0) {
                                           $query .=" OR `t`.`user_id` IN (" . $dd_ids . ") AND `t`.`user_type` = 'bdo'";
                                        }
                                    }
                                    else
                                    {
                                        if($request->has('rsm'))
                                        {
                                            $rsm=$request->input('rsm');
                                            $query.=" AND t.user_id = '".$rsm."' AND t.user_type = 'sales_regional'";
                                        }

                                        if ($request->has('asm')) {
                                            $asm=$request->input('asm');
                                            if ($request->has('rsm')) {
                                                $query.=" OR t.user_id = '".$asm."' AND t.user_type = 'sales_area' ";
                                            }
                                            else
                                            {
                                                $query.=" AND t.user_id = '".$asm."' AND t.user_type = 'sales_area' ";
                                            }
                                        }

                                        if ($request->has('bdo_id')) {
                                            $bdo_id=$request->input('bdo_id');
                                            if ($request->has('asm')) {
                                                $query.=" OR t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' ";
                                            }
                                            else
                                            {
                                                $query.=" AND t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' ";
                                            }
                                            
                                        }

                                        if ($request->has('dd_id')) {
                                            $dd_id=$request->input('dd_id');
                                            if ($request->has('bdo_id')) {
                                                $query.=" OR t.user_id = '".$dd_id."' AND t.user_type = 'dhanuka_doctor' ";
                                            }
                                            else
                                            {
                                                $query.=" AND t.user_id = '".$dd_id."' AND t.user_type = 'dhanuka_doctor' ";
                                            }
                                        }
                                    }
                                    

                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query.=" AND t.created_at BETWEEN '".$from_date."' 00:00:00 AND ".$to_date." 23:59:59";
                                    }

                                    if ($request->has('crop')) {
                                        $crop=$request->input('crop');
                                        $query .=" AND (`t`.`crop_1_id` = '".$crop."' OR `t`.`crop_2_id` = '".$crop."' OR `t`.`crop_3_id` = '".$crop."')";
                                    }
                                    $query .=" GROUP BY  
                                        t.ticket_id
                                    ";
                    $ticket_fielday = DB::select(DB::raw($query)); 
                    $fieldDay = array_map(function ($value) {
                        return (array) $value;
                    }, $ticket_fielday);
                    $totalFieldDay = count($fieldDay);

                    //total activity BD_FL
                    if (count($bdoresult) > 0) {
                        $bd_fl = DB::table('users')
                                 ->whereIn('id',$bdoresult)
                                 ->where('designation','BD-FL')
                                 ->where('status','1')
                                 ->select('users.id')
                                 ->get();
                        
                        $bd_flresult = array(); 
                        if (isset($bd_fl) && !empty($bd_fl)) 
                        {
                            $bd_flDatass = json_decode(json_encode($bd_fl),true);
                            foreach ($bd_flDatass as $key => $value) { 
                                if (is_array($value)) 
                                { 
                                    $bd_flresult = array_merge($bd_flresult, array_flatten($value)); 
                                } 
                                else 
                                { 
                                    $bd_flresult[$key] = $value; 
                                } 
                            }
                            
                            $query = "
                                        SELECT 
                                            t.ticket_id as tID 
                                        FROM tickets t
                                        WHERE t.status != '0'  AND t.ticket_status != '0' ";
                                        
                                        if ($request->has('bdo_id')) {
                                            $bdo_id=$request->input('bdo_id');
                                            $query.=" AND t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' ";
                                        }
                                        else
                                        {
                                            if (count($bd_flresult) > 0) {
                                                $bdo_ids = implode(',', $bd_flresult);
                                                $query.=" AND t.user_id in (" . $bdo_ids . ") AND t.user_type = 'bdo' ";
                                            }
                                            else
                                            {
                                                $query.=" 1 != 1";
                                            }
                                        }
                                        
                                        if($request->has('from_date') && $request->has('to_date')){
                                            $from_date=$request->input('from_date');
                                            $to_date=$request->input('to_date');
                                            $query.=" AND t.created_at BETWEEN '".$from_date."' 00:00:00 AND ".$to_date." 23:59:59";
                                        }

                                        if ($request->has('crop')) {
                                            $crop=$request->input('crop');
                                            $query .=" AND (`t`.`crop_1_id` = '".$crop."' OR `t`.`crop_2_id` = '".$crop."' OR `t`.`crop_3_id` = '".$crop."')";
                                        }
                                        $query .=" GROUP BY  
                                            t.ticket_id
                                        ";
                            $ticket_bdfl = DB::select(DB::raw($query)); 
                            $bd_fl = array_map(function ($value) {
                                return (array) $value;
                            }, $ticket_bdfl);

                            $bd_flticketcount = count($bd_fl);
                            $bd_flresultcount = count($bd_flresult);
                            if ($bd_flticketcount != 0) {
                                $totalBd_flActivity = round($bd_flticketcount / $bd_flresultcount);
                            }
                            else
                            {
                                $totalBd_flActivity = 0;
                            }
                        }
                        else
                        {
                            $totalBd_flActivity = 0;
                        }
                    }

                    //total activity S_FL
                    if (count($bdoresult) > 0) {
                        $bd_fl = DB::table('users')
                                 ->whereIn('id',$bdoresult)
                                 ->where('designation','S-FL')
                                 ->where('status','1')
                                 ->select('users.id')
                                 ->get();
                        
                        $s_flresult = array(); 
                        if (isset($s_fl) && !empty($s_fl)) 
                        {
                            $s_flDatass = json_decode(json_encode($s_fl),true);
                            foreach ($s_flDatass as $key => $value) { 
                                if (is_array($value)) 
                                { 
                                    $s_flresult = array_merge($s_flresult, array_flatten($value)); 
                                } 
                                else 
                                { 
                                    $s_flresult[$key] = $value; 
                                } 
                            }
                            
                            $query = "
                                        SELECT 
                                            t.ticket_id as tID 
                                        FROM tickets t
                                        WHERE t.status != '0'  AND t.ticket_status != '0' ";
                                        
                                        if ($request->has('bdo_id')) {
                                            $bdo_id=$request->input('bdo_id');
                                            $query.=" AND t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' ";
                                        }
                                        else
                                        {
                                            if (count($s_flresult) > 0) {
                                                $bdo_ids = implode(',', $s_flresult);
                                                $query.=" AND t.user_id in (" . $bdo_ids . ") AND t.user_type = 'bdo' ";
                                            }
                                            else
                                            {
                                                $query.=" 1 != 1";
                                            }
                                        }
                                        
                                        if($request->has('from_date') && $request->has('to_date')){
                                            $from_date=$request->input('from_date');
                                            $to_date=$request->input('to_date');
                                            $query.=" AND t.created_at BETWEEN '".$from_date."' 00:00:00 AND ".$to_date." 23:59:59";
                                        }

                                        if ($request->has('crop')) {
                                            $crop=$request->input('crop');
                                            $query .=" AND (`t`.`crop_1_id` = '".$crop."' OR `t`.`crop_2_id` = '".$crop."' OR `t`.`crop_3_id` = '".$crop."')";
                                        }
                                        $query .=" GROUP BY  
                                            t.ticket_id
                                        ";
                            $ticket_sfl = DB::select(DB::raw($query)); 
                            $s_fl = array_map(function ($value) {
                                return (array) $value;
                            }, $ticket_sfl);

                            $s_flticketcount = count($s_fl);
                            $s_flresultcount = count($s_flresult);
                            if ($s_flticketcount != 0) {
                                $totalS_flActivity = round($s_flticketcount / $s_flresultcount);
                            }
                            else
                            {
                                $totalS_flActivity = 0;
                            }
                        }
                        else
                        {
                            $totalS_flActivity = 0;
                        }
                    }


                    //register farmer average basis of ticket
                    $query = "
                                    SELECT 
                                        t.ticket_id as tID 
                                    FROM tickets t
                                    WHERE t.status != '0'  AND t.ticket_status != '0' ";

                                    if (!$request->has('rsm') || !$request->has('asm') || !$request->has('bdo_id') || !$request->has('dd_id')) 
                                    {
                                        if (count($rsmresult) > 0) {
                                           $query .=" AND `t`.`user_id` IN (" . $rsm_ids . ") AND `t`.`user_type` = 'sales_regional'";
                                        }

                                        if (count($asmresult) > 0) {
                                           $query .=" OR `t`.`user_id` IN (" . $asm_ids . ") AND `t`.`user_type` = 'sales_area'";
                                        }

                                        if (count($bdoresult) > 0) {
                                           $query .=" OR `t`.`user_id` IN (" . $bdo_ids . ") AND `t`.`user_type` = 'bdo'";
                                        }

                                        if (count($ddresult) > 0) {
                                           $query .=" OR `t`.`user_id` IN (" . $dd_ids . ") AND `t`.`user_type` = 'bdo'";
                                        }
                                    }
                                    else
                                    {
                                        if($request->has('rsm'))
                                        {
                                            $rsm=$request->input('rsm');
                                            $query.=" AND t.user_id = '".$rsm."' AND t.user_type = 'sales_regional'";
                                        }

                                        if ($request->has('asm')) {
                                            $asm=$request->input('asm');
                                            if ($request->has('rsm')) {
                                                $query.=" OR t.user_id = '".$asm."' AND t.user_type = 'sales_area' ";
                                            }
                                            else
                                            {
                                                $query.=" AND t.user_id = '".$asm."' AND t.user_type = 'sales_area' ";
                                            }
                                        }

                                        if ($request->has('bdo_id')) {
                                            $bdo_id=$request->input('bdo_id');
                                            if ($request->has('asm')) {
                                                $query.=" OR t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' ";
                                            }
                                            else
                                            {
                                                $query.=" AND t.user_id = '".$bdo_id."' AND t.user_type = 'bdo' ";
                                            }
                                            
                                        }

                                        if ($request->has('dd_id')) {
                                            $dd_id=$request->input('dd_id');
                                            if ($request->has('bdo_id')) {
                                                $query.=" OR t.user_id = '".$dd_id."' AND t.user_type = 'dhanuka_doctor' ";
                                            }
                                            else
                                            {
                                                $query.=" AND t.user_id = '".$dd_id."' AND t.user_type = 'dhanuka_doctor' ";
                                            }
                                        }
                                    }
                                    

                                    if($request->has('from_date') && $request->has('to_date')){
                                        $from_date=$request->input('from_date');
                                        $to_date=$request->input('to_date');
                                        $query.=" AND t.created_at BETWEEN '".$from_date."' 00:00:00 AND ".$to_date." 23:59:59";
                                    }

                                    if ($request->has('crop')) {
                                        $crop=$request->input('crop');
                                        $query .=" AND (`t`.`crop_1_id` = '".$crop."' OR `t`.`crop_2_id` = '".$crop."' OR `t`.`crop_3_id` = '".$crop."')";
                                    }


                                    $query .=" GROUP BY  
                                        t.ticket_id
                                    ";
                    $ticket_list = DB::select(DB::raw($query)); 
                    $ticketss = array_map(function ($value) {
                        return (array) $value;
                    }, $ticket_list);
                    $totalTickets = count($ticketss);
                    if ($totalTickets > 0) 
                    {
                        foreach ($ticketss as $key => $value) {
                            $farcount[] = DB::table('farmers')
                                        ->where('ticket_id',$value['tID'])
                                        ->count();
                        }
                        $totalfar = array_sum($farcount);
                        $totalfarmer = 0;
                        $totalfarmer = round($totalfar / $totalTickets);
                    }
                    else
                    {
                        $totalfarmer = 0;
                    }

                    //demo count
                    $demoscount = DB::table('demo_request');
                                if ($request->has('dd_id')) {
                                    $dd_id=$request->input('dd_id');
                                    $demoscount->where('demo_request.dd_id',$dd_id);
                                }
                                else
                                {
                                    if (count($ddresult)>0) {
                                      $demoscount->whereIn('dd_id',$ddresult);
                                          
                                    }
                                    // else
                                    // {
                                    //    $demoscount->where('1','!=','1'); 
                                    // }
                                }

                                if ($request->has('crop')) {
                                    $crop=$request->input('crop');
                                    $demoscount->Where('demo_request.crop_id',$crop);
                                }

                                if($request->has('from_date') && $request->has('to_date')){
                                    $from_date=$request->input('from_date');
                                    $to_date=$request->input('to_date');
                                    $demoscount->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }
                    $demoscount = $demoscount->count();

                    //efficiency data
                    $efficiencyData = Efficiency::select('month', 'year', 'efficiency')
                                        ->where('user_id', $user_id)
                                        ->where('user_type', $user_type)
                                        ->get()
                                        ->toArray();

                    if (count($efficiencyData) > 0) {
                        $efficiencyData1 = $efficiencyData;
                    }
                    else
                    {
                        $efficiencyData1 = [];
                    }
                    
                    
                    //total farmer of activity
                    $totalFarmerCount = 0;
                    $totalactCount = count($activity);
                    foreach ($activity as $key => $value) {
                        $totalFarmerData = DB::table('farmers')
                                           ->where('ticket_id', $activity[$key]['tID'])
                                           ->select('ticket_id')
                                           ->count();
                        $totalFarmerCount = $totalFarmerCount + $totalFarmerData;
                    }

                    $totalAverageActFarmer = 0;
                    $totalAverageActFarmer = $totalFarmerCount / $totalactCount;

                    //all data
                    $data = array('efficiencyData' => $efficiencyData1,'demolist' => $demolist, 'customer_care_open' => $active_count, 'customer_care_close' => $closed_count, 'customer_care_follow' => $follow_up_count, 'rating1' => $testimonialRates[0]->rating1, 'rating2' => $testimonialRates[0]->rating2, 'rating3' => $testimonialRates[0]->rating3, 'rating4' => $testimonialRates[0]->rating4, 'rating5' => $testimonialRates[0]->rating5, 'activities' => $totalActivity, 'total_village_meeting' => $totalVillageMeeting, 'total_field_day' => $totalFieldDay, 'bd_fl_activity' => $totalBd_flActivity, 's_fl_activity' => $totalS_flActivity, 'total_reg_farmer' => $totalfarmer, 'democount' => $demoscount, 'averageFarmerActivity' => round($totalAverageActFarmer));
                    
                    return $this->respond([
                                            'status' => 'success',
                                            'status_code' => $this->getStatusCode(),
                                            'message' => 'Supervisor report.',
                                            'data' => $data
                                ]);

                   
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function demoRequestListV1(Request $request){
        $rules = array(
            'apiToken' => 'required|string',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $curTime = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "v2/demoRequestListV1",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);

            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $dd_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                //$base_url = url('/');
                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";

                $default_from_date = date("Y-m-d", strtotime("45 days ago"));
                $default_to_date = date("Y-m-d");
                
                if ($user_type == 'bdo') 
                {   
                    //check counter fl bdo
                    $bdolist = DB::table('users')
                              ->where('counterpart_fl',$dd_id)
                              ->where('status','1')
                              ->select('id')
                              ->get();
                    $bdoresult = array($dd_id); 
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
                    }
                    // Log::info("bdoresult=>");
                    // Log::info($bdoresult);

                    if (count($bdoresult)>0) 
                    {
                        $ddData = DB::table('dhanuka_doctor')
                                  ->whereIn('bdo_id',$bdoresult)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        // Log::info("IF-ddData=>");
                        // Log::info($ddData);
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
                    else
                    {
                       $ddData = DB::table('dhanuka_doctor')
                                  ->where('bdo_id',$dd_id)
                                  ->where('status','1')
                                  ->select('dd_id')
                                  ->get();
                        // Log::info("Else-ddData=>");
                        // Log::info($ddData);
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
                    // Log::info("ddresult=>");
                    // Log::info($ddresult);
                    
                    if ($request->input('status') == 'Evaluated') {
                        $demoRequest =DemoRequest::join('villages','villages.village_id','=','demo_request.village_id','left')
                            ->join('tehsils','tehsils.tehsil_id','=','demo_request.tehsil_id','left')
                            ->join('districts','districts.district_id','=','demo_request.district_id','left')
                            ->join('states','states.state_id','=','demo_request.state_id','left')
                            ->join('products','products.product_id','=','demo_request.product_id','left')
                            ->join('crops','crops.crop_id','=','demo_request.crop_id','left')
                            ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','demo_request.dd_id','left');

                            if ($request->has('dd_ids')) {
                                $ddIdsss = explode(",",$request->input('dd_ids'));
                                $DDIDS = array();
                                foreach ($ddIdsss as $key => $value) {
                                    $DDIDS[] = $ddIdsss[$key];
                                }
                                // print_r($DDIDS);die;
                                $demoRequest->whereIn('demo_request.dd_id',$DDIDS);
                            }
                            else
                            {
                                $demoRequest->whereIn('demo_request.dd_id',$ddresult); 
                            }

                            if ($request->has('product')) {
                                $demoRequest->where('demo_request.product_id',$request->input('product'));
                            }

                            if ($request->has('crop')) {
                                $demoRequest->where('demo_request.crop_id',$request->input('crop'));
                            }

                            if($request->has('from_date') && $request->has('to_date')){
                                $from_date=$request->input('from_date');
                                $to_date=$request->input('to_date');
                                $demoRequest->whereBetween('demo_request.updated_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }  else {
                                $demoRequest->whereBetween('demo_request.updated_at',[$default_from_date." 00:00:00", $default_to_date." 23:59:59"]);
                            }
                            
                            if ($request->has('status')) {
                                //$demoRequest->where('demo_request.status',$request->input('status'));
                                $demoRequest->whereNotIn('demo_request.status', array('Pending','Overdue','Awaited'));
                            }

                            $demoRequest = $demoRequest->orderBy('demo_request.demo_request_id', 'DESC')
                            ->select('demo_request.*','villages.village','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','dhanuka_doctor.name as dd_name')
                            ->get();
                    }
                    else
                    {
                        $demoRequest =DemoRequest::join('villages','villages.village_id','=','demo_request.village_id','left')
                            ->join('tehsils','tehsils.tehsil_id','=','demo_request.tehsil_id','left')
                            ->join('districts','districts.district_id','=','demo_request.district_id','left')
                            ->join('states','states.state_id','=','demo_request.state_id','left')
                            ->join('products','products.product_id','=','demo_request.product_id','left')
                            ->join('crops','crops.crop_id','=','demo_request.crop_id','left')
                            ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','demo_request.dd_id','left');
                            if ($request->has('dd_ids')) {
                                $ddIdsss = explode(",",$request->input('dd_ids'));
                                $ids = $ddIdsss;
                                $ids = array_map(function ($value) {
                                    return (int) $value;
                                }, $ids);
                                
                                $demoRequest->whereIn('demo_request.dd_id',$ids);
                            }
                            else
                            {
                                $demoRequest->whereIn('demo_request.dd_id',$ddresult); 
                            }

                            if ($request->has('product')) {
                                $demoRequest->where('demo_request.product_id',$request->input('product'));
                            }
                            if ($request->has('crop')) {
                                $demoRequest->where('demo_request.crop_id',$request->input('crop'));
                            }
                            if($request->has('from_date') && $request->has('to_date')){
                                $from_date=$request->input('from_date');
                                $to_date=$request->input('to_date');
                                $demoRequest->whereBetween('demo_request.updated_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                            }  else {
                                $demoRequest->whereBetween('demo_request.updated_at',[$default_from_date." 00:00:00", $default_to_date." 23:59:59"]);
                            }
                            
                            if ($request->has('status')) {
                                if ($request->input('status') == 'Awaited') { 
                                    $demoRequest->whereIn('demo_request.status',['Awaited','Pending']);
                                }
                                else
                                {
                                    $demoRequest->where('demo_request.status',$request->input('status'));
                                }
                            }
                            $demoRequest = $demoRequest->orderBy('demo_request.demo_request_id', 'DESC')
                            ->select('demo_request.*','villages.village','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','dhanuka_doctor.name as dd_name')
                            ->get();
                           // echo "<pre>"; print_r($demoRequest);die;
                    }

                    if (isset($demoRequest) && !empty($demoRequest)) {
                        foreach ($demoRequest as $key => $value) {
                            $demofeedback=  DB::table('demofeedback')
                                            ->where('demofeedback.demo_request_id',$demoRequest[$key]->demo_request_id)
                                            ->select('demofeedback.creation_at','demofeedback.status','demofeedback.feedbackImage')
                                            ->orderBy('creation_at', 'DESC')
                                            ->get();
                        }
                    }
                }
                elseif ($user_type == 'dhanuka_doctor')
                {
                    $approval = $request->input('approval');
                    $product_id = ($request->input('product'))?$request->input('product'):"";
                    $crop_id = ($request->input('crop'))?$request->input('crop'):"";
                    $village_id = ($request->input('village_id'))?$request->input('village_id'):"";
                    $status = ($request->input('status'))?$request->input('status'):"";
                    if ($approval == 'approved') {
                        $demoRequest =DemoRequest::join('villages','villages.village_id','=','demo_request.village_id','left')
                                ->join('tehsils','tehsils.tehsil_id','=','demo_request.tehsil_id','left')
                                ->join('districts','districts.district_id','=','demo_request.district_id','left')
                                ->join('states','states.state_id','=','demo_request.state_id','left')
                                ->join('products','products.product_id','=','demo_request.product_id','left')
                                ->join('crops','crops.crop_id','=','demo_request.crop_id','left')
                                ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','demo_request.dd_id','left')
                                ->where('demo_request.dd_id',$dd_id)
                                ->whereNotIn('demo_request.status', ['Pending','Overdue']);
                                //->where('demo_request.status',$approval);
                                if (isset($product_id) && !empty($product_id)) {
                                $demoRequest->where('demo_request.product_id',$product_id);
                                }
                                if (isset($crop_id) && !empty($crop_id)) {
                                $demoRequest->where('demo_request.crop_id',$crop_id);
                                }
                                if($request->has('from_date') && $request->has('to_date')){
                                    $from_date=$request->input('from_date');
                                    $to_date=$request->input('to_date');
                                    $demoRequest->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }  else {
                                    $demoRequest->whereBetween('demo_request.created_at',[$default_from_date." 00:00:00", $default_to_date." 23:59:59"]);
                                }
                                if (isset($village_id) && !empty($village_id)) {
                                $demoRequest->where('demo_request.village_id',$village_id);
                                }
                                if (isset($status) && !empty($status)) {
                                $demoRequest->where('demo_request.status',$status);
                                }
                        $demoRequest = $demoRequest->orderBy('demo_request_id', 'DESC')
                                ->select('demo_request.*','villages.village','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','dhanuka_doctor.name as dd_name')
                                ->get();
                    }
                    else
                    {
                        $demoRequest =DemoRequest::join('villages','villages.village_id','=','demo_request.village_id','left')
                                ->join('tehsils','tehsils.tehsil_id','=','demo_request.tehsil_id','left')
                                ->join('districts','districts.district_id','=','demo_request.district_id','left')
                                ->join('states','states.state_id','=','demo_request.state_id','left')
                                ->join('products','products.product_id','=','demo_request.product_id','left')
                                ->join('crops','crops.crop_id','=','demo_request.crop_id','left')
                                ->join('dhanuka_doctor','dhanuka_doctor.dd_id','=','demo_request.dd_id','left')
                                ->where('demo_request.dd_id',$dd_id);
                                if (isset($product_id) && !empty($product_id)) {
                                $demoRequest->where('demo_request.product_id',$product_id);
                                }
                                if (isset($crop_id) && !empty($crop_id)) {
                                $demoRequest->where('demo_request.crop_id',$crop_id);
                                }
                                if($request->has('from_date') && $request->has('to_date')){
                                    $from_date=$request->input('from_date');
                                    $to_date=$request->input('to_date');
                                    $demoRequest->whereBetween('demo_request.created_at',[$from_date." 00:00:00", $to_date." 23:59:59"]);
                                }  else {
                                    $demoRequest->whereBetween('demo_request.created_at',[$default_from_date." 00:00:00", $default_to_date." 23:59:59"]);
                                }
                                if (isset($village_id) && !empty($village_id)) {
                                $demoRequest->where('demo_request.village_id',$village_id);
                                }
                                if (isset($status) && !empty($status)) {
                                $demoRequest->where('demo_request.status',$status);
                                }
                        $demoRequest = $demoRequest->orderBy('demo_request_id', 'DESC')
                                ->select('demo_request.*','villages.village','states.state','tehsils.tehsil','districts.district','products.product','crops.crop','dhanuka_doctor.name as dd_name')
                                ->get();
                                // echo "<pre>"; print_r($demoRequest); die;
                    }
                }

                if (isset($demoRequest) && !empty($demoRequest)) {
                    //$base_url = url('/');
                    $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
                    foreach ($demoRequest as $key => $value) {
                       if (!empty($demoRequest[$key]->demoImage)) {
                          $demoRequest[$key]->demoImage = $base_url . '/assets/demoimage/' . $demoRequest[$key]->demoImage;
                       }

                       if (!empty($demoRequest[$key]->farmer_image)) {
                          $demoRequest[$key]->farmer_image = $base_url . '/assets/farmer/' . $demoRequest[$key]->farmer_image;
                       }
                    }

                    $log_firstTime  = strtotime($log_start_time);
                    $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                    $log_time_diff  = $log_lastTime-$log_firstTime;
                    $timeConsumed   = round(microtime(true) - $curTime,3)*1000;
                    DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $dd_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                    return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Demo request list.',
                                        'data' =>$demoRequest
                            ]);
                   

                }
                else
                {
                    return $this->respond([
                                            'status' => 'success',
                                            'status_code' => $this->getStatusCode(),
                                            'message' => 'No data found.',
                                            'data' =>array()
                                ]);
                }
                
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function villageDetail(Request $request)
    {
        $rules = array(
            'apiToken' => 'required|string',
            'village_id' => 'required'
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
                    $village_id = $request->input('village_id');
                    //$ddData = DhanukaDoctor::where('dd_id', $user_id)->first();
                    $ddData = DB::table('villages')
                                ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                                ->join('districts','districts.district_id','=','villages.district_id')
                                ->join('states','states.state_id','=','districts.state_id')
                                ->select('villages.village_id as villageId','villages.village as villageName','states.state','tehsils.tehsil','districts.district')
                                ->where('villages.status', '1')
                                ->where('villages.village_id', $village_id)
                                ->get();
                    //for($i=0;$i<count($ddData);$i++){
                    if ($ddData[0]->villageId) {
                        $cropList=VillageFarmerCrop::join('crops','crops.crop_id','=','village_farmer_crop.crop_id')
                                ->where('village_id',$ddData[0]->villageId)
                                ->groupBy('village_farmer_crop.crop_id')
                                ->select('crops.crop',DB::raw("sum(acres) as acres"),DB::raw("count(*) as farmersCount"))
                                ->get();
                        
                        $farmers=VillageFarmer::where('village_id',$ddData[0]->villageId)->count();
                        $ddData[0]->farmers=$farmers;
                        $villageVisit=MarkVisit::where('village_id',$ddData[0]->villageId)->count();
                        $ddData[0]->avgVisit=$villageVisit;
                        $ddData[0]->cropList=$cropList;
                    }
                        
                    //}           
                    
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Village Detail',
                                'data' => $ddData[0]
                    ]);
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
        }
    }

    public function allLocations(Request $request)
    {
        $rules = array(
            'apiToken' => 'required:string' 
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
           return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        }
        else
        {
            $curTime = microtime(true);
            $log_start_time = date("Y-m-d H:i:s");
            $log_data = array(
                'api_name'   => "v2/allLocations",
                'start_time' => $log_start_time,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            $log_result = DB::table('query_log')->insertGetId($log_data);
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') 
            {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $device_name = ($request->input('device_name'))?$request->input('device_name'):'';
                $device_id  = ($request->input('device_id'))?$request->input('device_id'):'';
                $lat_location = ($request->input('lat_location'))?$request->input('lat_location'):'';
                $long_location = ($request->input('long_location'))?$request->input('long_location'):'';
                $app_version = ($request->input('app_version'))?$request->input('app_version'):'';
                $device_os  = ($request->input('device_os'))?$request->input('device_os'):'';

                if($app_version >='5.26') {
                    $checkUser = DB::table('device_info')
                                 ->where('user_id',$user_id)
                                 ->where('device_type',$device_name)
                                 ->where('user_type',$user_type)
                                 ->get();
                    if(count($checkUser)>0){
                        $updateDevice = DB::table('device_info')
                                        ->where('user_id',$user_id)
                                        ->where('device_type',$device_name)
                                        ->where('user_type',$user_type)
                                        ->update(['device_id' => $device_id, 'device_os' => $device_os, 'lat_location' => $lat_location, 'long_location' => $long_location, 'app_version' => $app_version, 'updated_at' => date('Y-m-d H:i:s')]);
                    }
                    else
                    {
                        $insertDevice = DB::table('device_info')
                                        ->insert(['user_id' => $user_id, 'user_type' => $user_type, 'device_type' => $device_name, 'device_id' => $device_id, 'device_os' => $device_os, 'lat_location' => $lat_location, 'long_location' => $long_location, 'app_version' => $app_version]);
                    }
                    

                    if($app_version != ''){
                        $app_version_data = array(
                            'user_id' => $user_id,
                            'user_type' => $user_type,
                            'app_version' => $app_version
                        );
                        $insert_version = AppVersion::firstOrCreate($app_version_data);
                    }
                } else {
                    $user_details = $this->findUserOtherDetail($user_type, $api_token);
                    $user_name = $user_details[0]->manager_name;
                    $user_mobile = $user_details[0]->mobile;
                    $user_ecn = $user_details[0]->ECN;
                    $userinfos = array(
                        'user_id' => $user_id,
                        'user_type' => $user_type,
                        'user_ecn' => $user_ecn,
                        'user_name' => $user_name,
                        'user_mobile' => $user_mobile,
                        'device_type' => $device_name,
                        'device_id' => $device_id,
                        'device_os' => $device_os,
                        'lat_location' => $lat_location,
                        'long_location' => $long_location,
                        'app_version' => $app_version,
                        'date_time' => date("Y-m-d H:i:s")
                    );
                    $logFile = getcwd()."/storage/device_info.txt";
                    $myfile = fopen($logFile, "a");
                    fwrite($myfile, json_encode($userinfos)."\n");
                    fclose($myfile);
                }
                
                //getting all states
                $states = DB::table('states')
                         ->where('status','1')
                         ->select('state_id','state')
                         ->get();

                //getting all districts
                $districts = DB::table('districts') 
                             ->where('status','1')
                             ->select('state_id','district_id','district')
                             ->get();

                //getting all tehsils
                $tehsils = DB::table('tehsils')
                           ->where('status','1')
                           ->select('district_id','tehsil_id','tehsil')
                           ->get();

                //getting all villages
                $villages = DB::table('villages')
                            ->where('status','1')
                            ->select('district_id','tehsil_id','village_id','village')
                            ->get();

                $data = array(
                                'states' => $states, 
                                'districts' => $districts, 
                                'tehsils' => $tehsils, 
                                'villages' => $villages
                              );

                $log_firstTime  = strtotime($log_start_time);
                $log_lastTime   = strtotime(date("Y-m-d H:i:s"));
                $log_time_diff  = $log_lastTime-$log_firstTime;
                $timeConsumed = round(microtime(true) - $curTime,3)*1000;
                DB::table('query_log')->where('id', $log_result)->update(['user_id'=> $user_id,'user_type'=>$user_type,'end_time'=> date('Y-m-d H:i:s'),'time_taken'=> $log_time_diff,'timeConsumed'=> $timeConsumed,'updated_at'=>date('Y-m-d H:i:s')]);

                return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'All Locations.',
                                'data' => $data
                            ]);
            } 
            else 
            {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }


    public function acpMap(Request $request){
        $rules = array(
            'apiToken' => 'required:string',
            'latitude' => 'required',
            'longitude' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
           return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        }
        else
        {   
            $api_token = $request->input('apiToken');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            //$offset = ($request->input('offset'))?$request->input('offset'):0;
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') 
            {   
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                if ($user_type == 'dhanuka_doctor') { 
                    $quer = "select acp.farmer_id as farmerId , acp.farmer_name as farmerName, acp.father_name as fatherName , sum(acres) as acres , acp.contact_number as contactNumber , acp.alternate_contact_number as alternateContactNumber, acp.farmer_image as farmarImage, s.state, t.tehsil ,d.district,acp.call_status,v.tehsil_id as tehsilId,v.district_id as districtId,d.state_id as stateId,v.village,v.village_id,acp.farmer_lat as farmerLat,acp.farmer_long as farmerLong ,(3959*acos(cos(radians($latitude))*cos(radians(acp.farmer_lat))*cos(radians(acp.farmer_long)-radians($longitude))+sin(radians($latitude))*sin(radians(acp.farmer_lat)))) AS distance
                    from village_farmers as acp 
                    left join village_farmer_crop as vfc on vfc.farmer_id = acp.farmer_id
                    left join villages as v on v.village_id = v.village_id
                    left join tehsils as t on t.tehsil_id = v.tehsil_id
                    left join districts as d on d.district_id = v.district_id
                    left join states as s on s.state_id = d.state_id
                    where acp.dd_id = ".$user_id."
                    group by vfc.farmer_id
                    having distance <= '2' 
                    ";
                    $villageFarmer = DB::select(DB::raw($quer));
                    foreach ($villageFarmer as $key => $value) 
                    {
                        $markVisit=MarkVisit::where('farmer_id',$villageFarmer[$key]->farmerId)->count();
                        $villageFarmer[$key]->acpVisit=$markVisit;
                        $leaddata = DB::table('leads')
                                    ->where('leads.farmer_id',$villageFarmer[$key]->farmerId)
                                    ->select('leads.lead_status')
                                    ->get();
                        if ($leaddata) {
                            $villageFarmer[$key]->lead_status = $leaddata[0]->lead_status;
                        }
                        else
                        {
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
                        }
                        else
                        {
                            $villageFarmer[$key]->products = [];
                        }

                        $crops = DB::table('village_farmer_crop')
                        ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                        ->where('village_farmer_crop.farmer_id',$villageFarmer[$key]->farmerId)
                        ->select('village_farmer_crop.crop_id','crop')
                        ->get();

                        if ($crops) {
                            $villageFarmer[$key]->crops = $crops;
                        }
                        else
                        {
                            $villageFarmer[$key]->crops = [];
                        }
                    }
                    for($i=0;$i<count($villageFarmer);$i++){
                        $villageFarmer[$i]->farmarImage=Config('app.base_url')."assets/farmer/".$villageFarmer[$i]->farmarImage;
                        $markVisit=MarkVisit::where('farmer_id',$villageFarmer[$i]->farmerId)->count();
                        $villageFarmer[$i]->acpVisit=$markVisit;
                    }

                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Farmer List.',
                                'data' =>$villageFarmer
                    ]);
                }
                elseif ($user_type == 'bdo') 
                {
                    $checkbdotype = DB::table('users')
                                        ->where('id',$user_id)
                                        ->where('status','1')
                                        ->select('designation','id')
                                        ->get();
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
                        array_push($bdo_ids, $user_id);
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
                    $quer = "select acp.farmer_id as farmerId , acp.farmer_name as farmerName, acp.father_name as fatherName , sum(acres) as acres , acp.contact_number as contactNumber , acp.alternate_contact_number as alternateContactNumber, acp.farmer_image as farmarImage, s.state, t.tehsil ,d.district,acp.call_status,v.tehsil_id as tehsilId,v.district_id as districtId,d.state_id as stateId,v.village,v.village_id,acp.farmer_lat as farmerLat,acp.farmer_long as farmerLong ,(3959*acos(cos(radians($latitude))*cos(radians(acp.farmer_lat))*cos(radians(acp.farmer_long)-radians($longitude))+sin(radians($latitude))*sin(radians(acp.farmer_lat)))) AS distance
                    from village_farmers as acp 
                    left join village_farmer_crop as vfc on vfc.farmer_id = acp.farmer_id
                    left join villages as v on v.village_id = v.village_id
                    left join tehsils as t on t.tehsil_id = v.tehsil_id
                    left join districts as d on d.district_id = v.district_id
                    left join states as s on s.state_id = d.state_id
                    where acp.dd_id in ('".$dd_ids."')
                    group by vfc.farmer_id
                    having distance <= '2' 
                    ";
                    $villageFarmer = DB::select(DB::raw($quer));
                    foreach ($villageFarmer as $key => $value) 
                    {
                        $markVisit=MarkVisit::where('farmer_id',$villageFarmer[$key]->farmerId)->count();
                        $villageFarmer[$key]->acpVisit=$markVisit;
                        $leaddata = DB::table('leads')
                                    ->where('leads.farmer_id',$villageFarmer[$key]->farmerId)
                                    ->select('leads.lead_status')
                                    ->get();
                        if ($leaddata) {
                            $villageFarmer[$key]->lead_status = $leaddata[0]->lead_status;
                        }
                        else
                        {
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
                        }
                        else
                        {
                            $villageFarmer[$key]->products = [];
                        }

                        $crops = DB::table('village_farmer_crop')
                        ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                        ->where('village_farmer_crop.farmer_id',$villageFarmer[$key]->farmerId)
                        ->select('village_farmer_crop.crop_id','crop')
                        ->get();

                        if ($crops) {
                            $villageFarmer[$key]->crops = $crops;
                        }
                        else
                        {
                            $villageFarmer[$key]->crops = [];
                        }

                        $leads = DB::table('leads')
                        ->where('farmer_id',$villageFarmer[$key]->farmerId)
                        ->where('farmer_type','normal_farmer')
                        ->select('lead_id', 'lead_code', 'lead_status', 'created_at','expected_purchase_date','purchased_from','purchased_area')
                        ->get();

                        if ($leads) {
                            $villageFarmer[$key]->leads = $leads;
                        }
                        else
                        {
                            $villageFarmer[$key]->leads = [];
                        }
                    }
                    for($i=0;$i<count($villageFarmer);$i++){
                        $villageFarmer[$i]->farmarImage=Config('app.base_url')."assets/farmer/".$villageFarmer[$i]->farmarImage;
                        $markVisit=MarkVisit::where('farmer_id',$villageFarmer[$i]->farmerId)->count();
                        $villageFarmer[$i]->acpVisit=$markVisit;
                    }

                    if (count($bdo_ids)>0) {
                        $ddData = DB::table('dhanuka_doctor')->whereIn('bdo_id',$bdo_ids)->where('status','1')->get();
                    }
                    else
                    {
                        $ddData = DB::table('dhanuka_doctor')->where('bdo_id',$user_id)->where('status','1')->get();
                    }
                    
                    $ddListRecord = array();

                    foreach ($ddData as $keyDD1 => $valueDD1) {
                    $ddListRecord[$keyDD1]['dd_id'] = $valueDD1->dd_id;
                    $ddListRecord[$keyDD1]['ECN'] = $valueDD1->ECN;
                    $ddListRecord[$keyDD1]['name'] = $valueDD1->name;
                    }

                    $villageFarmer555['totalACPFamer'] = 0;
                    $villageFarmer555['total_leads'] = 0;
                    $villageFarmer555['totalAcres'] = 0;
                    $villageFarmer555['totalClosedAcres'] = 0;
                    $villageFarmer555['ACPDetail'] = $villageFarmer;
                    $villageFarmer555['DDList'] = $ddListRecord;
                    
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Farmer List.',
                                'data' =>$villageFarmer
                    ]);
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
                        $dd_ids = implode("','", $ddresult);
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
                        $dd_ids = implode("','", $ddresult);
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
                        $dd_ids = implode("','", $ddresult);
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
                        $dd_ids = implode("','", $ddresult);
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
                        $dd_ids = implode("','", $ddresult);
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
                                //marketing supervisor dd
                                $ddData = DB::table('dhanuka_doctor')
                                          ->whereIn('sales_regional_manager_id',$rsmresult)
                                          ->where('status','1')
                                          ->select('dd_id')
                                          ->groupBy('dd_id')
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
                            $dd_ids = implode("','", $ddresult);
                        }
                    } 
                    $quer5 = "select acp.farmer_id as farmerId ,(3959*acos(cos(radians($latitude))*cos(radians(acp.farmer_lat))*cos(radians(acp.farmer_long)-radians($longitude))+sin(radians($latitude))*sin(radians(acp.farmer_lat)))) AS distance
                    from village_farmers as acp 
                    where acp.dd_id in ('".$dd_ids."')
                    group by acp.farmer_id
                    having distance <= '10'";
                    //where acp.dd_id in ('".$dd_ids."')
                    $villageFarmer555 = DB::select(DB::raw($quer5));
                    $Vresult = array();
                    if (count($villageFarmer555)>0 && isset($villageFarmer555)) {

                        $VDatass = json_decode(json_encode($villageFarmer555),true);
                        foreach ($VDatass as $key => $value) { 
                            unset($value['distance']);
                            if (is_array($value)) 
                            { 
                                $Vresult = array_merge($Vresult, array_flatten($value)); 
                            } 
                            else 
                            { 
                                $Vresult[$key] = $value; 
                            } 
                        }
                        $F_ids = implode("','", $Vresult);
                    }
                    
                    // print_r($Vresult);die;

                    $quer = "select acp.farmer_id as farmerId , acp.farmer_name as farmerName, acp.father_name as fatherName , sum(acres) as acres , acp.contact_number as contactNumber , acp.alternate_contact_number as alternateContactNumber, acp.farmer_image as farmarImage, s.state, t.tehsil ,d.district,acp.call_status,v.tehsil_id as tehsilId,v.district_id as districtId,d.state_id as stateId,v.village,v.village_id,acp.farmer_lat as farmerLat,acp.farmer_long as farmerLong
                    from village_farmers as acp 
                    left join village_farmer_crop as vfc on vfc.farmer_id = acp.farmer_id
                    left join villages as v on v.village_id = acp.village_id
                    left join tehsils as t on t.tehsil_id = v.tehsil_id
                    left join districts as d on d.district_id = v.district_id
                    left join states as s on s.state_id = d.state_id
                    where vfc.farmer_id in ('".$F_ids."')
                    group by vfc.farmer_id ";
                    $villageFarmer = DB::select(DB::raw($quer));
                    foreach ($villageFarmer as $key => $value) 
                    {
                        $markVisit=MarkVisit::where('farmer_id',$villageFarmer[$key]->farmerId)->count();
                        $villageFarmer[$key]->acpVisit=$markVisit;
                        $leaddata = DB::table('leads')
                                    ->where('leads.farmer_id',$villageFarmer[$key]->farmerId)
                                    ->select('leads.lead_status')
                                    ->get();
                        if ($leaddata) {
                            $villageFarmer[$key]->lead_status = $leaddata[0]->lead_status;
                        }
                        else
                        {
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
                        }
                        else
                        {
                            $villageFarmer[$key]->products = [];
                        }

                        $crops = DB::table('village_farmer_crop')
                        ->join('crops','crops.crop_id','=','village_farmer_crop.crop_id','left')
                        ->where('village_farmer_crop.farmer_id',$villageFarmer[$key]->farmerId)
                        ->select('village_farmer_crop.crop_id','crop')
                        ->get();

                        if ($crops) {
                            $villageFarmer[$key]->crops = $crops;
                        }
                        else
                        {
                            $villageFarmer[$key]->crops = [];
                        }

                        $leads = DB::table('leads')
                        ->where('farmer_id',$villageFarmer[$key]->farmerId)
                        ->where('farmer_type','normal_farmer')
                        ->select('lead_id', 'lead_code', 'lead_status', 'created_at','expected_purchase_date','purchased_from','purchased_area')
                        ->get();

                        if ($leads) {
                            $villageFarmer[$key]->leads = $leads;
                        }
                        else
                        {
                            $villageFarmer[$key]->leads = [];
                        }
                    }
                    for($i=0;$i<count($villageFarmer);$i++){
                        $villageFarmer[$i]->farmarImage=Config('app.base_url')."assets/farmer/".$villageFarmer[$i]->farmarImage;
                        $markVisit=MarkVisit::where('farmer_id',$villageFarmer[$i]->farmerId)->count();
                        $villageFarmer[$i]->acpVisit=$markVisit;
                    }

                    if (count($ddresult)>0) {
                        $ddData = DB::table('dhanuka_doctor')->whereIn('dd_id',$ddresult)->where('status','1')->get();
                    }
                    
                    $ddListRecord = array();

                    foreach ($ddData as $keyDD1 => $valueDD1) {
                    $ddListRecord[$keyDD1]['dd_id'] = $valueDD1->dd_id;
                    $ddListRecord[$keyDD1]['ECN'] = $valueDD1->ECN;
                    $ddListRecord[$keyDD1]['name'] = $valueDD1->name;
                    }

                    $villageFarmer5556['totalACPFamer'] = 0;
                    $villageFarmer5556['total_leads'] = 0;
                    $villageFarmer5556['totalAcres'] = 0;
                    $villageFarmer5556['totalClosedAcres'] = 0;
                    
                    $villageFarmer5556['ACPDetail'] = $villageFarmer;
                    $villageFarmer5556['DDList'] = $ddListRecord;
                    
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Farmer List.',
                                'data' =>$villageFarmer5556
                    ]);
                }
                
            }
            else 
            {
                return $this->respondNotFound("Invalid Attempt !!");
            }
            
        }
    }

    public function NewAtpFeedback(Request $request) {
        try {
            $data = $request->all();//print_r($data);
            
            //echo "<pre>";print_r($data); die;
            //print_r($request->file('feedbackImage'));die;
            $rules = array(
                'ticketData' => 'required'
            );

            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {

                if ($request->hasFile('feedbackImage')) 
                {
                    $feedbackImage = [];
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
                $ticketData = json_decode($ticketData1,true);
                foreach ($ticketData as $key => $value) {
                    $api_token = $ticketData[$key]['apiToken'];
                    $user = $this->findUser($api_token);
                    if ($user && count($user) == '1') {
                        $user_id = $user[0]->id;
                        $user_type = $user[0]->user_type;
                        $currentDat = date('Y-m-d H:i:s');
                        if(isset($ticketData[$key]['created_at']) && !empty($ticketData[$key]['created_at'])){
                            $currentDat = $ticketData[$key]['created_at'];
                        }
                        else{
                            $currentDat = date('Y-m-d H:i:s');
                        }
                        if ($user_type == 'bdo') {
                            $atpfeedback = DB::table('atpfeedback')->insert(
                                            ['ticket_id' => $ticketData[$key]['ticket_id'],'user_id' => $user_id, 'user_type' => $user_type, 'description' => ($ticketData[$key]['description'])?$ticketData[$key]['description']:"", 'feedbackImage' => ($feedbackImage[$key])?$feedbackImage[$key]:"", 'creation_at' => $currentDat]
                                          );
                        }
                        else
                        {   
                            //date('Y-m-d H:i:s')
                            $atpfeedback = DB::table('atpfeedback')->insert(
                                            ['ticket_id' => $ticketData[$key]['ticket_id'],'user_id' => $user_id, 'user_type' => $user_type, 'description' => ($ticketData[$key]['description'])?$ticketData[$key]['description']:"", 'feedbackImage' => ($feedbackImage[$key])?$feedbackImage[$key]:"", 'creation_at' => $currentDat]
                                          );

                            // if ($atpfeedback > 0) 
                            // {   
                                $ticketcheckdata = DB::table('tickets')
                                                   ->where('ticket_id', $ticketData[$key]['ticket_id'])
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
                                            $insert_self_participation_plan = DB::table('self_participation_plans')->insert(
                                            ['plan_atp_id' => $ticketcheckdata[0]->plan_atp_id,'user_id' => $user_id, 'user_type' => $user_type,'plan_date' => $plandata[0]->Cplan_date, 'reason' => $description, 'created_at' => $currentDat]
                                          );

                                            $latitude = ($ticketData[$key]['latitude'])?$ticketData[$key]['latitude']:"";
                                            $longitude = ($ticketData[$key]['longitude'])?$ticketData[$key]['longitude']:"";
                                            $feedbackImageData = ($feedbackImage[$key])? 'assets/atpfeedbackimage/' . $feedbackImage[$key] :"";
                                            $insert_self_participation = DB::table('self_participations')->insert(
                                            ['ticket_id' => $ticketData[$key]['ticket_id'],'user_id' => $user_id, 'user_type' => $user_type, 'description' => $description, 'uploaded_file' => $feedbackImageData,'latitude' => $latitude, 'longitude' => $longitude, 'created_at' => $currentDat ]
                                          );
                                        }
                                    }
                                    else
                                    {   
                                        $latitude = ($ticketData[$key]['latitude'])?$ticketData[$key]['latitude']:"";
                                        $longitude = ($ticketData[$key]['longitude'])?$ticketData[$key]['longitude']:"";
                                        $feedbackImageData = ($feedbackImage[$key])? 'assets/atpfeedbackimage/' . $feedbackImage[$key] :"";
                                        $description = ($ticketData[$key]['description'])?$ticketData[$key]['description']:"";
                                        $insert_self_participation = DB::table('self_participations')->insert(
                                            ['ticket_id' => $ticketData[$key]['ticket_id'],'user_id' => $user_id, 'user_type' => $user_type, 'description' => $description, 'uploaded_file' => $feedbackImageData,'latitude' => $latitude, 'longitude' => $longitude, 'created_at' => $currentDat ]
                                          );
                                    }
                                } 
                            
                        }
                    } else {
                        return $this->respondWithConflict("Invalid Attempt!");
                    }
                }

                if ($atpfeedback > 0) 
                {
                    return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Feedback submitted successfully.'
                        ]);
                }
                else 
                {
                    return $this->respondNotFound("Something went wrong. Please Try Again!!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function SelfParticipationImages(Request $request){
        $rules = array(
            'from_date' => 'required', 
            'to_date' => 'required' 
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
           return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        }
        else
        {   
            $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
            $from_date  = ($request->input('from_date'))?$request->input('from_date'):'';
            $to_date    = ($request->input('to_date'))?$request->input('to_date'):'';
            $checkImage = DB::table('self_participations')
                          ->where('created_at','>=',$from_date)
                          ->where('created_at','<=',$to_date)
                          ->where('uploaded_file','!=','')
                          ->select('self_participation_id','uploaded_file')
                          ->get();
            if(count($checkImage)>0){
                foreach ($checkImage as $key => $value) {
                    $checkImage[$key]->uploaded_file = $base_url.'/'.$checkImage[$key]->uploaded_file;
                }
                return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Self participation images.',
                                'data' => $checkImage
                            ]);
            }
            else{
                return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No self participation images found.',
                                'data' => []
                            ]);
            }
        }
    }

    public function uploadawsImage(Request $request) {
        if($request->file('image')){
            $file=$request->file('image');
            $fileName = time().rand(111,999).".".$file->getClientOriginalExtension();
            $sTempFileName = 'assets/atpfeedbackimage/' . $fileName;
            $uploadImage = Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
        }
        else
        {
            return $this->respond([
                'status' => 'error',
                'status_code' => $this->getStatusCode(),
                'message' => 'Please choose image'
            ]);
        }
    }

}
