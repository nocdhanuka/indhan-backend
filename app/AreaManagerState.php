<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class AreaManagerState extends Model
{
    public      $timestamps = false;
    protected   $table      = 'area_manager_states';
    protected   $fillable   = [ 
                                'area_manager_id', 
                                'state_id', 
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
