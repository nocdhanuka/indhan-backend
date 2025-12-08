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
use App\ProductCropRelation;
use App\Ticket;
use App\Farmer;
use App\Language;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;
use Illuminate\Support\MessageBag;

class productCropRelationController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAddRecommendation() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $product_detail = Product::where('status', '!=', '2')->get();
        $crop_detail    = Crop::where('status', '!=', '2')->get();
        $category_detail= ProductCropCategory::where('status', '!=', '2')->get();
        return View::make('backend.product_crop_relation.recommendationlist', array('product_detail'=>$product_detail, 'crop_detail'=>$crop_detail, 'category_detail'=>$category_detail));
    }   
    public function postAllCategory() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $my_data            = Input::all();
        $category_name      = $my_data['word'];
        $category_detail    = ProductCropRelation::distinct()->where('category_name', 'like', '%'.$category_name.'%')->get(['category_name']);
        $suggestion_list    = '';
        if($category_detail) {
            $suggestion_list .= '<ul id="country-list">';
            foreach($category_detail as $categories) {
                $suggestion_list .='<li onClick="selectCategory(\''.$categories->category_name.'\')">'.$categories->category_name.'</li>';
            }
            $suggestion_list .= '</ul>';
        }
        echo $suggestion_list;
    }
    public function postCreateRecommendation(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();  
            $rules  = array(
                            'category_name' => 'required',
                            'product_name'  => 'required',
                            'crop_name'     => 'required|array'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $category_name  = $my_data['category_name'];
            $product_name   = $my_data['product_name'];
            $crop_name      = $my_data['crop_name'];
            $utilization_Per_demo = $my_data['utilization_Per_demo'];
            if(count($crop_name)>0) {
                    $recommendation   = array();
                    foreach($crop_name AS $crop) {
                        $recommendation[]   = array(
                                                'product_crop_category_id'  => $category_name,
                                                'product_id'                => $product_name,
                                                'crop_id'                   => $crop,
                                                'utilization_Per_demo'      => $utilization_Per_demo,
                                                'status'                    => '1',
                                                'created_at'                => date('Y-m-d H:i:s')
                                                );
                    }                    
                    $insert_recommendation  = ProductCropRelation::insert($recommendation);
                    if($insert_recommendation){
                        Session::flash('message', "Product Crop Recommendation Created.");
                        return redirect('recommendation/add-recommendation');
                    }
                    else {
                        Session::flash('message', "An error occurred while inserting states. Please try again later.");
                        return Redirect::back()->withInput()->withErrors($validator);
                    }
            }
            else {
                Session::flash('message', "Please choose crop and try again.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    } 
    public function postAllrecommendationlist(Request $request) {
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
        
        $category_name  = $value['category_name'];
        $product_name   = $value['product_name'];
        
        if(empty($search_array['value']))
        {
            $categorysearch     = '(1=1)';
            if($category_name!='') {
                $categorysearch     .= "AND (pcr.product_crop_category_id='".$category_name."')";
            }
            if($product_name!='') {
                $categorysearch     .= "AND (pcr.product_id='".$product_name."')";
            }
            
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT
                                    pcc.category_name,
                                    p.product,
                                    c.crop,
                                    (CASE WHEN pcr.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(pcr.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    pcr.product_crop_relation_id,
                                    pcr.utilization_Per_demo
                                FROM        product_crop_relations pcr
                                LEFT JOIN   products p ON pcr.product_id=p.product_id
                                LEFT JOIN   crops c ON pcr.crop_id=c.crop_id
                                LEFT JOIN   product_crop_categories pcc ON pcr.product_crop_category_id=pcc.product_crop_category_id
                                WHERE
                                    ".$categorysearch." AND
                                    pcr.status='1'
                                ORDER BY
                                    pcr.created_at DESC,
                                    pcc.category_name ASC,
                                    p.product ASC,
                                    c.crop ASC
                                )   A,
                                (select @a:=0) initvars";
            $products   = collect(DB::select($query));  
            return Datatables::of($products)->make();
        } else {
            $search             =   $search_array['value']; 
            $categorysearch     =   '(1=1)';
            if($category_name!='') {
                $categorysearch     .= "AND (pcr.product_crop_category_id='".$category_name."')";
            }
            if($product_name!='') {
                $categorysearch     .= "AND (pcr.product_id='".$product_name."')";
            }
            
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT
                                    pcc.category_name,
                                    p.product,
                                    c.crop,
                                    (CASE WHEN pcr.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(pcr.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    pcr.product_crop_relation_id,
                                    pcr.utilization_Per_demo
                                FROM        product_crop_relations pcr
                                LEFT JOIN   products p ON pcr.product_id=p.product_id
                                LEFT JOIN   crops c ON pcr.crop_id=c.crop_id
                                LEFT JOIN   product_crop_categories pcc ON pcr.product_crop_category_id=pcc.product_crop_category_id
                                WHERE
                                    (".$categorysearch.") AND
                                    pcr.status<>'2'
                                ORDER BY
                                    pcr.created_at DESC,
                                    pcc.category_name ASC,
                                    p.product ASC,
                                    c.crop ASC
                                )   A,
                                (select @a:=0) initvars";
            $products  = collect(DB::select($query));
            return Datatables::of($products)->make();            
        }
    } 
    
    public function getEditRecommendation($recommendation_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($recommendation_id!='') {
                $recommendation_detail  = ProductCropRelation::where('product_crop_relation_id', $recommendation_id)->where('status', '1')->first();
                $product_detail         = Product::where('status', '!=', '2')->get();
                $crop_detail            = Crop::where('status', '!=', '2')->get();
                $category_detail        = ProductCropCategory::where('status', '!=', '2')->get();
                return View::make('backend.product_crop_relation.editrecommendation', array('recommendation_detail'=>$recommendation_detail, 'product_detail'=>$product_detail, 'crop_detail'=>$crop_detail, 'category_detail' => $category_detail));
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateRecommendation(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();                   
            $product_crop_category_id   = $my_data['category_name'];
            $product_id                 = $my_data['product_name'];
            $crop_id                    = $my_data['crop_name'];
            //dd($my_data);
            $rules  = array(
                            'product_crop_relation_id'  => 'required',
                            'category_name'             => 'required',
                            'product_name'              => 'required',
                            'crop_name'                 => 'required',
                            'utilization_Per_demo'      => 'required'
                        );
            
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                //dd(($validator));
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $check_recommendation   = DB::table('product_crop_relations')
                                            ->where('product_crop_category_id', '=', $product_crop_category_id)
                                            ->where('product_id', '=', $product_id)
                                            ->where('crop_id', '=', $crop_id)
                                            ->where('status', '!=', '2')
                                            ->count();
                if($check_recommendation==0) {                
                    $product_crop_relation_id   = $my_data['product_crop_relation_id'];
                    $product_crop_category_id   = $my_data['category_name'];
                    $product_id                 = $my_data['product_name'];
                    $crop_id                    = $my_data['crop_name'];
                    $utilization_Per_demo       = $my_data['utilization_Per_demo'];
                    $update_recommendation      = DB::table('product_crop_relations')
                                                    ->where('product_crop_relation_id', $product_crop_relation_id)
                                                    ->update(['product_crop_category_id'=>$product_crop_category_id,'product_id'=>$product_id,'crop_id'=>$crop_id,'utilization_Per_demo'=>$utilization_Per_demo, 'updated_at' => date('Y-m-d H:i:s')]);              
                    Session::flash('message', "Relation Successfully Updated.");
                    return redirect('recommendation/add-recommendation');
                } else {
                    $errors = new \Illuminate\Support\MessageBag();
                    /*$errors->add('category_name',       $product_crop_category_id);
                    $errors->add('product_name',        $product_id);
                    $errors->add('crop_name',           $crop_id);*/
                    $errors->add('your_custom_error',   'crop name already exists.!');
                    return Redirect::back()->withInput()->withErrors($errors);
                }
            }
        }
    }

    public function postUpdateRecommendationStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            
            $product_crop_relation_id   = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $product_crop_relation      = ProductCropRelation::where('product_crop_relation_id', $product_crop_relation_id)->first();
            
            if($product_crop_relation) {
                $product_crop_relation_status   = ($product_crop_relation->status=='0'?'1':'0');
                $update_product_crop_relation   = DB::table('product_crop_relations')
                                                    ->where('product_crop_relation_id', $product_crop_relation_id)
                                                    ->update(['status'=>$product_crop_relation_status, 'updated_at' => date('Y-m-d H:i:s')]); 
                echo '1';
            }
            else {
                echo '0';
            }
        }
        else {
            echo '2';
        }
    } 
    

    public function postRecommendationDelete(Request $request) {         
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
                $product_crop_relation_id     = $my_data['_ticket'];
                $update_product_crop_relation = DB::table('product_crop_relations')
                                                ->where('product_crop_relation_id', $product_crop_relation_id)
                                                ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_product_crop_relation) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    } 
}



