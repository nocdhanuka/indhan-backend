<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InfluencerContact extends Model
{
    protected $table    = 'influencer_contact';
    public $primaryKey  = 'influencer_contact_id';
    protected $fillable = [
                            'influencer_id',
                            'state_id',
                            'district_id',
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
