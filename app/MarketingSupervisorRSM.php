<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class MarketingSupervisorRSM extends Model
{	
	protected   $primaryKey = 'marketing_supervisor_rsm_id';
    public      $timestamps = false;
    protected   $table      = 'marketing_supervisor_rsm';
    protected   $fillable   = [ 
                                'marketing_supervisor_id', 
                                'sales_regional_manager_id', 
                                'status',
                                'creation_at'
                            ];
}
