<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class ReportGuideline extends Model
{
    public      $timestamps = false;
    protected   $table      = 'report_guideline';
    protected   $fillable   = [
                                'report_type_id',
                                'report_name',
                                'year',
                                'month',
                                'zonal_manager_id',
                                'regional_manager_id',
                                'sales_regional_manager_id',
                                'area_manager_id',
                                'sales_area_manager_id',
                                'user_id',
                                'uploaded_file',
                                'status',
                                'created_at',
                                'updated_at'
                            ];
}
