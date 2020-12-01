<?php

use Common\Core\IpAddress;
use AccountService\Account\GuestAccountService;
use AccountService\LoginAccount\LoginAccountLoginType;

class Account_guest extends Guest_Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_service = GuestAccountService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_service->setBenchmark($this->benchmark);
        $this->_service->setUserAgent($this->_getUserAgent());
    }
    
    public function otpSend(){
        
        $this->log_benchmark("total_execution_time_start", "otpSend_start", "otpSend", "start");
        
        $this->required(array('username'));
        
        $username = $this->input->post('username', TRUE);
        
        $this->_service->setBenchmarkFun("otpSend");
        if ($result = $this->_service->gusetSendOtp($username)) {
            
            $this->log_benchmark("otpSend_start", "otpSend_end", "otpSend", "end");

            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    public function otpVerify(){
        
        $this->required(array('username', 'otp_code', 'password'));
        
        $username = $this->input->post('username', TRUE);
        $otp_code = $this->input->post('otp_code', TRUE);
        $password = $this->input->post('password', TRUE);
        $invite_code = $this->input->post('invite_code', TRUE);
        
        if ($result = $this->_service->gusetVerifyOtp($username, LoginAccountLoginType::MOBILE, $otp_code, $password, $invite_code)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    public function mobileQuickLogin(){
        
        $this->required(array('token'));

        $token = $this->input->post('token', TRUE);

        if ($result = $this->_service->mobileQuickLogin($token)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;

    }
    
    public function getOpenId(){
        
        $this->required(array('code'));
        
        $code = $this->input->post('code', TRUE);
        
        if ($result = $this->_service->gusetVerifyOtp($code)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
}
