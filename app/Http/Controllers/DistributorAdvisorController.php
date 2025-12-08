<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
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

use App\TeaEstate;
use App\TeaEstateContact;
use App\EstateCategory;
use App\EstateCompany;
use App\EstateDesignation;
use App\TeaEstateSalesReport;
use App\TeaEstateATP;
use App\TeaEstateTarget;
use App\TeaEstateTestimonial;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Storage;

class DistributorAdvisorController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function getListDistributorAdvisor() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.distibutor_advisor.list-distibutor-advisor');
    }

    public function postAllDistributorAdvisorList(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $value          = Input::all();
        $status         = isset($value['status']) ? $value['status'] :'';
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];

        $start_date     = $value['start_date'];
        $end_date       = $value['end_date'];

        $adQuery = "(1=1)";
        if($start_date!='' && $end_date!='') {
            $adQuery .= " AND DATE(da.created_at) >= DATE('".$start_date."') AND DATE(da.created_at) <= DATE('".$end_date."')";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.user_ecn LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.territory LIKE '%".$search."%' OR A.market LIKE '%".$search."%' OR A.address LIKE '%".$search."%' OR A.distributor_name LIKE '%".$search."%' OR A.mobile LIKE '%".$search."%')";
        } else {
            $adSearch = "";
        }
        $sorting    = '';
        $query      = " 
                        SELECT
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                da.distributor_advisor_id,
                                (CASE WHEN da.is_distributor='0' THEN 'Distributor' ELSE 'Advisor' END) as is_distributor,
                                da.distributor_name,
                                da.distributor_firm_name,
                                da.mobile,
                                da.mobile2,
                                s.state,
                                d.district,
                                t.territory,
                                m.market,
                                da.address,
                                da.pincode,
                                da.turnover,
                                da.crope,
                                '' as cropname,
                                da.brands,
                                '' as brandname,
                                da.user_ecn,
                                (CASE WHEN da.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(da.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format
                            FROM distributor_advisor da
                            LEFT JOIN states s ON da.state_id=s.state_id
                            LEFT JOIN districts d ON da.district_id=d.district_id
                            LEFT JOIN territories t ON da.territory_id=t.territory_id
                            LEFT JOIN markets m ON da.market_id=m.market_id
                            WHERE $adQuery
                            GROUP BY da.distributor_advisor_id
                            ORDER BY
                                da.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $distributor_advisor_list = collect(DB::select($query));
        foreach ($distributor_advisor_list as $key => $value) {
            $crope  = $value->crope;
            $brands = $value->brands;

            if($crope) {
                $expcrop  = explode(',', $crope);
                $cropIds  = implode('","', $expcrop);
                $qr_crops = 'SELECT GROUP_CONCAT(DISTINCT crop) as crop FROM `crops` WHERE crop_id IN ("'.$cropIds.'")';
                $crops_list = collect(DB::select($qr_crops));
                if(count($crops_list)>0) {
                    $distributor_advisor_list[$key]->cropname = $crops_list[0]->crop;
                }
            }
            if($brands) {
                $expcbrands = explode(',', $brands);
                $brandIds   = implode('","', $expcbrands);
                $qr_brands  = 'SELECT GROUP_CONCAT(DISTINCT name) as name FROM `compcompanys` WHERE c_id IN ("'.$brandIds.'")';
                $brands_list = collect(DB::select($qr_brands));
                if(count($brands_list)>0) {
                    $distributor_advisor_list[$key]->brandname = $brands_list[0]->name;
                }
            }
        }
        //print_r($distributor_advisor_list);
        return Datatables::of($distributor_advisor_list)->make();
    }

    public function getEditDistributorAdvisor($distributor_advisor_id) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            if($distributor_advisor_id!='') {
                $detail = DB::table('distributor_advisor')->where('distributor_advisor_id', $distributor_advisor_id)->first();
                if($detail) {
                    $state      = State::get();
                    $district   = District::where('state_id', $detail->state_id)->where('status','!=','2')->get();
                    $territory  = territories::where('district_id', $detail->district_id)->where('status','!=','2')->get();
                    $market     = markets::where('territory_id', $detail->territory_id)->where('status', '!=', '2')->get();
                    $crops      = Crop::where('status', '1')->select('crop_id', 'crop')->groupBy('crop_id')->orderBy('crop', 'asc')->get();
                    $brands     = compcompanys::where('status', '1')->select('c_id', 'name')->groupBy('c_id')->orderBy('name', 'asc')->get();

                    return View::make('backend.distibutor_advisor.edit-distributor-advisor', array('detail' => $detail, 'state'=> $state, 'district'=> $district, 'territory'=> $territory, 'market'=> $market, 'crops'=> $crops, 'brands'=> $brands));
                } else {
                    return redirect('distributoradvisor/list-distributor-advisor');
                }
            } else {
                return redirect('distributoradvisor/list-distributor-advisor');
            }
        } else {
            return redirect('/');
        }
    }

    public function postUpdateDistributorAdvisor(Request $request) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();
            $rules      = array(
                            'distributor_advisor_id'  =>'required',
                            'state_id'                =>'required',
                            'district_id'             =>'required',
                            'territory_id'            =>'required',
                            'market_id'               =>'required',
                            'distributor_name'        =>'required',
                            'distributor_firm_name'   =>'required',
                            'mobile'                  =>'required'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $distributor_advisor_id = $my_data['distributor_advisor_id'];
                $state_id       = $my_data['state_id'];
                $district_id    = $my_data['district_id'];
                $territory_id   = $my_data['territory_id'];
                $market_id      = $my_data['market_id'];
                $distributor_name       = $my_data['distributor_name'];
                $distributor_firm_name  = $my_data['distributor_firm_name'];
                $mobile         = $my_data['mobile'];
                $mobile2        = (!empty($my_data['mobile2'])) ? $my_data['mobile2'] :'';
                $address        = (!empty($my_data['address'])) ? $my_data['address'] :'';
                $pincode        = (!empty($my_data['pincode'])) ? $my_data['pincode'] :'';
                $turnover       = (!empty($my_data['turnover'])) ? $my_data['turnover'] :'';
                $brands         = (count($my_data['brands'])>0) ? implode(',', $my_data['brands']) : '';
                $crope          = (count($my_data['crops'])>0) ? implode(',', $my_data['crops']) : '';

                $update_distributor_advisor = DB::table('distributor_advisor')
                                    ->where('distributor_advisor_id', $distributor_advisor_id)
                                    ->update([
                                        'state_id'      => $state_id,
                                        'district_id'   => $district_id,
                                        'territory_id'  => $territory_id,
                                        'market_id'     => $market_id,
                                        'distributor_name' => $distributor_name,
                                        'distributor_firm_name' => $distributor_firm_name,
                                        'mobile'    => $mobile,
                                        'mobile2'   => $mobile2,
                                        'address'   => $address,
                                        'pincode'   => $pincode,
                                        'turnover'  => $turnover,
                                        'crope'     => $crope,
                                        'brands'    => $brands,
                                        'status'    => '1',
                                        'updated_at'=> date('Y-m-d H:i:s')
                                    ]);
                if($update_distributor_advisor) {
                    Session::flash('message',"Distributor/Advisor Successfully Updated.");
                    return redirect('distributoradvisor/list-distributor-advisor');
                } else {
                    Session::flash('message', "An error occurret. Please try again later.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            }
        }
    }

    public function getListAtp() {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.distibutor_advisor.list-atp');
    }

    public function postAllAtpList(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $value          = Input::all();
        $status         = isset($value['status']) ? $value['status'] :'';
        $limit          = $value['length'];
        $start          = $value['start'];
        $search_array   = $value['search'];     // value & regax
        $column_array   = $value['columns'];    // array -> array (data, name, searchable, orderable & (search (array) | value & regax))
        $order_array    = $value['order'][0]['column'];
        $dir            = $value['order'][0]['dir'];

        $start_date     = $value['start_date'];
        $end_date       = $value['end_date'];

        $adQuery = "(1=1)";
        if($start_date!='' && $end_date!='') {
            $adQuery .= " AND DATE(daa.created_at) >= DATE('".$start_date."') AND DATE(daa.created_at) <= DATE('".$end_date."')";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.user_ecn LIKE '%".$search."%' OR A.state LIKE '%".$search."%' OR A.district LIKE '%".$search."%' OR A.territory LIKE '%".$search."%' OR A.market LIKE '%".$search."%' OR A.address LIKE '%".$search."%' OR A.distributor_name LIKE '%".$search."%' OR A.mobile LIKE '%".$search."%')";
        } else {
            $adSearch = "";
        }
        $sorting    = '';
        $query      = " 
                        SELECT
                            @a:=@a+1  as serial_number,
                            A.*
                        FROM
                            (
                            SELECT
                                daa.distributor_advisor_atp_id,
                                (CASE WHEN da.is_distributor='0' THEN 'Distributor' ELSE 'Advisor' END) as is_distributor,
                                da.distributor_name,
                                da.distributor_firm_name,
                                da.mobile,
                                da.mobile2,
                                s.state,
                                d.district,
                                t.territory,
                                m.market,
                                da.address,
                                daa.user_name,
                                daa.user_ecn,
                                daa.user_type,
                                daa.arrival_image,
                                DATE_FORMAT(daa.arrival_time, '%Y-%m-%d, %h:%i %p') as arrival_time,
                                daa.arrival_comment,
                                daa.arrival_latitude,
                                daa.arrival_longitude,
                                daa.departure_image,
                                DATE_FORMAT(daa.departure_time, '%Y-%m-%d, %h:%i %p') as departure_time,
                                daa.departure_comment,
                                daa.departure_latitude,
                                daa.departure_longitude,
                                daa.duration,
                                (CASE WHEN daa.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(daa.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format
                            FROM distributor_advisor_atp daa
                            LEFT JOIN distributor_advisor da ON daa.distributor_advisor_id=da.distributor_advisor_id
                            LEFT JOIN states s ON da.state_id=s.state_id
                            LEFT JOIN districts d ON da.district_id=d.district_id
                            LEFT JOIN territories t ON da.territory_id=t.territory_id
                            LEFT JOIN markets m ON da.market_id=m.market_id
                            WHERE $adQuery
                            GROUP BY daa.distributor_advisor_atp_id
                            ORDER BY
                                daa.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $atp_list = collect(DB::select($query));
        //print_r($atp_list);
        return Datatables::of($atp_list)->make();
    }
}
