<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'settings';
    protected $fillable = [
        'image_title',
        'description',
        'image'
    ];
}