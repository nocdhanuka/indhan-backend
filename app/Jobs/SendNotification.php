<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class SendNotification extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $data;
    
    public function __construct(array $data) {
        $this->data = $data;
    }
    
    public function handle()
    {   
        $message    = $this->data;
        $device_id  = $message['device_id'];
        $my_title   = $message['notification_title'];
        $my_body    = $message['notifiaction_mesaage'];
        
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        
        $notificationBuilder = new PayloadNotificationBuilder($my_title);
        $notificationBuilder->setBody($my_body)->setSound('default');
        $dataarray  = array(
                            "id" => 1,
                            "type" => 1,
                            'title' => $my_title,
                            'body' => $my_body,
                        );
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($dataarray);
        
        $option         = $optionBuilder->build();
        $notification   = $notificationBuilder->build();
        $data           = $dataBuilder->build();
        $token          = $device_id;
        
        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
        //dd($downstreamResponse);
    }
}
