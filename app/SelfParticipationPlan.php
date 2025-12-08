<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SelfParticipationPlan extends Model
{
    public      $timestamps = false;
    protected   $table      = 'self_participation_plans';
    protected   $fillable   = [
                                'plan_atp_id',
                                'user_id',
                                'user_type',
                                'status',
                                'plan_date',
                                'created_at',
                                'reason',
                                'created_at',
                                'updated_at'
                            ];
}
