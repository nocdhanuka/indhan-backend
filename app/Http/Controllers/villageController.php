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
use App\Village;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class villageController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAddVillage() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $state_detail       = State::get();
        return View::make('backend.village.villagelist', array('state_detail'=>$state_detail));
    }

    public function postVillageTehsil(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();     
            $district_name          = $my_data['district_name'];   
            $tehsil_list  = Tehsil::select('tehsil_id AS tId', 'tehsil AS tName')->where('district_id', $district_name)->where('status', '1')->orderBy('tehsil', 'ASC')->get()->toArray();
            //echo "<pre>";print_r($tehsil_list);exit();
            //dd($district_list);
            if(count($tehsil_list)>0) {
                echo json_encode($tehsil_list, true);
            }
            
        }
    }   
    public function postVillageDistrict(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();     
            $state          = $my_data['state_name'];   
            $district_list  = District::select('district_id AS dId', 'district AS dName')->where('state_id', $state)->where('status', '1')->orderBy('district', 'ASC')->get()->toArray();
            //dd($district_list);
            if(count($district_list)>0) {
                echo json_encode($district_list, true);
            }
            
        }
    }      
    public function postAllVillagelist(Request $request) {
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
                                    t.tehsil,
                                    v.village,
                                    (CASE WHEN v.latitude='' THEN 'NULL' ELSE v.latitude END) as latitude,
                                    (CASE WHEN v.longitude='' THEN 'NULL' ELSE v.longitude END) as longitude,
                                    (CASE WHEN v.radius='' THEN 'NULL' ELSE v.radius END) as radius,
                                    (CASE WHEN v.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(v.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                                    v.village_id
                                FROM        
                                    villages v  
                                LEFT JOIN tehsils t ON t.tehsil_id=v.tehsil_id
                                LEFT JOIN districts d ON d.district_id=t.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id
                                
                                WHERE
                                    ".$categorysearch." AND
                                    v.status<>'2'
                                ORDER BY
                                    v.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.tehsil ASC,
                                    v.village ASC
                                )  A,
                                (select @a:=0) initvars";
            $villages   = collect(DB::select($query));  
            return Datatables::of($villages)->make();
        }
        else 
        {
            $search     =  $search_array['value']; 
            $categorysearch     = '(1=1)';
            if($search!='') {
                $categorysearch     .= "AND (v.village LIKE '".$search."%')";
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
                                    t.tehsil,
                                    v.village,
                                    (CASE WHEN v.latitude='' THEN 'NULL' ELSE v.latitude END) as latitude,
                                    (CASE WHEN v.longitude='' THEN 'NULL' ELSE v.longitude END) as longitude,
                                    (CASE WHEN v.radius='' THEN 'NULL' ELSE v.radius END) as radius,
                                    (CASE WHEN v.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(v.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                                    v.village_id
                                FROM        
                                    villages v   
                                LEFT JOIN tehsils t ON t.tehsil_id=v.tehsil_id
                                LEFT JOIN districts d ON d.district_id=t.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id
                                
                                WHERE
                                    ".$categorysearch." AND
                                    v.status<>'2'
                                ORDER BY
                                    v.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.tehsil ASC,
                                    v.village ASC
                                )   A,
                                (select @a:=0) initvars";
            $villages  = collect(DB::select($query));
            return Datatables::of($villages)->make();            
        }
    }
    
    public function postCreateVillage(Request $request) {
     
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();    

            $state      = $my_data['state_name'];
            $district   = $my_data['district_name'];   
            $tehsil     = $my_data['tehsil_name'];
            $village    = $my_data['village_name'];   
            $latitude   = $my_data['village_latitude'];   
            $longitude  = $my_data['village_longitude'];   
            $radius     = $my_data['village_radius'];
            $rules      = array(
                            'state_name'    => 'required',
                            'district_id'   => 'required',
                            'tehsil_id'     => 'required',
                            'village_name'  => 'required|string',
                            'village_latitude'  => 'required',
                            'village_longitude' => 'required',
                            'village_radius'    => 'required',
                        );
            // if($district!='' && $tehsil!='') { 
            //     $tehsil_list  = Tehsil::select('tehsil_id AS tId', 'tehsil AS tName')->where('district_id', $district)->where('status', '1')->orderBy('tehsil', 'ASC')->get()->toArray();
            //     $my_data['tehsil_list'] = $tehsil_list;
            // }
            // else {
            //     $my_data['tehsil_list'] = array();
            // }
            // $validator  = Validator::make($my_data, $rules);
            // if ($validator->fails()) {
            //     return Redirect::back()->withInput($my_data)->withErrors($validator)->with('tehsil_name', $tehsil);
            // }
            $villagedata = array(
                                'district_id'   => $district,
                                'tehsil_id' => $tehsil,
                                'village'   => $village,
                                'latitude'  => $latitude,
                                'longitude' => $longitude,
                                'radius'    => $radius,
                                'status'    => '1',
                                'created_at'=> date('Y-m-d H:i:s'),
                                'updated_at'=> date('Y-m-d H:i:s')
                                ); 
            $insert_village = Village::firstOrCreate($villagedata);
            // Village::firstOrCreate(array('district_id' => $district, 'tehsil_id'   => $tehsil, 'village'   => $village, 'status' => '1', 'created_at' => date('Y-m-d H:i:s')));
            $result         = $insert_village->village_id;
            if($result) {
                Session::flash('message', "Village Successfully Created.");
                return redirect('village/add-village');             
            }
            else {
                // Session::flash('message', "An error occurret. Please try again later.");
                // return Redirect::back()->withInput()->withErrors($validator);
                return redirect('village/add-village');
            }
        }
    }  
    
    public function getEditVillage($village_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($village_id!='') {
                $state          = State::get();
                $village         = Village::where('village_id', $village_id)->where('status', '!=', '2')->first();
                $district       = District::where('district_id', $village->district_id)->where('status', '!=', '2')->first();
                $all_districts  = District::where('state_id', $district->state_id)->where('status', '!=', '2')->get();
                $tehsil         = Tehsil::where('tehsil_id', $village->tehsil_id)->where('status', '!=', '2')->first();
                //echo "<pre>";print_r($tehsil);exit();
                return View::make('backend.village.editvillage', array('state_detail'=>$state, 'all_districts'=>$all_districts, 'district_detail'=>$district, 'tehsil_detail'=>$tehsil, 'village_detail' => $village));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateVillage(Request $request) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();     
            $state      = $my_data['state_name'];
            $district   = $my_data['district_name'];   
            $tehsil     = $my_data['tehsil_name'];
            $village    = $my_data['village_name'];
            $latitude   = $my_data['village_latitude'];   
            $longitude  = $my_data['village_longitude'];   
            $radius     = $my_data['village_radius'];
            $village_id =  $my_data['village_id'];
            
            $rules      = array(
                            'state_name'    => 'required',
                            'district_id' => 'required',
                            'tehsil_id'   => 'required',
                            'village_name'   => 'required|string',
                            'village_id' => 'required',
                            'village_latitude'  => 'required',
                            'village_longitude' => 'required',
                            'village_radius'    => 'required'
                        );
                        
            $update_village = DB::table('villages')
                                ->where('village_id', $village_id)
                                ->update(array('district_id' => $district, 'tehsil_id'   => $tehsil, 'status' => '1', 'updated_at' => date('Y-m-d H:i:s'), 'village' => $village, 'latitude' => $latitude, 'longitude' => $longitude, 'radius' => $radius));              
            if($update_village) {
                Session::flash('message', "Village Successfully Updatet.");
                return redirect('village/add-village');              
            }
            else {
                Session::flash('message', "An error occurret. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    } 
    public function postUpdateVillageStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $village_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $village_data  = Village::where('village_id', $village_id)->first();
            if($village_data) {
                $village_data_status   = ($village_data->status=='0'?'1':'0');
                $update_village = DB::table('villages')
                                    ->where('village_id', $village_id)
                                    ->update(['status'=>$village_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    
    public function postImportVillages(Request $request)
    {   
        $my_data  = Request::all();      
        $rules  = array(
                        'villagedata' => 'required'
                        );
        $validator  = Validator::make($my_data, $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        
        if($my_data['villagedata']){
            $path1 = Request::file('villagedata')->getRealPath();
            //echo "<pre>"; print_r($path1); die;
            // $data = Excel::load($path)->get();
            $data = Excel::load($path1)->get();
            //echo "<pre>";print_r($data);die;
            $ExcelArr = array();
            if($data->count() > 0)
            {
                 foreach ($data as $key => $value) {
                    //echo "<pre>"; print_r($value->district_id); die;
                    $ExcelArr['district_id']= $value->district_id;
                    $ExcelArr['tehsil_id']  = $value->tehsil_id;
                    $ExcelArr['village']    = $value->village;
                    $ExcelArr['latitude']   = $value->latitude;
                    $ExcelArr['longitude']  = $value->longitude;
                    $ExcelArr['radius']     = $value->radius;
                    $ExcelArr['created_at'] = date('Y-m-d H:i:s');
                    $ExcelArr['updated_at'] = date('Y-m-d H:i:s');
                    //echo "<pre>"; print_r($ExcelArr); die;
                    $village = DB::table('villages')
                                ->where('villages.district_id',$value->district_id)
                                ->where('villages.tehsil_id',$value->tehsil_id)
                                ->where('villages.village',$value->village)
                                ->where('villages.latitude',$value->latitude)
                                ->where('villages.latitude',$value->latitude)
                                ->where('villages.radius',$value->radius)
                                ->select('villages.*')
                                ->count();
                    if ($village == 0) { 
                        Village::insert($ExcelArr);
                    }
                }
            }
        }
        return back()->with('success','Excel Data imported successfully');
    }
    

    public function postTehsilDelete(Request $request) {         
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
//                $tehsil_id     = $my_data['_ticket'];
//                $update_tehsil = DB::table('tehsils')
//                                    ->where('tehsil_id', $tehsil_id)
//                                    ->update(['status' => '2']);
//                if ($update_tehsil) {
//                    echo 1;
//                } else {
//                    echo 2;
//                }
//            }
//        }
    } 
}

