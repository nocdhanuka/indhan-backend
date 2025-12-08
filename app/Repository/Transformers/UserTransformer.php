<?php
namespace App\Repository\Transformers;

class UserTransformer extends Transformer{
    public function transform($user, $userType='', $user_states='', $activity_permission=0){
        switch ($userType) {
            case "marketing_supervisor" :   $username = $user->marketing_supervisor_name; $user_id = $user->marketing_supervisor_id;
                                        break;
            case "ZONAL"            :   $username = $user->zonal_manager_name; $user_id = $user->zonal_manager_id;
                                        break;
            case "REGIONAL"         :   $username = $user->regional_manager_name; $user_id = $user->regional_manager_id;
                                        break;
            case "AREA"             :   $username = $user->area_manager_name; $user_id = $user->area_manager_id;
                                        break;
            case "SALES_REGIONAL"   :   $username = $user->sales_regional_manager_name; $user_id = $user->sales_regional_manager_id;
                                        break;
            case "SALES_AREA"       :   $username = $user->sales_area_manager_name; $user_id = $user->sales_area_manager_id;
                                        break;
            case "BDO"              :   $username = $user->name; $user_id = $user->id;
                                        break;
            case "dhanuka_doctor"   :   $username = $user->name; $user_id = $user->dd_id;
                                        break;
	        case "general"          :   $username = $user->general_user_name; $user_id = $user->general_id;
                                        break;

        }
        return [
            'user_id'     => $user_id,
            'user_type'   => $userType,
            'designation' => (!empty($user->designation))?$user->designation:"",
            'user_state'  => $user_states,
            'ECN' 	      => $user->ECN,
            'Username'    => $username,
            'api_token'   => $user->api_token,
            'activity_permission' => $activity_permission,
        ];
    }
}





