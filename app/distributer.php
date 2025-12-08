<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class distributer extends Model
{
    public      $timestamps = false;
    protected   $table      = 'distributers';
    protected   $fillable   = [
                                'state_id',
                                'district_id',       
                                'territory_id', 
                                'market', 
                                'distributer_name',  
                                'mobilenumber',
                                'mobile2',
                                'Adress',   
                                'pincode', 
                                'turnover',
                                'addedbyecn',
                                'ecnname',
                                'distributerforname',
                                'distributer_id',
                                'crope',
                                'brands',
                                'status',
                                'connected_retailers',
                                'ratings',
                                'pesticide_turnover',
                                'counter_percentage_pesticides',
                                'created_at',
                                'updated_at'
                            ];
}
