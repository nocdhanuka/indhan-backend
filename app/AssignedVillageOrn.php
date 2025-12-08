<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class AssignedVillageOrn extends Model
{
    public      $timestamps = false;
    protected   $table      = 'assigned_village_orn';
    protected   $fillable   = [ 
                                'assigned_village_orn_id',
                                'assigned_village_id',
                                'assigned_by_bdo_id',
                                'assigned_to_dd_id',
                                'village_id',
                                'orn_status',
                                'orn_id',
                                'created_at',
                                'updated_at'
                            ];
}
