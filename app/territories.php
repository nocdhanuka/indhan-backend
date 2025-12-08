<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class territories extends Model
{
    public      $timestamps = false;
    protected   $table      = 'territories';
    protected   $fillable   = [
                                'district_id',
                                'territory',
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
