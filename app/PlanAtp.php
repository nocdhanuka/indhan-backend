<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class PlanAtp extends Model
{
    protected   $primaryKey = 'plan_atp_id';
    public      $timestamps = false;
    protected   $table      = 'plan_atp';
    protected   $fillable   = [
                                'plan_date',
                                'product_id',
                                'product_id_2',
                                'product_id_3',
                                'crop_id',
                                'other_crop_1',
                                'other_crop_2',
                                'data_source_id',
                                'state_id', 
                                'district_id', 
                                'tehsil_id',
                                'territory_id',
                                'village',
                                'expected_farmer',
                                'expected_pda',
                                'demo_request_id',
                                'dd_id',
                                'created_by_user_id',
                                'user_type',
                                'status',
                                'reason',
                                'is_deleted',
                                'created_at',
                                'updated_at',
                                'language_id',
                                'village_id',
                                'user_farmer',
                                'record_id',
                                'forwhom',
                                'presenter_id',
                                'presenter_type',
                                'monitor_id',
                                'monitor_type'
                            ];
}
