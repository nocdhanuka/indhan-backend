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
use Illuminate\Support\Facades\Storage;

ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);

class BDOWebServiceController extends ApiController {

    protected $userTransformer;

    public function __construct(userTransformer $userTransformer) {
        $this->userTransformer = $userTransformer;
        
    }
   
    public function ddList($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') 
            {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                //$base_url = url('/');
                $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";

              if ($user_type == 'bdo') 
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
                  }
                  else
                  {
                    $bdo_ids = array($user_id);
                  }

                  $dd = DB::table('dhanuka_doctor as dd')
                        ->leftJoin('states as s','dd.state_id', '=', 's.state_id')
                        ->leftJoin('districts as d','dd.district_id', '=','d.district_id')
                        ->leftJoin('tehsils as t','dd.tehsil_id', '=','t.tehsil_id')
                        ->leftJoin('languages as l','dd.language_id', '=','l.id')
                        ->whereIn('dd.bdo_id',$bdo_ids)
                        ->where('dd.status','1')
                        ->select('dd.*','s.state','l.language','d.district','t.tehsil')
                        ->get(); //print_r($dd);die;
                  $ddArray = array();
                  foreach ($dd as $key => $value) 
                  {
                    $ddArray[$key]['dd_id'] =  $value->dd_id;
                    $ddArray[$key]['ECN'] =  $value->ECN;
                    $ddArray[$key]['name'] =  $value->name;
                    $ddArray[$key]['dd_code'] =  $value->dd_code;
                    $ddArray[$key]['dd_hq'] =  $value->dd_hq;
                    $ddArray[$key]['dd_mobile_no'] =  $value->dd_mobile_no;
                    $ddArray[$key]['dd_alt_mobile_no'] =  $value->dd_alt_mobile_no;
                    $ddArray[$key]['dd_address'] =  $value->dd_address;
                    if (!empty($value->dd_image)) {
                        $ddArray[$key]['dd_image'] = $base_url.'/assets/ddimage/'.$value->dd_image;
                    }
                    else
                    {
                        $ddArray[$key]['dd_image'] = "";
                    }
                 
                    $ddArray[$key]['dd_email'] =  $value->dd_email;
                    $ddArray[$key]['department'] =  $value->department;
                    $ddArray[$key]['designation'] =  $value->designation;
                    $ddArray[$key]['app_permission_edit_ticket'] =  $value->app_permission_edit_ticket;
                    $ddArray[$key]['app_permission_close_ticket'] =  $value->app_permission_close_ticket;
                    $ddArray[$key]['state'] =  $value->state;
                    $ddArray[$key]['language'] =  $value->language;
                    $ddArray[$key]['district'] =  $value->district;
                    $ddArray[$key]['tehsil'] =  $value->tehsil;
                    $village = DB::table('assigned_village as av')
                                ->leftJoin('villages as v','av.village_id', '=', 'v.village_id')
                                ->where('av.assigned_to_dd_id', $value->dd_id)
                                ->count();
                    $villageArray = array();

                    // foreach ($village as $keyV => $valueV) {
                    //     $villageArray[$keyV]['village_id'] = $valueV->village_id;
                    //     $villageArray[$keyV]['village'] = $valueV->village;
                    // }
                    $crop = DB::table('dd_product_categories as pc')
                            ->Join('product_crop_relations as pcr','pc.product_crop_category_id', '=', 'pcr.product_crop_category_id')
                            //->leftJoin('crops as c','pcr.crop_id', '=', 'c.crop_id')
                            ->where('pc.dd_id', $value->dd_id)
                            ->groupBy('pcr.crop_id')
                            ->select('c.crop','c.crop_id')
                            ->count();
                            //echo $crop;die;
                   // print_r($crop);die;
                    $cropArray = array();
                    // foreach ($crop as $keyC => $valueC) {
                    //   $cropArray[$keyC]['crop_id'] = $valueC->crop_id;
                    //   $cropArray[$keyC]['crop'] = $valueC->crop;
                    // }
                    $totalVisitFarmer = DB::table('mark_visits')
                                        ->where('dd_id', $value->dd_id)
                                        ->count();

                    $totalFarmer = DB::table('mark_visits')->where('dd_id', $value->dd_id)->distinct('farmer_id')->count('farmer_id');
                    // $averageFarmer = (int)$totalVisitFarmer/(int)$totalFarmer;
                    if($totalFarmer > 0)
                    {
                        $aveVal = (int)$totalVisitFarmer/(int)$totalFarmer;
                        $averageFarmer = number_format((float)$aveVal, 2, '.', '');
                    }else
                    {
                      $averageFarmer = 0;  
                    }
     
                    $ddArray[$key]['villagesCount'] =  $village;
                    $ddArray[$key]['cropCount'] =  $crop;
                    $ddArray[$key]['averageFarmer'] =  (float)$averageFarmer;
                    //$ddArray[$key]['villages'] =  $villageArray;
                    //$ddArray[$key]['crops'] =  $cropArray;
                }


                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'DD List',
                            'data' => $ddArray
                ]);
              }
            } else {
              return $this->respondWithError("Invalid Attempt !!");
            }
         
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
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


     public function editDD(Request $request) {
        $data = $request->all();
        $rules = array(
            'api_token' => 'required|string',
            'dd_id' => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            //echo "<pre>"; print_r($data); die;
             //$base_url = url('/');
             $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
            $ddData = array();
            $api_token = $data['api_token'];
            $dd_id = $data['dd_id'];
            $file = $request->file('dd_image');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
            $user_id = $user[0]->id;
            $user_type = $user[0]->user_type;

            if($request->input('name') != '')
            {
              $ddData['name'] = $request->input('name');  
            }
            if($request->input('dd_mobile_no') != '')
            {
               $ddData['dd_mobile_no'] = $request->input('dd_mobile_no');  
            }
            if($request->input('dd_alt_mobile_no') != '')
            {
               $ddData['dd_alt_mobile_no'] = $request->input('dd_alt_mobile_no');  
            }




            if ($request->hasFile('dd_image')) {
                if ($request->file('dd_image')->isValid()) {
                            // $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                            // $fileName = md5(time() . rand()) . str_replace($file->getClientOriginalName(), '', $file->getClientOriginalName()) . '.' . $extension;
                            // $destinationPath = 'assets/ddimage/';
                            // $sTempFileName = 'assets/ddimage/' . $fileName;
                            // $request->file('dd_image')->move($destinationPath, $fileName);
                            //s3Upload
                            $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                            $sTempFileName = 'assets/ddimage/' . $fileName;
                            Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                            //
                            $imageUrl = $base_url . '/' . $sTempFileName;
                            $ddData['dd_image'] = $fileName;
                } else {
                    return $this->respondWithError("Invalid File !!");
                }
            }

           $updateImage = DhanukaDoctor::where('dd_id', $dd_id)->update($ddData);
           $result = DhanukaDoctor::where('dd_id', $dd_id)->get()->toArray();
           // echo "<pre>"; print_r($result); die;
           $resData['dd_id'] = $result[0]['dd_id'];
           $resData['api_token'] = $result[0]['api_token'];
           $resData['name'] = $result[0]['name'];
           $resData['dd_mobile_no'] = $result[0]['dd_mobile_no'];
           $resData['dd_alt_mobile_no'] = $result[0]['dd_alt_mobile_no'];
           if($result[0]['dd_image'] != '')
           {
            $imageUrl = $base_url . '/assets/ddimage/' . $result[0]['dd_image'];
           }
           else
           {
            $imageUrl = '';
           }
           $resData['dd_image'] = $imageUrl;
                    return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'DD Detail Updated Successfully!',
                    'data' => $resData
                    ]);
              } else {
                        return $this->respondWithError("Invalid Attempt !!");
                    }
        }
    }




    public function teamSummary(Request $request) {
        try {
            $data = $request->all();
        $rules = array(
            'apiToken' => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } 
        else 
        {
          $api_token = $request->input('apiToken');
          $date = date('Y-m-d');
          $month = $request->input('month');
          $start_date = $request->input('start_date');
          $end_date = $request->input('end_date');
          $user = $this->findUser($api_token);
           if ($user && count($user) == '1') 
           {  
              $user_id = $user[0]->id;
              $user_type = $user[0]->user_type;
              //$base_url = url('/');
              $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
              if ($user_type == 'bdo') 
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
                }
                else
                {
                  $bdo_ids = array($user_id);
                }

                $dd1 = DB::table('dhanuka_doctor as dd')
                        ->leftJoin('states as s','dd.state_id', '=', 's.state_id')
                        ->leftJoin('districts as d','dd.district_id', '=','d.district_id')
                        ->leftJoin('tehsils as t','dd.tehsil_id', '=','t.tehsil_id')
                        ->leftJoin('languages as l','dd.language_id', '=','l.id')
                        ->whereIn('dd.bdo_id',$bdo_ids)
                        ->where('dd.status','1');
                    if($month != '')
                    {
                        $dd1->whereMonth('dd.created_at','=',$month);
                        $dd1->whereYear('dd.created_at','=',date('Y'));
                    }
                    if($start_date != '' && $end_date != '')
                    {
                       $dd1->whereDate('dd.created_at','>=',$start_date)
                            ->whereDate('dd.created_at','<=',$end_date); 
                    }
                   
                   $dd = $dd1->select('dd.*','s.state','l.language','d.district','t.tehsil')
                        ->get();
                       
                    $ddArray = array();
                    $ddArray1 = array();
                    $aveSumArray =  array();
                    $totalFarmerArray =  array();
                foreach ($dd as $key => $value) {
                   $ddArray[$key]['dd_id'] =  $value->dd_id;
                   $ddArray[$key]['ECN'] =  $value->ECN;
                   $ddArray[$key]['name'] =  $value->name;
                   $ddArray[$key]['dd_code'] =  $value->dd_code;
                   $ddArray[$key]['dd_hq'] =  $value->dd_hq;
                   $ddArray[$key]['dd_mobile_no'] =  $value->dd_mobile_no;
                   $ddArray[$key]['dd_alt_mobile_no'] =  $value->dd_alt_mobile_no;
                   $ddArray[$key]['dd_address'] =  $value->dd_address;
                   if (!empty($value->dd_image)) {
                        $ddArray[$key]['dd_image'] = $base_url.'/assets/ddimage/'.$value->dd_image;
                    }
                    else
                    {
                        $ddArray[$key]['dd_image'] = "";
                    }
                   
                   $ddArray[$key]['dd_email'] =  $value->dd_email;
                   $ddArray[$key]['department'] =  $value->department;
                   $ddArray[$key]['designation'] =  $value->designation;
                   $ddArray[$key]['app_permission_edit_ticket'] =  $value->app_permission_edit_ticket;
                   $ddArray[$key]['app_permission_close_ticket'] =  $value->app_permission_close_ticket;

                $totalVisitFarmer1 = DB::table('mark_visits')
                ->where('dd_id', $value->dd_id);
                 if($month != '')
                    {
                        $totalVisitFarmer1->whereMonth('created_at','=',$month);
                    }
                     if($start_date != '' && $end_date != '')
                    {
                       $totalVisitFarmer1->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date); 
                    }
                $totalVisitFarmer = $totalVisitFarmer1->count();
                // echo "<pre>"; print_r($totalVisitFarmer); die;

                $totalFarmer1 = DB::table('mark_visits')
                ->where('dd_id', $value->dd_id);
                if($month != '')
                    {
                        $totalFarmer1->whereMonth('created_at','=',$month);
                    }
                    if($start_date != '' && $end_date != '')
                    {
                       $totalFarmer1->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date); 
                    }
               $totalFarmer = $totalFarmer1->distinct('farmer_id')
                ->count('farmer_id');
                // $averageFarmer = (int)$totalVisitFarmer/(int)$totalFarmer;
                if($totalFarmer > 0)
                    {
                    $aveVal = (int)$totalVisitFarmer/(int)$totalFarmer;
                    $averageFarmer = number_format((float)$aveVal, 2, '.', '');
                    }
                    else
                    {
                    $averageFarmer = 0;  
                    }
                    array_push($aveSumArray, (float)$averageFarmer);

                     $farmerCount1 = DB::table('village_farmers')
                        ->where('dd_id', $value->dd_id)
                        ->where('status', '1');
                        if($month != '')
                    {
                        $farmerCount1->whereMonth('created_at','=',$month);
                    }
                    if($start_date != '' && $end_date != '')
                    {
                       $farmerCount1->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date); 
                    }
                      $farmerCount = $farmerCount1->count();

                    array_push($totalFarmerArray, $farmerCount);


                }
                $sumFarmerCount = array_sum($totalFarmerArray);
                $sumofVist =  array_sum($aveSumArray); 
                $totalCountSumAr =  count($aveSumArray); 
                if($totalCountSumAr > 0)
                {
                 $visitAverage = number_format((float)$sumofVist, 2, '.', '')/$totalCountSumAr;   
                } 
                else{
                  $visitAverage = 0;  
                }             
                
             

                $villageCount1 = DB::table('assigned_village as av')
                                  ->leftJoin('villages as v','av.village_id', '=', 'v.village_id')
                                  ->whereIn('av.assigned_by_bdo_id', $bdo_ids);
                                  if($month != '')
                                  {
                                      $villageCount1->whereMonth('av.created_at','=',$month);
                                  }
                                  if($start_date != '' && $end_date != '')
                                  {
                                     $villageCount1->whereDate('av.created_at','>=',$start_date)
                                          ->whereDate('av.created_at','<=',$end_date); 
                                  }
                                   $villageCount = $villageCount1->count();

                $upcome1 = DB::table('plan_atp as pa')
                        ->leftJoin('dhanuka_doctor as dd','pa.dd_id', '=', 'dd.dd_id')
                        ->leftJoin('crops as c','pa.crop_id', '=', 'c.crop_id')
                        ->leftJoin('products as p','pa.product_id', '=', 'p.product_id')
                        ->whereIn('pa.created_by_user_id', $bdo_ids)
                        ->where('pa.user_type', 'bdo');
                        if(!empty($month))
                        {
                          $upcome1->whereMonth('pa.created_at','=',$month);
                        }
                        if(!empty($start_date) && !empty($end_date))
                        {
                          //$upcome1->whereDate('pa.created_at','>=',$start_date)->whereDate('pa.created_at','<=',$end_date); 
                          $upcome1->where(DB::raw("DATE_FORMAT(`pa`.`plan_date`, '%Y-%m-%d')"),'>',DB::raw("DATE_FORMAT('".$start_date."','%Y-%m-%d')"));
                          $upcome1->where(DB::raw("DATE_FORMAT(`pa`.`plan_date`, '%Y-%m-%d')"),'<',DB::raw("DATE_FORMAT('".$end_date."','%Y-%m-%d')"));
                        }
                        else
                        { 
                          $upcome1->where(DB::raw("DATE_FORMAT(`pa`.`plan_date`,'%Y-%m-%d')"),'>', DB::raw("DATE_FORMAT('".$date."','%Y-%m-%d')"));
                        }
                        $upcome = $upcome1->where('pa.status','0')
                        ->get();//echo $upcome;die;
                //print_r($upcome);die;
                foreach ($upcome as $keyUp => $valueUp) {
                   $ddArray1[$keyUp]['plan_atp_id'] =  $valueUp->plan_atp_id;
                   $ddArray1[$keyUp]['plan_date'] =  $valueUp->plan_date;
                   $ddArray1[$keyUp]['crop'] =  $valueUp->crop;
                   $ddArray1[$keyUp]['product'] =  $valueUp->product;
                   $ddArray1[$keyUp]['name'] =  $valueUp->name;
                   $ddArray1[$keyUp]['dd_id'] =  $valueUp->dd_id;
                   $ddArray1[$keyUp]['crop_id'] =  $valueUp->crop_id;
                   $ddArray1[$keyUp]['plan_atp_id'] =  $valueUp->plan_atp_id;
                   $ddArray1[$keyUp]['product_id'] =  $valueUp->product_id;
                   $ddArray1[$keyUp]['api_token'] =  $valueUp->api_token;
                   
                   if (!empty($valueUp->image)) {
                        $ddArray1[$keyUp]['image'] = $base_url.'/assets/atpimage/'.$valueUp->image;
                    }
                    else
                    {
                        $ddArray1[$keyUp]['image'] = "";
                    }
                }
                // echo "<pre>"; print_r($upcome); die;
               

                $teamSummary['ddCount'] = count($ddArray);
                $teamSummary['villageCount'] = $villageCount;
                $teamSummary['farmerCount'] = $sumFarmerCount;
                $teamSummary['averageVisit'] = round((float)$visitAverage,2);
                $teamSummary['upcomingATP'] = $ddArray1;
                $teamSummary['ddDetail'] = $ddArray;
                
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'DD List',
                            'data' => $teamSummary
                ]);
              }
              else
              {
                //supervisor team summary
                if ($user_type == 'zonal') {
                  //zonal manager dd
                  // $ddData = DB::table('dhanuka_doctor')
                  //           ->where('zonal_manager_id',$user_id)
                  //           ->select('dd_id')
                  //           ->get();
                  // $ddresult = array(); 
                  // if (isset($ddData) && !empty($ddData)) 
                  // {
                  //     $ddDatass = json_decode(json_encode($ddData),true);
                      
                  //     foreach ($ddDatass as $key => $value) { 
                  //         if (is_array($value)) 
                  //         { 
                  //             $ddresult = array_merge($ddresult, array_flatten($value)); 
                  //         } 
                  //         else 
                  //         { 
                  //             $ddresult[$key] = $value; 
                  //         } 
                  //     } 
                  // }

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
                    // $ddData = DB::table('dhanuka_doctor')
                    //           ->where('regional_manager_id',$user_id)
                    //           ->select('dd_id')
                    //           ->get();
                    // $ddresult = array(); 
                    // if (isset($ddData) && !empty($ddData)) 
                    // {
                    //     $ddDatass = json_decode(json_encode($ddData),true);
                        
                    //     foreach ($ddDatass as $key => $value) { 
                    //         if (is_array($value)) 
                    //         { 
                    //             $ddresult = array_merge($ddresult, array_flatten($value)); 
                    //         } 
                    //         else 
                    //         { 
                    //             $ddresult[$key] = $value; 
                    //         } 
                    //     } 
                    // }

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
                    // $ddData = DB::table('dhanuka_doctor')
                    //           ->where('area_manager_id',$user_id)
                    //           ->select('dd_id')
                    //           ->get();
                    // $ddresult = array(); 
                    // if (isset($ddData) && !empty($ddData)) 
                    // {
                    //     $ddDatass = json_decode(json_encode($ddData),true);
                        
                    //     foreach ($ddDatass as $key => $value) { 
                    //         if (is_array($value)) 
                    //         { 
                    //             $ddresult = array_merge($ddresult, array_flatten($value)); 
                    //         } 
                    //         else 
                    //         { 
                    //             $ddresult[$key] = $value; 
                    //         } 
                    //     } 
                    // }

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
                    // $ddData = DB::table('dhanuka_doctor')
                    //           ->where('sales_regional_manager_id',$user_id)
                    //           ->select('dd_id')
                    //           ->get();
                    // $ddresult = array(); 
                    // if (isset($ddData) && !empty($ddData)) 
                    // {
                    //     $ddDatass = json_decode(json_encode($ddData),true);
                        
                    //     foreach ($ddDatass as $key => $value) { 
                    //         if (is_array($value)) 
                    //         { 
                    //             $ddresult = array_merge($ddresult, array_flatten($value)); 
                    //         } 
                    //         else 
                    //         { 
                    //             $ddresult[$key] = $value; 
                    //         } 
                    //     } 
                    // }

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
                    // $ddData = DB::table('dhanuka_doctor')
                    //           ->where('sales_area_manager_id',$user_id)
                    //           ->select('dd_id')
                    //           ->get();
                    // $ddresult = array(); 
                    // if (isset($ddData) && !empty($ddData)) 
                    // {
                    //     $ddDatass = json_decode(json_encode($ddData),true);
                    //     $ddresult = [];
                    //     foreach ($ddDatass as $key => $value) { 
                    //         if (is_array($value)) 
                    //         { 
                    //             $ddresult = array_merge($ddresult, array_flatten($value)); 
                    //         } 
                    //         else 
                    //         { 
                    //             $ddresult[$key] = $value; 
                    //         } 
                    //     } 
                    // }

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
                          
                      }
                  }
                }
                
                //rsm
                if ($request->has('rsm')) {
                  $rsm=$request->input('rsm');
                }
                else
                {
                  $rsm = "";
                }

                //asm
                if ($request->has('asm')) {
                  $asm=$request->input('asm');
                }
                else
                {
                  $asm = "";
                }

                //fl
                if ($request->has('bdo')) {
                  $bdo=$request->input('bdo');
                }
                else
                {
                  $bdo = "";
                }
                $dd1 = DB::table('dhanuka_doctor as dd')
                        ->leftJoin('states as s','dd.state_id', '=', 's.state_id')
                        ->leftJoin('districts as d','dd.district_id', '=','d.district_id')
                        ->leftJoin('tehsils as t','dd.tehsil_id', '=','t.tehsil_id')
                        ->leftJoin('languages as l','dd.language_id', '=','l.id');
                        if (!empty($rsm) && empty($asm) && empty($bdo)) 
                        { 
                            //$rsm=$request->input('rsm');
                            $dd1->Where('dd.sales_regional_manager_id',$rsm);
                        }
                        elseif (!empty($rsm) && !empty($asm) && empty($bdo)) 
                        {
                          //$asm=$request->input('asm');
                          $dd1->where('dd.sales_area_manager_id',$asm);
                        }
                        elseif (empty($rsm) && !empty($asm) && empty($bdo)) 
                        {
                          //$asm=$request->input('asm');
                          $dd1->where('dd.sales_area_manager_id',$asm);
                        }
                        elseif (!empty($rsm) && !empty($asm) && !empty($bdo)) { 
                            //$bdo_id=$request->input('bdo');
                            $dd1->Where('dd.bdo_id',$bdo);
                        }
                        elseif (empty($rsm) && !empty($asm) && !empty($bdo)) { 
                            //$bdo_id=$request->input('bdo');
                            $dd1->Where('dd.bdo_id',$bdo);
                        }
                        elseif (empty($rsm) && empty($asm) && !empty($bdo)) { 
                            //$bdo_id=$request->input('bdo');
                            $dd1->Where('dd.bdo_id',$bdo);
                        }
                        elseif (empty($rsm) && empty($asm) && empty($bdo))
                        { 
                          if (count($bdoresult) > 0) 
                          {
                            $dd1->whereIn('dd.bdo_id',$bdoresult);
                          }
                          else
                          {
                            $dd1->where('1','!=','1');
                          }
                        }

                        
                        if($month != '')
                        {
                            $dd1->whereMonth('dd.created_at','=',$month);
                        }
                        if($start_date != '' && $end_date != '')
                        {
                            $dd1->whereDate('dd.created_at','>=',$start_date)
                                ->whereDate('dd.created_at','<=',$end_date); 
                        }
                       
                       $dd = $dd1->where('dd.status','1')->select('dd.*','s.state','l.language','d.district','t.tehsil')
                        ->get();
                       //print_r($dd);die;
                    $ddArray = array();
                    $ddArray1 = array();
                    $aveSumArray =  array();
                    $totalFarmerArray =  array();
                foreach ($dd as $key => $value) 
                {
                   $ddArray[$key]['dd_id'] =  $value->dd_id;
                   $ddArray[$key]['ECN'] =  $value->ECN;
                   $ddArray[$key]['name'] =  $value->name;
                   $ddArray[$key]['dd_code'] =  $value->dd_code;
                   $ddArray[$key]['dd_hq'] =  $value->dd_hq;
                   $ddArray[$key]['dd_mobile_no'] =  $value->dd_mobile_no;
                   $ddArray[$key]['dd_alt_mobile_no'] =  $value->dd_alt_mobile_no;
                   $ddArray[$key]['dd_address'] =  $value->dd_address;
                   if (!empty($value->dd_image)) {
                        $ddArray[$key]['dd_image'] = $base_url.'/assets/ddimage/'.$value->dd_image;
                    }
                    else
                    {
                        $ddArray[$key]['dd_image'] = "";
                    }
                   
                   $ddArray[$key]['dd_email'] =  $value->dd_email;
                   $ddArray[$key]['department'] =  $value->department;
                   $ddArray[$key]['designation'] =  $value->designation;
                   $ddArray[$key]['app_permission_edit_ticket'] =  $value->app_permission_edit_ticket;
                   $ddArray[$key]['app_permission_close_ticket'] =  $value->app_permission_close_ticket;

                  $totalVisitFarmer1 = DB::table('mark_visits')
                                        ->where('dd_id', $value->dd_id);
                  if($month != '')
                  {
                      $totalVisitFarmer1->whereMonth('created_at','=',$month);
                  }
                   if($start_date != '' && $end_date != '')
                  {
                     $totalVisitFarmer1->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date); 
                  }
                  $totalVisitFarmer = $totalVisitFarmer1->count();
                  
                  $totalFarmer1 = DB::table('mark_visits')
                                  ->where('dd_id', $value->dd_id);
                  if($month != '')
                    {
                        $totalFarmer1->whereMonth('created_at','=',$month);
                    }
                    if($start_date != '' && $end_date != '')
                    {
                       $totalFarmer1->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date); 
                    }
                  $totalFarmer = $totalFarmer1->distinct('farmer_id')
                  ->count('farmer_id');
                  // $averageFarmer = (int)$totalVisitFarmer/(int)$totalFarmer;
                  if($totalFarmer > 0)
                  {
                    $aveVal = (int)$totalVisitFarmer/(int)$totalFarmer;
                    $averageFarmer = number_format((float)$aveVal, 2, '.', '');
                    }
                    else
                    {
                    $averageFarmer = 0;  
                    }
                    array_push($aveSumArray, (float)$averageFarmer);

                     $farmerCount1 = DB::table('village_farmers')
                        ->where('dd_id', $value->dd_id)
                        ->where('status', '1');
                        if($month != '')
                    {
                        $farmerCount1->whereMonth('created_at','=',$month);
                    }
                    if($start_date != '' && $end_date != '')
                    {
                       $farmerCount1->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date); 
                    }
                      $farmerCount = $farmerCount1->count();

                    array_push($totalFarmerArray, $farmerCount);


                }
                $sumFarmerCount = array_sum($totalFarmerArray);
                $sumofVist =  array_sum($aveSumArray); 
                $totalCountSumAr =  count($aveSumArray); 
                if($totalCountSumAr > 0)
                {
                 $visitAverage = number_format((float)$sumofVist, 2, '.', '')/$totalCountSumAr;   
                } 
                else{
                  $visitAverage = 0;  
                }             
                
             

                $villageCount1 = DB::table('assigned_village as av')
                        ->leftJoin('villages as v','av.village_id', '=', 'v.village_id');
                        if ($request->has('bdo_id')) 
                        {
                          $bdo_id=$request->input('bdo');
                          $villageCount1->where('av.assigned_by_bdo_id',$bdo_id);
                        }
                        else
                        {
                          if (count($bdoresult) > 0) 
                          {
                            $villageCount1->whereIn('av.assigned_by_bdo_id',$bdoresult);
                          }
                          else
                          {
                            $villageCount1->where('1','!=','1');
                          }
                        }

                        
                        if($month != '')
                        {
                            $villageCount1->whereMonth('av.created_at','=',$month);
                        }
                        if($start_date != '' && $end_date != '')
                        {
                           $villageCount1->whereDate('av.created_at','>=',$start_date)
                                ->whereDate('av.created_at','<=',$end_date); 
                        }
                     $villageCount = $villageCount1->count();
                     //echo "<pre>";print_r($bdoresult);die;
                $upcome1 = DB::table('plan_atp as pa')
                          ->leftJoin('dhanuka_doctor as dd','pa.dd_id', '=', 'dd.dd_id')
                          ->leftJoin('crops as c','pa.crop_id', '=', 'c.crop_id')
                          ->leftJoin('products as p','pa.product_id', '=', 'p.product_id')
                          ->where('pa.user_type', 'bdo');
                          //->where('pa.status','1');
                          if ($request->has('bdo')) 
                          {
                            $bdo_id=$request->input('bdo');
                            $upcome1->where('pa.created_by_user_id',$bdo_id);
                          }
                          else
                          {
                            if (count($bdoresult) > 0) 
                            {
                              $upcome1->whereIn('pa.created_by_user_id',$bdoresult);
                            }
                            else
                            {
                              $upcome1->where('1','!=','1');
                            }
                          }

                          if($month != '')
                          {
                            //$upcome1->whereMonth('pa.created_at','=',$month);
                            $upcome1->where(DB::raw("(DATE_FORMAT(pa.plan_date,'%m'))"),'=', $month);
                          }
                          if($start_date != '' && $end_date != '')
                          { 
                            $upcome1->whereBetween(DB::raw('DATE(pa.plan_date)'),[$start_date." 00:00:00", $end_date." 23:59:59"]);
                            //$upcome1->whereDate('pa.created_at','>=',$start_date)->whereDate('pa.created_at','<=',$end_date); 
                          }
                          //DB::raw("(DATE_FORMAT(created_at,'%Y-%m'))"),"2016-07"
                          //$upcome = $upcome1->whereDate('pa.plan_date','>', $date)
                          $upcome = $upcome1->where(DB::raw("(DATE_FORMAT(pa.plan_date,'%Y-%m-%d'))"),'>', $date)->get(); 
                  //print_r($upcome);die;
                  foreach ($upcome as $keyUp => $valueUp) {
                     $ddArray1[$keyUp]['plan_atp_id'] =  $valueUp->plan_atp_id;
                     $ddArray1[$keyUp]['plan_date'] =  $valueUp->plan_date;
                     $ddArray1[$keyUp]['crop'] =  $valueUp->crop;
                     $ddArray1[$keyUp]['product'] =  $valueUp->product;
                     $ddArray1[$keyUp]['name'] =  $valueUp->name;
                     $ddArray1[$keyUp]['dd_id'] =  $valueUp->dd_id;
                     $ddArray1[$keyUp]['crop_id'] =  $valueUp->crop_id;
                     $ddArray1[$keyUp]['plan_atp_id'] =  $valueUp->plan_atp_id;
                     $ddArray1[$keyUp]['product_id'] =  $valueUp->product_id;
                     $ddArray1[$keyUp]['api_token'] =  $valueUp->api_token;
                   
                   if (!empty($valueUp->image)) {
                        $ddArray1[$keyUp]['image'] = $base_url.'/assets/atpimage/'.$valueUp->image;
                    }
                    else
                    {
                        $ddArray1[$keyUp]['image'] = "";
                    }
                }
                
                $teamSummary['ddCount'] = count($ddArray);
                $teamSummary['villageCount'] = $villageCount;
                $teamSummary['farmerCount'] = $sumFarmerCount;
                $teamSummary['averageVisit'] = round((float)$visitAverage);
                $teamSummary['upcomingATP'] = $ddArray1;
                $teamSummary['ddDetail'] = $ddArray;
                
                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'DD List',
                            'data' => $teamSummary
                ]);
              
              } 
           }
            else 
            {
              return $this->respondWithError("Invalid Attempt !!");
            }
          }
        } catch (Exception $e) {
          return $this->respondInternalError("An error occurred while performing an action!");
        }
    }


    //DD profile summary
    public function ddBDOprofilesummary(Request $request) {
        $data = $request->json()->all();
        //$base_url = url('/');
        $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
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
                    $getExistdd =  DhanukaDoctor::where('ECN',$ecn)->get()->toArray();
                    $dd_id = $getExistdd[0]['dd_id'];
                    //$ddData = DhanukaDoctor::where('dd_id', $user_id)->first();
                    $ddData = DB::table('dhanuka_doctor')
                                ->select('dd_id','name','dd_code','dd_image','dd_email','dd_mobile_no','dd_alt_mobile_no','dd_address')
                                ->where('status', '1')
                                ->where('dd_id', $dd_id)
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
                                       ->where('dd_farmer_visits.dd_id',$dd_id);
                        
                      }else{
                          $ddamountsData = DB::table('dd_farmer_visits')
                                          ->where('dd_id', $dd_id);
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
                                     ->where('dd_farmer_visits.dd_id', $dd_id);
                      
                    }else{
                      $ddfarmervisitsData = DB::table('dd_farmer_visits')
                              ->select('dd_farmer_visits_id','creation_time','amount','plan_atp_id')
                              ->where('dd_id', $dd_id);
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
                                      ->where('assigned_to_dd_id', $dd_id)
                                      ->count('assigned_village_id');

                    //total villages and total farmer
                    $ddvlfrData = "select v.village_id, v.village, count(vf.farmer_id) as totalfarmer, DATE_FORMAT(av.created_at, '%Y-%m-%d') as created_at  FROM assigned_village as av LEFT JOIN villages v ON v.village_id=av.village_id LEFT JOIN village_farmers vf ON vf.village_id=av.village_id WHERE av.assigned_to_dd_id = '".$dd_id."' GROUP BY av.village_id";
                    $ddvillagefarmerData    = collect(DB::select($ddvlfrData));

                    //total acres of dd
                    $ddacfrData = "select v.village, 190 as villageacres  FROM assigned_village as av LEFT JOIN villages v ON v.village_id=av.village_id LEFT JOIN village_farmers vf ON vf.village_id=av.village_id WHERE av.assigned_to_dd_id = '".$dd_id."' GROUP BY av.village_id";
                    $ddacData    = collect(DB::select($ddacfrData));
                    $total_acres = 0;
                    foreach ($ddacData as $key => $value) {
                       $total_acres = $total_acres + $ddacData[$key]->villageacres;
                    }

                    $getHistory = DB::table('dd_history')->where('dd_id',$dd_id)->get();
                   
                    $ddData[0]->average_farmer = "0";
                    $ddData[0]->total_earning = (!empty($ddamountsData))?$ddamountsData:"0";
                    $ddData[0]->my_earning = (!empty($ddfarmervisitsData))?$ddfarmervisitsData:[];
                    $ddData[0]->total_villages = (!empty($ddvillagesData))?$ddvillagesData:"0";
                    $ddData[0]->my_villages = (!empty($ddvillagefarmerData))?$ddvillagefarmerData:[];
                    $ddData[0]->total_acres = $total_acres;
                    $ddData[0]->my_acres = (!empty($ddacData))?$ddacData:[];
                    $ddData[0]->dd_history = (!empty($getHistory))?$getHistory:[];
                    
                    $dd_season = DB::table('dd_season')->leftjoin('seasons','seasons.id','=','dd_season.season_id')->where('dd_season.dd_id',$dd_id)->select('seasons.*')->get();
                    $ddData[0]->dd_season = (!empty($dd_season[0]->season_name))?$dd_season[0]->season_name:"";
                    
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


    public function allStateList(Request $request)
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
                $ddstate =  DB::table('states')->where('status','1')
                                    ->select('state_id','state')
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

    //stateDistrictList

    public function stateDistrictList(Request $request) {

            $rules = array(
                'apiToken' => 'required|string',
                'state_id' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
                $user = $this->findUser($api_token);
                $state_id= $request->input('state_id');
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;

                    $user_type = $user[0]->user_type;
                    $ddData = DB::table('districts')
                                ->select('district_id','district')
                                ->where('status', '1')
                                ->where('state_id', $state_id)
                                ->get();
                             
                    
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'All District List',
                                'data' => $ddData
                    ]);
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
    }


    public function districtTehshilList(Request $request) {

            $rules = array(
                'apiToken' => 'required|string',
                'district_id' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
                $user = $this->findUser($api_token);
                $district_id= $request->input('district_id');
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;

                    $user_type = $user[0]->user_type;
                    $ddData = DB::table('tehsils')
                                ->select('tehsil_id','tehsil')
                                ->where('status', '1')
                                ->where('district_id', $district_id)
                                ->get();
                             
                    
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'All Tehsils List',
                                'data' => $ddData
                    ]);
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
    }

    public function tehshilVillageList(Request $request) {

            $rules = array(
                'apiToken' => 'required|string',
                'tehsil_id' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
            } else {
                $api_token = $request->input('apiToken');
                $user = $this->findUser($api_token);
                $tehsil_id= $request->input('tehsil_id');
                if ($user && count($user) == '1') {
                    $user_id = $user[0]->id;

                    $user_type = $user[0]->user_type;
                    $ddData = DB::table('villages')
                                ->select('village_id','village')
                                ->where('status', '1')
                                ->where('tehsil_id', $tehsil_id)
                                ->get();
                             
                    
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'All Villages List',
                                'data' => $ddData
                    ]);
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
    }

    public function newAllStateList(Request $request)
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
                $states =  DB::table('states')->where('status','1')
                                    ->select('*')
                                    ->get();
                if (count($states)>0) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'State List Found!',
                                'data' => $states
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

    public function newAllDistrictList(Request $request) {

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
                    $Data = DB::table('districts')
                                ->select('*')
                                ->where('status', '1')
                                ->get();
                    if (count($Data)>0) {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'All District List',
                                    'data' => $Data
                        ]);
                    }
                    else
                    {
                        return $this->respond([
                                    'status' => 'success',
                                    'status_code' => $this->getStatusCode(),
                                    'message' => 'No district found',
                                    'data' => []
                        ]);
                    }

                    
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
    }


    public function newAllTehshilList(Request $request) {

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
                    $Data = DB::table('tehsils')
                            ->select('*')
                            ->where('status', '1')
                            ->get();
                  if (count($Data)>0) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'All Tehsils List',
                                'data' => $Data
                    ]);
                  }
                  else
                  {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No tehsil found',
                                'data' => []
                    ]);
                  }         
                    
                    
                } else {
                    return $this->respondNotFound("Invalid Attempt !!");
                }
            }
    }

    public function newAllVillageList(Request $request) {
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
              $Data = DB::table('villages')
                          ->select('*')
                          ->where('status', '1')
                          ->get();
            if (count($Data)>0) {
              return $this->respond([
                          'status' => 'success',
                          'status_code' => $this->getStatusCode(),
                          'message' => 'All Villages List',
                          'data' => $Data
              ]);
            }           
            else{
              return $this->respond([
                          'status' => 'success',
                          'status_code' => $this->getStatusCode(),
                          'message' => 'No village found',
                          'data' => []
              ]);
            }  
              
          } else {
              return $this->respondNotFound("Invalid Attempt !!");
          }
      }
    }


    public function allSeasonList(Request $request)
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
                $ddstate =  DB::table('seasons')->where('status','1')
                                    ->select('id','season_name')
                                    ->get();
                if ($ddstate) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Seasons List Found!',
                                'data' => $ddstate
                    ]);
                }
                else
                {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'No Seasons found',
                                'data' => []
                    ]);
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function addDdSeason(Request $request)
    {
        $rules = array(
            'apiToken' => 'required|string',
            'dd_id' => 'required',
            'season_id' => 'required'
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
                $dd_id = $request->input('dd_id');
                $season_id = $request->input('season_id');
                $ddseason = DB::table('dd_season')
                            ->where('dd_id',$dd_id)
                            ->select('dd_season.*')
                            ->count();
                if ($ddseason > 0) {
                  $updateDDseason = DB::table('dd_season')->where('dd_id', $dd_id)->update(['season_id' => $season_id, 'updated_at' => date('Y-m-d H:i:s')]);
                  if ($updateDDseason) {
                      return $this->respond([
                                  'status' => 'success',
                                  'status_code' => $this->getStatusCode(),
                                  'message' => 'Season updated successfully.'
                      ]);
                  }
                  else
                  {
                      return $this->respondNotFound("Please try again !!");
                  }
                }
                else
                {
                  $insertDDseason = DB::table('dd_season')->insert(
                                ['dd_id' => $dd_id, 'season_id' => $season_id, 'created_at' => date('Y-m-d H:i:s'), 'user_id' => $user_id, 'user_type' => $user_type]
                            );
                  if ($insertDDseason) {
                      return $this->respond([
                                  'status' => 'success',
                                  'status_code' => $this->getStatusCode(),
                                  'message' => 'Season added successfully.'
                      ]);
                  }
                  else
                  {
                      return $this->respondNotFound("Please try again !!");
                  }
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }

    public function assignDdvillage(Request $request)
    {

        $rules = array(
            'apiToken' => 'required|string',
            'dd_id' => 'required',
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
                $dd_id = $request->input('dd_id');
                $village_id = $request->input('village_id');
                $insertDDvillage = DB::table('assigned_village')->insert(
                                ['assigned_to_dd_id' => $dd_id, 'village_id' => $village_id, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'assigned_by_bdo_id' => $user_id]
                            );
                if ($insertDDvillage) {
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Village added successfully.'
                    ]);
                }
                else
                {
                    return $this->respondNotFound("Please try again !!");
                }
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    
    }

     public function upcomingFilter(Request $request)
    {
        $date = date('Y-m-d');
        //$base_url = url('/');
        $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
        $rules = array(
            'apiToken' => 'required|string'
        );
        $ddArray1 = array();

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
            
            $api_token = $request->input('apiToken');
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $upcome1 = DB::table('plan_atp as pa')
                        ->leftJoin('dhanuka_doctor as dd','pa.dd_id', '=', 'dd.dd_id')
                        ->leftJoin('crops as c','pa.crop_id', '=', 'c.crop_id')
                        ->leftJoin('products as p','pa.product_id', '=', 'p.product_id');
                        if($request->has('plan_date') && !empty($request->input('plan_date')))
                        { 
                          $plan_date = $request->input('plan_date');
                          //$upcome1->where(DB::raw("DATE_FORMAT(`pa`.`plan_date`, '%Y-%m-%d')"),'>',DB::raw("DATE_FORMAT('".$plan_date."','%Y-%m-%d')"));
                           $upcome1->where(DB::raw('DATE(pa.plan_date)'),$plan_date);
                          //$upcome1->where(DB::raw("DATE_FORMAT(`pa`.`plan_date`, '%Y-%m-%d')"),'=',$plan_date);
                        }
                        else
                        {
                          $upcome1->where('pa.created_by_user_id', $user_id);
                          $upcome1->where('pa.user_type', 'bdo'); 
                          $upcome1->where(DB::raw('DATE(pa.plan_date)'),'>',$date);
                          //$upcome1->where(DB::raw("DATE_FORMAT(`pa`.`plan_date`,'%Y-%m-%d')"),'>', DB::raw("DATE_FORMAT('".$date."','%Y-%m-%d')"));
                        }
                        // else
                        // {
                        //   $upcome1->where(DB::raw("DATE_FORMAT(`pa`.`plan_date`,'%Y-%m-%d')"),'>', DB::raw("DATE_FORMAT('".$date."','%Y-%m-%d')"));
                        // }
                        if($request->has('upcoming_dds') && !empty($request->input('upcoming_dds')))
                        { 
                          $upcoming_dds = $request->input('upcoming_dds');
                          $upcommingArr = explode(',', $upcoming_dds); 
                          $upcome1->whereIn('pa.dd_id',$upcommingArr); 
                          //$upcome1->where(DB::raw("DATE_FORMAT(`pa`.`plan_date`,'%Y-%m-%d')"),'>', DB::raw("DATE_FORMAT('".$date."','%Y-%m-%d')")); 
                        }
                        
                        
                        $upcome=$upcome1->get();
                       // echo "<pre>";print_r($upcome);die;
                        
                foreach ($upcome as $keyUp => $valueUp) {
                   $ddArray1[$keyUp]['plan_atp_id'] =  $valueUp->plan_atp_id;
                   $ddArray1[$keyUp]['plan_date'] =  $valueUp->plan_date;
                   $ddArray1[$keyUp]['crop'] =  $valueUp->crop;
                   $ddArray1[$keyUp]['product'] =  $valueUp->product;
                   $ddArray1[$keyUp]['name'] =  $valueUp->name;
                   $ddArray1[$keyUp]['dd_id'] =  $valueUp->dd_id;
                   $ddArray1[$keyUp]['crop_id'] =  $valueUp->crop_id;
                   $ddArray1[$keyUp]['plan_atp_id'] =  $valueUp->plan_atp_id;
                   $ddArray1[$keyUp]['product_id'] =  $valueUp->product_id;
                   $ddArray1[$keyUp]['api_token'] =  $valueUp->api_token;
           
                   if (!empty($valueUp->image)) {
                        $ddArray1[$keyUp]['image'] = $base_url.'/assets/atpimage/'.$valueUp->image;
                    }
                    else
                    {
                        $ddArray1[$keyUp]['image'] = "";
                    }
                }
               
                $data = array();
                $data['upcomingATP'] = $ddArray1;
                    return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Upcoming ATPs!',
                                'data' => $data
                    ]);
               
            } else {
                return $this->respondNotFound("Invalid Attempt !!");
            }
        }
    }


    public function bdoreport($api_token = '') {
        try {
            $user = User::where('api_token', $api_token)->first();
            if ($user) {
                $user_id = $user->id;
                $ticket_count = Ticket::where('user_id', $user_id)->count();
             $farmer_count = Farmer::where('user_id', $user_id)->where('ticket_status', '1')->count();
             $farmer_valid_count = Farmer::where('user_id', $user_id)->where('ticket_status', '1')->where('validation_status','Validated')->count(); 

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

    

     public function updateDeviceInfo(Request $request) {
      $date = date('Y-m-d H:i:s');
        $data = $request->all();
        $rules = array(
            'api_token' => 'required|string',
            'device_type' => 'required',
            'device_id' => 'required',
            'lat_location' => 'required',
            'long_location' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
             $base_url = url('/');
             $api_token = $request->input('api_token');
              $user = $this->findUser($api_token);
            if ($user) {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $exist = DB::table('device_info')->where(['user_id' => $user_id, 'user_type' => $user_type])->count();
                $data1 = array('user_id' => $user_id, 'user_type' => $user_type, 'device_type' => $request->input('device_type'), 'device_id' => $request->input('device_id'), 'lat_location' => $request->input('lat_location'), 'long_location' => $request->input('long_location'), 'updated_at' => $date);
                // $data1['user_id'] = $user_id;
                // $data1['user_type'] = $user_type;
                // $data1['device_type'] = $request->input('device_type');
                // $data1['device_id'] = $request->input('device_id');
                // $data1['lat_location'] = $request->input('lat_location');
                // $data1['long_location'] = $request->input('long_location');
                // $data1['updated_at'] = $date;
                if($exist > 0)
                {
                  DB::table('device_info')->where(['user_id' => $user_id, 'user_type' => $user_type])->update($data1);
                  return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Device updated Successfully',
                    ]);
                }
                else
                {
                  DB::table('device_info')->insert($data1);
                  return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'Device updated Successfully',
                    ]);
                }

             } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }

            
        }
    }


    public function flList(Request $request) {
      $date = date('Y-m-d H:i:s');
        $data = $request->all();
        $rules = array(
            'api_token' => 'required|string'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->respondValidationError('Main Fields Validation Failed.', $validator->errors());
        } else {
             $base_url = url('/');
             $api_token = $request->input('api_token');
              $user = $this->findUser($api_token);
            if ($user) {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $exist = DB::table('users')->where('id', $user_id)->where('status','1')->get();
                if(count($exist) > 0)
                {
                  $counter = $exist[0]->counterpart_fl;
                  $data1 = DB::table('users')->where('id', $counter)->where('status','1')->get();
                  $flArray = array();
                  foreach ($data1 as $key => $value) {
                   $flArray[$key]['id'] = $value->id;
                   $flArray[$key]['ECN'] = $value->ECN;
                   $flArray[$key]['name'] = $value->name;
                   $flArray[$key]['designation'] = $value->designation;
                   $flArray[$key]['department'] = $value->department;
                   $flArray[$key]['bdo_mobile_no'] = $value->bdo_mobile_no;
                  }
                  return $this->respond([
                                'status' => 'success',
                                'status_code' => $this->getStatusCode(),
                                'message' => 'FL list',
                                'data' => $flArray
                    ]);
                }
                else
                {
                  return $this->respondWithConflict("No exist!");

                }
                

               
                 

             } else {
                return $this->respondWithConflict("Invalid Attempt!");
            }

            
        }
    }



    public function BDOddList($api_token = '') {
        try {
            $user = $this->findUser($api_token);
            if ($user && count($user) == '1') {
                $user_id = $user[0]->id;
                $user_type = $user[0]->user_type;
                $base_url = url('/');
                
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
                }
                else
                {
                  $bdo_ids = array($user_id);
                }

                $dd = DB::table('dhanuka_doctor as dd')
                        ->leftJoin('states as s','dd.state_id', '=', 's.state_id')
                        ->leftJoin('districts as d','dd.district_id', '=','d.district_id')
                        ->leftJoin('tehsils as t','dd.tehsil_id', '=','t.tehsil_id')
                        ->leftJoin('languages as l','dd.language_id', '=','l.id')
                        ->whereIn('dd.bdo_id',$bdo_ids)
                        ->where('dd.status','1')
                        ->select('dd.*','s.state','l.language','d.district','t.tehsil')
                        ->get();
                    $ddArray = array();
                foreach ($dd as $key => $value) {
                   $ddArray[$key]['dd_id'] =  $value->dd_id;
                   $ddArray[$key]['ECN'] =  $value->ECN;
                   $ddArray[$key]['name'] =  $value->name;
                }


                return $this->respond([
                            'status' => 'success',
                            'status_code' => $this->getStatusCode(),
                            'message' => 'DD List',
                            'data' => $ddArray
                ]);
              } else {
                        return $this->respondWithError("Invalid Attempt !!");
                    }
         
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

     




}