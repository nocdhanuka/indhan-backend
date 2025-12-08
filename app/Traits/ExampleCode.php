<?php 
namespace App\Traits;

trait ExampleCode {
    
    public function userLanguage($language_id) {
        $get_register   = \ DB::table('language')
                                ->where(['id' => $language_id])
                                ->get();
        return $get_register;
    }
    
    public function userInstitute($islamic_institute_id) {
        $get_register   = \ DB::table('islamic_institute')
                            ->where(['institute_id' => $islamic_institute_id])
                            ->get();
        return $get_register;
    }
    
    public function userCountry($country_id) {
        $get_register   = \ DB::table('country')
                            ->where(['country_id' => $country_id])
                            ->get();
        return ($get_register?$get_register[0]->country:'');
    }
    
    public function userState($state_id) {
        $get_register   = \ DB::table('state')
                            ->where(['state_id' => $state_id])
                            ->get();
        return ($get_register?$get_register[0]->state:'');
    }
    
    public function userCity($district_id) {
        $get_register   = \ DB::table('district')
                            ->where(['district_id' => $district_id])
                            ->get();
        return ($get_register?$get_register[0]->district:'');
    }
    
    public function userProfileCreatedBy($profile_created_by_id) {
        $get_register   = \ DB::table('profile_created_by')
                            ->where(['profile_created_by_id' => $profile_created_by_id])
                            ->get();
        return ($get_register?$get_register[0]->relation:'');
    }
    
    public function userResidency($residency_status_id) {
        $get_register   = \ DB::table('residency_status')
                            ->where(['residency_status_id' => $residency_status_id])
                            ->get();
        return ($get_register?$get_register[0]->name:'');
    }
    
    public function userEthnicOrigin($ethnic_origin_id) {
        $get_register   = \ DB::table('ethnic_origin')
                            ->where(['ethnic_origin_id' => $ethnic_origin_id])
                            ->get();
        return ($get_register?$get_register[0]->ethnic_origin:'');
    }
    
    public function userInLaws($livingInLaws_id) {
        $get_register   = \ DB::table('living_with_in_laws')
                            ->where(['livingInLaws_id' => $livingInLaws_id])
                            ->get();
        return ($get_register?$get_register[0]->name:'');
    }
    
    public function userSectMuslim($sect_muslim_id) {
        $get_register   = \ DB::table('sect_muslim')
                            ->where(['sect_muslim_id' => $sect_muslim_id])
                            ->get();
        return ($get_register?$get_register[0]->sect_muslim:'');
    }
    
    public function userPray($pray_id) {
        $get_register   = \ DB::table('pray')
                            ->where(['pray_id' => $pray_id])
                            ->get();
        return ($get_register?$get_register[0]->pray:'');
    }
    
    public function userBodyType($body_type_id) {
        $get_register   = \ DB::table('body_type')
                            ->where(['body_type_id' => $body_type_id])
                            ->get();
        return ($get_register?$get_register[0]->body_type:'');
    }
    
    public function userHairColour($hair_colour_id) {
        $get_register   = \ DB::table('hair_colour')
                            ->where(['hair_colour_id' => $hair_colour_id])
                            ->get();
        return ($get_register?$get_register[0]->colour:'');
    }
    
    public function userObserveHijab($observe_hijab_id) {
        $get_register   = \ DB::table('observe_hijab')
                            ->where(['observe_hijab_id' => $observe_hijab_id])
                            ->get();
        return ($get_register?$get_register[0]->name:'');
    }
    
    public function userHeight($height_id) {
        $get_register   = \ DB::table('height')
                            ->where(['height_id' => $height_id])
                            ->get();
        return ($get_register?$get_register[0]->in_feet.'ft '.$get_register[0]->in_inch.'in - '.$get_register[0]->in_cm.'cm':'');
    }
    
    public function userOccupation($occupation_id) {
        $get_register   = \ DB::table('occupation')
                            ->where(['occupation_id' => $occupation_id])
                            ->get();
        return ($get_register?$get_register[0]->occupation:'');
    }
    
    public function userQualification($highest_qualification_id) {
        $get_register   = \ DB::table('highest_qualification')
                            ->where(['highest_qualification_id' => $highest_qualification_id])
                            ->get();
        return ($get_register?$get_register[0]->qualification:'');
    }
    
    public function date_time_ago($input_date,$date_format='M d, Y h:i A',$time_format='h:i A'){
        $formated_date = "";
        if (!empty($input_date)) {
            $current_date_time      = time();
            $orignal_date_time      = strtotime($input_date);
            $date_time_difference   = $current_date_time - $orignal_date_time;

            $full_days      = floor($date_time_difference/(60*60*24));
            $full_hours     = floor(($date_time_difference-($full_days*60*60*24))/(60*60));
            $full_minutes   = floor(($date_time_difference-($full_days*60*60*24)-($full_hours*60*60))/60);
            $full_seconds   = floor(($date_time_difference-($full_days*60*60*24)-($full_hours*60*60)-($full_minutes*60)));

            if($full_days > 2){
                $time = date($date_format,$orignal_date_time);
            } elseif($full_days == 2){
                $time = '2 days ago';
            } elseif(date("j",$current_date_time) > date("j",$orignal_date_time)){
                $time = 'Yesterday at '.date($time_format,$orignal_date_time);
            } elseif ($full_hours > 0) {
                $time = $full_hours . ' hours ago';
                if ($full_hours == 1) {
                    $time = $full_hours . ' hour ago';
                }
            } elseif ($full_minutes > 0) {
                $time = $full_minutes . ' mins ago';
                if ($full_minutes == 1) {
                    $time = $full_minutes . ' min ago';
                }
            } else {
                $time = 'Just now';
            }
            $formated_date = $time;                
        }
        return $formated_date;
    }
    
    
    
    
    
    
    
    
    
    
    public function userContinentCountry($country_id, $continent) {
        $get_register   = \ DB::table('continent_country')
                            ->leftjoin('country','continent_country.country', '=', 'country.country')
                            ->where(['country.country_id' => $country_id])
                            ->where(['parent_id' => $continent])
                            ->get();
        return ($get_register?$get_register[0]->country:'');
    }
}