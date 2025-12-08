<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InfluencerATPArivalDeparture extends Model
{
    protected $table    = 'influencer_atp_arrival_departure';
    public $primaryKey  = 'influencer_atp_arrival_departure_id';
    protected $fillable = [
                            'date_time',
                            'influencer_atp_id',
                            'user_id',
                            'user_type',
                            'user_ecn',
                            'user_name',
                            'arrival_image',
                            'arrival_time',
                            'arrival_comment',
                            'arrival_latitude',
                            'arrival_longitude',
                            'departure_image',
                            'departure_time',
                            'departure_comment',
                            'departure_latitude',
                            'departure_longitude',
                            'duration',
                            'distance',
                            'is_validate',
                            'is_manager',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
