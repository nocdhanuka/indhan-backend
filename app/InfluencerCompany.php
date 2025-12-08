<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InfluencerCompany extends Model
{
    protected $table    = 'influencer_company';
    public $primaryKey  = 'influencer_company_id';
    protected $fillable = [
                            'company_type',
                            'points',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}

