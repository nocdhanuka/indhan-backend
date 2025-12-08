<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InstitutionalTestimonial extends Model
{
    protected $table    = 'institutional_testimonial';
    public $primaryKey  = 'institutional_testimonial_id';
    protected $fillable = [
                            'institutionalType',
                            'institutional_name',
                            'user_ecn',
                            'office_type',
                            'type',
                            'institutional_id',
                            'institutional_contact_id',
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
                            'language_id',
                            'language_name',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
