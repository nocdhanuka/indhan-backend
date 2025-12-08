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
use App\Classes;
use App\Ticket;
use App\Farmer;
use App\Language;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class classController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAddClass() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.class.classlist', array());
    }
    public function postAllclasslist(Request $request) {
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
        
        if(empty($search_array['value']))
        {
            $sorting    = '';
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT                                
                                    p.name,
                                    (CASE WHEN p.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(p.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    p.class_id
                                FROM        
                                    class p
                                WHERE
                                    1=1 AND
                                    p.status<>'2'
                                ORDER BY
                                    created_at DESC
                                )   A,
                                (select @a:=0) initvars";
            $class   = collect(DB::select($query));  
            return Datatables::of($class)->make();
        }
        else {
            $search     =  $search_array['value']; 
            $sorting    = '';            
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT                                
                                    p.name,
                                    (CASE WHEN p.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(p.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    p.class_id
                                FROM        
                                    class p                               
                                WHERE
                                    1=1 AND
                                    p.status<>'2'
                                ORDER BY
                                    created_at DESC
                                )   A,
                                (select @a:=0) initvars";
            $class  = collect(DB::select($query));
            return Datatables::of($class)->make();            
        }
    }   
    public function postCreateClass(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules      = array(
                            'class_name'   => 'required|unique:class,name,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $class        = $my_data['class_name'];
            $insert_class = Classes::firstOrCreate(array('name'   => $class, 'status' => '1', 'created_at' => date('Y-m-d H:i:s')));
            $result       = $insert_class->id;
            if($result) {
                Session::flash('message', "Class Successfully Created.");
                return redirect('class/add-class');             
            }
            else {
                Session::flash('message', "An error occurred. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  
    
    public function getEditClass($class_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($class_id!='') {
                $class_detail = Classes::where('class_id', $class_id)->where('status', '!=', '2')->first();
                return View::make('backend.class.editclass', array('class_detail'=>$class_detail));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateClass(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'class_id'    => 'required|numeric',
                            'class_name'  => 'required|unique:class,name,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            else {
                $class_id     = $my_data['class_id'];
                $class_name   = $my_data['class_name'];
                $update_class = DB::table('class')
                                    ->where('class_id', $class_id)
                                    ->where('status', '!=', '2')
                                    ->update(['name'=>$class_name, 'updated_at' => date('Y-m-d H:i:s')]);              
                Session::flash('message', "Class Successfully Updated.");
                return redirect('class/add-class');
            }
        }
    } 
    public function postUpdateClassStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $class_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $class_data  = Classes::where('class_id', $class_id)->first();
            if($class_data) {
                $class_data_status   = ($class_data->status=='0'?'1':'0');
                $update_class = DB::table('class')
                                    ->where('class_id', $class_id)
                                    ->update(['status'=>$class_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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

    public function postClassDelete(Request $request) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();
            $rules  = array(
                            '_ticket'    => 'required|numeric'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                echo '0';
            }
            else {
                $class_id     = $my_data['_ticket'];
                $update_class = DB::table('class')
                                    ->where('class_id', $class_id)
                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_class) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    }

}


