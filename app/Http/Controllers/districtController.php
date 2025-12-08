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

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Log;

class districtController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAddDistrict() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $state_detail    = State::get();
        return View::make('backend.district.districtlist', array('state_detail'=>$state_detail));
    }
    public function postAlldistrictlist(Request $request) {
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
        
        $state_name     = $value['state_name'];
        
        if(empty($search_array['value']))
        {
            $categorysearch     = '(1=1)';
            if($state_name!='') {
                $categorysearch     .= "AND (s.state_id='".$state_name."')";
            }
            
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    s.state,
                                    d.district,
                                    (CASE WHEN d.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(d.created_at, '%M, %d %Y, %h:%i %p') as created_at,
                                    d.district_id
                                FROM        
                                    districts d  
                                LEFT JOIN states s ON s.state_id=d.state_id
                                WHERE
                                    ".$categorysearch." AND
                                    d.status<>'2'
                                ORDER BY
                                    d.created_at DESC,
                                    s.state ASC,
                                    d.district ASC
                                )   A,
                                (select @a:=0) initvars";
            $districts   = collect(DB::select($query));  
            return Datatables::of($districts)->make();
        }
        else {
            $search     =  $search_array['value']; 
            $categorysearch     = '(1=1)';
            if($state_name!='') {
                $categorysearch     .= "AND (s.state_id='".$state_name."')";
            }
                      
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT        
                                    s.state,                        
                                    d.district,
                                    (CASE WHEN d.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(d.created_at, '%M, %d %Y, %h:%i %p') as created_at,
                                    d.district_id
                                FROM        
                                    districts d  
                                LEFT JOIN states s ON s.state_id=d.state_id                             
                                WHERE
                                    ".$categorysearch." AND
                                    d.status<>'2'
                                ORDER BY
                                    d.created_at DESC,
                                    s.state ASC,
                                    d.district ASC
                                )   A,
                                (select @a:=0) initvars";
            $districts  = collect(DB::select($query));
            return Datatables::of($districts)->make();            
        }
    }   
    public function postCreateDistrict(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();     
            $state      = $my_data['state_name'];
            $district   = $my_data['district_name'];   
            $rules      = array(
                            'state_name'    => 'required|unique:districts,district,2,status|string',
                            'district_name' => 'required|unique:districts,district,$state,state_id|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $insert_district = District::firstOrCreate(array('state_id' => $state, 'district'   => $district, 'status' => '1', 'created_at' => date('Y-m-d H:i:s')));
            $result         = $insert_district->id;
            if($result) {
                Session::flash('message', "District Successfully Created.");
                return redirect('district/add-district');             
            }
            else {
                Session::flash('message', "An error occurred. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  
    
    public function getEditDistrict($district_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($district_id!='') {
                $state_detail    = State::get();
                $district_detail = District::where('district_id', $district_id)->where('status', '!=', '2')->first();
                return View::make('backend.district.editdistrict', array('state_detail'=>$state_detail, 'district_detail'=>$district_detail));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateDistrict(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'state_name'    => 'required|unique:districts,district,2,status|string',
                            'district_name' => 'required|unique:districts,district,$state,state_id|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            else {
                $district_id     = $my_data['district_id'];
                $district_name   = $my_data['district_name'];
                $update_district = DB::table('districts')
                                    ->where('district_id', $district_id)
                                    ->where('status', '!=', '2')
                                    ->update(['district'=>$district_name, 'updated_at' => date('Y-m-d H:i:s')]);              
                Session::flash('message', "District Successfully Updated.");
                return redirect('district/add-district');
            }
        }
    } 
    public function postUpdateDistrictStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $district_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $district_data  = District::where('district_id', $district_id)->first();
            if($district_data) {
                $district_data_status   = ($district_data->status=='0'?'1':'0');
                $update_district = DB::table('districts')
                                    ->where('district_id', $district_id)
                                    ->update(['status'=>$district_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
                echo '1';
            }
            else {
                echo '0';
            }
        }
        else {
            echo '0';
        }
    } 
    
    

    public function postDistrictDelete(Request $request) {         
//        $sess       =   \Session::get('adminid');
//        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
//        if ($userData) {
//            $my_data= Input::all();        
//            $rules  = array(
//                            '_ticket'    => 'required|numeric'
//                        );
//            $validator  = Validator::make($my_data, $rules);
//            if ($validator->fails()) {
//                echo '0';
//            }
//            else {
//                $district_id     = $my_data['_ticket'];
//                $update_district = DB::table('districts')
//                                    ->where('district_id', $district_id)
//                                    ->update(['status' => '2']);
//                if ($update_district) {
//                    echo 1;
//                } else {
//                    echo 2;
//                }
//            }
//        }
    } 
    
    
    
    public function postStateDistrict(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data            = Input::all();     
            $state              = (isset($my_data['state'])?$my_data['state']:'');
            $district_list      = District::select('district_id', 'district')->where('state_id', $state)->where('status', '1')->orderBy('district', 'ASC')->get()->toArray();
            $district           = array();
            if(count($district_list)>0) {
                $district['district'] = $district_list;
            }
            echo json_encode($district, true);die;
        }

        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $my_data            = Input::all();     
            $state              = (isset($my_data['state'])?$my_data['state']:'');
            $district_list      = District::select('district_id', 'district')->where('state_id', $state)->where('status', '1')->orderBy('district', 'ASC')->get()->toArray();
            $district           = array();
            if(count($district_list)>0) {
                $district['district'] = $district_list;
            }
            echo json_encode($district, true);die;
        }
    }

    public function postDistrictTehsil(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();     
            $district   = (isset($my_data['district'])?$my_data['district']:'');
            $tehsil_list = Tehsil::select('tehsil_id', 'tehsil')->where('district_id', $district)->where('status', '1')->orderBy('tehsil', 'ASC')->get()->toArray();
            // $district = array();
            $tehsil   = array();
            if(count($tehsil_list)>0) {
                $tehsil['tehsil'] = $tehsil_list;
            }
            echo json_encode($tehsil, true);die;
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $my_data    = Input::all();     
            $district   = (isset($my_data['district'])?$my_data['district']:'');
            $tehsil_list = Tehsil::select('tehsil_id', 'tehsil')->where('district_id', $district)->where('status', '1')->orderBy('tehsil', 'ASC')->get()->toArray();
            // $district = array();
            $tehsil   = array();
            if(count($tehsil_list)>0) {
                $tehsil['tehsil'] = $tehsil_list;
            }
            echo json_encode($tehsil, true);die;
        }
    }

    public function postTehsilVillage(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();     
            $tehsil     = (isset($my_data['tehsil'])?$my_data['tehsil']:'');
            $village_list = Village::select('village_id', 'village')->where('tehsil_id', $tehsil)->where('status', '1')->orderBy('village', 'ASC')->get()->toArray();
            $village = array();
            if(count($village_list)>0) {
                $village['village'] = $village_list;
            }
            echo json_encode($village, true);die;
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $my_data    = Input::all();     
            $tehsil     = (isset($my_data['tehsil'])?$my_data['tehsil']:'');
            $village_list = Village::select('village_id', 'village')->where('tehsil_id', $tehsil)->where('status', '1')->orderBy('village', 'ASC')->get()->toArray();
            $village = array();
            if(count($village_list)>0) {
                $village['village'] = $village_list;
            }
            echo json_encode($village, true);die;
        }
    }

    public function postStateZonal(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data            = Input::all();     
            $state              = (isset($my_data['state'])?$my_data['state']:'');
            $zonal_list         = DB::table('zonal_manager_states as zms')
                                  ->join('zonal_managers as zm','zm.zonal_manager_id','=','zms.zonal_manager_id','left')
                                  ->where('zms.state_id',$state)
                                  ->where('zms.status','1')
                                  ->select('zm.zonal_manager_id','zm.zonal_manager_name')
                                  ->orderBy('zm.zonal_manager_name','asc')
                                  ->get();
            $zonal              = array();
            if(count($zonal_list)>0) {
                $zonal_list = json_decode(json_encode($zonal_list),true);
                $zonal['zonal'] = $zonal_list;
            }
            echo json_encode($zonal, true);die;
        }
        $userData1   =   \DB::table('branch_admin')->where(['id' => $sess])->first();
        if ($userData1) {
            $my_data            = Input::all();     
            $state              = (isset($my_data['state'])?$my_data['state']:'');
            $zonal_list         = DB::table('zonal_manager_states as zms')
                                  ->join('zonal_managers as zm','zm.zonal_manager_id','=','zms.zonal_manager_id','left')
                                  ->where('zms.state_id',$state)
                                  ->where('zms.status','1')
                                  ->select('zm.zonal_manager_id','zm.zonal_manager_name')
                                  ->orderBy('zm.zonal_manager_name','asc')
                                  ->get();
            $zonal              = array();
            if(count($zonal_list)>0) {
                $zonal_list = json_decode(json_encode($zonal_list),true);
                $zonal['zonal'] = $zonal_list;
            }
            echo json_encode($zonal, true);die;
        }
    }

    public function postTehsilAssignVillage(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();     
            $tehsil     = (isset($my_data['tehsil'])?$my_data['tehsil']:'');
            // \DB::enableQueryLog();
            $query = "SELECT * FROM villages WHERE NOT EXISTS (SELECT * FROM assigned_village WHERE assigned_village.village_id = villages.village_id) AND villages.tehsil_id='".$tehsil."'";
            $village_list = DB::select(DB::raw($query));
            // $querylog = \DB::getQueryLog();
            // Log::info($querylog);
            // echo json_encode($village_list); exit();

            $village = array();
            if(count($village_list)>0) {
                $village['village'] = $village_list;
            }
            echo json_encode($village, true);die;
        }
    }
}
