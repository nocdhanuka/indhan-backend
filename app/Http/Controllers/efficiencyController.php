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
use App\Efficiency;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class efficiencyController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getZonalEfficiency() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.efficiency.zonal');
    }
    public function getRegionalEfficiency() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.efficiency.regional');
    }
    public function getSalesRegionalEfficiency() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.efficiency.sales_regional');
    }
    public function getAreaEfficiency() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.efficiency.area');
    }
    public function getSalesAreaEfficiency() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.efficiency.sales_area');
    }
    public function getBdoEfficiency() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.efficiency.bdo');
    }
    
    public function postAllbdoefficiency(Request $request) {
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
        
        $searchtype         = $value['manager_type'];        
        $efficiency_month   = $value['efficiency_month'];
        $efficiency_year    = $value['efficiency_year'];
        
        $datesearch = " AND 1=1";
        if($efficiency_month!='') {
            $datesearch .= " AND er.month = '".$efficiency_month."'";
        }
        if($efficiency_year!='') {
            $datesearch .= " AND er.year = '".$efficiency_year."'";
        }
        
            
        switch ($searchtype) {
            case "zonal"            :   $table = "zonal_managers";          $user_id = "zonal_manager_id";              $username   =  "zonal_manager_name";            break;
            case "regional"         :   $table = "regional_managers";       $user_id = "regional_manager_id";           $username   =  "regional_manager_name";         break;
            case "area"             :   $table = "area_managers";           $user_id = "area_manager_id";               $username   =  "area_manager_name";             break;
            case "sales_regional"   :   $table = "sales_regional_managers"; $user_id = "sales_regional_manager_id";     $username   =  "sales_regional_manager_name";   break;
            case "sales_area"       :   $table = "sales_area_managers";     $user_id = "sales_area_manager_id";         $username   =  "sales_area_manager_name";       break;
            case "bdo"              :   $table = "users";                   $user_id = "id";                            $username   =  "name";                          break;
        }
        
        if(empty($search_array['value']))
        {
            $query      = " 
                        SELECT                                 
                            @a:=@a+1  as serial_number,
                            A.*                                
                        FROM
                            (
                            SELECT     
                                u.ECN,
                                u.".$username.",   
                                er.month,
                                er.year,                                
                                er.CONST_percent_deduction,
                                er.CONST_activity_per_month,
                                er.CONST_average_farmer_registration,                
                                er.total_activity,
                                er.total_registered_farmer,
                                er.total_contacted_farmer,
                                er.total_validated_farmer,
                                er.total_nada_farmer,
                                er.total_pnc_farmer,
                                er.total_bn_farmer,        
                                er.nada_percent,
                                er.pnc_percent,
                                er.bn_percent,                                
                                er.farmer_valid_percent,
                                er.total_ex_valid_farmers,
                                er.average_activity_efficiency,
                                er.average_validate_farmer,                                
                                er.average_farmer_efficiency,                                
                                er.nada_deduction,
                                er.pnc_deduction,
                                er.bn_deduction,
                                er.efficiency
                            FROM        
                                efficiency_results er  
                            LEFT JOIN ".$table." u ON er.user_id=u.".$user_id."
                            WHERE 
                                er.user_type='".$searchtype."' ".$datesearch."
                            ORDER BY
                                u.".$username." ASC
                            )   A,
                            (select @a:=0) initvars";
            $districts   = collect(DB::select($query));  
            return Datatables::of($districts)->make();
        }
        else {
            $search     =  $search_array['value'];                       
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT     
                                    u.ECN,
                                    u.".$username.",   
                                    er.month,
                                    er.year,                        
                                    er.CONST_percent_deduction,
                                    er.CONST_activity_per_month,
                                    er.CONST_average_farmer_registration,                
                                    er.total_activity,
                                    er.total_registered_farmer,
                                    er.total_contacted_farmer,
                                    er.total_validated_farmer,
                                    er.total_nada_farmer,
                                    er.total_pnc_farmer,
                                    er.total_bn_farmer,        
                                    er.nada_percent,
                                    er.pnc_percent,
                                    er.bn_percent,                                
                                    er.farmer_valid_percent,
                                    er.total_ex_valid_farmers,
                                    er.average_activity_efficiency,
                                    er.average_validate_farmer,                                
                                    er.average_farmer_efficiency,                                
                                    er.nada_deduction,
                                    er.pnc_deduction,
                                    er.bn_deduction,
                                    er.efficiency
                                FROM        
                                    efficiency_results er  
                                LEFT JOIN ".$table." u ON er.user_id=u.".$user_id."
                                WHERE 
                                    er.user_type='".$searchtype."' ".$datesearch."
                                ORDER BY
                                    u.".$username." ASC
                                )   A,
                                (select @a:=0) initvars";
            $districts   = collect(DB::select($query));  
            return Datatables::of($districts)->make();            
        }
    }
    
    
    
    
    
    
    
    
    public function postExportEfficiency(Request $request) {          
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data            = Input::all(); 
            $search             = $my_data['search'];            
            $searchtype         = $my_data['manager_type'];        
            $efficiency_month   = $my_data['efficiency_month'];
            $efficiency_year    = $my_data['efficiency_year'];
            
            $datesearch = " AND 1=1";
            if($efficiency_month!='') {
                $datesearch .= " AND er.month = '".$efficiency_month."'";
            }
            if($efficiency_year!='') {
                $datesearch .= " AND er.year = '".$efficiency_year."'";
            }


            switch ($searchtype) {
                case "zonal"            :   $table = "zonal_managers";          $user_id = "zonal_manager_id";              $username   =  "zonal_manager_name";            break;
                case "regional"         :   $table = "regional_managers";       $user_id = "regional_manager_id";           $username   =  "regional_manager_name";         break;
                case "area"             :   $table = "area_managers";           $user_id = "area_manager_id";               $username   =  "area_manager_name";             break;
                case "sales_regional"   :   $table = "sales_regional_managers"; $user_id = "sales_regional_manager_id";     $username   =  "sales_regional_manager_name";   break;
                case "sales_area"       :   $table = "sales_area_managers";     $user_id = "sales_area_manager_id";         $username   =  "sales_area_manager_name";       break;
                case "bdo"              :   $table = "users";                   $user_id = "id";                            $username   =  "name";                          break;
            }
                        
            $query      =   " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT     
                                    u.ECN,
                                    u.".$username.",   
                                    er.month,
                                    er.year,                        
                                    er.CONST_percent_deduction,
                                    er.CONST_activity_per_month,
                                    er.CONST_average_farmer_registration,                
                                    er.total_activity,
                                    er.total_registered_farmer,
                                    er.total_contacted_farmer,
                                    er.total_validated_farmer,
                                    er.total_nada_farmer,
                                    er.total_pnc_farmer,
                                    er.total_bn_farmer,        
                                    er.nada_percent,
                                    er.pnc_percent,
                                    er.bn_percent,                                
                                    er.farmer_valid_percent,
                                    er.total_ex_valid_farmers,
                                    er.average_activity_efficiency,
                                    er.average_validate_farmer,                                
                                    er.average_farmer_efficiency,                                
                                    er.nada_deduction,
                                    er.pnc_deduction,
                                    er.bn_deduction,
                                    er.efficiency
                                FROM        
                                    efficiency_results er  
                                LEFT JOIN ".$table." u ON er.user_id=u.".$user_id."
                                WHERE 
                                    er.user_type='".$searchtype."' ".$datesearch."
                                ORDER BY
                                    u.".$username." ASC
                                )   A,
                                (select @a:=0) initvars";
            //echo $query; exit;
        
            $product_farmer = DB::select(DB::raw($query));
            $result         = array_map(function ($value) {
                                                return (array)$value;
                                            }, $product_farmer);
            $ticket_column  = array(                
                                    'Sr. No.',
                                    'ECN',
                                    'Name',
                                    'Month',
                                    'Year',

                                    'CONSTANT Percent Deduction',
                                    'CONSTANT Activity Per Month',
                                    'CONSTANT Average Farmer Registration',

                                    'Total Activity',
                                    'Total Registered Farmer',
                                    'Total Contacted Farmer',
                                    'Total Validated Farmer',
                                    'Total NADA Farmer',
                                    'Total PNC Farmer',
                                    'Total BN Farmer',

                                    'NADA Percent',
                                    'PNC Percent',
                                    'BN Percent',

                                    'Farmer Valid Percent',
                                    'Total ex valid Farmers',
                                    'Average Activity Efficiency',
                                    'Average Validate Farmer',                                
                                    'Average Farmer Efficiency',

                                    'NADA Deduction',
                                    'PNC Deduction',
                                    'BN Deduction',
                                    'Efficiency',
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
                                'name' => "Efficiency List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                            );
            return response()->json($response);
        }
    } 
    
}