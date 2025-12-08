<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InfluencerSalesReport extends Model
{
    protected $table    = 'influencer_sales_report';
    public $primaryKey  = 'influencer_sales_report_id';
    protected $fillable = [
                            'influencer_id',
                            'month',
                            'year',
                            'sales_turnover',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
