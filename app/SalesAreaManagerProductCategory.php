<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SalesAreaManagerProductCategory extends Model
{
    public      $timestamps = false;
    protected   $table      = 'sales_area_manager_product_categories';
    protected   $fillable   = [ 
                                'sales_area_manager_id', 
                                'product_crop_category_id', 
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
