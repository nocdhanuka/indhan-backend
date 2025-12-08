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
use App\Product;
use App\Crop;
use App\Ticket;
use App\Farmer;
use App\Language;
use App\Village;
use App\territories;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;



class territoryController extends Controller
{
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function getAddTerritory() {
        //  echo"hear";die;
        $sess = \Session::get('adminid');
        // dd($sess);
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $state_detail       = State::get();
        return View::make('backend.territory.add-territory',array('state_detail'=>$state_detail));
    }
    public function postTerritoryDistrict(Request $request) {
        //  echo"hear";die;
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();     
            $state          = $my_data['state_name'];   
            $district_list  = District::select('district_id AS dId', 'district AS dName')->where('state_id', $state)->where('status', '1')->orderBy('district', 'ASC')->get()->toArray();
            //dd($district_list);
            if(count($district_list)>0) {
                echo json_encode($district_list, true);
            }
            
        }
    }     
    
    public function postCreateTerritory(Request $request) {
        //  echo"one";die;
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            // print_r($my_data);die;    
            $state      = $my_data['state_name'];
            $district   = $my_data['district_name'];   
            $territory  = $my_data['territory_name'];
            // print_r($state);die;
            // dd($userData);   
            $rules      = array(
                            'state_name'    => 'required',
                            'district_name' => 'required',
                            'teritory_name'   => 'required|unique:territories,territory,$district,district_id|string',
                        );
                        // echo"two";die;
            if($state!='' && $district!='') {
                //  echo"THree";die;
                $district_list  = District::select('district_id AS dId', 'district AS dName')->where('state_id', $state)->where('status', '1')->orderBy('district', 'ASC')->get()->toArray();
                $my_data['district_list'] = $district_list;
            }
            else {
            //    echo"in";die;
                $my_data['district_list'] = array();
                // print_r($my_data);die;
            }
            // echo"out";die;
            $validator  = Validator::make($my_data, $rules);
            // echo"hear out side";die;
            if ($validator->fails()) {
            //   echo"inside validator";die;
                    //  return Redirect::back()->withInput($my_data)->withErrors($validator)->with('district', $district);
              
            //    echo"not in validator";die;
            $insert_territory = territories ::firstOrCreate(array('district_id' => $district,'territory'=> $territory, 'status' => '1', 'created_at' => date('Y-m-d H:i:s')));
            // echo"after query";die;
            // print_r($insert_territory);die;
            $result         = $insert_territory->id;
            //  echo"three";die;
            if($result) {
                Session::flash('message', "Territory Successfully Createt.");
                return redirect('territory/add-territory');             
            }
            else {
                Session::flash('message', "An error occurret. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
         }
        }        // echo"hear";die;
       
    }
    public function getEditTerritory($territory_id) {
        // echo"hear";die; 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($territory_id!='') {
                $state          = State::get();
                $territory      = territories::where('territory_id', $territory_id)->where('status', '!=', '2')->first();
                if($territory->district_id !=0) {
                    $district_ids   = 1;
                    $district       = District::where('district_id', $territory->district_id)->where('status', '!=', '2')->first();
                    $all_districts  = District::where('state_id', $district->state_id)->where('status', '!=', '2')->get();
                } else {
                    $district       = 0;
                    $district_ids   = 0;
                    $all_districts  = District::where('status', '!=', '2')->get();
                }
                // dd($all_districts);
                return View::make('backend.territory.editterritory', array('state_detail'=>$state, 'all_districts'=>$all_districts, 'district_detail'=>$district, 'district_ids'=>$district_ids, 'territory_detail'=>$territory));   
            }
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function postUpdateTerritory(Request $request) {
        // echo"iam";die; 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();  
            // print_r($my_data);die;   
            $state      = $my_data['state_name'];
            $district   = $my_data['district_name'];   
            $territory     = $my_data['territory_name'];   
            
            /*$rules      = array(
                            'state_name'    => 'required',
                            'district_name' => 'required',
                            'territory_name'   => 'required|unique:territories,territory,$district,district_id|string',
                        );*/
            $rules      = array(
                            'territory_name'    => 'required'
                        );
            // echo"test";die;
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
                    // echo"come ";die;    
            $territory_id     = $my_data['territory_id'];
            $territory_name   = $my_data['territory_name'];
            $update_territory = DB::table('territories')
                                ->where('territory_id', $territory_id)
                                ->where('status', '!=', '2')
                                ->update(array('district_id' => $district, 'territory'   => $territory, 'status' => '1', 'updated_at' => date('Y-m-d H:i:s')));              

            Session::flash('message', "TerritorySuccessfully Updatet.");
            return redirect('territory/add-territory');
                
            if($result) {
                Session::flash('message', "Territory Successfully Createt.");
                return redirect('territory/add-terrytory');             
            }
            else {
                Session::flash('message', "An error occurret. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
  }
  public function postAllTerritorylist(Request $request) {
    //   echo"one";die;
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
        // print_r($state_name);die;
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
                                    t.territory,
                                    (CASE WHEN t.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(t.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                                    t.territory_id
                                FROM        
                                territories t  
                                LEFT JOIN districts d ON d.district_id=t.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id
                                WHERE
                                    ".$categorysearch." AND
                                    t.status<>'2'
                                ORDER BY
                                    t.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.territory ASC
                                )   A,
                                (select @a:=0) initvars";
            $territories = collect(DB::select($query));
            // echo "hear again";die;  
            return Datatables::of($territories)->make();
        }
        else 
        {
            $search     =  $search_array['value']; 
            $categorysearch     = '(1=1)';
            if($search!='') {
                $categorysearch     .= "AND (t.territory LIKE '%".$search."%')";
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
                                    t.territory,
                                    (CASE WHEN t.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(t.created_at, '%Y-%m-%d, %h:%i %p') as created_at,
                                    t.territory_id
                                FROM        
                                territories t  
                                LEFT JOIN districts d ON d.district_id=t.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id                        
                                WHERE
                                    ".$categorysearch." AND
                                    t.status<>'2'
                                ORDER BY
                                    t.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.territory ASC
                                )   A,
                                (select @a:=0) initvars";
            $territories  = collect(DB::select($query));
            return Datatables::of($territories)->make();            
        }
    }
    public function postUpdateTerritoryStatus(Request $request) {         
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $territory_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $territory_data  = territories::where('territory_id', $territory_id)->first();
            if($territory_data) {
                $territory_data_status   = ($territory_data->status=='0'?'1':'0');
                $update_territory = DB::table('territories')
                                    ->where('territory_id', $territory_id)
                                    ->update(['status'=>$territory_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
