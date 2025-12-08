<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SalesAreaManagerProductCategoryOrn extends Model
{
    public      $timestamps = false;
    protected   $table      = 'sales_area_manager_product_categories_orn';
    protected   $fillable   = [ 
    							'sales_area_manager_product_category_id',
                                'sales_area_manager_id', 
                                'product_crop_category_id', 
                                'status',
                                'orn_status',
                                'orn_id',
                                'created_at',
                                'updated_at'
                            ];
}
