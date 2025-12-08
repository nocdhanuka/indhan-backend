<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SugarmillSalesReport extends Model
{
    protected $table    = 'sugarmill_sales_report';
    public $primaryKey  = 'sugarmill_sales_report_id';
    protected $fillable = [
                            'sugarmill_id',
                            'month',
                            'year',
                            'sales_turnover',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
