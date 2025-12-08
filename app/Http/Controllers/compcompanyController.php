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

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class compcompanyController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAddCompcompany() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.compcompany.compcompanylist', array());
    }
    public function postAllCompcompanylist(Request $request) {
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
                                    c.name,
                                    (CASE WHEN c.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(c.created_at, '%Y-%m-%d, %h:%i %c') as created_at,
                                    c.c_id
                                FROM        
                                    compcompany c
                                WHERE
                                    1=1 AND
                                    c.status<>'2'
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
                                    c.name,
                                    (CASE WHEN c.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(c.created_at, '%Y-%m-%d, %h:%i %c') as created_at,
                                    c.c_id
                                FROM        
                                    compcompany c                               
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
    public function postCreateCompcompany(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules      = array(
                            'company_name'   => 'required|unique:compcompany,name,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $company_name        = $my_data['company_name'];
            $insert_company = Classes::firstOrCreate(array('name'   => $company_name, 'status' => '1', 'created_at' => date('Y-m-d H:i:s')));
            $result       = $insert_company->id;
            if($result) {
                Session::flash('message', "Company Successfully Created.");
                return redirect('compcompany/add-compcompany');             
            }
            else {
                Session::flash('message', "An error occurred. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  
    
    public function getEditCompcompany($class_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($compcompany_id!='') {
                $compcompany_detail = Classes::where('c_id', $compcompany_id)->where('status', '!=', '2')->first();
                return View::make('backend.Compcompany.editCompcompany', array('compcompany_detail'=>$compcompany_detail));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateCompcompany(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'c_id'    => 'required|numeric',
                            'company_name'  => 'required|unique:compcompany,name,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            else {
                $c_id     = $my_data['c_id'];
                $company_name   = $my_data['company_name'];
                $update_class = DB::table('Compcompany')
                                    ->where('c_id', $c_id)
                                    ->where('status', '!=', '2')
                                    ->update(['name'=>$company_name, 'updated_at' => date('Y-m-d H:i:s')]);              
                Session::flash('message', "Company Successfully Updated.");
                return redirect('compcompany/add-compcompany');
            }
        }
    } 
    public function postUpdateCompcompanyStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $c_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $compcompany_data  = Classes::where('c_id', $c_id)->first();
            if($compcompany_data) {
                $compcompany_data_status   = ($compcompany_data->status=='0'?'1':'0');
                $update_class = DB::table('compcompany')
                                    ->where('c_id', $class_id)
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


