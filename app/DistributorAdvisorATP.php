<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class DistributorAdvisorATP extends Model
{
    protected $table    = 'distributor_advisor_atp';
    public $primaryKey  = 'distributor_advisor_atp_id';
    protected $fillable = [
                            'distributor_advisor_id',
                            'user_id',
                            'user_type',
                            'user_name',
                            'user_ecn',
                            'arrival_image',
                            'arrival_time',
                            'arrival_comment',
                            'arrival_latitude',
                            'arrival_longitude',
                            'departure_time',
                            'departure_image',
                            'departure_comment',
                            'departure_latitude',
                            'departure_longitude',
                            'is_arrival_departure',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
