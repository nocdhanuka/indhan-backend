<?php

//namespace App;
//
//use Illuminate\Foundation\Auth\User as Authenticatable;
//
//class User extends Authenticatable
//{
//    /**
//     * The attributes that are mass assignable.
//     *
//     * @var array
//     */
//    protected $fillable = [
//        'name', 'email', 'password',
//    ];
//
//    /**
//     * The attributes that should be hidden for arrays.
//     *
//     * @var array
//     */
//    protected $hidden = [
//        'password', 'remember_token',
//    ];
//}


namespace App;
 
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
//use Illuminate\Database\Eloquent\SoftDeletes;
 
class User extends Model implements AuthenticatableContract {
 
    use Authenticatable;    
    /**
    * [$primaryKey description]
    * @var string
    */
    protected $primaryKey   = 'id';
    protected $fillable     = [
                                'ECN',
                                'password',
                                'dash',        
                                'api_token',
                                'zonal_manager_id',
                                'regional_manager_id',
                                'sales_regional_manager_id',
                                'area_manager_id',
                                'sales_area_manager_id',
                                'state_id',
                                'district_id',
                                'language_id',
                                'department',
                                'designation', 
                                'counterpart_fl',            
                                'bdo_code',
                                'name',      
                                'bdo_mobile_no',                  
                                'bdo_hq',
                                'states',
                                'districts',
                                'territory',
                                'market',
                                'status',
                                'device_id',
                                'created_at',
                                'updated_at'
                            ];
    
    protected $table = 'users';
    
}
