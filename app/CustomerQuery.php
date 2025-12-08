<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class CustomerQuery extends Model
{
    protected $table    = 'customer_queries';
    protected $fillable = [
                            'customer_query_id',
                            'token_number',
                            'mobile',
                            'farmername',
                            'state',
                            'district',
                            'tehsil',
                            'village',
                            'product',
                            'crop',
                            'disease',
                            'pincode',
                            'type_of_call',
                            'is_follow_up',
                            'bdo_id',
                            'assigned_by',
                            'remark',
                            'query_datetime',
                            'status',
                            'closed_by_admin',
                            'created_at',
                            'updated_at'
                        ];
}
