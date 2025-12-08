<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Excel;
use Input;
use App\models\dailyrecommendation;
class excelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDownload()
    {
    
        $data= dailyrecommendation::whereBetween('date',['2015-12-07','2015-12-09'])->get()->toArray();
        
       // var_dump($data);exit;
        
      

Excel::create('Filename', function($excel) use($data) {

    $excel->sheet('Sheetname', function($sheet) use($data) {

        $sheet->fromArray($data);

    });

})->export('xls');
        
    }
       public function postUserReportDetails()
	{
		$user_report=Input::all();
       
		$from=$user_report['date_from'];
		$to=$user_report['date_to'];
        
         $date_from = date("Y-m-d", strtotime($from));
		 $date_to=date("Y-m-d", strtotime($to));
	 	
     
        // $data= dailyrecommendation::whereBetween('date',['2015-12-07','2015-12-09'])->get()->toArray();
       $data= dailyrecommendation::whereBetween('date',[$date_from,$date_to])->get()->toArray();
      var_dump($data);exit;
         
         Excel::create('Filename', function($excel) use($data) {

         $excel->sheet('Sheetname', function($sheet) use($data) {

         $sheet->fromArray($data);

         });
             
      })->export('xls');
					
     
         
	if($user_rep_dtls) 
    { 
        echo 1; 
    } 
     else
         { 
             echo 0; 
         }
    
    }
    
}