<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SugarmillContact extends Model
{
    protected $table    = 'sugarmill_contact';
    public $primaryKey  = 'sugarmill_contact_id';
    protected $fillable = [
                            'sugarmill_id',
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
