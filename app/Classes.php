<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Classes extends Model
{
    public      $timestamps = false;
    protected   $table      = 'class';
    protected   $fillable   = [
                                'name', 
                                'status', 
                                'created_at',
                                'updated_at'
                            ];
}
