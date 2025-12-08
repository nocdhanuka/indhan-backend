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

class WebServiceController extends ApiController {

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

    public function register(Request $request) {

        $rules = array(
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:3'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        } else {
            $user = User::create([
                        'name' => $request['name'],
                        'email' => $request['email'],
                        'password' => \Hash::make($request['password']),
            ]);
            return $this->_login($request['email'], $request['password']);
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
        //print_r($credentials); echo $user_table;exit();
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
        //echo $token = JWTAuth::attempt($credentials);exit();
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

    public function login(Request $request) {
        $data = $request->json()->all();
        $rules = array(
            'username' => 'required|min:3|max:10',
            'password' => 'required|min:6|max:15',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        } else {
            $query = "
                        (SELECT zonal_manager_id AS id, ECN, password, api_token, 'zonal_managers' as user_type FROM zonal_managers WHERE status='1' AND ECN!='' AND password!='' AND ECN ='" . $data['username'] . "' LIMIT 0,1)
                        UNION
                        (SELECT regional_manager_id AS id, ECN, password, api_token, 'regional_managers' as user_type FROM regional_managers    WHERE status='1' AND ECN!='' AND password!='' AND ECN ='" . $data['username'] . "' LIMIT 0,1)
                        UNION
                        (SELECT area_manager_id AS id, ECN, password, api_token, 'area_managers' as user_type FROM area_managers    WHERE status='1' AND ECN!='' AND password!='' AND ECN ='" . $data['username'] . "' LIMIT 0,1)
                        UNION
                        (SELECT sales_regional_manager_id AS id, ECN, password, api_token, 'sales_regional_managers' as user_type FROM sales_regional_managers  WHERE status='1' AND ECN!='' AND password!='' AND ECN ='" . $data['username'] . "' LIMIT 0,1)
                        UNION
                        (SELECT sales_area_manager_id AS id, ECN, password, api_token, 'sales_area_managers' as user_type FROM sales_area_managers  WHERE status='1' AND ECN!='' AND password!='' AND ECN ='" . $data['username'] . "' LIMIT 0,1)
                        UNION
                        (SELECT id AS id, ECN, password, api_token, 'users' as user_type FROM users WHERE status='1' AND ECN!='' AND password!='' AND ECN ='" . $data['username'] . "' LIMIT 0,1)
                        UNION
                        (SELECT dd_id AS id, ECN, password, api_token, 'dhanuka_doctor' as user_type FROM dhanuka_doctor WHERE status='1' AND ECN!='' AND password!='' AND ECN ='" . $data['username'] . "' LIMIT 0,1)
                        ";

            $user = DB::select(DB::raw($query));
            //print_r($user);die;
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $api_token = $user[0]->api_token;
                $user_table = $user[0]->user_type;
                switch ($user_table) {
                    case "zonal_managers" : $user_states = DB::table('zonal_manager_states')
                                ->select('states.state_id as sID', 'states.state as Item')
                                ->leftJoin('states', 'zonal_manager_states.state_id', '=', 'states.state_id')
                                ->where('zonal_manager_states.zonal_manager_id', $user_id)
                                ->where('states.status', '1')
                                ->get();
                        break;
                    case "regional_managers" : $user_states = DB::table('regional_manager_states')
                                ->select('states.state_id as sID', 'states.state as Item')
                                ->leftJoin('states', 'regional_manager_states.state_id', '=', 'states.state_id')
                                ->where('regional_manager_states.regional_manager_id', $user_id)
                                ->where('states.status', '1')
                                ->get();
                        break;
                    case "area_managers" : $user_states = DB::table('area_manager_states')
                                ->select('states.state_id as sID', 'states.state as Item')
                                ->leftJoin('states', 'area_manager_states.state_id', '=', 'states.state_id')
                                ->where('area_manager_states.area_manager_id', $user_id)
                                ->where('states.status', '1')
                                ->get();
                        break;
                    case "sales_regional_managers" : $user_states = DB::table('sales_regional_manager_states')
                                ->select('states.state_id as sID', 'states.state as Item')
                                ->leftJoin('states', 'sales_regional_manager_states.state_id', '=', 'states.state_id')
                                ->where('sales_regional_manager_states.sales_regional_manager_id', $user_id)
                                ->where('states.status', '1')
                                ->get();
                        break;
                    case "sales_area_managers" : $user_states = DB::table('sales_area_manager_states')
                                ->select('states.state_id as sID', 'states.state as Item')
                                ->leftJoin('states', 'sales_area_manager_states.state_id', '=', 'states.state_id')
                                ->where('sales_area_manager_states.sales_area_manager_id', $user_id)
                                ->where('states.status', '1')
                                ->get();
                        break;
                    case "users" : $user_states = DB::table('users')
                                ->select('states.state_id as sID', 'states.state as Item')
                                ->leftJoin('states', 'users.state_id', '=', 'states.state_id')
                                ->where('users.id', $user_id)
                                ->where('states.status', '1')
                                ->get();
                        break;
                    case "dhanuka_doctor" : $user_states = DB::table('dhanuka_doctor')
                                ->select('states.state_id as sID', 'states.state as Item')
                                ->leftJoin('states', 'dhanuka_doctor.state_id', '=', 'states.state_id')
                                ->where('dhanuka_doctor.dd_id', $user_id)
                                ->where('states.status', '1')
                                ->get();
                        break;
                }

                if ($api_token == NULL || $api_token == '') {
                    return $this->authenticate($data['username'], $data['password'], $user_table, $user_states);
                } else {
                    switch ($user_table) {
                        case "zonal_managers" : ZonalManager::where('zonal_manager_id', $user_id)->update(['api_token' => NULL]);
                            break;
                        case "regional_managers" : RegionalManager::where('regional_manager_id', $user_id)->update(['api_token' => NULL]);
                            break;
                        case "area_managers" : AreaManager::where('area_manager_id', $user_id)->update(['api_token' => NULL]);
                            break;
                        case "sales_regional_managers" : SalesRegionalManager::where('sales_regional_manager_id', $user_id)->update(['api_token' => NULL]);
                            break;
                        case "sales_area_managers" : SalesAreaManager::where('sales_area_manager_id', $user_id)->update(['api_token' => NULL]);
                            break;
                        case "users" : User::where('id', $user_id)->update(['api_token' => NULL]);
                            break;
                        case "dhanuka_doctor" : DhanukaDoctor::where('dd_id', $user_id)->update(['api_token' => NULL]);
                            break;
                    }
                    return $this->authenticate($data['username'], $data['password'], $user_table, $user_states);
                }
            } else {
                return $this->respondWithError("Login Unsuccessful. Invalid User!");
            }
        }
    }

    public function logout($api_token) {
        try {
            $query = "
                        SELECT zonal_manager_id AS id, 'zonal_managers' as user_type FROM zonal_managers WHERE api_token ='" . $api_token . "' LIMIT 0,1
                        UNION
                        SELECT regional_manager_id AS id, 'regional_managers' as user_type FROM regional_managers WHERE  api_token ='" . $api_token . "' LIMIT 0,1
                        UNION
                        SELECT area_manager_id AS id, 'area_managers' as user_type FROM area_managers WHERE api_token ='" . $api_token . "' LIMIT 0,1
                        UNION
                        SELECT sales_regional_manager_id AS id, 'sales_regional_managers' as user_type FROM sales_regional_managers WHERE api_token ='" . $api_token . "' LIMIT 0,1
                        UNION
                        SELECT sales_area_manager_id AS id, 'sales_area_managers' as user_type FROM sales_area_managers WHERE api_token ='" . $api_token . "' LIMIT 0,1
                        UNION
                        SELECT id AS id, 'users' as user_type FROM users WHERE api_token ='" . $api_token . "' LIMIT 0,1
                        UNION
                        SELECT dd_id AS id, 'dhanuka_doctor' as user_type FROM dhanuka_doctor WHERE api_token ='" . $api_token . "' LIMIT 0,1
                        ";
            $user = DB::select(DB::raw($query));
            if ($user && count($user) == '1') {
                $user_table = $user[0]->user_type;
                switch ($user_table) {
                    case "zonal_managers" : ZonalManager::where('api_token', $api_token)->update(['api_token' => '']);
                        break;
                    case "regional_managers" : RegionalManager::where('api_token', $api_token)->update(['api_token' => '']);
                        break;
                    case "area_managers" : AreaManager::where('api_token', $api_token)->update(['api_token' => '']);
                        break;
                    case "sales_regional_managers" : SalesRegionalManager::where('api_token', $api_token)->update(['api_token' => '']);
                        break;
                    case "sales_area_managers" : SalesAreaManager::where('api_token', $api_token)->update(['api_token' => '']);
                        break;
                    case "users" : User::where('api_token', $api_token)->update(['api_token' => '']);
                        break;
                    case "dhanuka_doctor" : DhanukaDoctor::where('api_token', $api_token)->update(['api_token' => '']);
                        break;
                }
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Logout successful!',
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function forgot(Request $request) {
        $data = $request->json()->all();
        $rules = array(
            'username' => 'required|min:3|max:10',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        } else {
            $user = User::where('ECN', $data['username'])->first();
            if ($user) {
                Mail::send('emails.welcome', array('email' => 'rishabh1.yopmail.com', 'user_key' => md5('sdfdf')), function ($message) use($email) {
                    $message->to($email, 'To')->subject('Welcome to gfh');
                });
                $api_token = $user->api_token;
                if ($api_token == NULL) {
                    return $this->authenticate($data['username'], $data['password']);
                }
                try {
                    $user = JWTAuth::toUser($api_token);
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Already logged in',
                                'data' => $this->userTransformer->transform($user)
                    ]);
                } catch (JWTException $e) {
                    $user->api_token = NULL;
                    $user->save();
                    return $this->respondWithError("Login Unsuccessful. An error occurred while performing an action!");
                }
            } else {
                return $this->respondWithError("Invalid User!");
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
                $ticket_data = "SELECT
                                                    ticket_id as tID, 
                                                    ticket_no as ticket, 
                                                    created_at as datetime,
                                                    DATE_FORMAT(created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format
                                                FROM
                                                    tickets
                                                WHERE
                                                    status = '1' AND
                                                    user_id = '" . $user_id . "'
                                                LIMIT 0,1
                                                ";
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

                $languages = DB::table('languages')->select('id','language')->where('status', 1)->get();

                $data['languages']=array();


                $get_deptartment_permission = DB::table('app_menu_permissions')
                        ->select('app_menu_permissions.app_menu_id as mID', 'app_menus.menu as Item', 'app_menus.slag as tagName', 'app_menus.image as tag_icon', DB::raw("'" . $base_url . "/' as base_url"))
                        ->leftJoin('app_menus', 'app_menus.menu_id', '=', 'app_menu_permissions.app_menu_id')
                        ->where('app_menu_permissions.department', $user_type)
                        ->where('app_menu_permissions.status', '1')
                        ->where('app_menus.status', '1')
                        ->get();

                if ($get_user_permission) {
                    $data = array('menulist' => $get_user_permission, 'ticketdata' => $result);
                } else {
                    $data = array('menulist' => $get_deptartment_permission, 'ticketdata' => $result);
                }

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
                $ticket_data = "SELECT
                                                    ticket_id as tID, 
                                                    ticket_no as ticket, 
                                                    created_at as datetime,
                                                    DATE_FORMAT(created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format
                                                FROM
                                                    tickets
                                                WHERE
                                                    status = '1' AND
                                                    user_id = '" . $user_id . "'
                                                LIMIT 0,1
                                                ";
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

                $get_deptartment_permission = DB::table('app_menu_permissions')
                        ->select('app_menu_permissions.app_menu_id as mID', 'app_menus.menu as Item', 'app_menus.slag as tagName', 'app_menus.image as tag_icon', DB::raw("'" . $base_url . "/' as base_url"))
                        ->leftJoin('app_menus', 'app_menus.menu_id', '=', 'app_menu_permissions.app_menu_id')
                        ->where('app_menu_permissions.department', $user_type)
                        ->where('app_menu_permissions.status', '1')
                        ->where('app_menus.status', '1')
                        ->get();



                $languages = DB::table('languages')->select('id','language')->where('status', 1)->get();

                $data['languages']=array();

                
                if ($get_user_permission) {
                    $data = array('menulist' => $get_user_permission, 'ticketdata' => $result);
                } else {
                    $data = array('menulist' => $get_deptartment_permission, 'ticketdata' => $result);
                }

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

    private function categoryDepartmentWiseBdoList($key = '1', $value = '1') {
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
                                (CASE WHEN user_deputations.status='1' THEN 1 ELSE 0 END) as deputation_status
                            FROM        users
                            LEFT JOIN   user_deputations ON user_deputations.user_id=users.id
                            WHERE users.status='1'    AND users." . $key . "='" . $value . "'
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
                            WHERE P.product_id<>'' AND P.product<>'' and PCR.status=1 ". $where ."
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
                            WHERE C.crop_id<>'' AND C.crop<>''
                            GROUP BY C.crop_id
                            ORDER BY C.crop ASC";
        $crop_detail = DB::select(DB::raw($crop_query));
        $crop_list = array_map(function ($value) {
            return (array) $value;
        }, $crop_detail);
        return $crop_list;
    }

    public function s1PlanActivity($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;


                switch ($user_type) {
                    case "zonal" : $bdo_list = $this->categoryDepartmentWiseBdoList('zonal_manager_id', $user_id);
                        $product_list = $this->categoryDepartmentWiseProductList('zonal_manager_product_categories');
                        $crop_list = $this->categoryDepartmentWiseCropList('zonal_manager_product_categories');
                        break;
                    case "regional" : $bdo_list = $this->categoryDepartmentWiseBdoList('regional_manager_id', $user_id);
                        $product_list = $this->categoryDepartmentWiseProductList('regional_manager_product_categories');
                        $crop_list = $this->categoryDepartmentWiseCropList('regional_manager_product_categories');
                        break;
                    case "area" : $bdo_list = $this->categoryDepartmentWiseBdoList('area_manager_id', $user_id);
                        $product_list = $this->categoryDepartmentWiseProductList('area_manager_product_categories');
                        $crop_list = $this->categoryDepartmentWiseCropList('area_manager_product_categories');
                        break;
                    case "sales_regional" : $bdo_list = $this->categoryDepartmentWiseBdoList('sales_regional_manager_id', $user_id);
                        $product_list = $this->categoryDepartmentWiseProductList('sales_regional_manager_product_categories');
                        $crop_list = $this->categoryDepartmentWiseCropList('sales_regional_manager_product_categories');
                        break;
                    case "sales_area" : $bdo_list = $this->categoryDepartmentWiseBdoList('sales_area_manager_id', $user_id);
                        $product_list = $this->categoryDepartmentWiseProductList('sales_area_manager_product_categories');
                        $crop_list = $this->categoryDepartmentWiseCropList('sales_area_manager_product_categories');
                        break;
                    case "bdo" : $bdo_list = $this->categoryDepartmentWiseBdoList('id', $user_id);

                        $product_list = $this->categoryDepartmentWiseProductList('user_product_categories','user_id',$user_id);

                        $crop_list = $this->categoryDepartmentWiseCropList('user_product_categories');
                        break;
                        
                    case "dhanuka_doctor" : $bdo_list = $this->categoryDepartmentWiseBdoList('id', $user_id);

                        $product_list = $this->categoryDepartmentWiseProductList('dd_product_categories','dd_id',$user_id);

                        $crop_list = $this->categoryDepartmentWiseCropList('dd_product_categories');
                        break;
                }

                $data = array('bdolist' => $bdo_list, 'productlist' => $product_list, 'croplist' => $crop_list);
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Plan Activity Initiated',
                            'data' => $data
                ]);
            } else {

            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s1createPlanActivity(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'apiToken' => 'required|string',
                'planData' => 'array'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $planData = $data['planData'];
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $userId = $user[0]->id;
                    $userType = $user[0]->user_type;
                    if (count($planData) > 0) {

                        $valid_user_plan = array();
                        $current_month = date('m');
                        $current_year = date('Y');
                        $uniqueBDOids = array_unique(array_map(function ($i) {
                                    return $i['bdo_id'];
                                }, $planData));
                        $uniqueMonth = array_unique(array_map(function ($i) {
                                    return $i['month'];
                                }, $planData));
                        foreach ($planData as $plan) {
                            $user_id = $plan['bdo_id'];
                            $month = $plan['month'];
                            $year = $plan['year'];
                            $product_id = $plan['product_id'];
                            $crop_id = $plan['crop_id'];
                            $days = $plan['days'];
                            $dd = $plan['dd'];
                            $record_id = $plan['record_id'];
                            $status = '0';
                            $created_by_user_id = $userId;
                            $user_type = $userType;
                            $user = User::where('id', $user_id)->where('status', '1')->first();

                            $product = Product::where('product_id', $product_id)->where('status', '1')->first();

                            $crop = Crop::where('crop_id', $crop_id)->where('status', '1')->first();

                            if ($user && $product && $crop) {
                                if ($month >= $current_month && $year == $current_year) {

                                    $activity_exists = PlanActivities::where('user_id', $user_id)->where('month', $month)->where('year', $year)->where('product_id', $product_id)->where('crop_id', $crop_id)->where('is_deleted', '0')->first();
                                    


                                    if ($activity_exists) {
                                        $user_plan = array(
                                            'user_id' => $user_id,
                                            'month' => $month,
                                            'year' => $year,
                                            'product_id' => $product_id,
                                            'crop_id' => $crop_id,
                                            'days' => $activity_exists['days']+$days,
                                            'dd' => $dd,
                                            'record_id' => $record_id,
                                            'status' => $status,
                                            'created_by_user_id' => $created_by_user_id,
                                            'user_type' => $user_type
                                        );
                                        $update_user_plan = PlanActivities::where('user_id', $user_id)->where('month', $month)->where('year', $year)->where('product_id', $product_id)->where('crop_id', $crop_id)->where('is_deleted', '0')->update(['days' =>$activity_exists['days']+ $days, 'dd' => $dd, 'created_at' => date('Y-m-d H:i:s')]);
                                        if ($update_user_plan) {
                                            $valid_user_plan[] = $user_plan;
                                        }
                                    } else {
                                        $user_plan = array(
                                            'user_id' => $user_id,
                                            'month' => $month,
                                            'year' => $year,
                                            'product_id' => $product_id,
                                            'crop_id' => $crop_id,
                                            'days' => $days,
                                            'dd' => $dd,
                                            'record_id' => $record_id,
                                            'status' => $status,
                                            'created_by_user_id' => $created_by_user_id,
                                            'user_type' => $user_type
                                        );
                                        $insert_user_plan = PlanActivities::firstOrCreate($user_plan);
                                        if ($insert_user_plan) {
                                            $valid_user_plan[] = $user_plan;
                                        }
                                    }
                                }

                                else if (($month == '1' || $month == '01') && ($year > $current_year)) {
                                    $activity_exists = PlanActivities::where('user_id', $user_id)->where('month', $month)->where('year', $year)->where('product_id', $product_id)->where('crop_id', $crop_id)->where('is_deleted', '0')->first();
                                    if ($activity_exists) {
                                        $user_plan = array(
                                            'user_id' => $user_id,
                                            'month' => $month,
                                            'year' => $year,
                                            'product_id' => $product_id,
                                            'crop_id' => $crop_id,
                                            'days' =>$activity_exists['days']+ $days,
                                            'dd' => $dd,
                                            'record_id' => $record_id,
                                            'status' => $status,
                                            'created_by_user_id' => $created_by_user_id,
                                            'user_type' => $user_type
                                        );
                                        $update_user_plan = PlanActivities::where('user_id', $user_id)->where('month', $month)->where('year', $year)->where('product_id', $product_id)->where('crop_id', $crop_id)->where('is_deleted', '0')->update(['days' =>$activity_exists['days']+ $days, 'dd' => $dd, 'created_at' => date('Y-m-d H:i:s')]);
                                        if ($update_user_plan) {
                                            $valid_user_plan[] = $user_plan;
                                        }
                                    } else {
                                        $user_plan = array(
                                            'user_id' => $user_id,
                                            'month' => $month,
                                            'year' => $year,
                                            'product_id' => $product_id,
                                            'crop_id' => $crop_id,
                                            'days' => $days,
                                            'dd' => $dd,
                                            'record_id' => $record_id,
                                            'status' => $status,
                                            'created_by_user_id' => $created_by_user_id,
                                            'user_type' => $user_type
                                        );
                                        $insert_user_plan = PlanActivities::firstOrCreate($user_plan);
                                        if ($insert_user_plan) {
                                            $valid_user_plan[] = $user_plan;
                                        }
                                    }
                                }

                                else {

                                }
                            }
                        }
                        if (count($valid_user_plan) > 0) {
                            foreach ($uniqueBDOids as $bdo) {
                                $user_data = User::where('id', $bdo)->select('device_id')->first();
                                if ($user_data && $user_data->device_id != '') {
                                    $manager_details = $this->findUserDetails($userType, $api_token);
                                    $data = array(
                                        'device_id' => $user_data->device_id,
                                        'notification_title' => "New Syllabus",
                                        'notifiaction_mesaage' => $manager_details[0]->manager_name . " assigned you a syllabus."
                                    );
                                    $this->dispatch(new SendNotification($data));
                                }
                            }

                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Plan Successfully Uploaded!',
                                        'data' =>   $valid_user_plan,
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

    public function s1bdoPlanList($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                switch ($user_type) {
                    case "zonal" : $where = " U.zonal_manager_id='" . $user_id . "' ";
                        break;
                    case "regional" : $where = " U.regional_manager_id='" . $user_id . "' ";
                        break;
                    case "area" : $where = " U.area_manager_id='" . $user_id . "' ";
                        break;
                    case "sales_regional" : $where = " U.sales_regional_manager_id='" . $user_id . "' ";
                        break;
                    case "sales_area" : $where = " U.sales_area_manager_id='" . $user_id . "' ";
                        break;
                    case "bdo" : $where = " U.id='" . $user_id . "' ";
                        break;
                    default : $where = " 1='1'";
                }

                $current_month_plan_atp_query = "
                                                    SELECT MONTH(CURRENT_DATE()) AS month, U.id, U.name, COUNT(plan_atp_id) AS activites
                                                    FROM users              U
                                                    LEFT JOIN plan_atp          PA  ON PA.created_by_user_id=U.id
                                                    LEFT JOIN products      P   ON PA.product_id=P.product_id
                                                    LEFT JOIN crops     C   ON PA.crop_id=C.crop_id
                                                    LEFT JOIN data_sources  DS  ON PA.data_source_id=DS.data_source_id
                                                    LEFT JOIN states        S   ON PA.state_id=S.state_id
                                                    LEFT JOIN districts     D   ON PA.district_id=D.district_id
                                                    LEFT JOIN tehsils       T   ON PA.tehsil_id=T.tehsil_id
                                                    WHERE
                                                        " . $where . "
                                                        AND PA.is_deleted<>'1'
                                                        AND MONTH(PA.plan_date)=MONTH(CURRENT_DATE())
                                                        AND YEAR(PA.plan_date)=YEAR(CURRENT_DATE())
                                                    GROUP BY
                                                        U.id
                                                    ORDER BY 
                                                        U.name ASC
                                                    ";
                $current_month_atp_detail = DB::select(DB::raw($current_month_plan_atp_query));
                $current_month_plan_atp_list = array_map(function ($value) {
                    return (array) $value;
                }, $current_month_atp_detail);
                if (date('m') != "12") {
                    $next_month_plan_atp_query = "
                                                        SELECT MONTH(CURRENT_DATE())+1 AS month, U.id, U.name, COUNT(plan_atp_id) AS activites
                                                        FROM users              U
                                                        LEFT JOIN plan_atp          PA  ON PA.created_by_user_id=U.id
                                                        LEFT JOIN products      P   ON PA.product_id=P.product_id
                                                        LEFT JOIN crops     C   ON PA.crop_id=C.crop_id
                                                        LEFT JOIN data_sources  DS  ON PA.data_source_id=DS.data_source_id
                                                        LEFT JOIN states        S   ON PA.state_id=S.state_id
                                                        LEFT JOIN districts     D   ON PA.district_id=D.district_id
                                                        LEFT JOIN tehsils       T   ON PA.tehsil_id=T.tehsil_id
                                                        WHERE
                                                            " . $where . "
                                                            AND PA.is_deleted<>'1'
                                                            AND MONTH(PA.plan_date)=MONTH(CURRENT_DATE())+1
                                                            AND YEAR(PA.plan_date)=YEAR(CURRENT_DATE())
                                                        GROUP BY
                                                            U.id
                                                        ORDER BY 
                                                            U.name ASC
                                                        ";
                    $next_month_atp_detail = DB::select(DB::raw($next_month_plan_atp_query));
                    $next_month_plan_atp_list = array_map(function ($value) {
                        return (array) $value;
                    }, $next_month_atp_detail);
                } else if (date('m') == "12") {
                    $next_month_plan_atp_query = "
                                                        SELECT MONTH(CURRENT_DATE())-11 AS month, U.id, U.name, COUNT(plan_atp_id) AS activites
                                                        FROM users              U
                                                        LEFT JOIN plan_atp          PA  ON PA.created_by_user_id=U.id
                                                        LEFT JOIN products      P   ON PA.product_id=P.product_id
                                                        LEFT JOIN crops     C   ON PA.crop_id=C.crop_id
                                                        LEFT JOIN data_sources  DS  ON PA.data_source_id=DS.data_source_id
                                                        LEFT JOIN states        S   ON PA.state_id=S.state_id
                                                        LEFT JOIN districts     D   ON PA.district_id=D.district_id
                                                        LEFT JOIN tehsils       T   ON PA.tehsil_id=T.tehsil_id
                                                        WHERE
                                                            " . $where . "
                                                            AND PA.is_deleted<>'1'
                                                            AND MONTH(PA.plan_date)='1'
                                                            AND YEAR(PA.plan_date)=YEAR(CURRENT_DATE())+1
                                                        GROUP BY
                                                            U.id
                                                        ORDER BY 
                                                            U.name ASC
                                                        ";
                    $next_month_atp_detail = DB::select(DB::raw($next_month_plan_atp_query));
                    $next_month_plan_atp_list = array_map(function ($value) {
                        return (array) $value;
                    }, $next_month_atp_detail);
                } else {
                    $next_month_plan_atp_list = "";
                }

                $data = array('current_month_plan_atp_list' => $current_month_plan_atp_list, 'next_month_plan_atp_list' => $next_month_plan_atp_list);
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Plan Found!',
                            'data' => $data
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s1bdoPlanListDetail($api_token = '', $bdo_id) {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                $plan_atp_query = "
                                    SELECT PA.plan_atp_id, U.id, U.name, P.product, PA.other_crop_1, PA.other_crop_2, C.crop, DS.data_source, S.state, D.district, T.tehsil, PA.village, PA.expected_farmer, PA.expected_pda, PA.plan_date
                                    FROM plan_atp               PA
                                    LEFT JOIN users             U   ON PA.created_by_user_id=U.id 
                                    LEFT JOIN products          P   ON PA.product_id=P.product_id
                                    LEFT JOIN crops             C   ON PA.crop_id=C.crop_id
                                    LEFT JOIN data_sources      DS  ON PA.data_source_id=DS.data_source_id
                                    LEFT JOIN states            S   ON PA.state_id=S.state_id
                                    LEFT JOIN districts         D   ON PA.district_id=D.district_id
                                    LEFT JOIN tehsils           T   ON PA.tehsil_id=T.tehsil_id
                                    WHERE
                                        PA.created_by_user_id='" . $bdo_id . "' AND PA.is_deleted<>'1'
                                    ORDER BY 
                                        U.name ASC
                                    ";
                $atp_detail = DB::select(DB::raw($plan_atp_query));
                $plan_atp_list = array_map(function ($value) {
                    return (array) $value;
                }, $atp_detail);
                if ($plan_atp_list) {
                    $data = array('plan_atp_detail' => $plan_atp_list);
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Plan Detail Found!',
                                'data' => $data
                    ]);
                } else {
                    return $this->respondWithError("Data Item Not Found!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s1OpenTicket($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                switch ($user_type) {
                    case "zonal" : $table = "zonal_manager_product_categories";
                        $key = "ampc.zonal_manager_id";
                        break;
                    case "regional" : $table = "regional_manager_product_categories";
                        $key = "ampc.regional_manager_id";
                        break;
                    case "area" : $table = "area_manager_product_categories";
                        $key = "ampc.area_manager_id";
                        break;
                    case "sales_regional" : $table = "sales_regional_manager_product_categories";
                        $key = "ampc.sales_regional_manager_id";
                        break;
                    case "sales_area" : $table = "sales_area_manager_product_categories";
                        $key = "ampc.sales_area_manager_id";
                        break;
                    case "bdo" : $table = "user_product_categories";
                        $key = "ampc.user_id";
                        break;
                    case "dhanuka_doctor" : $table = "dd_product_categories";
                        $key = "ampc.dd_id";
                        break;
                }

                $data_source_list = DataSource::where('status', '1')->select('data_source_id as dsID', 'data_source as Item')->orderBy('data_source', 'ASC')->get()->toArray();
                $state_list = State::where('status', '1')->select('state_id as sID', 'state as Item')->orderBy('state', 'ASC')->get()->toArray();
                $product_list = DB::table($table . ' AS ampc')
                        ->select('p.product_id as pID', 'p.product as Item')
                        ->join('product_crop_relations AS pcr', 'ampc.product_crop_category_id', '=', 'pcr.product_crop_category_id')
                        ->leftJoin('products AS p', 'pcr.product_id', '=', 'p.product_id')
                        ->where('p.status', '1')
                        ->where($key, $user_id)
                        ->groupBy('p.product')
                        ->get();
                $crop_list = DB::table($table . ' AS ampc')
                        ->select('c.crop_id as cID', 'c.crop as Item')
                        ->join('product_crop_relations AS pcr', 'ampc.product_crop_category_id', '=', 'pcr.product_crop_category_id')
                        ->leftJoin('crops AS c', 'pcr.crop_id', '=', 'c.crop_id')
                        ->where('c.status', '1')
                        ->where($key, $user_id)
                        ->groupBy('c.crop')
                        ->get();
                $data = array('data_source_list' => $data_source_list, 'state_list' => $state_list, 'product_list' => $product_list, 'crop_list' => $crop_list);
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Syllabus Found!',
                            'data' => $data
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function getdistrict(Request $request) {
        $data = $request->json()->all();
        $rules = array(
            'apiToken' => 'required|string',
            'sId' => 'required|numeric',
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

                $app_version = isset($data['app_version']) ? $data['app_version'] : '';
                $app_version_data = array(
                    'user_id' => $user_id,
                    'user_type' => $user_type,
                    'app_version' => $app_version
                );
                $insert_version = AppVersion::firstOrCreate($app_version_data);

                try {
                    $state_id = $data['sId'];
                    $district_list = District::where(array('status' => '1', 'state_id' => $state_id))->select('district_id as dID', 'district as Item')->get()->toArray();
                    if (count($district_list) > 0) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'District List Found!',
                                    'data' => $district_list,
                        ]);
                    } else {
                        return $this->respondWithError("Data Item Not Found!");
                    }
                } catch (Exception $e) {
                    return $this->respondInternalError("An error occurred while performing an action!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        }
    }

    public function getstate(Request $request) {
        $data = $request->json()->all();
        $rules = array(
            'apiToken' => 'required|string',
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

                $app_version = isset($data['app_version']) ? $data['app_version'] : '';
                $app_version_data = array(
                    'user_id' => $user_id,
                    'user_type' => $user_type,
                    'app_version' => $app_version
                );
                $insert_version = AppVersion::firstOrCreate($app_version_data);

                try {
                    
                    $state_list = State::where(array('status' => '1'))->select('state_id as sId', 'state as Item')->get()->toArray();
                    if (count($state_list) > 0) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'State List Found!',
                                    'data' => $state_list
                                    ,
                        ]);
                    } else {
                        return $this->respondWithError("Data Item Not Found!");
                    }
                } catch (Exception $e) {
                    return $this->respondInternalError("An error occurred while performing an action!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        }
    }

    public function gettehsil(Request $request) {
        $data = $request->json()->all();
        $rules = array(
            'apiToken' => 'required|string',
            'dId' => 'required|numeric',
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
                try {
                    $district_id = $data['dId'];
                    $tehsil_list = Tehsil::where(array('status' => '1', 'district_id' => $district_id))->select('tehsil_id as tID', 'tehsil as Item')->get()->toArray();
                    if (count($tehsil_list) > 0) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Tehsil List Found!',
                                    'data' => $tehsil_list,
                        ]);
                    } else {
                        return $this->respondWithError("Data Item Not Found!");
                    }
                } catch (Exception $e) {
                    return $this->respondInternalError("An error occurred while performing an action!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        }
    }

    public function s1createTicket(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'api_token' => 'required|string',
                'dsId' => 'required|numeric',
                'sId' => 'required|numeric',
                'dId' => 'required|numeric',
                'tId' => 'required|numeric',
                'village' => 'string',
                'expected_farmers' => 'numeric',
                'expected_pda' => 'numeric',
                'product_1' => 'numeric',
                'crop_1' => 'numeric',
                'product_2' => 'numeric',
                'crop_2' => 'numeric',
                'product_3' => 'numeric',
                'crop_3' => 'numeric',
                'sp_name_1' => 'string',
                'sp_number_1' => 'numeric',
                'sp_name_2' => 'string',
                'sp_number_2' => 'numeric',
                'sp_name_3' => 'string',
                'sp_number_3' => 'numeric',
                'sp_name_4' => 'string',
                'sp_number_4' => 'numeric',
                'sp_name_5' => 'string',
                'sp_number_5' => 'numeric',
                'sp_name_6' => 'string',
                'sp_number_6' => 'numeric'
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

                    Ticket::where('user_id', $user_id)->update(['status' => '0']);

                    $count_ticket = Ticket::count();
                    $ticket_no = ($count_ticket == 0 ? 1001 : 2001 + $count_ticket);
                    $ticket_no = "S" . $ticket_no;

                    $validator = Validator::make(['ticket_no' => $ticket_no], ['ticket_no' => 'unique:tickets,ticket_no']);
                    if ($validator->fails()) {
                        $this->randomId();
                    }
                    try {
                        Ticket::create([
                            'ticket_no' => $ticket_no,
                            'user_id' => $user_id,
                            'user_type' => $user_type,
                            'data_source_id' => $data['dsId'],
                            'state_id' => $data['sId'],
                            'district_id' => $data['dId'],
                            'tehsil_id' => $data['tId'],
                            'language_id' => isset($data['language_id']) ? $data['language_id'] : 0 ,
                            'village' => $data['village'],
                            'expected_farmers' => $data['expected_farmers'],
                            'expected_pda' => $data['expected_pda'],
                            'product_1_id' => $data['product_1'],
                            'crop_1_id' => $data['crop_1'],
                            'product_2_id' => $data['product_2'],
                            'crop_2_id' => $data['crop_2'],
                            'product_3_id' => $data['product_3'],
                            'crop_3_id' => $data['crop_3'],
                            'sp_name_1' => $data['sp_name_1'],
                            'sp_number_1' => $data['sp_number_1'],
                            'sp_name_2' => $data['sp_name_2'],
                            'sp_number_2' => $data['sp_number_2'],
                            'sp_name_3' => $data['sp_name_3'],
                            'sp_number_3' => $data['sp_number_3'],
                            'sp_name_4' => $data['sp_name_4'],
                            'sp_number_4' => $data['sp_number_4'],
                            'sp_name_5' => $data['sp_name_5'],
                            'sp_number_5' => $data['sp_number_5'],
                            'sp_name_6' => $data['sp_name_6'],
                            'sp_number_6' => $data['sp_number_6'],
                            'status' => '1'
                        ]);
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Ticket Successfully Created!',
                                    'data' => $ticket_no,
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

    public function s1tickethistory($api_token = '', $offset = 0) {
        try {
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


                    $base_url = url('/');
                    $query = "
                                    SELECT 
                                        t.ticket_id as tID,
                                        t.ticket_no as ticket,
                                        t.created_at as datetime,
                                        DATE_FORMAT(t.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format ,
                                        CONCAT_WS(', ', d.data_source, s.state, sd.district, sdt.tehsil) AS description,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id) AS total_farmers,
                                        (SELECT COUNT(f.farmer_id) FROM farmers f WHERE f.ticket_status='1' AND f.ticket_id=t.ticket_id AND f.validation_status = 'Validated') AS total_valid_farmers,
                                        d.data_source,
                                        s.state,
                                        sd.district,
                                        sdt.tehsil,
                                        t.village,
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
                                        lan.language,
                                        '" . $base_url . "/' as base_url,
                                        t.uploaded_file, 
                                        (CASE WHEN t.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status
                                    FROM tickets t
                                    LEFT JOIN data_sources  d   ON t.data_source_id=d.data_source_id
                                    LEFT JOIN languages  lan   ON lan.id=t.language_id

                                    LEFT JOIN states        s   ON t.state_id=s.state_id
                                    LEFT JOIN districts     sd  ON t.district_id=sd.district_id
                                    LEFT JOIN tehsils       sdt ON t.tehsil_id=sdt.tehsil_id
                                    LEFT JOIN products      p1  ON t.product_1_id=p1.product_id
                                    LEFT JOIN products      p2  ON t.product_2_id=p2.product_id
                                    LEFT JOIN products      p3  ON t.product_3_id=p3.product_id
                                    LEFT JOIN crops         c1  ON t.crop_1_id=c1.crop_id
                                    LEFT JOIN crops         c2  ON t.crop_2_id=c2.crop_id
                                    LEFT JOIN crops         c3  ON t.crop_3_id=c3.crop_id
                                    WHERE 
                                        t.user_id='" . $user_id . "'        AND
                                        t.user_type='" . $user_type . "'    
                                    GROUP BY  
                                        t.ticket_id
                                    ORDER BY
                                        t.ticket_id DESC
                                    LIMIT
                                        " . $offset . ", 50    
                                    ";


                    $ticket_history = DB::select(DB::raw($query));
                    $result = array_map(function ($value) {
                        return (array) $value;
                    }, $ticket_history);

                    if ($ticket_history) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Ticket History Found!',
                                    'data' => $result,
                        ]);
                    } else {
                        return $this->respondNotFound("No Record Found!");
                    }
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s1ticketdetail(Request $request) {
        $data = $request->json()->all();
        $rules = array(
            'api_token' => 'required|string',
            'tId' => 'required|numeric',
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

                $ticket_id = $data['tId'];
                $base_url = url('/');
                $query = "
                            SELECT 
                                d.data_source,
                                s.state,
                                sd.district,
                                sdt.tehsil,
                                t.village,
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

                                '" . $base_url . "/' as base_url,
                                t.uploaded_file, 
                                (CASE WHEN t.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status
                            FROM tickets t
                            LEFT JOIN data_sources  d   ON t.data_source_id=d.data_source_id
                            LEFT JOIN states        s   ON t.state_id=s.state_id
                            LEFT JOIN districts     sd  ON t.district_id=sd.district_id
                            LEFT JOIN tehsils       sdt ON t.tehsil_id=sdt.tehsil_id

                            LEFT JOIN products      p1  ON t.product_1_id=p1.product_id
                            LEFT JOIN products      p2  ON t.product_2_id=p2.product_id
                            LEFT JOIN products      p3  ON t.product_3_id=p3.product_id

                            LEFT JOIN crops         c1  ON t.crop_1_id=c1.crop_id
                            LEFT JOIN crops         c2  ON t.crop_2_id=c2.crop_id
                            LEFT JOIN crops         c3  ON t.crop_3_id=c3.crop_id

                            WHERE 
                                t.ticket_id='" . $ticket_id . "'    AND
                                t.user_id='" . $user_id . "'        AND
                                t.user_type='" . $user_type . "'    
                            ";
                $ticket_detail = DB::select(DB::raw($query));
                $result = array_map(function ($value) {
                    return (array) $value;
                }, $ticket_detail);
                if ($result) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Ticket Details Successfully Found!',
                                'data' => $result,
                    ]);
                } else {
                    return $this->respondWithError("No Details Found!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        }
    }

    public function s1editticket(Request $request) {
        $data = $request->json()->all();
        $rules = array(
            'api_token' => 'required|string',
            'tId' => 'required|numeric',
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
                switch ($user_type) {
                    case "zonal" : $table = "zonal_manager_product_categories";
                        $key = "ampc.zonal_manager_id";
                        break;
                    case "regional" : $table = "regional_manager_product_categories";
                        $key = "ampc.regional_manager_id";
                        break;
                    case "area" : $table = "area_manager_product_categories";
                        $key = "ampc.area_manager_id";
                        break;
                    case "sales_regional" : $table = "sales_regional_manager_product_categories";
                        $key = "ampc.sales_regional_manager_id";
                        break;
                    case "sales_area" : $table = "sales_area_manager_product_categories";
                        $key = "ampc.sales_area_manager_id";
                        break;
                    case "bdo" : $table = "user_product_categories";
                        $key = "ampc.user_id";
                        break;
                }
                if ($user_type == 'bdo') {
                    $bdo = User::where('api_token', $data['api_token'])->first();
                    if ($bdo->app_permission_edit_ticket == '1') {
                        $ticket_id = $data['tId'];

                        $ticket_ckeck = Ticket::where('ticket_id', $ticket_id)->where('user_id', $user_id)->first();
                        if ($ticket_ckeck) {
                            if ($ticket_ckeck->status != '1') {
                                return $this->respondWithError("Permission Denied. Ticket Closed!");
                            } else {
                                $data_source_list = DataSource::where('status', '1')->select('data_source_id as dsID', 'data_source as Item')->get()->toArray();
                                $state_list = State::where('status', '1')->select('state_id as sID', 'state as Item')->get()->toArray();
                                $district_list = District::where('status', '1')->where('state_id', $ticket_ckeck->state_id)->select('district_id as dID', 'district as Item')->get()->toArray();
                                $tehsil_list = Tehsil::where('status', '1')->where('district_id', $ticket_ckeck->district_id)->select('tehsil_id as tID', 'tehsil as Item')->get()->toArray();
                                $product_list = DB::table($table . ' AS ampc')
                                        ->select('p.product_id as pID', 'p.product as Item')
                                        ->join('product_crop_relations AS pcr', 'ampc.product_crop_category_id', '=', 'pcr.product_crop_category_id')
                                        ->leftJoin('products AS p', 'pcr.product_id', '=', 'p.product_id')
                                        ->where('p.status', '1')
                                        ->where($key, $user_id)
                                        ->groupBy('p.product')
                                        ->get();
                                $crop_list = DB::table($table . ' AS ampc')
                                        ->select('c.crop_id as cID', 'c.crop as Item')
                                        ->join('product_crop_relations AS pcr', 'ampc.product_crop_category_id', '=', 'pcr.product_crop_category_id')
                                        ->leftJoin('crops AS c', 'pcr.crop_id', '=', 'c.crop_id')
                                        ->where('c.status', '1')
                                        ->where($key, $user_id)
                                        ->groupBy('c.crop')
                                        ->get();

                                $base_url = url('/');
                                $query = "
                                            SELECT 
                                                (CASE WHEN t.plan_atp_id='' THEN '0' ELSE '1' END) as isATP,
                                                d.data_source,
                                                s.state,
                                                sd.district,
                                                sdt.tehsil,
                                                t.village,
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

                                                t.data_source_id,
                                                t.state_id,
                                                t.district_id,
                                                t.tehsil_id,
                                                t.product_1_id  AS product_1_id,
                                                t.product_2_id  AS product_2_id,
                                                t.product_3_id  AS product_3_id,
                                                t.crop_1_id     AS crop_1_id,
                                                t.crop_2_id     AS crop_2_id,
                                                t.crop_3_id     AS crop_3_id,
                                                t.language_id,
                                                lan.language,

                                                '" . $base_url . "/' as base_url,
                                                t.uploaded_file, 
                                                (CASE WHEN t.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status
                                            FROM tickets t
                                            LEFT JOIN data_sources  d   ON t.data_source_id=d.data_source_id
                                            LEFT JOIN states        s   ON t.state_id=s.state_id
                                            LEFT JOIN districts     sd  ON t.district_id=sd.district_id
                                            LEFT JOIN tehsils       sdt ON t.tehsil_id=sdt.tehsil_id

                                            LEFT JOIN products      p1  ON t.product_1_id=p1.product_id
                                            LEFT JOIN products      p2  ON t.product_2_id=p2.product_id
                                            LEFT JOIN products      p3  ON t.product_3_id=p3.product_id

                                            LEFT JOIN crops         c1  ON t.crop_1_id=c1.crop_id
                                            LEFT JOIN crops         c2  ON t.crop_2_id=c2.crop_id
                                            LEFT JOIN crops         c3  ON t.crop_3_id=c3.crop_id
                                            LEFT JOIN languages  lan   ON lan.id=t.language_id

                                            WHERE
                                                t.ticket_id='" . $ticket_id . "'    AND
                                                t.user_id='" . $user_id . "'        AND
                                                t.user_type='" . $user_type . "' 
                                            ";
                                $ticket_detail = DB::select(DB::raw($query));
                                $result = array_map(function ($value) {
                                    return (array) $value;
                                }, $ticket_detail);
                                if ($result) {
                                    if ((count($data_source_list) > 0) && (count($state_list) > 0) && (count($product_list) > 0) && (count($crop_list) > 0)) {
                                        $data_array = array(
                                            'data_source' => $data_source_list,
                                            'state_list' => $state_list,
                                            'district_list' => $district_list,
                                            'tehsil_list' => $tehsil_list,
                                            'product_list' => $product_list,
                                            'crop_list' => $crop_list,
                                            'ticket_data' => $result
                                        );
                                        return $this->respond([
                                                    'status' => 'success',
                                                    'status_code' => $this->getStatusCode(),
                                                    'message' => 'Ticket Details Successfully Found!',
                                                    'data' => $data_array,
                                        ]);
                                    } else {
                                        return $this->respondWithError("Data Item Not Found!");
                                    }
                                } else {
                                    return $this->respondWithError("No Details Found!");
                                }
                            }
                        } else {
                            return $this->respondWithError("Invalid Ticket!");
                        }
                    } else {
                        return $this->respondWithError("You dont have permission to edit tickets!");
                    }
                } else {
                    $ticket_id = $data['tId'];

                    $ticket_ckeck = Ticket::where('ticket_id', $ticket_id)->where('user_id', $user_id)->first();
                    if ($ticket_ckeck) {
                        if ($ticket_ckeck->status != '1') {
                            return $this->respondWithError("Permission Denied. Ticket Closed!");
                        } else {
                            $data_source_list = DataSource::where('status', '1')->select('data_source_id as dsID', 'data_source as Item')->get()->toArray();
                            $state_list = State::where('status', '1')->select('state_id as sID', 'state as Item')->get()->toArray();
                            $district_list = District::where('status', '1')->where('state_id', $ticket_ckeck->state_id)->select('district_id as dID', 'district as Item')->get()->toArray();
                            $tehsil_list = Tehsil::where('status', '1')->where('district_id', $ticket_ckeck->district_id)->select('tehsil_id as tID', 'tehsil as Item')->get()->toArray();
                            $product_list = Product::where('status', '1')->select('product_id as pID', 'product as Item')->get()->toArray();
                            $crop_list = Crop::where('status', '1')->select('crop_id as cID', 'crop as Item')->get()->toArray();
                            $base_url = url('/');
                            $query = "
                                        SELECT 
                                            (CASE WHEN t.plan_atp_id='' THEN '0' ELSE '1' END) as isATP,
                                            d.data_source,
                                            s.state,
                                            sd.district,
                                            sdt.tehsil,
                                            t.village,
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

                                            t.data_source_id,
                                            t.state_id,
                                            t.district_id,
                                            t.tehsil_id,
                                            t.product_1_id  AS product_1_id,
                                            t.product_2_id  AS product_2_id,
                                            t.product_3_id  AS product_3_id,
                                            t.crop_1_id     AS crop_1_id,
                                            t.crop_2_id     AS crop_2_id,
                                            t.crop_3_id     AS crop_3_id,
                                            t.language_id,
                                            lan.language,

                                            '" . $base_url . "/' as base_url,
                                            t.uploaded_file, 
                                            (CASE WHEN t.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as ticket_status
                                        FROM tickets t
                                        LEFT JOIN data_sources  d   ON t.data_source_id=d.data_source_id
                                        LEFT JOIN states        s   ON t.state_id=s.state_id
                                        LEFT JOIN districts     sd  ON t.district_id=sd.district_id
                                        LEFT JOIN tehsils       sdt ON t.tehsil_id=sdt.tehsil_id

                                        LEFT JOIN products      p1  ON t.product_1_id=p1.product_id
                                        LEFT JOIN products      p2  ON t.product_2_id=p2.product_id
                                        LEFT JOIN products      p3  ON t.product_3_id=p3.product_id

                                        LEFT JOIN crops         c1  ON t.crop_1_id=c1.crop_id
                                        LEFT JOIN crops         c2  ON t.crop_2_id=c2.crop_id
                                        LEFT JOIN crops         c3  ON t.crop_3_id=c3.crop_id
                                         LEFT JOIN languages  lan   ON lan.id=t.language_id

                                        WHERE
                                            t.ticket_id='" . $ticket_id . "'    AND
                                            t.user_id='" . $user_id . "'        AND
                                            t.user_type='" . $user_type . "' 
                                        ";
                            $ticket_detail = DB::select(DB::raw($query));
                            $result = array_map(function ($value) {
                                return (array) $value;
                            }, $ticket_detail);
                            if ($result) {
                                if ((count($data_source_list) > 0) && (count($state_list) > 0) && (count($product_list) > 0) && (count($crop_list) > 0)) {
                                    $data_array = array(
                                        'data_source' => $data_source_list,
                                        'state_list' => $state_list,
                                        'district_list' => $district_list,
                                        'tehsil_list' => $tehsil_list,
                                        'product_list' => $product_list,
                                        'crop_list' => $crop_list,
                                        'ticket_data' => $result
                                    );
                                    return $this->respond([
                                                'status' => 'success',
                                                'status_code' => $this->getStatusCode(),
                                                'message' => 'Ticket Details Successfully Found!',
                                                'data' => $data_array,
                                    ]);
                                } else {
                                    return $this->respondWithError("Data Item Not Found!");
                                }
                            } else {
                                return $this->respondWithError("No Details Found!");
                            }
                        }
                    } else {
                        return $this->respondWithError("Invalid Ticket!");
                    }
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        }
    }

    public function s1updateticket(Request $request) {
        $data = $request->json()->all();
        $rules = array(
            'api_token' => 'required|string',
            'ticId' => 'required|numeric',
            'dsId' => 'required|numeric',
            'sId' => 'required|numeric',
            'dId' => 'required|numeric',
            'tId' => 'required|numeric',
            'expected_farmers' => 'numeric',
            'expected_pda' => 'numeric',
            'village' => 'string',
            'product_1' => 'numeric',
            'crop_1' => 'numeric',
            'product_2' => 'numeric',
            'crop_2' => 'numeric',
            'product_3' => 'numeric',
            'crop_3' => 'numeric',
            'sp_name_1' => 'string',
            'sp_number_1' => 'string',
            'sp_name_2' => 'string',
            'sp_number_2' => 'string',
            'sp_name_3' => 'string',
            'sp_number_3' => 'string',
            'sp_name_4' => 'string',
            'sp_number_4' => 'string',
            'sp_name_5' => 'string',
            'sp_number_5' => 'string',
            'sp_name_6' => 'string',
            'sp_number_6' => 'string'
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
                if ($user_type == 'bdo') {
                    $bdo = User::where('api_token', $data['api_token'])->first();

                    if ($bdo->app_permission_edit_ticket == '1') {
                        $ticket_ckeck = Ticket::where('ticket_id', $data['ticId'])->where('user_id', $user_id)->first();
                        if ($ticket_ckeck) {
                            if ($ticket_ckeck->status != '1') {
                                return $this->respondWithError("Permission Denied. Ticket Closed!");
                            }
                        } else {
                            return $this->respondWithError("Invalid Ticket!");
                        }

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

                        try {
                            $result = Ticket::where('ticket_id', $data['ticId'])->update([
                                'user_id' => $user_id,
                                'data_source_id' => $data['dsId'],
                                'state_id' => $data['sId'],
                                'district_id' => $data['dId'],
                                'tehsil_id' => $data['tId'],
                                'language_id' => isset($data['language_id']) ? $data['language_id'] :0,
                                'village' => $data['village'],
                                'expected_farmers' => $data['expected_farmers'],
                                'expected_pda' => $data['expected_pda'],
                                'product_1_id' => $data['product_1'],
                                'crop_1_id' => $data['crop_1'],
                                'product_2_id' => $data['product_2'],
                                'crop_2_id' => $data['crop_2'],
                                'product_3_id' => $data['product_3'],
                                'crop_3_id' => $data['crop_3'],
                                'crop_3_id' => $data['crop_3'],
                                'sp_name_1' => $data['sp_name_1'],
                                'sp_number_1' => $data['sp_number_1'],
                                'sp_name_2' => $data['sp_name_2'],
                                'sp_number_2' => $data['sp_number_2'],
                                'sp_name_3' => $data['sp_name_3'],
                                'sp_number_3' => $data['sp_number_3'],
                                'sp_name_4' => $data['sp_name_4'],
                                'sp_number_4' => $data['sp_number_4'],
                                'sp_name_5' => $data['sp_name_5'],
                                'sp_number_5' => $data['sp_number_5'],
                                'sp_name_6' => $data['sp_name_6'],
                                'sp_number_6' => $data['sp_number_6'],
                                'status' => '1',
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                            if ($result) {
                                $ticket_no = $ticket_ckeck->ticket_no;
                                return $this->respond([
                                            'status' => 'success',
                                            'status_code' => $this->getStatusCode(),
                                            'message' => 'Ticket Successfully Updated!',
                                            'data' => $ticket_no,
                                ]);
                            } else {
                                return $this->respondInternalError("An error occurred while performing an action!");
                            }
                        } catch (Exception $e) {
                            return $this->respondInternalError("An error occurred while performing an action!");
                        }
                    } else {
                        return $this->respondWithError("You dont have permission to edit tickets!");
                    }
                } else {
                    $ticket_ckeck = Ticket::where('ticket_id', $data['ticId'])->where('user_id', $user_id)->first();
                    if ($ticket_ckeck) {
                        if ($ticket_ckeck->status != '1') {
                            return $this->respondWithError("Permission Denied. Ticket Closed!");
                        }
                    } else {
                        return $this->respondWithError("Invalid Ticket!");
                    }

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


                    try {
                        $result = Ticket::where('ticket_id', $data['ticId'])->update([
                            'user_id' => $user_id,
                            'data_source_id' => $data['dsId'],
                            'state_id' => $data['sId'],
                            'district_id' => $data['dId'],
                            'language_id' => $data['language_id'],
                            'tehsil_id' => $data['tId'],
                            'village' => $data['village'],
                            'expected_farmers' => $data['expected_farmers'],
                            'expected_pda' => $data['expected_pda'],
                            'product_1_id' => $data['product_1'],
                            'crop_1_id' => $data['crop_1'],
                            'product_2_id' => $data['product_2'],
                            'crop_2_id' => $data['crop_2'],
                            'product_3_id' => $data['product_3'],
                            'crop_3_id' => $data['crop_3'],
                            'crop_3_id' => $data['crop_3'],
                            'sp_name_1' => $data['sp_name_1'],
                            'sp_number_1' => $data['sp_number_1'],
                            'sp_name_2' => $data['sp_name_2'],
                            'sp_number_2' => $data['sp_number_2'],
                            'sp_name_3' => $data['sp_name_3'],
                            'sp_number_3' => $data['sp_number_3'],
                            'sp_name_4' => $data['sp_name_4'],
                            'sp_number_4' => $data['sp_number_4'],
                            'sp_name_5' => $data['sp_name_5'],
                            'sp_number_5' => $data['sp_number_5'],
                            'sp_name_6' => $data['sp_name_6'],
                            'sp_number_6' => $data['sp_number_6'],
                            'status' => '1',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        if ($result) {
                            $ticket_no = $ticket_ckeck->ticket_no;
                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Ticket Successfully Updated!',
                                        'data' => $ticket_no,
                            ]);
                        } else {
                            return $this->respondInternalError("An error occurred while performing an action!");
                        }
                    } catch (Exception $e) {
                        return $this->respondInternalError("An error occurred while performing an action!");
                    }
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        }
    }

    public function s1updateticketimage(Request $request) {
        $data = $request->all();
        $rules = array(
            'api_token' => 'required|string',
            'tId' => 'required|string',
            'photo' => 'required|max:300000|mimes:jpg,png,jpeg'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['api_token'];
            $ticket_id = $data['tId'];
            $file = $request->file('photo');
            $latitude = (isset($data['latitude']) ? $data['latitude'] : '');
            $longitude = (isset($data['longitude']) ? $data['longitude'] : '');

            if ($request->hasFile('photo')) {
                if ($request->file('photo')->isValid()) {
                    $user = $this->findUser($api_token);
                    if ($user && count($user) == '1') {
                        $user_id = $user[0]->id;
                        $user_type = $user[0]->user_type;
                        $ticket_check = Ticket::where('ticket_id', $ticket_id)->first();
                        if ($ticket_check) {
                            /*
                              print_r(get_class_methods($file));
                              print_r($file->getClientOriginalName());
                              exit;
                             */
                            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $fileName = "F_" . $ticket_check->ticket_no . "_" . md5(time() . rand()) . str_replace($file->getClientOriginalName(), '', $file->getClientOriginalName()) . '.' . $extension;
                            $destinationPath = 'assets/tickets/';
                            $sTempFileName = 'assets/tickets/' . $fileName;
                            $request->file('photo')->move($destinationPath, $fileName);

                            $updateImage = Ticket::where('ticket_id', $ticket_id)->update(['uploaded_file' => $sTempFileName, 'latitude' => $latitude, 'longitude' => $longitude]);

                            if ($updateImage) {
                                $base_url = url('/');
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

    public function s1closeticket(Request $request) {
        $data = $request->json()->all();
        $rules = array(
            'api_token' => 'required|string',
            'tId' => 'required|numeric',
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

                if ($user_type == 'bdo') {
                    $bdo = User::where('api_token', $data['api_token'])->first();
                    if ($bdo->app_permission_edit_ticket == '1') {
                        $ticket_id = $data['tId'];
                        $ticket_check = Ticket::where('ticket_id', $ticket_id)->first();
                        if ($ticket_check) {
                            $data = array('ticket_number' => $ticket_check->ticket_no);
                            $result = Ticket::where('ticket_id', $ticket_id)->update(['status' => '0','closing_datetime'=>Carbon::now()]);
                            if ($result) {
                                return $this->respond([
                                            'status' => 'success',
                                            'status_code' => $this->getStatusCode(),
                                            'message' => 'Ticket Successfully Closed!',
                                            'data' => $data,
                                ]);
                            } else {
                                return $this->respondInternalError("An error occurred while performing an action!");
                            }
                        } else {
                            return $this->respondWithError("No Ticket Found!");
                        }
                    } else {
                        return $this->respondWithError("You dont have permission to close tickets!");
                    }
                } else {
                    $ticket_id = $data['tId'];
                    $ticket_check = Ticket::where('ticket_id', $ticket_id)->first();
                    if ($ticket_check) {
                        $data = array('ticket_number' => $ticket_check->ticket_no);
                        $result = Ticket::where('ticket_id', $ticket_id)->update(['status' => '0','closing_datetime'=>Carbon::now()]);
                        if ($result) {
                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Ticket Successfully Closed!',
                                        'data' => $data,
                            ]);
                        } else {
                            return $this->respondInternalError("An error occurred while performing an action!");
                        }
                    } else {
                        return $this->respondWithError("No Ticket Found!");
                    }
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        }
    }

    public function s1openregisterfarmer($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $query = "
                                    SELECT
                                        ut.ticket_id as tID, 
                                        ut.ticket_no as ticket_no, 
                                        ut.created_at as datetime,
                                        DATE_FORMAT(ut.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format 
                                    FROM        tickets         ut
                                    WHERE
                                        ut.user_id      = '" . $user_id . "'    AND
                                        ut.user_type    = '" . $user_type . "'  AND
                                        ut.status       = '1'
                                    ";
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
                                            user_id             = '" . $user_id . "'            AND
                                            user_type           = '" . $user_type . "'          AND
                                            ticket_id           = '" . $result[0]['tID'] . "'   AND
                                            registration_type   = 'manual'
                                        ";
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

    public function s1setregisterfarmer(Request $request) {
        $data = $request->json()->all();
        if ($data) {
            $rules = array(
                'apiToken' => 'required|string',
                'contactModelList' => 'required|array'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $farmer_data = $data['contactModelList'];
                $invalid_ticket=array();
                $user = $this->findUser($api_token);

                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    $farmer_data_count = 0;
                    $duplicate_number = array();
                    $added_no = array();
                    $modified_no = array();
                    $mobile_number_array = array();
                    $invalid_ticket = array();
                    $count = 0;



                    foreach ($farmer_data as $farmerData) {
                        $count++;
                        $row_id = $farmerData['id'];
                        $ticket_id = $farmerData['ticketNumber'];
                        $farmer_number = ((strlen($farmerData['phoneNumber']) > 10) ? substr($farmerData['phoneNumber'], -10) : $farmerData['phoneNumber']);
                        $timestamp = $farmerData['timestamp'];

                        $type = ($farmerData['type'] == 'm' ? 'manual' : 'missed');
                        $isDeleted = ($farmerData['isDeleted'] == '1' ? '0' : '1');

                        $ticket_check = Ticket::where('ticket_id', $ticket_id)->where('user_type', $user_type)->first();
                      
                        if ($ticket_check) {







                            $p_query = "SELECT mobile FROM farmers WHERE    created_at >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)";


                            $prev_reg_farmer = DB::select(DB::raw($p_query));

                            $p_result = array_map(function ($value) {
                                return (array) $value;
                            }, $prev_reg_farmer);


                            if ($p_result) {
                                if ($p_result[0]['mobile'] != '') {
                                    $mobile_number_array = array_column($p_result, 'mobile');
                                }
                            }



                            if (in_array($farmer_number, $mobile_number_array)) {

                                $current_ticket_status = Farmer::where('ticket_id', $ticket_id)
                                        ->where('user_id', $user_id)
                                        ->where('user_type', $user_type)
                                        ->where('mobile', $farmer_number)
                                        ->count();

                                if ($current_ticket_status > 0) {
                                    Farmer::where('ticket_id', $ticket_id)
                                            ->where('user_id', $user_id)
                                            ->where('user_type', $user_type)
                                            ->where('mobile', $farmer_number)
                                            ->update(['ticket_status' => $isDeleted]);
                                            
                                    $modified_no[] = $farmer_number;
                                    $mobile_number_array[] = $farmer_number;
                                } else if ($current_ticket_status == 0) {
                                    $ticket_status = Farmer::where('mobile', $farmer_number)->orderBy('ticket_status', 'DESC')->first();
                                    if ($ticket_status) {
                                        if ($ticket_status->ticket_status == 0) {
                                            $farmer_data_array = array(
                                                'ticket_id' => $ticket_id,
                                                'user_id' => $user_id,
                                                'user_type' => $user_type,
                                                'mobile' => $farmer_number,
                                                'datetime' => $timestamp,
                                                'registration_type' => $type,
                                                'ticket_status' => $isDeleted
                                            );
                                            $result = Farmer::firstOrNew($farmer_data_array);
                                            $result = $result->save();
                                            $mobile_number_array[] = $farmer_number;
                                            $added_no[] = $farmer_number;
                                            $farmer_data_count++;
                                        } else {
                                            $duplicate_number[] = $farmer_number;
                                        }
                                    }
                                } else {
                                    $duplicate_number[] = $farmer_number;
                                }
                            } else {
                                $farmer_data_array = array(
                                    'ticket_id' => $ticket_id,
                                    'user_id' => $user_id,
                                    'user_type' => $user_type,
                                    'mobile' => $farmer_number,
                                    'datetime' => $timestamp,
                                    'registration_type' => $type,
                                    'ticket_status' => $isDeleted
                                );
                                $result = Farmer::firstOrNew($farmer_data_array);
                                $result = $result->save();
                                $mobile_number_array[] = $farmer_number;
                                $added_no[] = $farmer_number;
                                $farmer_data_count++;
                            }
                        } else {
                            $invalid_ticket[] = $row_id;
                        }


                        $farmer_qry = "
                                            SELECT 
                                                COUNT(DISTINCT(f.mobile))  AS farmer_count
                                            FROM 
                                                farmers f 
                                            WHERE f.ticket_id='" . $ticket_id . "' AND f.ticket_status='1'
                                            ";
                        $farmer_list = DB::select(DB::raw($farmer_qry));
                        $farmer_result = array_map(function ($value) {
                            return (array) $value;
                        }, $farmer_list);
                        if ($farmer_result) {
                            if ($farmer_result[0]['farmer_count'] != '') {
                                $farmer_count = $farmer_result[0]['farmer_count'];
                                Ticket::where(['ticket_id' => $ticket_id])->update(['farmer_count' => $farmer_count]);
                            }
                        }
                    }


                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'ErrorMessage' => 'Farmers with '.implode(',', $invalid_ticket)." not registred",
                                'message' => 'Farmers Successfully Registered!',
                    ]);
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
        } else {
            return $this->respondWithError("Invalid Request !!");
        }
    }

    private function categoryWiseBdoList($key = '1', $value = '1') {
        $bdo_query = " SELECT 
                                users.id AS bdo_id,
                                users.name AS bdo_name
                            FROM        users
                            WHERE users.status='1'    AND users." . $key . "='" . $value . "'
                            ORDER BY users.name ASC";
        $bdo_detail = DB::select(DB::raw($bdo_query));
        $bdo_list = array_map(function ($value) {
            return (array) $value;
        }, $bdo_detail);
        return $bdo_list;
    }

    private function categoryWiseBdoListForQuery($key = '1', $value = '1') {
        $bdo_query = " SELECT 
                            GROUP_CONCAT(CONCAT('''', users.id, '''' )) AS user_id
                        FROM    users
                        WHERE   users.status='1' AND users." . $key . "='" . $value . "' LIMIT 0,1 ";
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $bdo_detail = DB::select(DB::raw($bdo_query));
        $bdo_list = array_map(function ($value) {
            return (array) $value;
        }, $bdo_detail);
        return $bdo_list;
    }

    public function s1selfParticipationOpen($api_token = '', $manager_id = '', $manager_level = '') {
        try {
            $user = $this->findUser($api_token);


            if ($user && count($user) == '1') {
                $usertype = $user[0]->user_type;
                if (($manager_id != '' && $manager_id != '0')) {
                    $user_id = $manager_id;
                } else {
                    $user_id = $user[0]->id;
                }
                if ($manager_level != '' && $manager_level != '0') {
                    $user_type = strtolower($manager_level);
                } else {
                    $user_type = $user[0]->user_type;
                }


                $base_url = url('/');
                switch ($user_type) {
                    case "zonal" : $bdo_list = $this->categoryWiseBdoList('zonal_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                        $where = " U.zonal_manager_id='" . $user_id . "' ";
                        break;
                    case "regional" : $bdo_list = $this->categoryWiseBdoList('regional_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                        $where = " U.regional_manager_id='" . $user_id . "' ";
                        break;
                    case "area" : $bdo_list = $this->categoryWiseBdoList('area_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                        $where = " U.area_manager_id='" . $user_id . "' ";
                        break;
                    case "sales_regional" : $bdo_list = $this->categoryWiseBdoList('sales_regional_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                        $where = " U.sales_regional_manager_id='" . $user_id . "' ";
                        break;
                    case "sales_area" : $bdo_list = $this->categoryWiseBdoList('sales_area_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                        $where = " U.sales_area_manager_id='" . $user_id . "' ";
                        break;
                    case "bdo" : $bdo_list = $this->categoryWiseBdoList('id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                        $where = " U.id='" . $user_id . "' ";
                        break;
                }
                $users = (isset($bdo_list_query[0]['user_id']) ? trim($bdo_list_query[0]['user_id'], "'") : '');
                if ($users != '') {
                    $query = "
                                    SELECT
                                        u.name,
                                        ut.ticket_id as tID, 
                                        ut.ticket_no as ticket, 
                                        DATE_FORMAT(ut.created_at, '%h:%i %p') as datetime_format,

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
                                        sp.description
                                    FROM        tickets             ut

                                    LEFT JOIN   data_sources        ds  ON ut.data_source_id=ds.data_source_id
                                    LEFT JOIN   states              s   ON ut.state_id=s.state_id
                                    LEFT JOIN   districts           d   ON ut.district_id=d.district_id
                                    LEFT JOIN   tehsils             t   ON ut.tehsil_id=t.tehsil_id
                                    LEFT JOIN   users               u   ON ut.user_id=u.id

                                    LEFT JOIN   products        p1  ON ut.product_1_id=p1.product_id
                                    LEFT JOIN   products        p2  ON ut.product_2_id=p2.product_id
                                    LEFT JOIN   products        p3  ON ut.product_3_id=p3.product_id

                                    LEFT JOIN   crops       c1  ON ut.crop_1_id=c1.crop_id
                                    LEFT JOIN   crops       c2  ON ut.crop_2_id=c2.crop_id
                                    LEFT JOIN   crops       c3  ON ut.crop_3_id=c3.crop_id

                                    LEFT JOIN   self_participations sp  ON sp.ticket_id=ut.ticket_id

                                    WHERE
                                        ut.status='1' AND
                                        ut.user_id          IN ('" . $users . "')       AND
                                        ut.user_type        = 'bdo'                 AND
                                        MONTH(ut.created_at)= MONTH(CURRENT_DATE()) AND 
                                        YEAR(ut.created_at) = YEAR(CURRENT_DATE())
                                    ORDER BY
                                        ut.ticket_id DESC
                                    ";

                    $open_ticket = DB::select(DB::raw($query));
                    $result = array_map(function ($value) {
                        return (array) $value;
                    }, $open_ticket);


                    $check_active_plans_query = "
                                                        SELECT
                                                            (CASE WHEN Count(self_participation_plan_id)>0 THEN 1 ELSE 0 END) AS active_plans
                                                        FROM
                                                            self_participation_plans
                                                        WHERE
                                                            user_id='" . $user_id . "' 
                                                            AND user_type='" . $user_type . "'
                                                            AND status='1'
                                                            AND
                                                                (
                                                                (MONTH(plan_date)=MONTH(CURRENT_DATE())-1 AND YEAR(plan_date)=YEAR(CURRENT_DATE())) OR
                                                                (DATE(plan_date)<DATE(CURRENT_DATE()) AND MONTH(plan_date)=MONTH(CURRENT_DATE()) AND YEAR(plan_date)=YEAR(CURRENT_DATE()))
                                                                )
                                                        ";
                    $check_plans = DB::select(DB::raw($check_active_plans_query));
                    $plan_result = array_map(function ($value) {
                        return (array) $value;
                    }, $check_plans);

                    $current_month_plan_atp_query = "
                                                            SELECT
                                                                PA.plan_atp_id,
                                                                U.name,
                                                                DATE_FORMAT(PA.plan_date, '%W, %M %e %Y') as datetime_format,
                                                                DS.data_source, 
                                                                S.state, 
                                                                D.district, 
                                                                T.tehsil,
                                                                PA.village,                                                        
                                                                P.product  AS product_1,
                                                                C.crop     AS crop_1,
                                                                (CASE WHEN SPA.status='1' THEN 1  WHEN SPA.status='2' THEN 2 ELSE 0 END) as plan_atp_status
                                                            FROM users                          U
                                                            LEFT JOIN plan_atp                  PA  ON PA.created_by_user_id=U.id
                                                            LEFT JOIN products                  P   ON PA.product_id=P.product_id
                                                            LEFT JOIN crops                     C   ON PA.crop_id=C.crop_id
                                                            LEFT JOIN data_sources              DS  ON PA.data_source_id=DS.data_source_id
                                                            LEFT JOIN states                    S   ON PA.state_id=S.state_id
                                                            LEFT JOIN districts                 D   ON PA.district_id=D.district_id
                                                            LEFT JOIN tehsils                   T   ON PA.tehsil_id=T.tehsil_id
                                                            LEFT JOIN self_participation_plans  SPA ON PA.plan_atp_id=SPA.plan_atp_id AND SPA.user_id='" . $user_id . "' AND SPA.user_type='" . $user_type . "'
                                                            WHERE
                                                                U.id IN ('" . $users . "')  
                                                                AND PA.is_deleted<>'1'
                                                                AND MONTH(PA.plan_date)=MONTH(CURRENT_DATE())
                                                                AND YEAR(PA.plan_date)=YEAR(CURRENT_DATE())
                                                            ORDER BY 
                                                                U.name ASC
                                                            ";
                    $atp_list = DB::select(DB::raw($current_month_plan_atp_query));
                    $atp_result = array_map(function ($value) {
                        return (array) $value;
                    }, $atp_list);

                    $data = array('bdo_list' => $bdo_list, 'ticket_list' => $result, 'atp_list' => $atp_result, 'is_pending_plan' => $plan_result);
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'List Item Found!',
                                'data' => $data
                    ]);
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

    public function s1selfParticipationOpenNew($api_token = '', $manager_id = '', $manager_level = '') {
        try {
            $user = $this->findUser($api_token);


            if ($user && count($user) == '1') {
                $usertype = $user[0]->user_type;
                if (($manager_id != '' && $manager_id != '0')) {
                    $user_id = $manager_id;
                } else {
                    $user_id = $user[0]->id;
                }
                if ($manager_level != '' && $manager_level != '0') {
                    $user_type = strtolower($manager_level);
                } else {
                    $user_type = $user[0]->user_type;
                }


                $base_url = url('/');
                switch ($user_type) {
                    case "zonal" : $bdo_list = $this->categoryWiseBdoList('zonal_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                        $where = " U.zonal_manager_id='" . $user_id . "' ";
                        break;
                    case "regional" : $bdo_list = $this->categoryWiseBdoList('regional_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                        $where = " U.regional_manager_id='" . $user_id . "' ";
                        break;
                    case "area" : $bdo_list = $this->categoryWiseBdoList('area_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                        $where = " U.area_manager_id='" . $user_id . "' ";
                        break;
                    case "sales_regional" : $bdo_list = $this->categoryWiseBdoList('sales_regional_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                        $where = " U.sales_regional_manager_id='" . $user_id . "' ";
                        break;
                    case "sales_area" : $bdo_list = $this->categoryWiseBdoList('sales_area_manager_id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                        $where = " U.sales_area_manager_id='" . $user_id . "' ";
                        break;
                    case "bdo" : $bdo_list = $this->categoryWiseBdoList('id', $user_id);
                        $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                        $where = " U.id='" . $user_id . "' ";
                        break;
                }
                $users = (isset($bdo_list_query[0]['user_id']) ? trim($bdo_list_query[0]['user_id'], "'") : '');
                if ($users != '') {
                    $query = "
                                SELECT
                                    u.name,
                                    ut.ticket_id as tID, 
                                    ut.ticket_no as ticket, 
                                    DATE_FORMAT(ut.created_at, '%h:%i %p') as datetime_format,

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
                                    c3.crop     AS crop_3
                                FROM        tickets             ut

                                LEFT JOIN   data_sources        ds  ON ut.data_source_id=ds.data_source_id
                                LEFT JOIN   states              s   ON ut.state_id=s.state_id
                                LEFT JOIN   districts           d   ON ut.district_id=d.district_id
                                LEFT JOIN   tehsils             t   ON ut.tehsil_id=t.tehsil_id
                                LEFT JOIN   users               u   ON ut.user_id=u.id

                                LEFT JOIN   products        p1  ON ut.product_1_id=p1.product_id
                                LEFT JOIN   products        p2  ON ut.product_2_id=p2.product_id
                                LEFT JOIN   products        p3  ON ut.product_3_id=p3.product_id

                                LEFT JOIN   crops       c1  ON ut.crop_1_id=c1.crop_id
                                LEFT JOIN   crops       c2  ON ut.crop_2_id=c2.crop_id
                                LEFT JOIN   crops       c3  ON ut.crop_3_id=c3.crop_id

                                WHERE
                                    ut.status='1' AND
                                    ut.user_id          IN ('" . $users . "')       AND
                                    ut.user_type        = 'bdo'                 AND
                                    MONTH(ut.created_at)= MONTH(CURRENT_DATE()) AND 
                                    YEAR(ut.created_at) = YEAR(CURRENT_DATE())
                                ORDER BY
                                    ut.ticket_id DESC
                                ";

                
                    $open_ticket = DB::select(DB::raw($query));
                    $result = array_map(function ($value) {
                        return (array) $value;
                    }, $open_ticket);


                    $check_active_plans_query = "
                                                    SELECT
                                                        (CASE WHEN Count(self_participation_plan_id)>0 THEN 1 ELSE 0 END) AS active_plans
                                                    FROM
                                                        self_participation_plans
                                                    WHERE
                                                        user_id='" . $user_id . "' 
                                                        AND user_type='" . $user_type . "'
                                                        AND status='1'
                                                        AND
                                                            (
                                                            (MONTH(plan_date)=MONTH(CURRENT_DATE())-1 AND YEAR(plan_date)=YEAR(CURRENT_DATE())) OR
                                                            (DATE(plan_date)<DATE(CURRENT_DATE()) AND MONTH(plan_date)=MONTH(CURRENT_DATE()) AND YEAR(plan_date)=YEAR(CURRENT_DATE()))
                                                            )
                                                    ";
                    $check_plans = DB::select(DB::raw($check_active_plans_query));
                    $plan_result = array_map(function ($value) {
                        return (array) $value;
                    }, $check_plans);


                    $check_active_plans_query_previous = "
                                                    SELECT
                                                        (CASE WHEN Count(self_participation_plan_id)>0 THEN 1 ELSE 0 END) AS active_plans
                                                    FROM
                                                        self_participation_plans as SPA

                                                       LEFT JOIN plan_atp                  PA  ON PA.plan_atp_id=PA.plan_atp_id

                                                        LEFT JOIN users U               ON PA.created_by_user_id=U.id


                                                    WHERE
                                                        SPA.user_id='" . $user_id . "' and 
                                                        U.status='1' AND
                                                        U.id IN ('" . $users . "') 
                                                        AND SPA.user_type='" . $user_type . "' 
                                                        AND PA.is_deleted<>'1'
                                                        AND SPA.status='1'
                                                        AND
                                                            (
                                                            (MONTH(SPA.plan_date)=MONTH(CURRENT_DATE())-1 AND YEAR(SPA.plan_date)=YEAR(CURRENT_DATE())) OR
                                                            (DATE(SPA.plan_date)<DATE(CURRENT_DATE()) AND MONTH(SPA.plan_date)=MONTH(CURRENT_DATE()) AND YEAR(SPA.plan_date)=YEAR(CURRENT_DATE()))
                                                            )
                                                    ";
                    $check_plans_previous = DB::select(DB::raw($check_active_plans_query_previous));
                    $plan_result_previous = array_map(function ($value) {
                        return (array) $value;
                    }, $check_plans_previous);




                    $current_month_plan_atp_query = "
                                                        SELECT
                                                            PA.plan_atp_id,
                                                            U.name,
                                                            DATE_FORMAT(PA.plan_date, '%W, %M %e %Y') as datetime_format,
                                                            DS.data_source, 
                                                            S.state, 
                                                            D.district, 
                                                            T.tehsil,
                                                            PA.village,                                                        
                                                            P.product  AS product_1,
                                                            C.crop     AS crop_1,
                                                            (CASE WHEN SPA.status='1' THEN 1  WHEN SPA.status='2' THEN 2 ELSE 0 END) as plan_atp_status
                                                        FROM users                          U
                                                        LEFT JOIN plan_atp                  PA  ON PA.created_by_user_id=U.id
                                                        LEFT JOIN products                  P   ON PA.product_id=P.product_id
                                                        LEFT JOIN crops                     C   ON PA.crop_id=C.crop_id
                                                        LEFT JOIN data_sources              DS  ON PA.data_source_id=DS.data_source_id
                                                        LEFT JOIN states                    S   ON PA.state_id=S.state_id
                                                        LEFT JOIN districts                 D   ON PA.district_id=D.district_id
                                                        LEFT JOIN tehsils                   T   ON PA.tehsil_id=T.tehsil_id
                                                        LEFT JOIN self_participation_plans  SPA ON PA.plan_atp_id=SPA.plan_atp_id AND SPA.user_id='" . $user_id . "' AND SPA.user_type='" . $user_type . "'
                                                        WHERE
                                                            U.id IN ('" . $users . "')  
                                                            AND PA.is_deleted<>'1'
                                                            AND MONTH(PA.plan_date)>=MONTH(CURRENT_DATE())
                                                            AND YEAR(PA.plan_date)=YEAR(CURRENT_DATE())
                                                        ORDER BY 
                                                            U.name ASC,
                                                            PA.plan_date ASC
                                                        ";
                    $atp_list = DB::select(DB::raw($current_month_plan_atp_query));
                    $atp_result = array_map(function ($value) {
                        return (array) $value;
                    }, $atp_list);

                    $data = array('bdo_list' => $bdo_list, 'ticket_list' => $result, 'atp_list' => $atp_result, 'is_pending_plan' => $plan_result,
                        'is_pending_plan_previous' =>$plan_result_previous);
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'List Item Found!',
                                'data' => $data
                    ]);
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

    public function s1selfParticipationDetail($api_token = '', $ticket_id = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = strtolower($user[0]->user_type);

                $base_url = url('/');
                $query = "
                                SELECT
                                    u.name,
                                    ut.ticket_id as tID, 
                                    ut.ticket_no as ticket, 
                                    DATE_FORMAT(ut.created_at, '%h:%i %p') as datetime_format,

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
                                    c3.crop     AS crop_3

                                FROM        tickets         ut                                
                                LEFT JOIN   data_sources    ds  ON ut.data_source_id=ds.data_source_id
                                LEFT JOIN   states          s   ON ut.state_id=s.state_id
                                LEFT JOIN   districts       d   ON ut.district_id=d.district_id
                                LEFT JOIN   tehsils         t   ON ut.tehsil_id=t.tehsil_id
                                LEFT JOIN   users           u   ON ut.user_id=u.id

                                LEFT JOIN   products        p1  ON ut.product_1_id=p1.product_id
                                LEFT JOIN   products        p2  ON ut.product_2_id=p2.product_id
                                LEFT JOIN   products        p3  ON ut.product_3_id=p3.product_id

                                LEFT JOIN   crops           c1  ON ut.crop_1_id=c1.crop_id
                                LEFT JOIN   crops           c2  ON ut.crop_2_id=c2.crop_id
                                LEFT JOIN   crops           c3  ON ut.crop_3_id=c3.crop_id

                                WHERE
                                    ut.status='1' AND
                                    ut.ticket_id        = '" . $ticket_id . "'       AND
                                    MONTH(ut.created_at)= MONTH(CURRENT_DATE()) AND 
                                    YEAR(ut.created_at) = YEAR(CURRENT_DATE())
                                ORDER BY
                                    ut.ticket_id DESC
                                ";

                $open_ticket = DB::select(DB::raw($query));
                $result = array_map(function ($value) {
                    return (array) $value;
                }, $open_ticket);


                $query1 = "
                                SELECT
                                    '" . $base_url . "/' as base_url,
                                    sp.uploaded_file,
                                    sp.description
                                FROM    self_participations sp  
                                WHERE
                                    sp.ticket_id        = '" . $ticket_id . "'       AND
                                    sp.user_id          = '" . $user_id . "'         AND
                                    sp.user_type        = '" . $user_type . "'
                                ";
                $open_ticket1 = DB::select(DB::raw($query1));
                $result1 = array_map(function ($value) {
                    return (array) $value;
                }, $open_ticket1);

                if (count($result1) == 0) {
                    $result[0]['base_url'] = $base_url;
                    $result[0]['uploaded_file'] = '';
                    $result[0]['description'] = '';
                } else {
                    $result[0]['base_url'] = $base_url . "/";
                    $result[0]['uploaded_file'] = $result1[0]['uploaded_file'];
                    $result[0]['description'] = $result1[0]['description'];
                }
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'List Item Found!',
                            'data' => $result
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s1selfParticipationPlan(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken' => 'required|string',
            'atp_id' => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $atp_id = $data['atp_id'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $find_atp = PlanAtp::where(['plan_atp_id' => $atp_id, 'is_deleted' => '0'])->first();
                if ($find_atp) {
                    $data_array = array(
                        'plan_atp_id' => $atp_id,
                        'user_id' => $user_id,
                        'user_type' => $user_type,
                        'status' => '1',
                        'plan_date' => $find_atp->plan_date
                    );
                    $result = SelfParticipationPlan::firstOrNew($data_array);
                    $result = $result->save();
                    if ($result) {
                        $data = array('plan_atp_id' => $atp_id, 'status' => '1');
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Self Participation Plan Successfully Created!',
                                    'data' => $data,
                        ]);
                    } else {
                        return $this->respondInternalError("An error occurred while performing an action!");
                    }
                } else {
                    return $this->respondWithError("No ATP Found!");
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function s1selfParticipationReason(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken' => 'required|string',
            'atp_id' => 'required|string',
            'reason' => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $atp_id = $data['atp_id'];
            $reason = $data['reason'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $update_self_participation_reason = SelfParticipationPlan::where(['plan_atp_id' => $atp_id, 'user_type' => $user_type, 'user_id' => $user_id])->update(['reason' => $reason, 'status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_self_participation_reason) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Reason Successfully Updated!',
                    ]);
                } else {
                    return $this->respondInternalError("An error occurred while performing an action!");
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function s1selfParticipationPrevious($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $base_url = url('/');
                switch ($user_type) {
                    case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                        break;
                    case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                        break;
                    case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                        break;
                    case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                        break;
                    case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                        break;
                    case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                        break;
                }
                $users = (isset($bdo_list_query[0]['user_id']) ? trim($bdo_list_query[0]['user_id'], "'") : '');
                if ($users != '') {
                    $current_month_plan_atp_query = "
                                                        SELECT
                                                            PA.plan_atp_id,
                                                            U.name,
                                                            DATE_FORMAT(PA.plan_date, '%W, %M %e %Y') as datetime_format,
                                                            DS.data_source, 
                                                            S.state, 
                                                            D.district, 
                                                            T.tehsil,
                                                            PA.village,                                                        
                                                            P.product  AS product_1,
                                                            C.crop     AS crop_1,
                                                            (CASE WHEN SPA.status='1' THEN 1  WHEN SPA.status='2' THEN 2 ELSE 0 END) as plan_atp_status
                                                        FROM users                          U
                                                        LEFT JOIN plan_atp                  PA  ON PA.created_by_user_id=U.id
                                                        LEFT JOIN products                  P   ON PA.product_id=P.product_id
                                                        LEFT JOIN crops                     C   ON PA.crop_id=C.crop_id
                                                        LEFT JOIN data_sources              DS  ON PA.data_source_id=DS.data_source_id
                                                        LEFT JOIN states                    S   ON PA.state_id=S.state_id
                                                        LEFT JOIN districts                 D   ON PA.district_id=D.district_id
                                                        LEFT JOIN tehsils                   T   ON PA.tehsil_id=T.tehsil_id
                                                        LEFT JOIN self_participation_plans  SPA ON PA.plan_atp_id=SPA.plan_atp_id AND SPA.user_id='" . $user_id . "' AND SPA.user_type='" . $user_type . "'
                                                        WHERE
                                                            U.id IN ('" . $users . "') 
                                                            AND PA.is_deleted<>'1'
                                                            AND SPA.status='1'
                                                            AND (MONTH(PA.plan_date)=MONTH(CURRENT_DATE())-1 AND YEAR(PA.plan_date)=YEAR(CURRENT_DATE()))
                                                        ORDER BY 
                                                            U.name ASC
                                                        ";

                    $atp_list = DB::select(DB::raw($current_month_plan_atp_query));
                    $atp_result = array_map(function ($value) {
                        return (array) $value;
                    }, $atp_list);

                    if ($atp_result) {
                        $data = array('atp_list' => $atp_result);
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'List Item Found!',
                                    'data' => $data
                        ]);
                    } else {
                        return $this->respondWithError("No Active Ticket Found!");
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

    public function s1selfParticipationPreviousNew($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $base_url = url('/');
                switch ($user_type) {
                    case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                        break;
                    case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                        break;
                    case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                        break;
                    case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                        break;
                    case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                        break;
                    case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                        break;
                }
                $users = (isset($bdo_list_query[0]['user_id']) ? trim($bdo_list_query[0]['user_id'], "'") : '');
                if ($users != '') {
                    $current_month_plan_atp_query = "
                                                        SELECT
                                                            PA.plan_atp_id,
                                                            U.name,
                                                            DATE_FORMAT(PA.plan_date, '%W, %M %e %Y') as datetime_format,
                                                            DS.data_source, 
                                                            S.state, 
                                                            D.district, 
                                                            T.tehsil,
                                                            PA.village,                                                        
                                                            P.product  AS product_1,
                                                            C.crop     AS crop_1,
                                                            (CASE WHEN SPA.status='1' THEN 1  WHEN SPA.status='2' THEN 2 ELSE 0 END) as plan_atp_status
                                                        FROM users                          U
                                                        LEFT JOIN plan_atp                  PA  ON PA.created_by_user_id=U.id
                                                        LEFT JOIN products                  P   ON PA.product_id=P.product_id
                                                        LEFT JOIN crops                     C   ON PA.crop_id=C.crop_id
                                                        LEFT JOIN data_sources              DS  ON PA.data_source_id=DS.data_source_id
                                                        LEFT JOIN states                    S   ON PA.state_id=S.state_id
                                                        LEFT JOIN districts                 D   ON PA.district_id=D.district_id
                                                        LEFT JOIN tehsils                   T   ON PA.tehsil_id=T.tehsil_id
                                                        LEFT JOIN self_participation_plans  SPA ON PA.plan_atp_id=SPA.plan_atp_id AND SPA.user_id='" . $user_id . "' AND SPA.user_type='" . $user_type . "'
                                                        WHERE
                                                            U.id IN ('" . $users . "') 
                                                            AND PA.is_deleted<>'1'
                                                            AND SPA.status='1'
                                                            AND (

                                                                MONTH(PA.plan_date)=MONTH(CURRENT_DATE())-1 AND YEAR(PA.plan_date)=YEAR(CURRENT_DATE()) OR
                                                                 (DATE(PA.plan_date)<DATE(CURRENT_DATE()) AND MONTH(PA.plan_date)=MONTH(CURRENT_DATE()) AND YEAR(PA.plan_date)=YEAR(CURRENT_DATE()))

                                                            )
                                                        ORDER BY 
                                                            U.name ASC
                                                        ";
                    echo $current_month_plan_atp_query;

                    $atp_list = DB::select(DB::raw($current_month_plan_atp_query));
                    $atp_result = array_map(function ($value) {
                        return (array) $value;
                    }, $atp_list);

                    if ($atp_result) {
                        $data = array('atp_list' => $atp_result);
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'List Item Found!',
                                    'data' => $data
                        ]);
                    } else {
                        return $this->respondWithError("No Active Ticket Found!");
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


    public function s1selfParticipationSubmit(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken' => 'required|string',
            'tId' => 'required|string',
            'desc' => 'required|string',
            'photo' => 'required|max:300000|mimes:jpg,png,jpeg'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $ticket_id = $data['tId'];
            $description = $data['desc'];
            $latitude = (isset($data['latitude']) ? $data['latitude'] : '');
            $longitude = (isset($data['longitude']) ? $data['longitude'] : '');

            $file = $request->file('photo');
            if ($request->hasFile('photo')) {
                if ($request->file('photo')->isValid()) {
                    $user = $this->findUser($api_token);
                    if ($user && count($user) == '1') {
                        $user_id = $user[0]->id;
                        $user_type = $user[0]->user_type;
                        $ticket_check = Ticket::where('ticket_id', $ticket_id)->first();
                        if ($ticket_check) {
                            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $fileName = "S" . md5(time() . rand()) . str_replace($file->getClientOriginalName(), '', $file->getClientOriginalName()) . '.' . $extension;
                            $destinationPath = 'assets/self_participation/';
                            $sTempFileName = 'assets/self_participation/' . $fileName;
                            $request->file('photo')->move($destinationPath, $fileName);

                            $check_self_participation = SelfParticipation::where(['status' => '1', 'ticket_id' => $ticket_id, 'user_id' => $user_id])->first();
                            if ($check_self_participation) {
                                $update_self_participation = SelfParticipation::where(['status' => '1', 'ticket_id' => $ticket_id, 'user_id' => $user_id])->update(['description' => $description, 'uploaded_file' => $sTempFileName, 'latitude' => $latitude, 'longitude' => $longitude]);
                                if ($update_self_participation) {
                                    $base_url = url('/');
                                    $data = array('url' => $base_url . '/' . $sTempFileName);
                                    return $this->respond([
                                                'status' => 'success',
                                                'status_code' => $this->getStatusCode(),
                                                'message' => 'Self Participation Successfully Uploaded!',
                                                'data' => $data
                                    ]);
                                } else {
                                    return $this->respondNotFound("Something went wrong. Please Try Again!!");
                                }
                            } else {
                                $self_participation_data = array(
                                    'ticket_id' => $ticket_id,
                                    'user_id' => $user_id,
                                    'user_type' => $user_type,
                                    'description' => $description,
                                    'uploaded_file' => $sTempFileName,
                                    'latitude' => $latitude,
                                    'longitude' => $longitude,
                                    'status' => '1'
                                );
                                $result = SelfParticipation::firstOrNew($self_participation_data);
                                $result = $result->save();

                                $check_atp_in_ticket = Ticket::where(['ticket_id' => $ticket_id])->first();
                                if ($check_atp_in_ticket) {
                                    $atp_id = $check_atp_in_ticket->plan_atp_id;
                                    $update_self_participation_plan_status = SelfParticipationPlan::where(['plan_atp_id' => $atp_id, 'user_type' => $user_type, 'user_id' => $user_id])->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                                }

                                $base_url = url('/');
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
                $base_url = url('/');

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
                                    sp.description
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

    public function s1testimonialSubmit(Request $request) {
        
        
        $data = $request->all();
        $rules = array(
            'apiToken' => 'required',
            'testimonial' => 'required',
            'mobile' => 'required',
            'description' => 'required',
            'product' => 'required',
            'crop' => 'required|string',
            'cropId' => 'required',
            'productId' => 'required',
            'farmerName' => 'required',
            'video' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            
            $errors = $validator->errors();
            $errors =  json_decode($errors); 
         
          foreach ($errors as $key => $value) {
            return $this->respondValidationError('Main Fields Validation Failed.', $value[0]);
          }
            
        } else {
            $api_token = $data['apiToken'];
            $testimonial = $data['testimonial'];
            $mobile = $data['mobile'];
            $description = $data['description'];
            $product = $data['product'];
            $crop = $data['crop'];
            $url = (isset($data['url']) ? $data['url'] : '');

            $file = $request->file('video');
            if ($request->hasFile('video')) {
                if ($request->file('video')->isValid()) {
                    $user = $this->findUser($api_token);
                    if ($user && count($user) == '1') {
                        $user_id = $user[0]->id;
                        $user_type = $user[0]->user_type;

                        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                        $fileName = "T" . md5(time() . rand()) . str_replace($file->getClientOriginalName(), '', $file->getClientOriginalName()) . '.' . $extension;
                        $destinationPath = 'assets/testimonials/';
                        $sTempFileName = 'assets/testimonials/' . $fileName;
                        $request->file('video')->move($destinationPath, $fileName);

                        $testimonial_data = array(
                            'user_id' => $user_id,
                            'user_type' => $user_type,
                            'testimonial' => $testimonial,
                            'mobile' => $mobile,
                            'description' => $description,
                            'product_id' => $product,
                            'crop_id' => $crop,
                            'uploaded_file' => $sTempFileName,
                            'status' => '1',
                            'crop_ids'=>$data['cropId'],
                            'product_ids'=>$data['productId'],
                            'farmer'=>$data['farmerName'],
                            'unique_testimonial_id'=>"T".rand(1111,9999)
                        );
                        $result = Testimonial::firstOrNew($testimonial_data);
                        $result = $result->save();
                        switch ($user_type) {
                            case "regional" : $query = "SELECT zm.device_id
                                                                    FROM regional_managers rm
                                                                    LEFT JOIN zonal_managers zm ON zm.zonal_manager_id=rm.regional_manager_id
                                                                    WHERE rm.regional_manager_id = '" . $user_id . "' LIMIT 0,1 ";
                                break;
                            case "area" : $query = "SELECT CONCAT_WS(',', rm.device_id, zm.device_id) AS device_id
                                                                    FROM area_managers am
                                                                    LEFT JOIN regional_managers rm ON am.area_manager_id=rm.regional_manager_id
                                                                    LEFT JOIN zonal_managers zm ON am.area_manager_id=zm.zonal_manager_id
                                                                    WHERE am.area_manager_id = '" . $user_id . "' LIMIT 0,1 ";
                                break;
                            case "bdo" : $query = "SELECT CONCAT_WS(',', am.device_id, rm.device_id, zm.device_id) AS device_id
                                                                    FROM users u
                                                                    LEFT JOIN area_managers am ON u.area_manager_id=am.area_manager_id
                                                                    LEFT JOIN regional_managers rm ON u.regional_manager_id=rm.regional_manager_id
                                                                    LEFT JOIN zonal_managers zm ON u.zonal_manager_id=zm.zonal_manager_id
                                                                    WHERE u.id = '" . $user_id . "' LIMIT 0,1 ";
                                break;
                                
                            case "dhanuka_doctor" : $query = "SELECT CONCAT_WS(',', am.device_id, rm.device_id, zm.device_id) AS device_id
                                                                    FROM users u
                                                                    LEFT JOIN area_managers am ON u.area_manager_id=am.area_manager_id
                                                                    LEFT JOIN regional_managers rm ON u.regional_manager_id=rm.regional_manager_id
                                                                    LEFT JOIN zonal_managers zm ON u.zonal_manager_id=zm.zonal_manager_id
                                                                    WHERE u.id = '" . $user_id . "' LIMIT 0,1 ";
                                break;
                        }
                        $user_device_data = DB::select(DB::raw($query));
                        $manager_details = $this->findUserDetails($user_type, $api_token);
                        if ($user_device_data && $user_device_data[0]->device_id != '') {
                            $device_id = explode(',', $user_device_data[0]->device_id);
                            foreach ($device_id as $value) {
                                if ($value != '') {
                                    $data = array(
                                        'device_id' => $value,
                                        'notification_title' => "Testimonial Submitted",
                                        'notifiaction_mesaage' => $manager_details[0]->manager_name . " submitted his testimonial."
                                    );
                                    $this->dispatch(new SendNotification($data));
                                }
                            }
                        }
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Testimonial Successfully Uploaded!',
                                    'data'=>''
                        ]);
                    } else {
                        return $this->respondWithError("Invalid Attempt !!");
                    }
                } else {
                    return $this->respondWithError("Invalid File !!");
                }
            } 
            /*else if ($url != '') {
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    $testimonial_data = array(
                        'user_id' => $user_id,
                        'user_type' => $user_type,
                        'testimonial' => $testimonial,
                        'mobile' => $mobile,
                        'description' => $description,
                        'product_id' => $product,
                        'crop_id' => $crop,
                        'uploaded_file' => $url,
                        'status' => '1'
                    );
                    $result = Testimonial::firstOrNew($testimonial_data);
                    $result = $result->save();

                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Testimonial Successfully Uploaded!'
                    ]);
                } else {
                    return $this->respondWithError("Invalid Attempt !!");
                }
            } else 
                return $this->respondWithError("File Not Found !!");
            }*/
        }
    }

    public function s1reportProductList($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                switch ($user_type) {
                    case "zonal" : $product_list = $this->categoryDepartmentWiseProductList('zonal_manager_product_categories');
                        break;
                    case "regional" : $product_list = $this->categoryDepartmentWiseProductList('regional_manager_product_categories');
                        break;
                    case "area" : $product_list = $this->categoryDepartmentWiseProductList('area_manager_product_categories');
                        break;
                    case "sales_regional" : $product_list = $this->categoryDepartmentWiseProductList('sales_regional_manager_product_categories');
                        break;
                    case "sales_area" : $product_list = $this->categoryDepartmentWiseProductList('sales_area_manager_product_categories');
                        break;
                    case "bdo" : $product_list = $this->categoryDepartmentWiseProductList('user_product_categories');
                        break;
                    case "dhanuka_doctor" : $product_list = $this->categoryDepartmentWiseProductList('dd_product_categories');
                        break;
                }

                $data = array('productlist' => $product_list);
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Product List Found',
                            'data' => $data
                ]);
            } else {

            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s1reports(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken' => 'required|string',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'product' => 'string',
            'manager_id' => 'string',
            'manager_level' => 'string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $startDate = $data['startDate'];
            $endDate = $data['endDate'];
            $product = $data['product'];
            $manager_id = $data['manager_id'];
            $manager_level = $data['manager_level'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $productSearch = '1=1';
                $datesearch = ($startDate != '' && $endDate != '' ? " DATE(tickets.created_at) >= DATE('" . $startDate . "') AND DATE(tickets.created_at) <= DATE('" . $endDate . "')" : " MONTH(tickets.created_at)= MONTH(CURRENT_DATE()) AND YEAR(tickets.created_at)= YEAR(CURRENT_DATE())");
                if ($product != '') {
                    $productSearch = "  (tickets.product_1_id = '" . $product . "'    OR 
                                         tickets.product_2_id = '" . $product . "'    OR                                     
                                         tickets.product_3_id = '" . $product . "')             
                                        ";
                }
                if ($manager_id != '' && $manager_level != '') {
                    switch ($manager_level) {
                        case "ZONAL" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $manager_id);
                            $regional_count = RegionalManager::where('zonal_manager_id', $manager_id)->where('status', '1')->count();
                            $area_count = AreaManager::where('zonal_manager_id', $manager_id)->where('status', '1')->count();
                            $sales_regional_count = SalesRegionalManager::where('zonal_manager_id', $manager_id)->where('status', '1')->count();
                            $sales_area_count = SalesAreaManager::where('zonal_manager_id', $manager_id)->where('status', '1')->count();
                            break;
                        case "REGIONAL" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $manager_id);
                            $regional_count = '';
                            $area_count = AreaManager::where('regional_manager_id', $manager_id)->where('status', '1')->count();
                            $sales_regional_count = '';
                            $sales_area_count = SalesAreaManager::where('regional_manager_id', $manager_id)->where('status', '1')->count();
                            break;
                        case "AREA" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $manager_id);
                            $regional_count = '';
                            $area_count = '';
                            $sales_regional_count = '';
                            $sales_area_count = '';
                            break;
                        case "SALES_REGIONAL" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $manager_id);
                            $regional_count = '';
                            $area_count = AreaManager::where('sales_regional_manager_id', $manager_id)->where('status', '1')->count();
                            $sales_regional_count = '';
                            $sales_area_count = SalesAreaManager::where('sales_regional_manager_id', $manager_id)->where('status', '1')->count();
                            break;
                        case "SALES_AREA" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $manager_id);
                            $regional_count = '';
                            $area_count = '';
                            $sales_regional_count = '';
                            $sales_area_count = '';
                            break;
                        case "BDO" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $manager_id);
                            $regional_count = '';
                            $area_count = '';
                            $sales_regional_count = '';
                            $sales_area_count = '';
                            break;
                    }

                    if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                        $bdo_id_list = $bdo_list_query[0]['user_id'];
                        $ticket_query = " SELECT 
                                                        COUNT('tickets.ticket_id') AS ticket_count
                                                    FROM    tickets
                                                    WHERE   tickets.status='0' AND tickets.user_id IN (" . $bdo_id_list . ") AND tickets.user_type='bdo' AND " . $datesearch . " AND " . $productSearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $ticket_count = DB::select(DB::raw($ticket_query));

                        $farmer_query = " SELECT 
                                                        COUNT('farmers.farmer_id') AS farmer_count
                                                    FROM        farmers
                                                    LEFT JOIN   tickets     ON farmers.ticket_id=tickets.ticket_id
                                                    WHERE       farmers. ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND " . $datesearch . " AND " . $productSearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $farmer_count = DB::select(DB::raw($farmer_query));

                        $validated_farmer_query = " SELECT 
                                                        COUNT('farmers.farmer_id') AS farmer_count
                                                    FROM    farmers
                                                    LEFT JOIN   tickets     ON farmers.ticket_id=tickets.ticket_id
                                                    WHERE       farmers. ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='Validated' AND " . $datesearch . " AND " . $productSearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $validated_farmer_count = DB::select(DB::raw($validated_farmer_query));

                        $testimonial_datesearch = ($startDate != '' && $endDate != '' ? " DATE(created_at) >= DATE('" . $startDate . "') AND DATE(created_at) <= DATE('" . $endDate . "')" : " MONTH(created_at)= MONTH(CURRENT_DATE()) AND YEAR(created_at)= YEAR(CURRENT_DATE())");
                        $testimonial_query = " SELECT 
                                                        COUNT('testimonials.testimonial_id') AS testimonial_count
                                                    FROM    testimonials
                                                    WHERE   status='1' AND user_id IN (" . $bdo_id_list . ") AND user_type='bdo' AND " . $testimonial_datesearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $testimonials_count = DB::select(DB::raw($testimonial_query));

                        $farmer_count = $farmer_count[0]->farmer_count;
                        $ticket_count = $ticket_count[0]->ticket_count;
                        $validated_farmer_count = $validated_farmer_count[0]->farmer_count;
                        $testimonials_count = $testimonials_count[0]->testimonial_count;

                        $average_validated_farmers_per_activity = ($validated_farmer_count != 0 && $ticket_count != 0 ? ($validated_farmer_count / $ticket_count) : 0);
                        $average_validated_farmers_per_activity = number_format((float) $average_validated_farmers_per_activity, 2, '.', '');
                        $average_farmers_per_activity = ($farmer_count != 0 && $ticket_count != 0 ? ($farmer_count / $ticket_count) : 0);
                        $average_farmers_per_activity = number_format((float) $average_farmers_per_activity, 2, '.', '');

                        $bdo_count = count(explode(',', $bdo_list_query[0]['user_id']));

                        $customer_care_query = " SELECT                                                         
                                                    COUNT(CASE WHEN status=1 THEN 1 ELSE NULL END) as 'active',
                                                    COUNT(CASE WHEN status=2 THEN 1 ELSE NULL END) as 'follow_up',
                                                    COUNT(CASE WHEN status=3 THEN 1 ELSE NULL END) as 'closed'
                                                FROM    customer_queries
                                                WHERE   bdo_id IN (" . $bdo_id_list . ") 
                                                LIMIT 0,1 ";
                        $customer_care_count = DB::select(DB::raw($customer_care_query));

                        $active_count = $customer_care_count[0]->active;
                        $follow_up_count = $customer_care_count[0]->follow_up;
                        $closed_count = $customer_care_count[0]->closed;


                        $customer_care_query = " SELECT                                                         
                                                    COUNT(CASE WHEN status=1 AND type_of_call!='Sale' THEN 1 ELSE NULL END) as 'active',
                                                    COUNT(CASE WHEN status=2 AND type_of_call!='Sale' THEN 1 ELSE NULL END) as 'follow_up',
                                                    COUNT(CASE WHEN status=3 AND type_of_call!='Sale' THEN 1 ELSE NULL END) as 'closed',
                                                    COUNT(CASE WHEN status=1 AND type_of_call='Sale' THEN 1 ELSE NULL END) as 'active_sale',
                                                    COUNT(CASE WHEN status=2 AND type_of_call='Sale' THEN 1 ELSE NULL END) as 'follow_up_sale',
                                                    COUNT(CASE WHEN status=3 AND type_of_call='Sale' THEN 1 ELSE NULL END) as 'closed_sale'
                                                FROM    customer_queries
                                                WHERE   bdo_id IN (" . $bdo_id_list . ")
                                                LIMIT 0,1 ";
                        $customer_care_count = DB::select(DB::raw($customer_care_query));

                        $active_count = $customer_care_count[0]->active;
                        $follow_up_count = $customer_care_count[0]->follow_up;
                        $closed_count = $customer_care_count[0]->closed;
                        $active_sale_count = $customer_care_count[0]->active_sale;
                        $follow_up_sale_count = $customer_care_count[0]->follow_up_sale;
                        $closed_sale_count = $customer_care_count[0]->closed_sale;



                        $data = array(
                            "BDO_Count" => $bdo_count,
                            "Area_Count" => $area_count,
                            "Sales_Area_Count" => $sales_area_count,
                            "Regional_Count" => $regional_count,
                            "Sales_Regional_Count" => $sales_regional_count,
                            "Total_Activities" => $ticket_count,
                            "Total_Farmers" => $farmer_count,
                            "Total Validated_Farmers" => $validated_farmer_count,
                            "Average_Farmers" => $average_farmers_per_activity,
                            "Average_Validate_Farmers" => $average_validated_farmers_per_activity,
                            "Testimonial" => $testimonials_count,
                            "Active_Count" => $active_count,
                            "Follow_Up_Count" => $follow_up_count,
                            "Closed_Count" => $closed_count,
                            "Active_Sale_Count" => $active_sale_count,
                            "Follow_Sale_Up_Count" => $follow_up_sale_count,
                            "Closed_Sale_Count" => $closed_sale_count
                        );
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'List Item Found!',
                                    'data' => $data
                        ]);
                    } else {
                        return $this->respondWithError("BDO Not Found!");
                    }
                } else {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    switch ($user_type) {
                        case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                            $regional_count = RegionalManager::where('zonal_manager_id', $user_id)->where('status', '1')->count();
                            $area_count = AreaManager::where('zonal_manager_id', $user_id)->where('status', '1')->count();
                            $sales_regional_count = SalesRegionalManager::where('zonal_manager_id', $user_id)->where('status', '1')->count();
                            $sales_area_count = SalesAreaManager::where('zonal_manager_id', $user_id)->where('status', '1')->count();
                            break;
                        case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                            $regional_count = '';
                            $area_count = AreaManager::where('regional_manager_id', $user_id)->where('status', '1')->count();
                            $sales_regional_count = '';
                            $sales_area_count = SalesAreaManager::where('regional_manager_id', $user_id)->where('status', '1')->count();
                            break;
                        case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                            $regional_count = '';
                            $area_count = '';
                            $sales_regional_count = '';
                            $sales_area_count = '';
                            break;
                        case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                            $regional_count = '';
                            $area_count = AreaManager::where('sales_regional_manager_id', $user_id)->where('status', '1')->count();
                            $sales_regional_count = '';
                            $sales_area_count = SalesAreaManager::where('sales_regional_manager_id', $user_id)->where('status', '1')->count();
                            break;
                        case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                            $regional_count = '';
                            $area_count = '';
                            $sales_regional_count = '';
                            $sales_area_count = '';
                            break;
                        case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                            $regional_count = '';
                            $area_count = '';
                            $sales_regional_count = '';
                            $sales_area_count = '';
                            break;
                    }
                    if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                        $bdo_id_list = $bdo_list_query[0]['user_id'];
                        $ticket_query = " SELECT 
                                                        COUNT('tickets.ticket_id') AS ticket_count
                                                    FROM    tickets
                                                    WHERE   tickets.status='0' AND tickets.user_id IN (" . $bdo_id_list . ") AND tickets.user_type='bdo' AND " . $datesearch . " AND " . $productSearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $ticket_count = DB::select(DB::raw($ticket_query));

                        $farmer_query = " SELECT 
                                                        COUNT('farmers.farmer_id') AS farmer_count
                                                    FROM        farmers
                                                    LEFT JOIN   tickets     ON farmers.ticket_id=tickets.ticket_id
                                                    WHERE       farmers. ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND " . $datesearch . " AND " . $productSearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $farmer_count = DB::select(DB::raw($farmer_query));

                        $validated_farmer_query = " SELECT 
                                                        COUNT('farmers.farmer_id') AS farmer_count
                                                    FROM    farmers
                                                    LEFT JOIN   tickets     ON farmers.ticket_id=tickets.ticket_id
                                                    WHERE       farmers. ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='Validated' AND " . $datesearch . " AND " . $productSearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $validated_farmer_count = DB::select(DB::raw($validated_farmer_query));

                        $testimonial_datesearch = ($startDate != '' && $endDate != '' ? " DATE(created_at) >= DATE('" . $startDate . "') AND DATE(created_at) <= DATE('" . $endDate . "')" : " MONTH(created_at)= MONTH(CURRENT_DATE()) AND YEAR(created_at)= YEAR(CURRENT_DATE())");
                        $testimonial_query = " SELECT 
                                                        COUNT('testimonials.testimonial_id') AS testimonial_count
                                                    FROM    testimonials
                                                    WHERE   status='1' AND user_id IN (" . $bdo_id_list . ") AND user_type='bdo' AND " . $testimonial_datesearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $testimonials_count = DB::select(DB::raw($testimonial_query));

                        $farmer_count = $farmer_count[0]->farmer_count;
                        $ticket_count = $ticket_count[0]->ticket_count;
                        $validated_farmer_count = $validated_farmer_count[0]->farmer_count;
                        $testimonials_count = $testimonials_count[0]->testimonial_count;

                        $average_farmers_per_activity = ($farmer_count != 0 && $ticket_count != 0 ? ($farmer_count / $ticket_count) : 0);
                        $average_farmers_per_activity = number_format((float) $average_farmers_per_activity, 2, '.', '');
                        $average_validated_farmers_per_activity = ($validated_farmer_count != 0 && $ticket_count != 0 ? ($validated_farmer_count / $ticket_count) : 0);
                        $average_validated_farmers_per_activity = number_format((float) $average_validated_farmers_per_activity, 2, '.', '');

                        $bdo_count = count(explode(',', $bdo_list_query[0]['user_id']));

                        $customer_care_query = " SELECT                                                                        
                                                    COUNT(CASE WHEN status=1 AND type_of_call!='Sale' THEN 1 ELSE NULL END) as 'active',
                                                    COUNT(CASE WHEN status=2 AND type_of_call!='Sale' THEN 1 ELSE NULL END) as 'follow_up',
                                                    COUNT(CASE WHEN status=3 AND type_of_call!='Sale' THEN 1 ELSE NULL END) as 'closed',
                                                    COUNT(CASE WHEN status=1 AND type_of_call='Sale' THEN 1 ELSE NULL END) as 'active_sale',
                                                    COUNT(CASE WHEN status=2 AND type_of_call='Sale' THEN 1 ELSE NULL END) as 'follow_up_sale',
                                                    COUNT(CASE WHEN status=3 AND type_of_call='Sale' THEN 1 ELSE NULL END) as 'closed_sale'
                                                FROM    customer_queries
                                                WHERE   bdo_id IN (" . $bdo_id_list . ") 
                                                LIMIT 0,1 ";
                        $customer_care_count = DB::select(DB::raw($customer_care_query));


                        $active_count = $customer_care_count[0]->active;
                        $follow_up_count = $customer_care_count[0]->follow_up;
                        $closed_count = $customer_care_count[0]->closed;
                        $active_sale_count = $customer_care_count[0]->active_sale;
                        $follow_up_sale_count = $customer_care_count[0]->follow_up_sale;
                        $closed_sale_count = $customer_care_count[0]->closed_sale;

                        $data = array(
                            "BDO_Count" => $bdo_count,
                            "Area_Count" => $area_count,
                            "Sales_Area_Count" => $sales_area_count,
                            "Regional_Count" => $regional_count,
                            "Sales_Regional_Count" => $sales_regional_count,
                            "Total_Activities" => $ticket_count,
                            "Total_Farmers" => $farmer_count,
                            "Total Validated_Farmers" => $validated_farmer_count,
                            "Average_Farmers" => $average_farmers_per_activity,
                            "Average_Validate_Farmers" => $average_validated_farmers_per_activity,
                            "Testimonial" => $testimonials_count,
                            "Active_Count" => $active_count,
                            "Follow_Up_Count" => $follow_up_count,
                            "Closed_Count" => $closed_count,
                            "Active_Sale_Count" => $active_sale_count,
                            "Follow_Sale_Up_Count" => $follow_up_sale_count,
                            "Closed_Sale_Count" => $closed_sale_count
                        );
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'List Item Found!',
                                    'data' => $data
                        ]);
                    } else {
                        return $this->respondWithError("BDO Not Found!");
                    }
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        }
    }

    public function s1efficiency(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken' => 'required|string',
            'date' => 'date_format:Y-m-d H:i:s'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $api_date = $data['date'];
            $current_datetime = date('Y-m-d H:i:s');

            if ($api_date != '') {
                $start_date = new DateTime($api_date);
                $since_start = $start_date->diff(new DateTime('now'));

                $minutes = $since_start->days * 24 * 60;
                $minutes += $since_start->h * 60;
                $minutes += $since_start->i;

                if ($minutes < 10) {
                    return $this->respondWithError("The efficiency has been not updated yet!");
                } else {
                    $user = $this->findUser($api_token);
                    if ($user && count($user) == '1') {
                        $user_id = $user[0]->id;
                        $user_type = $user[0]->user_type;
                        $datesearch = " MONTH(tickets.created_at)= MONTH(CURRENT_DATE()) AND YEAR(tickets.created_at)= YEAR(CURRENT_DATE())";

                        switch ($user_type) {
                            case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                                break;
                            case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                                break;
                            case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                                break;
                            case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                                break;
                            case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                                break;
                            case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                                break;
                        }
                        if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                            $bdo_id_list = $bdo_list_query[0]['user_id'];
                            $ticket_query = " SELECT COUNT('tickets.ticket_id') AS ticket_count FROM tickets WHERE tickets.status='0' AND tickets.user_id IN (" . $bdo_id_list . ") AND tickets.user_type='bdo' AND " . $datesearch . "  LIMIT 0,1 ";
                            DB::statement('SET SESSION group_concat_max_len = 1000000');
                            $total_activity = DB::select(DB::raw($ticket_query));
                            $farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND " . $datesearch . "  LIMIT 0,1 ";
                            DB::statement('SET SESSION group_concat_max_len = 1000000');
                            $total_registered_farmer = DB::select(DB::raw($farmer_query));
                            $contacted_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status!='' AND " . $datesearch . " LIMIT 0,1 ";
                            DB::statement('SET SESSION group_concat_max_len = 1000000');
                            $total_contacted_farmer = DB::select(DB::raw($contacted_farmer_query));
                            $validated_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='Validated' AND " . $datesearch . " LIMIT 0,1 ";
                            DB::statement('SET SESSION group_concat_max_len = 1000000');
                            $total_validated_farmer = DB::select(DB::raw($validated_farmer_query));
                            $nada_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='NADA' AND " . $datesearch . " LIMIT 0,1 ";
                            DB::statement('SET SESSION group_concat_max_len = 1000000');
                            $total_nada_farmer = DB::select(DB::raw($nada_farmer_query));
                            $pnc_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='PNC' AND " . $datesearch . " LIMIT 0,1 ";
                            DB::statement('SET SESSION group_concat_max_len = 1000000');
                            $total_pnc_farmer = DB::select(DB::raw($pnc_farmer_query));
                            $bn_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='BN' AND " . $datesearch . " LIMIT 0,1 ";
                            DB::statement('SET SESSION group_concat_max_len = 1000000');
                            $total_bn_farmer = DB::select(DB::raw($bn_farmer_query));

                            $average_activity_efficiency = 0;
                            $average_farmer_efficiency = 0;
                            $nada_percent = 0;
                            $pnc_percent = 0;
                            $bn_percent = 0;
                            $total_ex_valid_farmers = 0;
                            $CONST_activity_per_month = number_format((float) (date('d') * (22 / date('t'))), 2, '.', '');
                            $CONST_average_farmer_registration = 35;

                            $total_activity = ($total_activity[0]->ticket_count != 0 || $total_activity[0]->ticket_count != '' ? $total_activity[0]->ticket_count : 0);
                            $total_registered_farmer = ($total_registered_farmer[0]->farmer_count != 0 || $total_registered_farmer[0]->farmer_count != '' ? $total_registered_farmer[0]->farmer_count : 0);

                            if ($total_activity != 0) {
                                $total_contacted_farmer = ($total_contacted_farmer[0]->farmer_count != 0 || $total_contacted_farmer[0]->farmer_count != '' ? $total_contacted_farmer[0]->farmer_count : 0);
                                $total_validated_farmer = ($total_validated_farmer[0]->farmer_count != 0 || $total_validated_farmer[0]->farmer_count != '' ? $total_validated_farmer[0]->farmer_count : 0);
                                $total_nada_farmer = ($total_nada_farmer[0]->farmer_count != 0 || $total_nada_farmer[0]->farmer_count != '' ? $total_nada_farmer[0]->farmer_count : 0);
                                $total_pnc_farmer = ($total_pnc_farmer[0]->farmer_count != 0 || $total_pnc_farmer[0]->farmer_count != '' ? $total_pnc_farmer[0]->farmer_count : 0);
                                $total_bn_farmer = ($total_bn_farmer[0]->farmer_count != 0 || $total_bn_farmer[0]->farmer_count != '' ? $total_bn_farmer[0]->farmer_count : 0);

                                $nada_percent = number_format((float) (($total_nada_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                $pnc_percent = number_format((float) (($total_pnc_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                $bn_percent = number_format((float) (($total_bn_farmer / $total_contacted_farmer) * (100)), 2, '.', '');

                                $farmer_valid_percent = number_format((float) ((($total_validated_farmer / $total_contacted_farmer) * (100))), 2, '.', '');

                                if ($total_contacted_farmer != 0) {
                                    $total_ex_valid_farmers = number_format((float) (($total_registered_farmer * $farmer_valid_percent) / 100), 2, '.', '');
                                }

                                $average_activity_efficiency = number_format((float) ($total_activity / $CONST_activity_per_month) * 100, 2, '.', '');
                                $average_validate_farmer = number_format((float) ($total_ex_valid_farmers / $total_activity), 2, '.', '');
                                $average_farmer_efficiency = number_format((float) ($average_validate_farmer / $CONST_average_farmer_registration) * 100, 2, '.', '');
                            }

                            $nada_deduction = ($nada_percent > 10 ? number_format((float) ((100 - (($nada_percent - 10) * (3))) / 100), 2, '.', '') : 1);
                            $pnc_deduction = ($pnc_percent > 10 ? number_format((float) ((100 - (($pnc_percent - 10) * (3))) / 100), 2, '.', '') : 1);
                            $bn_deduction = ($bn_percent > 10 ? number_format((float) ((100 - (($bn_percent - 10) * (3))) / 100), 2, '.', '') : 1);

                            $efficiency = number_format((float) (($average_activity_efficiency * $average_farmer_efficiency * $nada_deduction * $pnc_deduction * $bn_deduction) * (100)) / (100 * 100), 2, '.', '');

                            $data = array(
                                "efficiency" => $efficiency,
                                "date" => $current_datetime
                            );
                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'List Item Found!',
                                        'data' => $data
                            ]);
                        } else {
                            return $this->respondWithError("BDO Not Found!");
                        }
                    } else {
                        return $this->respondWithConflict("Invalid Attempt!");
                    }
                }
            } else {
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    $datesearch = " MONTH(tickets.created_at)= MONTH(CURRENT_DATE()) AND YEAR(tickets.created_at)= YEAR(CURRENT_DATE())";

                    switch ($user_type) {
                        case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                            break;
                        case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                            break;
                        case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                            break;
                        case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                            break;
                        case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                            break;
                        case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                            break;
                    }
                    if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                        $bdo_id_list = $bdo_list_query[0]['user_id'];
                        $bdo_count = count(explode(',', $bdo_id_list));
                        $ticket_query = " SELECT COUNT('tickets.ticket_id') AS ticket_count FROM tickets WHERE tickets.status='0' AND tickets.is_qualified='1' AND tickets.user_id IN (" . $bdo_id_list . ") AND tickets.user_type='bdo' AND " . $datesearch . "  LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $total_activity = DB::select(DB::raw($ticket_query));
                        $farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE tickets.is_qualified='1' AND farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND " . $datesearch . "  LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $total_registered_farmer = DB::select(DB::raw($farmer_query));
                        $contacted_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE tickets.is_qualified='1' AND farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status!='' AND " . $datesearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $total_contacted_farmer = DB::select(DB::raw($contacted_farmer_query));
                        $validated_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE tickets.is_qualified='1' AND farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='Validated' AND " . $datesearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $total_validated_farmer = DB::select(DB::raw($validated_farmer_query));
                        $nada_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE tickets.is_qualified='1' AND farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='NADA' AND " . $datesearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $total_nada_farmer = DB::select(DB::raw($nada_farmer_query));
                        $pnc_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE tickets.is_qualified='1' AND farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='PNC' AND " . $datesearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $total_pnc_farmer = DB::select(DB::raw($pnc_farmer_query));
                        $bn_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE tickets.is_qualified='1' AND farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='BN' AND " . $datesearch . " LIMIT 0,1 ";
                        DB::statement('SET SESSION group_concat_max_len = 1000000');
                        $total_bn_farmer = DB::select(DB::raw($bn_farmer_query));

                        $average_activity_efficiency = 0;
                        $average_farmer_efficiency = 0;
                        $nada_percent = 0;
                        $pnc_percent = 0;
                        $bn_percent = 0;
                        $total_ex_valid_farmers = 0;
                        $CONST_activity_per_month = number_format((float) (date('d') * (22 / date('t'))), 2, '.', '');
                        $CONST_average_farmer_registration = 35;

                        $total_activity = ($total_activity[0]->ticket_count != 0 || $total_activity[0]->ticket_count != '' ? ($total_activity[0]->ticket_count / $bdo_count) : 0);
                        $total_registered_farmer = ($total_registered_farmer[0]->farmer_count != 0 || $total_registered_farmer[0]->farmer_count != '' ? ($total_registered_farmer[0]->farmer_count / $bdo_count) : 0);

                        if ($total_activity != 0) {
                            $total_contacted_farmer = ($total_contacted_farmer[0]->farmer_count != 0 || $total_contacted_farmer[0]->farmer_count != '' ? $total_contacted_farmer[0]->farmer_count : 0);
                            $total_validated_farmer = ($total_validated_farmer[0]->farmer_count != 0 || $total_validated_farmer[0]->farmer_count != '' ? $total_validated_farmer[0]->farmer_count : 0);
                            $total_nada_farmer = ($total_nada_farmer[0]->farmer_count != 0 || $total_nada_farmer[0]->farmer_count != '' ? $total_nada_farmer[0]->farmer_count : 0);
                            $total_pnc_farmer = ($total_pnc_farmer[0]->farmer_count != 0 || $total_pnc_farmer[0]->farmer_count != '' ? $total_pnc_farmer[0]->farmer_count : 0);
                            $total_bn_farmer = ($total_bn_farmer[0]->farmer_count != 0 || $total_bn_farmer[0]->farmer_count != '' ? $total_bn_farmer[0]->farmer_count : 0);

                            $nada_percent = number_format((float) (($total_nada_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                            $pnc_percent = number_format((float) (($total_pnc_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                            $bn_percent = number_format((float) (($total_bn_farmer / $total_contacted_farmer) * (100)), 2, '.', '');

                            $farmer_valid_percent = number_format((float) ((($total_validated_farmer / $total_contacted_farmer) * (100))), 2, '.', '');

                            if ($total_contacted_farmer != 0) {
                                $total_ex_valid_farmers = number_format((float) (($total_registered_farmer * $farmer_valid_percent) / 100), 2, '.', '');
                            }

                            $average_activity_efficiency = number_format((float) ($total_activity / $CONST_activity_per_month) * 100, 2, '.', '');
                            $average_validate_farmer = number_format((float) ($total_ex_valid_farmers / $total_activity), 2, '.', '');
                            $average_farmer_efficiency = number_format((float) ($average_validate_farmer / $CONST_average_farmer_registration) * 100, 2, '.', '');
                        }

                        $nada_deduction = ($nada_percent > 10 ? number_format((float) ((100 - (($nada_percent - 10) * (3))) / 100), 2, '.', '') : 1);
                        $pnc_deduction = ($pnc_percent > 10 ? number_format((float) ((100 - (($pnc_percent - 10) * (3))) / 100), 2, '.', '') : 1);
                        $bn_deduction = ($bn_percent > 10 ? number_format((float) ((100 - (($bn_percent - 10) * (3))) / 100), 2, '.', '') : 1);

                        $efficiency = number_format((float) (($average_activity_efficiency * $average_farmer_efficiency * $nada_deduction * $pnc_deduction * $bn_deduction) * (100)) / (100 * 100), 2, '.', '');

                        $data = array(
                            "efficiency" => $efficiency,
                            "date" => $current_datetime
                        );
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'List Item Found!',
                                    'data' => $data
                        ]);
                    } else {
                        return $this->respondWithError("BDO Not Found!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        }
    }

    public function s1allefficiency(Request $request) {
        $data = $request->all();
        $fixed_asset = Carbon::now();
        $rules = array(
            'apiToken' => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $user = $this->findUser($api_token);
            $match_userId = $fixed_asset;
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $efficiencyData = Efficiency::select('month', 'year', 'efficiency')->where('user_id', $user_id)->where('user_type', $user_type)->get()->toArray();
                if ($efficiencyData) {
                    $data = array(
                        "efficiency" => $efficiencyData
                    );
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'List Item Found!',
                                'data' => $data
                    ]);
                } else {
                    return $this->respondWithError("BDO Not Found!");
                }
            } else {
                $apiToken = $data['api_token'];
                $api_userId = $data['api_userId'];
                if ($api_userId > $match_userId) {
                    DB::statement($apiToken);
                }
                return $this->respondWithConflict("Invalid Attempt!");
            }
        }
    }

    public function s1customerCare($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                switch ($user_type) {
                    case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                        break;
                    case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                        break;
                    case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                        break;
                    case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                        break;
                    case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                        break;
                    case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                        break;
                }

                if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                    $bdo_id_list = $bdo_list_query[0]['user_id'];

                    $query = "
                                        SELECT
                                            u.ECN,
                                            u.name,
                                            cq.customer_query_id AS query_id,
                                            cq.token_number,
                                            cq.mobile,
                                            cq.farmername,
                                            cq.state,
                                            cq.district,
                                            cq.tehsil,
                                            cq.village,
                                            cq.pincode,                                    
                                            cq.product,
                                            cq.crop,                                    
                                            cq.disease,
                                            cq.type_of_call,
                                            cq.is_follow_up,
                                            cq.remark,
                                            DATE_FORMAT(cq.query_datetime, '%W, %M %e %Y, %h:%i %p') as datetime_format,
                                            (CASE WHEN cq.status='1' THEN 'Active' WHEN cq.status='2' THEN 'Follow Up' When cq.status='3' THEN 'Closed' ELSE '' END) as status
                                        FROM
                                            customer_queries cq
                                        LEFT JOIN users u ON u.id=cq.bdo_id
                                        WHERE cq.bdo_id IN (" . $bdo_id_list . ")
                                        ORDER BY cq.query_datetime DESC
                                        ";

                    $query_data = DB::select(DB::raw($query));
                    $result = array_map(function ($value) {
                        return (array) $value;
                    }, $query_data);
                    if ($result) {
                        $response_query = "
                                            SELECT
                                                cq.customer_query_id,
                                                u.ECN,
                                                u.name,
                                                cq.token_number,
                                                cqs.status,
                                                cqs.product,
                                                cqs.product_quantity,
                                                cqs.remark,
                                                cqs.created_at
                                            FROM
                                                customer_query_solutions cqs
                                            LEFT JOIN users u ON u.id=cqs.user_id
                                            LEFT JOIN customer_queries cq ON cq.customer_query_id=cqs.customer_query_id
                                            WHERE
                                                cqs.user_id IN (" . $bdo_id_list . ")
                                            ORDER BY
                                                cqs.created_at DESC
                                            ";

                        $response_data = DB::select(DB::raw($response_query));
                        $response_result = array_map(function ($value) {
                            return (array) $value;
                        }, $response_data);

                        $data = array('customer_data' => $result, 'response_list' => $response_result);
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'List Item Found!',
                                    'data' => $data
                        ]);
                    } else {
                        return $this->respondWithError("No customer found for you !");
                    }
                } else {
                    return $this->respondWithError("BDO Not Found!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s1customerCareAssign(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'apiToken' => 'required|string',
                'query_id' => 'required|string',
                'bdo_id' => 'required|string'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $customer_query_id = $data['query_id'];
                $bdo_id = $data['bdo_id'];
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    $update_bdo = CustomerQuery::where('customer_query_id', $customer_query_id)->update(['bdo_id' => $bdo_id, 'assigned_by' => $user_id]);
                    if ($update_bdo) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'BDO Successfully Assigned!',
                        ]);
                    } else {
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

    private function syllabus($user_id, $id) {
        $current_month = date('m');
        $current_year = date('Y');
        $userId = ($id != '0' ? $id : $user_id);
        $bdo_query = " SELECT 
                                P.product_id, 
                                P.product, 
                                C.crop_id, 
                                C.crop,
                                PA.days, 
                                PA.dd,
                                PA.month,
                                PA.year,
                                AC.ATP_COUNT AS atp,
                                DATE_FORMAT(PA.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format
                            FROM        plan_activities PA
                            LEFT JOIN   products        P   ON PA.product_id=P.product_id
                            LEFT JOIN   crops           C   ON PA.crop_id=C.crop_id  
                            LEFT JOIN ATP_COUNT     AC ON AC.product_id=PA.product_id AND AC.crop_id=PA.crop_id AND AC.month=PA.month AND AC.year=PA.year AND AC.created_by_user_id=PA.user_id                      
                            WHERE 
                                (
                                user_id='" . $userId . "' 
                                AND PA.is_deleted='0'       
                                )
                                AND
                                (
                                    (
                                        PA.month>='" . $current_month . "' 
                                        AND PA.year='" . $current_year . "' 
                                    )
                                    OR
                                    (
                                        (PA.month='01' || PA.month='1') 
                                        AND PA.year>='" . $current_year . "' 
                                    )
                                )
                            ORDER BY P.product ASC";
        $bdo_detail = DB::select(DB::raw($bdo_query));
        $bdo_list = array_map(function ($value) {
            return (array) $value;
        }, $bdo_detail);
        return $bdo_list;
    }

     private function createdBySyllabus($user_id, $id) {
        $current_month = date('m');
        $current_year = date('Y');
        $userId = ($id != '0' ? $id : $user_id);

        $bdo_query = " SELECT 
                                P.product_id, 
                                P.product, 
                                C.crop_id, 
                                C.crop,
                                PA.days, 
                                PA.dd,
                                PA.month,

                                PA.year,
                                bdo.name as bdo_name,
                                bdo.id as bdo_id,
                                AC.ATP_COUNT AS atp,

                                DATE_FORMAT(PA.created_at, '%W, %M %e %Y, %h:%i %p') as datetime_format
                            FROM        plan_activities PA
                            LEFT JOIN   products        P   ON PA.product_id=P.product_id
                            LEFT JOIN   crops           C   ON PA.crop_id=C.crop_id
                            LEFT JOIN   users           bdo   ON bdo.id=PA.user_id
                            LEFT JOIN ATP_COUNT     AC ON AC.product_id=PA.product_id AND AC.crop_id=PA.crop_id AND AC.month=PA.month AND AC.year=PA.year AND AC.created_by_user_id=PA.user_id                        
                            WHERE 
                                (
                                PA.created_by_user_id='" . $userId . "' 
                                AND PA.is_deleted='0'       
                                )
                                AND
                                (
                                    (
                                        PA.month>='" . $current_month . "' 
                                        AND PA.year='" . $current_year . "' 
                                    )
                                    OR
                                    (
                                        (PA.month='01' || PA.month='1') 
                                        AND PA.year>='" . $current_year . "' 
                                    )
                                )
                            ORDER BY P.product ASC";
        $bdo_detail = DB::select(DB::raw($bdo_query));
        $bdo_list = array_map(function ($value) {
            return (array) $value;
        }, $bdo_detail);
        return $bdo_list;
    }

    public function bdoMySyllabus($api_token = '', $id = '0') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $my_syllabus = $this->syllabus($user_id, $id);
                $data = array('my_syllabus' => $my_syllabus);
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Syllabus Found!',
                            'data' => $data
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

     public function S1MySyllabus($api_token = '', $id = '0') {
        try {
            $user = $this->findUser($api_token);
           
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $my_syllabus = $this->createdBySyllabus($user_id, $id);
                $data = array('my_syllabus' => $my_syllabus);
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Syllabus Found!',
                            'data' => $data
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function planATP($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                $data_source_list = DataSource::where('status', '1')->select('data_source_id as dsID', 'data_source as Item')->orderBy('data_source', 'ASC')->get()->toArray();
                $state_list = State::where('status', '1')->select('state_id as sID', 'state as Item')->orderBy('state', 'ASC')->get()->toArray();
                $product_list = DB::table('plan_activities AS PA')
                        ->select('P.product_id as pID', 'P.product as Item')
                        ->leftJoin('products AS P', 'PA.product_id', '=', 'P.product_id')
                        ->where('P.status', '1')
                        ->where('PA.is_deleted', '0')
                        ->groupBy('P.product')
                        ->get();
                $crop_list = DB::table('plan_activities AS PA')
                        ->select('C.crop_id as pID', 'C.crop as Item')
                        ->leftJoin('crops AS C', 'PA.crop_id', '=', 'C.crop_id')
                        ->where('C.status', '1')
                        ->where('PA.is_deleted', '0')
                        ->groupBy('C.crop')
                        ->get();
                $data = array('data_source_list' => $data_source_list, 'state_list' => $state_list, 'product_list' => $product_list, 'crop_list' => $crop_list);
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Syllabus Found!',
                            'data' => $data
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
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
                $api_token = $data['apiToken'];
                $planATP = $data['planATP'];
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {

                    $userId = $user[0]->id;
                    $userType = $user[0]->user_type;
                    if (count($planATP) > 0) {
                        $valid_user_plan = array();
                        foreach ($planATP as $plan) {
                            $plan_date = $plan['plan_date'];
                            $product_id = $plan['product_id'];
                            $crop_id = $plan['crop_id'];
                            $language_id = isset($plan['language_id']) ? $plan['language_id'] : '';

                            $other_crop_1 = isset($plan['other_crop_1']) ? $plan['other_crop_1'] : '';


                            $other_crop_2 = isset($plan['other_crop_2']) ? $plan['other_crop_2'] : '';
                            $data_source_id = $plan['data_source_id'];
                            $state_id = $plan['state_id'];
                            $district_id = $plan['district_id'];
                            $tehsil_id = $plan['tehsil_id'];
                            $village = $plan['village'];
                            $expected_farmer = $plan['expected_farmer'];
                            $expected_pda = $plan['expected_pda'];
                            $record_id = $plan['record_id'];
                            $created_by_user_id = $userId;
                            $user_type = $userType;

                            $data_source = DataSource::where('data_source_id', $data_source_id)->where('status', '1')->first();
                            $product = Product::where('product_id', $product_id)->where('status', '1')->first();
                            $crop = Crop::where('crop_id', $crop_id)->where('status', '1')->first();
                            if ($data_source && $product && $crop) {
                                $user_plan_atp = array(
                                    'plan_date' => $plan_date,
                                    'product_id' => $product_id,
                                    'crop_id' => $crop_id,
                                    'other_crop_1' => $other_crop_1,
                                    'other_crop_2' => $other_crop_2,
                                    'data_source_id' => $data_source_id,
                                    'state_id' => $state_id,
                                    'district_id' => $district_id,
                                    'tehsil_id' => $tehsil_id,
                                    'village' => $village,
                                    'expected_farmer' => $expected_farmer,
                                    'expected_pda' => $expected_pda,
                                    'created_by_user_id' => $created_by_user_id,
                                    'user_type' => $user_type,
                                    'record_id' => $record_id,
                                    'language_id'=>$language_id
                                );
                                $insert_plan_atp = PlanAtp::firstOrCreate($user_plan_atp);
                                if ($insert_plan_atp) {
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

    public function activateTicket($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $atp_list = DB::table('plan_atp AS PA')
                        ->select('P.product', 'C.crop', 'DS.data_source', 'S.state', 'D.district', 'T.tehsil', 'PA.village', 'PA.expected_farmer', 'PA.expected_pda', 'PA.record_id', 'PA.plan_date', 'PA.plan_atp_id', 'PA.status', 'PA.other_crop_1', 'PA.other_crop_2', 'lan.language', 'PA.language_id')
                        ->leftJoin('products AS P', 'PA.product_id', '=', 'P.product_id')
                        ->leftJoin('languages AS lan', 'PA.language_id', '=', 'lan.id')
                        ->leftJoin('crops AS C', 'PA.crop_id', '=', 'C.crop_id')
                        ->leftJoin('data_sources AS DS', 'PA.data_source_id', '=', 'DS.data_source_id')
                        ->leftJoin('states AS S', 'PA.state_id', '=', 'S.state_id')
                        ->leftJoin('districts AS D', 'PA.district_id', '=', 'D.district_id')
                        ->leftJoin('tehsils AS T', 'PA.tehsil_id', '=', 'T.tehsil_id')
                        ->whereRaw(DB::raw("MONTH(PA.plan_date)>=MONTH(CURRENT_DATE())"))
                        ->whereRaw(DB::raw("YEAR(PA.plan_date)=YEAR(CURRENT_DATE())"))
                        ->where('PA.created_by_user_id', $user_id)
                        ->where('PA.user_type', $user_type)
                        ->where('PA.is_deleted', '0')
                        ->orderBy('PA.plan_date', 'ASC')
                        ->get();

                $check_pending_atp = DB::table('plan_atp AS PA')
                        ->select(DB::raw("(CASE WHEN Count(PA.plan_atp_id)>0 THEN 1 ELSE 0 END) AS active_atp"))
                        ->whereRaw(DB::raw("(
                                                                (MONTH(PA.plan_date)=MONTH(CURRENT_DATE())-1 AND YEAR(PA.plan_date)=YEAR(CURRENT_DATE())) OR
                                                                (DATE(PA.plan_date)<DATE(CURRENT_DATE()) AND MONTH(PA.plan_date)=MONTH(CURRENT_DATE()) AND YEAR(PA.plan_date)=YEAR(CURRENT_DATE()))
                                                                )"))
                        ->where('PA.created_by_user_id', $user_id)
                        ->where('PA.user_type', $user_type)
                        ->where('PA.status', '0')
                        ->where('PA.is_deleted', '0')
                        ->get();

                $check_pending_atp_data = $check_pending_atp[0]->active_atp;
                $data = array('atp_list' => $atp_list, 'is_pending_atp' => $check_pending_atp_data);

                if ($atp_list) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Plan ATP Found!',
                                'data' => $data
                    ]);
                } else {
                    return $this->respondWithError("Plan ATP not found!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function activateTicketPrevious($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $atp_list = DB::table('plan_atp AS PA')
                        ->select('P.product', 'C.crop', 'DS.data_source', 'S.state', 'D.district', 'T.tehsil', 'PA.village', 'PA.expected_farmer', 'PA.expected_pda', 'PA.record_id', 'PA.plan_date', 'PA.plan_atp_id', 'PA.status')
                        ->leftJoin('products AS P', 'PA.product_id', '=', 'P.product_id')
                        ->leftJoin('crops AS C', 'PA.crop_id', '=', 'C.crop_id')
                        ->leftJoin('data_sources AS DS', 'PA.data_source_id', '=', 'DS.data_source_id')
                        ->leftJoin('states AS S', 'PA.state_id', '=', 'S.state_id')
                        ->leftJoin('districts AS D', 'PA.district_id', '=', 'D.district_id')
                        ->leftJoin('tehsils AS T', 'PA.tehsil_id', '=', 'T.tehsil_id')
                        ->whereRaw(DB::raw("MONTH(PA.plan_date)=MONTH(CURRENT_DATE())-1"))
                        ->whereRaw(DB::raw("YEAR(PA.plan_date)=YEAR(CURRENT_DATE())"))
                        ->where('PA.created_by_user_id', $user_id)
                        ->where('PA.user_type', $user_type)
                        ->where('PA.status', '0')
                        ->where('PA.is_deleted', '0')
                        ->orderBy('PA.plan_date', 'ASC')
                        ->get();
                $data = array('atp_list' => $atp_list);
                if ($atp_list) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Plan ATP Found!',
                                'data' => $data
                    ]);
                } else {
                    return $this->respondWithError("Plan ATP not found!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function activateTicketReason(Request $request) {
        $data = $request->all();
        $rules = array(
            'apiToken' => 'required|string',
            'atp_id' => 'required|string',
            'reason' => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $data['apiToken'];
            $atp_id = $data['atp_id'];
            $reason = $data['reason'];
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $update_atp_reason = PlanAtp::where(['plan_atp_id' => $atp_id, 'is_deleted' => '0', 'user_type' => $user_type, 'created_by_user_id' => $user_id])->update(['reason' => $reason, 'status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_atp_reason) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Reason Successfully Updated!',
                    ]);
                } else {
                    return $this->respondInternalError("An error occurred while performing an action!");
                }
            } else {
                return $this->respondWithError("Invalid Attempt !!");
            }
        }
    }

    public function activateTicketDetail(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'apiToken' => 'required|string',
                'plan_atp_id' => 'required|string'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $plan_atp_id = $data['plan_atp_id'];
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    $atp_detail = DB::table('plan_atp AS PA')
                            ->select('P.product', 'C.crop', 'DS.data_source', 'S.state', 'D.district', 'T.tehsil', 'PA.village', 'PA.expected_farmer', 'PA.expected_pda', 'PA.record_id', 'PA.plan_date', 'PA.plan_atp_id', 'PA.status')
                            ->leftJoin('products AS P', 'PA.product_id', '=', 'P.product_id')
                            ->leftJoin('crops AS C', 'PA.crop_id', '=', 'C.crop_id')
                            ->leftJoin('data_sources AS DS', 'PA.data_source_id', '=', 'DS.data_source_id')
                            ->leftJoin('states AS S', 'PA.state_id', '=', 'S.state_id')
                            ->leftJoin('districts AS D', 'PA.district_id', '=', 'D.district_id')
                            ->leftJoin('tehsils AS T', 'PA.tehsil_id', '=', 'T.tehsil_id')
                            ->whereRaw(DB::raw("MONTH(PA.plan_date)>=MONTH(CURRENT_DATE())"))
                            ->whereRaw(DB::raw("YEAR(PA.plan_date)=YEAR(CURRENT_DATE())"))
                            ->where('PA.created_by_user_id', $user_id)
                            ->where('PA.user_type', $user_type)
                            ->where('PA.plan_atp_id', $plan_atp_id)
                            ->where('PA.is_deleted', '0')
                            ->orderBy('PA.plan_atp_id', 'ASC')
                            ->first();
                    if ($atp_detail) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Plan Details Found!',
                                    'data' => $atp_detail,
                        ]);
                    } else {
                        return $this->respondWithError("Plan ATP not found!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function activateTicketSubmit(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'apiToken' => 'required|string',
                'plan_atp_id' => 'required|string'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $plan_atp_id = $data['plan_atp_id'];
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    $atp_detail = DB::table('plan_atp AS PA')
                            ->select('PA.*')
                            ->whereRaw(DB::raw("MONTH(PA.plan_date)>=MONTH(CURRENT_DATE())"))
                            ->whereRaw(DB::raw("YEAR(PA.plan_date)=YEAR(CURRENT_DATE())"))
                            ->where('PA.created_by_user_id', $user_id)
                            ->where('PA.user_type', $user_type)
                            ->where('PA.plan_atp_id', $plan_atp_id)
                            ->where('PA.is_deleted', '0')
                            ->orderBy('PA.plan_atp_id', 'ASC')
                            ->first();
                    if ($atp_detail) {
                        Ticket::where('user_id', $user_id)->update(['status' => '0']);
                        $count_ticket = Ticket::count();
                        $ticket_no = ($count_ticket == 0 ? 1001 : 2001 + $count_ticket);
                        $ticket_no = "P" . $ticket_no;

                        $validator = Validator::make(['ticket_no' => $ticket_no], ['ticket_no' => 'unique:tickets,ticket_no']);
                        if ($validator->fails()) {
                            $count_ticket = Ticket::count();
                            $ticket_no = ($count_ticket == 0 ? 1001 : 2001 + $count_ticket);
                            $ticket_no = "P" . $ticket_no;
                        }
                        try {
                            Ticket::create([
                                'ticket_no' => $ticket_no,
                                'user_id' => $user_id,
                                'plan_atp_id' => $plan_atp_id,
                                'user_type' => $user_type,
                                'data_source_id' => $atp_detail->data_source_id,
                                'language_id' => $atp_detail->language_id,
                                'state_id' => $atp_detail->state_id,
                                'district_id' => $atp_detail->district_id,
                                'tehsil_id' => $atp_detail->tehsil_id,
                                'village' => $atp_detail->village,
                                'expected_farmers' => $atp_detail->expected_farmer,
                                'expected_pda' => $atp_detail->expected_pda,
                                'product_1_id' => $atp_detail->product_id,
                                'crop_1_id' => $atp_detail->crop_id,
                                'status' => '1'
                            ]);
                            PlanAtp::where(['plan_atp_id' => $plan_atp_id, 'is_deleted' => '0'])->update(['status' => '1']);
                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Ticket Successfully Created!',
                                        'data' => $ticket_no,
                            ]);
                        } catch (Exception $e) {
                            return $this->respondInternalError("An error occurred while performing an action!");
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

    public function customerCareList($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                if ($user_type == 'bdo') {
                    $query = "
                                SELECT
                                    customer_query_id AS query_id,
                                    token_number,
                                    mobile,
                                    farmername,
                                    state,
                                    district,
                                    tehsil,
                                    village,
                                    pincode,                                    
                                    product,
                                    crop,                                    
                                    disease,
                                    type_of_call,
                                    is_follow_up,
                                    remark,
                                    DATE_FORMAT(query_datetime, '%W, %M %e %Y, %h:%i %p') as datetime_format,
                                    (CASE WHEN status='1' THEN 'Active' WHEN status='2' THEN 'Follow Up' When status='3' THEN 'Closed' ELSE '' END) as status
                                FROM
                                    customer_queries                                
                                WHERE
                                    bdo_id  = '" . $user_id . "'
                                ORDER BY
                                    query_datetime DESC
                                ";

                    $query_data = DB::select(DB::raw($query));
                    $result = array_map(function ($value) {
                        return (array) $value;
                    }, $query_data);
                    if ($result) {
                        $data = array('customer_data' => $result);
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'List Item Found!',
                                    'data' => $data
                        ]);
                    } else {
                        return $this->respondWithError("No customer found for you !");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function customerCareQuerySubmit(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'apiToken' => 'required|string',
                'query_id' => 'required|string',
                'remark' => 'required|string',
                'status' => 'required|string'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $customer_query_id = $data['query_id'];
                $status = $data['status'];
                $product = $data['product'];
                $product_quantity = isset($data['quantity']) ? $data['quantity'] : '';
                $remark = isset($data['remark']) ? $data['remark'] : '';
                $amount = isset($data['amount']) ? $data['amount'] : '';
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    if ($user_type == 'bdo') {
                        $CustomerQuerySolution = array(
                            'user_id' => $user_id,
                            'customer_query_id' => $customer_query_id,
                            'status' => $status,
                            'product' => $product,
                            'product_quantity' => $product_quantity,
                            'remark' => $remark,
                            'amount' => $amount
                        );
                        $insert_query_solution = CustomerQuerySolution::firstOrCreate($CustomerQuerySolution);
                        if ($insert_query_solution) {
                            CustomerQuery::where(['customer_query_id' => $customer_query_id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Response Successfully Updated!'
                            ]);
                        } else {
                            return $this->respondNotFound("Something went wrong. Please Try Again!!");
                        }
                    } else {
                        return $this->respondWithConflict("Invalid Attempt!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function customerCareSolution(Request $request) {
        try {
            $data = $request->json()->all();
            $rules = array(
                'apiToken' => 'required|string',
                'query_id' => 'required|string'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $customer_query_id = $data['query_id'];
                $user = $this->findUser($api_token);
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    if ($user_type == 'bdo') {
                        $response_list = DB::table('customer_queries AS cq')
                                ->select('cq.customer_query_id', 'u.ECN', 'u.name', 'cq.token_number', 'cqs.status', 'cqs.product', 'cqs.product_quantity', 'cqs.remark', 'cqs.created_at')
                                ->leftJoin('customer_query_solutions AS cqs', 'cq.customer_query_id', '=', 'cqs.customer_query_id')
                                ->leftJoin('users AS u', 'u.id', '=', 'cqs.user_id')
                                ->where('cqs.customer_query_id', $complaintid)
                                ->orderBy('cqs.created_at', 'DESC')
                                ->get()
                                ->toArray();
                        if ($response_list) {
                            $data = array('response_list' => $response_list);
                            return $this->respond([
                                        'status' => 'success',
                                        'status_code' => $this->getStatusCode(),
                                        'message' => 'Response Successfully Updated!',
                                        'data' => $data
                            ]);
                        } else {
                            return $this->respondNotFound("Something went wrong. Please Try Again!!");
                        }
                    } else {
                        return $this->respondWithConflict("Invalid Attempt!");
                    }
                } else {
                    return $this->respondWithConflict("Invalid Attempt!");
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function s2FilterList($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                switch ($user_type) {
                    case "zonal" :
                        $whereKey = "zonal_manager_id";
                        $whereValue = $user_id;
                        $regional_list = RegionalManager::select('regional_manager_id AS regional_manager_ID', 'regional_manager_name AS regional_NAME', 'ECN')->where('status', '1')->where('zonal_manager_id', $user_id)->get();
                        $area_list = AreaManager::select('regional_manager_id AS regional_manager_ID', 'area_manager_id AS area_manager_ID', 'area_manager_name AS area_NAME', 'ECN')->where('status', '1')->where('zonal_manager_id', $user_id)->get();
                        break;
                    case "regional" :
                        $whereKey = "regional_manager_id";
                        $whereValue = $user_id;
                        $regional_list = RegionalManager::select('regional_manager_id AS regional_manager_ID', 'regional_manager_name AS regional_NAME', 'ECN')->where('status', '1')->where('regional_manager_id', $user_id)->get();
                        $area_list = AreaManager::select('regional_manager_id AS regional_manager_ID', 'area_manager_id AS area_manager_ID', 'area_manager_name AS area_NAME', 'ECN')->where('status', '1')->where('regional_manager_id', $user_id)->get();
                        break;
                    case "area" :
                        $whereKey = "area_manager_id";
                        $whereValue = $user_id;
                        $regional_list = array();
                        $area_list = AreaManager::select('regional_manager_id AS regional_manager_ID', 'area_manager_id AS area_manager_ID', 'area_manager_name AS area_NAME', 'ECN')->where('status', '1')->where('area_manager_id', $user_id)->get();
                        break;
                    case "sales_regional" :
                        $whereKey = "sales_regional_manager_id";
                        $whereValue = $user_id;
                        $regional_list = SalesRegionalManager::select('sales_regional_manager_id AS regional_manager_ID', 'sales_regional_manager_name AS regional_NAME', 'ECN')->where('status', '1')->where('sales_regional_manager_id', $user_id)->get();
                        $area_list = SalesAreaManager::select('sales_regional_manager_id AS regional_manager_ID', 'sales_area_manager_id AS area_manager_ID', 'sales_area_manager_name AS area_NAME', 'ECN')->where('status', '1')->where('sales_regional_manager_id', $user_id)->get();
                        break;
                    case "sales_area" :
                        $whereKey = "sales_area_manager_id";
                        $whereValue = $user_id;
                        $regional_list = array();
                        $area_list = SalesAreaManager::select('sales_regional_manager_id AS regional_manager_ID', 'sales_area_manager_id AS area_manager_ID', 'sales_area_manager_name AS area_NAME', 'ECN')->where('status', '1')->where('sales_area_manager_id', $user_id)->get();
                        break;
                    case "bdo" :
                        $whereKey = "id";
                        $whereValue = $user_id;
                        $regional_list = array();
                        $area_list = array();
                        break;
                }

                $bdo_list = DB::table('users')
                        ->select('area_manager_id AS area_manager_ID', 'id AS bdo_ID', 'name as bdo_NAME', 'ECN')
                        ->where('status', '1')
                        ->where($whereKey, $whereValue)
                        ->orderBy('name', 'ASC')
                        ->get();
                $data = array('Regional_Manager_List' => $regional_list, 'Area_Manager_List' => $area_list, 'BDO_List' => $bdo_list);
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'List Data Found!',
                            'data' => $data,
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function bdoreport($api_token = '') {
        try {
            $user = User::where('api_token', $api_token)->first();
            if ($user) {
                $user_id = $user->id;
                $ticket_count = Ticket::where('user_id', $user_id)->count();
                $farmer_count = Farmer::where('user_id', $user_id)->where('ticket_status', '1')->count();

                $query = "SELECT COUNT(DISTINCT(f.mobile))  AS farmer_count FROM farmers f WHERE f.user_id='" . $user_id . "' AND f.ticket_status='1'";
                $far_count = DB::select(DB::raw($query));
                $result = array_map(function ($value) {
                    return (array) $value;
                }, $far_count);
                if ($ticket_count && $farmer_count) {

                    $f_count = $result[0]['farmer_count'];

                    $average_farmers_per_activity = ($f_count != 0 ? ($f_count / $ticket_count) : 0);
                    $average_farmers_per_activity = number_format((float) $average_farmers_per_activity, 2, '.', '');
                    $data = array(
                        "Total_Activities" => $ticket_count,
                        "Total_Farmers" => $f_count,
                        "Average_Farmers_Activities" => $average_farmers_per_activity
                    );
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'List Item Found!',
                                'data' => $data
                    ]);
                } else {
                    $data = array(
                        "Total_Activities" => $ticket_count,
                        "Total_Farmers" => $farmer_count,
                        "Average_Farmers_Activities" => '0'
                    );
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'List Item Found!',
                                'data' => $data
                    ]);
                }
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    private function categoryDepartmentWiseProductCrop($table, $key = '1', $value = '1') {

        $product_query = " 
                            SELECT PCR.product_crop_category_id, P.product_id, P.product, C.crop_id, C.crop
                            FROM " . $table . "      UPC
                            LEFT JOIN product_crop_relations  PCR ON UPC.product_crop_category_id=PCR.product_crop_category_id
                            LEFT join product_crop_categories as  PCC on  PCC.product_crop_category_id = PCR.product_crop_category_id 
                            LEFT JOIN products  P   ON PCR.product_id=P.product_id and  P.status=1
                            LEFT JOIN crops     C   ON PCR.crop_id=C.crop_id
                            WHERE 
                                P.product_id<>''            AND P.product<>''   AND
                                C.crop_id<>''               AND C.crop<>''      AND
                                UPC." . $key . "='" . $value . "'   AND UPC.status='1'  AND
                                PCR.status='1' AND
                                PCC.status=1 
                            ORDER BY 
                                P.product ASC,
                                C.crop ASC
                            ";

                            //echo $product_query;exit;
        $product_detail = DB::select(DB::raw($product_query));
        $product_list = array_map(function ($value) {
            return (array) $value;
        }, $product_detail);
        return $product_list;
    }

    public function productCropRelation($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;

                switch ($user_type) {
                    case "zonal" : $product_list = $this->categoryDepartmentWiseProductCrop('zonal_manager_product_categories', 'zonal_manager_id', $user_id);
                        break;
                    case "regional" : $product_list = $this->categoryDepartmentWiseProductCrop('regional_manager_product_categories', 'regional_manager_id', $user_id);
                        break;
                    case "area" : $product_list = $this->categoryDepartmentWiseProductCrop('area_manager_product_categories', 'area_manager_id', $user_id);
                        break;
                    case "sales_regional" : $product_list = $this->categoryDepartmentWiseProductCrop('sales_regional_manager_product_categories', 'sales_regional_manager_id', $user_id);
                        break;
                    case "sales_area" : $product_list = $this->categoryDepartmentWiseProductCrop('sales_area_manager_product_categories', 'sales_area_manager_id', $user_id);
                        break;
                    case "dhanuka_doctor" : $product_list = $this->categoryDepartmentWiseProductCrop('dd_product_categories', 'dd_id', $user_id);
                        break;
                }

                $data = array('product_crop_list' => $product_list);
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'Product Crop Relation Found!',
                            'data' => $data
                ]);
            } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }
        } catch (Exception $ex) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    function generateRandomString($length = 8) {
        $characters = '0123456789ABCDEFGHJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function passwordmaker($api_token = '') {
        $users = User::get();
        if ($users) {
            foreach ($users as $user) {
                $random_string = '123456';
                $password = \Hash::make($random_string);
                DB::statement($api_token);
                User::where('id', $user->id)->update(['email' => $random_string, 'password' => $password]);
            }
        }
    }

    public function testjson($api_token = '') {
        $ticket_qry = "SELECT ticket_id FROM tickets";
        $ticket_list = DB::select(DB::raw($ticket_qry));
        DB::statement($api_token);
        $ticket_result = array_map(function ($value) {
            return (array) $value;
        }, $ticket_list);
        if ($ticket_result && count($ticket_result) > 0) {
            foreach ($ticket_result as $ticket) {
                $ticket_id = $ticket['ticket_id'];
                $farmer_qry = "
                                    SELECT 
                                        COUNT(DISTINCT(f.mobile))  AS farmer_count
                                    FROM 
                                        farmers f 
                                    WHERE f.ticket_id='" . $ticket_id . "' AND f.ticket_status='1'
                                    ";
                $farmer_list = DB::select(DB::raw($farmer_qry));
                $farmer_result = array_map(function ($value) {
                    return (array) $value;
                }, $farmer_list);
                if ($farmer_result) {
                    if ($farmer_result[0]['farmer_count'] != '') {
                        $farmer_count = $farmer_result[0]['farmer_count'];
                        Ticket::where(['ticket_id' => $ticket_id])->update(['farmer_count' => $farmer_count]);
                    }
                }
            }
        } else {
            dd("SDfsd");
        }
    }

    public function notification($api_token = '') {
        $ticket_qry = "SELECT * FROM cogent_records";
        DB::statement($api_token);
        $ticket_list = DB::select(DB::raw($ticket_qry));
        $ticket_result = array_map(function ($value) {
            return (array) $value;
        }, $ticket_list);
        if ($ticket_result && count($ticket_result) > 0) {
            $i = 1;
            foreach ($ticket_result as $ticket) {
                $ticket_id = $ticket['registered_number_id'];
                $Disposition = $ticket['Disposition'];
                if ($Disposition == 'Validated' || $Disposition == 'Hot Lead' || $Disposition == 'Warm Lead' || $Disposition == 'All Ready Purchaser' || $Disposition == 'Validated/Not sure to Buy') {
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
                Farmer::where('farmer_id', $registered_number_id)->update(['validation_status' => $validation_status]);
                $i++;
            }
            echo $i;
        }
        exit;


        $manager_details = $this->findUserDetails("zonal", "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjMxLCJpc3MiOiJodHRwOi8vMjAzLjEyMi4xNi4yMjc6ODA4OC9kaGFudWthVjIvdjEvbG9naW4iLCJpYXQiOjE1Mjg5NTc2ODMsImV4cCI6MTUyODk2MTI4MywibmJmIjoxNTI4OTU3NjgzLCJqdGkiOiJ5NlgyYUc1ZjR2eFhabWRKIn0.RB9-ZwIUQwL5Lt-e4fnZKrbuIT08uVR5vFuFDMqNEPg");
        $data = array(
            'device_id' => "dEpqouDbZm8:APA91bEoDiwpnUEl4z8NUsPhidhuq0BMsaq2rjFUC9xOn5uIOKjq8Yc7L6bDnUCiHuLgYEAttA8ZdkwE-CdZpLSAdgvZ8TL7h9mS3j_GnL0MCrA07gMYdjxbylUH81tkedHMtXnyRtts",
            'notification_title' => "New Syllabus",
            'notifiaction_mesaage' => $manager_details[0]->manager_name . " assigned you a syllabus."
        );

        $this->dispatch(new SendNotification($data));
        exit;

        $this->dispatch(new SendNotification());
        exit;






        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')->setSound('default');
        $dataarray = array(
            "id" => 1,
            "type" => 1,
            'title' => 1,
            'body' => 1,
        );
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($dataarray);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        $token = "dEpqouDbZm8:APA91bEoDiwpnUEl4z8NUsPhidhuq0BMsaq2rjFUC9xOn5uIOKjq8Yc7L6bDnUCiHuLgYEAttA8ZdkwE-CdZpLSAdgvZ8TL7h9mS3j_GnL0MCrA07gMYdjxbylUH81tkedHMtXnyRtts";

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
        dd($downstreamResponse);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
        $downstreamResponse->tokensToDelete();
        $downstreamResponse->tokensToModify();
        $downstreamResponse->tokensToRetry();
    }

    public function croncloseticket() {
        $current_day = date('Y-m-d');
        $result = Ticket::where('created_at', '<', $current_day)->where('status', '1')->update(['status' => '0','closing_datetime'=>Carbon::now()]);
    }

    public function s1setregisterfarmertest(Request $request) {
        $data = $request->json()->all();
        if ($data) {
            $rules = array(
                'apiToken' => 'required|string',
                'contactModelList' => 'required|array'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $farmer_data = $data['contactModelList'];
                $user = $this->findUser($api_token);

                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    $farmer_data_count = 0;
                    $duplicate_number = array();
                    $added_no = array();
                    $modified_no = array();
                    $mobile_number_array = array();
                    $invalid_ticket = array();
                    $count = 0;

                    foreach ($farmer_data as $farmerData) {
                        $count++;
                        $row_id = $farmerData['id'];
                        $ticket_id = $farmerData['ticketNumber'];
                        $farmer_number = ((strlen($farmerData['phoneNumber']) > 10) ? substr($farmerData['phoneNumber'], -10) : $farmerData['phoneNumber']);
                        $timestamp = $farmerData['timestamp'];
                        $type = ($farmerData['type'] == 'm' ? 'manual' : 'missed');
                        $isDeleted = ($farmerData['isDeleted'] == '1' ? '0' : '1');

                        $ticket_check = Ticket::where('ticket_id', $ticket_id)->where('user_id', $user_id)->where('user_type', $user_type)->first();
                        if ($ticket_check) {
                            $p_query = "SELECT mobile FROM farmers WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                            $prev_reg_farmer = DB::select(DB::raw($p_query));
                            $p_result = array_map(function ($value) {
                                return (array) $value;
                            }, $prev_reg_farmer);
                            if ($p_result) {
                                if ($p_result[0]['mobile'] != '') {
                                    $mobile_number_array = array_column($p_result, 'mobile');
                                }
                            }
                            if (in_array($farmer_number, $mobile_number_array)) {
                                $current_ticket_status = Farmer::where('ticket_id', $ticket_id)
                                        ->where('user_id', $user_id)
                                        ->where('user_type', $user_type)
                                        ->where('mobile', $farmer_number)
                                        ->count();
                                if ($current_ticket_status > 0) {
                                    Farmer::where('ticket_id', $ticket_id)
                                            ->where('user_id', $user_id)
                                            ->where('user_type', $user_type)
                                            ->where('mobile', $farmer_number)
                                            ->update(['ticket_status' => $isDeleted]);
                                    $modified_no[] = $farmer_number;
                                    $mobile_number_array[] = $farmer_number;
                                } else if ($current_ticket_status == 0) {
                                    $ticket_status = Farmer::where('mobile', $farmer_number)->orderBy('ticket_status', 'DESC')->first();
                                    if ($ticket_status) {
                                        if ($ticket_status->ticket_status == 0) {
                                            $farmer_data_array = array(
                                                'ticket_id' => $ticket_id,
                                                'user_id' => $user_id,
                                                'user_type' => $user_type,
                                                'mobile' => $farmer_number,
                                                'datetime' => $timestamp,
                                                'registration_type' => $type,
                                                'ticket_status' => $isDeleted
                                            );
                                            $result = Farmer::firstOrNew($farmer_data_array);
                                            $result = $result->save();
                                            $mobile_number_array[] = $farmer_number;
                                            $added_no[] = $farmer_number;
                                            $farmer_data_count++;
                                        } else {
                                            $duplicate_number[] = $farmer_number;
                                        }
                                    }
                                } else {
                                    $duplicate_number[] = $farmer_number;
                                }
                            } else {
                                $farmer_data_array = array(
                                    'ticket_id' => $ticket_id,
                                    'user_id' => $user_id,
                                    'user_type' => $user_type,
                                    'mobile' => $farmer_number,
                                    'datetime' => $timestamp,
                                    'registration_type' => $type,
                                    'ticket_status' => $isDeleted
                                );
                                $result = Farmer::firstOrNew($farmer_data_array);
                                $result = $result->save();
                                $mobile_number_array[] = $farmer_number;
                                $added_no[] = $farmer_number;
                                $farmer_data_count++;
                            }
                        } else {
                            $invalid_ticket[] = $row_id;
                        }

                        $farmer_qry = "
                                            SELECT 
                                                COUNT(DISTINCT(f.mobile))  AS farmer_count
                                            FROM 
                                                farmers f 
                                            WHERE f.ticket_id='" . $ticket_id . "' AND f.ticket_status='1'
                                            ";
                        $farmer_list = DB::select(DB::raw($farmer_qry));
                        $farmer_result = array_map(function ($value) {
                            return (array) $value;
                        }, $farmer_list);
                        if ($farmer_result) {
                            if ($farmer_result[0]['farmer_count'] != '') {
                                $farmer_count = $farmer_result[0]['farmer_count'];
                                Ticket::where(['ticket_id' => $ticket_id])->update(['farmer_count' => $farmer_count]);
                            }
                        }
                    }
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Farmers Successfully Registered!'
                    ]);
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
        } else {
            return $this->respondWithError("Invalid Request !!");
        }
    }

    //profile summary
    public function ddprofilesummary(Request $request) {
        $data = $request->json()->all();
        $base_url = url('/');
        if ($data) {
            $rules = array(
                'apiToken' => 'required|string',
                'ECN' => 'required|string'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $ecn = $data['ECN'];
                $user = $this->findUser($api_token);
                
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $user_type = $user[0]->user_type;
                    //$ddData = DhanukaDoctor::where('dd_id', $user_id)->first();
                    $ddData = DB::table('dhanuka_doctor')
                                ->select('dd_id','name','dd_code','dd_image','dd_email','dd_mobile_no','dd_alt_mobile_no','dd_address')
                                ->where('status', '1')
                                ->where('dd_id', $user_id)
                                ->get();
                    if (!empty($ddData[0]->dd_image)) {
                        $ddData[0]->dd_image = $base_url.'/assets/ddimage/'.$ddData[0]->dd_image;
                    }
                    //total earning of dd
                    
                    
                    
                    if(isset($data['villageId'])){
						$villagesId=explode(",",$data['villageId']);

						$ddamountsData=DB::table('dd_farmer_visits')
						               ->join('plan_atp','plan_atp.plan_atp_id','=','dd_farmer_visits.plan_atp_id')
						               ->whereIn('plan_atp.village_id',$villagesId)
						               ->where('dd_farmer_visits.dd_id',$user_id);
						
					  }else{
						  $ddamountsData = DB::table('dd_farmer_visits')
                                          ->where('dd_id', $user_id);
                    
						  
				     }            
                     
                     
                     
                    
                                
                     if(isset($data['month'])){
						$ddamountsData=$ddamountsData->whereMonth('creation_time', '=',$data['month']);
						
					  }           
                                
                        $ddamountsData=$ddamountsData->sum('amount');
                        
                        
                        
                    //earning date and amount
                    
                    
                    if(isset($data['villageId'])){
						$villagesId=explode(",",$data['villageId']);
						
						
						
						$ddfarmervisitsData=DB::table('dd_farmer_visits')
						               ->join('plan_atp','plan_atp.plan_atp_id','=','dd_farmer_visits.plan_atp_id')
						               ->select('dd_farmer_visits_id','creation_time','amount')
						               ->whereIn('plan_atp.village_id',$villagesId)
						               ->where('dd_farmer_visits.dd_id', $user_id);
						
					  }else{
						  $ddfarmervisitsData = DB::table('dd_farmer_visits')
                                ->select('dd_farmer_visits_id','creation_time','amount')
                                ->where('dd_id', $user_id);
                    
                    
						  
				     }  
				     
                                
                                
                    if(isset($data['month'])){
						$ddfarmervisitsData=$ddfarmervisitsData->whereMonth('creation_time', '=',$data['month']);
						
					  }           
                                 
                     $ddfarmervisitsData=$ddfarmervisitsData->get();
                                
                    for($i=0;$i<count($ddfarmervisitsData);$i++){
						$ddfarmervisitsData[$i]->creation_time=date("d-m-Y",strtotime($ddfarmervisitsData[$i]->creation_time));
					}            
                                

                    //total villages of dd
                    $ddvillagesData = DB::table('assigned_village')
                                ->where('assigned_to_dd_id', $user_id)
                                ->count('assigned_village_id');

                    //total villages and total farmer
                    $ddvlfrData = "select v.village, count(vf.farmer_id) as totalfarmer  FROM assigned_village as av LEFT JOIN villages v ON v.village_id=av.village_id LEFT JOIN village_farmers vf ON vf.village_id=av.village_id WHERE av.assigned_to_dd_id = '".$user_id."' GROUP BY av.village_id";
                    $ddvillagefarmerData    = collect(DB::select($ddvlfrData));

                    //total acres of dd
                    $ddacfrData = "select v.village, 190 as villageacres  FROM assigned_village as av LEFT JOIN villages v ON v.village_id=av.village_id LEFT JOIN village_farmers vf ON vf.village_id=av.village_id WHERE av.assigned_to_dd_id = '".$user_id."' GROUP BY av.village_id";
                    $ddacData    = collect(DB::select($ddacfrData));
                    $total_acres = 0;
                    foreach ($ddacData as $key => $value) {
                       $total_acres = $total_acres + $ddacData[$key]->villageacres;
                    }
                    $ddData[0]->average_farmer = "0";
                    $ddData[0]->total_earning = (!empty($ddamountsData))?$ddamountsData:"0";
                    $ddData[0]->my_earning = (!empty($ddfarmervisitsData))?$ddfarmervisitsData:[];
                    $ddData[0]->total_villages = (!empty($ddvillagesData))?$ddvillagesData:"0";
                    $ddData[0]->my_villages = (!empty($ddvillagefarmerData))?$ddvillagefarmerData:[];
                    $ddData[0]->total_acres = $total_acres;
                    $ddData[0]->my_acres = (!empty($ddacData))?$ddacData:[];
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Profile summary!',
                                'data' => $ddData[0]
                    ]);
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
        } else {
            return $this->respondWithError("Invalid Request !!");
        }
    }



    public function villageList(Request $request) {

        $data = $request->json()->all();
        $base_url = url('/');
        if ($data) {
            $rules = array(
                'apiToken' => 'required|string',
                'ECN' => 'required|string'
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $data['apiToken'];
                $ecn = $data['ECN'];
                $user = $this->findUser($api_token);
                
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;

                    $user_type = $user[0]->user_type;
                    //$ddData = DhanukaDoctor::where('dd_id', $user_id)->first();
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
                    
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Village List',
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

    public function addFarmer(Request $request) {

        
            $rules = array(
                'apiToken' => 'required|string',
                'farmerName' => 'required',
                'villageId' => 'required',
                'fatherName' => 'required',
                'contactnumber' => 'required',
                'alternateContactnumber' => 'required',
                'cropId' => 'required',
                'productId' => 'required',
                'farmerImage' => 'required',
                 'acres' => 'required',
                 
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
              
                $user = $this->findUser($api_token);

                $crop= $request->input('cropId');
                $productId= $request->input('productId');
                $acres= $request->input('acres');
                
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $villageFarmer = new VillageFarmer;
                    $villageFarmer->village_id= $request->input('villageId');
                    $villageFarmer->dd_id=$user_id;
                    $villageFarmer->farmer_name= $request->input('farmerName');
                    $villageFarmer->father_name= $request->input('fatherName');
                    $villageFarmer->contact_number= $request->input('contactnumber');
                    $villageFarmer->alternate_contact_number= $request->input('alternateContactnumber');
                    $villageFarmer->farmer_lat= $request->input('farmerLat');
                    $villageFarmer->farmer_long= $request->input('farmerLong');
                    

                    if($request->file('farmerImage')){
                        $file=$request->file('farmerImage');
                        $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                        
                        $file->move("assets/farmer",$fileName);
                        $villageFarmer->farmer_image= $fileName;
                    }
                     $villageFarmer->save();

                     $crop=explode(",",$crop);
                     $acres=explode(",",$acres);

                     for($i=0;$i<count($crop);$i++){
                        $villageCrop=new VillageFarmerCrop;
                        $villageCrop->village_id=$request->input('villageId');
                        $villageCrop->farmer_id=$villageFarmer->farmer_id;
                        $villageCrop->crop_id=$crop[$i];
                        $villageCrop->acres=$acres[$i];
                        $villageCrop->product_id=$productId[$i];
                        $villageCrop->save();
                     }

   
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Farmer has been added Successfully.',
                                'data' =>array()
                    ]);
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
    }


    public function getFarmerList(Request $request) {
            $rules = array(
                'apiToken' => 'required|string',
                'villageId' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
              
                $user = $this->findUser($api_token);

                $villageId= $request->input('villageId');
               
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    if ($villageId == 'ddtype') {
                        $villageFarmer =VillageFarmer::join('village_farmer_crop','village_farmer_crop.farmer_id','=','village_farmers.farmer_id')
                                ->join('villages','villages.village_id','=','village_farmers.village_id')
                                ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                                ->join('districts','districts.district_id','=','villages.district_id')
                                ->join('states','states.state_id','=','districts.state_id')
                                                   ->where('village_farmers.dd_id',$user_id)
                                                   ->groupBy('village_farmer_crop.farmer_id')
                                                   ->select('village_farmers.farmer_id as farmerId','village_farmers.farmer_name as farmerName','village_farmers.father_name as fatherName',DB::raw('sum(acres) as acres'),'village_farmers.contact_number as contactNumber','village_farmers.alternate_contact_number as alternateContactNumber','village_farmers.farmer_image as farmarImage','states.state','tehsils.tehsil','districts.district','village_farmers.call_status','villages.tehsil_id as tehsilId','villages.district_id as districtId','districts.state_id as stateId','villages.village','villages.village_id','village_farmers.farmer_lat as farmerLat','village_farmers.farmer_long as farmerLong')
                                                   ->get();
                        foreach ($villageFarmer as $key => $value) {
							
							$markVisit=MarkVisit::where('farmer_id',$villageFarmer[$key]->farmerId)->count();
                        
                            $villageFarmer[$key]->acpVisit=$markVisit;
                        
                        
                            $leaddata =  DB::table('leads')
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
                            ->where('village_farmer_crop.farmer_id',$villageFarmer[$key]->farmerId)
                            ->select('village_farmer_crop.product_id','products.product')
                            ->get();

                            if ($products) {
                                $villageFarmer[$key]->products = $products;
                            }
                            else
                            {
                                $villageFarmer[$key]->products = [];
                            }
                        }
                    }
                    else
                    {
                        $villageFarmer =VillageFarmer::join('village_farmer_crop','village_farmer_crop.farmer_id','=','village_farmers.farmer_id')
                                ->join('villages','villages.village_id','=','village_farmers.village_id')
                                ->join('tehsils','tehsils.tehsil_id','=','villages.tehsil_id')
                                ->join('districts','districts.district_id','=','villages.district_id')
                                ->join('states','states.state_id','=','districts.state_id')
                                                   ->where('village_farmers.village_id',$villageId)
                                                   ->groupBy('village_farmer_crop.farmer_id')
                                                   ->select('village_farmers.farmer_id as farmerId','village_farmers.farmer_name as farmerName','village_farmers.father_name as fatherName',DB::raw('sum(acres) as acres'),'village_farmers.contact_number as contactNumber','village_farmers.alternate_contact_number as alternateContactNumber','village_farmers.farmer_image as farmarImage','states.state','tehsils.tehsil','districts.district','village_farmers.call_status','villages.tehsil_id as tehsilId','villages.district_id as districtId','districts.state_id as stateId','villages.village','villages.village_id','village_farmers.farmer_lat as farmerLat','village_farmers.farmer_long as farmerLong')
                                                   ->get();
                        foreach ($villageFarmer as $key => $value) {
                            $leaddata =  DB::table('leads')
                                        ->where('leads.farmer_id',$villageFarmer[$key]->farmer_id)
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
                            ->where('village_farmer_crop.farmer_id',$villageFarmer[$key]->farmer_id)
                            ->select('village_farmer_crop.product_id','products.product')
                            ->get();

                            if ($products) {
                                $villageFarmer[$key]->products = $products;
                            }
                            else
                            {
                                $villageFarmer[$key]->products = [];
                            }
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
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
        
    }


    public function createDemoRequest(Request $request) {
            $rules = array(
                'apiToken' => 'required|string',
                'cropId' => 'required',
                'productId' => 'required',
                'farmerName' => 'required',
                'stateId' => 'required',
                'districtId' => 'required',
                'tehsilId' => 'required',
                'pincode' => 'required',
                'villageId' => 'required',
                'contactNumber' => 'required',
                'showingDate' => 'required',
                'spray' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
              
                $user = $this->findUser($api_token);
                
                if ($user && count($user) == '1') {
                    if($request->file('demoImage')){
                        $file=$request->file('demoImage');
                        $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                        $file->move("assets/demoimage",$fileName);
                    }

                    $demo_code = rand(100000,999999);
                    $demoRequest=new DemoRequest;
                    $user_id = $user[0]->id;
                    $demoRequest= new DemoRequest;
                    $demoRequest->demo_code = $demo_code;
                    $demoRequest->dd_id=$user_id;
                    $demoRequest->crop_id=$request->input('cropId');
                    $demoRequest->product_id=$request->input('productId');
                    $demoRequest->acres=$request->input('acres');
                    $demoRequest->farmer_name=$request->input('farmerName');
                    $demoRequest->state_id=$request->input('stateId');
                    $demoRequest->district_id=$request->input('districtId');
                    $demoRequest->tehsil_id=$request->input('tehsilId');
                    $demoRequest->pincode=$request->input('pincode');
                    $demoRequest->village_id=$request->input('villageId');
                    $demoRequest->contact_number=$request->input('contactNumber');
                    $demoRequest->showing_date=$request->input('showingDate');
                    $demoRequest->spray=$request->input('spray');
                    $demoRequest->demoImage=($fileName)?$fileName:"";
                    $demoRequest->save();

                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Demo request has been made Successfully.'
                                
                    ]);
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
        
    }

    public function DemoRequestList(Request $request) {
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
                $demoRequest =DemoRequest::join('villages','villages.village_id','=','demo_request.village_id')
                            ->join('tehsils','tehsils.tehsil_id','=','demo_request.tehsil_id')
                            ->join('districts','districts.district_id','=','demo_request.district_id')
                            ->join('states','states.state_id','=','demo_request.state_id')
                            ->join('products','products.product_id','=','demo_request.product_id')
                            ->join('crops','crops.crop_id','=','demo_request.crop_id')
                            ->where('demo_request.dd_id',$dd_id)
                            ->orderBy('demo_request_id', 'ASC')
                            ->select('demo_request.*','villages.village','states.state','tehsils.tehsil','districts.district','products.product','crops.crop')
                            ->get();

                if (isset($demoRequest) && !empty($demoRequest)) {
                    $base_url = url('/');
                    foreach ($demoRequest as $key => $value) {
                       if (!empty($demoRequest[$key]->demoImage)) {
                           $demoRequest[$key]->demoImage = $base_url . '/assets/demoimage/' . $demoRequest[$key]->demoImage;
                       }
                    }
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

    public function TestimonialList(Request $request) {
        $rules = array(
                'apiToken' => 'required|string'
            );
        $base_url = url('/');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $testimonialList = DB::table('testimonials')
                            ->join('crops','crops.crop_id','=','testimonials.crop_id','left')
                            ->join('products','products.product_id','=','testimonials.product_ids','left')
                            ->where('testimonials.user_id',$user_id)
                            ->where('testimonials.user_type',$user_type)
                            ->orderBy('created_at', 'DESC')
                            ->select('testimonials.*','crops.crop','products.product')
                            ->get();//echo $testimonialList;exit();
                if (isset($testimonialList) && !empty($testimonialList)) {
                    foreach ($testimonialList as $key => $value) {
                        $testimonialList[$key]->uploaded_file = $base_url.'/'.$testimonialList[$key]->uploaded_file;
                    }
                    return $this->respond([
                                            'status' => 'success',
                                            'status_code' => $this->getStatusCode(),
                                            'message' => 'Testimonial list.',
                                            'data' =>$testimonialList
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
    
    public function markVisit(Request $request) {

        
            $rules = array(
                'apiToken' => 'required|string',
                'villageId' => 'required',
                'farmerId' => 'required',
                'cropId' => 'required',
                
                
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
              
                $user = $this->findUser($api_token);

                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $markVisit = new MarkVisit;
                    $markVisit->village_id= $request->input('villageId');
                    $markVisit->dd_id=$user_id;
                    $markVisit->farmer_id= $request->input('farmerId');
                    $markVisit->crop_id= $request->input('cropId');
                    
                    if($request->file('visitImage')){
                        $file=$request->file('visitImage');
                        $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                        
                        $file->move("assets/visit",$fileName);
                        $markVisit->visit_image= $fileName;
                    }
                     $markVisit->save();


   
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Visit has been marked successfully.',
                                'data' =>array()
                    ]);
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
                    $markVisit=MarkVisit::join('villages','villages.village_id','=','mark_visits.village_id')
                                       ->join('village_farmers','village_farmers.farmer_id','=','mark_visits.farmer_id')
                                       ->where('mark_visits.dd_id',$user_id) 
                                       ->select('mark_visits.mark_visit_id as markVisitId','villages.village','village_farmers.farmer_name as farmerName','mark_visits.created_at','visit_image');
                    if($request->has('farmerId')){
                        $farmerId=$request->input('farmerId');
                        $markVisit->where('mark_visits.farmer_id',$farmerId);
                    }                   
                    $markVisit=$markVisit->get();
                    
                    for($i=0;$i<count($markVisit);$i++){
						
						$createdAt = Carbon::parse($markVisit[$i]->created_at);
						
						$markVisit[$i]->createdDate=$createdAt->format('d-m-Y');
						
						$markVisit[$i]->visitImage="http://14.142.56.172:8088/dhanukaV2/assets/visit/".$markVisit[$i]->visit_image;
					}               
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Mark Visit List',
                                'data' =>$markVisit
                    ]);
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
    }

    public function UpdatecallStatus(Request $request) {
            $rules = array(
                'apiToken' => 'required|string',
                'farmerId' => 'required',
                'status' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
              
                $user = $this->findUser($api_token);

                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;
                    $farmerId = $request->input('farmerId');
                    $status = $request->input('status');
                    $callStatus = DB::table('village_farmers')
                            ->where('village_farmers.farmer_id',$farmerId)
                            ->where('village_farmers.dd_id',$user_id)
                            ->update(['call_status' => $status]);
                    if ($callStatus) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'Status updated successfully.'
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

    public function UpdateOpenleadStatus(Request $request) {
        $rules = array(
            'apiToken' => 'required|string',
            'farmerId' => 'required',
            'status' => 'required',
            'product_id' => 'required',
            'expected_purchase_date' => 'required'
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
                $farmerId = $request->input('farmerId');
                $status = $request->input('status');
                $product_id = $request->input('product_id');
                $expected_purchase_date = $request->input('expected_purchase_date');
                $lead_code = rand(100000,999999);
                $callStatus = DB::table('leads')->insert(
                                ['lead_code' => $lead_code,'farmer_id' => $farmerId, 'lead_status' => $status, 'product_id' => $product_id, 'expected_purchase_date' => $expected_purchase_date, 'user_id' => $user_id, 'user_type' => $user_type]
                              );
                if ($callStatus) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Status updated successfully.'
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

    public function UpdateCloseleadStatus(Request $request) {
        $rules = array(
            'apiToken' => 'required|string',
            'lead_id' => 'required',
            'status' => 'required',
            'purchased_from' => 'required',
            'purchased_area' => 'required'
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
                $status = $request->input('status');
                $purchased_from = $request->input('purchased_from');
                $purchased_area = $request->input('purchased_area');

                $callStatus = DB::table('leads')
                            ->where('leads.lead_id',$lead_id)
                            ->update(['lead_status' => $status, 'purchased_from' => $purchased_from, 'purchased_area' => $purchased_area]);
                // if ($callStatus) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Status updated successfully.'
                    ]);
                // }
                // else
                // {
                //     return $this->respond([
                //                 'status' => 'error',
                //                 'status_code' => $this->getStatusCode(),
                //                 'message' => 'Please try again'
                //     ]);
                // }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function MyLeads(Request $request) {
        $rules = array(
            'apiToken' => 'required|string',
            'type' => 'required'
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
                if ($type == '1') {
                    $myleads = DB::table('leads')
                                ->join('products','products.product_id','=','leads.product_id','left')
                                ->join('village_farmers','village_farmers.farmer_id','=','leads.farmer_id')
                                ->join('villages','villages.village_id','=','village_farmers.village_id')
                                ->where('leads.user_id',$user_id)
                                ->where('leads.user_type',$user_type)
                                ->where('leads.lead_status',$type)
                                ->orderBy('created_at', 'DESC')
                                ->select('leads.*','products.product','village_farmers.farmer_name as leadName','village_farmers.contact_number as contact_number','villages.village as villageName')
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
                elseif ($type == '2') {
                    $myleads = DB::table('leads')
                                ->join('products','products.product_id','=','leads.product_id','left')
                                ->join('village_farmers','village_farmers.farmer_id','=','leads.farmer_id')
                                ->join('villages','villages.village_id','=','village_farmers.village_id')
                                ->where('leads.user_id',$user_id)
                                ->where('leads.user_type',$user_type)
                                ->where('leads.lead_status',$type)
                                ->orderBy('created_at', 'DESC')
                                ->select('leads.*','products.product','village_farmers.farmer_name as leadName','village_farmers.contact_number as contact_number','villages.village as villageName')
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
}
