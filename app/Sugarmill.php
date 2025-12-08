<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Sugarmill extends Model
{
    protected $table    = 'sugarmill';
    public $primaryKey  = 'sugarmill_id';
    protected $fillable = [
                            'office_type',
                            'sugarmill_type',
                            'parent_sugarmill_id',
                            'name',
                            'turn_over',
                            'land_area',
                            'sugarmill_company_id',
                            'sugarmill_category_id',
                            'sugarmill_category_type',
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
