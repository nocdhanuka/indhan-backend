<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SalesRegionalManagerStateOrn extends Model
{
    public      $timestamps = false;
    protected   $table      = 'sales_regional_manager_states_orn';
    protected   $fillable   = [ 
    							'sales_regional_manager_state_id',
                                'sales_regional_manager_id', 
                                'state_id', 
                                'status',
                                'orn_status',
                                'orn_id',
                                'created_at',
                                'updated_at'
                            ];
}
