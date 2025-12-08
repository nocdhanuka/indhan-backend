<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Influencer extends Model
{
    protected $table    = 'influencer';
    public $primaryKey  = 'influencer_id';
    protected $fillable = [
                            'office_type',
                            'influencer_type',
                            'parent_influencer_id',
                            'name',
                            'turn_over',
                            'land_area',
                            'influencer_company_id',
                            'influencer_category_id',
                            'influencer_category_type',
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
