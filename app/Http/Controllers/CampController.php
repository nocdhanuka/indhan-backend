<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\permission;
use Carbon\Carbon;
use View;
use Input;
use Response;
use Excel;
use File;
use App\models\dailyrecommendation;
use Swift_Mailer;
use Flash;
use Notify;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Session;
use App\AppMenu;
use App\AppMenuPermission;
use App\DataSource;
use App\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Yajra\Datatables\Facades\Datatables;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use Khill\Lavacharts\Lavacharts;
use DateTime;
use App\GeneralUser;
use App\User;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

class CampController extends Controller
{
    public $db_Id;
    public function __construct()
    {
        $sess = \Session::get('adminid');
        if (!isset($sess) || $sess == "") {
            return redirect('/');
        }
    }

    public function campaignList()
    {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        $currentDate = date('Y-m-d');

        $result = DB::table('campaign')
                    ->join('campaign_status_type', 'campaign_status_type.status_id', '=', 'campaign.campaign_status')
                    ->select('campaign.*', 'campaign_status_type.status_name')
                    //->where('date_of_expire','>=', $currentDate)
                    ->get(); 
        foreach ($result as $key => $value) {
            
           $resp = "SELECT COUNT(distinct r.ecn) as resp_count,DATE(r.resp_timestamp) FROM campaign_response r 
           WHERE r.campaign_id =".$value->campaign_id;
           $resp_count  = collect(DB::select($resp));

        //    $abc = DB::table('campaign_response')
        //         ->select('campaign_response.*')
        //         ->where('campaign_response.campaign_id', '=', $value->campaign_id)
        //         ->groupBy('campaign_response.ecn',DB::raw("DATE_FORMAT(campaign_response.resp_timestamp, '%Y-%m-%d')"))
        //         ->count();
        //     dd($abc)
        //    dd($resp_count[0]->resp_count);
           // $resp_count = DB::table('campaign_response')
                        // ->distinct('ecn')
             //           ->where('campaign_id', $value->campaign_id)
                        
               //         ->count('campaign_id');
            
            $value->resp_count = $resp_count[0]->resp_count;
        }
        return view('backend.campaign.campaignList')->with('result',$result);
    }

    public function getAllCampList(Request $request)
    {
        try{
            $data = $request->json()->all();
            $query      = " 
                            SELECT                                 
                                @a:=@a+1  as serial_number,
                                A.*                                
                            FROM
                                (
                                    SELECT c.campaign_id,c.campaign_name,s.status_name FROM campaign c
                                    JOIN campaign_status_type s ON(s.status_id = c.campaign_status)
                                    WHERE c.date_of_expire >= CURRENT_DATE) A,
                                (select @a:=0) initvars";
            $campaigns  = collect(DB::select($query));

            foreach ($campaigns as $key => $value) {
                // print_r($campaigns);
                $resp_count = DB::table('campaign_response')
                            ->where('campaign_id', $campaigns[$key]->campaign_id)
                            ->count('campaign_id');
                $campaigns[$key]->resp_count = $resp_count;
            }
            return Datatables::of($campaigns)->make(); 	
        } catch (Exception $e) {
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function createCampaign()
    {
        $active_tab = "tab1default";
        return view('backend.campaign.createCampaign',compact('active_tab'));
    }
    public function saveCampaignStep1(Request $request)
    {
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first(); 
        
        $id = DB::table('campaign')->insertGetId([
            'campaign_name' => $request->name,
            'camp_mandatory' => $request->mandatory,
            'camp_push_notify' => $request->push_notify,
            'date_of_expire' => $request->expiry_date,
            'campaign_type_id' => $request->campType,
            'campaign_status' => 2,
            'response' => 0,
            'rec_flag' => $request->is_recurring
        ]);
        // $this->db_Id = $id; 
        $message = "Hooray";
        $this->broadcastMessage('Aishwarya',$message);
        Session::flash('message', "Campaign Successfully Created.");
        $active_tab = "tab2default";
        // return redirect()->back()->with(['active_tab' => $active_tab]);
        return view('backend.campaign.createCampaign',compact('active_tab'))->with('id', $id);
    }

    public function editCampaignStep1(Request $request)
    {
        // dd($request);
        $id = DB::table('campaign')
            ->where('campaign_id', $request->campaign_id)
            ->update([
                'campaign_name' => $request->name,
                'camp_mandatory' => $request->mandatory,
                'camp_push_notify' => $request->push_notify,
                'date_of_expire' => $request->expiry_date,
                'campaign_type_id' => $request->campType,
                'campaign_status' => 1,
                'response' => 0,
                'rec_flag' => $request->is_recurring
            ]);


            $data1 = DB::table('campaign')
            ->where('campaign_id', $request->campaign_id)
            ->get();
        $data2 = DB::table('campaign_questions')
            ->where('campaign_id', $request->campaign_id)
            ->get();

            $result = array();
            $resultopt = array();
            // $res['options'] ="";

        // dd($data2);
        foreach ($data2 as $value) {
            $res['campaign_que_id'] = $value->campaign_que_id;
            $res['campaign_id'] = $value->campaign_id;
            $res['question_number'] = $value->question_number;
            $res['question_type'] = $value->question_type;
            $res['question_text'] = $value->question_text;
            $res['mandatory_ans'] = $value->mandatory_ans;
            $res['option_type'] = $value->option_type;
            $res['proceed_to'] = $value->proceed_to;

            $data3 = DB::table('campaign_option')
            ->where('campaign_id', $value->campaign_id)
            ->where('ques_number', $value->question_number)
            ->get();
            
            if ($data3) {
                foreach ($data3 as $optValue) {
                    $resultopt[] = $optValue;                     
                }
            }
            $result[] = $res;
        }
        for ($i=0; $i < count($result); $i++) { 
        //    echo json_encode($result[$i])."<br>";
        //    if($result[$i]['question_type'] == 0){
            // echo json_encode($resultopt[$i]);
            for ($j=0; $j < count($resultopt); $j++) { 
                if($result[$i]['question_number'] == $resultopt[$j]->ques_number){
                    $result[$i]['options'][] = $resultopt[$j];
                }
            }
        //    }
        }
        // echo "<pre>";
        // print_r($result);
        // echo "<pre>";
       
        // dd($data2);
        return view('backend.campaign.editCampaign')->with(['data1' => $data1, 'data2' => $data2,'result' => $result]);


        // $data1 = DB::table('campaign')
        //     ->where('campaign_id', $request->campaign_id)
        //     ->get();
        // $data2 = DB::table('campaign_questions')
        //     ->where('campaign_id', $request->campaign_id)
        //     ->get();
        // // Session::flash('message', "Campaign Successfully Created.");
        // return view('backend.campaign.editCampaign')->with(['data1' => $data1, 'data2' => $data2]);
    }

    public function saveCampaignStep2(Request $request)
    {
        $resultData = array();
        $resultRow = array();
        $optionArr = array();
        $optTableData = array();        

        $j = 1;
        $v = 1;

        // dd($request);
       

        

        for ($i = 0; $i < count($request->queType); $i++) {

            // echo "<pre>";
            // print_r($request);
            // echo "<pre>";

            // $resultData['queType'] = $request->queType[$i];
            

            if($request->queType[$i] == 0){
                if($request->option_type[$i] == 1){
                    print_r("Option Type :: ".$request->option_type[$i]);
                    $resultData['queNo'] = $request->queNumber[$i];
                    $resultData['queType'] = $request->queType[$i];
                    $resultData['queText'] = $request->queText[$i];
                    $resultData['mandAns'] = $request->mand[$i];
                    $resultData['option_type'] = $request->option_type[$i];
                    $resultData['optValue'] = '';
                    $resultData['proceed_to'] = '';
                }elseif($request->option_type[$i] == 2){
                    print_r("Text Type :: ".$request->option_type[$i]);
                    $resultData['queNo'] = $request->queNumber[$i];
                    $resultData['queType'] = $request->queType[$i];
                    $resultData['queText'] = $request->queText[$i];
                    $resultData['mandAns'] =$request->mand[$i];
                    $resultData['option_type'] = $request->option_type[$i];
                    $resultData['optValue'] = '';
                    $resultData['proceed_to'] = '';
                }
            }else{
                $resultData['queNo'] = $request->queNumber[$i];
                $resultData['queType'] = $request->queType[$i];
                $resultData['queText'] = $request->queText[$i];
                $resultData['mandAns'] = '';
                $resultData['optValue'] = '';
                $resultData['option_type'] = 3;
                $resultData['proceed_to'] = '';
            }   
            // echo "<pre>";     
            // print_r($resultData);
            // echo "<pre>";

            // if ($request->queType[$i] != 2) {
            //     if ($request->queType[$i] == 0) {
            //         $resultData['queNo'] = $request->queNumber[$i];   
            //         // $resultData['option_type'] = $request->option_type[$i];                 
            //     } else {
            //         // $resultData['option_type'] = $request->option_type[$i];
            //         $resultData['queNo'] = $request->queNumber[$i]; 
            //     }
            //     // $resultData['queNo'] = $request->queNumber[$i];
            //     $resultData['queText'] = $request->queText[$i];
            //     $resultData['mandAns'] = $request->mand[$i];
            //     // $resultData['optValue'] = $request->option_value[$i];
                
            // } else {
            //     $resultData['queNo'] = $request->queNumber[$i];
            //     $resultData['queText'] = $request->queText[$i];
            //     $resultData['mandAns'] = '';
            //     $resultData['optValue'] = '';
            //     // $resultData['option_type'] = $request->option_type[$i];
            // }
            // $resultData['option_type'] = $request->option_type[$i];
            $resultRow[] = $resultData;
        }

        // dd($request->all());

        // for ($i = 0; $i < count($request->proceedTo); $i++) {
        //     $obj['campaign_id'] = $request->campaign_id;
        //     $obj['optValue'] = isset($request->option_value[$i]) ? ($request->option_value[$i]) : ('');
        //     $obj['que_opt_val'] = isset($request->que_opt_val[$i]) ? ($request->que_opt_val[$i]) : ($request->option_value[$i]);
        //     $obj['proceed_to'] = isset($request->proceedTo[$i]) ? ($request->proceedTo[$i]) : ('');
        //     $optTableData[] = $obj;
        // }
        // for ($i = 0; $i < count($request->que_opt_val); $i++) {
        //     $obj['campaign_id'] = $request->campaign_id;
        //     $obj['optValue'] = $request->option_value[$i]; ($request->option_value[$i]) ? ($request->option_value[$i]) : ('');
        //     $obj['que_opt_val'] = $request->que_opt_val[$i];
        //     $obj['proceed_to'] = $request->proceedTo[$i];
        //     $optTableData[] = $obj;
        // }
    
       
        // echo "<pre>";
        // print_r($optTableData);
        // echo "<pre>";

        dd($resultRow);    

        // for ($j=0; $j < count($optTableData); $j++) { 
        //     $result = DB::table('campaign_option')->insert([
        //         'campaign_id' => $request->campaign_id,
        //         'ques_number' => $optTableData[$j]['que_opt_val'],
        //         'option_value' => $optTableData[$j]['optValue'],
        //         'proceed_to' => $optTableData[$j]['proceed_to']
        //     ]);
        // }
        for ($i = 0; $i < count($resultRow); $i++) {
            $result = DB::table('campaign_questions')->insertGetId([
                'campaign_id' => $request->campaign_id,
                'question_number' => $resultRow[$i]['queNo'],
                'question_type' => $resultRow[$i]['queType'],
                'question_text' => $resultRow[$i]['queText'],
                'mandatory_ans' => $resultRow[$i]['mandAns'],
                'option_value' => $resultRow[$i]['optValue'],
                'option_type' => $resultRow[$i]['option_type'],
                'proceed_to' => $resultRow[$i]['proceed_to']
            ]);
        }
        $active_tab = "tab1default";
        // return redirect()->back()->with(['active_tab' => $active_tab]);
        return view('backend.campaign.createCampaign',compact('active_tab')); 
    }

    public function campaignOption(Request $request)
    {

        // foreach ($request->option_value as $value) {
        //     $result = DB::table('campaign_option')->insert([
        //         'campaign_id' => 1,
        //         'option_type' => 1,
        //         'option_value' => $value,
        //     ]);
        // }

        return view('backend.campaign.createCampaign');
    }

    public function getCampOptions(Request $request)
    {
        $result = DB::table('campaign_option')->get();
        return $result;
    }

    public function editCampaign(Request $request, $id)
    {
        $data1 = DB::table('campaign')
            ->where('campaign_id', $id)
            ->get();
        $data2 = DB::table('campaign_questions')
            ->where('campaign_id', $id)
            ->get();
            $result = array();
            $resultopt = array();
            // $res['options'] ="";

        // dd($data2);
        foreach ($data2 as $value) {
            $res['campaign_que_id'] = $value->campaign_que_id;
            $res['campaign_id'] = $value->campaign_id;
            $res['question_number'] = $value->question_number;
            $res['question_type'] = $value->question_type;
            $res['question_text'] = $value->question_text;
            $res['mandatory_ans'] = $value->mandatory_ans;
            $res['option_type'] = $value->option_type;
            $res['proceed_to'] = $value->proceed_to;

            $data3 = DB::table('campaign_option')
            ->where('campaign_id', $value->campaign_id)
            ->where('ques_number', $value->question_number)
            ->get();
            
            if ($data3) {
                foreach ($data3 as $optValue) {
                    $resultopt[] = $optValue;                     
                }
            }
            $result[] = $res;
        }
        for ($i=0; $i < count($result); $i++) { 
        //    echo json_encode($result[$i])."<br>";
        //    if($result[$i]['question_type'] == 0){
            // echo json_encode($resultopt[$i]);
            for ($j=0; $j < count($resultopt); $j++) { 
                if($result[$i]['question_number'] == $resultopt[$j]->ques_number){
                    $result[$i]['options'][] = $resultopt[$j];
                }
            }
        //    }
        }
        // echo "<pre>";
        // print_r($result);
        // echo "<pre>";
       
        // dd($data2);
        return view('backend.campaign.editCampaign')->with(['data1' => $data1, 'data2' => $data2,'result' => $result]);
    }
    public function editCampaignStatus(Request $request, $id, $status_id)
    {
        // dd($id);
        $data1 = DB::table('campaign')
            ->where('campaign_id', $id)
            ->update(['campaign_status' => $status_id]);

        // $result = DB::table('campaign')
        //     ->join('campaign_status_type', 'campaign_status_type.status_id', '=', 'campaign.campaign_status')
        //     ->select('campaign.*', 'campaign_status_type.status_name')
        //     ->get();

            $result = DB::table('campaign')
            ->join('campaign_status_type', 'campaign_status_type.status_id', '=', 'campaign.campaign_status')
            ->select('campaign.*', 'campaign_status_type.status_name')
            //->where('date_of_expire','>=', $currentDate)
            ->get(); 
    foreach ($result as $key => $value) {
   
    $resp_count = DB::table('campaign_response')
                ->where('campaign_id', $value->campaign_id)
                ->count('campaign_id');
    $value->resp_count = $resp_count;
    // echo "<pre>";
    // print_r($resp_count);
    // echo "<pre>";
}
        return view('backend.campaign.campaignList')->with('result', $result);
    }

    public function updateCampaignStatus(Request $request)
    {
        // dd($id);
        $data1 = DB::table('campaign')
            ->where('campaign_id',$request->campaign_id)
            ->update(['campaign_status' => $request->status]);

        $result = DB::table('campaign')
            ->join('campaign_status_type', 'campaign_status_type.status_id', '=', 'campaign.campaign_status')
            ->select('campaign.*', 'campaign_status_type.status_name')
            ->get();


        return  json_encode(array("status"=>"success","message"=>"status updated successfully"));
        
    }

    public function editoption(Request $request, $id)
    {
        $data = DB::table('campaign_questions')
            ->where('campaign_que_id', $id)
            ->update([
                'option_value' => $request->option_value[0]
            ]);
        $data1 = DB::table('campaign')
            ->where('campaign_id', $request->campaign_id[0])
            ->get();
        $data2 = DB::table('campaign_questions')
            ->where('campaign_id', $request->campaign_id[0])
            ->get();
        return view('backend.campaign.editCampaign')->with(['data1' => $data1, 'data2' => $data2]);
    }
    public function deleteQue(Request $request)
    {
        $campaign_que_id = $request->input('campaign_que_id');
        $data = DB::table('campaign_questions')
            ->where('campaign_que_id', $campaign_que_id)
            ->delete();
        $data1 = DB::table('campaign')
            ->where('campaign_id', $request->campaign_id)
            ->get();
        $data2 = DB::table('campaign_questions')
            ->where('campaign_id', $request->campaign_id)
            ->get();

        // return view('backend.campaign.editCampaign')->with(['data1' => $data1, 'data2' => $data2]);
        return back();
    }

    public function getCampaign(Request $request)
    {
        $currentDate = date('Y-m-d');
        $enc = $request->input('enc');
        $c_type_id = $request->input('c_type_id');
        $data = DB::table('campaign')
            ->where('campaign_type_id', $c_type_id)
            ->where('campaign_status', 1)
            ->get();

        $fdata=array();
        foreach ($data as $value) { 
            if($value->rec_flag == 0){  
                $campdata = DB::table('campaign_response')
                ->where('ecn', $enc)
                ->where('campaign_id', $value->campaign_id)
                ->where('campaign_response_status', 1)
                ->whereDate('resp_timestamp', '=', Carbon::now()->toDateString())
                ->get(); 
                if(!empty($campdata)){

                    $value->camp_response='F';
                }else{
                    $value->camp_response='N';
                }
            } 
            $fdata[]=$value;
        }

        echo  json_encode($fdata);
    }

    public function getMandCampaign(Request $request)
    {
        $enc = $request->input('enc');
        $data = DB::table('campaign')
            ->where('campaign_status', 1)
            ->where('camp_mandatory', 1)
            ->get();

        $camp =array();
        $x = array();
        foreach ($data as $value) {
            $campdata = DB::table('campaign_response')
                        ->where('ecn', $enc)
                        ->where('campaign_id', $value->campaign_id)
                        ->where('campaign_response_status', 1)
                        ->get();
            foreach($campdata as $respvalue) {
                $x = $respvalue->campaign_id;
            }
            if($x != $value->campaign_id){
                $obj['campaign_id'] = $value->campaign_id;
                $obj['campaign_name'] = $value->campaign_name;
                $camp[] = $obj;
            }
        }
        return response()->json(['message' => "You have a pending request to respond",'data' => $camp,'statusCode'=> '200']);
    }

    public function getCampAns(Request $request)
    {
        $ecn = $request->input('ecn');
        $id = $request->input('campaign_id');
	
        $data = DB::table('campaign_response')
                        ->where('ecn', $ecn)
                        ->where('campaign_id',$id)
                        ->where('campaign_response_status', 1)
                        ->whereDate('resp_timestamp', '=', Carbon::now()->toDateString())
                        ->get();
	// print_r($data);

        // $camp =array();
        // $x = array();
        // foreach ($data as $value) {
        //     $campdata = DB::table('campaign_response')
        //                 ->where('ecn', $enc)
        //                 ->where('campaign_id', $value->campaign_id)
        //                 ->where('campaign_response_status', 1)
        //                 ->get();
        //     foreach($campdata as $respvalue) {
        //         $x = $respvalue->campaign_id;
        //     }
        //     if($x != $value->campaign_id){
        //         $camp[] = $value->campaign_id;
        //     }
        // }
        return response()->json(['message' => "answers",'data' => $data,'statusCode'=> '200']);
    }

    public function getCampaignChatList(Request $request, $id)
    {

        $data1 = DB::table('campaign')
            ->where('campaign_id', $id)
            ->get();
        $data2 = DB::table('campaign_questions')
            ->where('campaign_id', $id)
            ->get();
            $result = array();
            $resultopt = array();
        foreach ($data2 as $value) {
            $res['campaign_que_id'] = $value->campaign_que_id;
            $res['campaign_id'] = $value->campaign_id;
            $res['question_number'] = $value->question_number;
            $res['question_type'] = $value->question_type;
            $res['question_text'] = $value->question_text;
            $res['mandatory_ans'] = $value->mandatory_ans;
            $res['option_type'] = $value->option_type;
            $res['proceed_to'] = $value->proceed_to;
            $data3 = DB::table('campaign_option')
            ->where('campaign_id', $value->campaign_id)
            ->where('ques_number', $value->question_number)
            ->get();
            
            if ($data3) {
                foreach ($data3 as $optValue) {
                    $resultopt[] = $optValue;                     
                }
            }
            $result[] = $res;
        }
        for ($i=0; $i < count($result); $i++) { 
        //    echo json_encode($result[$i])."<br>";
           if($result[$i]['option_type'] == 1){
            // echo json_encode($resultopt[$i]);
            if ($result[$i]['question_type'] == 0) {
                for ($j=0; $j < count($resultopt); $j++) {
                    if ($result[$i]['question_number'] == $resultopt[$j]->ques_number) {
                        $result[$i]['options'][] = $resultopt[$j];
                    }
                }
            }
           }elseif($result[$i]['option_type'] == 2){
             $result[$i]['options'][] = [];
           }else if($result[$i]['option_type'] == 3){
            $result[$i]['options'][] = [];
          }
        }


        // $data = DB::table('campaign_questions')
        //     ->where('campaign_id', $id)
        //     ->get();

        // $optQueArray = array();
        // $textQueArray = array();
        // $submitArray = array();
        // $questionNumber = "";
        // foreach ($data as $value) {
        //     if ($value->option_type == 1) {
        //         if ($value->question_type == 0) {
        //             $optQueArray['campaign_que_id '] = $value->campaign_que_id;
        //             $optQueArray['campaign_id'] = $value->campaign_id;
        //             $optQueArray['question_number'] = $value->question_number;
        //             $optQueArray['question_text'] = $value->question_text;
        //             $optQueArray['mandatory_ans'] = $value->mandatory_ans;
        //             $optQueArray['option_type'] = $value->option_type;
        //             $questionNumber = $value->question_number;
        //             // $optQueArray[] = $ob1;
        //         } else {
        //             $queNum = $value->question_number;
        //             $options = $value->option_value;
        //             $proceed = $value->proceed_to;
        //             $obj1 = [$options, $proceed];
        //             $optQueArray['options'][] = $obj1;
        //         }

        //     } else if ($value->option_type == 2) {
        //         if ($value->question_type == 0) {
        //             $textQueArray['campaign_que_id '] = $value->campaign_que_id;
        //             $textQueArray['campaign_id'] = $value->campaign_id;
        //             $textQueArray['question_number'] = $value->question_number;
        //             $textQueArray['question_text'] = $value->question_text;
        //             $textQueArray['mandatory_ans'] = $value->mandatory_ans;
        //             $textQueArray['option_type'] = $value->option_type;
        //             $textQueArray['proceed_to'] = $value->proceed_to;
        //             $textQueArray['options'] = [];
        //         }
        //     } else {
        //         $submitArray['campaign_que_id '] = $value->campaign_que_id;
        //         $submitArray['campaign_id'] = $value->campaign_id;
        //         $submitArray['question_text'] = $value->question_text;
        //         $submitArray['options'] = [];
        //     }
        // }
        
        // $resultData[] = $optQueArray;
        // $resultData[] = $textQueArray;
        // $resultData[] = $submitArray;
        
        return json_encode($result);
    }

    public function postResponse(Request $request)
    {
        $result = DB::table('campaign_response')->insert([
            'campaign_id' => $request->input('campaign_id'),
            'user_id' => $request->input('user_id'),
            'question_list' => json_encode($request->input('question_list')),
            'answer_list' => json_encode($request->input('answer_list')),
            'resp_timestamp' => $request->input('timestamp')
        ]);
        return json_encode($result);
    }

    public function saveRow(Request $request)
    {
        // $data = $request;
        $data        = Input::all();
        $c_id = $data['campaign_id'];
        // $optVal = $data['optVal'];
        if(isset($data['optVal'])){
        if (count($data['optVal']) > 0) {
            $result = DB::table('campaign_questions')->insert([
                'campaign_id' => $data['campaign_id'],
                'question_number' => $data['questionNum'],
                'question_type' => $data['que_type'],
                'question_text' => $data['question'],
                'mandatory_ans' => $data['mand'],
                'option_type' => 1
            ]);

            for ($j=0; $j < count($data['optVal']); $j++) {
                $result = DB::table('campaign_option')->insert([
                    'campaign_id' => $data['campaign_id'],
                    'ques_number' => $data['questionNum'],
                    'option_value' => $data['optVal'][$j],
                    'proceed_to' => $data['proceedTo'][$j]
                ]);
            }
        }
        }else{
            $result = DB::table('campaign_questions')->insert([
            'campaign_id' => $data['campaign_id'],
            'question_number' => $data['questionNum'],
            'question_type' => $data['que_type'],
            'question_text' => $data['question'],
            'mandatory_ans' => $data['mand'],
            'option_value' => '',
            'option_type' => 2,
            'proceed_to' => $data['proceed']
        ]);
        }
        echo json_encode($data);
        return json_encode($data);
    }

    public function updateOption(Request $request)
    {
        // $data = Input::all();;
        // echo json_encode($data);
       

        // echo count($request->optionId);


        // dd($request->all());
        //  return json_encode($request);
        for ($i=0; $i < count($request->optionId); $i++) { 
            $result = DB::table('campaign_option')->where('option_id', $request->optionId[$i])->update([
                'option_value' => $request->optionData[$i],
                'proceed_to' => $request->proceedData[$i]
            ]);
        }
        //$result = DB::table('campaign_option')->update([
        //     'campaign_que_id' => $request->campaign_que_id,
        //     'campaign_id' => $request->campaign_id,
        //     'question_number' => $request->question_number,
        //     'question_type' => $request->campaign_id,
        //     'question_text' => $request->question_text,
        //     'mandatory_ans' => $request->mandatory_ans,
        //     'option_value' => $request->option_value,
        //     'option_type' =>  $request->option_type,
        //     'proceed_to' => $request->proceed_to
        //         "option" 

        $active_tab = "tab2default";
        // return redirect()->back()->with(['active_tab' => $active_tab]);
        // return view('backend.campaign.createCampaign',compact('active_tab')); 
        return back()->with(compact('active_tab'));

    }

    public function updateQue(Request $request)
    {
        // dd($request);

        $result = DB::table('campaign_questions')
        ->where('campaign_que_id', $request->campaign_que_id[0])->update([
            'question_text' => $request->queText[0]
        ]);
        return back();
    }
    public function updateTextProceedTo(Request $request)
    {
        $result = DB::table('campaign_questions')
        ->where('campaign_que_id', $request->campaign_que_id[0])->update([
            'proceed_to' => $request->proceedData[0]
        ]);
        return back();
    }

    public function rawData(Request $request, $id)
    {
        // dd($id);
        $sess       =   \Session::get('adminid');
        $userData   =   \DB::table('admin')->where(['id' => $sess])->first();
        $questionsArr = array();
        $result1 = array();$questionArr = array();

        $filledCampaigns = DB::table('campaign_response')
                ->select('campaign_response.*')
                ->where('campaign_response.campaign_id', '=', $id)
                ->groupBy('campaign_response.ecn',DB::raw("DATE_FORMAT(campaign_response.resp_timestamp, '%Y-%m-%d')"))
                ->get();
        
        
        
        // DB::table('campaign_response')
        // ->select(DB::raw('campaign_id, max(resp_timestamp) as resp_timestamp,campaign_id,question_list,answer_list,ecn'))
        // ->where('campaign_id', '=', $id)
        // ->groupBy('ecn')
        // //->orderBy('paper_update', 'desc')
        // ->get();
        
        

        $campaignName = DB::table('campaign')
                        ->select('campaign_name')
                        ->where('campaign_id', '=', $id)
                        ->get();
        // print_r($campaignName[0]->campaign_name);
       $headerArr = array("Name","Response Date","ECN");
        $ecnArr = array();
        $timeStampArr = array();
        $questions = DB::table('campaign_questions')
                    ->where('campaign_id', $id)
                    ->get();

        foreach ($questions as $qk=>$queValue) {
            // print_r($queValue->campaign_que_id);
            // print_r($queValue->question_text);
            $questionArr[$queValue->campaign_que_id] = $queValue->question_text;
            ++$qk;
            $headerArr[]="Question ".$qk;
            $headerArr[]="Answer ".$qk;
        }

        
        $collection = array();
        foreach ($filledCampaigns as $key => $camp) {
            // dd($camp);
            $questions_ids=json_decode($camp->question_list);
            // echo "<pre>";
            // print_r($questions_ids);
            // echo "<pre>";
            $answer_list=json_decode($camp->answer_list);
            $answerData=array();
            $quesData=array();
            foreach($questions_ids as $k=>$qid){
                $quesData[$qid] =!empty($questions_ids[$k]) ? $questions_ids[$k] :'';
                $answerData[$qid]=!empty($answer_list[$k]) ? $answer_list[$k] :'';
            }

            $dataArr=array();

            $query = " SELECT id, name as username FROM users WHERE ECN ='" . $camp->ecn . "' LIMIT 0,1";

            $user = DB::select(DB::raw($query));

            if(empty($user[0])){
                    
                $query = "SELECT  zonal_manager_id  as id, zonal_manager_name as username FROM zonal_managers WHERE ECN ='" . $camp->ecn . "' LIMIT 0,1";
                $user = DB::select(DB::raw($query));
            }
         

            if(empty($user[0])){
                    
                $query = "SELECT  regional_manager_id  as id, regional_manager_name as username FROM regional_managers WHERE ECN ='" . $camp->ecn . "' LIMIT 0,1";
                $user = DB::select(DB::raw($query));
            }

            if(empty($user[0])){
                    
                $query = "SELECT  area_manager_id  as id, area_manager_name as username FROM area_managers WHERE ECN ='" . $camp->ecn . "' LIMIT 0,1";
                $user = DB::select(DB::raw($query));
            }

            if(empty($user[0])){
                    
                $query = "SELECT  sales_regional_manager_id  as id, sales_regional_manager_name as username FROM sales_regional_managers WHERE ECN ='" . $camp->ecn . "' LIMIT 0,1";
                $user = DB::select(DB::raw($query));
            }

            
            if(empty($user[0])){
                    
                $query = "SELECT  sales_area_manager_id  as id, sales_area_manager_name as username FROM sales_area_managers WHERE ECN ='" . $camp->ecn . "' LIMIT 0,1";
                $user = DB::select(DB::raw($query));
            }

            if(empty($user[0])){
                    
                $query = "SELECT  dd_id  as id, name as username FROM dhanuka_doctor WHERE ECN ='" . $camp->ecn . "' LIMIT 0,1";
                $user = DB::select(DB::raw($query));
            }

            
            if(empty($user[0])){
                    
                $query = "SELECT  general_id   as id, general_user_name as username FROM general  WHERE ECN ='" . $camp->ecn . "' LIMIT 0,1";
                $user = DB::select(DB::raw($query));
            }

            $dataArr[]=!empty($user[0]->username) ? $user[0]->username :'NA';
            $dataArr[]= \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $camp->resp_timestamp)->format('Y-m-d');
            $dataArr[]=$camp->ecn;
            
            foreach ($questionArr as $queId=>$queTxt) {
                $dataArr[]= !empty($quesData[$queId]) ? $queTxt : '';//$queTxt;
                $dataArr[]= !empty($answerData[$queId]) ? $answerData[$queId] : '';
            }
            $collection[]=$dataArr;
        }
            
        $result1["q"] = $collection;
        
        $result1["headers"] = $headerArr;

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=".$campaignName[0]->campaign_name.".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
    
        $columns = $headerArr;
        $callback = function() use ($collection, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
    
            foreach($collection as $col) {
                fputcsv($file,$col);
            }
            fclose($file);
            
        };
        return Response::stream($callback, 200, $headers);
        
      //  return view('backend.campaign.campRawData')->with('result', $result1);
    }

    public function campBroadcastMessage(Request $request)
    {
        
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('Dhanuka FSMD initiated a survey! Respond now');
        $notificationBuilder->setBody("Push Notification")->setSound('default');
                    
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'sender_name' => "Dhanuka",
            'message' => "Push Notification"
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens =  'ekMQRGIaW68:APA91bELv_n1vnHbfGeVu-3WkfXEZmldmS9YdcNoRsiFC5XTywNbJECYOfp8ILr1XEprnwr7oiY7h-jc3ERor-w1PRauS81y_8Iyx8Aj86ChxcQOeWhYrlLr3Oa1XoybxDcTLGI_PkTP';//User::all()->pluck('device_id')->toArray();
        // dd($tokens);
        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
        dd($downstreamResponse);
        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
        $downstreamResponse->tokensToDelete();
        $downstreamResponse->tokensToModify();
        $downstreamResponse->tokensToRetry();
        // return $downstreamResponse->numberSuccess();
    }

    public function getSubMenu(Request $request,$id)
    {
        $result = DB::table('campaign_type')
                            ->where('app_menu_id',$id)
                            ->get();
        return json_encode($result);
    }

    public function  sendCampNotification(){
        

        $tokens = User::where("device_id","!=",'')->pluck('device_id')->toArray();
        dd($tokens);

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);


        $message="";
        $notificationBuilder = new PayloadNotificationBuilder('Dhanuka FSMD initiated a survey! Respond now');
        $notificationBuilder->setBody($message)
                    ->setSound('default')
                    ->setClickAction("http://localhost/dhanuka2/admin/campaign/saveCampaignStep1");
                    
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'sender_name' => $senderName,
            'message' => $message
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = User::all()->pluck('device_id')->toArray();
        // dd($tokens);
        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
    }

    public function broadcastMessage($senderName,$message)
    {
        
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('Dhanuka FSMD initiated a survey! Respond now');
        $notificationBuilder->setBody($message)
                    ->setSound('default')
                    ->setClickAction("http://localhost/dhanuka2/admin/campaign/saveCampaignStep1");
                    
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'sender_name' => $senderName,
            'message' => $message
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = User::all()->pluck('device_id')->toArray();
        // dd($tokens);
        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
        // dd($downstreamResponse->numberSuccess());
        // return $downstreamResponse->numberSuccess();
    }
}
