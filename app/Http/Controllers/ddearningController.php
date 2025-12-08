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
use App\Tehsil;
use App\Product;
use App\Crop;
use App\ProductCropRelation;
use App\ProductCropCategory;

use App\Ticket;
use App\Farmer;

use App\Testimonial;
use App\SelfParticipation;
use App\CustomerQuery;

use App\DhanukaDoctor;
use App\DdProductCategory;

use App\Efficiency;

use App\DdFarmerVisits;
use App\DdEarningValue;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use Khill\Lavacharts\Lavacharts;

class ddearningController extends Controller
{
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function getDdEarning() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.dd_earning.ddearninglist');
    } 
    public function postDdearninglist() {
    	//echo "here";exit();
        //print_r(Request::all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        //print_r(Request::all()); exit;
        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        
        //$search = $search_array['value'];             
        $query  =   
                    " 
                    SELECT                                 
                        @a:=@a+1  as serial_number,
                        A.*
                    FROM
                        (
                        SELECT
                            t.ticket_no,
                            dd.name,
                            dfv.expected_farmers,
                            dfv.registered_farmers,
                            dfv.expected_value,
                            dfv.amount,
                            dfv.creation_time
                            
                        FROM        dd_farmer_visits dfv
                        LEFT JOIN   dhanuka_doctor  dd  ON dd.dd_id=dfv.dd_id 
                        LEFT JOIN   tickets  t  ON t.plan_atp_id=dfv.plan_atp_id
                        ORDER BY
                            dfv.creation_time DESC
                        ) A,
                        (select @a:=0) initvars";
        $top_open_ticket    = collect(DB::select($query));//echo "<pre>";print_r($top_open_ticket);exit();
        return Datatables::of($top_open_ticket)->make();            
        
    }

    public function getRsmvalues() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.dd_earning.Rsmvalueslist');
    } 

    public function postRsmvalueslist() {
    	//echo "here";exit();
        //print_r(Request::all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        //print_r(Request::all()); exit;
        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        
        //$search = $search_array['value'];             
        $query  =   
                    " 
                    SELECT                                 
                        @a:=@a+1  as serial_number,
                        A.*
                    FROM
                        (
                        SELECT
                            rm.regional_manager_name,
                            dev.dd_earning_value_id,
                            rm.regional_manager_id,
                            dev.dd_field_earning_value,
                            dev.dd_village_earning_value,
                            dev.creation_time
                            
                        FROM        regional_managers  rm
                        LEFT JOIN   rsm_earning_value dev  ON dev.regional_manager_id=rm.regional_manager_id 
                        ORDER BY
                            dev.creation_time DESC
                        ) A,
                        (select @a:=0) initvars";
        $top_open_ticket    = collect(DB::select($query));//echo "<pre>";print_r($top_open_ticket);exit();
        return Datatables::of($top_open_ticket)->make();            
        
    }
    
    public function getEditRsmvalue($regional_manager_id) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $query =  "(SELECT * FROM rsm_earning_value WHERE regional_manager_id='".$regional_manager_id."' order by creation_time desc LIMIT 0,1)";
            $rsmData   = DB::select(DB::raw($query));
            
        return View::make('backend.dd_earning.earingValue', array('rsm_detail'=>$rsmData));
    }     
    

    public function postEarningvaluelist(Request $request) {
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
        
        //$search     =  $search_array['value']; 
        // $categorysearch     = '(1=1)';
        // if($state_name!='') {
        //     $categorysearch     .= "AND (s.state_id='".$state_name."')";
        // }
                  
        $query      = " 
                        SELECT                                 
                        @a:=@a+1  as serial_number,
                        A.*
                    FROM
                        (
                        SELECT
                            dev.dd_field_earning_value,
                            dev.dd_village_earning_value,
                            dev.creation_time
                            
                        FROM        dd_earning_value dev
                        WHERE dev.regional_manager_id = '".$value['regional_manager_id']."'
                        ORDER BY
                            dev.dd_earning_value_id DESC
                        ) A,
                        (select @a:=0) initvars";
        $Earningvalue  = collect(DB::select($query));
        //echo "<pre>";print_r($Earningvalue);die;
        return Datatables::of($Earningvalue)->make();            
        
    } 

    public function postUpdateRsmvalue(Request $request) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();   //print_r($my_data);die;  
            $dd_field_earning_value      = $my_data['dd_field_earning_value'];
            $dd_village_earning_value   = $my_data['dd_village_earning_value'];   
            $regional_manager_id     = $my_data['regional_manager_id'];   
            $rules      = array(
                            'dd_field_earning_value'    => 'required',
                            'dd_village_earning_value' => 'required',
                            'regional_manager_id'   => 'required'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput($my_data)->withErrors($validator)->with('regional_manager_id', $regional_manager_id);
            }
            
            $rsvaluedata = array('regional_manager_id' => $regional_manager_id, 'dd_village_earning_value'   => $dd_village_earning_value, 'dd_field_earning_value'   => $dd_field_earning_value, 'creation_time' => date('Y-m-d H:i:s'));
            $insert_rsvalue = DB::table('dd_earning_value')->insert($rsvaluedata);
            if($insert_rsvalue > 0) {
                $rsm_earning_valuedata = DB::table('rsm_earning_value')
                ->where('regional_manager_id', $regional_manager_id)
                ->update(['dd_village_earning_value'   => $dd_village_earning_value, 'dd_field_earning_value'   => $dd_field_earning_value, 'updation_time' => date('Y-m-d H:i:s')]);
                Session::flash('message', "Earning Values Updated Successfully Createt.");
                return redirect('ddearning/edit-rsmvalue/'.$regional_manager_id);             
            }
            else {
                Session::flash('message', "An error occurret. Please try again later.");
               //return Redirect::back()->withInput()->withErrors($validator);
                return redirect('ddearning/edit-rsmvalue/'.$regional_manager_id); 
            }
        }
    } 
}
