<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class markets extends Model
{
    public      $timestamps = false;
    protected   $table      = 'markets';
    protected   $fillable   = [
                                'village_id',
                                'district_id',
                                'territory_id',
                                'village',
                                'status',
                                'market',
                                'added_by',
                                'created_at',
                                'updated_at'
                            ];
}
