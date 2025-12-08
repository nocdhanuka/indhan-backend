<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\permission;
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
use App\AppMenu;
use App\AppMenuPermission;
use App\DataSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use Khill\Lavacharts\Lavacharts;
use DateTime;

use App\State;
use App\Language;
use App\ProductCropCategory;
use App\DhanukaDoctor;
use App\User;
use Session;
use App\GeneralUser;

class GeneralController extends Controller
{
    public function __construct()
    {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function addgeneral(Request $request)
    {
	$state_list = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
	$language_list  = Language::select('id', 'language')->orderBy('language', 'asc')->get();
	$category_list  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
       return View::make('backend.general.addgeneral',array('state_list'=>$state_list,'language_list'=>$language_list,'category_list'=>$category_list));
    }
    public function createGenUser(Request $request)
    {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();
		
            $rules  = array(
                             'ECN'                   => 'required|numeric',
                            'general_user_name'      => 'required|string',
                            'general_user_mobile'   => 'required|numeric',
				'designation'   => 'required', 
			'state' =>                        'required'
			);
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $ECN                      = $my_data['ECN'];
            $general_name             = $my_data['general_user_name'];
            $general_mobile_number    = $my_data['general_user_mobile'];
	    $designation    	      = $my_data['designation'];
	    $state    	              = $my_data['state'];          
            
            //print_r($state);die();
		 $adminController    = new adminController;
                    $random_string      = $adminController->generateRandomString(8);
                    $password           = \Hash::make($random_string);

		$gen_user_array    = array(
                                                    'ECN'                   => $ECN,
                                                    'dash'                  => $random_string,
                                                    'password'              => $password,
                                                    'general_user_name'    => $general_name,
                                                    'general_user_mobile'  => $general_mobile_number,
					 	    'designation'          => $designation,
						    'state'		   => $state,
                                                    'status'                => '1'
                                                );   
                    $insert_gen_user   = GeneralUser::firstOrCreate($gen_user_array);
                    $result                 = $insert_gen_user;

                if ($result) {
                    Session::flash('message', "General User Successfully Created.");
                    return redirect('admin/gen-list');
                } else {
                    Session::flash('message', "An error occurred. Please try again later.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            
        } 
        return View::make('backend.general.addgeneral');
    }
    public function generateRandomString($length = 8) {
            $characters = '0123456789ABCDEFGHJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
    public function generalUserList(Request $request)
    {
        return view::make('backend.general.genlist');
    }
    public function getAllUserList(Request $request)
    {
        try{
		$data = $request->json()->all();
		 $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                SELECT g.general_user_name, g.general_user_mobile,g.designation,s.state,g.ecn,g.dash,(CASE WHEN g.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,g.general_id
						FROM general g
                                            	LEFT JOIN states s ON g.state=s.state_id)   A,
                                (select @a:=0) initvars";
            $genUser  = collect(DB::select($query));
            return Datatables::of($genUser)->make(); 	
	} catch (Exception $e) { 
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }
    public function editGenUser(Request $request,$id)
    {
	$sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
	$generalUser = GeneralUser::where('general_id', $id)->first();
	$state_list = State::where('status', '1')->select('state_id', 'state')->orderBy('state', 'asc')->get();
	$language_list  = Language::select('id', 'language')->orderBy('language', 'asc')->get();
	$category_list  = ProductCropCategory::where('status', '1')->select('product_crop_category_id', 'category_name')->orderBy('category_name', 'asc')->get();
       return View::make('backend.general.editgen',array('state_list'=>$state_list,'language_list'=>$language_list,'category_list'=>$category_list,'generalUser'=>$generalUser));

            //return View::make('backend.general.editgen');
        }
        else {
            return redirect('/');
        }	
    }
    public function updateUser(Request $request)
    {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();
		
            $rules  = array(
                            'general_user_name'      => 'required|string',
                            'general_user_mobile'   => 'required|numeric',
				'designation'   => 'required', 
				'state' =>                        'required'
			);
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            $gen_id		      = $my_data['gen_id'];
            $general_name             = $my_data['general_user_name'];
            $general_mobile_number    = $my_data['general_user_mobile'];
	    $designation    	      = $my_data['designation'];
	    $state    	              = $my_data['state'];          
            
            //print_r($state);die();
		 $adminController    = new adminController;
                    $random_string      = $adminController->generateRandomString(8);
                    $password           = \Hash::make($random_string);

		  $gen_user_array    = array(
                                                    'general_user_name'    => $general_name,
                                                    'general_user_mobile'  => $general_mobile_number,
					 	    'designation'          => $designation,
						    'state'		   => $state,
                                                    'status'                => '1'
                                                );   

                    $result                 =  DB::table('general')
                                                ->where('general_id', $gen_id)
                                                ->where('status', '1')
                                                ->update($gen_user_array); 
                if ($result) {
                    Session::flash('message', "General User Successfully Updated.");
                    return redirect('admin/gen-list');
                } else {
                    Session::flash('message', "An error occurred. Please try again later.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            
        } 
        return View::make('backend.general.addgeneral');
    }

    public function updateStatus(Request $request)
    {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all(); 
            $gen_id      = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $gen_data    = GeneralUser::where('general_id', $gen_id)->first();
            if($gen_data) {
                $gen_data_status = ($gen_data->status=='0'?'1':'0');
                $update_zonal_manager      = DB::table('general')
                                                ->where('general_id', $gen_id)
                                                ->update(['status'=>$gen_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
}

