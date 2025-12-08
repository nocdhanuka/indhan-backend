<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Formulation extends Model
{
    public      $timestamps = false;
    protected   $table      = 'formulation';
    protected   $fillable   = [
                                'class_id',
                                'name',
                                'brand',
                                'nrv',
                                'volume',
                                'created_at',
                                'updated_at'
                            ];
}
