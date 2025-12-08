<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class RegionalManagerState extends Model
{
    public      $timestamps = false;
    protected   $table      = 'regional_manager_states';
    protected   $fillable   = [ 
                                'regional_manager_id', 
                                'state_id', 
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
