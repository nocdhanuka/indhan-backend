<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class MarkVisit extends Model
{


    protected   $primaryKey = 'mark_visit_id';
    protected $dates = [
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
        'updated_date',
        'deleted_at',
    ];
}
