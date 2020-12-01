<?php

namespace Common\Helper\RocketMessageBroker;
use Common\Helper\RocketMessageBroker\EventProducer;
use Common\Helper\RocketMessageBroker\RocketTopic;

class CommunicationProducer extends EventProducer{

    function __construct(){
        parent::__construct(RocketTopic::TOPIC_MESSAGE);
    }

    // /*
    //  * message_id & transaction_id is not being used
    //  */
    // public function sendNotification($deviceToken, $title, $content, $extended_content = array())
    // {
    //     if($data = $this->constructNotification($deviceToken, $title, $content, $extended_content))
    //     {
    //         if($this->trigger('hex_ics_notification',self::RABBITMQ_QUEUE_NOTIFICATION,$data)){
    //             return true;    
    //         }
    //         return false;
    //     }

    //     return false;
    // }
    
    public function sendSms($mobileNo, $content, $smsType)
    {
        if($data = $this->constructSMS($mobileNo, $content, $smsType))
        {   
            if($this->publishMessage($data, RocketTag::TAG_SMS)){
                return true;    
            }
            return false;
        }

        return false;
    }

    // protected function constructNotification($deviceToken, $title, $content, $extended_content = array())
    // {
    //     $temp                      = array();
    //     $temp['title']             = $title;
    //     $temp['content']           = $content;
    //     $temp['device_token']      = $deviceToken;
        
    //     if (!empty($extended_content)) {
    //         $temp['extended_content'] = json_encode($extended_content);
    //     }
        
    //     $temp = json_encode($temp);
    //     return $temp; 
    // }
    
    protected function constructSMS($mobileNo, $content, $smsType)
    {
        $data = array();
        $data['mobile_number'] = $mobileNo;
        $data['content']       = $content;
        $data['sms_type'] = $smsType;
        
        return json_encode($data);
    }
}
