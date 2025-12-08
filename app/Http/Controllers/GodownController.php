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
use App\Godown;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\adminController;

class GodownController extends Controller {

    public function __construct() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function getAddGodown() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $state_detail   = State::get();
        return View::make('backend.godown.godownlist', array('state_detail'=>$state_detail));
    }

    public function postAllGodownlist(Request $request) {
        // echo "Called!"; exit();
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
        
        $addQryList = '(1=1)';
        if(empty($search_array['value'])) {
            if($state_name!='') {
                $addQryList .= "AND (s.state_id='".$state_name."')";
            }
        } else {
            $search = $search_array['value']; 
            if($search!='') {
                $addQryList .= "AND (g.godown LIKE '".$search."%')";
            }
        }
        $query  = " 
                    SELECT                                 
                        @a:=@a+1  as serial_number,
                        A.*                                
                    FROM
                        (
                        SELECT        
                            s.state,
                            d.district,
                            g.godown,
                            g.contact_person,
                            g.contact_number,
                            (CASE WHEN g.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                            DATE_FORMAT(g.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                            g.godown_id
                        FROM
                            godown g
                        LEFT JOIN districts d ON d.district_id=g.district_id
                        LEFT JOIN states s ON s.state_id=g.state_id
                        WHERE
                            ".$addQryList." AND
                            g.status<>'2'
                        ORDER BY
                            g.created_at DESC,
                            s.state ASC,
                            d.district ASC,
                            g.godown ASC
                        )  A,
                        (select @a:=0) initvars";
        $villages   = collect(DB::select($query));  
        return Datatables::of($villages)->make();
    }
    
    public function postCreateGodown(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();
            // echo "<pre>"; print_r($my_data); exit();

            $state      = $my_data['state_name'];
            $district   = $my_data['district_name'];

            $rules      = array(
                            'state_name'    => 'required',
                            'district_name'   => 'required',
                            'godown_name'  => 'required|string',
                            'contact_person'  => 'required',
                            'contact_number' => 'required'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput($my_data)->withErrors($validator)->with('district_name', $district);
            }
            $godown     = $my_data['godown_name'];   
            $contact_person   = $my_data['contact_person'];
            $contact_number  = $my_data['contact_number'];

            $godowndata = array(
                                'state_id'  => $state,
                                'district_id' => $district,
                                'godown'      => $godown,
                                'contact_person' => $contact_person,
                                'contact_number' => $contact_number,
                                'status'    => '1',
                                'created_at'=> date('Y-m-d H:i:s'),
                                'updated_at'=> date('Y-m-d H:i:s')
                            );
            // echo "<pre>"; print_r($godowndata); exit();
            $insert_godown = Godown::firstOrCreate($godowndata);
            $result        = $insert_godown->godown_id;
            if($result) {
                Session::flash('message', "Godown Successfully Created.");
                return redirect('godown/add-godown');
            } else {
                return redirect('godown/add-godown');
            }
        }
    }
    
    public function getEditGodown($godown_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($godown_id!='') {
                $state          = State::get();
                $godown         = Godown::where('godown_id', $godown_id)->where('status', '!=', '2')->first();
                if($godown->district_id != 0) {
                    $district       = District::where('district_id', $godown->district_id)->where('status', '!=', '2')->first();
                    $all_districts  = District::where('state_id', $district->state_id)->where('status', '!=', '2')->get();
                } else {
                    $district       = 0;
                    $all_districts  = District::where('status', '!=', '2')->get();
                }
                return View::make('backend.godown.editgodown', array('state_detail'=>$state, 'all_districts'=>$all_districts, 'district_detail'=>$district, 'godown_detail' => $godown));
            } else {
                return redirect('/');
            }
        } else {
            return redirect('/');
        }
    }

    public function postUpdateGodown(Request $request) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();
            
            $rules      = array(
                            'state_name'    => 'required',
                            'district_name'   => 'required',
                            'godown_name'  => 'required|string',
                            'contact_person'  => 'required',
                            'contact_number' => 'required',
                            'godown_id' => 'required',
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput($my_data)->withErrors($validator);
            }

            $state      = $my_data['state_name'];
            $district   = $my_data['district_name'];
            $godown_name    = $my_data['godown_name'];
            $contact_person = $my_data['contact_person'];
            $contact_number = $my_data['contact_number'];
            $godown_id  = $my_data['godown_id'];
                        
            $update_godown = DB::table('godown')
                                ->where('godown_id', $godown_id)
                                ->update(array('state_id' => $state, 'district_id' => $district, 'status' => '1', 'updated_at' => date('Y-m-d H:i:s'), 'godown' => $godown_name, 'contact_person' => $contact_person, 'contact_number' => $contact_number));              
            if($update_godown) {
                Session::flash('message', "Godown Successfully Updated.");
                return redirect('godown/add-godown');              
            } else {
                Session::flash('message', "An error occurret. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }

    public function postUpdateGodownStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $godown_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $godown_data  = Godown::where('godown_id', $godown_id)->first();
            if($godown_data) {
                $godown_data_status   = ($godown_data->status=='0'?'1':'0');
                $update_godown = DB::table('godown')
                                    ->where('godown_id', $godown_id)
                                    ->update(['status'=>$godown_data_status, 'updated_at' => date('Y-m-d H:i:s')]);
                echo '1';
            } else {
                echo '0';
            }
        } else {
            echo '0';
        }
    } 
    
    public function postImportGodowns(Request $request) {   
        $my_data  = Request::all();      
        $rules  = array(
                        'godowndata' => 'required'
                    );
        $validator  = Validator::make($my_data, $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        if($my_data['godowndata']){
            $path1 = Request::file('godowndata')->getRealPath();
            // echo "<pre>"; print_r($path1); die;
            // $data = Excel::load($path)->get();
            $data = Excel::load($path1)->get();
            // echo "<pre>";print_r($data);die;
            $ExcelArr = array();
            if($data->count() > 0) {
                 foreach ($data as $key => $value) {
                    $ExcelArr['state_id']       = $value->state_id;
                    $ExcelArr['district_id']    = $value->district_id;
                    $ExcelArr['godown']         = $value->godown;
                    $ExcelArr['contact_person'] = $value->contact_person;
                    $ExcelArr['contact_number'] = $value->contact_number;
                    $ExcelArr['created_at']     = date('Y-m-d H:i:s');
                    $ExcelArr['updated_at']     = date('Y-m-d H:i:s');
                    //echo "<pre>"; print_r($ExcelArr); die;
                    $godown = DB::table('godown')
                                ->where('godown.state_id',$value->state_id)
                                ->where('godown.district_id',$value->district_id)
                                ->where('godown.godown',$value->godown)
                                ->where('godown.contact_person',$value->contact_person)
                                ->where('godown.contact_number',$value->contact_number)
                                ->select('godown.*')
                                ->count();
                    if ($godown == 0) {
                        Godown::insert($ExcelArr);
                    }
                }
            }
        }
        return back()->with('success','Excel Data imported successfully');
    }

}

