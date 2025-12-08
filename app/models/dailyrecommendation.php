<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class dailyrecommendation extends Model
{
      protected $table = 'user_reg';
     public $timestamps = false;
   
  function dailyrecommendation()
	{
     $from = Carbon::now()->startOfWeek()->toDateString(); 
     $till = Carbon::now()->toDateString();
	
      $fromDate=date("Y-m-d", strtotime($from));
     $tillDate= date("Y-m-d", strtotime($till));
      
	 $gender=\Session::get('gender');
	   $user_gender=$gender;
		if($user_gender=='male')
		{
			$search_gender="female";
		}
		else
		{
			$search_gender="male";
		}
	
		
			$profile=\ DB::table('user_reg')
			          ->join('user_profile','user_profile.user_id','=','user_reg.id')
			           ->leftJoin('education','education.education_id','=','user_profile.education')
			          ->whereBetween('date',[$fromDate, $tillDate] )
					  ->where('gender',$search_gender)
					  ->where('email_key','=',NULL)
					  ->where('deactivate_status','=','0')
					   ->where('profile_strength','>=','30')
					   ->limit(5)
					  ->get();
	  
		//var_dump($profile);exit;
	   return $profile;
	 }
    
    
    function HighlightedProfile()
	{
	$id= \Session::get('id');	
     
        if($id)        
     {
         
       $gender= \Session::get('gender');
       
         if($gender=='male')
         {
            $high_profile=\DB::table('user_payment_details')
		                  ->join('user_profile','user_profile.user_id','=','user_payment_details.uid')
		                  ->leftJoin('education', 'education.education_id', '=', 'user_profile.education')
						   ->leftJoin('user_reg', 'user_reg.id', '=', 'user_profile.user_id')
					       ->where('user_payment_status','=','1')
                           ->where('gender','=','female')
                           ->get();
					 // var_dump($high_profile);exit();
					 
				foreach($high_profile as $package_userid)
				  {
						  $pk_userid =   $package_userid->uid;
						
					
				  } 
               \Session::put('package_user_id',$pk_userid);
              return $high_profile;
             
            
         }
         elseif($gender=='female')
         {
               $high_profile=\DB::table('user_payment_details')
		                  ->join('user_profile','user_profile.user_id','=','user_payment_details.uid')
		                  ->leftJoin('education', 'education.education_id', '=', 'user_profile.education')
						   ->leftJoin('user_reg', 'user_reg.id', '=', 'user_profile.user_id')
					       ->where('user_payment_status','=','1')
                           ->where('gender','=','male')
                           ->get();
					 // var_dump($high_profile);exit();
					 
				foreach($high_profile as $package_userid)
				  {
						  $pk_userid =   $package_userid->uid;
						
					
				  }
             \Session::put('package_user_id',$pk_userid);
             return $high_profile;
         }
         else
         {
            
          }
            
         
     }
        else
        {
             $high_profile=\DB::table('user_payment_details')
		                  ->join('user_profile','user_profile.user_id','=','user_payment_details.uid')
		                  ->leftJoin('education', 'education.education_id', '=', 'user_profile.education')
						   ->leftJoin('user_reg', 'user_reg.id', '=', 'user_profile.user_id')
					       ->where('user_payment_status','=','1')
                           ->get();
					 // var_dump($high_profile);exit();
					 
				foreach($high_profile as $package_userid)
				  {
						  $pk_userid =   $package_userid->uid;
						
					
				  }
				  $pk_userid="";
              \Session::put('package_user_id',$pk_userid);
              return $high_profile;
        }
     
		 
		
	}
}