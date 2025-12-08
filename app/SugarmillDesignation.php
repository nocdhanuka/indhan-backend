<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SugarmillDesignation extends Model
{
    protected $table    = 'sugarmill_designation';
    public $primaryKey  = 'sugarmill_designation_id ';
    protected $fillable = [
                            'turn_over',
                            'level',
                            'designation',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}

