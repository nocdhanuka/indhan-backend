<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class CircularTestimonial extends Model
{
    protected $table    = 'circular_testimonial';
    public $primaryKey  = 'circular_testimonial_id';
    protected $fillable = [
                            'circular_id',
                            'testimonial_id',
                            'description',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
