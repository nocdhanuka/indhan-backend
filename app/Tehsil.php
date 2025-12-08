<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Tehsil extends Model
{
    public      $timestamps = false;
    protected   $table      = 'tehsils';
    protected   $fillable   = [
                                'state_id',
                                'district_id',
                                'tehsil',
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
