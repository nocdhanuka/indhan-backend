<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Reminder extends Model
{
    protected   $primaryKey = 'reminder_id';
    protected   $table      = 'reminder';
    protected   $fillable   = [
                                'crop_id',
                                'product_id',
                                'stage_id',
                                'spray',
                                'number_day_review',
                                'date',
                                'status', 
                                'created_at',
                                'updated_at'
                              ];
}
