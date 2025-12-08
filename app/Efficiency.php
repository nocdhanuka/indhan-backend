<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Efficiency extends Model
{
    protected   $primaryKey = 'efficiency_result_id';
    public      $timestamps = false;
    protected   $table      = 'efficiency_results';
    protected   $fillable   = [
                                'user_id',
                                'user_type',
                                'month',
                                'year',
                                
                                'CONST_percent_deduction',
                                'CONST_activity_per_month',
                                'CONST_average_farmer_registration',
                
                                'total_activity',
                                'total_registered_farmer',
                                'total_contacted_farmer',
                                'total_validated_farmer',
                                'total_nada_farmer',
                                'total_pnc_farmer',
                                'total_bn_farmer',
        
                                'nada_percent',
                                'pnc_percent',
                                'bn_percent',
                                
                                'farmer_valid_percent',
                                'total_ex_valid_farmers',
                                'average_activity_efficiency',
                                'average_validate_farmer',                                
                                'average_farmer_efficiency',
                                
                                'nada_deduction',
                                'pnc_deduction',
                                'bn_deduction',
                                'efficiency',
                                'created_at',
                                'updated_at'
                            ];
}
