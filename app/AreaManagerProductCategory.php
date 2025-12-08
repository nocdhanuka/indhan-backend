<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class AreaManagerProductCategory extends Model
{
    public      $timestamps = false;
    protected   $table      = 'area_manager_product_categories';
    protected   $fillable   = [ 
                                'area_manager_id', 
                                'product_crop_category_id', 
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
