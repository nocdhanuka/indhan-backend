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
use App\Village;
use App\Product;
use App\Crop;
use App\Ticket;
use App\Farmer;
use App\Language;
use App\CustomerQuery;

use App\ZonalManager;
use App\RegionalManager;
use App\SalesRegionalManager;
use App\AreaManager;
use App\SalesAreaManager;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class atpController extends Controller {
    public function __construct() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAtpList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $dataSourceList = DB::table('data_sources')->select('data_source_id','data_source')->where('status','1')->orderBy('data_source','asc')->get();
        return View::make('backend.atp.atplist',array('dataSourceList'=>$dataSourceList));
    }
    
    public function postAllatplist(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }else{
            $role_sess      = \Session::get('login_role_id');
            //echo $role_sess;die;
            if($role_sess == '8'){
                $userData = \DB::table('branch_admin')->where(['id' => $sess])->first();
                if(count($userData)>0) {
                    $zonal_manager_id = $userData->zonal_manager_id;
                    if($zonal_manager_id != '' && !empty($zonal_manager_id)){
                        $zonalq = " and pa.created_by_user_id = '".$zonal_manager_id."' and pa.user_type = 'zonal'";
                    }
                    else{
                        $zonalq = "";
                    }
                    //rsm
                    $rsmData = DB::table('sales_regional_managers')
                              ->where('zonal_manager_id',$zonal_manager_id)
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
                        $rsmresult1 = implode("','", $rsmresult);
                        if($rsmresult1 != '' && !empty($rsmresult1)){
                            $rsmresultq = " or pa.created_by_user_id in ('".$rsmresult1."') and pa.user_type = 'sales_regional'";
                        }
                        else{
                            $rsmresultq = "";
                        }
                    }

                    //asm
                    $asmData = DB::table('sales_area_managers')
                              ->where('zonal_manager_id',$zonal_manager_id)
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
                        $asmresult1 = implode("','", $asmresult);
                        if($asmresult1 != '' && !empty($asmresult1)){
                            $asmresultq = " or pa.created_by_user_id in ('".$asmresult1."') and pa.user_type = 'sales_area'";
                        }
                        else{
                            $asmresultq = "";
                        }
                    }

                    //regional
                    $rData = DB::table('regional_managers')
                              ->where('zonal_manager_id',$zonal_manager_id)
                              ->select('regional_manager_id')
                              ->get();
                    $rresult = array(); 
                    if (isset($rData) && !empty($rData)) 
                    {
                        $rDatass = json_decode(json_encode($rData),true);
                        foreach ($rDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $rresult = array_merge($rresult, array_flatten($value)); 
                            } 
                            else 
                            { 
                                $rresult[$key] = $value; 
                            } 
                        } 
                        $rresult1 = implode("','", $rresult);
                        if($rresult1 != '' && !empty($rresult1)){
                            $rresultq = " or pa.created_by_user_id in ('".$rresult1."') and pa.user_type = 'regional' ";
                        }
                        else{
                            $rresultq = "";
                        }
                    }

                    //area
                    $aData = DB::table('area_managers')
                              ->where('zonal_manager_id',$zonal_manager_id)
                              ->select('area_manager_id')
                              ->get();
                    $aresult = array(); 
                    if (isset($aData) && !empty($aData)) 
                    {
                        $aDatass = json_decode(json_encode($aData),true);
                        foreach ($aDatass as $key => $value) { 
                            if (is_array($value)) 
                            { 
                                $aresult = array_merge($aresult, array_flatten($value)); 
                            } 
                            else 
                            { 
                                $aresult[$key] = $value; 
                            } 
                        } 
                        $aresult1 = implode("','", $aresult);
                        if($aresult1 != '' && !empty($aresult1)){
                            $aresultq = " or pa.created_by_user_id in ('".$aresult1."') and pa.user_type = 'area' ";
                        }
                        else{
                            $aresultq = "";
                        }
                    }

                    //bdo data
                    $ddData = DB::table('users')
                              ->where('zonal_manager_id',$zonal_manager_id)
                              ->select('id')
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
                        if($ddresult1 != '' && !empty($ddresult1)){
                            $ddresultq = " or pa.created_by_user_id in ('".$ddresult1."') and pa.user_type = 'bdo' ";
                        }
                        else{
                            $ddresultq = "";
                        }
                    }
                }
                else{
                    $zonalq     = "";
                    $rsmresultq = "";
                    $asmresultq = "";
                    $rresultq   = "";
                    $aresultq   = "";
                    $ddresultq  = "";
                }
            }
            else{
                $zonalq     = "";
                $rsmresultq = "";
                $asmresultq = "";
                $rresultq   = "";
                $aresultq   = "";
                $ddresultq  = "";
            }
            
        }

        $value          = Input::all();
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];
        
    
        $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
        $end_date       = isset($value['end_date']) ?  $value['end_date']:'';

        $search_query=" pa.is_deleted <> '1' ";
        $current_date = date('Y-m-d');
        // if($start_date!='' && $end_date!='') {
        //     $search_query.= " and DATE(pa.plan_date) >= DATE('".$start_date."') AND DATE(pa.plan_date) <= DATE('".$end_date."')";
        // }elseif($start_date!='') {
        //     $search_query.= " and DATE(pa.plan_date) >= DATE('".$start_date."')";
        // }
        if($role_sess == '8'){
            if($start_date!='' && $end_date!='' && $start_date != $end_date) {
                $search_query.= $zonalq." and DATE(pa.plan_date) >= DATE('".$start_date."') AND DATE(pa.plan_date) <= DATE('".$end_date."')".$rsmresultq." and DATE(pa.plan_date) >= DATE('".$start_date."') AND DATE(pa.plan_date) <= DATE('".$end_date."') and pa.is_deleted <> '1' ".$asmresultq." and DATE(pa.plan_date) >= DATE('".$start_date."') AND DATE(pa.plan_date) <= DATE('".$end_date."') and pa.is_deleted <> '1' ".$rresultq." and DATE(pa.plan_date) >= DATE('".$start_date."') AND DATE(pa.plan_date) <= DATE('".$end_date."') and pa.is_deleted <> '1' ".$aresultq." and DATE(pa.plan_date) >= DATE('".$start_date."') AND DATE(pa.plan_date) <= DATE('".$end_date."') and pa.is_deleted <> '1'".$ddresultq." and DATE(pa.plan_date) >= DATE('".$start_date."') AND DATE(pa.plan_date) <= DATE('".$end_date."') and pa.is_deleted <> '1'";
            }elseif($start_date!='' && $end_date!='' && $start_date == $end_date) {
                $search_query.= $zonalq." and DATE(pa.plan_date) >= DATE('".$start_date."') ".$rsmresultq." and DATE(pa.plan_date) >= DATE('".$start_date."') and pa.is_deleted <> '1' ".$asmresultq." and DATE(pa.plan_date) >= DATE('".$start_date."') and pa.is_deleted <> '1' ".$rresultq." and DATE(pa.plan_date) >= DATE('".$start_date."') and pa.is_deleted <> '1' ".$aresultq." and DATE(pa.plan_date) >= DATE('".$start_date."') and pa.is_deleted <> '1'".$ddresultq." and DATE(pa.plan_date) >= DATE('".$start_date."') and pa.is_deleted <> '1'";
            }
            else if($start_date == ''){
                $search_query.= $zonalq." ".$rsmresultq." and pa.is_deleted <> '1' ".$asmresultq." and pa.is_deleted <> '1' ".$rresultq." and pa.is_deleted <> '1' ".$aresultq." and pa.is_deleted <> '1' ".$ddresultq." and pa.is_deleted <> '1' ";
            }
        }else{
            if($start_date!='' && $end_date!='' && $start_date != $end_date) {
                $search_query.= " and DATE(pa.plan_date) >= DATE('".$start_date."') AND DATE(pa.plan_date) <= DATE('".$end_date."')";
            }elseif($start_date!='' && $end_date!='' && $start_date == $end_date) {
                $search_query.= " and DATE(pa.plan_date) >= DATE('".$start_date."') ";
            }
        }
        if(!empty($value['meetingType'])) {
            $meetingType = $value['meetingType'];
            $search_query .= "AND pa.data_source_id = '".$meetingType."'";
        }
        
        //echo $search_query;die;
        if(isset($search_array['value']) && $search_array['value']!=''){
            $search = $search_array['value']; 

            $search_query.= "  AND
            (                        
            u.name              LIKE '%".$search."%'    OR        
            p.product           LIKE '%".$search."%'    OR
            c.crop              LIKE '%".$search."%'    OR
            ds.data_source      LIKE '%".$search."%'    OR
            s.state             LIKE '%".$search."%'    OR
            d.district          LIKE '%".$search."%'    OR
            t.tehsil            LIKE '%".$search."%'    OR
            tr.territory        LIKE '%".$search."%'    OR 
            pa.village          LIKE '%".$search."%'    OR
            pa.reason           LIKE '%".$search."%'    OR
            pa.plan_atp_id      LIKE '%".$search."%'
            )";
        }
                // AND DATE_FORMAT(ut.created_at, '%Y-%m-%d') <= ".$current_date." 
                // (CASE WHEN ut.ticket_status = '1' THEN ut.ticket_no ELSE '' END) 
                $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    u.name,
                                    u.ECN,
                                    DATE_FORMAT(pa.plan_date, '%Y-%m-%d') as plan_date,
                                    p.product,
                                    p1.product as product1,
                                    p2.product as product2,
                                    c.crop,
                                    ds.data_source,
                                    s.state,
                                    d.district,
                                    t.tehsil,
                                    pa.village,
                                    pa.expected_farmer,
                                    pa.expected_pda,
                                    IF( pa.reason = '', '', u.name ) as reason_by,
                                    IF( pa.reason = '', '', pa.user_type ) as reason_by_type,
                                    pa.reason,
                                    DATE_FORMAT(pa.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    ut.ticket_no,
                                    pa.plan_atp_id,
                                    IF(pa.dd_id = 0, '', dd.name) as ddname,
                                    IF(pa.dd_id = 0, '', dd.ECN) as ddecn,
                                    IF(pa.forwhom = 1, '', cp.name) as cpname,
                                    IF(pa.forwhom = 1, '', cp.ECN) as cpecn,
                                    pa.data_source_id,
                                    IF(pa.plan_date < curdate() , 'N' , 'Y' ) as editable,
                                    pa.presenter_name,
                                    pa.monitor_name,
                                    tr.territory
                                FROM
                                    plan_atp pa  
                                LEFT JOIN users u ON pa.created_by_user_id=u.id
                                LEFT JOIN dhanuka_doctor dd ON pa.dd_id=dd.dd_id
                                LEFT JOIN users cp ON u.counterpart_fl=cp.id
                                LEFT JOIN products p ON pa.product_id=p.product_id
                                LEFT JOIN products p1 ON pa.product_id_2=p1.product_id
                                LEFT JOIN products p2 ON pa.product_id_3=p2.product_id
                                LEFT JOIN crops c ON pa.crop_id=c.crop_id
                                LEFT JOIN data_sources ds ON pa.data_source_id=ds.data_source_id
                                LEFT JOIN states s ON pa.state_id=s.state_id
                                LEFT JOIN districts d ON pa.district_id=d.district_id
                                LEFT JOIN tehsils t ON pa.tehsil_id=t.tehsil_id
                                LEFT JOIN territories tr ON pa.territory_id=tr.territory_id
                                LEFT JOIN tickets ut ON pa.plan_atp_id=ut.plan_atp_id and ut.ticket_status != '0' 
                                WHERE ".$search_query."
                                
                                ORDER BY 
                                    pa.plan_date DESC,
                                    u.name DESC,
                                    p.product DESC,
                                    c.crop DESC,
                                    s.state DESC,
                                    d.district DESC,
                                    t.tehsil DESC,
                                    pa.village DESC
                                )   A,
                                (select @a:=0) initvars
                                ";
                                // return $query;
                               
            $districts   = collect(DB::select($query));  

            return Datatables::of($districts)->make();  
    }
    
    public function postAtpDelete(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            '_planid'    => 'required|numeric'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                echo '0';
            }
            else {
                $atp_id             = $my_data['_planid'];
                $chk_ticket         = Ticket::where('plan_atp_id', $atp_id)->count();
                $chk_participation  = \App\SelfParticipationPlan::where('plan_atp_id', $atp_id)->count();
                //dd($chk_participation);
                if($chk_participation==0 && $chk_ticket==0) {
                    $update_atp = DB::table('plan_atp')
                                    ->where('plan_atp_id', $atp_id)
                                    ->update(['is_deleted' => '1', 'updated_at' => date('Y-m-d H:i:s')]);
                    if ($update_atp) {
                        echo 1;
                    } else {
                        echo 2;
                    }
                }
                else {
                    echo 3;
                }
                
            }
        }
    } 
    
      
    public function postExportAllAtp(Request $request) {          
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data    =   Input::all(); 
            $search     =   $my_data['search'];
            $start_date     = isset($my_data['startdate']) ? $my_data['startdate'] :'';
            $end_date       = isset($my_data['enddate']) ?  $my_data['enddate']:'';
            $dataSearch = "  pa.is_deleted<>'1' ";

            if($search!='') {
                $dataSearch.=   "
                                AND
                                (
                                    u.name          LIKE '%".$search."%'    OR 
                                    p.product       LIKE '%".$search."%'    OR 
                                    c.crop          LIKE '%".$search."%'    OR 
                                    ds.data_source  LIKE '%".$search."%'    OR 
                                    s.state         LIKE '%".$search."%'    OR 
                                    d.district      LIKE '%".$search."%'    OR 
                                    t.tehsil        LIKE '%".$search."%'    OR 
                                    tr.territory    LIKE '%".$search."%'    OR 
                                    pa.village      LIKE '%".$search."%'
                                )             
                                ";
            }
             

            if($start_date!='' && $end_date!='') {
                $dataSearch.= " and DATE(pa.plan_date) >= DATE('".$start_date."') AND DATE(pa.plan_date) <= DATE('".$end_date."')";
            }elseif($start_date!='') {
                $dataSearch.= " and DATE(pa.plan_date) >= DATE('".$start_date."')";
            }
        
            $query      =   " 
                            SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*                                
                        FROM
                            (
                            SELECT        
                                u.name,
                                u.ECN,
                                IF(pa.dd_id = 0, 'NULL', dd.name) as ddname,
                                IF(pa.dd_id = 0, 'NULL', dd.ECN) as ddecn,
                                IF(pa.forwhom = 1, '', cp.name) as cpname,
                                IF(pa.forwhom = 1, '', cp.ECN) as cpecn,
                                DATE_FORMAT(pa.plan_date, '%Y-%m-%d') as plan_date,
                                p.product,
                                p1.product as product1,
                                p2.product as product2,
                                c.crop,
                                ds.data_source,
                                s.state,
                                d.district,
                                t.tehsil,
                                tr.territory,
                                pa.village,
                                pa.expected_farmer,
                                pa.expected_pda,
                                pa.reason,
                                DATE_FORMAT(pa.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                ut.ticket_no as generated_ticket,
                                pa.plan_atp_id,
                                pa.presenter_name,
                                pa.monitor_name
                            FROM        
                                plan_atp pa  
                            LEFT JOIN users u ON pa.created_by_user_id=u.id
                            LEFT JOIN dhanuka_doctor dd ON pa.dd_id=dd.dd_id
                            LEFT JOIN users cp ON u.counterpart_fl=cp.id
                            LEFT JOIN products p ON pa.product_id=p.product_id
                            LEFT JOIN products p1 ON pa.product_id_2=p1.product_id
                            LEFT JOIN products p2 ON pa.product_id_3=p2.product_id
                            LEFT JOIN crops c ON pa.crop_id=c.crop_id
                            LEFT JOIN data_sources ds ON pa.data_source_id=ds.data_source_id
                            LEFT JOIN states s ON pa.state_id=s.state_id
                            LEFT JOIN districts d ON pa.district_id=d.district_id
                            LEFT JOIN tehsils t ON pa.tehsil_id=t.tehsil_id
                            LEFT JOIN territories tr ON pa.territory_id=tr.territory_id
                            LEFT JOIN tickets ut ON pa.plan_atp_id=ut.plan_atp_id and ut.ticket_status != '0' 
                            WHERE  ".$dataSearch."
                            ORDER BY 
                                pa.plan_date DESC,
                                u.name DESC,
                                p.product DESC,
                                c.crop DESC,
                                s.state DESC,
                                d.district DESC,
                                t.tehsil DESC,
                                pa.village DESC
                            )   A,
                            (select @a:=0) initvars";
            //echo $query; exit;
        
            $product_farmer = DB::select(DB::raw($query));
            $result         = array_map(function ($value) {
                                                return (array)$value;
                                            }, $product_farmer);
            $ticket_column  = array(                
                                    'Id',
                                    'BDO Name',
                                    'ECN',
                                    'Assigned DD',
                                    'Assigned DD ECN',
                                    'Counterpart FL',
                                    'Counterpart FL ECN',
                                    'Plan Date',
                                    'Product 1',
                                    'Product 2',
                                    'Product 3',
                                    'Crop',
                                    'Data Source',
                                    'State',
                                    'District',
                                    'Tehsil',
                                    'Territory',
                                    'Village',
                                    'Expected Farmer',
                                    'Expected PDA',
                                    'Reason',
                                    'Created At',
                                    'Generated Ticket',
                                    'ATP No',
                                    'Presenter Name',
                                    'Monitor Name'
                                );
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
                                'name' => "BDO Plan List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                            );
            return response()->json($response);
        }
    } 

    public function getEditatp($plan_id) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $planData = DB::table('plan_atp')->where('plan_atp_id',$plan_id)->get();
        $languageList =DB::table('languages')->select('id','language')->where('status','1')->orderBy('language','asc')->get();
        $dataSourceList = DB::table('data_sources')->select('data_source_id','data_source')->where('status','1')->orderBy('data_source','asc')->get();
        $cropList = DB::table('crops')->select('crop_id','crop')->where('status','1')->orderBy('crop','asc')->get();
        $productList = DB::table('products')->select('product_id','product')->where('status','1')->orderBy('product','asc')->get();
        $state    = State::get();
        $district = District::where('state_id', $planData[0]->state_id)->where('status', '1')->get();
        $tehsil   = Tehsil::where('district_id', $planData[0]->district_id)->where('status', '1')->get();
        $village  = Village::where('tehsil_id', $planData[0]->tehsil_id)->where('status', '1')->get();

        $created_by_user_id = $planData[0]->created_by_user_id;
        $user_type = $planData[0]->user_type;
        $get_parent_user = $this->getPresneterMonitor($created_by_user_id,$user_type);
        return View::make('backend.atp.editatp', array('dataSourceList'=>$dataSourceList, 'cropList'=>$cropList, 'productList'=>$productList, 'languageList'=>$languageList, 'planData'=>$planData[0], 'state'=> $state, 'district'=>$district, 'tehsil'=>$tehsil, 'village'=> $village,'parent_user' => $get_parent_user));
    }

    public function postUpdateatp(Request $request){
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data      = Input::all();
            // echo "<pre>"; print_r($my_data); exit();

            $rules  = array(
                'plan_atp_id'    => 'required|numeric',
                'presenter_id'   => 'required|numeric',
                'monitor_id'     => 'required|numeric',
                'presenter_type' => 'required',
                'monitor_type'   => 'required',
                'meeting_date'   => 'required',
                'meeting_detail' => 'required'
            );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors('Validation Failed.',$validator);
            } else {
                $plan_atp_id     = $my_data['plan_atp_id'];
                $presenter_id    = $my_data['presenter_id'];
                $presenter_type  = $my_data['presenter_type'];
                $presenter_name  = $my_data['presenter_name'];
                $monitor_id      = $my_data['monitor_id'];
                $monitor_type    = $my_data['monitor_type'];
                $monitor_name    = $my_data['monitor_name'];
                $meeting         = $my_data['meeting_date'];
                $meeting_detail  = $my_data['meeting_detail'];

                $expmeeting      = explode(":", $meeting);
                if(count($expmeeting)==3) {
                    $meeting_date    = $meeting;
                } else {
                    $meeting_date    = $meeting.":00";
                }

                if ($plan_atp_id != 0) {
                    $update_plan = DB::table('plan_atp')
                                     ->where('plan_atp_id', $plan_atp_id)
                                     ->update(['presenter_id'=>$presenter_id,'presenter_type'=>$presenter_type, 'presenter_name' => $presenter_name, 'monitor_id'=>$monitor_id,'monitor_type'=>$monitor_type, 'monitor_name'=> $monitor_name, 'meeting_date'=>$meeting_date,'meeting_detail'=>$meeting_detail]);
                    if ($update_plan) {
                        Session::flash('message', "ATP Successfully Updated.");
                        return redirect('atp/editatp/'.$plan_atp_id);
                    } else {
                        Session::flash('message', "Please try again.");
                        return redirect('atp/editatp/'.$plan_atp_id);
                    }
                } else {
                    Session::flash('message', "Please try again.");
                    return redirect('atp/atp-list');
                }
            }
        }
    }

    public function getPresneterMonitor($user_id, $user_type)
    {
        $result=array();
        $userType = array('zonal','regional','sales_regional','area','sales_area','bdo','dhanuka_doctor');
        if(in_array($user_type, $userType)) {
            $result=array();
            if($user_type == "zonal") {
                $table = 'sales_regional_managers';
                $key_id = 'zonal_manager_id';
                $srm_id = $this->getSalesRegionalManagerId($table,$key_id,$user_id);
                $query = " 
                SELECT zonal_manager_id as id, zonal_manager_name as name, ECN, 'zonal' as user_type FROM zonal_managers WHERE zonal_manager_id='".$user_id."' AND status='1'
                union all
                SELECT regional_manager_id as id, regional_manager_name as name, ECN, 'regional' as user_type FROM regional_managers WHERE zonal_manager_id='".$user_id."' AND status='1'
                union all
                SELECT sales_regional_manager_id as id, sales_regional_manager_name as name, ECN, 'sales_regional' as user_type FROM sales_regional_managers WHERE zonal_manager_id='".$user_id."' AND status='1'
                union all
                SELECT ms.marketing_supervisor_id as id, ms.marketing_supervisor_name as name, ms.ECN, 'marketing_supervisor' as user_type FROM marketing_supervisor as ms JOIN marketing_supervisor_rsm as msr ON ms.marketing_supervisor_id= msr.marketing_supervisor_id AND msr.sales_regional_manager_id='".$srm_id."' AND ms.status='1'
                union all
                SELECT area_manager_id as id, area_manager_name as name, ECN, 'area' as user_type FROM area_managers WHERE zonal_manager_id='".$user_id."' AND status='1'
                union all
                SELECT sales_area_manager_id as id, sales_area_manager_name as name, ECN, 'sales_area' as user_type FROM sales_area_managers WHERE zonal_manager_id='".$user_id."' AND status='1'
                union all
                SELECT id, name, ECN, 'bdo' as user_type FROM users WHERE zonal_manager_id='".$user_id."' AND status='1'
                union all
                SELECT dd_id as id, name, ECN, 'dhanuka_doctor' as user_type FROM dhanuka_doctor WHERE zonal_manager_id='".$user_id."' AND status='1'";
            } else if($user_type == "regional") {
                $query = " 
                SELECT z.zonal_manager_id as id, z.zonal_manager_name as name,z.ECN as ECN, 'zonal' as user_type FROM zonal_managers z INNER JOIN regional_managers r ON r.zonal_manager_id=z.zonal_manager_id AND r.regional_manager_id='".$user_id."' AND z.status='1'
                union all
                SELECT regional_manager_id as id, regional_manager_name as name, ECN, 'regional' as user_type FROM regional_managers WHERE regional_manager_id='".$user_id."' AND status='1'
                union all
                SELECT area_manager_id as id, area_manager_name as name, ECN, 'area' as user_type FROM area_managers WHERE regional_manager_id='".$user_id."' AND status='1'
                union all
                SELECT sales_area_manager_id as id, sales_area_manager_name as name, ECN, 'sales_area' as user_type FROM sales_area_managers WHERE regional_manager_id='".$user_id."' AND status='1'
                union all
                SELECT id, name, ECN, 'bdo' as user_type FROM users WHERE regional_manager_id='".$user_id."' AND status='1'
                union all
                SELECT dd_id as id, name, ECN, 'dhanuka_doctor' as user_type FROM dhanuka_doctor WHERE regional_manager_id='".$user_id."' AND status='1'";
            } else if($user_type == "sales_regional") {
                $srm_id = $user_id;
                $query = " 
                SELECT z.zonal_manager_id as id, z.zonal_manager_name as name,z.ECN as ECN, 'zonal' as user_type FROM zonal_managers z INNER JOIN sales_regional_managers sr ON sr.zonal_manager_id=z.zonal_manager_id AND sr.sales_regional_manager_id='".$user_id."' AND z.status='1'
                union all
                SELECT sales_regional_manager_id as id, sales_regional_manager_name as name, ECN, 'sales_regional' as user_type FROM sales_regional_managers WHERE sales_regional_manager_id='".$user_id."' AND status='1'
                union all
                SELECT ms.marketing_supervisor_id as id, ms.marketing_supervisor_name as name, ms.ECN, 'marketing_supervisor' as user_type FROM marketing_supervisor as ms JOIN marketing_supervisor_rsm as msr ON ms.marketing_supervisor_id= msr.marketing_supervisor_id AND msr.sales_regional_manager_id='".$srm_id."' AND ms.status='1'
                union all
                SELECT area_manager_id as id, area_manager_name as name, ECN, 'area' as user_type FROM area_managers WHERE sales_regional_manager_id='".$user_id."' AND status='1'
                union all
                SELECT sales_area_manager_id as id, sales_area_manager_name as name, ECN, 'sales_area' as user_type FROM sales_area_managers WHERE sales_regional_manager_id='".$user_id."' AND status='1'
                union all
                SELECT id, name, ECN, 'bdo' as user_type FROM users WHERE sales_regional_manager_id='".$user_id."' AND status='1'
                union all
                SELECT dd_id as id, name, ECN, 'dhanuka_doctor' as user_type FROM dhanuka_doctor WHERE sales_regional_manager_id='".$user_id."' AND status='1'";
            } else if($user_type == "area") {
                $table = 'area_managers';
                $key_id = 'area_manager_id';
                $srm_id = $this->getSalesRegionalManagerId($table,$key_id,$user_id);
                $query = " 
                SELECT z.zonal_manager_id as id, z.zonal_manager_name as name,z.ECN as ECN, 'zonal' as user_type FROM zonal_managers z INNER JOIN area_managers a ON a.zonal_manager_id=z.zonal_manager_id AND a.area_manager_id='".$user_id."' AND z.status='1'
                union all
                SELECT r.regional_manager_id as id, r.regional_manager_name as name, r.ECN as ECN, 'regional' as user_type FROM regional_managers r INNER JOIN area_managers a ON a.regional_manager_id=r.regional_manager_id AND a.area_manager_id='".$user_id."' AND r.status='1'
                union all
                SELECT sr.sales_regional_manager_id as id, sr.sales_regional_manager_name as name, sr.ECN as ECN, 'sales_regional' as user_type FROM sales_regional_managers sr INNER JOIN area_managers a ON a.sales_regional_manager_id=sr.sales_regional_manager_id AND a.area_manager_id='".$user_id."' AND sr.status='1'
                union all
                SELECT ms.marketing_supervisor_id as id, ms.marketing_supervisor_name as name, ms.ECN, 'marketing_supervisor' as user_type FROM marketing_supervisor as ms JOIN marketing_supervisor_rsm as msr ON ms.marketing_supervisor_id= msr.marketing_supervisor_id AND msr.sales_regional_manager_id='".$srm_id."' AND ms.status='1'
                union all
                SELECT area_manager_id as id, area_manager_name as name, ECN, 'area' as user_type FROM area_managers WHERE area_manager_id='".$user_id."' AND status='1'
                union all
                SELECT id, name, ECN, 'bdo' as user_type FROM users WHERE area_manager_id='".$user_id."' AND status='1'
                union all
                SELECT dd_id as id, name, ECN, 'dhanuka_doctor' as user_type FROM dhanuka_doctor WHERE area_manager_id='".$user_id."' AND status='1'";
            } else if($user_type == "sales_area") {
                $table = 'sales_area_managers';
                $key_id = 'sales_area_manager_id';
                $srm_id = $this->getSalesRegionalManagerId($table,$key_id,$user_id);
                $query = " 
                SELECT z.zonal_manager_id as id, z.zonal_manager_name as name,z.ECN as ECN, 'zonal' as user_type FROM zonal_managers z INNER JOIN sales_area_managers sa ON sa.zonal_manager_id=z.zonal_manager_id AND sa.sales_area_manager_id='".$user_id."' AND z.status='1'
                union all
                SELECT r.regional_manager_id as id, r.regional_manager_name as name, r.ECN as ECN, 'regional' as user_type FROM regional_managers r INNER JOIN sales_area_managers sa ON sa.regional_manager_id=r.regional_manager_id AND sa.sales_area_manager_id='".$user_id."' AND r.status='1'
                union all
                SELECT sr.sales_regional_manager_id as id, sr.sales_regional_manager_name as name, sr.ECN as ECN, 'sales_regional' as user_type FROM sales_regional_managers sr INNER JOIN sales_area_managers sa ON sa.sales_regional_manager_id=sr.sales_regional_manager_id AND sa.sales_area_manager_id='".$user_id."' AND sa.status='1'
                union all
                SELECT ms.marketing_supervisor_id as id, ms.marketing_supervisor_name as name, ms.ECN, 'marketing_supervisor' as user_type FROM marketing_supervisor as ms JOIN marketing_supervisor_rsm as msr ON ms.marketing_supervisor_id= msr.marketing_supervisor_id AND msr.sales_regional_manager_id='".$srm_id."' AND ms.status='1'
                union all
                SELECT sales_area_manager_id as id, sales_area_manager_name as name, ECN, 'sales_area' as user_type FROM sales_area_managers WHERE sales_area_manager_id='".$user_id."' AND status='1'
                union all
                SELECT id, name, ECN, 'bdo' as user_type FROM users WHERE sales_area_manager_id='".$user_id."' AND status='1'
                union all
                SELECT dd_id as id, name, ECN, 'dhanuka_doctor' as user_type FROM dhanuka_doctor WHERE sales_area_manager_id='".$user_id."' AND status='1'";
            } else if($user_type == "bdo") {
                $table = 'users';
                $key_id = 'id';
                $srm_id = $this->getSalesRegionalManagerId($table,$key_id,$user_id);
                $query = " 
                SELECT z.zonal_manager_id as id, z.zonal_manager_name as name,z.ECN as ECN, 'zonal' as user_type FROM zonal_managers z INNER JOIN users u ON u.zonal_manager_id=z.zonal_manager_id AND u.id='".$user_id."' AND z.status='1'
                union all
                SELECT r.regional_manager_id as id, r.regional_manager_name as name, r.ECN as ECN, 'regional' as user_type FROM regional_managers r INNER JOIN users u ON u.regional_manager_id=r.regional_manager_id AND u.id='".$user_id."' AND r.status='1'
                union all
                SELECT sr.sales_regional_manager_id as id, sr.sales_regional_manager_name as name, sr.ECN as ECN, 'sales_regional' as user_type FROM sales_regional_managers sr INNER JOIN users u ON u.sales_regional_manager_id=sr.sales_regional_manager_id AND u.id='".$user_id."' AND sr.status='1'
                union all
                SELECT ms.marketing_supervisor_id as id, ms.marketing_supervisor_name as name, ms.ECN, 'marketing_supervisor' as user_type FROM marketing_supervisor as ms JOIN marketing_supervisor_rsm as msr ON ms.marketing_supervisor_id= msr.marketing_supervisor_id AND msr.sales_regional_manager_id='".$srm_id."' AND ms.status='1'
                union all
                SELECT sa.sales_area_manager_id as id, sa.sales_area_manager_name as name, sa.ECN as ECN, 'sales_area' as user_type FROM sales_area_managers sa INNER JOIN users u ON u.sales_area_manager_id=sa.sales_area_manager_id AND u.id='".$user_id."' AND sa.status='1'
                union all
                SELECT a.area_manager_id as id, a.area_manager_name as name, a.ECN as ECN, 'area' as user_type FROM area_managers a INNER JOIN users u ON u.area_manager_id=a.area_manager_id AND u.id='".$user_id."' AND a.status='1'
                union all
                SELECT id, name, ECN, 'bdo' as user_type FROM users WHERE id='".$user_id."' AND status='1'
                union all
                SELECT dd_id as id, name, ECN, 'dhanuka_doctor' as user_type FROM dhanuka_doctor WHERE bdo_id='".$user_id."' AND status='1'";
            } else {
                $table = 'dhanuka_doctor';
                $key_id = 'dd_id';
                $srm_id = $this->getSalesRegionalManagerId($table,$key_id,$user_id);
                $query = " 
                SELECT z.zonal_manager_id as id, z.zonal_manager_name as name,z.ECN as ECN, 'zonal' as user_type FROM zonal_managers z INNER JOIN dhanuka_doctor dd ON dd.zonal_manager_id=z.zonal_manager_id AND dd.dd_id='".$user_id."' AND z.status='1'
                union all
                SELECT r.regional_manager_id as id, r.regional_manager_name as name, r.ECN as ECN, 'regional' as user_type FROM regional_managers r INNER JOIN dhanuka_doctor dd ON dd.regional_manager_id=r.regional_manager_id AND dd.dd_id='".$user_id."' AND r.status='1'
                union all
                SELECT sr.sales_regional_manager_id as id, sr.sales_regional_manager_name as name, sr.ECN as ECN, 'sales_regional' as user_type FROM sales_regional_managers sr INNER JOIN dhanuka_doctor dd ON dd.sales_regional_manager_id=sr.sales_regional_manager_id AND dd.dd_id='".$user_id."' AND sr.status='1'
                union all
                SELECT ms.marketing_supervisor_id as id, ms.marketing_supervisor_name as name, ms.ECN, 'marketing_supervisor' as user_type FROM marketing_supervisor as ms JOIN marketing_supervisor_rsm as msr ON ms.marketing_supervisor_id= msr.marketing_supervisor_id AND msr.sales_regional_manager_id='".$srm_id."' AND ms.status='1'
                union all
                SELECT sa.sales_area_manager_id as id, sa.sales_area_manager_name as name, sa.ECN as ECN, 'sales_area' as user_type FROM sales_area_managers sa INNER JOIN dhanuka_doctor dd ON dd.sales_area_manager_id=sa.sales_area_manager_id AND dd.dd_id='".$user_id."' AND sa.status='1'
                union all
                SELECT a.area_manager_id as id, a.area_manager_name as name, a.ECN as ECN, 'area' as user_type FROM area_managers a INNER JOIN dhanuka_doctor dd ON dd.area_manager_id=a.area_manager_id AND dd.dd_id='".$user_id."' AND a.status='1'
                union all
                SELECT u.id, u.name, u.ECN, 'bdo' as user_type FROM users u INNER JOIN dhanuka_doctor dd ON dd.bdo_id=u.id AND dd.dd_id='".$user_id."' AND u.status='1'
                union all
                SELECT dd_id as id, name, ECN, 'dhanuka_doctor' as user_type FROM dhanuka_doctor WHERE dd_id='".$user_id."' AND status='1'";
            }
            $user = DB::select(DB::raw($query));
            return $user;
        }
        return $result;
    }

    public function getSalesRegionalManagerId($table,$key_id,$user_id) {
        $query = "SELECT sales_regional_manager_id FROM $table WHERE $key_id=$user_id AND status=1";
        $user = DB::select(DB::raw($query));
        if(count($user)>0) {
            $srm_id = $user[0]->sales_regional_manager_id;
        } else {
            $srm_id = 0;
        }
        return $srm_id;
    }

}