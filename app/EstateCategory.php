<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class EstateCategory extends Model
{
    protected $table    = 'estate_category';
    public $primaryKey  = 'estate_category_id';
    protected $fillable = [
                            'turn_over',
                            'turn_over_point',
                            'land_holding',
                            'land_holding_point',
                            'estate_company_id',
                            'company_point',
                            'company_category',
                            'category_point',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}

