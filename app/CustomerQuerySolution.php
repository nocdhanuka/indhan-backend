<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class CustomerQuerySolution extends Model
{
    protected $table    = 'customer_query_solutions';
    protected $fillable = [
                            'customer_query_solution_id',
                            'customer_query_id',
                            'user_id',
                            'status',
                            'product',
                            'product_quantity',
                            'remark',
                            'amount',
                            'created_at',
                            'updated_at'
                        ];
}
