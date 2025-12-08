<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Dcdistrictwise extends Model
{
    public $timestamps  = false;
    protected $table    = 'dcdistrictwise';
    protected $fillable = [
                            'ecnnumber',
                            'ecnname',
                            'state_id',
                            'district_id',
                            'class_id',
                            'formulation_id',
                            'volume',
                            'value',
                            'status',
                            'remarks'
                        ];
}
