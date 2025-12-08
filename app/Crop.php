<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Crop extends Model
{
    public      $timestamps = false;
    protected   $table      = 'crops';
    protected   $fillable   = [
                                'crop', 
                                'status', 
                                'created_at',
                                'updated_at'
                            ];
}
