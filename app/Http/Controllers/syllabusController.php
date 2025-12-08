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

class syllabusController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;

        // VIEW 
        // CREATE VIEW ATP_COUNT AS 
        // SELECT count(plan_atp_id) ATP_COUNT, created_by_user_id, product_id, crop_id, MONTH(plan_date) AS month, YEAR(plan_date) AS year, plan_date
        // FROM plan_atp
        // GROUP BY 
        // created_by_user_id,
        // product_id,
        // crop_id,
        // MONTH(plan_date),
        // YEAR(plan_date)
        // ORDER BY created_by_user_id ASC, plan_date ASC, product_id ASC, MONTH(plan_date) ASC, YEAR(plan_date) ASC, plan_date ASC

        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getSyllabusList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        
        $query              = " 
                                SELECT 
                                    am.area_manager_id,
                                    am.area_manager_name,
                                    am.ECN
                                FROM        
                                    plan_activities pa
                                LEFT JOIN area_managers am ON pa.created_by_user_id=am.area_manager_id AND pa.user_type='area'
                                GROUP BY am.area_manager_id
                                ORDER BY am.area_manager_name ASC 
                                " ;
        
        $area_manager_list  = DB::select(DB::raw($query));
        return View::make('backend.syllabus.syllabuslist', array('area_manager_list' => $area_manager_list));
    }
    
    public function postAllsyllabuslist(Request $request) {
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
        
        
        $area_manager   = $value['area_manager'];
        $bdo            = $value['bdo'];
        $syllabus_year  = $value['syllabus_year'];
        $syllabus_month = $value['syllabus_month'];
        
        if(empty($search_array['value']))
        { 
            $sorting        ='';            
            $datesearch     = "pa.is_deleted<>'1'";
            if($area_manager!='') {
                $datesearch .= " AND pa.created_by_user_id = '".$area_manager."'";
            }
            if($bdo!='') {
                $datesearch .= " AND pa.user_id = '".$bdo."'";
            }
            if($syllabus_year!='') {
                $datesearch .= " AND pa.year = '".$syllabus_year."'";
            }
            if($syllabus_month!='') {
                $datesearch .= " AND pa.month = '".$syllabus_month."'";
            }
            $query      = " 
                        SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*                                
                        FROM
                            (

                            SELECT        
                                am.area_manager_name,
                                u.name,
                                pa.month,
                                pa.year,
                                p.product,
                                c.crop,
                                pa.days,
                                pa.dd,
                                DATE_FORMAT(pa.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                AC.ATP_COUNT AS atp,
                                pa.plan_activity_id
                            FROM        
                                plan_activities     pa  
                            LEFT JOIN users         u ON pa.user_id=u.id
                            LEFT JOIN area_managers am ON pa.created_by_user_id=am.area_manager_id AND pa.user_type='area'
                            LEFT JOIN products      p ON pa.product_id=p.product_id
                            LEFT JOIN crops         c ON pa.crop_id=c.crop_id
                            LEFT JOIN ATP_COUNT     AC ON AC.product_id=pa.product_id AND AC.crop_id=pa.crop_id AND AC.month=pa.month AND AC.year=pa.year AND AC.created_by_user_id=pa.user_id
                            WHERE 
                                ".$datesearch."
                            ORDER BY
                                pa.created_at DESC,
                                am.area_manager_name ASC,
                                u.name ASC,
                                p.product ASC,
                                c.crop ASC
                            )   A,
                            (select @a:=0) initvars";
            $districts   = collect(DB::select($query));  
            return Datatables::of($districts)->make();
        }
        else { 
            $search         = $search_array['value']; 
            $sorting        = '';
            $datesearch     = "pa.is_deleted<>'1'";
            if($area_manager!='') {
                $datesearch .= " AND pa.created_by_user_id = '".$area_manager."'";
            }
            if($bdo!='') {
                $datesearch .= " AND pa.user_id = '".$bdo."'";
            }
            if($syllabus_year!='') {
                $datesearch .= " AND pa.year = '".$syllabus_year."'";
            }
            if($syllabus_month!='') {
                $datesearch .= " AND pa.month = '".$syllabus_month."'";
            }                   
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    am.area_manager_name,
                                    u.name,
                                    pa.month,
                                    pa.year,
                                    p.product,
                                    c.crop,
                                    pa.days,
                                    pa.dd,
                                    DATE_FORMAT(pa.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    AC.ATP_COUNT AS atp,
                                    pa.plan_activity_id
                                FROM        
                                    plan_activities     pa  
                                LEFT JOIN users         u ON pa.user_id=u.id
                                LEFT JOIN area_managers am ON pa.created_by_user_id=am.area_manager_id AND pa.user_type='area'
                                LEFT JOIN products      p ON pa.product_id=p.product_id
                                LEFT JOIN crops         c ON pa.crop_id=c.crop_id
                                LEFT JOIN ATP_COUNT     AC ON AC.product_id=pa.product_id AND AC.crop_id=pa.crop_id AND AC.month=pa.month AND AC.year=pa.year AND AC.created_by_user_id=pa.user_id
                                WHERE 
                                    ".$datesearch."
                                ORDER BY
                                    pa.created_at DESC,
                                    am.area_manager_name ASC,
                                    u.name ASC,
                                    p.product ASC,
                                    c.crop ASC
                                )   A,
                                (select @a:=0) initvars";
            $districts   = collect(DB::select($query));  
            return Datatables::of($districts)->make();            
        }
    }
    
    public function postExportAllSyllabus(Request $request) {          
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data        =   Input::all(); 
            $area_manager   = $my_data['area_manager'];
            $bdo            = $my_data['bdo'];
            $syllabus_year  = $my_data['syllabus_year'];
            $syllabus_month = $my_data['syllabus_month'];
            
            $datesearch     = "pa.is_deleted<>'1'";
            if($area_manager!='') {
                $datesearch .= " AND pa.created_by_user_id = '".$area_manager."'";
            }
            if($bdo!='') {
                $datesearch .= " AND pa.user_id = '".$bdo."'";
            }
            if($syllabus_year!='') {
                $datesearch .= " AND pa.year = '".$syllabus_year."'";
            }
            if($syllabus_month!='') {
                $datesearch .= " AND pa.month = '".$syllabus_month."'";
            } 
                        
            $query      =   " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    am.area_manager_name,
                                    u.name,
                                    pa.month,
                                    pa.year,
                                    p.product,
                                    c.crop,
                                    pa.days,
                                    pa.dd,
                                    DATE_FORMAT(pa.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    AC.ATP_COUNT AS atp
                                FROM        
                                    plan_activities     pa  
                                LEFT JOIN users         u ON pa.user_id=u.id
                                LEFT JOIN area_managers am ON pa.created_by_user_id=am.area_manager_id AND pa.user_type='area'
                                LEFT JOIN products      p ON pa.product_id=p.product_id
                                LEFT JOIN crops         c ON pa.crop_id=c.crop_id
                                LEFT JOIN ATP_COUNT     AC ON AC.product_id=pa.product_id AND AC.crop_id=pa.crop_id AND AC.month=pa.month AND AC.year=pa.year AND AC.created_by_user_id=pa.user_id
                                WHERE 
                                    ".$datesearch."
                                ORDER BY
                                    pa.created_at DESC,
                                    am.area_manager_name ASC,
                                    u.name ASC,
                                    p.product ASC,
                                    c.crop ASC
                                )   A,
                                (select @a:=0) initvars";
            //echo $query; exit;
        
            $product_farmer = DB::select(DB::raw($query));
            $result         = array_map(function ($value) {
                                                return (array)$value;
                                            }, $product_farmer);
            $ticket_column  = array(                
                                    'Id',
                                    'Area Manager',
                                    'BDO Name',
                                    'Month',
                                    'Year',
                                    'Product',
                                    'Crop',
                                    'Activities',
                                    'No of DD',
                                    'Created At',
                                    'ATP Created'
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
                                'name' => "Plan Syllabus List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                            );
            return response()->json($response);
        }
    } 
    
    public function postDeleteSyllabus(Request $request) {        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data    = Input::all(); 
            $area_manager   = (isset($my_data['area_manager'])?$my_data['area_manager']:'');  
            $bdo            = (isset($my_data['bdo'])?$my_data['bdo']:'');  
            $syllabus_year  = (isset($my_data['syllabus_year'])?$my_data['syllabus_year']:'');  
            $syllabus_month = (isset($my_data['syllabus_month'])?$my_data['syllabus_month']:''); 
            $value          = array('is_deleted' => '1');
            $plan_activity  = DB::table('plan_activities')
                                ->where(['created_by_user_id' => $area_manager, 'user_id' => $bdo, 'year' => $syllabus_year, 'month' => $syllabus_month])
                                ->update($value);
            if ($plan_activity) {
                echo 1;
            } else {
                echo 0;
            }       
        }
        else {
            echo 2;
        }
    }

    public function postDeleteSyllabusByID(Request $request) {        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();


        if ($userData) {
            $my_data    = Input::all(); 
            


             $id = (isset($my_data['id'])?$my_data['id']:''); 




            $value          = array('is_deleted' => '1');


            $plan_activity  = DB::table('plan_activities')
                                ->where(['plan_activity_id' =>  $id])
                                ->update($value);
            if ($plan_activity) {
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