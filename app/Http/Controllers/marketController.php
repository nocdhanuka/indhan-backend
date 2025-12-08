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
use App\markets;
use App\territories;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class marketController extends Controller
{
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function getAddMarket() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $state_detail       = State::get();
        return View::make('backend.market.marketlist',array('state_detail'=>$state_detail));
    }
    
    public function postCreateMarket(Request $request) {
        //  echo"hear";die;
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        // print_r($userData);die; 
        if ($userData) {
            $my_data    = Input::all();    
                // print_r($my_data);die;
            $state      = $my_data['state_name'];
            $district   = $my_data['district_name'];   
            $territory    = $my_data['territory_name'];
            $market     = $my_data['market_name'];
            $rules      = array(
                            'state_name'    => 'required',
                            'district_id' => 'required',
                            'territory_id'   => 'required',
                            'market_name'   => 'required|string'
                        );
                        // print_r($rules);die;
                        
            $marketdata = array(
                                'district_id' => $district,
                                'territory_id'   => $territory,
                                'market'   => $market,
                                'status'    => '1',
                                'created_at' => date('Y-m-d H:i:s')
                                );
                                    // print_r($marketdata);die;
                                //  dd($marketdata); 
            $insert_market = markets::firstOrCreate($marketdata);
           
            $result         = $insert_market->market_id;
            if($result) {
                Session::flash('message', "market Successfully Createt.");
                return redirect('market/add-market');             
            } else {
                return redirect('market/add-market');
            }
        }
    }
 
    public function getEditMarket($market_id) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            if($market_id!='') {
                $state          = State::get();
                $market         = markets::where('market_id', $market_id)->where('status', '!=', '2')->first();
                if($market->district_id!=0) {
                    $district_ids   = 1;
                    $district       = District::where('district_id', $market->district_id)->where('status', '!=', '2')->first();
                    $all_districts  = District::where('state_id', $district->state_id)->where('status', '!=', '2')->get();
                    $territory      = territories::where('territory_id', $market->territory_id)->where('status', '!=', '2')->first();
                    $all_territory  = territories::where('district_id', $district->district_id)->where('status', '!=', '2')->get();
                } else {
                    $district       = 0;
                    $territory      = 0;
                    $district_ids   = 0;
                    $all_districts  = District::where('status', '!=', '2')->get();
                    $all_territory  = territories::where('status', '!=', '2')->get();
                }
                return View::make('backend.market.editmarket', array('state_detail'=>$state, 'all_districts'=>$all_districts, 'district_detail'=>$district, 'all_territory'=>$all_territory, 'territory_detail'=>$territory, 'market_detail' => $market,'district_ids'=>$district_ids));   
            } else {
                return redirect('/');
            }
        } else {
            return redirect('/');
        }
    }

    public function postUpdateMarket(Request $request) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data    = Input::all();     
            $state      = $my_data['state_name'];
            $district   = $my_data['district_name'];   
            $territory  = $my_data['territory_name'];
            $market     = $my_data['market_name']; 
            $market_id  = $my_data['market_id'];
            $rules      = array(
                            'state_name'    => 'required',
                            'district_id' => 'required',
                            'territory_id'   => 'required',
                            'market_name'   => 'required|string',
                            'market_id' => 'required'
                        );
                    
            $update_market = DB::table('markets')
                                ->where('market_id', $market_id)
                                ->update(array('district_id' => $district, 'territory_id'   => $territory, 'status' => '1', 'updated_at' => date('Y-m-d H:i:s'), 'market' => $market));
            if($update_market) {
                Session::flash('message', "market Successfully Updatet.");
                return redirect('market/add-market');              
            } else {
                Session::flash('message', "An error occurret. Please try again later.");
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }

    public function postAllMarketlist(Request $request) {
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

        if(empty($search_array['value'])) {
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
                                    m.market,
                                    (CASE WHEN m.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(m.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                                    m.market_id
                                FROM        
                                    markets m  
                                LEFT JOIN territories t ON t.territory_id=m.territory_id
                                LEFT JOIN districts d ON d.district_id=m.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id
                                
                                WHERE
                                    ".$categorysearch." AND
                                    m.status<>'2'
                                ORDER BY
                                    m.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.territory ASC,
                                    m.market ASC
                                )  A,
                                (select @a:=0) initvars";
            $markets   = collect(DB::select($query));
            return Datatables::of($markets)->make();
        } else {
            $search     =  $search_array['value']; 
            $categorysearch     = '(1=1)';
            if($search!='') {
                $categorysearch     .= "AND (m.market LIKE '%".$search."%')";
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
                                    m.market,
                                    (CASE WHEN m.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                    DATE_FORMAT(m.created_at, '%Y-%m %d, %h:%i %p') as created_at,
                                    m.market_id
                                FROM        
                                    markets m   
                                LEFT JOIN territories t ON t.territory_id=m.territory_id
                                LEFT JOIN districts d ON d.district_id=m.district_id
                                LEFT JOIN states s ON s.state_id=d.state_id
                                
                                WHERE
                                    ".$categorysearch." AND
                                    m.status<>'2'
                                ORDER BY
                                    m.created_at DESC,
                                    s.state ASC,
                                    d.district ASC,
                                    t.territory ASC,
                                    m.market ASC
                                )   A,
                                (select @a:=0) initvars";
            $markets  = collect(DB::select($query));
            // echo"hear we done";die;
            return Datatables::of($markets)->make();            
        }
    }   

    public function postImportMarkets(Request $request){   
        $my_data  = Request::all();
        $rules  = array(
                    'marketdata' => 'required'
                );
        $validator  = Validator::make($my_data, $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        if($my_data['marketdata']){
            $path1 = Request::file('marketdata')->getRealPath();
            // echo "<pre>"; print_r($path1); die;
            // $data = Excel::load($path)->get();
            $data = Excel::load($path1)->get();
            //echo "<pre>";print_r($data);die;
            $ExcelArr = array();
            if($data->count() > 0) {
                 foreach ($data as $key => $value) {
                    //echo "<pre>"; print_r($value->district_id); die;
                    $ExcelArr['district_id'] = $value->district_id;
                    $ExcelArr['territory_id'] = $value->territory_id;
                    $ExcelArr['market'] = $value->market;
                    $ExcelArr['created_at'] = date('Y-m-d H:i:s');
                    $ExcelArr['updated_at'] = date('Y-m-d H:i:s');
                    // echo "<pre>"; print_r($ExcelArr); die;
                    $market = DB::table('markets')
                                ->where('markets.district_id',$value->district_id)
                                ->where('markets.territory_id',$value->territory_id)
                                ->where('markets.market',$value->market)
                                ->select('markets.*')
                                ->count();
                    if ($market == 0) { 
                        market::insert($ExcelArr);
                    }
                 }
            }

        }
        return back()->with('success','Excel Data imported successfully');
    }

    public function postMarketDistrict(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data        = Input::all();
            $district_name          = $my_data['district_name']; 
            $tehsil_list  = territories::select('territory_id AS tId', 'territory AS tName')->where('district_id', $district_name)->where('status', '1')->get()->toArray();
            // echo "<pre>";print_r($tehsil_list);exit();
            //dd($district_list);
            if(count($tehsil_list)>0) {
                echo json_encode($tehsil_list, true);
            }
            
        }
    }

    public function postMarketTerritory(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data        = Input::all();
            $territory_name = $my_data['territory_name'];   
            $market_list  = markets::select('market_id AS mId', 'market AS mName')->where('territory_id', $territory_name)->where('status', '1')->get()->toArray();
            if(count($market_list)>0) {
                echo json_encode($market_list, true);
            }
        }
    }  
}
