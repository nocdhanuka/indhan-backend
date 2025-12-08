<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
//use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
//use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralUser  extends Model implements AuthenticatableContract {
 
    use Authenticatable; 
    /**
    * [$primaryKey description]
    * @var string
    */
    protected   $primaryKey = 'general_id';
    public      $timestamps = false;
    protected   $table      = 'general';
    protected   $fillable   = [
                                'ECN',
                                'dash',
                                'password',
                                'api_token',
                                'general_user_name', 
                                'general_user_mobile',
                                'designation',
                                'state', 
                                'status',
                                'device_id',
                                'created_at',
                                'updated_at'
                            ];
}
