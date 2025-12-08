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

class languageController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAddLanguage() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.language.languagelist', array());
    }
    public function postAlllanguagelist(Request $request) {
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
                                    p.language,
                                    (CASE WHEN p.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(p.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    p.id
                                FROM        
                                    languages p                               
                                WHERE
                                    1=1 AND
                                    p.status<>'2'
                                ORDER BY
                                    created_at DESC
                                )   A,
                                (select @a:=0) initvars";
            $languages   = collect(DB::select($query));  
            return Datatables::of($languages)->make();
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
                                    p.language,
                                    (CASE WHEN p.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(p.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    p.id
                                FROM        
                                    languages p                               
                                WHERE
                                    1=1 AND
                                    p.status<>'2'
                                ORDER BY
                                    created_at DESC
                                )   A,
                                (select @a:=0) initvars";
            $languages  = collect(DB::select($query));
            return Datatables::of($languages)->make();            
        }
    }   
    public function postCreateLanguage(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules      = array(
                            'language_name'   => 'required|unique:languages,language,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $language        = $my_data['language_name'];
            //dd($my_data);
            $insert_language = Language::firstOrCreate(array('language'   => $language, 'status' => '1', 'created_at' => date('Y-m-d H:i:s')));
            //dd($my_data);
            $result         = $insert_language->id;
            if($result) {
                Session::flash('message', "Language Successfully Created.");
                return redirect('language/add-language');             
            }
            else {
                Session::flash('message', "An error occurred. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  
    
    public function getEditLanguage($id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($id!='') {
                $language_detail = Language::where('id', $id)->where('status', '!=', '2')->first();
                return View::make('backend.language.editlanguage', array('language_detail'=>$language_detail));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateLanguage(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'id'    => 'required|numeric',
                            'language_name'  => 'required|unique:languages,language,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            else {
                $id     = $my_data['id'];
                $language_name   = $my_data['language_name'];
                $update_language = DB::table('languages')
                                    ->where('id', $id)
                                    ->where('status', '!=', '2')
                                    ->update(['language'=>$language_name, 'updated_at' => date('Y-m-d H:i:s')]);              
                Session::flash('message', "Language Successfully Updated.");
                return redirect('language/add-language');
            }
        }
    } 
    public function postUpdateLanguageStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $language_data  = Language::where('id', $id)->first();
            if($language_data) {
                $language_data_status   = ($language_data->status=='0'?'1':'0');
                $update_language = DB::table('languages')
                                    ->where('id', $id)
                                    ->update(['status'=>$language_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    
    

    public function postLanguageDelete(Request $request) {         
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
                $id     = $my_data['_ticket'];
                $update_language = DB::table('languages')
                                    ->where('id', $id)
                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_language) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    } 
}




















