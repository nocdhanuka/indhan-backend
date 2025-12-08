<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class AssignedVillage extends Model
{
    public      $timestamps = false;
    protected   $table      = 'assigned_village';
    protected   $fillable   = [
                                'assigned_village_id',
                                'assigned_by_bdo_id',
                                'assigned_to_dd_id',
                                'village_id',
                                'created_at',
                                'updated_at'
                            ];
}
