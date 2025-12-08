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

use App\AppMenu;
use App\AppMenuPermission;
use App\DataSource;
use App\Language;

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

use App\State;
use App\District;
use App\Tehsil;
use App\markets;
use App\territories;
use App\Village;
use App\Product;
use App\Crop;
use App\ProductCropRelation;
use App\ProductCropCategory;

use App\Ticket;
use App\Farmer;

use App\Testimonial;
use App\SelfParticipation;
use App\CustomerQuery;

use App\DhanukaDoctor;
use App\DdProductCategory;

use App\Efficiency;
use App\AssignedVillage;
use App\Reminder;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;

use Khill\Lavacharts\Lavacharts;

use App\UserProductCategoryOrn;
use App\DdProductCategoryOrn;
use App\AssignedVillageOrn;
use App\DhanukaDoctorOrn;
use App\Userorn;
use App\UserDeputationOrn;
use App\AreaManagerorn;
use App\AreaManagerStateOrn;
use App\AreaManagerProductCategoryOrn;
use App\RegionalManagerOrn;
use App\RegionalManagerStateOrn;
use App\RegionalManagerProductCategoryOrn;
use App\SalesAreaManagerOrn;
use App\SalesAreaManagerStateOrn;
use App\SalesAreaManagerProductCategoryOrn;
use App\SalesRegionalManagerOrn;
use App\SalesRegionalManagerStateOrn;
use App\SalesRegionalManagerProductCategoryOrn;

ini_set('max_execution_time', 0);
// ini_set("memory_limit", "-1");
// set_time_limit(0);

class TestController extends Controller {
    public function __construct() {
        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect(env('APP_URL'));
        }
    }

    public function getExportdata($value='') {
        $data = array(
            array('data1', 'data2'),
            array('data3', 'data4')
        );

        Excel::create('Filename', function($excel) use($data) {
            $excel->sheet('Sheetname', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->export('xls');
    }
}
?>