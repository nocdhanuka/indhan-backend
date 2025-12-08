<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class TeaEstateSalesReport extends Model
{
    protected $table    = 'estate_sales_report';
    public $primaryKey  = 'estate_sales_report_id';
    protected $fillable = [
                            'tea_estate_id',
                            'month',
                            'year',
                            'sales_turnover',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
