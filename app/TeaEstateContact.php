<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class TeaEstateContact extends Model
{
    protected $table    = 'tea_estate_contact';
    public $primaryKey  = 'tea_estate_contact_id';
    protected $fillable = [
                            'tea_estate_id',
                            'level',
                            'designation',
                            'name',
                            'wa_number',
                            'mobile_number',
                            'email',
                            'status',
                            'created_at',
                            'updated_at'
                        ];
}
