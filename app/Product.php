<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Product extends Model
{
    public      $timestamps = false;
    protected   $table      = 'products';
    protected   $fillable   = [
                                'product', 
                                'status', 
                                'created_at',
                                'updated_at'
                            ];
}
