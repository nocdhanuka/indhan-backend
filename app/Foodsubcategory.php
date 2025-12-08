<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Foodsubcategory extends Model
{
    public      $timestamps = false;
    protected   $table      = 'foodsubcategory';
    protected   $fillable   = [
                                'fc_id',
                                'name',
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
