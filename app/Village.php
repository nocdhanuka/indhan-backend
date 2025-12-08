<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Village extends Model
{
    public      $timestamps = false;
    protected   $table      = 'villages';
    protected   $fillable   = [
                                'village_id',
                                'district_id',
                                'tehsil_id',
                                'village',
                                'latitude',
                                'longitude',
                                'radius',
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
