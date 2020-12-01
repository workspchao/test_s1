<?php

use Common\Core\IpAddress;
use AccountService\Account\UserAccountService;


class Account_user extends User_Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_service = UserAccountService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_service->setUserAgent($this->_getUserAgent());
    }
    
    public function login(){
        
        $this->required(array('username','password'));

        $username = $this->input_post('username');
        $password = $this->input_post('password');
        $address  = $this->input_post('address');
        $lat      = $this->input_post('lat');
        $long     = $this->input_post('long');

        if ($result = $this->_service->userLogin($username, $password, $address, $lat, $long)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    public function wxlogin(){
        
        $this->required(array('code'));

        $code        = $this->input_post('code');
        $invite_code = $this->input_post('invite_code');
        $channel     = $this->input_post('channel');

        $address     = $this->input_post('address');
        $lat         = $this->input_post('lat');
        $long        = $this->input_post('long');
        
        if ($result = $this->_service->wxUserLogin($code, $invite_code, $channel, $address, $lat, $long)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    public function logout(){
        
        $user_id = $this->_getUserId();

        $this->_service->setUpdatedBy($user_id);
        
        if ($result = $this->_service->userLogout($user_id)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    public function forgotPasswordOtpSend(){
        
        $this->required(array('username'));
        
        $username = $this->input->post('username', TRUE);
        
        if ($result = $this->_service->forgotPwdSendOtp($username)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    public function forgotPasswordOtpVerify(){
        
        $this->required(array('username', 'otp_code', 'password'));
        
        $username = $this->input->post('username', TRUE);
        $otp_code = $this->input->post('otp_code', TRUE);
        $password = $this->input->post('password', TRUE);
        
        if ($result = $this->_service->forgotPwdVerifyOtp($username, $otp_code, $password)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    public function changePassword(){
        
        $user_id = $this->_getUserId();
        
        $this->required(array('old_password', 'new_password'));
        
        $old_password = $this->input->post('old_password', TRUE);
        $new_password = $this->input->post('new_password', TRUE);
        
        if ($result = $this->_service->changePwd($user_id, $old_password, $new_password)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    public function userInfo(){
        
        $user_id = $this->_getUserId();
        
        if ($result = $this->_service->getUserInfo($user_id)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    public function masterInfo(){
        
        $user_id = $this->_getUserId();
        
        if ($result = $this->_service->getMasterInfo($user_id)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
//    
//    /**
//     * 绑定师傅(用户端禁用)
//     * @return boolean
//     */
//    public function bindInvite(){
//        
//        $user_id = $this->_getUserId();
//        
//        $this->required(array("invite_code"));
//        
//        $invite_code = $this->input_post("invite_code");
//        
//        if($result = $this->_service->bindInviteCode($user_id, $invite_code)){
//            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
//            return true;
//        }
//
//        $this->_respondWithFailedCode($this->_service->getResponseCode());
//        return false;
//    }
//    
    public function bindMobileOtpSend(){
        
        $user_id = $this->_getUserId();
        
        $this->required(array('mobile'));
        
        $mobile = $this->input_post('mobile');
        
        if ($result = $this->_service->mobileBindOtp($user_id, $mobile)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    public function bindMobileOtpVerify(){
        
        $user_id = $this->_getUserId();
        
        $this->required(array('mobile', 'otp_code', 'password'));
        
        $mobile = $this->input_post('mobile');
        $otp_code = $this->input_post('otp_code');
        $password = $this->input_post('password');
        
        if ($result = $this->_service->mobileBindVerify($user_id, $mobile, $otp_code, $password)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    public function mobileQuickBind(){
        
        $user_id = $this->_getUserId();
        $this->required(array('token'));
        $token = $this->input_post('token');
        
        if ($result = $this->_service->mobileQuickBind($user_id, $token)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    public function bindWeiXin(){
        
        $user_id = $this->_getUserId();
        
        $this->required(array('code'));
        
        $code = $this->input_post('code');
        
        if ($result = $this->_service->weixinBind($user_id, $code)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    /**
     * App分享链接
     * @return boolean
     */
    public function appShareLink(){
        
        $user_id = $this->_getUserId();
        
        $type = $this->input_get('type');
        if(empty($type)){
            $share_type = 'wx';
        }
        else if(strval($type) === "1"){
            $share_type = "wx";
        }
        else if(strval($type) === "2"){
            $share_type = "pyq";
        }
        else if(strval($type) === "3"){
            $share_type = "link";
        }
        
        if($result = $this->_service->getAppShareLink($user_id, $share_type)){
            
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    public function appShareBack(){
        
    }
    
//    public function mobileOtpSend(){
//        
//        $user_id = $this->_getUserId();
//        
//        $this->required(array('mobile'));
//        
//        $mobile = $this->input_post('mobile');
//        
//        if ($result = $this->_service->mobileBindOtp($user_id, $mobile)) {
//            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
//            return true;
//        }
//
//        $this->_respondWithFailedCode($this->_service->getResponseCode());
//        return false;        
//    }
//    
//    public function mobileOtpVerify(){
//        
//        $user_id = $this->_getUserId();
//        
//        $this->required(array('mobile', 'otp_code', 'password'));
//        
//        $mobile = $this->input_post('mobile');
//        $otp_code = $this->input_post('otp_code');
//        $password = $this->input_post('password');
//        
//        if ($result = $this->_service->mobileBindVerify($user_id, $mobile, $otp_code, $password)) {
//            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
//            return true;
//        }
//
//        $this->_respondWithFailedCode($this->_service->getResponseCode());
//        return false;
//    }
}
