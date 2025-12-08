<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class ProductCropCategory extends Model
{
    public      $timestamps = false;
    protected   $table      = 'product_crop_categories';
    protected   $fillable   = [
                                'category_name', 
                                'status', 
                                'created_at',
                                'updated_at'
                            ];
}
