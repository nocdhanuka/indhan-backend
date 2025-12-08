<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SugarmillTarget extends Model
{
    protected $table    = 'sugarmill_target';
    public $primaryKey  = 'target_id';
    protected $fillable = [
                            'target_month',
                            'target_year',
                            'user_name',
                            'user_ecn',
                            'user_type',
                            'office_type',
                            'category_a',
                            'category_a_target',
                            'category_b',
                            'category_b_target',
                            'category_c',
                            'category_c_target',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
