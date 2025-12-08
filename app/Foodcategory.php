<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Foodcategory extends Model
{
    public      $timestamps = false;
    protected   $table      = 'foodcategory';
    protected   $fillable   = [
                                'name',
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
