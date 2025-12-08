<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Compcompany extends Model
{
    public      $timestamps = false;
    protected   $table      = 'compcompany';
    protected   $fillable   = [
                                'name', 
                                'status', 
                                'created_at',
                                'updated_at'
                            ];
}
