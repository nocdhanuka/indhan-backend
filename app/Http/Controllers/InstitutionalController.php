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
use App\Language;

use App\TeaEstate;
use App\TeaEstateContact;
use App\EstateCategory;
use App\EstateCompany;
use App\EstateDesignation;
use App\TeaEstateSalesReport;
use App\TeaEstateATP;
use App\TeaEstateTarget;
use App\InstitutionalTestimonial;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Storage;

class InstitutionalController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function getListTestimonials() {
        $sess   = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        $institutional_type = DB::table('institutional_type')->get();
        return View::make('backend.institutional.list-testimonials',array('institutional_type'=> $institutional_type));
    }

    public function postAllTestimonialList(Request $request) {
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
            $adQuery .= " AND DATE(it.created_at) >= DATE('".$start_date."') AND DATE(it.created_at) <= DATE('".$end_date."')";
        }
        if(!empty($value['institutional_type'])) {
            $institutionalType = $value['institutional_type'];
            if($institutionalType ==3) {
                $estate     = 'influencer';
                $estate_id  = 'influencer_id';
                $estate_category    = 'influencer_category';
                $estate_category_id = 'influencer_category_id';
            } else if($institutionalType ==2) {
                $estate     = 'sugarmill';
                $estate_id  = 'sugarmill_id';
                $estate_category    = 'sugarmill_category';
                $estate_category_id = 'sugarmill_category_id';
            } else {
                $estate     = 'tea_estate';
                $estate_id  = 'tea_estate_id';
                $estate_category    = 'estate_category';
                $estate_category_id = 'estate_category_id';
            }
            $adQuery .= " AND it.institutionalType = '".$institutionalType."'";
        } else {
            $estate     = 'tea_estate';
            $estate_id  = 'tea_estate_id';
            $estate_category    = 'estate_category';
            $estate_category_id = 'estate_category_id';
        }

        /*if(!empty($value['institutional_id'])) {
            $institutional_id = $value['institutional_id'];
            $adQuery .= " AND t.institutional_id = '".$institutional_id."'";
        }*/

        if(!empty($search_array['value'])) {
            $search = $search_array['value'];
            $adSearch = " WHERE (A.contact_number LIKE '%".$search."%' OR A.wa_contact_number LIKE '%".$search."%' OR A.contact_name LIKE '%".$search."%' OR A.testimonial_name LIKE '%".$search."%')";
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
                                it.institutional_testimonial_id,
                                itp.institutional_type,
                                DATE_FORMAT(it.date_time, '%Y-%m-%d, %h:%i %p') as date_time,
                                (CASE WHEN t.office_type='1' THEN 'Head Office' ELSE 'Branch Office' END) as office_type,
                                ect.company_category,
                                it.language_name,
                                it.contact_name,
                                it.contact_designation,
                                it.contact_number,
                                it.wa_contact_number,
                                (CASE WHEN it.can_be_shared='1' THEN 'Yes' ELSE 'No' END) as can_be_shared,
                                it.image_video,
                                it.latitude,
                                it.longitude,
                                it.testimonial_name,
                                it.description,
                                (CASE WHEN it.status='1' THEN 'ACTIVE' ELSE 'INACTIVE' END) as status,
                                DATE_FORMAT(it.created_at, '%Y-%m-%d, %h:%i %p') as datetime_format
                            FROM institutional_testimonial it
                            LEFT JOIN institutional_type itp ON it.institutionalType=itp.institutional_type_id
                            LEFT JOIN ".$estate." t ON it.institutional_id=t.".$estate_id."
                            LEFT JOIN ".$estate_category." ect ON t.".$estate_category_id."=ect.".$estate_category_id."
                            WHERE $adQuery
                            GROUP BY it.institutional_testimonial_id
                            ORDER BY
                                it.created_at DESC
                            ) A,
                            (select @a:=0) initvars
                            $adSearch";
        // print_r($query); exit();
        $testimonial_list = collect(DB::select($query));
        //print_r($testimonial_list);
        return Datatables::of($testimonial_list)->make();
    }

    public function getEditTestimonial($institutional_testimonial_id) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            if($institutional_testimonial_id!='') {
                $testimonialdetail   = InstitutionalTestimonial::where('institutional_testimonial_id',$institutional_testimonial_id)->first();
                if($testimonialdetail) {
                    $languages  = Language::get();
                    return View::make('backend.institutional.edit-testimonial', array('testimonialdetail' => $testimonialdetail, 'languages'=>$languages));
                } else {
                    return redirect('institutional/list-testimonials');
                }
            } else {
                return redirect('institutional/list-testimonials');
            }
        } else {
            return redirect('/');
        }
    }

    public function postUpdateTestimonial(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data    = Input::all();
            $rules      = array(
                            'institutional_testimonial_id' => 'required',
                            'testimonial_name'      => 'required',
                            // 'image_video'   => 'required',
                            'description'   => 'required',
                            'date_time'     => 'required',
                            'language_id'   => 'required'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $institutional_testimonial_id = $my_data['institutional_testimonial_id'];
                $testimonial_name   = $my_data['testimonial_name'];
                $date_time          = $my_data['date_time'];
                $description        = $my_data['description'];
                $language_id        = $my_data['language_id'];
                $language_name      = $my_data['language_name'];

                $update_image       = "";
                if($request->file('image_video')) {
                    $file=$request->file('image_video');
                    if(!empty($file)) {
                        //s3Upload
                        $fileName= "Tea_".time().rand(111,999).".".$file->getClientOriginalExtension();
                        // $sTempFileName = 'assets/tea_estate_testimonial/' . $fileName;
                        $sTempFileName = 'assets/testimonials/' . $fileName;
                        Storage::disk('s3')->put($sTempFileName, fopen($file, 'r+'),'public');
                        $update_image = InstitutionalTestimonial::where('institutional_testimonial_id',$institutional_testimonial_id)
                            ->update([
                                'image_video'     => $sTempFileName,
                                'updated_at'=> date('Y-m-d H:i:s')
                            ]);
                    }
                }

                $update_testimonial = InstitutionalTestimonial::
                                    where('institutional_testimonial_id',$institutional_testimonial_id)
                                    ->update([
                                        'testimonial_name'  => $testimonial_name,
                                        'date_time'   => $date_time,
                                        'description' => $description,
                                        'language_id' => $language_id,
                                        'language_name' => $language_name,
                                        'updated_at'  => date('Y-m-d H:i:s')
                                    ]);
                // return $update_testimonial;
                if($update_testimonial || $update_image) {
                    Session::flash('message', "Testimonial Successfully Updated.");
                    return redirect('institutional/list-testimonials');
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
            $my_data      = Input::all();
            $testimonial_id     = (isset($my_data['_ticket'])?$my_data['_ticket']:'');
            $testimonial_data   = InstitutionalTestimonial::where('institutional_testimonial_id', $testimonial_id)->first();
            if($testimonial_data) {
                $testimonial_status = ($testimonial_data->status=='0'?'1':'0');
                $update_testimonial = DB::table('institutional_testimonial')
                                    ->where('institutional_testimonial_id',$testimonial_id)
                                    ->update(['status'=>$testimonial_status, 'updated_at' => date('Y-m-d H:i:s')]);
                echo '1';
            } else {
                echo '0';
            }
        } else {
            echo '0';
        }
    }
}
