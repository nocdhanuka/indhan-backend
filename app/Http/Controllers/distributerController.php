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
use App\compcompanys;
use App\distributer;
use App\territories;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;


class distributerController extends Controller
{
    public function __construct() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    public function getAddDistributor(){
		
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $state_detail       = State::get();
        $crops_detail       = Crop::get();
        $comp_detail        =compcompanys::get();
        return View('backend.distributer.adddistributer',array('state_detail'=>$state_detail, 'crops_detail'=>$crops_detail,'company'=>$comp_detail ));
    
        //return $distributorList;                             
    }
    
    public function getDistributerList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $state_detail       = State::get();
        return View::make('backend.distributer.distributer',array('state_detail'=>$state_detail));
    }
    
    public function postCreateDistributer(){
        $sess       =   \Session::get('adminid');
        // print_r($sess);die;
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data    = Input::all();
            // print_r($my_data);die;  
            $state                = $my_data['state_name'];
            $district             = $my_data['district_name'];   
            $territory            = $my_data['territory_name'];
            $distributername      = $my_data['distributername'];
            $market               = $my_data['market_name'];
            $mobilenumbmer	  = $my_data['mobilenumbmer'];
            $mobilenumber         = $my_data['mobilenumber'];
            $Adress               = $my_data['address'];
            $pincode              = $my_data['pincode'];
            // $ecnnumber            = $my_data['ecnnumber'];
            $turnover             = $my_data['turnover'];
            $distributerforname   = $my_data['Distributerfor_name'];
            $pesticide_turnover   = $my_data['pesticide_turnover'];
            $crope                = (!empty($my_data['crope'])) ? json_encode($my_data['crope']) : '';
            $brands               = (!empty($my_data['brand'])) ? json_encode($my_data['brand']) : '';

            $counter_percentage_pesticides = (!empty($my_data['counter_percentage_pesticides'])) ? $my_data['counter_percentage_pesticides'] : NULL;
                                
            $distributerforname   = $my_data['Distributerfor_name'];
            $rules = array(
                            'state_id'                =>'required',
                            'district_id'             =>'required',
                            'territory_id'            =>'required',
                            'distributername'         =>'required',
                            'market_name'             =>'required|string',
                            'mobilenumber'            =>'required',
                            'Adress'                  =>'required',
                            'pincode'                 =>'required',
                            'turnover'                =>'required',
                            'crope'                   =>'required',
                            'company'                 =>'required',                   
                            'distributerforname'      =>'required',
                            'pesticide_turnover'      =>'required'
                        );
            $distributerdata= array(
                                'state_id'                         => $state,
                                'district_id'                      => $district,
                                'territory_id'                     => $territory,
                                'distributer_name'                 => $distributername,
                                'market'                           => $market,
                                'mobilenumber'                     => $mobilenumbmer,
                                'mobile2'                          => $mobilenumber,
                                // 'ecnname'                          => $ecnnumber,
                                'Adress'                           => $Adress,
                                'addedbyecn'                       => $sess,
                                'pincode'                          => $pincode,
                                'turnover'                         => $turnover,
                                'distributerforname'               => $distributerforname,
                                'crope'                            => $crope,
                                'brands'                           => $brands,
                                'pesticide_turnover'               => $pesticide_turnover,
                                'counter_percentage_pesticides'    => $counter_percentage_pesticides,
                                'status'                           => '1',
                                'created_at'                       => date('Y-m-d H:i:s')
                            ); 
                                 
            $insert_distributer = distributer::firstOrCreate($distributerdata);
                        
            $result         = $insert_distributer->distributer_id;
            if($result) {
                Session::flash('message', "Distributer added Successfully Createt.");
                return redirect('distributer/distributer-list');             
            }
            else {
                
                return redirect('distributer/distributer-list');
            }
        }
    }

    public function getEditDistributer($id){
        //   print_r($id);die;
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            if($id!='') {
                $state          = State::get();
                $user           = distributer::select('*')->where('id',$id)->first();
                $user_market    = ($user->market) ? $user->market : 0;
                $market         = markets::where('market_id',$user_market)->where('status', '!=', '2')->first();
                
                if($market && $market->district_id) {
                    $district       = District::where('district_id', $market->district_id)->where('status', '!=', '2')->first();        
                    $all_districts  = District::where('state_id', $district->state_id)->where('status', '!=', '2')->get();
                    $all_territory  = territories::where('territory_id', $district->state_id)->where('status', '!=', '2')->get();
                    // echo "=>".gettype($district)."<==>".gettype($all_districts); exit();
                } else {
                    $district       = District::where('district_id', 0)->where('status', '!=', '2')->first();
                    $all_districts  = District::where('state_id', 0)->where('status', '!=', '2')->get();
                    $all_territory  = territories::where('territory_id', 0)->where('status', '!=', '2')->get();
                }

                if($market && $market->territory_id) {
                    $territory_detail     = territories::where('territory_id', $market->territory_id)->where('status', '!=', '2')->first();
                } else {
                    $territory_detail     = territories::where('territory_id', 0)->where('status', '!=', '2')->first();
                }

                if(!$market) {
                    $market     = markets::where('market_id', 0)->where('status', '!=', '2')->first();
                }

                $user_crop      = distributer::select('crope')->where('id',$id)->first();
                // print_r($user_crop);die;
                $crops          = Crop::where('status', '1')->select('crop_id', 'crop')->groupBy('crop_id')->orderBy('crop', 'asc')->get();        
                //  print_r($crops->crope_id);die;
                 
                $user_brand     = distributer::select('brands')->where('id',$id)->first();
                //    print_r($user_brand->brands);die;
                $brands         = compcompanys::where('status', '1')->select('c_id', 'name')->groupBy('c_id')->orderBy('name', 'asc')->get();
                    // print_r($brands->c_id);die;
                $distArray = array(
                    'state_detail'=>$state,
                    'district_detail'=>$district,
                    'all_districts'=>$all_districts,
                    'territory_detail'=>$territory_detail,
                    'all_territory'=>$all_territory,
                    'user_detail'=>$user,
                    'market_detail'=>$market,
                    'crops'=>$crops,
                    'user_crop'=>$user_crop,
                    'user_brand'=>$user_brand,
                    'brands'=>$brands
                );
                /*echo "<pre>";
                print_r($distArray);
                exit();*/
                return View::make('backend.distributer.editdistributer', $distArray);  
            }
            
            else {
                return redirect('/');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function postAllDistributerlist(Request $request) {
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

        $start_date     = $value['start_date'];
        $end_date       = $value['end_date'];

        if(empty($search_array['value']))
        {
            $sorting    = '';
            $datesearch = '';

            if($start_date!='' && $end_date!='') {
                $datesearch = "DATE(dbs.created_at) >= DATE('".$start_date."') AND DATE(dbs.created_at) <= DATE('".$end_date."')";
            } else {
                $datesearch .= "(1=1)";
            }

            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (      
                                    SELECT
                                        dbs.market as market_id,
                                        s.state,
                                        d.district,
                                        t.territory,
                                        m.market,
                                        dbs.distributer_name,
                                        dbs.mobilenumber,
                                        dbs.mobile2,
                                        dbs.Adress,
                                        dbs.pincode,
                                        dbs.turnover,
                                        dbs.addedbyecn as ecnNumber,
                                        dbs.distributerforname,
                                        (CASE WHEN dbs.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                        DATE_FORMAT(dbs.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,
                                        dbs.id,
                                        dbs.pesticide_turnover,
                                        REPLACE(REPLACE(dbs.crope,'[',''),']','') as crop,
                                        REPLACE(REPLACE(dbs.brands,'[',''),']','') as brands,
                                        dbs.counter_percentage_pesticides,
                                        dbs.connected_retailers,
                                        dbs.ratings
                                    FROM distributers dbs
                                    LEFT JOIN   states  s   ON dbs.state_id=s.state_id
                                    LEFT JOIN   districts   d   ON dbs.district_id=d.district_id
                                    LEFT JOIN   territories   t   ON dbs.territory_id=t.territory_id
                                    LEFT JOIN   markets   m   ON dbs.market=m.market_id
                                    WHERE ".$datesearch." AND
                                    dbs.status<>'2'
                                    ORDER BY
                                    dbs.created_at DESC
                                )   A,
                                (select @a:=0) initvars";
                        // echo $query; exit;
            $distributers = collect(DB::select($query));
            foreach ($distributers as $key => $value) {
                $cropIds  = $value->crop;
                $brandIds = $value->brands;
                $market_id = $value->market_id;
                if($cropIds) {
                    $qr_crops = 'SELECT GROUP_CONCAT(DISTINCT crop) as crop FROM `crops` WHERE crop_id IN ('.$cropIds.')';
                    $crops_list = collect(DB::select($qr_crops));
                    $distributers[$key]->cropname = $crops_list[0]->crop;
                } else {
                    $distributers[$key]->cropname = "";
                }
                if($brandIds) {
                    $qr_brands = 'SELECT GROUP_CONCAT(DISTINCT name) as name FROM `compcompanys` WHERE c_id IN ('.$brandIds.')';
                    $brands_list = collect(DB::select($qr_brands));
                    $distributers[$key]->brandname = $brands_list[0]->name;
                } else {
                    $distributers[$key]->brandname = "";
                }
                if($market_id) {
                    $qr_market = "SELECT ECN FROM `users` WHERE market LIKE '%".'"'.$market_id.'"'."%'";
                    $market_list = collect(DB::select($qr_market));
                    $distributers[$key]->ECN = (count($market_list)>0) ? $market_list[0]->ECN : '';
                } else {
                    $distributers[$key]->ECN = "";
                }
            }
            return Datatables::of($distributers)->make();
        }
        else
        {
            $search     = $search_array['value'];
            $sorting    = '';
            $datesearch = '(1=1)';
            if($start_date!='' && $end_date!='') {
                $datesearch.= " and  DATE(dbs.created_at) >= DATE('".$start_date."') AND DATE(dbs.created_at) <= DATE('".$end_date."')";
            }
            $query      = "
                            SELECT
                                @a:=@a+1  as serial_number,
                                A.*
                            FROM
                                (
                                    SELECT
                                        dbs.market as market_id,
                                        s.state,
                                        d.district,
                                        t.territory,
                                        m.market,
                                        dbs.distributer_name,
                                        dbs.mobilenumber,
                                        dbs.mobile2,
                                        dbs.Adress,
                                        dbs.pincode,
                                        dbs.turnover,
                                        dbs.addedbyecn as ecnNumber,
                                        dbs.distributerforname,
                                        (CASE WHEN dbs.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                        DATE_FORMAT(dbs.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format,
                                        dbs.id,
                                        dbs.pesticide_turnover,
                                        REPLACE(REPLACE(dbs.crope,'[',''),']','') as crop,
                                        REPLACE(REPLACE(dbs.brands,'[',''),']','') as brands,
                                        dbs.counter_percentage_pesticides,
                                        dbs.connected_retailers,
                                        dbs.ratings
                                    FROM distributers dbs
                                    LEFT JOIN   states  s   ON dbs.state_id=s.state_id
                                    LEFT JOIN   districts   d   ON dbs.district_id=d.district_id
                                    LEFT JOIN   territories   t   ON dbs.territory_id=t.territory_id
                                    LEFT JOIN   markets   m   ON dbs.market=m.market_id
                                    WHERE ".$datesearch." AND
                                        dbs.status<>'2'
                                        AND (
                                            s.state                 LIKE '%".$search."%'    OR
                                            d.district              LIKE '%".$search."%'    OR
                                            t.territory             LIKE '%".$search."%'    OR
                                            m.market                LIKE '%".$search."%'    OR
                                            dbs.distributer_name    LIKE '%".$search."%'    OR
                                            dbs.mobilenumber        LIKE '%".$search."%'
                                        )
                                    ORDER BY
                                    dbs.created_at DESC
                                )   A,
                                (select @a:=0) initvars";
                        // echo $query; exit;
            $distributors  = collect(DB::select($query));
            foreach ($distributors as $key => $value) {
                $cropIds  = $value->crop;
                $brandIds = $value->brands;
                $market_id = $value->market_id;
                if($cropIds) {
                    $qr_crops = 'SELECT GROUP_CONCAT(DISTINCT crop) as crop FROM `crops` WHERE crop_id IN ('.$cropIds.')';
                    $crops_list = collect(DB::select($qr_crops));
                    $distributors[$key]->cropname = $crops_list[0]->crop;
                } else {
                    $distributors[$key]->cropname = "";
                }
                if($brandIds) {
                    $qr_brands = 'SELECT GROUP_CONCAT(DISTINCT name) as name FROM `compcompanys` WHERE c_id IN ('.$brandIds.')';
                    $brands_list = collect(DB::select($qr_brands));
                    $distributors[$key]->brandname = $brands_list[0]->name;
                } else {
                    $distributors[$key]->brandname = "";
                }
                if($market_id) {
                    $qr_market = "SELECT ECN FROM `users` WHERE market LIKE '%".'"'.$market_id.'"'."%'";
                    $market_list = collect(DB::select($qr_market));
                    $distributors[$key]->ECN = (count($market_list)>0) ? $market_list[0]->ECN : '';
                } else {
                    $distributors[$key]->ECN = "";
                }
            }
            return Datatables::of($distributors)->make();            
        }
    }

    public function postDistributerDistrict(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();     
            $district_name          = $my_data['district_name']; 
            $tehsil_list  = territories::select('territory_id AS tId', 'territory AS tName')->whereIN('district_id', $district_name)->where('status', '1')->get()->toArray();
            if(count($tehsil_list)>0) {
                echo json_encode($tehsil_list, true);
            }
            
        }
    }

    public function postDistributerTerritory(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data        = Input::all();
            $territory_name          = $my_data['territory_name'];   
            $market_list  = markets::select('market_id AS mId', 'market AS mName')->whereIN('territory_id', $territory_name)->where('status', '1')->get()->toArray();
            if(count($market_list)>0) {
                echo json_encode($market_list, true);
            }
            
        }
    }

    public function postUpdateDistributer(){
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data    = Input::all();
            // echo "<pre>"; print_r($my_data); die;
            $state                = $my_data['state_name'];
            $district             = $my_data['district_name'];   
            $territory            = $my_data['territory_name'];
            $distributername      = $my_data['distributername'];
            $market               = $my_data['market_name'];
            $mobilenumbmer         = $my_data['mobilenumbmer'];
            $mobilenumber         = $my_data['mobilenumber'];
            $Adress               = $my_data['address'];
            $pincode              = $my_data['pincode'];
            // $ecnnumber            = $my_data['ecnnumber'];
            $turnover             = $my_data['turnover'];
            $distributerforname   = $my_data['Distributerfor_name'];
            $pesticide_turnover   = $my_data['pesticide_turnover'];
            $crope                = (!empty($my_data['crope'])) ? json_encode($my_data['crope']) : '';
            $brands               = (!empty($my_data['brand'])) ? json_encode($my_data['brand']) : '';
            $id                   = $my_data['id'];
            $counter_percentage_pesticides = (!empty($my_data['counter_percentage_pesticides'])) ? $my_data['counter_percentage_pesticides'] : NULL;
                                
            $distributerforname   = $my_data['Distributerfor_name'];
            $rules = array(
                            'state_id'                =>'required',
                            'district_id'             =>'required',
                            'territory_id'            =>'required',
                            'distributername'         =>'required',
                            'market_name'             =>'required|string',
                            'mobilenumber'            =>'required',
                            'Adress'                  =>'required',
                            'pincode'                 =>'required',
                            'turnover'                =>'required',
                            'crope'                   =>'required',
                            'company'                 =>'required',                   
                            'distributerforname'      =>'required',
                            'id'                      =>'required',
                            'pesticide_turnover'      =>'required'
                        );
            $update_distributer = DB::table('distributers')
                                ->where('id', $id)
                                ->update( array(
                                'state_id'                         => $state,
                                'district_id'                      => $district,
                                'territory_id'                     => $territory,
                                'distributer_name'                 => $distributername,
                                'market'                           => $market,
                                'mobilenumber'                     => $mobilenumbmer,
                                'mobile2'                          => $mobilenumber,
                                'Adress'                           => $Adress,
                                'pincode'                          => $pincode,
                                'turnover'                         => $turnover,
                                'distributerforname'               => $distributerforname,
                                'crope'                            => $crope,
                                'brands'                           => $brands,
                                'pesticide_turnover'               => $pesticide_turnover,
                                'counter_percentage_pesticides'    => $counter_percentage_pesticides,
                                'status'                           => '1',
                                'created_at'                       => date('Y-m-d H:i:s')
                                )); 
                             // print_r($update_distributer);
            if($update_distributer) {
                Session::flash('message', "Distributer Updated Successfully.");
                return redirect('distributer/distributer-list');             
            }
            else {
                
                return redirect('distributer/distributer-list');
            }
        }
    }
  
    public function postDistributerStatus(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all(); 
            $id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');            
            $distributer_data  = distributer::where('id', $id)->first();
            if($distributer_data) {
                $distributer_data_status   = ($distributer_data->status=='0'?'1':'0');
                $update_village = DB::table('distributers')
                                    ->where('id', $id)
                                    ->update(['status'=>$distributer_data_status, 'updated_at' => date('Y-m-d H:i:s')]); 
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
