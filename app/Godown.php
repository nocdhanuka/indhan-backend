<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Godown extends Model
{
    protected $table    = 'godown';
    protected $fillable = [
                            'state_id',
                            'district_id',
                            'godown',
                            'contact_person',
                            'contact_number',
                            'status',
                            'created_at',
                            'updated_at',
                        ];
}
