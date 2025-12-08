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

class productController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAddProduct() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.product.productlist', array());
    }
     public function postAllproductlist(Request $request) {
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
        

        $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
        $end_date       = isset($value['end_date']) ?  $value['end_date']:'';
        $sorting    = '';

        $datesearch     = '';


        $sorting    = '';     
        if(!empty($search_array['value']))
        {
          
            $search     =  $search_array['value']; 
             
        }

        if($start_date!='' && $end_date!='') {
                $datesearch = "DATE(p.created_at) >= DATE('".$start_date."') AND DATE(p.created_at) <= DATE('".$end_date."')";
        }elseif($start_date!='') {
                $datesearch = "DATE(p.created_at) >= DATE('".$start_date."')";
        }
        else {
                $datesearch = "(1=1)";
        }


         

        $query      = " 
        SELECT                                 
        @a:=@a+1  as serial_number,
        A.*                                
        FROM
        (
        SELECT                                
            p.product,
            (CASE WHEN p.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
            DATE_FORMAT(p.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
            p.product_id
        FROM        
            products p                               
        WHERE ".$datesearch."
             AND
            p.status<>'2'
        ORDER BY
            created_at DESC
        )   A,
        (select @a:=0) initvars";

         $products  = collect(DB::select($query));
        
        return Datatables::of($products)->make();   
    }
    public function postCreateProduct(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules      = array(
                            'product_name'   => 'required|unique:products,product,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $product        = $my_data['product_name'];
            $insert_product = Product::firstOrCreate(array('product'   => $product, 'status' => '1', 'created_at' => date('Y-m-d H:i:s')));
            $result         = $insert_product->id;
            if($result) {
                Session::flash('message', "Product Successfully Created.");
                return redirect('product/add-product');             
            }
            else {
                Session::flash('message', "An error occurred. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  
    
    public function getEditProduct($product_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($product_id!='') {
                $product_detail = Product::where('product_id', $product_id)->where('status', '!=', '2')->first();
                return View::make('backend.product.editproduct', array('product_detail'=>$product_detail));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateProduct(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'product_id'    => 'required|numeric',
                            'product_name'  => 'required|unique:products,product,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            else {
                $product_id     = $my_data['product_id'];
                $product_name   = $my_data['product_name'];
                $update_product = DB::table('products')
                                    ->where('product_id', $product_id)
                                    ->where('status', '!=', '2')
                                    ->update(['product'=>$product_name, 'updated_at' => date('Y-m-d H:i:s')]);              
                Session::flash('message', "Product Successfully Updated.");
                return redirect('product/add-product');
            }
        }
    } 
    public function postUpdateProductStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $product_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $product_data  = Product::where('product_id', $product_id)->first();
            if($product_data) {
                $product_data_status    = ($product_data->status=='0'?'1':'0');
                $update_product         = DB::table('products')
                                            ->where('product_id', $product_id)
                                            ->update(['status'=>$product_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
    
    public function postProductDelete(Request $request) {         
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
                $product_id     = $my_data['_ticket'];
                $update_product = DB::table('products')
                                    ->where('product_id', $product_id)
                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_product) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    } 
    
}



