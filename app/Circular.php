<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Circular extends Model
{
    protected $table    = 'circular';
    public $primaryKey  = 'circular_id';
    protected $fillable = [
                            'user_id',
                            'user_type',
                            'user_ecn',
                            'user_name',
                            'language_id',
                            'language_name',
                            'circular_name',
                            'circular_path',
                            'published_date',
                            'institutionalType',
                            'contact_ids',
                            'whatsapp_message',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
