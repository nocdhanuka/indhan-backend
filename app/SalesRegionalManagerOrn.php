<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
//use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
//use Illuminate\Database\Eloquent\SoftDeletes;

class SalesRegionalManagerOrn  extends Model implements AuthenticatableContract {
 
    use Authenticatable; 
    /**
    * [$primaryKey description]
    * @var string
    */
    protected   $primaryKey = 'sales_regional_managers_orn_id';
    public      $timestamps = false;
    protected   $table      = 'sales_regional_managers_orn';
    protected   $fillable   = [ 
                                'sales_regional_manager_id',
                                'ECN',
                                'dash',
                                'password',
                                'api_token',
                                'zonal_manager_id', 
                                'sales_regional_manager_hq_code', 
                                'sales_regional_manager_name', 
                                'sales_regional_manager_mobile', 
                                'sales_regional_manager_pincode', 
                                'status', 
                                'orn_status',
                                'device_id',
                                'created_at',
                                'updated_at'
                            ];
}
