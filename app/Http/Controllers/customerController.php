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

use App\User;
use App\AppMenu;
use App\DataSource;
use App\State;
use App\District;
use App\Tehsil;
use App\Product;
use App\Crop;
use App\Ticket;
use App\Farmer;
use App\Language;
use App\CustomerQuery;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class customerController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getCustomerQueries() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.customer.customerquerylist');
    }
    
    public function postAllquerylist(Request $request) {
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
        
        $searchtype =   $value['searchtype'];
        if($searchtype!='' && $searchtype=='sale') {
            $where = "cq.type_of_call='Sale'";
        }
        else {
            $where = "cq.type_of_call<>'Sale'";
        }
        

        $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
        $end_date       = isset($value['end_date']) ?  $value['end_date']:'';


        if($start_date!='' && $end_date!='') {
                $where.= " and DATE(cq.query_datetime) >= DATE('".$start_date."') AND DATE(cq.query_datetime) <= DATE('".$end_date."')";
        }elseif($start_date!='') {
                $where.= " and DATE(cq.query_datetime) >= DATE('".$start_date."')";
        }
        
      
        

        $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    cq.token_number,
                                    cq.farmername,
                                    cq.mobile,
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
                                    u.ECN,
                                    u.name,
                                    DATE_FORMAT(cq.query_datetime, '%Y-%m-%d, %h:%i %p') as created_at,
                                    (CASE WHEN cq.status='1' THEN 'ACTIVE' WHEN cq.status='2' THEN 'FOLLOW UP' ELSE 'CLOSED' END) as query_status,
                                    cq.bdo_id,
                                    cq.customer_query_id
                                FROM        
                                    customer_queries cq  
                                LEFT JOIN users u ON cq.bdo_id=u.id
                                WHERE ".$where."
                                ORDER BY
                                    cq.query_datetime DESC
                                )   A,
                                (select @a:=0) initvars";
            $districts   = collect(DB::select($query));  
            return Datatables::of($districts)->make(); 
    }
    
    public function postExportAllQueries(Request $request) {          
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data    =   Input::all(); 
            $search     =   $my_data['search'];
            $searchtype =   $my_data['searchtype'];
            $dataSearch = '';
            
            if($searchtype=='sale') {
                $dataSearch .=   "(cq.type_of_call='Sale')";
            }
            else {
                $dataSearch .=   "(cq.type_of_call<>'Sale')";
            }

            $start_date     = isset($my_data['start_date']) ? $my_data['start_date'] :'';
            $end_date       = isset($my_data['end_date']) ?  $my_data['end_date']:'';

            if($start_date!='' && $end_date!='') {
            $dataSearch.= " and DATE(cq.created_at) >= DATE('".$start_date."') AND DATE(cq.created_at) <= DATE('".$end_date."')";
            }elseif($start_date!='') {
            $dataSearch.= " and DATE(cq.created_at) >= DATE('".$start_date."')";
            }
            
            if($search!='') {
                $dataSearch .=   "
                                AND
                                (
                                    cq.token_number LIKE '%".$search."%'    OR
                                    cq.mobile       LIKE '%".$search."%'    OR
                                    cq.farmername   LIKE '%".$search."%'    OR
                                    cq.state        LIKE '%".$search."%'    OR
                                    cq.district     LIKE '%".$search."%'    OR
                                    cq.tehsil       LIKE '%".$search."%'    OR
                                    cq.village      LIKE '%".$search."%'    OR
                                    cq.product      LIKE '%".$search."%'    OR
                                    cq.crop         LIKE '%".$search."%'    OR
                                    cq.disease      LIKE '%".$search."%'    OR
                                    cq.pincode      LIKE '%".$search."%'    OR
                                    cq.type_of_call LIKE '%".$search."%'    OR
                                    cq.is_follow_up LIKE '%".$search."%'    OR
                                    cq.remark       LIKE '%".$search."%'    OR
                                    u.ECN           LIKE '%".$search."%'    OR
                                    u.name          LIKE '%".$search."%'    
                                )             
                                ";
            }
            
            $query      =   " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    cq.token_number,
                                    cq.farmername,
                                    cq.mobile,
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
                                    u.ECN,
                                    u.name,
                                    DATE_FORMAT(cq.query_datetime, '%Y-%m-%d, %h:%i %p') as created_at,
                                    (CASE WHEN cq.status='1' THEN 'Active' WHEN cq.status='2' THEN 'Follow Up' ELSE 'Closed' END) AS Status
                                FROM        
                                    customer_queries cq  
                                LEFT JOIN users u ON cq.bdo_id=u.id
                                LEFT JOIN customer_query_solutions cqs ON cq.customer_query_id=cqs.customer_query_id AND cq.status='3'
                                WHERE 
                                    ".$dataSearch."
                                GROUP BY cq.customer_query_id
                                ORDER BY
                                    cq.query_datetime DESC
                                )   A,
                                (select @a:=0) initvars";
                                //,
                                    // CONCAT_WS(
                                    //             '\n', 
                                    //             NULLIF(cq.admin_comment , ''),
                                    //             NULLIF(cqs.remark , '')
                                    //         ) AS comment,
                                    // DATE_FORMAT(cqs.created_at, '%Y-%m-%d, %h:%i %p') as closed_date
            //echo $query; exit;
            $product_farmer = DB::select(DB::raw($query));
            $result         = array_map(function ($value) {
                                                return (array)$value;
                                            }, $product_farmer);
            $ticket_column  = array(                
                                    'Id',
                                    'Token Number',
                                    'Farmer Name',
                                    'Mobile',
                                    'State',
                                    'District',
                                    'Tehsil',
                                    'Village',
                                    'Pincode',
                                    'Product',
                                    'Crop',
                                    'Disease',
                                    'Call Type',
                                    'Is Follow Up',
                                    'Remark',
                                    'ECN',
                                    'Query Assigned To',
                                    'Query Created At',
                                    'Status'
                                    );
            //,
                                    // 'Last Remark',
                                    // 'Closed Date'
            array_unshift($result, $ticket_column);
            
            $primarycolor   =   "rgb(255, 0, 0)";
            $myFile         =   Excel::create("filename", function($excel) use($result, $primarycolor) {
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
            $response   = array(
                                'name' => "Customer Query List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                            );
            return response()->json($response);
        }
    } 
        
    public function postBdoList(Request $request) {        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        //print_r($userData); exit;
        if ($userData) {
            $my_data        = Input::all(); 
            $bdo_id         = (isset($my_data['_bdo'])?$my_data['_bdo']:'');  
            $tokennumber    = (isset($my_data['_tokennumber'])?$my_data['_tokennumber']:''); 
            $complaintid    = (isset($my_data['_queryid'])?$my_data['_queryid']:'');           
            $bdo_list       = DB::table('users AS u')
                                ->select('u.id', 'u.ECN', 'u.name', 's.state', 'd.district')
                                ->leftJoin('states AS s', 's.state_id', '=', 'u.state_id')
                                ->leftJoin('districts AS d', 'd.district_id', '=', 'u.district_id')
                                ->where('u.status', '1')
                                ->orderBy('s.state', 'ASC')
                                ->orderBy('d.district', 'ASC')
                                ->orderBy('u.name', 'ASC')
                                ->get();
            if($bdo_list) {
                echo "<h4>Token Number - ".$tokennumber."</h4>";
                echo "<hr>";
                echo "Assigned To : <select name='bdolist' id='bdolist'>";
                $state = '';
                foreach($bdo_list as $bdo) {
                    if($state=='' || $state!=$bdo->state) {
                        if($state!='') {
                            echo '</optgroup>';
                        }
                        $state = $bdo->state;
                        echo '<optgroup label="'.$bdo->state.'">';
                        echo "<option value='".$bdo->id."'>".$bdo->name." (".$bdo->ECN.") ".$bdo->state.", ".$bdo->district."</option>";
                    }
                    else {
                        echo "<option value='".$bdo->id."'>".$bdo->name." (".$bdo->ECN.") ".$bdo->state.", ".$bdo->district."</option>";
                    }
                }
                echo "</select>";
                echo "<input type='hidden' id='queryId' name='queryId' value='".$complaintid."' />";
                echo "<input type='button' id='assignedTo' name='assignedTo' value='submit' />";
            }
            else {
                echo "<h4>No Record Found !</h4>";
            }            
        }
        else {
            echo "<h4>No Record Found !</h4>";
        }
    }
    
    public function postUpdateComplaint(Request $request) {        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data    = Input::all(); 
            $bdo_id     = (isset($my_data['_bdolist'])?$my_data['_bdolist']:'');  
            $queryId    = (isset($my_data['_queryId'])?$my_data['_queryId']:'');  
            $value      = array('bdo_id' => $bdo_id, 'updated_at' => date('Y-m-d H:i:s'));
            $bdo_list   = DB::table('customer_queries')
                            ->where('customer_query_id', $queryId)
                            ->update($value);
            if ($bdo_list) {
                echo 1;
            } else {
                echo 0;
            }       
        }
        else {
            echo 2;
        }
    }
    
    public function postBdoResponse(Request $request) {        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        //print_r($userData); exit;
        if ($userData) {
            $my_data        = Input::all(); 
            $bdo_id         = (isset($my_data['_bdo'])?$my_data['_bdo']:'');  
            $tokennumber    = (isset($my_data['_tokennumber'])?$my_data['_tokennumber']:''); 
            $complaintid    = (isset($my_data['_queryid'])?$my_data['_queryid']:'');           
            $response_list  = DB::table('customer_queries AS cq')
                                ->select('cq.customer_query_id', 'u.ECN', 'u.name', 'cq.token_number', 'cqs.status', 'cqs.product', 'cqs.product_quantity', 'cqs.remark', 'cqs.created_at')
                                ->leftJoin('customer_query_solutions AS cqs', 'cq.customer_query_id', '=', 'cqs.customer_query_id')
                                ->leftJoin('users AS u', 'u.id', '=', 'cqs.user_id')
                                ->where('cqs.customer_query_id', $complaintid)
                                ->orderBy('cqs.created_at', 'DESC')
                                ->get();
            $complaint_data = CustomerQuery::where('customer_query_id', $complaintid)->first();
            
            echo '<table class="table table-striped" role="grid" style="border: 1px solid #f4f4f4;" >
                    <thead>
                        <tr>
                            <th>ECN</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Remark</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>';
            if($response_list) {
                foreach($response_list as $bdo_response) { 
                    echo "<tr>
                            <td>".$bdo_response->ECN."</td>
                            <td>".$bdo_response->name."</td>
                            <td>".($bdo_response->status=='1'?'Active':($bdo_response->status=='2'?'Follow Up':($bdo_response->status=='3'?'Closed':'')))."</td>
                            <td>".$bdo_response->product."</td>
                            <td>".$bdo_response->product_quantity."</td>
                            <td>".$bdo_response->remark."</td>
                            <td>".$bdo_response->created_at."</td>
                        </tr>";
                }
            }
            if($complaint_data->closed_by_admin=='1') {
                echo "<tr>
                        <td>-</td>
                        <td>Admin</td>
                        <td>Closed</td>
                        <td>-</td>
                        <td>-</td>
                        <td>".$complaint_data->admin_comment."</td>
                        <td>".$complaint_data->updated_at."</td>
                    </tr>";
            }
            if($complaint_data->closed_by_admin=='0' && !$response_list) {
                echo "<tr>
                        <td colspan='7'>No Record Found</td>
                    </tr>";
            }
            echo "</tbody></table>";
            
        }
        else {
            echo "<h4>No Record Found !</h4>";
        }
    }
    
    
    
    
    
    public function getSaleQueries() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.customer.salequerylist');
    }
    
    public function postBdoSaleResponse(Request $request) {        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        //print_r($userData); exit;
        if ($userData) {
            $my_data        = Input::all(); 
            $bdo_id         = (isset($my_data['_bdo'])?$my_data['_bdo']:'');  
            $tokennumber    = (isset($my_data['_tokennumber'])?$my_data['_tokennumber']:'');
            $complaintid    = (isset($my_data['_queryid'])?$my_data['_queryid']:'');
            $remark         = (isset($my_data['_remark'])?$my_data['_remark']:'');      
            $response_list  = DB::table('customer_queries AS cq')
                                ->select('cq.customer_query_id', 'u.ECN', 'u.name', 'cq.token_number', 'cqs.status', 'cqs.product', 'cqs.product_quantity', 'cqs.remark', 'cqs.created_at')
                                ->leftJoin('customer_query_solutions AS cqs', 'cq.customer_query_id', '=', 'cqs.customer_query_id')
                                ->leftJoin('users AS u', 'u.id', '=', 'cqs.user_id')
                                ->where('cqs.customer_query_id', $complaintid)
                                ->orderBy('cqs.created_at', 'DESC')
                                ->get();
            $complaint_data = CustomerQuery::where('customer_query_id', $complaintid)->first();
            
            echo '<table class="table table-striped" role="grid" style="border: 1px solid #f4f4f4;" >
                    <thead>
                        <tr>
                            <th>ECN</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Remark</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>';
            if($response_list) {
                foreach($response_list as $bdo_response) { 
                    echo "<tr>
                            <td>".$bdo_response->ECN."</td>
                            <td>".$bdo_response->name."</td>
                            <td>".($bdo_response->status=='3'?'Closed':($bdo_response->status=='2'?'Follow Up':'Active'))."</td>
                            <td>".$bdo_response->product."</td>
                            <td>".$bdo_response->product_quantity."</td>
                            <td>".$bdo_response->remark."</td>
                            <td>".$bdo_response->created_at."</td>
                        </tr>";
                }
            }
            if($complaint_data->closed_by_admin=='1') {
                echo "<tr>
                        <td>-</td>
                        <td>Admin</td>
                        <td>Closed</td>
                        <td>-</td>
                        <td>-</td>
                        <td>".$complaint_data->admin_comment."</td>
                        <td>".$complaint_data->updated_at."</td>
                    </tr>";
            }
            if($complaint_data->closed_by_admin=='0' && !$response_list) {
                echo "<tr>
                        <td colspan='7'>No Record Found</td>
                    </tr>";
            }
            echo "</tbody></table>";
            
        }
        else {
            echo "<h4>No Record Found !</h4>";
        }
    }
    
    public function postQueryClosed(Request $request) {        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data    = Input::all(); 
            $queryId    = (isset($my_data['_queryid'])?$my_data['_queryid']:'');  
            $remark     = (isset($my_data['_remark'])?$my_data['_remark']:''); 
            $value      = array('status' => '3', 'closed_by_admin' => '1', 'admin_comment' => $remark, 'updated_at' => date('Y-m-d H:i:s'));
            
            $bdo_list   = DB::table('customer_queries')
                            ->where('customer_query_id', $queryId)
                            ->update($value);
            if ($bdo_list) {
                echo 1;
            } else {
                echo 0;
            }       
        }
        else {
            echo 2;
        }
    }
}