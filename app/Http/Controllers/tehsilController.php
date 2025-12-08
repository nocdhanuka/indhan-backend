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

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class tehsilController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAddTehsil() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $state_detail       = State::get();
        return View::make('backend.tehsil.tehsillist', array('state_detail'=>$state_detail));
    }     
    public function postTehsilDistrict(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();     
            $state          = $my_data['state_name'];
            if($state) {
                $district_list  = District::select('district_id AS dId', 'district AS dName')->where('state_id', $state)->where('status', '1')->orderBy('district', 'ASC')->get()->toArray();
            } else {
                $district_list  = District::select('district_id AS dId', 'district AS dName')->where('status', '1')->orderBy('district', 'ASC')->get()->toArray();
            }
            //dd($district_list);
            if(count($district_list)>0) {
                echo json_encode($district_list, true);
            }
            
        }
    }
    public function postAlltehsillist(Request $request) {
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
                                    (CASE WHEN t.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(t.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                                    t.tehsil_id
                                FROM        
                                    tehsils t  
                                LEFT JOIN districts d ON d.district_id=t.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id
                                WHERE
                                    ".$categorysearch." AND
                                    t.status<>'2'
                                ORDER BY
                                    t.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.tehsil ASC
                                )   A,
                                (select @a:=0) initvars";
            $tehsils   = collect(DB::select($query));  
            return Datatables::of($tehsils)->make();
        }
        else 
        {
            $search     =  $search_array['value']; 
            $categorysearch     = '(1=1)';
            if($search!='') {
                $categorysearch     .= "AND (t.tehsil LIKE '".$search."%')";
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
                                    (CASE WHEN t.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(t.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    t.tehsil_id
                                FROM        
                                    tehsils t  
                                LEFT JOIN districts d ON d.district_id=t.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id                        
                                WHERE
                                    ".$categorysearch." AND
                                    t.status<>'2'
                                ORDER BY
                                    t.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.tehsil ASC
                                )   A,
                                (select @a:=0) initvars";
            $tehsils  = collect(DB::select($query));
            return Datatables::of($tehsils)->make();            
        }
    }
    
    public function postCreateTehsil(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();     
            $state      = $my_data['state_name'];
            $district   = $my_data['district_name'];   
            $tehsil     = $my_data['tehsil_name'];   
            $rules      = array(
                            'state_name'    => 'required',
                            'district_name' => 'required',
                            'tehsil_name'   => 'required|unique:tehsils,tehsil,$district,district_id|string',
                        );
            if($state!='' && $district!='') {
                $district_list  = District::select('district_id AS dId', 'district AS dName')->where('state_id', $state)->where('status', '1')->orderBy('district', 'ASC')->get()->toArray();
                $my_data['district_list'] = $district_list;
            }
            else {
                $my_data['district_list'] = array();
            }
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput($my_data)->withErrors($validator)->with('district', $district);
            }
            
            $insert_tehsil = Tehsil::firstOrCreate(array('district_id' => $district, 'tehsil'   => $tehsil, 'status' => '1', 'created_at' => date('Y-m-d H:i:s')));
            $result         = $insert_tehsil->id;
            if($result) {
                Session::flash('message', "Tehsil Successfully Createt.");
                return redirect('tehsil/add-tehsil');             
            }
            else {
                Session::flash('message', "An error occurret. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  
    
    public function getEditTehsil($tehsil_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($tehsil_id!='') {
                $state          = State::get();
                $tehsil         = Tehsil::where('tehsil_id', $tehsil_id)->where('status', '!=', '2')->first();
                $district       = District::where('district_id', $tehsil->district_id)->where('status', '!=', '2')->first();
                $all_districts  = District::where('state_id', $district->state_id)->where('status', '!=', '2')->get();
                //dd($all_districts);
                return View::make('backend.tehsil.edittehsil', array('state_detail'=>$state, 'all_districts'=>$all_districts, 'district_detail'=>$district, 'tehsil_detail'=>$tehsil));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateTehsil(Request $request) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();     
            $state      = $my_data['state_name'];
            $district   = $my_data['district_name'];   
            $tehsil     = $my_data['tehsil_name'];   
            
            $rules      = array(
                            'state_name'    => 'required',
                            'district_name' => 'required',
                            'tehsil_name'   => 'required|unique:tehsils,tehsil,$district,district_id|string',
                        );
            if($state!='' && $district!='') {
                $district_list  = District::select('district_id AS dId', 'district AS dName')->where('state_id', $state)->where('status', '1')->orderBy('district', 'ASC')->get()->toArray();
                $my_data['district_list'] = $district_list;
            }
            else {
                $my_data['district_list'] = array();
            }
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput($my_data)->withErrors($validator)->with('district', $district);
            }
                        
            $tehsil_id     = $my_data['tehsil_id'];
            $tehsil_name   = $my_data['tehsil_name'];
            $update_tehsil = DB::table('tehsils')
                                ->where('tehsil_id', $tehsil_id)
                                ->where('status', '!=', '2')
                                ->update(array('district_id' => $district, 'tehsil'   => $tehsil, 'status' => '1', 'updated_at' => date('Y-m-d H:i:s')));              
            Session::flash('message', "Tehsil Successfully Updatet.");
            return redirect('tehsil/add-tehsil');
                
            if($result) {
                Session::flash('message', "Tehsil Successfully Createt.");
                return redirect('tehsil/add-tehsil');             
            }
            else {
                Session::flash('message', "An error occurret. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    } 
    public function postUpdateTehsilStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $tehsil_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $tehsil_data  = Tehsil::where('tehsil_id', $tehsil_id)->first();
            if($tehsil_data) {
                $tehsil_data_status   = ($tehsil_data->status=='0'?'1':'0');
                $update_tehsil = DB::table('tehsils')
                                    ->where('tehsil_id', $tehsil_id)
                                    ->update(['status'=>$tehsil_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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

