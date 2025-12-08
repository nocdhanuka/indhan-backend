<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class CampaignModel extends Model
{
    public      $timestamps = false;
    protected   $table      = 'campaign';
    protected   $fillable   = [
                                'campaign_name',
                                'camp_mandatory',
                                'camp_push_notify',
                                'date_of_expire',
                                'campaign_type_id'
                            ];
}
