<?php

namespace Common\Helper;

class ResponseMessage
{
    private $message;
    private $header;

    const RESULT = 'result';
    const TOTAL = 'total';

    /**
     * This is constructor
     */
    function __construct()
    {
        $this->header = new ResponseHeader();
        
        $this->setStatus(false);
        $this->setStatusCode($this->getHeader()->getStatus());
        $this->setMessage(null);
    }

    public function setStatusCode($code)
    {
        $this->message['status_code'] = $code;
    }
    
    public function setStatus($status)
    {
        $this->message['status'] = $status;
    }

    public function resetMessage()
    {
        //Reset previous messages
        foreach ($this->message as $key => $value) {
            unset($this->message[$key]);
        }

        $this->setStatusCcode($this->getHeader()->getStatus());
        $this->setStatus(null);
        $this->setMessage('');
    }

    public function setMessage($message, $option = NULL)
    {
        $temp_status = $this->getStatus();
        $temp_status_code = $this->getStatusCode();
        $this->message = array();
        $this->setStatus($temp_status);
        $this->setStatusCode($temp_status_code);

        if($option == null){
            $this->message['result'] = null;
        }
        else{
            if (is_array($option)) {
                foreach ($option as $key => $value) {
                    $this->message[$key] = $value;
                }
            }
            else{
                $this->message['result'] = $option;
            }
        }

        $this->message['message'] = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getJsonMessage()
    {
        return json_encode($this->message);
    }

    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Get the status code of this response
     */
    public function getStatusCode()
    {
        return $this->message['status_code'];
    }
    
    /**
     * Get the status of this response
     */
    public function getStatus()
    {
        return $this->message['status'];
    }
}
