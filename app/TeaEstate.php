<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class TeaEstate extends Model
{
    protected $table    = 'tea_estate';
    public $primaryKey  = 'tea_estate_id';
    protected $fillable = [
                            'office_type',
                            'tea_estate_type',
                            'parent_tea_estate_id',
                            'name',
                            'turn_over',
                            'land_area',
                            'estate_company_id',
                            'estate_category_id',
                            'estate_category_type',
                            'state_id',
                            'district_id',
                            'address',
                            'latitude',
                            'longitude',
                            'image',
                            'fl_name',
                            'fl_ecn',
                            'fl_type',
                            'addedby_id',
                            'addedby_name',
                            'addedby_ecn',
                            'addedby_type',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
