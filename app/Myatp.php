<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Myatp extends Model
{
    public      $timestamps = false;
    protected   $table      = 'my_atp';
    protected   $fillable   = [
                                'my_atp_id',
                                'user_id',
                                'user_type',
                                'plan_atp_id',
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
