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
use App\Efficiency;
use App\PlanAtp;

use App\Circular;
use App\TeaEstateContact;
use App\InfluencerContact;
use App\SugarmillContact;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Log;

ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);

class cronController extends Controller {

    public function __construct() {
        $current_day = date('Y-m-d');
        // $result = Ticket::whereDate('created_at', '<', $current_day)->where('ticket_status', '1')->update(['status' => '2','ticket_status' => '2','closing_datetime'=>Carbon::now()]);
        // $result1 = PlanAtp::whereDate('plan_date', '<', $current_day)->where('status', '1')->update(['status' => '2','updated_at'=>date('Y-m-d H:i:s')]);
    }

    private function categoryWiseBdoListForQuery($key = '1', $value = '1') {
        $bdo_query = " SELECT GROUP_CONCAT(CONCAT('''', users.id, '''' )) AS user_id FROM    users WHERE   users.status='1' AND users." . $key . "='" . $value . "' LIMIT 0,1 ";
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $bdo_detail = DB::select(DB::raw($bdo_query));
        $bdo_list = array_map(function ($value) {
            return (array) $value;
        }, $bdo_detail);
        return $bdo_list;
    }

    private function getAllUser() {
        $query = "
                    (SELECT zonal_manager_id AS id, 'zonal' as user_type  FROM zonal_managers WHERE status='1')
                    UNION
                    (SELECT regional_manager_id AS id, 'regional' as user_type FROM regional_managers WHERE status='1')
                    UNION
                    (SELECT area_manager_id AS id, 'area' as user_type FROM area_managers WHERE status='1')
                    UNION
                    (SELECT sales_regional_manager_id AS id, 'sales_regional' as user_type FROM sales_regional_managers WHERE status='1')
                    UNION
                    (SELECT sales_area_manager_id AS id, 'sales_area' as user_type FROM sales_area_managers WHERE status='1')
                    UNION
                    (SELECT id AS id, 'bdo' as user_type FROM users WHERE status='1')
                    ";
        $user = DB::select(DB::raw($query));
        return $user;
    }

    private function totalActivity($bdo_id_list, $datesearch, $bdo_count) {
        $ticket_query = " SELECT COUNT('tickets.ticket_id') AS ticket_count FROM tickets WHERE tickets.status='0' AND tickets.user_id IN (" . $bdo_id_list . ") AND tickets.user_type='bdo' AND " . $datesearch . " LIMIT 0,1 ";
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $total_activity = DB::select(DB::raw($ticket_query));
        $total_activity = ($total_activity[0]->ticket_count != 0 || $total_activity[0]->ticket_count != '' ? $total_activity[0]->ticket_count / $bdo_count : 0);
        return $total_activity;
    }

    private function totalRegisteredFarmer($bdo_id_list, $datesearch, $bdo_count) {
        $farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND " . $datesearch . " LIMIT 0,1 ";
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $total_registered_farmer = DB::select(DB::raw($farmer_query));
        $total_registered_farmer = ($total_registered_farmer[0]->farmer_count != 0 || $total_registered_farmer[0]->farmer_count != '' ? $total_registered_farmer[0]->farmer_count / $bdo_count : 0);
        return $total_registered_farmer;
    }

    private function totalContactedFarmer($bdo_id_list, $datesearch, $bdo_count) {
        $contacted_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status!='' AND " . $datesearch . " LIMIT 0,1 ";
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $total_contacted_farmer = DB::select(DB::raw($contacted_farmer_query));
        $total_contacted_farmer = ($total_contacted_farmer[0]->farmer_count != 0 || $total_contacted_farmer[0]->farmer_count != '' ? $total_contacted_farmer[0]->farmer_count / $bdo_count : 0);
        return $total_contacted_farmer;
    }

    private function totalValidatedFarmer($bdo_id_list, $datesearch, $bdo_count) {
        $validated_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='Validated' AND " . $datesearch . " LIMIT 0,1 ";
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $total_validated_farmer = DB::select(DB::raw($validated_farmer_query));
        $total_validated_farmer = ($total_validated_farmer[0]->farmer_count != 0 || $total_validated_farmer[0]->farmer_count != '' ? $total_validated_farmer[0]->farmer_count / $bdo_count : 0);
        return $total_validated_farmer;
    }

    private function totalNadaFarmer($bdo_id_list, $datesearch, $bdo_count) {
        $nada_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='NADA' AND " . $datesearch . " LIMIT 0,1 ";
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $total_nada_farmer = DB::select(DB::raw($nada_farmer_query));
        $total_nada_farmer = ($total_nada_farmer[0]->farmer_count != 0 || $total_nada_farmer[0]->farmer_count != '' ? $total_nada_farmer[0]->farmer_count / $bdo_count : 0);
        return $total_nada_farmer;
    }

    private function totalPncFarmer($bdo_id_list, $datesearch, $bdo_count) {
        $pnc_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='PNC' AND " . $datesearch . " LIMIT 0,1 ";
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $total_pnc_farmer = DB::select(DB::raw($pnc_farmer_query));
        $total_pnc_farmer = ($total_pnc_farmer[0]->farmer_count != 0 || $total_pnc_farmer[0]->farmer_count != '' ? $total_pnc_farmer[0]->farmer_count / $bdo_count : 0);
        return $total_pnc_farmer;
    }

    private function totalBnFarmer($bdo_id_list, $datesearch, $bdo_count) {
        $bn_farmer_query = " SELECT COUNT('farmers.farmer_id') AS farmer_count FROM farmers LEFT JOIN tickets ON farmers.ticket_id=tickets.ticket_id WHERE farmers.ticket_status='1' AND farmers.user_id IN (" . $bdo_id_list . ") AND farmers.user_type='bdo' AND validation_status='BN' AND " . $datesearch . " LIMIT 0,1 ";
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $total_bn_farmer = DB::select(DB::raw($bn_farmer_query));
        $total_bn_farmer = ($total_bn_farmer[0]->farmer_count != 0 || $total_bn_farmer[0]->farmer_count != '' ? $total_bn_farmer[0]->farmer_count / $bdo_count : 0);
        return $total_bn_farmer;
    }

    public function getCalculateEfficiency() {
        $user_list = $this->getAllUser();
        if ($user_list) {
            foreach ($user_list as $bdo) {
                $user_id = $bdo->id;
                $user_type = $bdo->user_type;

                $month_list = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
                $current_year = date('Y');
                $current_month = date('m');

                $average_activity_efficiency = 0;
                $average_farmer_efficiency = 0;
                $nada_percent = 0;
                $pnc_percent = 0;
                $bn_percent = 0;
                $total_ex_valid_farmers = 0;
                $CONST_activity_per_month = 22;
                $CONST_average_farmer_registration = 35;
                $CONST_percent_deduction = 3;
                $average_registered_farmer = 0;
                $farmer_valid_percent = 0;

                foreach ($month_list as $month) {
                    if ($month < $current_month) {
                        if (($current_month - 1) == $month) {
                            $datesearch = " MONTH(tickets.created_at)= '" . $month . "' AND YEAR(tickets.created_at)= YEAR(CURRENT_DATE())";
                            switch ($user_type) {
                                case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                                    break;
                                case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                                    break;
                                case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                                    break;
                                case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                                    break;
                                case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                                    break;
                                case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                                    break;
                            }
                            if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                                $bdo_id_list = $bdo_list_query[0]['user_id'];
                                $bdo_count = count(explode(',', $bdo_id_list));
                                $total_activity = $this->totalActivity($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                if ($total_activity != 0) {
                                    $total_registered_farmer = $this->totalRegisteredFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    $total_contacted_farmer = $this->totalContactedFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    $total_validated_farmer = $this->totalValidatedFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    $total_nada_farmer = $this->totalNadaFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    $total_pnc_farmer = $this->totalPncFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    $total_bn_farmer = $this->totalBnFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);

                                    if ($total_contacted_farmer != 0) {
                                        $nada_percent = number_format((float) (($total_nada_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                        $pnc_percent = number_format((float) (($total_pnc_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                        $bn_percent = number_format((float) (($total_bn_farmer / $total_contacted_farmer) * (100)), 2, '.', '');

                                        $farmer_valid_percent = number_format((float) ((($total_validated_farmer / $total_contacted_farmer) * (100))), 2, '.', '');
                                        $total_ex_valid_farmers = number_format((float) (($total_registered_farmer * $farmer_valid_percent) / 100), 2, '.', '');
                                    }

                                    $average_activity_efficiency = number_format((float) ($total_activity / $CONST_activity_per_month) * 100, 2, '.', '');
                                    $average_validate_farmer = number_format((float) ($total_ex_valid_farmers / $total_activity), 2, '.', '');
                                    $average_farmer_efficiency = number_format((float) ($average_validate_farmer / $CONST_average_farmer_registration) * 100, 2, '.', '');

                                    $nada_deduction = ($nada_percent > 10 ? number_format((float) ((100 - (($nada_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                    $pnc_deduction = ($pnc_percent > 10 ? number_format((float) ((100 - (($pnc_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                    $bn_deduction = ($bn_percent > 10 ? number_format((float) ((100 - (($bn_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');

                                    $nada_deduct = ($nada_deduction == 0 ? 1 : $nada_deduction);
                                    $pnc_deduct = ($pnc_deduction == 0 ? 1 : $pnc_deduction);
                                    $bn_deduct = ($bn_deduction == 0 ? 1 : $bn_deduction);
                                    $efficiency = number_format((float) (($average_activity_efficiency * $average_farmer_efficiency * $nada_deduct * $pnc_deduct * $bn_deduct) * (100)) / (100 * 100), 2, '.', '');

                                    $efficiency_exist = Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => $month, 'year' => $current_year])->first();
                                    if ($efficiency_exist) {
                                        $efficiency_array = array(
                                            'user_id' => $user_id,
                                            'user_type' => $user_type,
                                            'month' => $month,
                                            'year' => $current_year,
                                            'CONST_percent_deduction' => $CONST_percent_deduction,
                                            'CONST_activity_per_month' => $CONST_activity_per_month,
                                            'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                            'total_activity' => $total_activity,
                                            'total_registered_farmer' => $total_registered_farmer,
                                            'total_contacted_farmer' => $total_contacted_farmer,
                                            'total_validated_farmer' => $total_validated_farmer,
                                            'total_nada_farmer' => $total_nada_farmer,
                                            'total_pnc_farmer' => $total_pnc_farmer,
                                            'total_bn_farmer' => $total_bn_farmer,
                                            'nada_percent' => $nada_percent,
                                            'pnc_percent' => $pnc_percent,
                                            'bn_percent' => $bn_percent,
                                            'farmer_valid_percent' => $farmer_valid_percent,
                                            'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                            'average_activity_efficiency' => $average_activity_efficiency,
                                            'average_validate_farmer' => $average_validate_farmer,
                                            'average_farmer_efficiency' => $average_farmer_efficiency,
                                            'nada_deduction' => $nada_deduction,
                                            'pnc_deduction' => $pnc_deduction,
                                            'bn_deduction' => $bn_deduction,
                                            'efficiency' => $efficiency,
                                            'updated_at' => date('Y-m-d H:i:s')
                                        );
                                        Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => $month, 'year' => $current_year])->update($efficiency_array);
                                    } else {
                                        $efficiency_array = array(
                                            'user_id' => $user_id,
                                            'user_type' => $user_type,
                                            'month' => $month,
                                            'year' => $current_year,
                                            'CONST_percent_deduction' => $CONST_percent_deduction,
                                            'CONST_activity_per_month' => $CONST_activity_per_month,
                                            'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                            'total_activity' => $total_activity,
                                            'total_registered_farmer' => $total_registered_farmer,
                                            'total_contacted_farmer' => $total_contacted_farmer,
                                            'total_validated_farmer' => $total_validated_farmer,
                                            'total_nada_farmer' => $total_nada_farmer,
                                            'total_pnc_farmer' => $total_pnc_farmer,
                                            'total_bn_farmer' => $total_bn_farmer,
                                            'nada_percent' => $nada_percent,
                                            'pnc_percent' => $pnc_percent,
                                            'bn_percent' => $bn_percent,
                                            'farmer_valid_percent' => $farmer_valid_percent,
                                            'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                            'average_activity_efficiency' => $average_activity_efficiency,
                                            'average_validate_farmer' => $average_validate_farmer,
                                            'average_farmer_efficiency' => $average_farmer_efficiency,
                                            'nada_deduction' => $nada_deduction,
                                            'pnc_deduction' => $pnc_deduction,
                                            'bn_deduction' => $bn_deduction,
                                            'efficiency' => $efficiency,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                        );
                                        DB::table('efficiency_results')->insert($efficiency_array);
                                    }
                                }
                            }
                        } else {
                            $efficiency_exist = Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => $month, 'year' => $current_year])->first();
                            if (!$efficiency_exist) {
                                $datesearch = " MONTH(tickets.created_at)= '" . $month . "' AND YEAR(tickets.created_at)= YEAR(CURRENT_DATE())";
                                switch ($user_type) {
                                    case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                                        break;
                                    case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                                        break;
                                    case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                                        break;
                                    case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                                        break;
                                    case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                                        break;
                                    case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                                        break;
                                }
                                if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                                    $bdo_id_list = $bdo_list_query[0]['user_id'];
                                    $bdo_count = count(explode(',', $bdo_id_list));
                                    $total_activity = $this->totalActivity($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    if ($total_activity != 0) {
                                        $total_registered_farmer = $this->totalRegisteredFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                        $total_contacted_farmer = $this->totalContactedFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                        $total_validated_farmer = $this->totalValidatedFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                        $total_nada_farmer = $this->totalNadaFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                        $total_pnc_farmer = $this->totalPncFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                        $total_bn_farmer = $this->totalBnFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);

                                        if ($total_contacted_farmer != 0) {
                                            $nada_percent = number_format((float) (($total_nada_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                            $pnc_percent = number_format((float) (($total_pnc_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                            $bn_percent = number_format((float) (($total_bn_farmer / $total_contacted_farmer) * (100)), 2, '.', '');

                                            $farmer_valid_percent = number_format((float) ((($total_validated_farmer / $total_contacted_farmer) * (100))), 2, '.', '');
                                            $total_ex_valid_farmers = number_format((float) (($total_registered_farmer * $farmer_valid_percent) / 100), 2, '.', '');
                                        }

                                        $average_activity_efficiency = number_format((float) ($total_activity / $CONST_activity_per_month) * 100, 2, '.', '');
                                        $average_validate_farmer = number_format((float) ($total_ex_valid_farmers / $total_activity), 2, '.', '');
                                        $average_farmer_efficiency = number_format((float) ($average_validate_farmer / $CONST_average_farmer_registration) * 100, 2, '.', '');

                                        $nada_deduction = ($nada_percent > 10 ? number_format((float) ((100 - (($nada_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                        $pnc_deduction = ($pnc_percent > 10 ? number_format((float) ((100 - (($pnc_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                        $bn_deduction = ($bn_percent > 10 ? number_format((float) ((100 - (($bn_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');

                                        $nada_deduct = ($nada_deduction == 0 ? 1 : $nada_deduction);
                                        $pnc_deduct = ($pnc_deduction == 0 ? 1 : $pnc_deduction);
                                        $bn_deduct = ($bn_deduction == 0 ? 1 : $bn_deduction);
                                        $efficiency = number_format((float) (($average_activity_efficiency * $average_farmer_efficiency * $nada_deduct * $pnc_deduct * $bn_deduct) * (100)) / (100 * 100), 2, '.', '');

                                        $efficiency_array = array(
                                            'user_id' => $user_id,
                                            'user_type' => $user_type,
                                            'month' => $month,
                                            'year' => $current_year,
                                            'CONST_percent_deduction' => $CONST_percent_deduction,
                                            'CONST_activity_per_month' => $CONST_activity_per_month,
                                            'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                            'total_activity' => $total_activity,
                                            'total_registered_farmer' => $total_registered_farmer,
                                            'total_contacted_farmer' => $total_contacted_farmer,
                                            'total_validated_farmer' => $total_validated_farmer,
                                            'total_nada_farmer' => $total_nada_farmer,
                                            'total_pnc_farmer' => $total_pnc_farmer,
                                            'total_bn_farmer' => $total_bn_farmer,
                                            'nada_percent' => $nada_percent,
                                            'pnc_percent' => $pnc_percent,
                                            'bn_percent' => $bn_percent,
                                            'farmer_valid_percent' => $farmer_valid_percent,
                                            'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                            'average_activity_efficiency' => $average_activity_efficiency,
                                            'average_validate_farmer' => $average_validate_farmer,
                                            'average_farmer_efficiency' => $average_farmer_efficiency,
                                            'nada_deduction' => $nada_deduction,
                                            'pnc_deduction' => $pnc_deduction,
                                            'bn_deduction' => $bn_deduction,
                                            'efficiency' => $efficiency,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                        );
                                        DB::table('efficiency_results')->insert($efficiency_array);
                                    }
                                }
                            }
                        }
                    } else if ($month == $current_month) {
                        if ($month == '01') {
                            $datesearch = " MONTH(tickets.created_at)= '12' AND YEAR(tickets.created_at)= YEAR(CURRENT_DATE())-1";
                            switch ($user_type) {
                                case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                                    break;
                                case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                                    break;
                                case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                                    break;
                                case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                                    break;
                                case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                                    break;
                                case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                                    break;
                            }
                            if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                                $bdo_id_list = $bdo_list_query[0]['user_id'];
                                $bdo_count = count(explode(',', $bdo_id_list));
                                $total_activity = $this->totalActivity($bdo_id_list, $datesearch, $bdo_count);
                                if ($total_activity != 0) {
                                    $total_registered_farmer = $this->totalRegisteredFarmer($bdo_id_list, $datesearch, $bdo_count);
                                    $total_contacted_farmer = $this->totalContactedFarmer($bdo_id_list, $datesearch, $bdo_count);
                                    $total_validated_farmer = $this->totalValidatedFarmer($bdo_id_list, $datesearch, $bdo_count);
                                    $total_nada_farmer = $this->totalNadaFarmer($bdo_id_list, $datesearch, $bdo_count);
                                    $total_pnc_farmer = $this->totalPncFarmer($bdo_id_list, $datesearch, $bdo_count);
                                    $total_bn_farmer = $this->totalBnFarmer($bdo_id_list, $datesearch, $bdo_count);

                                    if ($total_contacted_farmer != 0) {
                                        $nada_percent = number_format((float) (($total_nada_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                        $pnc_percent = number_format((float) (($total_pnc_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                        $bn_percent = number_format((float) (($total_bn_farmer / $total_contacted_farmer) * (100)), 2, '.', '');

                                        $farmer_valid_percent = number_format((float) ((($total_validated_farmer / $total_contacted_farmer) * (100))), 2, '.', '');
                                        $total_ex_valid_farmers = number_format((float) (($total_registered_farmer * $farmer_valid_percent) / 100), 2, '.', '');
                                    }

                                    $average_activity_efficiency = number_format((float) ($total_activity / $CONST_activity_per_month) * 100, 2, '.', '');
                                    $average_validate_farmer = number_format((float) ($total_ex_valid_farmers / $total_activity), 2, '.', '');
                                    $average_farmer_efficiency = number_format((float) ($average_validate_farmer / $CONST_average_farmer_registration) * 100, 2, '.', '');

                                    $nada_deduction = ($nada_percent > 10 ? number_format((float) ((100 - (($nada_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                    $pnc_deduction = ($pnc_percent > 10 ? number_format((float) ((100 - (($pnc_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                    $bn_deduction = ($bn_percent > 10 ? number_format((float) ((100 - (($bn_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');

                                    $nada_deduct = ($nada_deduction == 0 ? 1 : $nada_deduction);
                                    $pnc_deduct = ($pnc_deduction == 0 ? 1 : $pnc_deduction);
                                    $bn_deduct = ($bn_deduction == 0 ? 1 : $bn_deduction);
                                    $efficiency = number_format((float) (($average_activity_efficiency * $average_farmer_efficiency * $nada_deduct * $pnc_deduct * $bn_deduct) * (100)) / (100 * 100), 2, '.', '');

                                    $previous_year = $current_year - 1;
                                    $efficiency_exist = Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => '12', 'year' => $previous_year])->first();
                                    if ($efficiency_exist) {
                                        $efficiency_array = array(
                                            'user_id' => $user_id,
                                            'user_type' => $user_type,
                                            'month' => $month,
                                            'year' => $current_year,
                                            'CONST_percent_deduction' => $CONST_percent_deduction,
                                            'CONST_activity_per_month' => $CONST_activity_per_month,
                                            'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                            'total_activity' => $total_activity,
                                            'total_registered_farmer' => $total_registered_farmer,
                                            'total_contacted_farmer' => $total_contacted_farmer,
                                            'total_validated_farmer' => $total_validated_farmer,
                                            'total_nada_farmer' => $total_nada_farmer,
                                            'total_pnc_farmer' => $total_pnc_farmer,
                                            'total_bn_farmer' => $total_bn_farmer,
                                            'nada_percent' => $nada_percent,
                                            'pnc_percent' => $pnc_percent,
                                            'bn_percent' => $bn_percent,
                                            'farmer_valid_percent' => $farmer_valid_percent,
                                            'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                            'average_activity_efficiency' => $average_activity_efficiency,
                                            'average_validate_farmer' => $average_validate_farmer,
                                            'average_farmer_efficiency' => $average_farmer_efficiency,
                                            'nada_deduction' => $nada_deduction,
                                            'pnc_deduction' => $pnc_deduction,
                                            'bn_deduction' => $bn_deduction,
                                            'efficiency' => $efficiency,
                                            'updated_at' => date('Y-m-d H:i:s')
                                        );
                                        Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => '12', 'year' => $previous_year])->update($efficiency_array);
                                    } else {
                                        $efficiency_array = array(
                                            'user_id' => $user_id,
                                            'user_type' => $user_type,
                                            'month' => '12',
                                            'year' => $previous_year,
                                            'CONST_percent_deduction' => $CONST_percent_deduction,
                                            'CONST_activity_per_month' => $CONST_activity_per_month,
                                            'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                            'total_activity' => $total_activity,
                                            'total_registered_farmer' => $total_registered_farmer,
                                            'total_contacted_farmer' => $total_contacted_farmer,
                                            'total_validated_farmer' => $total_validated_farmer,
                                            'total_nada_farmer' => $total_nada_farmer,
                                            'total_pnc_farmer' => $total_pnc_farmer,
                                            'total_bn_farmer' => $total_bn_farmer,
                                            'nada_percent' => $nada_percent,
                                            'pnc_percent' => $pnc_percent,
                                            'bn_percent' => $bn_percent,
                                            'farmer_valid_percent' => $farmer_valid_percent,
                                            'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                            'average_activity_efficiency' => $average_activity_efficiency,
                                            'average_validate_farmer' => $average_validate_farmer,
                                            'average_farmer_efficiency' => $average_farmer_efficiency,
                                            'nada_deduction' => $nada_deduction,
                                            'pnc_deduction' => $pnc_deduction,
                                            'bn_deduction' => $bn_deduction,
                                            'efficiency' => $efficiency,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                        );
                                        DB::table('efficiency_results')->insert($efficiency_array);
                                    }
                                }
                            }
                        }

                        $datesearch = " MONTH(tickets.created_at)= MONTH(CURRENT_DATE()) AND YEAR(tickets.created_at)= YEAR(CURRENT_DATE())";
                        switch ($user_type) {
                            case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                                break;
                            case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                                break;
                            case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                                break;
                            case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                                break;
                            case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                                break;
                            case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                                break;
                        }
                        if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                            $bdo_id_list = $bdo_list_query[0]['user_id'];
                            $bdo_count = count(explode(',', $bdo_id_list));
                            $total_activity = $this->totalActivity($bdo_id_list, $datesearch, $bdo_count);
                            if ($total_activity != 0) {
                                $CONST_activity_per_month = number_format((float) (date('d') * (22 / date('t'))), 2, '.', '');
                                $total_registered_farmer = $this->totalRegisteredFarmer($bdo_id_list, $datesearch, $bdo_count);
                                $total_contacted_farmer = $this->totalContactedFarmer($bdo_id_list, $datesearch, $bdo_count);
                                $total_validated_farmer = $this->totalValidatedFarmer($bdo_id_list, $datesearch, $bdo_count);
                                $total_nada_farmer = $this->totalNadaFarmer($bdo_id_list, $datesearch, $bdo_count);
                                $total_pnc_farmer = $this->totalPncFarmer($bdo_id_list, $datesearch, $bdo_count);
                                $total_bn_farmer = $this->totalBnFarmer($bdo_id_list, $datesearch, $bdo_count);

                                if ($total_contacted_farmer != 0) {
                                    $nada_percent = number_format((float) (($total_nada_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                    $pnc_percent = number_format((float) (($total_pnc_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                    $bn_percent = number_format((float) (($total_bn_farmer / $total_contacted_farmer) * (100)), 2, '.', '');

                                    $farmer_valid_percent = number_format((float) ((($total_validated_farmer / $total_contacted_farmer) * (100))), 2, '.', '');
                                    $total_ex_valid_farmers = number_format((float) (($total_registered_farmer * $farmer_valid_percent) / 100), 2, '.', '');
                                }

                                $average_activity_efficiency = number_format((float) ($total_activity / $CONST_activity_per_month) * 100, 2, '.', '');
                                $average_validate_farmer = number_format((float) ($total_ex_valid_farmers / $total_activity), 2, '.', '');
                                $average_farmer_efficiency = number_format((float) ($average_validate_farmer / $CONST_average_farmer_registration) * 100, 2, '.', '');

                                $nada_deduction = ($nada_percent > 10 ? number_format((float) ((100 - (($nada_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                $pnc_deduction = ($pnc_percent > 10 ? number_format((float) ((100 - (($pnc_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                $bn_deduction = ($bn_percent > 10 ? number_format((float) ((100 - (($bn_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');

                                $nada_deduct = ($nada_deduction == 0 ? 1 : $nada_deduction);
                                $pnc_deduct = ($pnc_deduction == 0 ? 1 : $pnc_deduction);
                                $bn_deduct = ($bn_deduction == 0 ? 1 : $bn_deduction);
                                $efficiency = number_format((float) (($average_activity_efficiency * $average_farmer_efficiency * $nada_deduct * $pnc_deduct * $bn_deduct) * (100)) / (100 * 100), 2, '.', '');

                                $efficiency_exist = Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => $month, 'year' => $current_year])->first();
                                if ($efficiency_exist) {
                                    $efficiency_array = array(
                                        'user_id' => $user_id,
                                        'user_type' => $user_type,
                                        'month' => $month,
                                        'year' => $current_year,
                                        'CONST_percent_deduction' => $CONST_percent_deduction,
                                        'CONST_activity_per_month' => $CONST_activity_per_month,
                                        'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                        'total_activity' => $total_activity,
                                        'total_registered_farmer' => $total_registered_farmer,
                                        'total_contacted_farmer' => $total_contacted_farmer,
                                        'total_validated_farmer' => $total_validated_farmer,
                                        'total_nada_farmer' => $total_nada_farmer,
                                        'total_pnc_farmer' => $total_pnc_farmer,
                                        'total_bn_farmer' => $total_bn_farmer,
                                        'nada_percent' => $nada_percent,
                                        'pnc_percent' => $pnc_percent,
                                        'bn_percent' => $bn_percent,
                                        'farmer_valid_percent' => $farmer_valid_percent,
                                        'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                        'average_activity_efficiency' => $average_activity_efficiency,
                                        'average_validate_farmer' => $average_validate_farmer,
                                        'average_farmer_efficiency' => $average_farmer_efficiency,
                                        'nada_deduction' => $nada_deduction,
                                        'pnc_deduction' => $pnc_deduction,
                                        'bn_deduction' => $bn_deduction,
                                        'efficiency' => $efficiency,
                                        'updated_at' => date('Y-m-d H:i:s')
                                    );
                                    Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => $month, 'year' => $current_year])->update($efficiency_array);
                                } else {
                                    $efficiency_array = array(
                                        'user_id' => $user_id,
                                        'user_type' => $user_type,
                                        'month' => $month,
                                        'year' => $current_year,
                                        'CONST_percent_deduction' => $CONST_percent_deduction,
                                        'CONST_activity_per_month' => $CONST_activity_per_month,
                                        'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                        'total_activity' => $total_activity,
                                        'total_registered_farmer' => $total_registered_farmer,
                                        'total_contacted_farmer' => $total_contacted_farmer,
                                        'total_validated_farmer' => $total_validated_farmer,
                                        'total_nada_farmer' => $total_nada_farmer,
                                        'total_pnc_farmer' => $total_pnc_farmer,
                                        'total_bn_farmer' => $total_bn_farmer,
                                        'nada_percent' => $nada_percent,
                                        'pnc_percent' => $pnc_percent,
                                        'bn_percent' => $bn_percent,
                                        'farmer_valid_percent' => $farmer_valid_percent,
                                        'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                        'average_activity_efficiency' => $average_activity_efficiency,
                                        'average_validate_farmer' => $average_validate_farmer,
                                        'average_farmer_efficiency' => $average_farmer_efficiency,
                                        'nada_deduction' => $nada_deduction,
                                        'pnc_deduction' => $pnc_deduction,
                                        'bn_deduction' => $bn_deduction,
                                        'efficiency' => $efficiency,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    );
                                    DB::table('efficiency_results')->insert($efficiency_array);
                                }
                            }
                        }
                    } else {

                    }
                }
            }
        }
    }

    public function getTicketQualifiedStatus() {
        $tickets = Ticket::where([['farmer_count', '>=', '15']])->get();
        if ($tickets) {
            foreach ($tickets as $ticket) {
                $registered_farmer = $ticket->farmer_count;
                $validated_farmers = Farmer::where('validation_status', 'Validated')->where('ticket_id', $ticket->ticket_id)->count();
                $contacted_farmers = Farmer::whereIn('validation_status', ['Validated', 'NADA', 'PNC', 'BN'])->where('ticket_id', $ticket->ticket_id)->count();

                if ($contacted_farmers != 0) {
                    $validation_percent = number_format((float) ($validated_farmers / $contacted_farmers), 2, '.', '');
                    $extrapolated_farmers = $registered_farmer * $validation_percent;
                    if ($extrapolated_farmers >= 15) {
                        Ticket::where('ticket_id', $ticket->ticket_id)->update(['is_qualified' => '1', 'validated_farmers' => $validated_farmers]);
                    } else {
                        Ticket::where('ticket_id', $ticket->ticket_id)->update(['is_qualified' => '0', 'validated_farmers' => $validated_farmers]);
                    }
                }
            }
        }
    }

    
    public function getHardCalculateEfficiency() {
        $user_list = $this->getAllUser();
        if ($user_list) {
            foreach ($user_list as $bdo) {
                $user_id = $bdo->id;
                $user_type = $bdo->user_type;

                $month_list = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
                $current_year = date('Y');
                $current_month = date('m');

                $average_activity_efficiency = 0;
                $average_farmer_efficiency = 0;
                $nada_percent = 0;
                $pnc_percent = 0;
                $bn_percent = 0;
                $total_ex_valid_farmers = 0;
                $CONST_activity_per_month = 22;
                $CONST_average_farmer_registration = 35;
                $CONST_percent_deduction = 3;
                $average_registered_farmer = 0;
                $farmer_valid_percent = 0;

                foreach ($month_list as $month) {
                    if ($month < $current_month) {
                        if (($current_month - 1) == $month) {
                            $datesearch = " MONTH(tickets.created_at)= '" . $month . "' AND YEAR(tickets.created_at)= YEAR(CURRENT_DATE())";
                            switch ($user_type) {
                                case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                                    break;
                                case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                                    break;
                                case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                                    break;
                                case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                                    break;
                                case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                                    break;
                                case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                                    break;
                            }
                            if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                                $bdo_id_list = $bdo_list_query[0]['user_id'];
                                $bdo_count = count(explode(',', $bdo_id_list));
                                $total_activity = $this->totalActivity($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                if ($total_activity != 0) {
                                    $total_registered_farmer = $this->totalRegisteredFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    $total_contacted_farmer = $this->totalContactedFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    $total_validated_farmer = $this->totalValidatedFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    $total_nada_farmer = $this->totalNadaFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    $total_pnc_farmer = $this->totalPncFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    $total_bn_farmer = $this->totalBnFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);

                                    if ($total_contacted_farmer != 0) {
                                        $nada_percent = number_format((float) (($total_nada_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                        $pnc_percent = number_format((float) (($total_pnc_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                        $bn_percent = number_format((float) (($total_bn_farmer / $total_contacted_farmer) * (100)), 2, '.', '');

                                        $farmer_valid_percent = number_format((float) ((($total_validated_farmer / $total_contacted_farmer) * (100))), 2, '.', '');
                                        $total_ex_valid_farmers = number_format((float) (($total_registered_farmer * $farmer_valid_percent) / 100), 2, '.', '');
                                    }

                                    $average_activity_efficiency = number_format((float) ($total_activity / $CONST_activity_per_month) * 100, 2, '.', '');
                                    $average_validate_farmer = number_format((float) ($total_ex_valid_farmers / $total_activity), 2, '.', '');
                                    $average_farmer_efficiency = number_format((float) ($average_validate_farmer / $CONST_average_farmer_registration) * 100, 2, '.', '');

                                    $nada_deduction = ($nada_percent > 10 ? number_format((float) ((100 - (($nada_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                    $pnc_deduction = ($pnc_percent > 10 ? number_format((float) ((100 - (($pnc_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                    $bn_deduction = ($bn_percent > 10 ? number_format((float) ((100 - (($bn_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');

                                    $nada_deduct = ($nada_deduction == 0 ? 1 : $nada_deduction);
                                    $pnc_deduct = ($pnc_deduction == 0 ? 1 : $pnc_deduction);
                                    $bn_deduct = ($bn_deduction == 0 ? 1 : $bn_deduction);
                                    $efficiency = number_format((float) (($average_activity_efficiency * $average_farmer_efficiency * $nada_deduct * $pnc_deduct * $bn_deduct) * (100)) / (100 * 100), 2, '.', '');

                                    $efficiency_exist = Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => $month, 'year' => $current_year])->first();
                                    if ($efficiency_exist) {
                                        $efficiency_array = array(
                                            'user_id' => $user_id,
                                            'user_type' => $user_type,
                                            'month' => $month,
                                            'year' => $current_year,
                                            'CONST_percent_deduction' => $CONST_percent_deduction,
                                            'CONST_activity_per_month' => $CONST_activity_per_month,
                                            'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                            'total_activity' => $total_activity,
                                            'total_registered_farmer' => $total_registered_farmer,
                                            'total_contacted_farmer' => $total_contacted_farmer,
                                            'total_validated_farmer' => $total_validated_farmer,
                                            'total_nada_farmer' => $total_nada_farmer,
                                            'total_pnc_farmer' => $total_pnc_farmer,
                                            'total_bn_farmer' => $total_bn_farmer,
                                            'nada_percent' => $nada_percent,
                                            'pnc_percent' => $pnc_percent,
                                            'bn_percent' => $bn_percent,
                                            'farmer_valid_percent' => $farmer_valid_percent,
                                            'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                            'average_activity_efficiency' => $average_activity_efficiency,
                                            'average_validate_farmer' => $average_validate_farmer,
                                            'average_farmer_efficiency' => $average_farmer_efficiency,
                                            'nada_deduction' => $nada_deduction,
                                            'pnc_deduction' => $pnc_deduction,
                                            'bn_deduction' => $bn_deduction,
                                            'efficiency' => $efficiency,
                                            'updated_at' => date('Y-m-d H:i:s')
                                        );
                                        Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => $month, 'year' => $current_year])->update($efficiency_array);
                                    } else {
                                        $efficiency_array = array(
                                            'user_id' => $user_id,
                                            'user_type' => $user_type,
                                            'month' => $month,
                                            'year' => $current_year,
                                            'CONST_percent_deduction' => $CONST_percent_deduction,
                                            'CONST_activity_per_month' => $CONST_activity_per_month,
                                            'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                            'total_activity' => $total_activity,
                                            'total_registered_farmer' => $total_registered_farmer,
                                            'total_contacted_farmer' => $total_contacted_farmer,
                                            'total_validated_farmer' => $total_validated_farmer,
                                            'total_nada_farmer' => $total_nada_farmer,
                                            'total_pnc_farmer' => $total_pnc_farmer,
                                            'total_bn_farmer' => $total_bn_farmer,
                                            'nada_percent' => $nada_percent,
                                            'pnc_percent' => $pnc_percent,
                                            'bn_percent' => $bn_percent,
                                            'farmer_valid_percent' => $farmer_valid_percent,
                                            'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                            'average_activity_efficiency' => $average_activity_efficiency,
                                            'average_validate_farmer' => $average_validate_farmer,
                                            'average_farmer_efficiency' => $average_farmer_efficiency,
                                            'nada_deduction' => $nada_deduction,
                                            'pnc_deduction' => $pnc_deduction,
                                            'bn_deduction' => $bn_deduction,
                                            'efficiency' => $efficiency,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                        );
                                        DB::table('efficiency_results')->insert($efficiency_array);
                                    }
                                }
                            }
                        } else {
                            $efficiency_exist = Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => $month, 'year' => $current_year])->first();
                            if (!$efficiency_exist) {
                                $datesearch = " MONTH(tickets.created_at)= '" . $month . "' AND YEAR(tickets.created_at)= YEAR(CURRENT_DATE())";
                                switch ($user_type) {
                                    case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                                        break;
                                    case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                                        break;
                                    case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                                        break;
                                    case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                                        break;
                                    case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                                        break;
                                    case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                                        break;
                                }
                                if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                                    $bdo_id_list = $bdo_list_query[0]['user_id'];
                                    $bdo_count = count(explode(',', $bdo_id_list));
                                    $total_activity = $this->totalActivity($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                    if ($total_activity != 0) {
                                        $total_registered_farmer = $this->totalRegisteredFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                        $total_contacted_farmer = $this->totalContactedFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                        $total_validated_farmer = $this->totalValidatedFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                        $total_nada_farmer = $this->totalNadaFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                        $total_pnc_farmer = $this->totalPncFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);
                                        $total_bn_farmer = $this->totalBnFarmer($bdo_id_list, $datesearch, $bdo_count, $bdo_count);

                                        if ($total_contacted_farmer != 0) {
                                            $nada_percent = number_format((float) (($total_nada_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                            $pnc_percent = number_format((float) (($total_pnc_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                            $bn_percent = number_format((float) (($total_bn_farmer / $total_contacted_farmer) * (100)), 2, '.', '');

                                            $farmer_valid_percent = number_format((float) ((($total_validated_farmer / $total_contacted_farmer) * (100))), 2, '.', '');
                                            $total_ex_valid_farmers = number_format((float) (($total_registered_farmer * $farmer_valid_percent) / 100), 2, '.', '');
                                        }

                                        $average_activity_efficiency = number_format((float) ($total_activity / $CONST_activity_per_month) * 100, 2, '.', '');
                                        $average_validate_farmer = number_format((float) ($total_ex_valid_farmers / $total_activity), 2, '.', '');
                                        $average_farmer_efficiency = number_format((float) ($average_validate_farmer / $CONST_average_farmer_registration) * 100, 2, '.', '');

                                        $nada_deduction = ($nada_percent > 10 ? number_format((float) ((100 - (($nada_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                        $pnc_deduction = ($pnc_percent > 10 ? number_format((float) ((100 - (($pnc_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                        $bn_deduction = ($bn_percent > 10 ? number_format((float) ((100 - (($bn_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');

                                        $nada_deduct = ($nada_deduction == 0 ? 1 : $nada_deduction);
                                        $pnc_deduct = ($pnc_deduction == 0 ? 1 : $pnc_deduction);
                                        $bn_deduct = ($bn_deduction == 0 ? 1 : $bn_deduction);
                                        $efficiency = number_format((float) (($average_activity_efficiency * $average_farmer_efficiency * $nada_deduct * $pnc_deduct * $bn_deduct) * (100)) / (100 * 100), 2, '.', '');

                                        $efficiency_array = array(
                                            'user_id' => $user_id,
                                            'user_type' => $user_type,
                                            'month' => $month,
                                            'year' => $current_year,
                                            'CONST_percent_deduction' => $CONST_percent_deduction,
                                            'CONST_activity_per_month' => $CONST_activity_per_month,
                                            'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                            'total_activity' => $total_activity,
                                            'total_registered_farmer' => $total_registered_farmer,
                                            'total_contacted_farmer' => $total_contacted_farmer,
                                            'total_validated_farmer' => $total_validated_farmer,
                                            'total_nada_farmer' => $total_nada_farmer,
                                            'total_pnc_farmer' => $total_pnc_farmer,
                                            'total_bn_farmer' => $total_bn_farmer,
                                            'nada_percent' => $nada_percent,
                                            'pnc_percent' => $pnc_percent,
                                            'bn_percent' => $bn_percent,
                                            'farmer_valid_percent' => $farmer_valid_percent,
                                            'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                            'average_activity_efficiency' => $average_activity_efficiency,
                                            'average_validate_farmer' => $average_validate_farmer,
                                            'average_farmer_efficiency' => $average_farmer_efficiency,
                                            'nada_deduction' => $nada_deduction,
                                            'pnc_deduction' => $pnc_deduction,
                                            'bn_deduction' => $bn_deduction,
                                            'efficiency' => $efficiency,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                        );
                                        DB::table('efficiency_results')->insert($efficiency_array);
                                    }
                                }
                            }
                        }
                    } else if ($month == $current_month) {
                        if ($month == '01') {
                            $datesearch = " MONTH(tickets.created_at)= '12' AND YEAR(tickets.created_at)= YEAR(CURRENT_DATE())-1";
                            switch ($user_type) {
                                case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                                    break;
                                case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                                    break;
                                case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                                    break;
                                case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                                    break;
                                case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                                    break;
                                case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                                    break;
                            }
                            if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                                $bdo_id_list = $bdo_list_query[0]['user_id'];
                                $bdo_count = count(explode(',', $bdo_id_list));
                                $total_activity = $this->totalActivity($bdo_id_list, $datesearch, $bdo_count);
                                if ($total_activity != 0) {
                                    $total_registered_farmer = $this->totalRegisteredFarmer($bdo_id_list, $datesearch, $bdo_count);
                                    $total_contacted_farmer = $this->totalContactedFarmer($bdo_id_list, $datesearch, $bdo_count);
                                    $total_validated_farmer = $this->totalValidatedFarmer($bdo_id_list, $datesearch, $bdo_count);
                                    $total_nada_farmer = $this->totalNadaFarmer($bdo_id_list, $datesearch, $bdo_count);
                                    $total_pnc_farmer = $this->totalPncFarmer($bdo_id_list, $datesearch, $bdo_count);
                                    $total_bn_farmer = $this->totalBnFarmer($bdo_id_list, $datesearch, $bdo_count);

                                    if ($total_contacted_farmer != 0) {
                                        $nada_percent = number_format((float) (($total_nada_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                        $pnc_percent = number_format((float) (($total_pnc_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                        $bn_percent = number_format((float) (($total_bn_farmer / $total_contacted_farmer) * (100)), 2, '.', '');

                                        $farmer_valid_percent = number_format((float) ((($total_validated_farmer / $total_contacted_farmer) * (100))), 2, '.', '');
                                        $total_ex_valid_farmers = number_format((float) (($total_registered_farmer * $farmer_valid_percent) / 100), 2, '.', '');
                                    }

                                    $average_activity_efficiency = number_format((float) ($total_activity / $CONST_activity_per_month) * 100, 2, '.', '');
                                    $average_validate_farmer = number_format((float) ($total_ex_valid_farmers / $total_activity), 2, '.', '');
                                    $average_farmer_efficiency = number_format((float) ($average_validate_farmer / $CONST_average_farmer_registration) * 100, 2, '.', '');

                                    $nada_deduction = ($nada_percent > 10 ? number_format((float) ((100 - (($nada_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                    $pnc_deduction = ($pnc_percent > 10 ? number_format((float) ((100 - (($pnc_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                    $bn_deduction = ($bn_percent > 10 ? number_format((float) ((100 - (($bn_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');

                                    $nada_deduct = ($nada_deduction == 0 ? 1 : $nada_deduction);
                                    $pnc_deduct = ($pnc_deduction == 0 ? 1 : $pnc_deduction);
                                    $bn_deduct = ($bn_deduction == 0 ? 1 : $bn_deduction);
                                    $efficiency = number_format((float) (($average_activity_efficiency * $average_farmer_efficiency * $nada_deduct * $pnc_deduct * $bn_deduct) * (100)) / (100 * 100), 2, '.', '');

                                    $previous_year = $current_year - 1;
                                    $efficiency_exist = Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => '12', 'year' => $previous_year])->first();
                                    if ($efficiency_exist) {
                                        $efficiency_array = array(
                                            'user_id' => $user_id,
                                            'user_type' => $user_type,
                                            'month' => $month,
                                            'year' => $current_year,
                                            'CONST_percent_deduction' => $CONST_percent_deduction,
                                            'CONST_activity_per_month' => $CONST_activity_per_month,
                                            'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                            'total_activity' => $total_activity,
                                            'total_registered_farmer' => $total_registered_farmer,
                                            'total_contacted_farmer' => $total_contacted_farmer,
                                            'total_validated_farmer' => $total_validated_farmer,
                                            'total_nada_farmer' => $total_nada_farmer,
                                            'total_pnc_farmer' => $total_pnc_farmer,
                                            'total_bn_farmer' => $total_bn_farmer,
                                            'nada_percent' => $nada_percent,
                                            'pnc_percent' => $pnc_percent,
                                            'bn_percent' => $bn_percent,
                                            'farmer_valid_percent' => $farmer_valid_percent,
                                            'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                            'average_activity_efficiency' => $average_activity_efficiency,
                                            'average_validate_farmer' => $average_validate_farmer,
                                            'average_farmer_efficiency' => $average_farmer_efficiency,
                                            'nada_deduction' => $nada_deduction,
                                            'pnc_deduction' => $pnc_deduction,
                                            'bn_deduction' => $bn_deduction,
                                            'efficiency' => $efficiency,
                                            'updated_at' => date('Y-m-d H:i:s')
                                        );
                                        Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => '12', 'year' => $previous_year])->update($efficiency_array);
                                    } else {
                                        $efficiency_array = array(
                                            'user_id' => $user_id,
                                            'user_type' => $user_type,
                                            'month' => '12',
                                            'year' => $previous_year,
                                            'CONST_percent_deduction' => $CONST_percent_deduction,
                                            'CONST_activity_per_month' => $CONST_activity_per_month,
                                            'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                            'total_activity' => $total_activity,
                                            'total_registered_farmer' => $total_registered_farmer,
                                            'total_contacted_farmer' => $total_contacted_farmer,
                                            'total_validated_farmer' => $total_validated_farmer,
                                            'total_nada_farmer' => $total_nada_farmer,
                                            'total_pnc_farmer' => $total_pnc_farmer,
                                            'total_bn_farmer' => $total_bn_farmer,
                                            'nada_percent' => $nada_percent,
                                            'pnc_percent' => $pnc_percent,
                                            'bn_percent' => $bn_percent,
                                            'farmer_valid_percent' => $farmer_valid_percent,
                                            'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                            'average_activity_efficiency' => $average_activity_efficiency,
                                            'average_validate_farmer' => $average_validate_farmer,
                                            'average_farmer_efficiency' => $average_farmer_efficiency,
                                            'nada_deduction' => $nada_deduction,
                                            'pnc_deduction' => $pnc_deduction,
                                            'bn_deduction' => $bn_deduction,
                                            'efficiency' => $efficiency,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                        );
                                        DB::table('efficiency_results')->insert($efficiency_array);
                                    }
                                }
                            }
                        }

                        $datesearch = " MONTH(tickets.created_at)= MONTH(CURRENT_DATE()) AND YEAR(tickets.created_at)= YEAR(CURRENT_DATE())";
                        switch ($user_type) {
                            case "zonal" : $bdo_list_query = $this->categoryWiseBdoListForQuery('zonal_manager_id', $user_id);
                                break;
                            case "regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('regional_manager_id', $user_id);
                                break;
                            case "area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('area_manager_id', $user_id);
                                break;
                            case "sales_regional" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_regional_manager_id', $user_id);
                                break;
                            case "sales_area" : $bdo_list_query = $this->categoryWiseBdoListForQuery('sales_area_manager_id', $user_id);
                                break;
                            case "bdo" : $bdo_list_query = $this->categoryWiseBdoListForQuery('id', $user_id);
                                break;
                        }
                        if ($bdo_list_query && $bdo_list_query[0]['user_id'] != '') {
                            $bdo_id_list = $bdo_list_query[0]['user_id'];
                            $bdo_count = count(explode(',', $bdo_id_list));
                            $total_activity = $this->totalActivity($bdo_id_list, $datesearch, $bdo_count);
                            if ($total_activity != 0) {
                                $CONST_activity_per_month = number_format((float) (date('d') * (22 / date('t'))), 2, '.', '');
                                $total_registered_farmer = $this->totalRegisteredFarmer($bdo_id_list, $datesearch, $bdo_count);
                                $total_contacted_farmer = $this->totalContactedFarmer($bdo_id_list, $datesearch, $bdo_count);
                                $total_validated_farmer = $this->totalValidatedFarmer($bdo_id_list, $datesearch, $bdo_count);
                                $total_nada_farmer = $this->totalNadaFarmer($bdo_id_list, $datesearch, $bdo_count);
                                $total_pnc_farmer = $this->totalPncFarmer($bdo_id_list, $datesearch, $bdo_count);
                                $total_bn_farmer = $this->totalBnFarmer($bdo_id_list, $datesearch, $bdo_count);

                                if ($total_contacted_farmer != 0) {
                                    $nada_percent = number_format((float) (($total_nada_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                    $pnc_percent = number_format((float) (($total_pnc_farmer / $total_contacted_farmer) * (100)), 2, '.', '');
                                    $bn_percent = number_format((float) (($total_bn_farmer / $total_contacted_farmer) * (100)), 2, '.', '');

                                    $farmer_valid_percent = number_format((float) ((($total_validated_farmer / $total_contacted_farmer) * (100))), 2, '.', '');
                                    $total_ex_valid_farmers = number_format((float) (($total_registered_farmer * $farmer_valid_percent) / 100), 2, '.', '');
                                }

                                $average_activity_efficiency = number_format((float) ($total_activity / $CONST_activity_per_month) * 100, 2, '.', '');
                                $average_validate_farmer = number_format((float) ($total_ex_valid_farmers / $total_activity), 2, '.', '');
                                $average_farmer_efficiency = number_format((float) ($average_validate_farmer / $CONST_average_farmer_registration) * 100, 2, '.', '');

                                $nada_deduction = ($nada_percent > 10 ? number_format((float) ((100 - (($nada_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                $pnc_deduction = ($pnc_percent > 10 ? number_format((float) ((100 - (($pnc_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');
                                $bn_deduction = ($bn_percent > 10 ? number_format((float) ((100 - (($bn_percent - 10) * ($CONST_percent_deduction))) / 100), 2, '.', '') : '0');

                                $nada_deduct = ($nada_deduction == 0 ? 1 : $nada_deduction);
                                $pnc_deduct = ($pnc_deduction == 0 ? 1 : $pnc_deduction);
                                $bn_deduct = ($bn_deduction == 0 ? 1 : $bn_deduction);
                                $efficiency = number_format((float) (($average_activity_efficiency * $average_farmer_efficiency * $nada_deduct * $pnc_deduct * $bn_deduct) * (100)) / (100 * 100), 2, '.', '');

                                $efficiency_exist = Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => $month, 'year' => $current_year])->first();
                                if ($efficiency_exist) {
                                    $efficiency_array = array(
                                        'user_id' => $user_id,
                                        'user_type' => $user_type,
                                        'month' => $month,
                                        'year' => $current_year,
                                        'CONST_percent_deduction' => $CONST_percent_deduction,
                                        'CONST_activity_per_month' => $CONST_activity_per_month,
                                        'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                        'total_activity' => $total_activity,
                                        'total_registered_farmer' => $total_registered_farmer,
                                        'total_contacted_farmer' => $total_contacted_farmer,
                                        'total_validated_farmer' => $total_validated_farmer,
                                        'total_nada_farmer' => $total_nada_farmer,
                                        'total_pnc_farmer' => $total_pnc_farmer,
                                        'total_bn_farmer' => $total_bn_farmer,
                                        'nada_percent' => $nada_percent,
                                        'pnc_percent' => $pnc_percent,
                                        'bn_percent' => $bn_percent,
                                        'farmer_valid_percent' => $farmer_valid_percent,
                                        'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                        'average_activity_efficiency' => $average_activity_efficiency,
                                        'average_validate_farmer' => $average_validate_farmer,
                                        'average_farmer_efficiency' => $average_farmer_efficiency,
                                        'nada_deduction' => $nada_deduction,
                                        'pnc_deduction' => $pnc_deduction,
                                        'bn_deduction' => $bn_deduction,
                                        'efficiency' => $efficiency,
                                        'updated_at' => date('Y-m-d H:i:s')
                                    );
                                    Efficiency::where(['user_id' => $user_id, 'user_type' => $user_type, 'month' => $month, 'year' => $current_year])->update($efficiency_array);
                                } else {
                                    $efficiency_array = array(
                                        'user_id' => $user_id,
                                        'user_type' => $user_type,
                                        'month' => $month,
                                        'year' => $current_year,
                                        'CONST_percent_deduction' => $CONST_percent_deduction,
                                        'CONST_activity_per_month' => $CONST_activity_per_month,
                                        'CONST_average_farmer_registration' => $CONST_average_farmer_registration,
                                        'total_activity' => $total_activity,
                                        'total_registered_farmer' => $total_registered_farmer,
                                        'total_contacted_farmer' => $total_contacted_farmer,
                                        'total_validated_farmer' => $total_validated_farmer,
                                        'total_nada_farmer' => $total_nada_farmer,
                                        'total_pnc_farmer' => $total_pnc_farmer,
                                        'total_bn_farmer' => $total_bn_farmer,
                                        'nada_percent' => $nada_percent,
                                        'pnc_percent' => $pnc_percent,
                                        'bn_percent' => $bn_percent,
                                        'farmer_valid_percent' => $farmer_valid_percent,
                                        'total_ex_valid_farmers' => $total_ex_valid_farmers,
                                        'average_activity_efficiency' => $average_activity_efficiency,
                                        'average_validate_farmer' => $average_validate_farmer,
                                        'average_farmer_efficiency' => $average_farmer_efficiency,
                                        'nada_deduction' => $nada_deduction,
                                        'pnc_deduction' => $pnc_deduction,
                                        'bn_deduction' => $bn_deduction,
                                        'efficiency' => $efficiency,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    );
                                    DB::table('efficiency_results')->insert($efficiency_array);
                                }
                            }
                        }
                    } else {

                    }
                }
            }
        }
        return redirect('admin/dashboard');
    }


    public function getHardTicketQualifiedStatus() {

        $tickets = Ticket::where('created_at', '>', (new \Carbon\Carbon)->submonths(3) )->get();
      
        if ($tickets) {
            foreach ($tickets as $ticket) {
                $registered_farmer = $ticket->farmer_count;
                $validated_farmers = Farmer::where('validation_status', 'Validated')->where('ticket_id', $ticket->ticket_id)->count();

                $contacted_farmers = Farmer::whereIn('validation_status', ['Validated', 'NADA', 'PNC', 'BN'])->where('ticket_id', $ticket->ticket_id)->count();

                if ($contacted_farmers != 0) {
                    $validation_percent = number_format((float) ($validated_farmers / $contacted_farmers), 2, '.', '');
                    $extrapolated_farmers = $registered_farmer * $validation_percent;
                    if ($extrapolated_farmers >= 15) {
                        Ticket::where('ticket_id', $ticket->ticket_id)->update(['is_qualified' => '1', 'validated_farmers' => $validated_farmers]);
                    } else {
                        Ticket::where('ticket_id', $ticket->ticket_id)->update(['is_qualified' => '0', 'validated_farmers' => $validated_farmers]);
                    }
                }
            }
            return redirect('admin/dashboard');
        }
    }


    public function getUpdateBdoFarmerCount() {


      

       // $query="SELECT F.farmer_id, F.ticket_id, T.farmer_count, COUNT(F.mobile)  
        $query="SELECT  F.ticket_id  ,T.farmer_count
        FROM tickets T
        LEFT JOIN farmers F ON T.ticket_id=F.ticket_id
        WHERE 
        F.ticket_status='1'
        GROUP BY F.ticket_id
        HAVING T.farmer_count<>COUNT(F.mobile)";

         $tickets = DB::select(DB::raw($query));

         //print_r($tickets);

         foreach ($tickets as $ticket) {
             # code...

            $duplicate_row_query = "SELECT farmer_id, COUNT(mobile) AS duplicate_rows  
            FROM `farmers`
            WHERE `ticket_id` = $ticket->ticket_id AND `ticket_status` = 1
            GROUP BY `mobile` 
            HAVING COUNT(mobile) > 1";

            $duplicate_rows = DB::select(DB::raw($duplicate_row_query));

            //dd($duplicate_rows[0]->duplicate_rows);

            while($duplicate_rows>1) {
                $update_query =" UPDATE farmers 
                JOIN (SELECT farmer_id 
                FROM `farmers`
                WHERE `ticket_id` = $ticket->ticket_id AND `ticket_status` = 1
                GROUP BY `mobile` 
                HAVING COUNT(mobile) > 1) x ON x.farmer_id = farmers.farmer_id
                SET ticket_status = '3'";
                //dd($update_query);

                DB::update(DB::raw($update_query));
            }


            

         }

    }

    public function getSendWhatsappMessage() {
        $logFile = getcwd()."/storage/cron_log.txt";
        $myfile = fopen($logFile, "a");
        $crr_date = date('Y-m-d H:i:s');
        fwrite($myfile, "crr_date=>".$crr_date."\n");
        $getcircular = Circular::where('published_date','<=',$crr_date)->where('status',1)->where('send',0)->select('circular_id','circular_name','contact_ids','published_date','status','send','circular_path','institutionalType')->get();
        // fwrite($myfile, json_encode($getcircular)."\n");
        if(count($getcircular)>0) {
            // echo "<pre>"; print_r($getcircular); exit();
            foreach ($getcircular as $key => $circular) {
                $waNumber           = array();
                $circular_id        = $circular->circular_id;
                fwrite($myfile, "circular_id=>".$circular_id."\n");
                // $circular_path_url  = url('/').'/circular/'.$circular->circular_path;
                $circular_path_url  = 'http://dhanukabdapp.in:8088/dhanukaV2/circular/'.$circular->circular_path;
                $circular_name      = $circular->circular_name;
                $circular_contact   = $circular->contact_ids;
                $circular_contacts  = explode(',', $circular_contact);
                $body = $circular_name."\n".$circular_path_url;
                if($circular->institutionalType==1) {
                    $contacts = TeaEstateContact::whereIn('tea_estate_contact_id',$circular_contacts)->where('status',1)->select('wa_number',DB::raw('"'.$circular_path_url.'" as circular_path'))->get();
                } else if($circular->institutionalType==2) {
                    $contacts = SugarmillContact::whereIn('sugarmill_contact_id',$circular_contacts)->where('status',1)->select('wa_number',DB::raw('"'.$circular_path_url.'" as circular_path'))->get();
                } else {
                    $contacts = InfluencerContact::whereIn('influencer_contact_id',$circular_contacts)->where('status',1)->select('wa_number',DB::raw('"'.$circular_path_url.'" as circular_path'))->get();
                }
                // fwrite($myfile, json_encode($contacts)."\n");
                // echo "<pre>"; print_r($contacts); exit();
                if(count($contacts)>0) {
                    foreach ($contacts as $key => $contact) {
                        $wa_number  = $contact->wa_number;
                        // echo "=>".$wa_number; exit();
                        // echo "=>".$circular->circular_id; exit();
                        $contact_arr= array($wa_number,$circular_path_url);
                        // fwrite($myfile, json_encode($contact_arr)."\n");
                        $waNumber[] = $contact_arr;
                        // $token      = "nsr77bujr6xfr09a";
                        // $instance_id= "instance2816";
                        $token      = "4817opus1441oua0";
                        $instance_id= "instance3682";
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://api.ultramsg.com/".$instance_id."/messages/chat",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_SSL_VERIFYHOST => 0,
                            CURLOPT_SSL_VERIFYPEER => 0,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => "token=".$token."&to=+91".$wa_number."&body=".$body."&priority=10&referenceId=".$circular->circular_id,
                            CURLOPT_HTTPHEADER => array(
                                "content-type: application/x-www-form-urlencoded"
                            ),
                        ));
                        $response = curl_exec($curl);
                        $err = curl_error($curl);
                        curl_close($curl);
                        if (!$err) {
                            // echo $response;
                            fwrite($myfile, "Success #: ".$response."\n");
                            Circular::where('circular_id',$circular_id)->update(['send'=> 1,'updated_at'=> date('Y-m-d H:i:s')]);
                        } else {
                            fwrite($myfile, "cURL Error #: ".json_encode($err)."\n");
                        }
                    }
                }
                fwrite($myfile, "After loop \n".json_encode($waNumber)."\n");
                // fwrite($myfile, "WhatsApp Message Sent\n");
            }
        }
        fclose($myfile);
        echo "WhatsApp Message Sent.";
    }

    public function getSendFarmerWhatsappMessage() {
        $logFile = getcwd()."/storage/farmer_wp_cron_log.txt";
        $myfile = fopen($logFile, "a");
        $crr_date = date('Y-m-d H:i:s');
        fwrite($myfile, "cron crr_date=>".$crr_date."\n");
        $getFarmers = Farmer::select('farmer_id','mobile')->where('ticket_status',1)->where('cron_status',1)->get();
        if(count($getFarmers)>0) {
            $api_key = "DLRxD8131y6pQGO0vVkQyCJ7AK";
            $farmers_mobile = array();
            foreach ($getFarmers as $key => $farmers) {
                $farmer_id  = $farmers->farmer_id;
                $mobile     = $farmers->mobile;
                fwrite($myfile, "farmer_id=>".$farmer_id."\n");
                $farmers_mobile[] = $mobile;
                $post_data = '{
                    "template": {
                        "components": [
                            {
                                "type": "button",
                                "index": 0,
                                "parameters": [
                                    {
                                        "type": "payload",
                                        "payload": "flow_7CB4B19E95DD45C89381762525EEBD1C"
                                    }
                                ],
                                "sub_type": "quick_reply"
                            },
                            {
                                "type": "button",
                                "index": 1,
                                "parameters": [
                                    {
                                        "type": "payload",
                                        "payload": "flow_3D0E063AC31E4428835C90FAFDEF4F43"
                                    }
                                ],
                                "sub_type": "quick_reply"
                            }
                        ],
                        "namespace": "a8cd8a40_0239_4ba6_8ac7_34edceec36d7",
                        "name": "meeting",
                        "language": {
                            "code": "en",
                            "policy": "deterministic"
                        }
                    },
                    "to": "91'.$mobile.'",
                    "type": "template"
                }';

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://waba.360dialog.io/v1/messages",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $post_data,
                    CURLOPT_HTTPHEADER => array(
                        "content-type: application/json",
                        "D360-API-KEY: ".$api_key
                    ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if (!$err) {
                    fwrite($myfile, "Success #: ".$response."\n");
                    DB::table('farmers')->where('farmer_id', $farmer_id)->update(['cron_status' => '0','updated_at'=> date('Y-m-d H:i:s')]);
                } else {
                    fwrite($myfile, "cURL Error #: ".json_encode($err)."\n");
                }
            }
            fwrite($myfile, "After loop \n".json_encode($farmers_mobile)."\n");
            /*echo "<pre>";
            print_r($response);
            print_r($err);
            echo "</pre>";*/
        }
        fwrite($myfile, "cron end_date=>".date('Y-m-d H:i:s')."\n");
        fclose($myfile);
        echo "WhatsApp Message Sent to Farmers.";
    }
}
