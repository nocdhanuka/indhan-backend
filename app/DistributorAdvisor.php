<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class DistributorAdvisor extends Model
{
    protected $table    = 'distributor_advisor';
    public $primaryKey  = 'distributor_advisor_id';
    protected $fillable = [
                            'user_id',
                            'user_type',
                            'user_name',
                            'user_ecn',
                            'state_id',
                            'district_id',
                            'territory_id',
                            'market_id',
                            'distributor_name',
                            'distributor_type',
                            'distributor_firm_name',
                            'mobile',
                            'mobile2',
                            'address',
                            'pincode',
                            'turnover',
                            'crope',
                            'brands',
                            'is_distributor',
                            'is_visited',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
