<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InfluencerDesignation extends Model
{
    protected $table    = 'influencer_designation';
    public $primaryKey  = 'influencer_designation_id ';
    protected $fillable = [
                            'turn_over',
                            'level',
                            'designation',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}

