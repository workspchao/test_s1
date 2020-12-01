<?php

namespace AccountService\Account;

use AccountService\Common\MessageCode;
use AccountService\LoginAccount\LoginAccountLoginType;
use AccountService\LoginLog\LoginLogStatus;
use AccountService\LoginLog\LoginLogType;
use AccountService\LoginLog\LoginLog;
use AccountService\UserProfile\UserProfile;
use AccountService\LoginAccount\LoginAccount;
use AccountService\Fun\FunType;
use AccountService\IncrementTable\IncrementIDAttribute;
use Common\ValueObject\PasswordObj;
use Common\Validator\MobileNumberValidator;
use AccountService\Otp\OtpType;

class GuestAccountService extends AccountService {

    protected static $_instance = NULL;

    function __construct() {
        
    }

    public static function build() {
        if (self::$_instance == NULL) {
            self::$_instance = new GuestAccountService();
        }
        return self::$_instance;
    }
    
    public function gusetSendOtp($username, $login_type = LoginAccountLoginType::MOBILE){
        
        $this->log_benchmark("otpSend_start", "gusetSendOtp_start", "gusetSendOtp", "gusetSendOtp start");
        
        $otpType = null;
        if($login_type == LoginAccountLoginType::MOBILE){
            $regex = '^1(3|4|5|6|7|8|9)[0-9]\d{8}$^';
            $v = MobileNumberValidator::make($username, $regex);
            if($v->fails()){
                
                $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
                log_message("error", "gusetSendOtp fail -> mobile number invalid. $tmpLogEntity");

                $this->setResponseCode(MessageCode::CODE_INVALID_MOBILE_NUMBER);
                return false;
            }
            $otpType = OtpType::SMS;
        }
        else if($login_type == LoginAccountLoginType::EMAIL){
            $otpType = OtpType::EMAIL;
        }
        
        //check black list
        if (!$this->_checkBlackList($this->getIpAddress())) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "gusetSendOtp fail -> user has been blacklisted (ipaddress). $tmpLogEntity");
            
            //$this->setResponseCode(MessageCode::CODE_REGISTER_OTP_SEND);
            return false;
        }
        
        
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        $serviceUserProfile = $this->_getServiceUserProfile();
        $serviceOtp = $this->_getServiceOtp();
        
        //check login account if exists
        if ($entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type)) {
            $user_id = $entityLoginAccount->getUserId();
            if($entityUserProfile = $serviceUserProfile->getUserProfile($user_id)){
                if($entityUserProfile->getStatus() == UserStatus::UNVERIFIED){
                    
                    $otpResult = false;
                    //check otp code
                    if($login_type == LoginAccountLoginType::MOBILE){
                        $otpResult = $this->sendOtpCode(OtpType::SMS, $username, $user_id);
                    }
                    else if($login_type == LoginAccountLoginType::EMAIL){
                        $otpResult = $this->sendOtpCode(OtpType::EMAIL, $username, $user_id);
                    }
                    
                    if($otpResult){

                        $this->log_benchmark("gusetSendOtp_start", "gusetSendOtp_end", "gusetSendOtp", "gusetSendOtp end");
                        
                        $this->setResponseCode(MessageCode::CODE_OTP_HAS_BEEN_SENT);
                        return $otpResult;
                    }
                }
            }
            
            //login log?
            $this->_addLoginLog(LoginLogStatus::FAILED, LoginLogType::LOGIN);

            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "gusetSendOtp fail -> user login account already exists. $tmpLogEntity");

            $this->log_benchmark("gusetSendOtp_start", "gusetSendOtp_end", "gusetSendOtp", "gusetSendOtp end");

            $this->setResponseCode(MessageCode::CODE_USER_ALREADY_EXISTS);
            return false;
        }
        
        //else user not exists, just send otp to destination
        if(!$otpResult = $this->sendGuestOtpCode($otpType, $username)){
            
            $this->log_benchmark("gusetSendOtp_start", "gusetSendOtp_end", "gusetSendOtp", "gusetSendOtp end");

            return false;
        }
        
        $this->log_benchmark("gusetSendOtp_start", "gusetSendOtp_end", "gusetSendOtp", "gusetSendOtp end");

        $this->setResponseCode(MessageCode::CODE_OTP_HAS_BEEN_SENT);
        return $otpResult;
    }

    public function gusetVerifyOtp($username, $login_type = LoginAccountLoginType::MOBILE, $otp_code, $password, $invite_code = null){
        
        $otpType = null;
        if($login_type == LoginAccountLoginType::MOBILE){
            $regex = '^1(3|4|5|6|7|8|9)[0-9]\d{8}$^';
            $v = MobileNumberValidator::make($username, $regex);
            if($v->fails()){
                
                $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
                log_message("error", "gusetVerifyOtp fail -> mobile number invalid. $tmpLogEntity");

                $this->setResponseCode(MessageCode::CODE_INVALID_MOBILE_NUMBER);
                return false;
            }
            $otpType = OtpType::SMS;
        }
        else if($login_type == LoginAccountLoginType::EMAIL){
            $otpType = OtpType::EMAIL;
        }
        
        //check black list
        if (!$this->_checkBlackList($this->getIpAddress())) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "gusetVerifyOtp fail -> user has been blacklisted (ipaddress). $tmpLogEntity");
            
            //$this->setResponseCode(MessageCode::CODE_REGISTER_OTP_SEND);
            return false;
        }
        
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        $serviceUserProfile = $this->_getServiceUserProfile();
        $serviceOtp = $this->_getServiceOtp();
        $serviceUserInvite = $this->_getServiceUserInvite();
        
        if(!$serviceOtp->verifyOtp($otpType, $username, null, $otp_code)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "otp_type" => $otpType, "otp_code" => $otp_code));
            log_message("error", "gusetVerifyOtp fail -> otp code can not verify. $tmpLogEntity");

            $this->setResponseCode($serviceOtp->getResponseCode());
            return false;
        }
        
        //check login account if exists
        if ($entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type)) {
            $user_id = $entityLoginAccount->getUserId();
            if($entityUserProfile = $serviceUserProfile->getUserProfile($user_id)){
                if($entityUserProfile->getStatus() == UserStatus::UNVERIFIED){
                    //update user status
                    if(!$serviceUserProfile->updateUserStatus($entityUserProfile->getId(), UserStatus::VERIFIED)){
                        
                        $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
                        log_message("error", "gusetVerifyOtp fail -> update user status fail. $tmpLogEntity");
                        return false;
                    }
                }
            }
            
            //login log?
            $this->_addLoginLog(LoginLogStatus::FAILED, LoginLogType::LOGIN);

            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "gusetVerifyOtp fail -> user login account already exists. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_USER_ALREADY_EXISTS);
            return false;
        }
        
        //else user not exists, create user info
        //$role, $user_type, $username, $login_type, $status, $password = null, $name = null, $nickname = null, $avatar_url = null, $app_id = null
        list($entityLoginAccount, $entityUserProfile, $token) = $this->createUser(UserType::APPUSER, $username, $login_type, 
                UserStatus::VERIFIED, $password);
        
        if(!$entityLoginAccount){
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "gusetVerifyOtp fail -> createUser fail. $tmpLogEntity");
            return false;
        }
        
        $user_id = $entityLoginAccount->getUserId();
        
        //创建邀请关系
        $entityUserInvite_MySelf = $serviceUserInvite->createUserInvite($user_id, $invite_code);
        
        $this->_addLoginLog(LoginLogStatus::SUCCESS, LoginLogType::LOGIN, $entityLoginAccount);

        if ($entityAccessToken = $this->_proceedToLogin($entityLoginAccount, UserType::APPUSER)) {

            //$userInfo = $entityUserProfile->getSelectedField(array('name', 'nick_name', 'avatar_url', 'accountID', 'user_status', ''));
            
            $userInfo = $this->_getLoginUserInfo($user_id, $entityUserProfile, $entityUserInvite_MySelf, true);
            $tokenInfo = $entityAccessToken->getSelectedField(array('token', 'expired_at'));
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
            return array('user' => $userInfo, 'access' => $tokenInfo);
        }
        
        $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
        return false;
    }

    public function mobileQuickLogin($mobileAuthtoken, $invite_code = null){
        
        $loginType = LoginAccountLoginType::MOBILE;

        //根据token从阿里云获取手机号
        $servAliMobileAuth = $this->_getServiceAliMobileAuth();

        if(!$authRes = $servAliMobileAuth->mobileAuth($mobileAuthtoken)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        if($authRes['Code'] != 'OK'){
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }
        
        $mobile = $authRes['GetMobileResultDTO']['Mobile'];
        if(empty($mobile)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        
        $serviceLoginAccount  = $this->_getServiceLoginAccount();
        $serviceUserProfile   = $this->_getServiceUserProfile();
        $serviceUserInvite    = $this->_getServiceUserInvite();
        
        

        $entityUserInvite_MySelf = NULL;
        $entityUserProfile       = NULL;
        if (!$entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($mobile, $loginType)) {
            //不存在，则创建新用户
            list($entityLoginAccount, $entityUserProfile, $token) = $this->createUser(UserType::APPUSER, $mobile, $loginType, 
                UserStatus::VERIFIED, NULL);
        
            if(!$entityLoginAccount){
                $tmpLogEntity = json_encode(array("username" => $mobile, "login_type" => $loginType));
                log_message("error", "mobileQuickLogin fail -> createUser fail. $tmpLogEntity");
                return false;
            }

            $userId = $entityLoginAccount->getUserId();
            
            //创建邀请关系
            $entityUserInvite_MySelf = $serviceUserInvite->createUserInvite($userId, $invite_code);
        }

        $userId = $entityLoginAccount->getUserId();
        $this->_addLoginLog(LoginLogStatus::SUCCESS, LoginLogType::LOGIN, $entityLoginAccount);
        if ($entityAccessToken = $this->_proceedToLogin($entityLoginAccount, UserType::APPUSER)) {

            $userInfo = $this->_getLoginUserInfo($userId, $entityUserProfile, $entityUserInvite_MySelf, true);
            $tokenInfo = $entityAccessToken->getSelectedField(array('token', 'expired_at'));
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
            return array('user' => $userInfo, 'access' => $tokenInfo);
        }
        
        $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
        return false;
    }
}
