<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InfluencerCategory extends Model
{
    protected $table    = 'influencer_category';
    public $primaryKey  = 'influencer_category_id';
    protected $fillable = [
                            'turn_over',
                            'turn_over_point',
                            'land_holding',
                            'land_holding_point',
                            'influencer_company_id',
                            'company_point',
                            'company_category',
                            'category_point',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}

