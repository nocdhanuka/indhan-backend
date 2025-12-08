<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class AppMenuPermission extends Model
{
    public      $timestamps = false;
    protected   $table      = 'app_menu_permissions';
    protected   $fillable   = [
                                'department',
                                'app_menu_id',
                                'user_id',
                                'user_type',
                                'status', 
                                'created_at',
                                'updated_at'
                            ];
}
