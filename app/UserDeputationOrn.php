<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class UserDeputationOrn extends Model
{
    public $timestamps  = false;
    protected $table    = 'user_deputations_orn';
    protected $fillable = [
                            'user_deputation_id',
                            'user_id', 
                            'zonal_manager_id', 
                            'regional_manager_id', 
                            'sales_regional_manager_id', 
                            'area_manager_id', 
                            'sales_area_manager_id',
                            'deputation_from_date',
                            'deputation_to_date', 
                            'status', 
                            'orn_status',
                            'orn_id',
                            'created_at',
                            'updated_at'
                        ];
}
