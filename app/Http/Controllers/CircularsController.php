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

use App\ZonalManager;
use App\ZonalManagerState;
use App\ZonalManagerProductCategory;
use App\RegionalManager;
use App\RegionalManagerState;
use App\RegionalManagerProductCategory;
use App\SalesRegionalManager;
use App\SalesRegionalManagerState;
use App\SalesRegionalManagerProductCategory;
use App\AreaManager;
use App\AreaManagerState;
use App\AreaManagerProductCategory;
use App\SalesAreaManager;
use App\SalesAreaManagerState;
use App\SalesAreaManagerProductCategory;
use App\User;
use App\UserDeputation;
use App\UserProductCategory;
use App\AppMenu;

use App\State;
use App\District;

use App\TeaEstate;
use App\TeaEstateContact;
use App\EstateCategory;
use App\EstateCompany;
use App\EstateDesignation;
use App\TeaEstateATP;
use App\TeaEstateTestimonial;

use App\SugarmillContact;
use App\InfluencerContact;

use App\Circular;
use App\CircularTestimonial;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CircularsController extends Controller {
    public function __construct() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function getListCircular() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $estatecategory = Circular::where('status',1)->get();
        $estatecompany  = EstateCompany::where('status',1)->get();
        return View::make('backend.circular.list-circular');
    }

    public function postAllCircularList(Request $request) {
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
            $adQuery .= " AND DATE(c.created_at) >= DATE('".$start_date."') AND DATE(c.created_at) <= DATE('".$end_date."')";
        }

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.user_name LIKE '%".$search."%' OR A.user_ecn LIKE '%".$search."%' OR A.circular_name LIKE '%".$search."%' OR A.whatsapp_message LIKE '%".$search."%')";
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
                                c.circular_id,
                                c.user_name,
                                c.user_ecn,
                                c.user_type,
                                (CASE 
                                    WHEN c.institutionalType='1' THEN 'Tea Estate'
                                    WHEN c.institutionalType='2' THEN 'Sugarmill'
                                    ELSE 'Influencer'
                                END) AS institutionalType,
                                c.circular_name,
                                c.circular_path,
                                DATE_FORMAT(c.published_date, '%Y-%m-%d, %h:%i %p') as published_date,
                                c.contact_ids,
                                '0' as contacts,
                                '0' as circular_testimonial,
                                c.whatsapp_message,
                                (CASE WHEN c.status='1' THEN 'APPROVED' ELSE 'ON HOLD' END) as status,
                                DATE_FORMAT(c.created_at, '%Y-%m-%d, %h:%i %p') as created_at
                            FROM circular c
                            WHERE $adQuery
                            GROUP BY c.circular_id
                            ORDER BY
                                c.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        $circular_list      = collect(DB::select($query));
        foreach ($circular_list as $key => $value) {
            $circular_id    = $value->circular_id;
            $contact_ids    = $value->contact_ids;
            if($contact_ids) {
                $expContactIds  = explode(',', $contact_ids);
                $contact_cnt    = count($expContactIds);
                $circular_list[$key]->contacts = $contact_cnt;
            }
            $testimonialcnt = CircularTestimonial::where('circular_id',$circular_id)->count();
            $circular_list[$key]->circular_testimonial = $testimonialcnt;
        }
        return Datatables::of($circular_list)->make();
    }

    public function getEditCircular($circular_id) { 
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($circular_id!='') {
                $circulardetail   = Circular::where('circular_id', $circular_id)->first();
                if($circulardetail) {
                    if($circulardetail->institutionalType==1) {
                        $contactList    = TeaEstateContact::where('status',1)->get();
                        $contact_by_id  = 'tea_estate_contact_id';
                    } else if($circulardetail->institutionalType==2) {
                        $contactList    = SugarmillContact::where('status',1)->get();
                        $contact_by_id  = 'sugarmill_contact_id';
                    } else {
                        $contactList    = InfluencerContact::where('status',1)->get();
                        $contact_by_id  = 'influencer_contact_id';
                    }
                    // $circulartestimonial= CircularTestimonial::where('circular_id', $circular_id)->get();

                    if($circulardetail->institutionalType==3) {
                        $circulartestimonial = CircularTestimonial::join('testimonials','testimonials.self_participation_id','=','circular_testimonial.testimonial_id')->where('circular_testimonial.circular_id',$circular_id)->select('circular_testimonial.circular_testimonial_id','circular_testimonial.circular_id','circular_testimonial.testimonial_id','circular_testimonial.description','testimonials.uploaded_file as image_video')->get();
                    } else {
                        $circulartestimonial = CircularTestimonial::join('institutional_testimonial','institutional_testimonial.institutional_testimonial_id','=','circular_testimonial.testimonial_id')->where('circular_testimonial.circular_id',$circular_id)->select('circular_testimonial.circular_testimonial_id','circular_testimonial.circular_id','circular_testimonial.testimonial_id','circular_testimonial.description','institutional_testimonial.image_video')->get();
                    }
                    $state      = State::get();
                    if($circulardetail->state_id!=0) {
                        $district = District::where('state_id',$circulardetail->state_id)->where('status','!=','2')->get();
                    } else {
                        $district = District::where('status', '!=', '2')->get();
                    }
                    return View::make('backend.circular.edit-circular', array('state_detail'=>$state,'district_detail'=>$district,'circulardetail' => $circulardetail, 'contactList' => $contactList, 'circulartestimonial'=> $circulartestimonial,'contact_by_id'=>$contact_by_id));
                } else {
                    return redirect('circulars/list-circular');
                }
            } else {
                return redirect('circulars/list-circular');
            }
        } else {
            return redirect('/');
        }
    }

    public function postUpdateCircular(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();
            $rules      = array(
                            'circular_id'       => 'required',
                            'circular_name'     => 'required',
                            'published_date'    => 'required',
                            'whatsapp_message'  => 'required',
                            'contact_ids'       => 'required',
                            'status'            => 'required'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $circular_id        = $my_data['circular_id'];
                $state_id           = $my_data['state_id'];
                $district_id        = $my_data['district_id'];
                $circular_name      = $my_data['circular_name'];
                $published_date     = $my_data['published_date'];
                $whatsapp_message   = $my_data['whatsapp_message'];
                $contact_ids        = $my_data['contact_ids'];
                $description        = $my_data['description'];
                $status             = $my_data['status'];

                $update_circular    = Circular::where('circular_id',$circular_id)
                                    ->update([
                                        'state_id'         => $state_id,
                                        'district_id'      => $district_id,
                                        'circular_name'    => $circular_name,
                                        'published_date'   => $published_date,
                                        'whatsapp_message' => $whatsapp_message,
                                        'status'           => $status,
                                        'contact_ids'      => implode(',',$contact_ids),
                                        'updated_at'       => date('Y-m-d H:i:s')
                                    ]);
                if(count($description)>0) {
                    foreach ($description as $key => $value) {
                        $update_testimonial = CircularTestimonial::where('circular_testimonial_id',$key)->update(['description' => $value, 'updated_at'=> date('Y-m-d H:i:s')]);
                    }
                }
                if($update_circular) {
                    Session::flash('message', "Circular Successfully Updated.");
                    return redirect('circulars/list-circular');
                } else {
                    Session::flash('message', "An error occurret. Please try again later.");
                    return Redirect::back()->withInput()->withErrors($validator);
                }
            }
        }
    }

    public function postUpdateStatus(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data        = Input::all();
            $circular_id    = (isset($my_data['_ticket'])?$my_data['_ticket']:'');
            $circular_data  = Circular::where('circular_id', $circular_id)->first();
            if($circular_data) {
                $circular_status = ($circular_data->status=='0'?'1':'0');
                $update_circular = DB::table('circular')->where('circular_id',$circular_id)
                                    ->update(['status'=>$circular_status, 'updated_at' => date('Y-m-d H:i:s')]);
                echo '1';
            } else {
                echo '0';
            }
        } else {
            echo '0';
        }
    }

    public function getListContact($circular_id='') {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        if(($circular_id!='') && ($circular_id!=0)) {
            $circular = Circular::where('circular_id',$circular_id)->get();
            if(count($circular)>0) {
                // echo json_encode($circular); exit();
                return View::make('backend.circular.list-circularcontact',array('circular'=> $circular,'circular_id'=>$circular_id));
            } else {
                return redirect('/circulars/list-circular');
            }
        } else {
            $circular = Circular::get();
            return View::make('backend.circular.list-circularcontact',array('circular'=> $circular,'circular_id'=>$circular_id));
        }
    }

    public function postAllContactList(Request $request) {
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

        if(!empty($value['circular_id'])) {
            $circular_id = $value['circular_id'];
            $getcircular = Circular::where('circular_id',$circular_id)->get();
            if(count($getcircular)>0) {
                $adQuery = "(1=1)";
                /*if($start_date!='' && $end_date!='') {
                    $adQuery .= " AND DATE(tc.created_at) >= DATE('".$start_date."') AND DATE(tc.created_at) <= DATE('".$end_date."')";
                }*/
                if(!empty($search_array['value'])) {
                    $search = $search_array['value'];
                    $adSearch = " WHERE (A.contact_name LIKE '%".$search."%' OR A.wa_number LIKE '%".$search."%' OR A.mobile_number LIKE '%".$search."%' OR A.email LIKE '%".$search."%')";
                } else {
                    $adSearch = "";
                }
                $sorting    = '';

                $circular_name      = $getcircular[0]->circular_name;
                $circular_contact   = $getcircular[0]->contact_ids;
                // $circular_contacts  = explode(',', $circular_contact);

                if($getcircular[0]->institutionalType==1) {
                    $query  = " 
                            SELECT
                                @a:=@a+1  as serial_number,
                                A.*
                            FROM (
                                SELECT
                                    tc.tea_estate_contact_id,
                                    tc.level,
                                    tc.designation,
                                    tc.name as contact_name,
                                    tc.wa_number,
                                    tc.mobile_number,
                                    tc.email
                                FROM tea_estate_contact tc
                                WHERE tea_estate_contact_id IN (".$circular_contact.")
                                GROUP BY tc.tea_estate_contact_id
                                ORDER BY
                                    tc.created_at DESC
                            ) A,
                            (select @a:=0) initvars $adSearch";
                } else if($getcircular[0]->institutionalType==2) {
                    $query  = " 
                            SELECT
                                @a:=@a+1  as serial_number,
                                A.*
                            FROM (
                                SELECT
                                    sc.sugarmill_contact_id,
                                    sc.level,
                                    sc.designation,
                                    sc.name as contact_name,
                                    sc.wa_number,
                                    sc.mobile_number,
                                    sc.email
                                FROM sugarmill_contact sc
                                WHERE sugarmill_contact_id IN (".$circular_contact.")
                                GROUP BY sc.sugarmill_contact_id
                                ORDER BY sc.created_at DESC
                            ) A,
                            (select @a:=0) initvars $adSearch";
                } else {
                    $query  = " 
                            SELECT
                                @a:=@a+1  as serial_number,
                                A.*
                            FROM (
                                SELECT
                                    ic.influencer_contact_id,
                                    ic.level,
                                    ic.designation,
                                    ic.name as contact_name,
                                    ic.wa_number,
                                    ic.mobile_number,
                                    ic.email
                                FROM influencer_contact ic
                                WHERE influencer_contact_id IN (".$circular_contact.")
                                GROUP BY ic.influencer_contact_id
                                ORDER BY ic.created_at DESC
                            ) A,
                            (select @a:=0) initvars $adSearch";
                }
                // print_r($query); exit();
                $circular_contact_list = collect(DB::select($query));
                //print_r($circular_contact_list);
                return Datatables::of($circular_contact_list)->make();
            }  else {
                return redirect('/circulars/list-circular');
            }
        } else {
            return redirect('/circulars/list-circular');
        }
    }

    public function postCirclarContact(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data        = Input::all();
            $circular_type  = $my_data['circular_type'];
            $state_id       = (!empty($my_data['state_id'])) ? $my_data['state_id'] : 0;
            $district_id    = (!empty($my_data['district_id'])) ? $my_data['district_id'] : 0;

            if($circular_type == 1) {
                $query = TeaEstateContact::select('tea_estate_contact_id as institutional_contact_id','wa_number','name')->where('status','1');
                if($state_id!=0) {
                    $query->where('state_id', $state_id);
                }
                if($district_id!=0) {
                    $query->where('district_id', $district_id);
                }
                $institutional_data = $query->get()->toArray();
            } else if($circular_type==2) {
                $query = SugarmillContact::select('sugarmill_contact_id as institutional_contact_id','wa_number','name')->where('status','1');
                if($state_id!=0) {
                    $query->where('state_id', $state_id);
                }
                if($district_id!=0) {
                    $query->where('district_id', $district_id);
                }
                $institutional_data = $query->get()->toArray();
            } else {
                $query = InfluencerContact::select('influencer_contact_id as institutional_contact_id','wa_number','name')->where('status','1');
                if($state_id!=0) {
                    $query->where('state_id', $state_id);
                }
                if($district_id!=0) {
                    $query->where('district_id', $district_id);
                }
                $institutional_data = $query->get()->toArray();
            }
            // dd($institutional_data);
            if(count($institutional_data)>0) {
                echo json_encode($institutional_data, true);
            } else {
                echo 0;
            }
        }
    }
}
