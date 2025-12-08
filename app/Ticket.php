<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Ticket extends Model
{
    public $timestamps  = false;
    protected $table    = 'tickets';
    protected $fillable = [
                            'ticket_no', 
                            'user_id', 
                            'user_type',
                            'plan_atp_id',
                            'demo_request_id',
                            'user_farmer',
                            'data_source_id', 
                            'state_id', 
                            'district_id', 
                            'tehsil_id', 
                            'territory_id', 
                            'village', 
                            'village_id',
                            'product_1_id', 
                            'crop_1_id', 
                            'product_2_id', 
                            'crop_2_id', 
                            'product_3_id', 
                            'crop_3_id', 
                            'expected_farmers', 
                            'expected_pda', 
                            'sp_name_1',
                            'sp_number_1',
                            'sp_name_2',
                            'sp_number_2',
                            'sp_name_3',
                            'sp_number_3',
                            'sp_name_4',
                            'sp_number_4',
                            'sp_name_5',
                            'sp_number_5',
                            'sp_name_6',
                            'sp_number_6',
                            'uploaded_file',
                            'status',
                            'farmer_count',
                            'is_qualified',
                            'validated_farmers',
                            'created_at',
                            'updated_at',
                            'closing_datetime',
                            'language_id',
                            'ticket_status',
                            'forwhom',
                            'assign_to_dd',
                            'assign_to_supervisor',
                            'uploadedBy',
                            'uploadedByType',
                            'dd_ticket_role'
                        ];
}
