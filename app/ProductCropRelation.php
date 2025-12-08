<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class ProductCropRelation extends Model
{
    public      $timestamps = false;
    protected   $table      = 'product_crop_relations';
    protected   $fillable   = [
                                'product_crop_category_id',
                                'product_id',
                                'crop_id',
                                'utilization_Per_demo',
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
