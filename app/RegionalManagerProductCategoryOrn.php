<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class RegionalManagerProductCategoryOrn extends Model
{
    public      $timestamps = false;
    protected   $table      = 'regional_manager_product_categories_orn';
    protected   $fillable   = [ 
    							'regional_manager_product_category_id',
                                'regional_manager_id', 
                                'product_crop_category_id', 
                                'status',
                                'orn_status',
                                'orn_id',
                                'created_at',
                                'updated_at'
                            ];
}
