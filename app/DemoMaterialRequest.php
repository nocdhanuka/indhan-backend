<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class DemoMaterialRequest extends Model
{
    protected $table    = 'demo_material_request';
    public $primaryKey='demo_material_request_id';
    protected $fillable = [
                            'requested_by_ecn',
                            's1_ecn_no',
                            's1_ecn_type',
                            's2_ecn_no',
                            's2_ecn_type',
                            's1_ecn_name',
                            'product_id',
                            'product_name',
                            'crop_id',
                            'crop_name',
                            'expected_demos',
                            'need_by_date',
                            'requested_units',
                            'godown_id',
                            'godown_name',
                            'created_at',
                            'updated_at',
                            'rsm_approval_status',
                            'rsm_ecn_no',
                            'rsm_approved_unit',
                            'rsm_approval_date',
                            'admin_approval_status',
                            'admin_approval_date',
                            'et_dispatch_date',
                            'dispatch_status',
                            'dispatch_units',
                            'actual_dispatch_date',
                            'dispatch_status_timestamp',
                            'updated_status_timestamp',
                            'receiving_status',
                            'received_unit',
                            'receiving_date',
                            'receiving_status_captured_date'
                        ];
}
