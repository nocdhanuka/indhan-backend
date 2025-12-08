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
}