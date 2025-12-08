<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class RegionalManagerProductCategory extends Model
{
    public      $timestamps = false;
    protected   $table      = 'regional_manager_product_categories';
    protected   $fillable   = [ 
                                'regional_manager_id', 
                                'product_crop_category_id', 
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
