<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SelfParticipation extends Model
{
    public      $timestamps = false;
    protected   $table      = 'self_participations';
    protected   $fillable   = [
                                'ticket_id',
                                'user_id',
                                'user_type',
                                'arival_time',
                                'description',
                                'uploaded_file',
                                'latitude',
                                'longitude',
                                'departure_time',
                                'departure_description',
                                'departure_uploaded_file',
                                'departure_latitude',
                                'departure_longitude',
                                'duration',
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
