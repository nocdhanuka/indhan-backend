<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InfluencerATP extends Model
{
    protected $table    = 'influencer_atp';
    public $primaryKey  = 'influencer_atp_id';
    protected $fillable = [
                            'date_time',
                            'fl_name',
                            'fl_ecn',
                            'fl_type',
                            'manager_name',
                            'manager_ecn',
                            'manager_type',
                            'influencer_category',
                            'influencer_id',
                            'ticket_number',
                            'is_manager',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
