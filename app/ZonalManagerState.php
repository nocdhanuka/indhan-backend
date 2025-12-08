<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class ZonalManagerState extends Model
{
    public      $timestamps = false;
    protected   $table      = 'zonal_manager_states';
    protected   $fillable   = [ 
                                'zonal_manager_id', 
                                'state_id', 
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
