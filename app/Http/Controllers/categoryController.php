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
use App\ProductCropCategory;
use App\Ticket;
use App\Farmer;
use App\Language;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class categoryController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAddCategory() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.category.categorylist', array());
    }
    public function postAllcategorylist(Request $request) {
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
                                    p.category_name,
                                    (CASE WHEN p.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(p.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    p.product_crop_category_id
                                FROM        
                                    product_crop_categories p                               
                                WHERE
                                    1=1 AND
                                    p.status<>'2'
                                ORDER BY
                                    created_at DESC
                                )   A,
                                (select @a:=0) initvars";
            $categorys   = collect(DB::select($query));  
            return Datatables::of($categorys)->make();
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
                                    p.category_name,
                                    (CASE WHEN p.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(p.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    p.product_crop_category_id
                                FROM        
                                    product_crop_categories p                               
                                WHERE
                                    1=1 AND
                                    p.status<>'2'
                                ORDER BY
                                    created_at DESC
                                )   A,
                                (select @a:=0) initvars";
            $categorys  = collect(DB::select($query));
            return Datatables::of($categorys)->make();            
        }
    }   
    public function postCreateCategory(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules      = array(
                            'category_name'   => 'required|unique:product_crop_categories,category_name,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $category_name      = $my_data['category_name'];
            $insert_category    = ProductCropCategory::firstOrCreate(array('category_name'   => $category_name, 'status' => '1', 'created_at' => date('Y-m-d H:i:s')));
            $result             = $insert_category->id;
            if($result) {
                Session::flash('message', "Category Successfully Created.");
                return redirect('category/add-category');             
            }
            else {
                Session::flash('message', "An error occurred. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  

    public function getEditCategory($category_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($category_id!='') {
                $category_detail = ProductCropCategory::where('product_crop_category_id', $category_id)->where('status', '!=', '2')->first();
                return View::make('backend.category.editcategory', array('category_detail'=>$category_detail));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateCategory(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'category_id'    => 'required|numeric',
                            'category_name'  => 'required|unique:product_crop_categories,category_name,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            else {
                $category_id     = $my_data['category_id'];
                $category_name   = $my_data['category_name'];
                $update_category = DB::table('product_crop_categories')
                                    ->where('product_crop_category_id', $category_id)
                                    ->where('status', '!=', '2')
                                    ->update(['category_name'=>$category_name, 'updated_at' => date('Y-m-d H:i:s')]);              
                Session::flash('message', "Category Successfully Updated.");
                return redirect('category/add-category');
            }
        }
    } 
    public function postUpdateCategoryStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $category_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $category_data  = ProductCropCategory::where('product_crop_category_id', $category_id)->first();
            if($category_data) {
                $category_data_status   = ($category_data->status=='0'?'1':'0');
                $update_category        = DB::table('product_crop_categories')
                                            ->where('product_crop_category_id', $category_id)
                                            ->update(['status'=>$category_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    
    

    public function postCategoryDelete(Request $request) {         
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
                $category_id     = $my_data['_ticket'];
                $update_category = DB::table('product_crop_categories')
                                    ->where('product_crop_category_id', $category_id)
                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_category) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    } 
}

