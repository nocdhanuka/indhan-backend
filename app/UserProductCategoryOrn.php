<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class UserProductCategoryOrn extends Model
{
    public      $timestamps = false;
    protected   $table      = 'user_product_categories_orn';
    protected   $fillable   = [ 
    							'user_product_category_id',
                                'user_id', 
                                'product_crop_category_id', 
                                'status',
                                'orn_status',
                                'orn_id',
                                'created_at',
                                'updated_at'
                            ];
}
