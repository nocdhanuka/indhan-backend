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

use App\TeaEstate;
use App\TeaEstateContact;
use App\EstateCategory;
use App\EstateCompany;
use App\EstateDesignation;
use App\TeaEstateATP;
use App\InstitutionalTestimonial;
use App\Testimonial;

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

class CircularController extends Controller {
    public function __construct() {
        /*$sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }*/
    }

    public function getCircular(Request $request, $path=0, $preview=0) {
        if($path!='0') {
            $crr_date = date('Y-m-d H:i:s');
            $exist    = 1;
            if($preview!=0) {
                $adQry = "AND c.user_ecn='".$preview."'";
            } else {
                $adQry =" AND c.published_date <='".$crr_date."' AND ct.status=1 AND c.status=1";
            }
            $testimonial_qry = "
                SELECT
                    count(ct.circular_testimonial_id) as total,
                    GROUP_CONCAT(ct.testimonial_id) AS testimonial_ids,
                    c.circular_id,
                    c.circular_name,
                    c.institutionalType,
                    DATE_FORMAT(c.published_date, '%d %M %Y') as published_date
                FROM circular_testimonial ct
                LEFT JOIN circular c ON c.circular_id=ct.circular_id
                WHERE c.circular_path='".$path."' $adQry LIMIT 0,1 ";
            // return $testimonial_qry;
            $estate_testimonial_ids = DB::select(DB::raw($testimonial_qry));
            $testimonialList        = array();
            if($estate_testimonial_ids[0]->total > 0) {
                $circular_id     = $estate_testimonial_ids[0]->circular_id;
                $circular_name   = $estate_testimonial_ids[0]->circular_name;
                $published_date  = $estate_testimonial_ids[0]->published_date;
                $testimonial_ids = $estate_testimonial_ids[0]->testimonial_ids;
                $expTestimonial  = explode(',',$testimonial_ids);
                // DB::enableQueryLog();
                if($estate_testimonial_ids[0]->institutionalType == 3) {
                    $testimonialList =Testimonial::join('circular_testimonial','circular_testimonial.testimonial_id','=','testimonials.self_participation_id')
                    ->whereIn('testimonials.self_participation_id',$expTestimonial)
                    ->where('circular_testimonial.circular_id',$circular_id)
                    ->where('testimonials.status',1)
                    ->where('circular_testimonial.status',1)
                    ->groupBy('circular_testimonial.circular_testimonial_id')
                    ->select('testimonials.self_participation_id  as institutional_testimonial_id','testimonials.mobile as contact_number','testimonials.mobile as wa_contact_number',DB::raw("'1' as can_be_shared"),'testimonials.uploaded_file as image_video','testimonials.description as testimonial_description','testimonials.status as testimonial_status','circular_testimonial.*')->get();
                } else {
                    $testimonialList =InstitutionalTestimonial::join('circular_testimonial','circular_testimonial.testimonial_id','=','institutional_testimonial.institutional_testimonial_id')
                    ->whereIn('institutional_testimonial.institutional_testimonial_id',$expTestimonial)
                    ->where('circular_testimonial.circular_id',$circular_id)
                    ->where('institutional_testimonial.status',1)
                    ->where('circular_testimonial.status',1)
                    ->groupBy('circular_testimonial.circular_testimonial_id')
                    ->select('institutional_testimonial.institutional_testimonial_id','institutional_testimonial.institutional_id','institutional_testimonial.institutional_contact_id','institutional_testimonial.contact_name','institutional_testimonial.contact_designation','institutional_testimonial.contact_number','institutional_testimonial.wa_contact_number','institutional_testimonial.can_be_shared','institutional_testimonial.image_video','institutional_testimonial.description as testimonial_description','institutional_testimonial.date_time','institutional_testimonial.status as testimonial_status','circular_testimonial.*')->get();
                }
                // $querylog = DB::getQueryLog();
                // Log::info($querylog);
                $video_arr  = array('webm','flv','vob','ogg','avi','mov','mp4','wmv','avchd');
                $image_arr  = array('apng','avif','gif','jpeg','jpg','png','svg','webp');
                $param_arr = array('exist' => 1,'circular_name' => $circular_name,'published_date'=> $published_date,'testimonialList' => $testimonialList,'base_url' => 'https://dhanuka-app.s3.ap-south-1.amazonaws.com/','video_arr'=> $video_arr,'image_arr'=> $image_arr);
            } else {
                $exist = 0;
                $param_arr = array('exist'=>0);
            }
        } else {
            $exist = 0;
            $param_arr = array('exist'=>0);
        }
        return View::make('frontend.listcircular',$param_arr);
    }
}
