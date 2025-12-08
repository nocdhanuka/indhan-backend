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
use App\CustomerQuery;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\adminController;

class testimonialController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getTestimonialList() {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
        return View::make('backend.testimonial.testimoniallist');
    }
    
    public function postAllquerylist(Request $request) {
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
        //$base_url   =   url('/');
        $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
        
        $start_date     = isset($value['start_date']) ? $value['start_date'] :'';
        $end_date       = isset($value['end_date']) ?  $value['end_date']:'';

        $search_query=" (1=1) ";

        if($start_date!='' && $end_date!='') {
            $search_query.= " and DATE(t.created_at) >= DATE('".$start_date."') AND DATE(t.created_at) <= DATE('".$end_date."')";
        }elseif($start_date!='') {
            $search_query.= " and DATE(t.created_at) >= DATE('".$start_date."')";
        }

        if(isset($search_array['value']) && $search_array['value']!=''){
            $search = $search_array['value']; 

            $search_query.= " and (                                
            zm.ECN                              LIKE '%".$search."%'    OR
            rm.ECN                              LIKE '%".$search."%'    OR
            am.ECN                              LIKE '%".$search."%'    OR
            sam.ECN                             LIKE '%".$search."%'    OR
            srm.ECN                             LIKE '%".$search."%'    OR
            u.ECN                               LIKE '%".$search."%'    OR
            zm.zonal_manager_name               LIKE '%".$search."%'    OR
            rm.regional_manager_name            LIKE '%".$search."%'    OR
            am.area_manager_name                LIKE '%".$search."%'    OR
            sam.sales_area_manager_name         LIKE '%".$search."%'    OR
            srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
            u.name                              LIKE '%".$search."%'    OR
            t.testimonial                       LIKE '%".$search."%'    OR
            t.description                       LIKE '%".$search."%'    OR
            t.uploaded_file                     LIKE '%".$search."%'    OR
            t.product_id                        LIKE '%".$search."%'    OR
            t.crop_id                           LIKE '%".$search."%'    OR
            t.mobile                            LIKE '%".$search."%'
            ) ";
        }
            
        $query  = " 
            SELECT                                 
                @a:=@a+1  as serial_number,
                A.*                                
            FROM
                (
                SELECT        
                    CONCAT_WS(
                                '\n', 
                                NULLIF(zm.ECN, ''),
                                NULLIF(rm.ECN, ''),
                                NULLIF(am.ECN, ''),
                                NULLIF(srm.ECN, ''),
                                NULLIF(sam.ECN, ''),
                                NULLIF(u.ECN, ''),
                                NULLIF(dd.ECN,'')
                            ) AS ecn,
                    CONCAT_WS(
                                '\n', 
                                NULLIF(zm.zonal_manager_name, ''),
                                NULLIF(rm.regional_manager_name, ''),
                                NULLIF(am.area_manager_name, ''),
                                NULLIF(srm.sales_regional_manager_name, ''),
                                NULLIF(sam.sales_area_manager_name, ''),
                                NULLIF(u.name, ''),
                                NULLIF(dd.name,'')
                            ) AS name,
                    UPPER(t.user_type) AS role,
                    t.testimonial,
                    t.mobile,
                    t.description,
                    t.product_id,
                    t.crop_id,                                
                    t.uploaded_file,
                    DATE_FORMAT(t.created_at, '%Y-%m-%d, %h:%i %p') as created_at,

                    '".$base_url."/' as base_url,
                    MAX(tr.id) as max_id,
                    (select rating from testimonial_rating where id = MAX(tr.id)) as averageRating,
                    (select CONCAT_WS(
                        '\n', 
                        NULLIF(zms.zonal_manager_name, ''),
                        NULLIF(rms.regional_manager_name, ''),
                        NULLIF(ams.area_manager_name, ''),
                        NULLIF(srms.sales_regional_manager_name, ''),
                        NULLIF(sams.sales_area_manager_name, ''),
                        NULLIF(ms.marketing_supervisor_name, '')
                    ) AS rater_name 
                    from testimonial_rating as trrs 
                    LEFT JOIN   zonal_managers          zms  ON trrs.supervisor_id=zms.zonal_manager_id AND trrs.supervisor_type='zonal'
                    LEFT JOIN   regional_managers       rms  ON trrs.supervisor_id=rms.regional_manager_id AND trrs.supervisor_type='regional'
                    LEFT JOIN   area_managers           ams  ON trrs.supervisor_id=ams.area_manager_id AND trrs.supervisor_type='area'
                    LEFT JOIN   sales_regional_managers srms ON trrs.supervisor_id=srms.sales_regional_manager_id AND trrs.supervisor_type='sales_regional'
                    LEFT JOIN   sales_area_managers     sams ON trrs.supervisor_id=sams.sales_area_manager_id AND trrs.supervisor_type='sales_area'
                    LEFT JOIN   marketing_supervisor     ms ON trrs.supervisor_id=ms.marketing_supervisor_id  AND trrs.supervisor_type='marketing_supervisor'
                    Group BY trrs.id
                    Having max(tr.id) = trrs.id ) as rater_name


                FROM        testimonials            t
                LEFT JOIN   users                   u   ON t.user_id=u.id AND t.user_type='bdo'
                LEFT JOIN   dhanuka_doctor          dd   ON t.user_id=dd.dd_id AND t.user_type='dhanuka_doctor'
                LEFT JOIN   zonal_managers          zm  ON t.user_id=zm.zonal_manager_id AND t.user_type='zonal'
                LEFT JOIN   regional_managers       rm  ON t.user_id=rm.regional_manager_id AND t.user_type='regional'
                LEFT JOIN   area_managers           am  ON t.user_id=am.area_manager_id AND t.user_type='area'
                LEFT JOIN   sales_regional_managers srm ON t.user_id=srm.sales_regional_manager_id AND t.user_type='sales_regional'
                LEFT JOIN   sales_area_managers     sam ON t.user_id=sam.sales_area_manager_id AND t.user_type='sales_area'

                LEFT JOIN testimonial_rating tr ON t.self_participation_id=tr.testimonial_id
                WHERE 
                ".$search_query."
                Group BY `t`.`self_participation_id`
                ORDER BY
                    t.self_participation_id DESC
                )   A,
                (select @a:=0) initvars";
                // echo $query; exit;
                //testimonial_rating tr ON t.self_participation_id=tr.testimonial_id
                //IFNULL(AVG(CASE WHEN `testimonial_id` != '' THEN `rating` END) DIV 1,0) as averageRating
                //(SELECT    MAX(id) max_id, rating,testimonial_id FROM testimonial_rating GROUP BY  id) as t_max ON t_max.testimonial_id = t.self_participation_id
        $participants   = collect(DB::select($query));  
        return Datatables::of($participants)->make(); 
    }

    public function postFiltertestimonial(Request $request) {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        } 
        $value    = Input::all();
        $rsm      = isset($value['filter_RSM']) ? $value['filter_RSM'] :'';
        $asm      = isset($value['filter_ASM']) ? $value['filter_ASM'] :'';
        $fl       = isset($value['filter_FL']) ? $value['filter_FL'] :'';
        $dd       = isset($value['filter_DD']) ? $value['filter_DD'] :'';
        $crop     = isset($value['filter_crop']) ? $value['filter_crop'] :'';
        $product  = isset($value['filter_product']) ? $value['filter_product'] :'';
        //$base_url =   url('/');
        $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com";
        $query  = " 
            SELECT                                 
                @a:=@a+1  as serial_number,
                A.*                                
            FROM
                (
                SELECT        
                    CONCAT_WS(
                                '\n', 
                                NULLIF(zm.ECN, ''),
                                NULLIF(rm.ECN, ''),
                                NULLIF(am.ECN, ''),
                                NULLIF(srm.ECN, ''),
                                NULLIF(sam.ECN, ''),
                                NULLIF(u.ECN, ''),
                                NULLIF(dd.ECN, '')
                            ) AS ecn,
                    CONCAT_WS(
                                '\n', 
                                NULLIF(zm.zonal_manager_name, ''),
                                NULLIF(rm.regional_manager_name, ''),
                                NULLIF(am.area_manager_name, ''),
                                NULLIF(srm.sales_regional_manager_name, ''),
                                NULLIF(sam.sales_area_manager_name, ''),
                                NULLIF(u.name, ''),
                                NULLIF(dd.name, '')
                            ) AS name,
                    UPPER(t.user_type) AS role,
                    t.testimonial,
                    t.mobile,
                    t.description,
                    t.product_id,
                    t.crop_id,                                
                    t.uploaded_file,
                    DATE_FORMAT(t.created_at, '%Y-%m-%d, %h:%i %p') as created_at,

                    '".$base_url."/' as base_url,
                    MAX(tr.id) as max_id,
                    (select rating from testimonial_rating where id = MAX(tr.id)) as averageRating,
                    (select CONCAT_WS(
                        '\n', 
                        NULLIF(zms.zonal_manager_name, ''),
                        NULLIF(rms.regional_manager_name, ''),
                        NULLIF(ams.area_manager_name, ''),
                        NULLIF(srms.sales_regional_manager_name, ''),
                        NULLIF(sams.sales_area_manager_name, ''),
                        NULLIF(ms.marketing_supervisor_name, '')
                    ) AS rater_name 
                    from testimonial_rating as trrs 
                    LEFT JOIN   zonal_managers          zms  ON trrs.supervisor_id=zms.zonal_manager_id AND trrs.supervisor_type='zonal'
                    LEFT JOIN   regional_managers       rms  ON trrs.supervisor_id=rms.regional_manager_id AND trrs.supervisor_type='regional'
                    LEFT JOIN   area_managers           ams  ON trrs.supervisor_id=ams.area_manager_id AND trrs.supervisor_type='area'
                    LEFT JOIN   sales_regional_managers srms ON trrs.supervisor_id=srms.sales_regional_manager_id AND trrs.supervisor_type='sales_regional'
                    LEFT JOIN   sales_area_managers     sams ON trrs.supervisor_id=sams.sales_area_manager_id AND trrs.supervisor_type='sales_area'
                    LEFT JOIN   marketing_supervisor     ms ON trrs.supervisor_id=ms.marketing_supervisor_id  AND trrs.supervisor_type='marketing_supervisor'
                    Group BY trrs.id
                    Having max(tr.id) = trrs.id ) as rater_name
                FROM        testimonials            t
                LEFT JOIN   users                   u   ON t.user_id=u.id AND t.user_type='bdo'
                LEFT JOIN   dhanuka_doctor          dd   ON t.user_id=dd.dd_id AND t.user_type='dhanuka_doctor'
                LEFT JOIN   zonal_managers          zm  ON t.user_id=zm.zonal_manager_id AND t.user_type='zonal'
                LEFT JOIN   regional_managers       rm  ON t.user_id=rm.regional_manager_id AND t.user_type='regional'
                LEFT JOIN   area_managers           am  ON t.user_id=am.area_manager_id AND t.user_type='area'
                LEFT JOIN   sales_regional_managers srm ON t.user_id=srm.sales_regional_manager_id AND t.user_type='sales_regional'
                LEFT JOIN   sales_area_managers     sam ON t.user_id=sam.sales_area_manager_id AND t.user_type='sales_area'
                LEFT JOIN testimonial_rating tr ON t.self_participation_id=tr.testimonial_id 
                WHERE ";
                if ($rsm != '' || $asm != '' || $fl != '' || $dd != '') 
                {
                    if ($rsm && !$asm && !$fl && !$dd) 
                    {   
                        $query  .=" t.user_id = '".$rsm."' and t.user_type = 'sales_regional'";
                    }
                    
                    if ($rsm && $asm && !$fl && !$dd) 
                    {   
                        $query  .=" t.user_id = '".$asm."' and t.user_type = 'sales_area'";
                    }

                    if ($rsm && $asm && $fl && !$dd) {
                        $query  .=" t.user_id = '".$fl."' and t.user_type = 'bdo'";
                    }

                    if ($rsm && $asm && $fl && $dd) {
                        $query  .=" t.user_id = '".$dd."' and t.user_type = 'dhanuka_doctor'";
                    } 
                }
                else
                {
                    $query  .=" (1=1)";
                }

                if ($crop != '') {
                    $query  .=" and t.crop_ids = '".$crop."'";
                } 

                if ($product != '') {
                    $query  .=" and t.product_ids = '".$product."'";
                } 
                
                $query  .=" Group BY `t`.`self_participation_id`
                ORDER BY
                    t.self_participation_id DESC
                )   A,
                (select @a:=0) initvars";
        $participants   = collect(DB::select($query));
        return Datatables::of($participants)->make(); 
    }
    
    public function postExportAllTestimonial(Request $request) {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        if ($userData) {
            $my_data    =   Input::all(); 
            $search     =   $my_data['search'];


            $search_query = '';


            $start_date     = isset($my_data['start_date']) ? $my_data['start_date'] :'';
            $end_date       = isset($my_data['end_date']) ?  $my_data['end_date']:'';

            $search_query=" (1=1) ";

            if($start_date!='' && $end_date!='') {
                $search_query.= " and DATE(t.created_at) >= DATE('".$start_date."') AND DATE(t.created_at) <= DATE('".$end_date."')";
            }elseif($start_date!='') {
                $search_query.= " and DATE(t.created_at) >= DATE('".$start_date."')";
            }


            $base_url = "https://dhanuka-app.s3.ap-south-1.amazonaws.com/";

            if($search!='') {
                $search_query .= "
                    AND
                    (
                        zm.ECN                              LIKE '%".$search."%'    OR
                        rm.ECN                              LIKE '%".$search."%'    OR
                        am.ECN                              LIKE '%".$search."%'    OR
                        sam.ECN                             LIKE '%".$search."%'    OR
                        srm.ECN                             LIKE '%".$search."%'    OR
                        u.ECN                               LIKE '%".$search."%'    OR
                        zm.zonal_manager_name               LIKE '%".$search."%'    OR
                        rm.regional_manager_name            LIKE '%".$search."%'    OR
                        am.area_manager_name                LIKE '%".$search."%'    OR
                        sam.sales_area_manager_name         LIKE '%".$search."%'    OR
                        srm.sales_regional_manager_name     LIKE '%".$search."%'    OR
                        u.name                              LIKE '%".$search."%'    OR
                        t.testimonial                       LIKE '%".$search."%'    OR
                        t.description                       LIKE '%".$search."%'    OR
                        t.uploaded_file                     LIKE '%".$search."%'    OR
                        t.product_id                        LIKE '%".$search."%'    OR
                        t.crop_id                           LIKE '%".$search."%'    OR
                        t.mobile                            LIKE '%".$search."%'
                    )";
            }
            else {
                $dataSearch = "(1=1)";
            }
            
            $query  = " 
            SELECT                                 
                @a:=@a+1  as serial_number,
                A.*                                
            FROM
                (
                SELECT        
                    CONCAT_WS(
                                '\n', 
                                NULLIF(zm.ECN, ''),
                                NULLIF(rm.ECN, ''),
                                NULLIF(am.ECN, ''),
                                NULLIF(srm.ECN, ''),
                                NULLIF(sam.ECN, ''),
                                NULLIF(u.ECN, ''),
                                NULLIF(dd.ECN,'')
                            ) AS ecn,
                    CONCAT_WS(
                                '\n', 
                                NULLIF(zm.zonal_manager_name, ''),
                                NULLIF(rm.regional_manager_name, ''),
                                NULLIF(am.area_manager_name, ''),
                                NULLIF(srm.sales_regional_manager_name, ''),
                                NULLIF(sam.sales_area_manager_name, ''),
                                NULLIF(u.name, ''),
                                NULLIF(dd.name,'')
                            ) AS name,
                    UPPER(t.user_type) AS role,
                    t.testimonial,
                    t.mobile,
                    t.description,
                    t.product_id,
                    t.crop_id, 
                    IF( t.uploaded_file != '', CONCAT('".$base_url."', t.uploaded_file), '' ) as uploaded_file,                               
                    DATE_FORMAT(t.created_at, '%Y-%m-%d, %h:%i %p') as created_at,

                    (select rating from testimonial_rating where id = MAX(tr.id)) as averageRating,
                    (select CONCAT_WS(
                        '\n', 
                        NULLIF(zms.zonal_manager_name, ''),
                        NULLIF(rms.regional_manager_name, ''),
                        NULLIF(ams.area_manager_name, ''),
                        NULLIF(srms.sales_regional_manager_name, ''),
                        NULLIF(sams.sales_area_manager_name, ''),
                        NULLIF(ms.marketing_supervisor_name, '')
                    ) AS rater_name 
                    from testimonial_rating as trrs 
                    LEFT JOIN   zonal_managers          zms  ON trrs.supervisor_id=zms.zonal_manager_id AND trrs.supervisor_type='zonal'
                    LEFT JOIN   regional_managers       rms  ON trrs.supervisor_id=rms.regional_manager_id AND trrs.supervisor_type='regional'
                    LEFT JOIN   area_managers           ams  ON trrs.supervisor_id=ams.area_manager_id AND trrs.supervisor_type='area'
                    LEFT JOIN   sales_regional_managers srms ON trrs.supervisor_id=srms.sales_regional_manager_id AND trrs.supervisor_type='sales_regional'
                    LEFT JOIN   sales_area_managers     sams ON trrs.supervisor_id=sams.sales_area_manager_id AND trrs.supervisor_type='sales_area'
                    LEFT JOIN   marketing_supervisor     ms ON trrs.supervisor_id=ms.marketing_supervisor_id  AND trrs.supervisor_type='marketing_supervisor'
                    Group BY trrs.id
                    Having max(tr.id) = trrs.id ) as rater_name


                FROM        testimonials            t
                LEFT JOIN   users                   u   ON t.user_id=u.id AND t.user_type='bdo'
                LEFT JOIN   dhanuka_doctor          dd   ON t.user_id=dd.dd_id AND t.user_type='dhanuka_doctor'
                LEFT JOIN   zonal_managers          zm  ON t.user_id=zm.zonal_manager_id AND t.user_type='zonal'
                LEFT JOIN   regional_managers       rm  ON t.user_id=rm.regional_manager_id AND t.user_type='regional'
                LEFT JOIN   area_managers           am  ON t.user_id=am.area_manager_id AND t.user_type='area'
                LEFT JOIN   sales_regional_managers srm ON t.user_id=srm.sales_regional_manager_id AND t.user_type='sales_regional'
                LEFT JOIN   sales_area_managers     sam ON t.user_id=sam.sales_area_manager_id AND t.user_type='sales_area'

                LEFT JOIN testimonial_rating tr ON t.self_participation_id=tr.testimonial_id
                WHERE 
                ".$search_query."
                Group BY `t`.`self_participation_id`
                ORDER BY
                    t.self_participation_id DESC
                )   A,
                (select @a:=0) initvars";
            // echo $query; exit;
        
            $product_farmer = DB::select(DB::raw($query));
            $result         = array_map(function ($value) {
                                return (array)$value;
                            }, $product_farmer);
            $ticket_column  = array(                
                                    'Id',
                                    'ECN',
                                    'Username',
                                    'User Role',
                                    'Testimonial For',
                                    'Mobile',
                                    'Description',
                                    'Product',
                                    'Crop',
                                    'URL',
                                    'Created At',
                                    'Rating',
                                    'Evaluated By'
                                    );
            array_unshift($result, $ticket_column);
            
            $primarycolor   =   "rgb(255, 0, 0)";
            $myFile         =   Excel::create("filename", function($excel) use($result, $primarycolor) {
                                    $excel->setTitle('title');
                                    $excel->sheet('Sheetname', function($sheet) use($result, $primarycolor) {
                                        $sheet->fromArray($result, null, 'A1', false, false);
                                        $sheet->row(1, function($row) use ($primarycolor) {
                                            $row->setFontSize(12)
                                                ->setFontWeight('bold')
                                                ->setBackground('#62bdb9')
                                                ->setAlignment('left')
                                                ->setFontColor('#000000');
                                        });
                                    });
                                });
                 
            $myFile     = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
            $response   = array(
                                'name' => "Testimonial List.xlsx", //no extention needed
                                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
                            );
            return response()->json($response);
        }
    } 
}