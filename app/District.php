<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class District extends Model
{
    public      $timestamps = false;
    protected   $table      = 'districts';
    protected   $fillable   = [
                                'state_id',
                                'district',
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
