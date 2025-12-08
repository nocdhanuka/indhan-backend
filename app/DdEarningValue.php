<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class DdEarningValue extends Model
{
    public      $timestamps = false;
    protected   $table      = 'dd_earning_value';
    protected   $fillable   = [
                                'dd_earning_value_id',
                                'regional_manager_id',
                                'dd_field_earning_value',
                                'dd_village_earning_value',
                                'creation_time'
                            ];
}
