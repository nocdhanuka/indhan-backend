<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Input;

class notification extends Model
{
    //
      protected $table = 'notification';
     public $timestamps = false;
   
  function headersearch()
  {
    $sess= \Session::get('id');
    $date = date("Y-m-d H:i:s");
		
		$return_value['login']=\ DB::table('user_reg')
                               ->join('user_profile','user_profile.user_id','=','user_reg.id')
							   ->where('user_id' ,$sess)
                               ->get(); 
		
      foreach ($return_value['login'] as $user)
			{  
				$id=$user->id;
				
			}
 
		 
 
			$return_value['notification']=\ DB::table('notification')
							->where('status', '=', '0')
							->where('r_id', '=', $sess)
							->get(); 
							   
			$count=count($return_value['notification']);
			
		 	$return_value['count_value']=$count;
			
      $my_data=Input::all();
		 
			foreach($return_value['notification'] as $getnoti)
				{
					$content= $getnoti->content;
		 
				}
		 
			if($my_data)
				{
					if(isset($my_data['stat'])) {
					$stat=$my_data['stat'];
		           echo $stat;
					if($stat==0)
						{
							$update_noti=\DB::table('notification')
							       ->where('status', '=', $stat)
							       ->update(['status' => '1']);
						}
					}
				}
		
		
		return $return_value;  
  }
  
    
}
