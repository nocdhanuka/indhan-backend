<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class TeaEstateATP extends Model
{
    protected $table    = 'estate_atp';
    public $primaryKey  = 'estate_atp_id';
    protected $fillable = [
                            'date_time',
                            'fl_name',
                            'fl_ecn',
                            'fl_type',
                            'manager_name',
                            'manager_ecn',
                            'manager_type',
                            'estate_category',
                            'tea_estate_id',
                            'ticket_number',
                            'is_manager',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
