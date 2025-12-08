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

class cropController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getAddCrop() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.crop.croplist', array());
    }
    public function postAllcroplist(Request $request) {
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
                                    p.crop,
                                    (CASE WHEN p.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(p.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    p.crop_id
                                FROM        
                                    crops p                               
                                WHERE
                                    1=1 AND
                                    p.status<>'2'
                                ORDER BY
                                    created_at DESC
                                )   A,
                                (select @a:=0) initvars";
            $crops   = collect(DB::select($query));  
            return Datatables::of($crops)->make();
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
                                    p.crop,
                                    (CASE WHEN p.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(p.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    p.crop_id
                                FROM        
                                    crops p                               
                                WHERE
                                    1=1 AND
                                    p.status<>'2'
                                ORDER BY
                                    created_at DESC
                                )   A,
                                (select @a:=0) initvars";
            $crops  = collect(DB::select($query));
            return Datatables::of($crops)->make();            
        }
    }   
    public function postCreateCrop(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules      = array(
                            'crop_name'   => 'required|unique:crops,crop,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $crop        = $my_data['crop_name'];
            $insert_crop = Crop::firstOrCreate(array('crop'   => $crop, 'status' => '1', 'created_at' => date('Y-m-d H:i:s')));
            $result         = $insert_crop->id;
            if($result) {
                Session::flash('message', "Crop Successfully Created.");
                return redirect('crop/add-crop');             
            }
            else {
                Session::flash('message', "An error occurred. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }  
    
    public function getEditCrop($crop_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($crop_id!='') {
                $crop_detail = Crop::where('crop_id', $crop_id)->where('status', '!=', '2')->first();
                return View::make('backend.crop.editcrop', array('crop_detail'=>$crop_detail));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }
    public function postUpdateCrop(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Input::all();        
            $rules  = array(
                            'crop_id'    => 'required|numeric',
                            'crop_name'  => 'required|unique:crops,crop,2,status|string',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            else {
                $crop_id     = $my_data['crop_id'];
                $crop_name   = $my_data['crop_name'];
                $update_crop = DB::table('crops')
                                    ->where('crop_id', $crop_id)
                                    ->where('status', '!=', '2')
                                    ->update(['crop'=>$crop_name, 'updated_at' => date('Y-m-d H:i:s')]);              
                Session::flash('message', "Crop Successfully Updated.");
                return redirect('crop/add-crop');
            }
        }
    } 
    public function postUpdateCropStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $crop_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $crop_data  = Crop::where('crop_id', $crop_id)->first();
            if($crop_data) {
                $crop_data_status   = ($crop_data->status=='0'?'1':'0');
                $update_crop = DB::table('crops')
                                    ->where('crop_id', $crop_id)
                                    ->update(['status'=>$crop_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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

    public function postCropDelete(Request $request) {         
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
                $crop_id     = $my_data['_ticket'];
                $update_crop = DB::table('crops')
                                    ->where('crop_id', $crop_id)
                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_crop) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    }


    public function getAddStage() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.crop.addstage', array());
    }
    public function postCreateStage(Request $request) {
       $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Request::all(); 
            
            DB::table('stages')->insert(['stage_name'=>$my_data['stage_name'],'stage_start_date'=>$my_data['stage_start_date'],'stage_end_date'=>$my_data['stage_end_date'],'updated_at'=>date("Y-m-d H:i:s")]);
            Session::flash('message', "Stage Successfully Created.");
            return redirect('crop/stage-list');
            
        }else{
            return redirect('crop/add-stage');
            
        }
    }
     public function getStageList()
    {
        return view('backend.crop.stagelist');       
    }

    public function postStageList(){
        
        $stageList="select stage_id,stage_name,stage_start_date,stage_end_date from stages WHERE status =1";
        //echo $distributorList;exit;                  
         $stageList  = collect(DB::select($stageList));
         return Datatables::of($stageList)->make();                             
         //return $distributorList;                             
    }
    public function postStageDelete(Request $request){
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Request::all();        
            $rules  = array(
                            '_ticket'    => 'required|numeric'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                echo '0';
            }
            else {
                $distributor_id     = $my_data['_ticket'];
                $update_distributor = DB::table('stages')
                                    ->where('stage_id', $distributor_id)
                                    ->update(['status' => '2']);
                if ($update_distributor) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
    }

    public function getEditStage($id){
        $stage = DB::table('stages')
                                    ->where('stage_id', $id)
                                    ->first();
         return view('backend.crop.editstage',compact('stage'));
         //return $distributorList;                             
    }
    public function postUpdateStage(Request $request){
        
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= Request::all();
            $distId=Request::input('stageId');
            DB::table('stages')->where('stage_id',$distId)->update(['stage_name'=>$my_data['stage_name'],'stage_start_date'=>$my_data['stage_start_date'],'stage_end_date'=>$my_data['stage_end_date'],'updated_at'=>date("Y-m-d H:i:s")]);
            Session::flash('message', "Stage Successfully Updated.");
            return redirect('crop/stage-list');
            
        }else{
            return redirect('distributor/edit-stage/'.$distId);
            
        }

    }

}


