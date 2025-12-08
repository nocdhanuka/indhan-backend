<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

use Input;
//use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route;
use Request;
//use Illuminate\Routing\Router;

class permission extends Model
{
  
     protected $table = 'pages';
     public $timestamps = false;
    
  function permission()
	{
		
		$permission="";
        
         $role_sess=\Session::get('login_role_id');
         $sess_perm=\Session::get('permission');
		
     if($sess_perm) 
        {
    
         //$method = Request::getMethod();
        $pathInfo = Request::getPathInfo();

       //   echo __FUNCTION__; exit;
        $function= ( explode( '/', $pathInfo ) );
         
        $function_name=$function[2];
         
      
	
     $select_pages=\DB::table('pages')
                   ->where('pages',$function_name)
                   ->get();
         
        foreach($select_pages as $row)
        {
            $page_id=$row->p_id;
        
        }
        
	 
    if($select_pages) 
    {
    
	 $id=explode(',',$sess_perm);
       
        
	  $a=(in_array($page_id,$id));
        
    
        if($a=="true") 
        {
      
            $permission = "access";
            
           
        } 
        else 
        {
            $permission = "failed";
          //  echo $permission;exit;
            redirect('admin/not_admin');
        }
     } 
     else 
         {
        $permission = "failed";
          }
	 }
      
      //var_dump($permission);exit;
	 return $permission;
    }
    
    
}