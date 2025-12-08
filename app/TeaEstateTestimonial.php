<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class TeaEstateTestimonial extends Model
{
    protected $table    = 'estate_testimonial';
    public $primaryKey  = 'estate_testimonial_id';
    protected $fillable = [
                            'tea_estate_id',
                            'tea_estate_contact_id',
                            'contact_name',
                            'contact_designation',
                            'contact_number',
                            'wa_contact_number',
                            'can_be_shared',
                            'image_video',
                            'latitude',
                            'longitude',
                            'testimonial_name',
                            'description',
                            'date_time',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
