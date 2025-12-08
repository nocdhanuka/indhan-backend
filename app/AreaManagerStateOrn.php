<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class AreaManagerStateOrn extends Model
{
    public      $timestamps = false;
    protected   $table      = 'area_manager_states_orn';
    protected   $fillable   = [ 
                                'area_manager_state_id',
                                'area_manager_id',
                                'state_id', 
                                'status',
                                'orn_status',
                                'orn_id',
                                'created_at',
                                'updated_at'
                            ];
}
