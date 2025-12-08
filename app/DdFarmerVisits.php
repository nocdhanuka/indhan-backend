<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class DdFarmerVisits extends Model
{
    public      $timestamps = false;
    protected   $table      = 'dd_farmer_visits';
    protected   $fillable   = [
                                'dd_farmer_visits_id',
                                'dd_id',
                                'expected_farmers',
                                'registered_farmers',
                                'expected_value',
                                'amount',
                                'creation_time'
                            ];
}