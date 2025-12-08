<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class ActiveLog extends Model
{
    public      $timestamps = false;
    protected   $table      = 'active_logs';
    protected   $primaryKey = 'active_log_id';
    protected   $fillable   = [
                                'user_id',
                                'user_type',
                                'last_access_time',
                                'logout_time'
                            ];
}
