<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class DdProductCategory extends Model
{
    public      $timestamps = false;
    protected   $table      = 'dd_product_categories';
    protected   $fillable   = [ 
                                'dd_id', 
                                'product_crop_category_id', 
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
