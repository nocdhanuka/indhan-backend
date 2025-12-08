<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
//use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
//use Illuminate\Database\Eloquent\SoftDeletes;

class SalesAreaManager  extends Model implements AuthenticatableContract {
 
    use Authenticatable;   
    /**
    * [$primaryKey description]
    * @var string
    */
    protected   $primaryKey = 'sales_area_manager_id';
    public      $timestamps = false;
    protected   $table      = 'sales_area_managers';
    protected   $fillable   = [
                                'ECN',
                                'dash',
                                'password',
                                'api_token',
                                'zonal_manager_id', 
                                'regional_manager_id', 
                                'sales_regional_manager_id', 
                                'sales_area_manager_hq_code', 
                                'sales_area_manager_name', 
                                'sales_area_manager_mobile', 
                                'sales_area_manager_pincode', 
                                'status', 
                                'device_id',
                                'created_at',
                                'updated_at'
                            ];
}
