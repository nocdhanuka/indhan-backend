<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Testimonial extends Model
{
    public      $timestamps = false;
    protected   $table      = 'testimonials';
    protected   $fillable   = [ 
                                'self_participation_id',
                                'user_id',
                                'user_type',
                                'testimonial',
                                'mobile',
                                'description',
                                'uploaded_file',
                                'product_id',
                                'crop_id',
                                'status',
                                'created_at',
                                'updated_at',
                                'unique_testimonial_id',
                                'farmer',
                                'product_ids',
                                'crop_ids'
                            ];
}
