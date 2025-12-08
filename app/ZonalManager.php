<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
//use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
//use Illuminate\Database\Eloquent\SoftDeletes;

class ZonalManager  extends Model implements AuthenticatableContract {
 
    use Authenticatable; 
    /**
    * [$primaryKey description]
    * @var string
    */
    protected   $primaryKey = 'zonal_manager_id';
    public      $timestamps = false;
    protected   $table      = 'zonal_managers';
    protected   $fillable   = [
                                'ECN',
                                'dash',
                                'password',
                                'api_token',
                                'zonal_manager_name', 
                                'zonal_manager_mobile', 
                                'zonal_manager_pincode', 
                                'status',
                                'device_id',
                                'created_at',
                                'updated_at'
                            ];
}
