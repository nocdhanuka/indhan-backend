<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\permission;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use Khill\Lavacharts\Lavacharts;
use DateTime;


class CallController extends Controller
{
    public function __construct() {

        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    public function getCallList()
    {
        return view('backend.calls.calllist');       
    }

    public function postCallList(Request $request)
    { 
      $value = Input::all(); //echo "<pre>";print_r($value);die;
      $start_date     = $value['start_date'];
      $end_date       = $value['end_date'];
      $datesearch     = '';

      if($start_date!='' && $end_date!='') {
          $datesearch = "AND DATE(f.updated_at) >= DATE('".$start_date." 00:00:00') AND DATE(f.updated_at) <= DATE('".$end_date." 23:59:59')";
      }
      else {
          $datesearch = "";
      }
      $callList="SELECT f.farmer_id,
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
                CONCAT_WS(
                            '\n', 
                            NULLIF(zm.zonal_manager_name, ''),
                            NULLIF(rm.regional_manager_name, ''),
                            NULLIF(am.area_manager_name, ''),
                            NULLIF(srm.sales_regional_manager_name, ''),
                            NULLIF(sam.sales_area_manager_name, ''),
                            NULLIF(u.name, ''),
                            NULLIF(dd.name, '')
                        ) AS username,
                t.ticket_no,
                f.farmer_name,
                f.mobile,
                f.updated_at
                FROM  `farmers` as f 
                LEFT JOIN   users  u   ON f.user_id=u.id AND f.user_type='bdo'
                LEFT JOIN   zonal_managers  zm  ON f.user_id=zm.zonal_manager_id AND f.user_type='zonal'
                LEFT JOIN   regional_managers rm  ON f.user_id=rm.regional_manager_id AND f.user_type='regional'
                LEFT JOIN   area_managers   am  ON f.user_id=am.area_manager_id AND f.user_type='area'
                LEFT JOIN   sales_regional_managers srm ON f.user_id=srm.sales_regional_manager_id AND f.user_type='sales_regional'
                LEFT JOIN   sales_area_managers sam ON f.user_id=sam.sales_area_manager_id AND f.user_type='sales_area'
                LEFT JOIN   dhanuka_doctor dd ON f.user_id=dd.dd_id AND f.user_type='dhanuka_doctor'
                LEFT JOIN   tickets t ON f.ticket_id=t.ticket_id 
                WHERE  `call_status` =  '1' ".$datesearch." order by `f`.`farmer_id` DESC "; 
            $callList  = collect(DB::select($callList));
            if (count($callList) > 0) 
            {
              foreach ($callList as $key => $value) {
                $query1="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date 
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList[$key]->farmer_id."' AND `l`.`farmer_type` = 'register_farmer'";
                $getlead  = collect(DB::select($query1));
                if (count($getlead)>0) {
                  $callList[$key]->lead_code = $getlead[0]->lead_code;
                  if ($getlead[0]->lead_status == '1') {
                    $callList[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead[0]->lead_status == '2') {
                    $callList[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead[0]->lead_status == '3') {
                    $callList[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList[$key]->lead_status = "";
                  }
                  $callList[$key]->distributor_name = $getlead[0]->distributor_name;
                  $callList[$key]->product = $getlead[0]->product;
                  $callList[$key]->open_date = $getlead[0]->open_date;
                  $callList[$key]->closed_date = $getlead[0]->closed_date;
                  
                  if ($getlead[0]->closed_date == '0000-00-00 00:00:00' || $getlead[0]->closed_date == '') {
                    $callList[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  { 
                    $datetime1 = new DateTime($getlead[0]->open_date);
                    $datetime2 = new DateTime($getlead[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList[$key]->diffrence_open_closed_date = $elapsed;
                    
                  }
                  
                }
                else
                {
                  $callList[$key]->lead_code = "";
                  $callList[$key]->lead_status = "";
                  $callList[$key]->distributor_name = "";
                  $callList[$key]->product = "";
                  $callList[$key]->open_date = "";
                  $callList[$key]->closed_date = "";
                  $callList[$key]->diffrence_open_closed_date = "";
                }
                
              }
              
              foreach ($callList as $key => $value) {
                $getfarmercrop = DB::table('leads as l')
                                 ->join('farmers as f','f.farmer_id','=','l.farmer_id')
                                 ->join('tickets as t','t.ticket_id','=','f.ticket_id','left')
                                 ->join('crops as c','c.crop_id','=','t.crop_1_id','left')
                                 ->where('l.lead_code',$callList[$key]->lead_code)
                                 ->where('l.farmer_type','register_farmer')
                                 ->select('c.crop')
                                 ->groupBy('c.crop_id')
                                 ->get(); 
                if (count($getfarmercrop)>0) {
                    $callList[$key]->crop = $getfarmercrop[0]->crop;
                }else{
                    $callList[$key]->crop = "";
                }

                $callList[$key]->leadSource = "Field day";
              }
              //echo "<pre>";print_r($callList);die;
            }
            
            $callList12="SELECT f.farmer_id,
            dd.ECN,
            dd.name as username,
            t.ticket_no,
            f.farmer_name,
            f.contact_number,
            f.updated_at
            FROM `village_farmers` as f 
            LEFT JOIN   dhanuka_doctor dd ON f.dd_id=dd.dd_id
            LEFT JOIN   tickets t ON f.created_at=t.ticket_id
            WHERE  `call_status` =  '1' ".$datesearch." order by `f`.`farmer_id` DESC "; 
            $callList12  = collect(DB::select($callList12));
            if (count($callList12) > 0) 
            {
              foreach ($callList12 as $key => $value) {
                $query2="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date   
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList12[$key]->farmer_id."' AND `l`.`farmer_type` = 'normal_farmer'";
                $getlead1  = collect(DB::select($query2));
                if (count($getlead1)>0) {
                  $callList12[$key]->lead_code = $getlead1[0]->lead_code;
                  if ($getlead1[0]->lead_status == '1') {
                    $callList12[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead1[0]->lead_status == '2') {
                    $callList12[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead1[0]->lead_status == '3') {
                    $callList12[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList12[$key]->lead_status = "";
                  }

                  $callList12[$key]->distributor_name = $getlead1[0]->distributor_name;
                  $callList12[$key]->product = $getlead1[0]->product;
                  $callList12[$key]->open_date = $getlead1[0]->open_date;
                  $callList12[$key]->closed_date = $getlead1[0]->closed_date;
                  if ($getlead1[0]->closed_date == '0000-00-00 00:00:00' || $getlead1[0]->closed_date == '') {
                    $callList12[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  {
                    $datetime1 = new DateTime($getlead1[0]->open_date);
                    $datetime2 = new DateTime($getlead1[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList12[$key]->diffrence_open_closed_date = $elapsed;
                  }
                }
                else
                {
                  $callList12[$key]->lead_code = "";
                  $callList12[$key]->lead_status = "";
                  $callList12[$key]->distributor_name = "";
                  $callList12[$key]->product = "";
                  $callList12[$key]->open_date = "";
                  $callList12[$key]->closed_date = "";
                  $callList12[$key]->diffrence_open_closed_date = "";
                }
              }

              foreach ($callList12 as $key => $value) {
                $getacpcrop = DB::table('village_farmer_crop as vfc')
                              ->join('crops as c','c.crop_id','=','vfc.crop_id')
                              ->where('vfc.farmer_id',$callList12[$key]->farmer_id)
                              ->select('c.crop')
                              ->get();
                if (count($getacpcrop)>0) {
                    $callList12[$key]->crop = $getacpcrop[0]->crop;
                }
                else
                {
                    $callList12[$key]->crop = "";
                }

                $callList12[$key]->ticket_no = "";
                $callList12[$key]->leadSource = "ACP";
              }
            }

             $merged1 = $callList->merge($callList12);
            //$merged =$merged1->toArray(); 
            //echo "<pre>";print_r($merged1);die;
            return Datatables::of($merged1)->make(); 
      		  //return view('backend.calls.calllist',compact('merged'));       
	  }
 // public function postCallList(){
 //        $callList="SELECT farmer_id,farmer_name,mobile,updated_at FROM  `farmers` WHERE  `call_status` =  '1'"; 
 //          $callList  = collect(DB::select($callList));

 //          $callList12="SELECT farmer_id,farmer_name,contact_number,updated_at FROM `village_farmers` WHERE  `call_status` =  '1'"; 

 //          $callList12  = collect(DB::select($callList12));
 //          $merged = $callList->merge($callList12);
 //          echo "<pre>"; print_r($merged); die;
 //          // return $merged;
 //   return view('backend.calls.calllist', compact('merged'));         
 //         // return Datatables::of($merged)->make(); 
 //    }

    public function getCallFilter()
    {     
          $sess = \Session::get('adminid');
          $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first(); 
          if (count($userData1)>0) {
              $rmsList = "SELECT sales_regional_manager_id,sales_regional_manager_name FROM sales_regional_managers WHERE  `status` =  '1' and `zonal_manager_id` = '".$userData1->zonal_manager_id."'"; 
              $rmsList  = collect(DB::select($rmsList));
               $cropList="SELECT crop_id,crop FROM  `crops` WHERE  `status` =  '1'"; 
              $cropList  = collect(DB::select($cropList));
              $data['rmsList'] = $rmsList;
              $data['cropList'] = $cropList;
          }
          else
          {
              $rmsList = "SELECT sales_regional_manager_id,sales_regional_manager_name FROM sales_regional_managers WHERE  `status` =  '1'"; 
              $rmsList  = collect(DB::select($rmsList));
               $cropList="SELECT crop_id,crop FROM  `crops` WHERE  `status` =  '1'"; 
              $cropList  = collect(DB::select($cropList));
              $data['rmsList'] = $rmsList;
              $data['cropList'] = $cropList;
          }
          
            
           echo json_encode($data);
        
    }

    public function postCallFilterAjax(Request $request)
    {
      //echo "<pre>"; print_r($request->all()); die;
      $value = $request->all();
      if($value['type'] == 'ASM')
      {
         $rmsList = "SELECT sales_area_manager_id,sales_area_manager_name FROM sales_area_managers WHERE  status =  '1' AND sales_regional_manager_id = '".$value['option']."'"; 
          $rmsList  = collect(DB::select($rmsList));
            echo json_encode($rmsList);die;
      }
      if($value['type'] == 'FL')
      {
         $flList="SELECT id,name FROM  `users` WHERE  `status` =  '1' AND sales_area_manager_id = '".$value['option']."'"; 
          $flList  = collect(DB::select($flList));
            echo json_encode($flList);
      }
      if($value['type'] == 'DD')
      {
         $ddList="SELECT dd_id,name FROM  `dhanuka_doctor` WHERE  `status` =  '1' AND bdo_id = '".$value['option']."'"; 
          $ddList  = collect(DB::select($ddList));
            echo json_encode($ddList);
      }
      if($value['type'] == 'crop')
      {
         $proList="SELECT p.product_id,p.product FROM  `products` as p LEFT JOIN product_crop_relations as pc ON p.product_id = pc.product_id  WHERE  p.status =  '1' AND pc.crop_id = '".$value['option']."' group by pc.product_id"; 
          $proList  = collect(DB::select($proList));
            echo json_encode($proList);
      }
        
    }

     public function postCallListFilter(Request $request)
    {
       
  if(empty($request->filter_crop) && empty($request->filter_DD) && empty($request->filter_FL) && empty($request->filter_ASM) && empty($request->filter_RSM) && empty($request->filter_product) && empty($request->filter_start) && empty($request->filter_end))
     {
      $callList="SELECT f.farmer_id,
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
      CONCAT_WS(
                  '\n', 
                  NULLIF(zm.zonal_manager_name, ''),
                  NULLIF(rm.regional_manager_name, ''),
                  NULLIF(am.area_manager_name, ''),
                  NULLIF(srm.sales_regional_manager_name, ''),
                  NULLIF(sam.sales_area_manager_name, ''),
                  NULLIF(u.name, ''),
                  NULLIF(dd.name, '')
              ) AS username,
              t.ticket_no,
              f.farmer_name,
              f.mobile,
              f.updated_at 
              FROM  `farmers` as f 
              LEFT JOIN   users  u   ON f.user_id=u.id AND f.user_type='bdo'
              LEFT JOIN   zonal_managers  zm  ON f.user_id=zm.zonal_manager_id AND f.user_type='zonal'
              LEFT JOIN   regional_managers rm  ON f.user_id=rm.regional_manager_id AND f.user_type='regional'
              LEFT JOIN   area_managers   am  ON f.user_id=am.area_manager_id AND f.user_type='area'
              LEFT JOIN   sales_regional_managers srm ON f.user_id=srm.sales_regional_manager_id AND f.user_type='sales_regional'
              LEFT JOIN   sales_area_managers sam ON f.user_id=sam.sales_area_manager_id AND f.user_type='sales_area'
              LEFT JOIN   dhanuka_doctor dd ON f.user_id=dd.dd_id AND f.user_type='dhanuka_doctor'
              LEFT JOIN   tickets t ON f.ticket_id=t.ticket_id 
               WHERE  `call_status` =  '1'"; 
          $callList  = collect(DB::select($callList));
          if (count($callList) > 0) 
          { 
            foreach ($callList as $key => $value) {
                $query1="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList[$key]->farmer_id."' AND `l`.`farmer_type` = 'register_farmer'";
                $getlead  = collect(DB::select($query1));
                if (count($getlead)>0) {
                  $callList[$key]->lead_code = $getlead[0]->lead_code;
                  if ($getlead[0]->lead_status == '1') {
                    $callList[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead[0]->lead_status == '2') {
                    $callList[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead[0]->lead_status == '3') {
                    $callList[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList[$key]->lead_status = "";
                  }
                  $callList[$key]->distributor_name = $getlead[0]->distributor_name;
                  $callList[$key]->product = $getlead[0]->product;
                  $callList[$key]->open_date = $getlead[0]->open_date;
                  $callList[$key]->closed_date = $getlead[0]->closed_date;
                  if ($getlead[0]->closed_date == '0000-00-00 00:00:00' || $getlead[0]->closed_date == '') {
                    $calllist[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  {
                    
                    $datetime1 = new DateTime($getlead[0]->open_date);
                    $datetime2 = new DateTime($getlead[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList[$key]->diffrence_open_closed_date = $elapsed;
                  }
                }
                else
                {
                  $callList[$key]->lead_code = "";
                  $callList[$key]->lead_status = "";
                  $callList[$key]->distributor_name = "";
                  $callList[$key]->product = "";
                  $callList[$key]->open_date = "";
                  $callList[$key]->closed_date = "";
                  $callList[$key]->diffrence_open_closed_date = "";
                }
                
              }

            foreach ($callList as $key => $value) {
              $getfarmercrop = DB::table('leads as l')
                                 ->join('farmers as f','f.farmer_id','=','l.farmer_id')
                                 ->join('tickets as t','t.ticket_id','=','f.ticket_id','left')
                                 ->join('crops as c','c.crop_id','=','t.crop_1_id','left')
                                 ->where('l.lead_code',$callList[$key]->lead_code)
                                 ->where('l.farmer_type','register_farmer')
                                 ->select('c.crop')
                                 ->groupBy('c.crop_id')
                                 ->get(); 
                if (count($getfarmercrop)>0) {
                    $callList[$key]->crop = $getfarmercrop[0]->crop;
                }else{
                    $callList[$key]->crop = "";
                }

              $callList[$key]->leadSource = "Field day";
            }
          }

          $callList12="SELECT f.farmer_id,
          dd.ECN,
          dd.name as username,
          t.ticket_no,
          f.farmer_name,
          f.contact_number,
          f.updated_at 
          FROM `village_farmers` as f 
          LEFT JOIN   dhanuka_doctor dd ON f.dd_id=dd.dd_id
          LEFT JOIN   tickets t ON f.created_at=t.ticket_id
          WHERE  `call_status` =  '1'"; 
          $callList12  = collect(DB::select($callList12));
          if (count($callList12) > 0) 
          {
            foreach ($callList12 as $key => $value) {
                $query2="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date  
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList12[$key]->farmer_id."' AND `l`.`farmer_type` = 'normal_farmer'";
                $getlead1  = collect(DB::select($query2));
                if (count($getlead1)>0) {
                  $callList12[$key]->lead_code = $getlead1[0]->lead_code;
                  if ($getlead1[0]->lead_status == '1') {
                    $callList12[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead1[0]->lead_status == '2') {
                    $callList12[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead1[0]->lead_status == '3') {
                    $callList12[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList12[$key]->lead_status = "";
                  }

                  $callList12[$key]->distributor_name = $getlead1[0]->distributor_name;
                  $callList12[$key]->product = $getlead1[0]->product;
                  $callList12[$key]->open_date = $getlead1[0]->open_date;
                  $callList12[$key]->closed_date = $getlead1[0]->closed_date;
                  if ($getlead1[0]->closed_date == '0000-00-00 00:00:00' || $getlead1[0]->closed_date == '') {
                    $calllist12[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  {
                    $datetime1 = new DateTime($getlead1[0]->open_date);
                    $datetime2 = new DateTime($getlead1[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList12[$key]->diffrence_open_closed_date = $elapsed;
                  }
                }
                else
                {
                  $callList12[$key]->lead_code = "";
                  $callList12[$key]->lead_status = "";
                  $callList12[$key]->distributor_name = "";
                  $callList12[$key]->product = "";
                  $callList12[$key]->open_date = "";
                  $callList12[$key]->closed_date = "";
                  $callList12[$key]->diffrence_open_closed_date = "";
                }
              }

              foreach ($callList12 as $key => $value) {
                $getacpcrop = DB::table('village_farmer_crop as vfc')
                              ->join('crops as c','c.crop_id','=','vfc.crop_id')
                              ->where('vfc.farmer_id',$callList12[$key]->farmer_id)
                              ->select('c.crop')
                              ->get();
                if (count($getacpcrop)>0) {
                    $callList12[$key]->crop = $getacpcrop[0]->crop;
                }
                else
                {
                    $callList12[$key]->crop = "";
                }

                $callList12[$key]->ticket_no = "";
                $callList12[$key]->leadSource = "ACP";
              }
          }
          $merged = $callList->merge($callList12);

        }
        else
        {
         $farmerArr = array();
        $farmerArray = array();
       if(empty($request->filter_DD) && empty($request->filter_FL) && empty($request->filter_ASM) && !empty($request->filter_RSM))
       {
          
          $List = "SELECT id FROM  `users` WHERE  status =  '1' AND sales_regional_manager_id = $request->filter_RSM"; 
          $List  = collect(DB::select($List));
          foreach ($List as $key => $value) {
           $ListBDO = "SELECT farmer_id FROM  `farmers` WHERE  call_status =  '1' AND user_type = 'bdo' AND user_id = $value->id"; 
            $ListBDO  = collect(DB::select($ListBDO));
            foreach ($ListBDO as $keyBDO => $valueBDO) {
               array_push($farmerArr, $valueBDO->farmer_id);
            }
          } 
          

          $List1 = "SELECT dd_id FROM  `dhanuka_doctor` WHERE  status =  '1' AND sales_regional_manager_id = $request->filter_RSM"; 
          $List1  = collect(DB::select($List1));
          foreach ($List1 as $key1 => $value1) {
           $List1BDO = "SELECT farmer_id FROM  `farmers` WHERE  call_status =  '1' AND user_type = 'dhanuka_doctor' AND user_id = $value1->dd_id"; 
            $List1BDO  = collect(DB::select($List1BDO));
            foreach ($List1BDO as $key1BDO => $value1BDO) {
               array_push($farmerArr, $value1BDO->farmer_id);
            }
             $List2BDO = "SELECT farmer_id FROM  `village_farmers` WHERE  call_status =  '1' AND dd_id = $value1->dd_id"; 
            $List2BDO  = collect(DB::select($List2BDO));
            foreach ($List2BDO as $key2BDO => $value2BDO) {
               array_push($farmerArr, $value2BDO->farmer_id);
            }
          }

           
       }
       elseif(empty($request->filter_DD) && empty($request->filter_FL) && !empty($request->filter_ASM))
       {
          
          $List = "SELECT id FROM  `users` WHERE  status =  '1' AND sales_area_manager_id = $request->filter_ASM"; 
          $List  = collect(DB::select($List));
          foreach ($List as $key => $value) {
           $ListBDO = "SELECT farmer_id FROM  `farmers` WHERE  call_status =  '1' AND user_type = 'bdo' AND user_id = $value->id"; 
            $ListBDO  = collect(DB::select($ListBDO));
            foreach ($ListBDO as $keyBDO => $valueBDO) {
               array_push($farmerArr, $valueBDO->farmer_id);
            }
          } 
          

          $List1 = "SELECT dd_id FROM  `dhanuka_doctor` WHERE  status =  '1' AND sales_area_manager_id = $request->filter_ASM"; 
          $List1  = collect(DB::select($List1));
          foreach ($List1 as $key1 => $value1) {
            // echo "<pre>"; print_r($List1); die;
           $List1BDO = "SELECT farmer_id FROM  `farmers` WHERE  call_status =  '1' AND user_type = 'dhanuka_doctor' AND user_id = $value1->dd_id"; 
            $List1BDO  = collect(DB::select($List1BDO));
            foreach ($List1BDO as $key1BDO => $value1BDO) {
               array_push($farmerArr, $value1BDO->farmer_id);
            }
            $List2BDO = "SELECT farmer_id FROM  `village_farmers` WHERE  call_status =  '1' AND dd_id = $value1->dd_id"; 
            $List2BDO  = collect(DB::select($List2BDO));
            foreach ($List2BDO as $key2BDO => $value2BDO) {
               array_push($farmerArr, $value2BDO->farmer_id);
            }
          }

          
       }
       elseif(empty($request->filter_DD) && !empty($request->filter_FL))
       {
        
           $ListBDO = "SELECT farmer_id FROM  `farmers` WHERE  call_status =  '1' AND user_type = 'bdo' AND user_id = $request->filter_FL"; 
            $ListBDO  = collect(DB::select($ListBDO));
            foreach ($ListBDO as $keyBDO => $valueBDO) {
               array_push($farmerArr, $valueBDO->farmer_id);
            }
       

          $List1 = "SELECT dd_id FROM  `dhanuka_doctor` WHERE  status =  '1' AND bdo_id = $request->filter_FL"; 
          $List1  = collect(DB::select($List1));
          foreach ($List1 as $key1 => $value1) {
            // echo "<pre>"; print_r($List1); die;
           $List1BDO = "SELECT farmer_id FROM  `farmers` WHERE  call_status =  '1' AND user_type = 'dhanuka_doctor' AND user_id = $value1->dd_id"; 
            $List1BDO  = collect(DB::select($List1BDO));
            foreach ($List1BDO as $key1BDO => $value1BDO) {
               array_push($farmerArr, $value1BDO->farmer_id);
            }

            $List2BDO = "SELECT farmer_id FROM  `village_farmers` WHERE  call_status =  '1' AND dd_id = $value1->dd_id"; 
            $List2BDO  = collect(DB::select($List2BDO));
            foreach ($List2BDO as $key2BDO => $value2BDO) {
               array_push($farmerArr, $value2BDO->farmer_id);
            }
          }

          
       }
       elseif(!empty($request->filter_DD))
       {

           $List1BDO = "SELECT farmer_id FROM  `farmers` WHERE  call_status =  '1' AND user_type = 'dhanuka_doctor' AND user_id = $request->filter_DD"; 
            $List1BDO  = collect(DB::select($List1BDO));
            foreach ($List1BDO as $key1BDO => $value1BDO) {
               array_push($farmerArr, $value1BDO->farmer_id);
            }
            $List2BDO = "SELECT farmer_id FROM  `village_farmers` WHERE  call_status =  '1' AND dd_id = $request->filter_DD"; 
            $List2BDO  = collect(DB::select($List2BDO));
            foreach ($List2BDO as $key2BDO => $value2BDO) {
               array_push($farmerArr, $value2BDO->farmer_id);
            }
          

       }

        if(empty($request->filter_product) && !empty($request->filter_crop))
        {
          $List2crop = "SELECT vfc.farmer_id FROM  `village_farmer_crop` as vfc LEFT JOIN farmers as f ON vfc.farmer_id = f.farmer_id LEFT JOIN village_farmers as vf ON vfc.farmer_id = vf.farmer_id WHERE  vfc.crop_id =  $request->filter_crop AND(f.call_status = '1' OR vf.call_status = '1')";
            $List2crop  = collect(DB::select($List2crop));
           
          foreach ($List2crop as $keycrop => $valuecrop) {
            array_push($farmerArray, $valuecrop->farmer_id);
          }
        }
        elseif(!empty($request->filter_product))
        {
          $List2crop = "SELECT vfc.farmer_id FROM  `village_farmer_crop` as vfc LEFT JOIN farmers as f ON vfc.farmer_id = f.farmer_id LEFT JOIN village_farmers as vf ON vfc.farmer_id = vf.farmer_id WHERE  vfc.product_id =  $request->filter_product AND vfc.crop_id =  $request->filter_crop AND(f.call_status = '1' OR vf.call_status = '1')";
            $List2crop  = collect(DB::select($List2crop));
          foreach ($List2crop as $keycrop => $valuecrop) {
            array_push($farmerArray, $valuecrop->farmer_id);
          }
        }
       // elseif(empty($request->filter_DD) && empty($request->filter_FL) && empty($request->filter_ASM) && empty($request->filter_RSM))
       // {

       // }
        if(count($farmerArr) > 0 && count($farmerArray) > 0)
        {
          $result = array_intersect($farmerArr,$farmerArray);
        }
        elseif(count($farmerArr) > 0 && count($farmerArray) == 0 && (empty($request->filter_crop)  && empty($request->filter_product)))
        {
            $result = $farmerArr;
        }
         elseif(count($farmerArr) == 0 && count($farmerArray) > 0 && (empty($request->filter_RSM) && empty($request->filter_ASM) && empty($request->filter_FL) && empty($request->filter_DD)))
        {
            $result = $farmerArray;
        }
        else
        {
           $result = array();
        }
            // echo "<pre>"; print_r($result); die;
       if(count($result) > 0)
       {
        if(!empty($request->filter_start) && !empty($request->filter_end))
        {
          $checkFarmer = implode("','", $result);
          $from = $request->filter_start;
          $to = $request->filter_end;
          
          $callList="SELECT f.farmer_id,
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
          CONCAT_WS(
                      '\n', 
                      NULLIF(zm.zonal_manager_name, ''),
                      NULLIF(rm.regional_manager_name, ''),
                      NULLIF(am.area_manager_name, ''),
                      NULLIF(srm.sales_regional_manager_name, ''),
                      NULLIF(sam.sales_area_manager_name, ''),
                      NULLIF(u.name, ''),
                      NULLIF(dd.name, '')
                  ) AS username,
              t.ticket_no,
              f.farmer_name,
              f.mobile,
              f.updated_at 
              FROM  `farmers` as f 
              LEFT JOIN   users  u   ON f.user_id=u.id AND f.user_type='bdo'
              LEFT JOIN   zonal_managers  zm  ON f.user_id=zm.zonal_manager_id AND f.user_type='zonal'
              LEFT JOIN   regional_managers rm  ON f.user_id=rm.regional_manager_id AND f.user_type='regional'
              LEFT JOIN   area_managers   am  ON f.user_id=am.area_manager_id AND f.user_type='area'
              LEFT JOIN   sales_regional_managers srm ON f.user_id=srm.sales_regional_manager_id AND f.user_type='sales_regional'
              LEFT JOIN   sales_area_managers sam ON f.user_id=sam.sales_area_manager_id AND f.user_type='sales_area'
              LEFT JOIN   dhanuka_doctor dd ON f.user_id=dd.dd_id AND f.user_type='dhanuka_doctor'
              LEFT JOIN   tickets t ON f.ticket_id=t.ticket_id 
               WHERE  `call_status` =  '1' AND farmer_id IN ('".$checkFarmer."') AND DATE(created_at) between '$from' AND '$to'"; 
          $callList  = collect(DB::select($callList));
          if (count($callList) > 0) 
          {
            foreach ($callList as $key => $value) {
                $query1="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList[$key]->farmer_id."' AND `l`.`farmer_type` = 'register_farmer'";
                $getlead  = collect(DB::select($query1));
                if (count($getlead)>0) {
                  $callList[$key]->lead_code = $getlead[0]->lead_code;
                  if ($getlead[0]->lead_status == '1') {
                    $callList[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead[0]->lead_status == '2') {
                    $callList[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead[0]->lead_status == '3') {
                    $callList[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList[$key]->lead_status = "";
                  }
                  $callList[$key]->distributor_name = $getlead[0]->distributor_name;
                  $callList[$key]->product = $getlead[0]->product;
                  $callList[$key]->open_date = $getlead[0]->open_date;
                  $callList[$key]->closed_date = $getlead[0]->closed_date;
                  if ($getlead[0]->closed_date == '0000-00-00 00:00:00' || $getlead[0]->closed_date == '') {
                    $calllist[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  {
                    
                    $datetime1 = new DateTime($getlead[0]->open_date);
                    $datetime2 = new DateTime($getlead[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList[$key]->diffrence_open_closed_date = $elapsed;
                  }
                }
                else
                {
                  $callList[$key]->lead_code = "";
                  $callList[$key]->lead_status = "";
                  $callList[$key]->distributor_name = "";
                  $callList[$key]->product = "";
                  $callList[$key]->open_date = "";
                  $callList[$key]->closed_date = "";
                  $callList[$key]->diffrence_open_closed_date = "";
                }
                
              }

            foreach ($callList as $key => $value) {
              $getfarmercrop = DB::table('leads as l')
                                 ->join('farmers as f','f.farmer_id','=','l.farmer_id')
                                 ->join('tickets as t','t.ticket_id','=','f.ticket_id','left')
                                 ->join('crops as c','c.crop_id','=','t.crop_1_id','left')
                                 ->where('l.lead_code',$callList[$key]->lead_code)
                                 ->where('l.farmer_type','register_farmer')
                                 ->select('c.crop')
                                 ->groupBy('c.crop_id')
                                 ->get(); 
                if (count($getfarmercrop)>0) {
                    $callList[$key]->crop = $getfarmercrop[0]->crop;
                }else{
                    $callList[$key]->crop = "";
                }
                
              $callList[$key]->leadSource = "Field day";
            }
          }

          $callList12="SELECT f.farmer_id,
          dd.ECN,
          dd.name as username,
          t.ticket_no,
          f.farmer_name,
          f.contact_number,
          f.updated_at 
          FROM `village_farmers` as f 
          LEFT JOIN   dhanuka_doctor dd ON f.dd_id=dd.dd_id
          LEFT JOIN   tickets t ON f.created_at=t.ticket_id
          WHERE  `call_status` =  '1' AND farmer_id IN ('".$checkFarmer."') AND DATE(created_at) between '$from' AND '$to'"; 
          $callList12  = collect(DB::select($callList12));
          if (count($callList12) > 0) 
          {
            foreach ($callList12 as $key => $value) {
                $query2="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date  
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList12[$key]->farmer_id."' AND `l`.`farmer_type` = 'normal_farmer'";
                $getlead1  = collect(DB::select($query2));
                if (count($getlead1)>0) {
                  $callList12[$key]->lead_code = $getlead1[0]->lead_code;
                  if ($getlead1[0]->lead_status == '1') {
                    $callList12[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead1[0]->lead_status == '2') {
                    $callList12[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead1[0]->lead_status == '3') {
                    $callList12[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList12[$key]->lead_status = "";
                  }

                  $callList12[$key]->distributor_name = $getlead1[0]->distributor_name;
                  $callList12[$key]->product = $getlead1[0]->product;
                  $callList12[$key]->open_date = $getlead1[0]->open_date;
                  $callList12[$key]->closed_date = $getlead1[0]->closed_date;
                  if ($getlead1[0]->closed_date == '0000-00-00 00:00:00' || $getlead1[0]->closed_date == '') {
                    $callList12[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  {
                    
                    $datetime1 = new DateTime($getlead1[0]->open_date);
                    $datetime2 = new DateTime($getlead1[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList12[$key]->diffrence_open_closed_date = $elapsed;
                  }
                }
                else
                {
                  $callList12[$key]->lead_code = "";
                  $callList12[$key]->lead_status = "";
                  $callList12[$key]->distributor_name = "";
                  $callList12[$key]->product = "";
                  $callList12[$key]->open_date = "";
                  $callList12[$key]->closed_date = "";
                  $callList12[$key]->diffrence_open_closed_date = "";
                }
              }

              foreach ($callList12 as $key => $value) {
                $getacpcrop = DB::table('village_farmer_crop as vfc')
                              ->join('crops as c','c.crop_id','=','vfc.crop_id')
                              ->where('vfc.farmer_id',$callList12[$key]->farmer_id)
                              ->select('c.crop')
                              ->get();
                if (count($getacpcrop)>0) {
                    $callList12[$key]->crop = $getacpcrop[0]->crop;
                }
                else
                {
                    $callList12[$key]->crop = "";
                }

                $callList12[$key]->ticket_no = "";
                $callList12[$key]->leadSource = "ACP";
              }
          }

        // $callList="SELECT farmer_id,farmer_name,mobile,updated_at FROM  `farmers` WHERE  `call_status` =  '1' AND farmer_id IN ('".$checkFarmer."') AND DATE(created_at) between '$from' AND '$to'"; 
        //   $callList  = collect(DB::select($callList));

        //   $callList12="SELECT farmer_id,farmer_name,contact_number,updated_at FROM `village_farmers` WHERE  `call_status` =  '1' AND farmer_id IN ('".$checkFarmer."') AND DATE(created_at) between '$from' AND '$to'"; 
        //   $callList12  = collect(DB::select($callList12));
          $merged = $callList->merge($callList12);
        }
        else
        {
          $checkFarmer = implode("','", $result);
          $callList="SELECT f.farmer_id,
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
          CONCAT_WS(
                      '\n', 
                      NULLIF(zm.zonal_manager_name, ''),
                      NULLIF(rm.regional_manager_name, ''),
                      NULLIF(am.area_manager_name, ''),
                      NULLIF(srm.sales_regional_manager_name, ''),
                      NULLIF(sam.sales_area_manager_name, ''),
                      NULLIF(u.name, ''),
                      NULLIF(dd.name, '')
                  ) AS username,
              t.ticket_no,
              f.farmer_name,
              f.mobile,
              f.updated_at 
              FROM  `farmers` as f 
              LEFT JOIN   users  u   ON f.user_id=u.id AND f.user_type='bdo'
              LEFT JOIN   zonal_managers  zm  ON f.user_id=zm.zonal_manager_id AND f.user_type='zonal'
              LEFT JOIN   regional_managers rm  ON f.user_id=rm.regional_manager_id AND f.user_type='regional'
              LEFT JOIN   area_managers   am  ON f.user_id=am.area_manager_id AND f.user_type='area'
              LEFT JOIN   sales_regional_managers srm ON f.user_id=srm.sales_regional_manager_id AND f.user_type='sales_regional'
              LEFT JOIN   sales_area_managers sam ON f.user_id=sam.sales_area_manager_id AND f.user_type='sales_area'
              LEFT JOIN   dhanuka_doctor dd ON f.user_id=dd.dd_id AND f.user_type='dhanuka_doctor'
              LEFT JOIN   tickets t ON f.ticket_id=t.ticket_id 
               WHERE  `call_status` =  '1' AND farmer_id IN ('".$checkFarmer."')"; 
          $callList  = collect(DB::select($callList));
          if (count($callList) > 0) 
          {
            foreach ($callList as $key => $value) {
                $query1="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList[$key]->farmer_id."' AND `l`.`farmer_type` = 'register_farmer'";
                $getlead  = collect(DB::select($query1));
                if (count($getlead)>0) {
                  $callList[$key]->lead_code = $getlead[0]->lead_code;
                  if ($getlead[0]->lead_status == '1') {
                    $callList[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead[0]->lead_status == '2') {
                    $callList[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead[0]->lead_status == '3') {
                    $callList[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList[$key]->lead_status = "";
                  }
                  $callList[$key]->distributor_name = $getlead[0]->distributor_name;
                  $callList[$key]->product = $getlead[0]->product;
                  $callList[$key]->open_date = $getlead[0]->open_date;
                  $callList[$key]->closed_date = $getlead[0]->closed_date;
                  if ($getlead[0]->closed_date == '0000-00-00 00:00:00' || $getlead[0]->closed_date == '') {
                    $callList[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  {
                    
                    $datetime1 = new DateTime($getlead[0]->open_date);
                    $datetime2 = new DateTime($getlead[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList[$key]->diffrence_open_closed_date = $elapsed;
                  }
                }
                else
                {
                  $callList[$key]->lead_code = "";
                  $callList[$key]->lead_status = "";
                  $callList[$key]->distributor_name = "";
                  $callList[$key]->product = "";
                  $callList[$key]->open_date = "";
                  $callList[$key]->closed_date = "";
                  $callList[$key]->diffrence_open_closed_date = "";
                }
                
              }

            foreach ($callList as $key => $value) {
              $getfarmercrop = DB::table('leads as l')
                                 ->join('farmers as f','f.farmer_id','=','l.farmer_id')
                                 ->join('tickets as t','t.ticket_id','=','f.ticket_id','left')
                                 ->join('crops as c','c.crop_id','=','t.crop_1_id','left')
                                 ->where('l.lead_code',$callList[$key]->lead_code)
                                 ->where('l.farmer_type','register_farmer')
                                 ->select('c.crop')
                                 ->groupBy('c.crop_id')
                                 ->get(); 
                if (count($getfarmercrop)>0) {
                    $callList[$key]->crop = $getfarmercrop[0]->crop;
                }else{
                    $callList[$key]->crop = "";
                }
                
              $callList[$key]->leadSource = "Field day";
            }
          }

          $callList12="SELECT f.farmer_id,
          dd.ECN,
          dd.name as username,
          t.ticket_no,
          f.farmer_name,
          f.contact_number,
          f.updated_at 
          FROM `village_farmers` as f 
          LEFT JOIN   dhanuka_doctor dd ON f.dd_id=dd.dd_id
          LEFT JOIN   tickets t ON f.created_at=t.ticket_id
          WHERE  `call_status` =  '1' AND farmer_id IN ('".$checkFarmer."')"; 
          $callList12  = collect(DB::select($callList12));
          if (count($callList12) > 0) 
          {
            foreach ($callList12 as $key => $value) {
                $query2="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date  
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList12[$key]->farmer_id."' AND `l`.`farmer_type` = 'normal_farmer'";
                $getlead1  = collect(DB::select($query2));
                if (count($getlead1)>0) {
                  $callList12[$key]->lead_code = $getlead1[0]->lead_code;
                  if ($getlead1[0]->lead_status == '1') {
                    $callList12[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead1[0]->lead_status == '2') {
                    $callList12[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead1[0]->lead_status == '3') {
                    $callList12[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList12[$key]->lead_status = "";
                  }

                  $callList12[$key]->distributor_name = $getlead1[0]->distributor_name;
                  $callList12[$key]->product = $getlead1[0]->product;
                  $callList12[$key]->open_date = $getlead1[0]->open_date;
                  $callList12[$key]->closed_date = $getlead1[0]->closed_date;
                  if ($getlead1[0]->closed_date == '0000-00-00 00:00:00' || $getlead1[0]->closed_date == '') {
                    $callList12[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  {
                    
                    $datetime1 = new DateTime($getlead1[0]->open_date);
                    $datetime2 = new DateTime($getlead1[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList12[$key]->diffrence_open_closed_date = $elapsed;
                  }
                }
                else
                {
                  $callList12[$key]->lead_code = "";
                  $callList12[$key]->lead_status = "";
                  $callList12[$key]->distributor_name = "";
                  $callList12[$key]->product = "";
                  $callList12[$key]->open_date = "";
                  $callList12[$key]->closed_date = "";
                  $callList12[$key]->diffrence_open_closed_date = "";
                }
              }

              foreach ($callList12 as $key => $value) {
                $getacpcrop = DB::table('village_farmer_crop as vfc')
                              ->join('crops as c','c.crop_id','=','vfc.crop_id')
                              ->where('vfc.farmer_id',$callList12[$key]->farmer_id)
                              ->select('c.crop')
                              ->get();
                if (count($getacpcrop)>0) {
                    $callList12[$key]->crop = $getacpcrop[0]->crop;
                }
                else
                {
                    $callList12[$key]->crop = "";
                }

                $callList12[$key]->ticket_no = "";
                $callList12[$key]->leadSource = "ACP";
              }
          }

        //   $checkFarmer = implode("','", $result);
        // $callList="SELECT farmer_id,farmer_name,mobile,updated_at FROM  `farmers` WHERE  `call_status` =  '1' AND farmer_id IN ('".$checkFarmer."')"; 
        //   $callList  = collect(DB::select($callList));

        //    $callList12="SELECT farmer_id,farmer_name,contact_number,updated_at FROM `village_farmers` WHERE  `call_status` =  '1' AND farmer_id IN ('".$checkFarmer."')"; 
        //   $callList12  = collect(DB::select($callList12));
          $merged = $callList->merge($callList12);
        }
       }
       else
       {
        if(!empty($request->filter_start) && !empty($request->filter_end))
        {
          $from = $request->filter_start;
          $to = $request->filter_end;
        //$checkFarmer = implode("','", $result);

        $callList="SELECT f.farmer_id,
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
          CONCAT_WS(
                      '\n', 
                      NULLIF(zm.zonal_manager_name, ''),
                      NULLIF(rm.regional_manager_name, ''),
                      NULLIF(am.area_manager_name, ''),
                      NULLIF(srm.sales_regional_manager_name, ''),
                      NULLIF(sam.sales_area_manager_name, ''),
                      NULLIF(u.name, ''),
                      NULLIF(dd.name, '')
                  ) AS username,
              t.ticket_no,
              f.farmer_name,
              f.mobile,
              f.updated_at 
              FROM  `farmers` as f 
              LEFT JOIN   users  u   ON f.user_id=u.id AND f.user_type='bdo'
              LEFT JOIN   zonal_managers  zm  ON f.user_id=zm.zonal_manager_id AND f.user_type='zonal'
              LEFT JOIN   regional_managers rm  ON f.user_id=rm.regional_manager_id AND f.user_type='regional'
              LEFT JOIN   area_managers   am  ON f.user_id=am.area_manager_id AND f.user_type='area'
              LEFT JOIN   sales_regional_managers srm ON f.user_id=srm.sales_regional_manager_id AND f.user_type='sales_regional'
              LEFT JOIN   sales_area_managers sam ON f.user_id=sam.sales_area_manager_id AND f.user_type='sales_area'
              LEFT JOIN   dhanuka_doctor dd ON f.user_id=dd.dd_id AND f.user_type='dhanuka_doctor'
              LEFT JOIN   tickets t ON f.ticket_id=t.ticket_id 
               WHERE  `call_status` =  '1' AND DATE(created_at) between '$from' AND '$to' AND farmer_id IN ('')"; 
          $callList  = collect(DB::select($callList));
          if (count($callList) > 0) 
          {
            foreach ($callList as $key => $value) {
                $query1="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList[$key]->farmer_id."' AND `l`.`farmer_type` = 'register_farmer'";
                $getlead  = collect(DB::select($query1));
                if (count($getlead)>0) {
                  $callList[$key]->lead_code = $getlead[0]->lead_code;
                  if ($getlead[0]->lead_status == '1') {
                    $callList[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead[0]->lead_status == '2') {
                    $callList[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead[0]->lead_status == '3') {
                    $callList[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList[$key]->lead_status = "";
                  }
                  $callList[$key]->distributor_name = $getlead[0]->distributor_name;
                  $callList[$key]->product = $getlead[0]->product;
                  $callList[$key]->open_date = $getlead[0]->open_date;
                  $callList[$key]->closed_date = $getlead[0]->closed_date;
                  if ($getlead[0]->closed_date == '0000-00-00 00:00:00' || $getlead[0]->closed_date == '') {
                    $callList[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  {
                    
                    $datetime1 = new DateTime($getlead[0]->open_date);
                    $datetime2 = new DateTime($getlead[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList[$key]->diffrence_open_closed_date = $elapsed;
                  }
                }
                else
                {
                  $callList[$key]->lead_code = "";
                  $callList[$key]->lead_status = "";
                  $callList[$key]->distributor_name = "";
                  $callList[$key]->product = "";
                  $callList[$key]->open_date = "";
                  $callList[$key]->closed_date = "";
                  $callList[$key]->diffrence_open_closed_date = "";
                }
                
              }

            foreach ($callList as $key => $value) {
              $getfarmercrop = DB::table('leads as l')
                                 ->join('farmers as f','f.farmer_id','=','l.farmer_id')
                                 ->join('tickets as t','t.ticket_id','=','f.ticket_id','left')
                                 ->join('crops as c','c.crop_id','=','t.crop_1_id','left')
                                 ->where('l.lead_code',$callList[$key]->lead_code)
                                 ->where('l.farmer_type','register_farmer')
                                 ->select('c.crop')
                                 ->groupBy('c.crop_id')
                                 ->get(); 
                if (count($getfarmercrop)>0) {
                    $callList[$key]->crop = $getfarmercrop[0]->crop;
                }else{
                    $callList[$key]->crop = "";
                }
                
              $callList[$key]->leadSource = "Field day";
            }
          }

          $callList12="SELECT f.farmer_id,
          dd.ECN,
          dd.name as username,
          t.ticket_no,
          f.farmer_name,
          f.contact_number,
          f.updated_at 
          FROM `village_farmers` as f 
          LEFT JOIN   dhanuka_doctor dd ON f.dd_id=dd.dd_id
          LEFT JOIN   tickets t ON f.created_at=t.ticket_id
          WHERE  `call_status` =  '1' AND DATE(created_at) between '$from' AND '$to' AND farmer_id IN ('')"; 
          $callList12  = collect(DB::select($callList12));
          if (count($callList12) > 0) 
          {
            foreach ($callList12 as $key => $value) {
                $query2="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date  
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList12[$key]->farmer_id."' AND `l`.`farmer_type` = 'normal_farmer'";
                $getlead1  = collect(DB::select($query2));
                if (count($getlead1)>0) {
                  $callList12[$key]->lead_code = $getlead1[0]->lead_code;
                  if ($getlead1[0]->lead_status == '1') {
                    $callList12[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead1[0]->lead_status == '2') {
                    $callList12[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead1[0]->lead_status == '3') {
                    $callList12[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList12[$key]->lead_status = "";
                  }

                  $callList12[$key]->distributor_name = $getlead1[0]->distributor_name;
                  $callList12[$key]->product = $getlead1[0]->product;
                  $callList12[$key]->open_date = $getlead1[0]->open_date;
                  $callList12[$key]->closed_date = $getlead1[0]->closed_date;
                  if ($getlead1[0]->closed_date == '0000-00-00 00:00:00' || $getlead1[0]->closed_date == '') {
                    $callList12[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  {
                    
                    $datetime1 = new DateTime($getlead1[0]->open_date);
                    $datetime2 = new DateTime($getlead1[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList12[$key]->diffrence_open_closed_date = $elapsed;
                  }
                }
                else
                {
                  $callList12[$key]->lead_code = "";
                  $callList12[$key]->lead_status = "";
                  $callList12[$key]->distributor_name = "";
                  $callList12[$key]->product = "";
                  $callList12[$key]->open_date = "";
                  $callList12[$key]->closed_date = "";
                  $callList12[$key]->diffrence_open_closed_date = "";
                }
              }

              foreach ($callList12 as $key => $value) {
                $getacpcrop = DB::table('village_farmer_crop as vfc')
                              ->join('crops as c','c.crop_id','=','vfc.crop_id')
                              ->where('vfc.farmer_id',$callList12[$key]->farmer_id)
                              ->select('c.crop')
                              ->get();
                if (count($getacpcrop)>0) {
                    $callList12[$key]->crop = $getacpcrop[0]->crop;
                }
                else
                {
                    $callList12[$key]->crop = "";
                }

                $callList12[$key]->ticket_no = "";
                $callList12[$key]->leadSource = "ACP";
              }
          }

        // $callList="SELECT farmer_id,farmer_name,mobile,updated_at FROM  `farmers` WHERE  `call_status` =  '1' AND DATE(created_at) between '$from' AND '$to' AND farmer_id IN ('')"; 
        //   $callList  = collect(DB::select($callList));

        //   $callList12="SELECT farmer_id,farmer_name,contact_number,updated_at FROM `village_farmers` WHERE  `call_status` =  '1' AND DATE(created_at) between '$from' AND '$to' AND farmer_id IN ('')"; 
        //   $callList12  = collect(DB::select($callList12));
          $merged = $callList->merge($callList12);
        }
        else
        {
         
         $callList="SELECT f.farmer_id,
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
          CONCAT_WS(
                      '\n', 
                      NULLIF(zm.zonal_manager_name, ''),
                      NULLIF(rm.regional_manager_name, ''),
                      NULLIF(am.area_manager_name, ''),
                      NULLIF(srm.sales_regional_manager_name, ''),
                      NULLIF(sam.sales_area_manager_name, ''),
                      NULLIF(u.name, ''),
                      NULLIF(dd.name, '')
                  ) AS username,
              t.ticket_no,
              f.farmer_name,
              f.mobile,
              f.updated_at 
              FROM  `farmers` as f 
              LEFT JOIN   users  u   ON f.user_id=u.id AND f.user_type='bdo'
              LEFT JOIN   zonal_managers  zm  ON f.user_id=zm.zonal_manager_id AND f.user_type='zonal'
              LEFT JOIN   regional_managers rm  ON f.user_id=rm.regional_manager_id AND f.user_type='regional'
              LEFT JOIN   area_managers   am  ON f.user_id=am.area_manager_id AND f.user_type='area'
              LEFT JOIN   sales_regional_managers srm ON f.user_id=srm.sales_regional_manager_id AND f.user_type='sales_regional'
              LEFT JOIN   sales_area_managers sam ON f.user_id=sam.sales_area_manager_id AND f.user_type='sales_area'
              LEFT JOIN   dhanuka_doctor dd ON f.user_id=dd.dd_id AND f.user_type='dhanuka_doctor'
              LEFT JOIN   tickets t ON f.ticket_id=t.ticket_id 
               WHERE  `call_status` =  '1' AND farmer_id IN ('')"; 
          $callList  = collect(DB::select($callList));
          if (count($callList) > 0) 
          {
            foreach ($callList as $key => $value) {
                $query1="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList[$key]->farmer_id."' AND `l`.`farmer_type` = 'register_farmer'";
                $getlead  = collect(DB::select($query1));
                if (count($getlead)>0) {
                  $callList[$key]->lead_code = $getlead[0]->lead_code;
                  if ($getlead[0]->lead_status == '1') {
                    $callList[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead[0]->lead_status == '2') {
                    $callList[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead[0]->lead_status == '3') {
                    $callList[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList[$key]->lead_status = "";
                  }
                  $callList[$key]->distributor_name = $getlead[0]->distributor_name;
                  $callList[$key]->product = $getlead[0]->product;
                  $callList[$key]->open_date = $getlead[0]->open_date;
                  $callList[$key]->closed_date = $getlead[0]->closed_date;
                  if ($getlead[0]->closed_date == '0000-00-00 00:00:00' || $getlead[0]->closed_date == '') {
                    $callList[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  {
                    
                    $datetime1 = new DateTime($getlead[0]->open_date);
                    $datetime2 = new DateTime($getlead[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList[$key]->diffrence_open_closed_date = $elapsed;
                  }
                }
                else
                {
                  $callList[$key]->lead_code = "";
                  $callList[$key]->lead_status = "";
                  $callList[$key]->distributor_name = "";
                  $callList[$key]->product = "";
                  $callList[$key]->open_date = "";
                  $callList[$key]->closed_date = "";
                  $callList[$key]->diffrence_open_closed_date = "";
                }
                
              }

            foreach ($callList as $key => $value) {
              $getfarmercrop = DB::table('leads as l')
                                 ->join('farmers as f','f.farmer_id','=','l.farmer_id')
                                 ->join('tickets as t','t.ticket_id','=','f.ticket_id','left')
                                 ->join('crops as c','c.crop_id','=','t.crop_1_id','left')
                                 ->where('l.lead_code',$callList[$key]->lead_code)
                                 ->where('l.farmer_type','register_farmer')
                                 ->select('c.crop')
                                 ->groupBy('c.crop_id')
                                 ->get(); 
                if (count($getfarmercrop)>0) {
                    $callList[$key]->crop = $getfarmercrop[0]->crop;
                }else{
                    $callList[$key]->crop = "";
                }
                
              $callList[$key]->leadSource = "Field day";
            }
          }

          $callList12="SELECT f.farmer_id,
          dd.ECN,
          dd.name as username,
          t.ticket_no,
          f.farmer_name,
          f.contact_number,
          f.updated_at 
          FROM `village_farmers` as f 
          LEFT JOIN   dhanuka_doctor dd ON f.dd_id=dd.dd_id
          LEFT JOIN   tickets t ON f.created_at=t.ticket_id
          WHERE  `call_status` =  '1' AND farmer_id IN ('')"; 
          $callList12  = collect(DB::select($callList12));
          if (count($callList12) > 0) 
          {
            foreach ($callList12 as $key => $value) {
                $query2="SELECT 
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_code, '')
                                    ) AS lead_code,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(l.lead_status, '')
                                    ) AS lead_status,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(d.name, '')
                                    ) AS distributor_name,
                            CONCAT_WS(
                                        '\n', 
                                        NULLIF(p.product, '')
                                    ) AS product,
                            DATE_FORMAT(l.created_at, '%W, %M %e %Y, %h:%i %p') as open_date,
                            DATE_FORMAT(l.updated_at, '%W, %M %e %Y, %h:%i %p') as closed_date
                             
                            FROM  `leads` as l 
                            LEFT JOIN products as p ON p.product_id=l.product_id
                            LEFT JOIN   distributor d ON d.distributor_id=l.distributor_id 
                            WHERE  `l`.`farmer_id` =  '".$callList12[$key]->farmer_id."' AND `l`.`farmer_type` = 'normal_farmer'";
                $getlead1  = collect(DB::select($query2));
                if (count($getlead1)>0) {
                  $callList12[$key]->lead_code = $getlead1[0]->lead_code;
                  if ($getlead1[0]->lead_status == '1') {
                    $callList12[$key]->lead_status = 'Open';
                  }
                  elseif ($getlead1[0]->lead_status == '2') {
                    $callList12[$key]->lead_status = 'Sold';
                  }
                  elseif ($getlead1[0]->lead_status == '3') {
                    $callList12[$key]->lead_status = 'Unsold';
                  }
                  else
                  {
                    $callList12[$key]->lead_status = "";
                  }

                  $callList12[$key]->distributor_name = $getlead1[0]->distributor_name;
                  $callList12[$key]->product = $getlead1[0]->product;
                  $callList12[$key]->open_date = $getlead1[0]->open_date;
                  $callList12[$key]->closed_date = $getlead1[0]->closed_date;
                  if ($getlead1[0]->closed_date == '0000-00-00 00:00:00' || $getlead1[0]->closed_date == '') {
                    $callList12[$key]->diffrence_open_closed_date = "";
                  }
                  else
                  {
                    
                    $datetime1 = new DateTime($getlead1[0]->open_date);
                    $datetime2 = new DateTime($getlead1[0]->closed_date);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
                    $callList12[$key]->diffrence_open_closed_date = $elapsed;
                  }
                }
                else
                {
                  $callList12[$key]->lead_code = "";
                  $callList12[$key]->lead_status = "";
                  $callList12[$key]->distributor_name = "";
                  $callList12[$key]->product = "";
                  $callList12[$key]->open_date = "";
                  $callList12[$key]->closed_date = "";
                  $callList12[$key]->diffrence_open_closed_date = "";
                }
              }

              foreach ($callList12 as $key => $value) {
                $getacpcrop = DB::table('village_farmer_crop as vfc')
                              ->join('crops as c','c.crop_id','=','vfc.crop_id')
                              ->where('vfc.farmer_id',$callList12[$key]->farmer_id)
                              ->select('c.crop')
                              ->get();
                if (count($getacpcrop)>0) {
                    $callList12[$key]->crop = $getacpcrop[0]->crop;
                }
                else
                {
                    $callList12[$key]->crop = "";
                }

                $callList12[$key]->ticket_no = "";
                $callList12[$key]->leadSource = "ACP";
              }
          }

        // $callList="SELECT farmer_id,farmer_name,mobile,updated_at FROM  `farmers` WHERE  `call_status` =  '1' AND farmer_id IN ('')"; 
        //   $callList  = collect(DB::select($callList));

        //   $callList12="SELECT farmer_id,farmer_name,contact_number,updated_at FROM `village_farmers` WHERE  `call_status` =  '1' AND farmer_id IN ('')"; 
        //   $callList12  = collect(DB::select($callList12));
          $merged = $callList->merge($callList12);
        }

     
     }
     

     }
       // echo json_encode($merged->toArray()); die;
        //echo json_encode($merged); die;
     return Datatables::of($merged)->make(); 


    }
	
    
    
}
