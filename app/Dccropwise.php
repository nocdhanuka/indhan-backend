<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Dccropwise extends Model
{
    public $timestamps  = false;
    protected $table    = 'dccropwise';
    protected $fillable = [
                            'ecnnumber',
                            'ecnname',
                            'state_id',
                            'district_id',
                            'class_id',
                            'formulation_id',
                            'volume',
                            'value',
                            'maize',
                            'rice',
                            'wheat',
                            'cotton',
                            'grapes',
                            'apple',
                            'mango',
                            'pomegranate',
                            'citrus',
                            'other_fruits',
                            'groundnut',
                            'soybean',
                            'other_oilseeds',
                            'sugarcane',
                            'tea',
                            'bengal_gram',
                            'black_gram',
                            'other_pulses',
                            'cumin',
                            'brinjal',
                            'chillies',
                            'cole_crops',
                            'cucurbits_melons',
                            'onion_garlic',
                            'potato',
                            'tomato',
                            'other_veg',
                            'others',
                            'total',
                            'percentage',
                            'status'
                        ];
}
