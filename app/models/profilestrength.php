<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class profilestrength extends Model {
    
    protected $table_profile        = 'user_profile';
    protected $table_basic_detail   = 'user_basic_detail';
    public $timestamps              = false;
    
    function profilestrength($id) {
        $profile_data       = \DB::table('user_profile')
                                ->where('user_id', $id)
                                ->get();
        
        $basic_detail_data  = \DB::table('user_basic_detail')
                                ->where('user_id', $id)
                                ->get();
        
        $profile_values     = 0;
        
        if (!empty($profile_data)) {
            $profile_strength = $profile_data[0];
            // 3
            if ($profile_strength->img_status == "1") {
                $profile_values = $profile_values + 28;
            }
        }
        
        
        if (!empty($basic_detail_data)) {
            $basic_detail_strength = $basic_detail_data[0];
            // 4
            if (!empty($basic_detail_strength->adj_1)) {
                $profile_values = $profile_values + 4;
            }
            // 5
            if (!empty($basic_detail_strength->adj_2)) {
                $profile_values = $profile_values + 4;
            }
            // 6
            if (!empty($basic_detail_strength->adj_3)) {
                $profile_values = $profile_values + 4;
            }
            // 7
            if (!empty($basic_detail_strength->country_id)) {
                $profile_values = $profile_values + 4;
            }
            // 8
            if (!empty($basic_detail_strength->state_id)) {
                $profile_values = $profile_values + 4;
            }
            // 9
            if (!empty($basic_detail_strength->city_id)) {
                $profile_values = $profile_values + 4;
            }
            // 10
            if (!empty($basic_detail_strength->language_id)) {
                $profile_values = $profile_values + 4;
            }
            // 11
            if (!empty($basic_detail_strength->residency_status_id)) {
                $profile_values = $profile_values + 4;
            }
            // 12
            if (!empty($basic_detail_strength->ethnic_origin_id)) {
                $profile_values = $profile_values + 4;
            } 
                    // 13
                    if (!empty($basic_detail_strength->pray_id) || !empty($basic_detail_strength->beard)) {
                        $profile_values = $profile_values + 4;
                    } 
            // 14
            if (!empty($basic_detail_strength->marital_status)) {
                $profile_values = $profile_values + 4;
            }
            // 15
            if (!empty($basic_detail_strength->like_to_have_children)) {
                $profile_values = $profile_values + 4;
            } 
            // 16
            if (!empty($basic_detail_strength->body_type_id)) {
                $profile_values = $profile_values + 4;
            }
            // 17
            if (!empty($basic_detail_strength->hair_colour_id)) {
                $profile_values = $profile_values + 4;
            }
            // 18
            if (!empty($basic_detail_strength->height_id)) {
                $profile_values = $profile_values + 4;
            }
            // 19
            if (!empty($basic_detail_strength->occupation_id)) {
                $profile_values = $profile_values + 4;
            } 
            // 20
            if (!empty($basic_detail_strength->highest_qualification_id)) {
                $profile_values = $profile_values + 4;
            }
            // 21
            if (!empty($basic_detail_strength->islamic_institute_id)) {
                $profile_values = $profile_values + 4;
            }
            
        }
        $prfl_update = \DB::table('user_profile')
                            ->where('user_id', $id)
                            ->update(['profile_strength' => $profile_values]);
        return $profile_values;
    }

}
