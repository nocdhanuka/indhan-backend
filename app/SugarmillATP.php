<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SugarmillATP extends Model
{
    protected $table    = 'sugarmill_atp';
    public $primaryKey  = 'sugarmill_atp_id';
    protected $fillable = [
                            'date_time',
                            'fl_name',
                            'fl_ecn',
                            'fl_type',
                            'manager_name',
                            'manager_ecn',
                            'manager_type',
                            'sugarmill_category',
                            'sugarmill_id',
                            'ticket_number',
                            'is_manager',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
