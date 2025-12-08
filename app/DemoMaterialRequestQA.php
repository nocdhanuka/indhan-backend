<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class DemoMaterialRequestQA extends Model
{
    protected $table    = 'demo_material_request_qa';
    public $primaryKey  = 'demo_material_request_qa_id';
    protected $fillable = [
                            'demo_material_request_id',
                            'content',
                            'user_id',
                            'user_type',
                            'user_ecn',
                            'user_name',
                            'created_at',
                            'updated_at'
                        ];
}
