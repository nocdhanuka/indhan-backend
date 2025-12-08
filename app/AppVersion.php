<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class AppVersion extends Model
{
    public      $timestamps = false;
    protected   $table      = 'app_versions';
    protected   $fillable   = [
                                'user_id',
                                'user_type',
                                'app_version',
                                'created_at',
                                'updated_at'
                            ];
}
