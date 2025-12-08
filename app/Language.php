<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Language extends Model
{
    public      $timestamps = false;
    protected   $table      = 'languages';
    protected   $fillable   = [
                                'language', 
                                'iso_639-1',
                                'status', 
                                'created_at',
                                'updated_at'
                            ];
}
