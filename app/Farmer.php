<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Farmer extends Model
{
    protected $table    = 'farmers';
    protected $fillable = [
                            'ticket_id', 
                            'user_id',
                            'user_type',
                            'mobile', 
                            'datetime', 
                            'farmer_type',
                            'registration_type',
                            'ticket_status',
                            'validation_status',
                            'created_at',
                            'updated_at',
                        ];
}
