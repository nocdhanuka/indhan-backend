<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class EstateCompany extends Model
{
    protected $table    = 'estate_company';
    public $primaryKey  = 'estate_company_id';
    protected $fillable = [
                            'company_type',
                            'points',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}

