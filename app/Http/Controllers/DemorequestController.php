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

use App\Dcdistrictwise;
use App\Dccropwise;
use App\Dccompanywise;
use App\Farmer;

use App\Testimonial;
use App\SelfParticipation;
use App\CustomerQuery;

use App\DhanukaDoctor;
use App\DdProductCategory;

use App\Efficiency;
use App\AssignedVillage;
use App\Reminder;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use Khill\Lavacharts\Lavacharts;

use App\UserProductCategoryOrn;
use App\DdProductCategoryOrn;
use App\AssignedVillageOrn;
use App\DhanukaDoctorOrn;
use App\Userorn;
use App\UserDeputationOrn;
use App\AreaManagerorn;
use App\AreaManagerStateOrn;
use App\AreaManagerProductCategoryOrn;
use App\RegionalManagerOrn;
use App\RegionalManagerStateOrn;
use App\RegionalManagerProductCategoryOrn;
use App\SalesAreaManagerOrn;
use App\SalesAreaManagerStateOrn;
use App\SalesAreaManagerProductCategoryOrn;
use App\SalesRegionalManagerOrn;
use App\SalesRegionalManagerStateOrn;
use App\SalesRegionalManagerProductCategoryOrn;

use App\DemoMaterialRequest;
use App\DemoMaterialRequestQA;

ini_set('max_execution_time', 0);
// ini_set("memory_limit", "-1");
// set_time_limit(0);

class DemorequestController extends Controller {

    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect(env('APP_URL'));
        }
    }

    public function getListRequest() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $states       = State::get();
        $districts    = District::get();
        return View::make('backend.demorequest.listrequestdata');
    }
    
    public function postAlllistrequestdata(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        // print_r(Request::all()); exit;
        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        $base_url       = url('/');
        
        $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
        $end_date       = isset($value['end_date']) ?  $value['end_date']:'';

        $add2query = "";
        if($start_date!='' && $end_date!='') {
            $add2query = " DATE(dr.created_at) >= DATE('".$start_date."') AND DATE(dr.created_at) <= DATE('".$end_date."')";
        } elseif($start_date!='') {
            $add2query = "DATE(dr.created_at) >= DATE('".$start_date."')";
        } else {
            $add2query = "(1=1)";
        }

        $needed_start_date= isset($value['needed_start_date']) ? $value['needed_start_date'] :'';
        $needed_end_date = isset($value['needed_end_date']) ? $value['needed_end_date']:'';
        if($needed_start_date!='' && $needed_end_date!='') {
            $add2query .= " AND DATE(dr.need_by_date) >= DATE('".$needed_start_date."') AND DATE(dr.need_by_date) <= DATE('".$needed_end_date."')";
        } elseif($needed_start_date!='') {
            $add2query .= " AND DATE(dr.need_by_date) >= DATE('".$needed_start_date."')";
        } else {}

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $add2query .= " AND (
                dr.requested_by_ecn LIKE '%".$search."%'   OR
                dr.s1_ecn_no        LIKE '%".$search."%'   OR
                dr.product_name     LIKE '%".$search."%'   OR
                dr.crop_name        LIKE '%".$search."%'   OR
                dr.godown_name      LIKE '%".$search."%'
            )";
        }

        $query = "
            SELECT
                @a:=@a+1  as serial_number,
                A.*
                    FROM
                        (
                            SELECT
                                dr.requested_by_ecn,
                                dr.product_name,
                                dr.crop_name,
                                dr.expected_demos,
                                DATE_FORMAT(dr.need_by_date, '%Y-%m %d, %h:%i %p') as need_by_date,
                                dr.requested_units,
                                dr.godown_name,
                                DATE_FORMAT(dr.created_at, '%Y-%m %d, %h:%i %p') as c_datetime_format,
                                dr.rsm_approval_status,
                                dr.rsm_approved_unit,
                                dr.rsm_ecn_no,
                                DATE_FORMAT(dr.rsm_approval_date, '%Y-%m %d, %h:%i %p') as rsm_approval_date,
                                dr.admin_approval_status,
                                DATE_FORMAT(dr.admin_approval_date, '%Y-%m %d, %h:%i %p') as admin_approval_date,
                                dr.dispatch_status,
                                dr.dispatch_units,
                                DATE_FORMAT(dr.actual_dispatch_date, '%Y-%m %d, %h:%i %p') as actual_dispatch_date,
                                DATE_FORMAT(dr.dispatch_status_timestamp, '%Y-%m %d, %h:%i %p') as dispatch_status_timestamp,
                                dr.receiving_status,
                                DATE_FORMAT(dr.receiving_date, '%Y-%m %d, %h:%i %p') as receiving_date,
                                DATE_FORMAT(dr.receiving_status_captured_date, '%Y-%m %d, %h:%i %p') as receiving_status_captured_date,
                                dr.received_comment,
                                dr.demo_material_request_id
                            FROM    demo_material_request   dr
                            WHERE  ".$add2query."
                            ORDER BY c_datetime_format DESC
                        ) A,
                        (select @a:=0) initvars";
        // echo $query; exit;
        $demorequest_data = collect(DB::select($query));
        return Datatables::of($demorequest_data)->make();
    }

    public function postUpdateAdminStatus(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $material_data_status = (isset($my_data['status'])?$my_data['status']:'');
            $material_request_id =(isset($my_data['requestId'])?$my_data['requestId']:'');
            $material_data  = DemoMaterialRequest::where('demo_material_request_id', $material_request_id)->first();
            if($material_data) {
                $update_material_data = DB::table('demo_material_request')
                                    ->where('demo_material_request_id', $material_request_id)
                                    ->update([
                                        'admin_approval_status'=>$material_data_status,
                                        'admin_approval_date'=>  date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                echo '1';
            } else {
                echo '0';
            }
        } else {
            echo '0';
        }
    }

    public function postUpdateDispatchStatus(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $et_dispatch_date = (isset($my_data['et_dispatch'])? date("Y-m-d H:i:s",strtotime($my_data['et_dispatch'])):'');
            $dispatch_status = (isset($my_data['dispatchStatus'])?$my_data['dispatchStatus']:'');
            $dispatch_units = (isset($my_data['dispatchunit'])?$my_data['dispatchunit']:'');
            $actual_dispatch_date = (isset($my_data['actual_dispatch'])? date("Y-m-d H:i:s",strtotime($my_data['actual_dispatch'])):'');
            $material_request_id =(isset($my_data['request_id'])?$my_data['request_id']:'');            
            $material_data  = DemoMaterialRequest::where('demo_material_request_id', $material_request_id)->first();
            if($material_data) {
                $update_material_data = DB::table('demo_material_request')
                                    ->where('demo_material_request_id', $material_request_id)
                                    ->update([
                                        'et_dispatch_date'=>$et_dispatch_date,
                                        'dispatch_status'=>$dispatch_status,
                                        'dispatch_units'=>$dispatch_units,
                                        'actual_dispatch_date'=>$actual_dispatch_date,
                                        'dispatch_status_timestamp'=>date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                echo '1';
            } else {
                echo '0';
            }
        } else {
            echo '0';
        }
    }

    public function getMaterialChat($demorequest_id) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($demorequest_id!='') {
                // return $demorequestqa = DemoMaterialRequestQA::where('demo_material_request_id', $demorequest_id)->get();
                return View::make('backend.demorequest.listrequestchat',array('demorequest_id'=>$demorequest_id));
            } else {
                return redirect('/demorequest/list-request');
            }
        } else {
            return redirect('/');
        }
    }

    public function postAllMaterialChatlist(Request $request) {
        // echo "Called!"; exit();
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

        // $state_name     = $value['state_name'];
        
        $addQryList = '(1=1)';
        if(!empty($value['demorequest_id'])) {
            $demorequest_id = $value['demorequest_id'];
            $addQryList .= ' AND qa.demo_material_request_id="'.$demorequest_id.'"';
        }
        $query  = " 
                    SELECT                                 
                        @a:=@a+1  as serial_number,
                        A.*                                
                    FROM
                        (
                        SELECT 
                            DATE_FORMAT(qa.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                            CONCAT_WS(', ', qa.user_name, qa.user_ecn) as user,
                            qa.content as comments
                        FROM
                            demo_material_request_qa qa
                        WHERE $addQryList
                        ORDER BY
                            qa.created_at DESC
                        )  A,
                        (select @a:=0) initvars";
        // return $query;
        $qaChat   = collect(DB::select($query));  
        return Datatables::of($qaChat)->make();
    }

    public function postCreateMaterialChat(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();
            // echo "<pre>"; print_r($my_data); exit();
            $rules  = array(
                        'demorequest_id' => 'required',
                        'comments'       => 'required'
                    );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput($my_data)->withErrors($validator);
            }
            $demorequest_id = $my_data['demorequest_id'];
            $comments       = $my_data['comments'];

            $commentdata = array(
                            'demo_material_request_id' => $demorequest_id,
                            'content'   => $comments,
                            'user_id'   => $sess,
                            'user_type' => 'admin',
                            'user_name' => 'Admin',
                            'created_at'=> date('Y-m-d H:i:s'),
                            'updated_at'=> date('Y-m-d H:i:s')
                        );
            // echo "<pre>"; print_r($commentdata); exit();
            $insert_comment = DemoMaterialRequestQA::firstOrCreate($commentdata);
            $result        = $insert_comment->demo_material_request_qa_id;
            if($result) {
                Session::flash('message', "Chat Successfully Created.");
                return redirect('demorequest/material-chat/'.$demorequest_id);
            } else {
                return redirect('demorequest/material-chat/'.$demorequest_id);
            }
        }
    }

}