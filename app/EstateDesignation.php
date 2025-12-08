<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class EstateDesignation extends Model
{
    protected $table    = 'estate_designation';
    public $primaryKey  = 'estate_designation_id ';
    protected $fillable = [
                            'turn_over',
                            'level',
                            'designation',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}

