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
 
class DhanukaDoctorOrn extends Model implements AuthenticatableContract {
 
    use Authenticatable;    
    /**
    * [$primaryKey description]
    * @var string
    */
    protected $primaryKey   = 'dhanuka_doctor_orn_id';
    protected $fillable     = [ 
                                'dd_id',
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
                                'dd_code',
                                'name',      
                                'dd_mobile_no', 
                                'dd_alt_mobile_no',                 
                                'dd_hq',
                                'bdo_id',
                                'dd_email',
                                'dd_address',
                                'tehsil_id',

                                'status',
                                'orn_status',
                                'device_id',

                                'created_at',
                                'updated_at'
                            ];
    
    protected $table = 'dhanuka_doctor_orn';
    
}
