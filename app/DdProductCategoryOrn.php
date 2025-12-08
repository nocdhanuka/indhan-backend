<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class DdProductCategoryOrn extends Model
{
    public      $timestamps = false;
    protected   $table      = 'dd_product_categories_orn';
    protected   $fillable   = [ 
    							'dd_product_category_id',
                                'dd_id', 
                                'product_crop_category_id', 
                                'status',
                                'orn_status',
                                'orn_id',
                                'created_at',
                                'updated_at'
                            ];
}
