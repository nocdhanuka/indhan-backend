<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class MarketingSupervisorProductCategory extends Model
{	
	protected   $primaryKey = 'marketing_supervisor_product_category_id';
    public      $timestamps = false;
    protected   $table      = 'marketing_supervisor_product_categories';
    protected   $fillable   = [ 
                                'marketing_supervisor_id', 
                                'product_crop_category_id', 
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
