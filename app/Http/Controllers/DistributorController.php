<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB,Session,Validator;
use Yajra\Datatables\Facades\Datatables;

class DistributorController extends Controller
{
    public function __construct() {

        //$this->authMiddleWare();        
        //echo "<pre>"; print_r(session()->all()); exit;
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }
    
    public function getDistributorList()
    {
		return view('backend.distributor.distributorlist');       
	}
	
	public function postDistributorList(){
		
		$distributorList="select distributor_id,name,contact_number,address,type from distributor where status=1";
		//echo $distributorList;exit;                  
		 $distributorList  = collect(DB::select($distributorList));
         return Datatables::of($distributorList)->make();                             
		 //return $distributorList;                             
	}
    
	
	public function getAddDistributor(){
		
         return view('backend.distributor.adddistributor');
		 //return $distributorList;                             
	}
    
	
	public function getEditDistributor($id){
		$distributor = DB::table('distributor')
                                    ->where('distributor_id', $id)
                                    ->first();
         return view('backend.distributor.editdistributor',compact('distributor'));
		 //return $distributorList;                             
	}
    
	
	public function postCreateDistributor(Request $request){
		
		$sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
			$my_data= $request->all();        
			
			DB::table('distributor')->insert(['name'=>$my_data['name'],'contact_number'=>$my_data['contact_number'],'address'=>$my_data['address'],'type'=>$my_data['type'],'status'=>'1']);
			Session::flash('message', "Distributor Successfully Created.");
            return redirect('distributor/distributor-list');
			
		}else{
			return redirect('distributor/add-distributor');
			
		}

	}
	
	
	public function postUpdateDistributor(Request $request){
		
		$sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
			$my_data= $request->all();        
			$distId=$request->input('distId');
			DB::table('distributor')->where('distributor_id',$distId)->update(['name'=>$my_data['name'],'contact_number'=>$my_data['contact_number'],'address'=>$my_data['address'],'type'=>$my_data['type'],'updated_at'=>date("Y-m-d H:i:s")]);
			Session::flash('message', "Distributor Successfully Updated.");
            return redirect('distributor/distributor-list');
			
		}else{
			return redirect('distributor/edit-distributor/'.$distId);
			
		}

	}
	
	public function postDistributorDelete(Request $request){
		$sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= $request->all();        
            $rules  = array(
                            '_ticket'    => 'required|numeric'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                echo '0';
            }
            else {
                $distributor_id     = $my_data['_ticket'];
                $update_distributor = DB::table('distributor')
                                    ->where('distributor_id', $distributor_id)
                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_distributor) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
	}

	public function getRetailerList()
    {
		return view('backend.distributor.distributorlist');       
	}
	
	public function postRetailerList(){
		
		$distributorList1="SELECT r . * , GROUP_CONCAT( d.name
ORDER BY d.distributor_id ) as distributor FROM  `retailer` AS r INNER JOIN distributor d ON FIND_IN_SET( d.distributor_id, r.distributor ) >0 where r.status = '1' GROUP BY r.retailer_id";            
		 $distributorList1  = collect(DB::select($distributorList1));
		 // echo "<pre>"; print_r($distributorList1); die;
         return Datatables::of($distributorList1)->make();                             
		 //return $distributorList;                             
	}

	public function getAddRetailer(){
			
			$distributor = DB::table('distributor')->where('status',1)->get();

         return view('backend.distributor.addretailer',compact('distributor'));
		 //return $distributorList;                             
	}

	public function postRetailerDelete(Request $request){
		$sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
            $my_data= $request->all();        
            $rules  = array(
                            '_ticket'    => 'required|numeric'
                        );
            $validator  = Validator::make($my_data, $rules);
            if ($validator->fails()) {
                echo '0';
            }
            else {
                $distributor_id     = $my_data['_ticket'];
                $update_distributor = DB::table('retailer')
                                    ->where('retailer_id', $distributor_id)
                                    ->update(['status' => '2', 'updated_at' => date('Y-m-d H:i:s')]);
                if ($update_distributor) {
                    echo 1;
                } else {
                    echo 2;
                }
            }
        }
	}

	public function postCreateRetailer(Request $request){
		
		$sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
			$my_data= $request->all(); 
			$distributor = implode(',', $my_data['distributor']);
			$my_data['distributor'] = $distributor;
			// echo "<pre>"; print_r($my_data); die;
			
			DB::table('retailer')->insert(['name'=>$my_data['name'],'contact_number'=>$my_data['contact_number'],'address'=>$my_data['address'],'distributor'=>$my_data['distributor'],'status'=>'1']);
			Session::flash('message', "Retailer Successfully Created.");
            return redirect('distributor/distributor-list');
			
		}else{
			return redirect('distributor/add-retailer');
			
		}

	}

	public function getEditRetailer($id){
		$distributor = DB::table('distributor')->where('status',1)->get();
		$retailer = DB::table('retailer')
                                    ->where('retailer_id', $id)
                                    ->first();
         return view('backend.distributor.editretailer',compact('retailer','distributor'));
		 //return $distributorList;                             
	}

	public function postUpdateRetailer(Request $request){
		
		$sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        if ($userData) {
			$my_data= $request->all(); 
			$distributor = implode(',', $my_data['distributor']);
			$my_data['distributor'] = $distributor;       
			$distId=$request->input('distId');
			DB::table('retailer')->where('retailer_id',$distId)->update(['name'=>$my_data['name'],'contact_number'=>$my_data['contact_number'],'address'=>$my_data['address'],'distributor'=>$my_data['distributor'],'updated_at'=>date("Y-m-d H:i:s")]);
			Session::flash('message', "Retailer Successfully Updated.");
            return redirect('distributor/distributor-list');
			
		}else{
			return redirect('distributor/edit-retailer/'.$distId);
			
		}

	}
    
    
}
