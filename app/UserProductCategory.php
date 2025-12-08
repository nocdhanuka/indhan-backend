<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class UserProductCategory extends Model
{
    public      $timestamps = false;
    protected   $table      = 'user_product_categories';
    protected   $fillable   = [ 
                                'user_id', 
                                'product_crop_category_id', 
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
