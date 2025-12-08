<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
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
use App\AppMenu;
use App\ReportGuideline;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Storage;

class ReportsController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function getAddReports() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $reports_type   = DB::table('report_type')->get();
        $crr_year       = date('Y');
        $years          = date('Y', strtotime('+1 years'));
        $months         = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
        $zonal_list     = ZonalManager::where('status', '1')->get();
        return View::make('backend.reports.add-reports',array('zonal_list'=>$zonal_list,'reports_type'=>$reports_type, 'months'=>$months, 'years'=>$years,'crr_year'=>$crr_year));
    }

    public function postCreateReports(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $report_file = '';
            $my_data= Input::all();
            $rules  = array(
                        'report_name'   => 'required',
                        'report_type'   => 'required',
                        'year'          => 'required',
                        'month'         => 'required',
                        'zonal_manager' => 'required'
                    );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            if($request->file('report_file')) {
                $file=$request->file('report_file');
                //s3Upload
                $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                $sTempFileName = 'assets/ReportsGuidelines/' . $fileName;
                Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                $report_file = $sTempFileName;
            }
            $report_name        = $my_data['report_name'];
            $report_type        = $my_data['report_type'];
            $year               = $my_data['year'];
            $month              = $my_data['month'];
            $status             = $my_data['status'];
            $zonal_manager      = $my_data['zonal_manager'];
            $regional_manager   = (!empty($my_data['regional_manager'])) ? implode(',', $my_data['regional_manager']) : NULL;
            $sales_regional_manager = (!empty($my_data['sales_regional_manager'])) ? implode(',', $my_data['sales_regional_manager']) : NULL;
            $area_manager       = (!empty($my_data['area_manager'])) ? implode(',', $my_data['area_manager']) : NULL;
            $sales_area_manager = (!empty($my_data['sales_area_manager'])) ? implode(',', $my_data['sales_area_manager']) : NULL;
            $user_id            = (!empty($my_data['user_id'])) ? implode(',', $my_data['user_id']) : NULL;

            $report_arr = array(
                            'report_type_id'    => $report_type,
                            'report_name'       => $report_name,
                            'year'              => $year,
                            'month'             => $month,
                            'zonal_manager_id'  => $zonal_manager,
                            'regional_manager_id'       => $regional_manager,
                            'sales_regional_manager_id' => $sales_regional_manager,
                            'area_manager_id'           => $area_manager,
                            'sales_area_manager_id'     => $sales_area_manager,
                            'user_id'       => $user_id,
                            'uploaded_file' => $report_file,
                            'status'        => $status,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        );

            $insert_report = ReportGuideline::firstOrCreate($report_arr);
            $result     = $insert_report->id;
            if($result) {
                Session::flash('message', "Reports Created Successfully.");
                return redirect('reports/add-reports');             
            } else {
                Session::flash('message', "An error occurret. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }

    public function getEditReports($report_id) {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        if($report_id!='') {
            $reportData   = \DB::table('report_guideline')->where(['report_id' => $report_id])->first();
            // echo "<pre>";
            // print_r($reportData); exit();
            if ($reportData) {
                $reports_type   = DB::table('report_type')->get();
                $crr_year       = date('Y');
                $years          = date('Y', strtotime('+1 years'));
                $months         = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
                $zonal_list     = ZonalManager::where('status', '1')->get();

                $regional_list  = RegionalManager::where('status', '1')->select('regional_manager_id', 'regional_manager_name')->where(['zonal_manager_id' => $reportData->zonal_manager_id])->orderBy('regional_manager_name', 'asc')->get();

                $sales_regional_list  = SalesRegionalManager::where('status', '1')->select('sales_regional_manager_id', 'sales_regional_manager_name')->whereIn('zonal_manager_id',explode(',', $reportData->zonal_manager_id))->orderBy('sales_regional_manager_name', 'asc')->get();

                $area_manager_list          = AreaManager::where('status', '1')->select('area_manager_id', 'area_manager_name')->whereIn('regional_manager_id',explode(',', $reportData->regional_manager_id))->orderBy('area_manager_name', 'asc')->get();
                
                $sales_area_manager_list    = SalesAreaManager::where('status', '1')->select('sales_area_manager_id', 'sales_area_manager_name')->whereIn('regional_manager_id',explode(',', $reportData->regional_manager_id))->orderBy('sales_area_manager_name', 'asc')->get();
                
                $bdo_list        = User::where('status', '1')->select('id', 'name')->whereIn('sales_area_manager_id', explode(',', $reportData->sales_area_manager_id))->orderBy('name', 'asc')->get();

                return View::make('backend.reports.edit-reports',array('reportData'=>$reportData,'zonal_list'=>$zonal_list, 'regional_list'=>$regional_list, 'sales_regional_list'=>$sales_regional_list, 'area_manager_list'=>$area_manager_list, 'sales_area_manager_list'=>$sales_area_manager_list, 'bdo_list'=> $bdo_list, 'reports_type'=>$reports_type, 'months'=>$months, 'years'=>$years,'crr_year'=>$crr_year));
            } else {
                return redirect('/reports/list-reports');
            }
        } else {
            return redirect('/reports/list-reports');
        }
    }

    public function postUpdateReports(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data= Input::all();
            $rules  = array(
                        'report_name'   => 'required',
                        'report_type'   => 'required',
                        'year'          => 'required',
                        'month'         => 'required',
                        'zonal_manager' => 'required',
                        'report_id'     => 'required|numeric'
                    );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            $report_id    = $my_data['report_id'];
            $reportData   = \DB::table('report_guideline')->where(['report_id' => $report_id])->first();
            // echo "<pre>";
            // print_r($reportData); exit();
            if ($reportData) {
                if($request->file('report_file')) {
                    $file=$request->file('report_file');
                    //s3Upload
                    $fileName=time().rand(111,999).".".$file->getClientOriginalExtension();
                    $sTempFileName = 'assets/ReportsGuidelines/' . $fileName;
                    Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                    $report_file = $sTempFileName;
                    DB::table('report_guideline')->where('report_id', $report_id)->update(array('uploaded_file' => $report_file));
                }
                $report_name        = $my_data['report_name'];
                $report_type        = $my_data['report_type'];
                $year               = $my_data['year'];
                $month              = $my_data['month'];
                $status             = $my_data['status'];
                $zonal_manager      = $my_data['zonal_manager'];
                $regional_manager   = (!empty($my_data['regional_manager'])) ? implode(',', $my_data['regional_manager']) : NULL;
                $sales_regional_manager = (!empty($my_data['sales_regional_manager'])) ? implode(',', $my_data['sales_regional_manager']) : NULL;
                $area_manager       = (!empty($my_data['area_manager'])) ? implode(',', $my_data['area_manager']) : NULL;
                $sales_area_manager = (!empty($my_data['sales_area_manager'])) ? implode(',', $my_data['sales_area_manager']) : NULL;
                $user_id            = (!empty($my_data['user_id'])) ? implode(',', $my_data['user_id']) : NULL;

                $report_arr = array(
                                'report_type_id'    => $report_type,
                                'report_name'       => $report_name,
                                'year'              => $year,
                                'month'             => $month,
                                'zonal_manager_id'  => $zonal_manager,
                                'regional_manager_id'       => $regional_manager,
                                'sales_regional_manager_id' => $sales_regional_manager,
                                'area_manager_id'           => $area_manager,
                                'sales_area_manager_id'     => $sales_area_manager,
                                'user_id'       => $user_id,
                                'status'        => $status,
                                'updated_at'    => date('Y-m-d H:i:s')
                            );

                // echo "<pre>"; print_r($report_arr); exit();
                $update_report = DB::table('report_guideline')->where('report_id', $report_id)->update($report_arr);
                if($update_report) {
                    Session::flash('message', "Reports Updated Successfully.");
                    return redirect('reports/list-reports');             
                } else {
                    Session::flash('message', "An error occurret. Please try again later.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            } else {
                return redirect('/reports/list-reports');
            }
        }
    }

    public function getListReports() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $crr_year       = 2020;
        $years          = date('Y', strtotime('+1 years'));
        $months         = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
        return View::make('backend.reports.list-reports',array('months'=>$months, 'years'=>$years,'crr_year'=>$crr_year));
    }

    public function postAllReportsList(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $value          = Input::all();
        $status         = isset($value['status']) ? $value['status'] :'';
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];

        $start_date     = $value['start_date'];
        $end_date       = $value['end_date'];
        $year           = $value['year'];
        $month          = $value['month'];

        $adQuery = "(1=1)";
        if($start_date!='' && $end_date!='') {
            $adQuery .= " AND DATE(r.created_at) >= DATE('".$start_date."') AND DATE(r.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($year)) { $adQuery .= " AND r.year = '".$year."'"; }
        if(!empty($month)) { $adQuery .= " AND r.month = '".$month."'"; }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.report_name LIKE '%".$search."%' OR A.report_type LIKE '%".$search."%')";
        } else {
            $adSearch = "";
        }
        $sorting    = '';
        $query      = " 
                        SELECT
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                r.report_id,
                                r.report_name as report_name,
                                rt.name as report_type,
                                r.year,
                                r.month,
                                zm.zonal_manager_name,
                                r.regional_manager_id,
                                r.sales_regional_manager_id,
                                r.area_manager_id,
                                r.sales_area_manager_id,                                
                                r.user_id,                                
                                r.uploaded_file,
                                DATE_FORMAT(r.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,
                                (CASE WHEN r.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status
                            FROM report_guideline r                                
                            LEFT JOIN   report_type                 rt      ON r.report_type_id=rt.report_type_id
                            LEFT JOIN   zonal_managers              zm      ON r.zonal_manager_id=zm.zonal_manager_id
                            WHERE ".$adQuery."
                            GROUP BY r.report_id
                            ORDER BY
                                r.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
                           
        // print_r($query); exit();
        $report_list    = collect(DB::select($query));
        foreach ($report_list as $key => $value) {
            $regional_manager_id        = $value->regional_manager_id;
            $sales_regional_manager_id  = $value->sales_regional_manager_id;
            $area_manager_id            = $value->area_manager_id;
            $sales_area_manager_id      = $value->sales_area_manager_id;
            $user_id                    = $value->user_id;
            if($regional_manager_id) {
                $regional_manager_id = ltrim(rtrim($regional_manager_id,','),',');
                $qr_regional_manager = 'SELECT GROUP_CONCAT(DISTINCT regional_manager_name) as regional_manager_name FROM `regional_managers` WHERE regional_manager_id IN ('.$regional_manager_id.')';
                $regional_manager_list = collect(DB::select($qr_regional_manager));
                $report_list[$key]->regional_manager_name = $regional_manager_list[0]->regional_manager_name;
            } else {
                $report_list[$key]->regional_manager_name = "";
            }
            if($sales_regional_manager_id) {
                $sales_regional_manager_id = ltrim(rtrim($sales_regional_manager_id,','),',');
                $qr_sales_regional_manager = 'SELECT GROUP_CONCAT(DISTINCT sales_regional_manager_name) as sales_regional_manager_name FROM `sales_regional_managers` WHERE sales_regional_manager_id IN ('.$sales_regional_manager_id.')';
                $sales_regional_manager_list = collect(DB::select($qr_sales_regional_manager));
                $report_list[$key]->sales_regional_manager_name = $sales_regional_manager_list[0]->sales_regional_manager_name;
            } else {
                $report_list[$key]->sales_regional_manager_name = "";
            }
            if($area_manager_id) {
                $area_manager_id = ltrim(rtrim($area_manager_id,','),',');
                $qr_area_manager = 'SELECT GROUP_CONCAT(DISTINCT area_manager_name) as area_manager_name FROM `area_managers` WHERE area_manager_id IN ('.$area_manager_id.')';
                $area_manager_list = collect(DB::select($qr_area_manager));
                $report_list[$key]->area_manager_name = $area_manager_list[0]->area_manager_name;
            } else {
                $report_list[$key]->area_manager_name = "";
            }
            if($sales_area_manager_id) {
                $sales_area_manager_id = ltrim(rtrim($sales_area_manager_id,','),',');
                $qr_sales_area_manager = 'SELECT GROUP_CONCAT(DISTINCT sales_area_manager_name) as sales_area_manager_name FROM `sales_area_managers` WHERE sales_area_manager_id IN ('.$sales_area_manager_id.')';
                $sales_area_manager_list = collect(DB::select($qr_sales_area_manager));
                $report_list[$key]->sales_area_manager_name = $sales_area_manager_list[0]->sales_area_manager_name;
            } else {
                $report_list[$key]->sales_area_manager_name = "";
            }
            if($user_id) {
                $user_id = ltrim(rtrim($user_id,','),',');
                $qr_user = 'SELECT GROUP_CONCAT(DISTINCT name) as name FROM `users` WHERE id IN ('.$user_id.')';
                $user_list = collect(DB::select($qr_user));
                $report_list[$key]->front_liner = $user_list[0]->name;
            } else {
                $report_list[$key]->front_liner = "";
            }
        }
        //print_r($report_list);
        return Datatables::of($report_list)->make();
    }
    
    public function postUpdateReportsStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $territory_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $territory_data  = territories::where('territory_id', $territory_id)->first();
            if($territory_data) {
                $territory_data_status   = ($territory_data->status=='0'?'1':'0');
                $update_territory = DB::table('territories')
                                    ->where('territory_id', $territory_id)
                                    ->update(['status'=>$territory_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
                echo '1';
            } else {
                echo '0';
            }
        } else {
            echo '0';
        }
    }

}
