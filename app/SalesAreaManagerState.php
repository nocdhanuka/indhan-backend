<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SalesAreaManagerState extends Model
{
    public      $timestamps = false;
    protected   $table      = 'sales_area_manager_states';
    protected   $fillable   = [ 
                                'sales_area_manager_id', 
                                'state_id', 
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
