<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class PlanActivities extends Model
{
    protected   $primaryKey = 'plan_activity_id';
    public      $timestamps = false;
    protected   $table      = 'plan_activities';
    protected   $fillable   = [
                                'user_id',
                                'month',
                                'year',
                                'product_id',
                                'crop_id', 
                                'days', 
                                'dd', 
                                'record_id',
                                'status',
                                'created_by_user_id',
                                'user_type',
                                'is_deleted',
                                'created_at',
                                'updated_at'
                            ];
}
