<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class MarketingSupervisor extends Model implements AuthenticatableContract
{   
    use Authenticatable; 
    /**
    * [$primaryKey description]
    * @var string
    */
    protected   $primaryKey = 'marketing_supervisor_id';
    public      $timestamps = false;
    protected   $table      = 'marketing_supervisor';
    protected   $fillable   = [ 
                                'ECN', 
                                'dash', 
                                'password',
                                'api_token',
                                'marketing_supervisor_hq_code',
                                'marketing_supervisor_name',
                                'marketing_supervisor_mobile',
                                'status',
                                'device_id',
                                'created_at',
                                'updated_at'
                            ];
}
