<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class ZonalManagerProductCategory extends Model
{
    public      $timestamps = false;
    protected   $table      = 'zonal_manager_product_categories';
    protected   $fillable   = [ 
                                'zonal_manager_id', 
                                'product_crop_category_id', 
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
