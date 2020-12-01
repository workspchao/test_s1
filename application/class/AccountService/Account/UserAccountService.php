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
use AccountService\Otp\OtpType;
use Common\Validator\MobileNumberValidator;
use Common\Helper\StringMasker;
use Common\Microservice\WeixinService\WeixinMobileService;
use AccountService\Wxconfig\Wxconfig;
use AccountService\Wxconfig\WxconfigType;
use Common\Core\BaseDateTime;
use Common\Helper\RandomCodeGenerator;
use AccountService\NewsDomainPool\NewsDomainPoolType;
use AccountService\NewsDomainPool\NewsDomainPool;
use AccountService\CoreConfigData\CoreConfigType;
use Common\Helper\AES128Encryption;
use AccountService\UserCashoutMode\UserCashoutModeType;
use AccountService\PlatformStatics\PlatformStatics;
use AccountService\PlatformDailyStatics\PlatformDailyStatics;
use AccountService\PlatformHourStatics\PlatformHourStatics;
use AccountService\PasswordPolicy\AdminPasswordPolicyFactory;

class UserAccountService extends AccountService {

    protected static $_instance = NULL;

    function __construct() {
        
    }

    public static function build() {
        if (self::$_instance == NULL) {
            self::$_instance = new UserAccountService();
        }
        return self::$_instance;
    }
    
    public function userLogin($username, $password, $address = NULL, $lat = NULL, $long = NULL) {

        $login_type = LoginAccountLoginType::MOBILE;

        //check black list
        if (!$this->_checkBlackList($this->getIpAddress())) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "user has been blacklisted (ipaddress). $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }
        
        //check login account if exists
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        if (!$entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type)) {

            //login log?
            $this->_addLoginLog(LoginLogStatus::FAILED, LoginLogType::LOGIN);
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "user login account not found. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        $user_id = $entityLoginAccount->getUserId();

        $this->setUpdatedBy($user_id);

        //this screen can only be done after user as been identified by userID
        if (!$this->_checkBlackList(NULL, $user_id)) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "user has been blacklisted (user_id). $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        $filter = new UserProfile();
        $filter->setId($user_id);

        $serviceUserProfile = $this->_getServiceUserProfile();
        if (!$collection = $serviceUserProfile->selectUserProfile($filter)) {
            
            $tmpLogEntity = json_encode(array("id" => $user_id, "username" => $username, "login_type" => $login_type));
            log_message("error", "user profile not found. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        $entityUserProfile = $collection->result->current();

        //
        if($entityUserProfile->getUserType() != UserType::APPUSER){
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        //检查用户状态是否正常
        if($entityUserProfile->getStatus() != UserStatus::VERIFIED && $entityUserProfile->getStatus() != UserStatus::ANOMALOUS && $entityUserProfile->getStatus() != UserStatus::HIGHRISK){
            $this->setResponseCode(MessageCode::CODE_INVALID_USER_STATUS);
            return false;
        }



        if (!$entityLoginAccount->authenticate($password)) {
            
            $tmpLogEntity = json_encode(array("id" => $user_id, "username" => $username, "login_type" => $login_type));
            log_message("error", "user password is incorrect. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        $this->_addLoginLog(LoginLogStatus::SUCCESS, LoginLogType::LOGIN, $entityLoginAccount, $address, $lat, $long);

        if ($entityAccessToken = $this->_proceedToLogin($entityLoginAccount, UserType::APPUSER)) {

//            $userInfo = $entityUserProfile->getSelectedField(array('name', 'user_type', 'accountID', 'user_status'));
//            $tokenInfo = $entityAccessToken->getSelectedField(array('session_type', 'access_type', 'token', 'expired_at'));
            $userInfo = $this->_getLoginUserInfo($user_id, $entityUserProfile);
            $tokenInfo = $entityAccessToken->getSelectedField(array('token', 'expired_at'));
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
            return array('user' => $userInfo, 'access' => $tokenInfo);
        }
        $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
        return false;
    }

    public function userLogout($user_profile_id, $login_type = null) {

        //check login account if exists

        $filterLoginAccount = new LoginAccount();
        $filterLoginAccount->setUserId($user_profile_id);
        if (!empty($login_type)) {
            $filterLoginAccount->setLoginType($login_type);
        }

        $serviceLoginAccount = $this->_getServiceLoginAccount();
        if (!$collectionLoginAccount = $serviceLoginAccount->selectLoginAccount($filterLoginAccount)) {
            
            $tmpLogEntity = json_encode(array("user_id" =>  $user_profile_id, "login_type" => $login_type));
            log_message("error", "adminLogout fail, login account not found. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        if (!empty($login_type)) {
            //logout one account
            $entityLoginAccount = $collectionLoginAccount->result->current();
            if (!$this->_logoutAccount($entityLoginAccount)) {
                
                $tmpLogEntity = json_encode($entityLoginAccount);
                log_message("error", "adminLogout fail, _logoutAccount fail. $tmpLogEntity");

                return false;
            }
            
            $this->_addLoginLog(LoginLogStatus::SUCCESS, LoginLogType::LOGOUT, $entityLoginAccount);

        }
        else {
            //logout all account
            foreach ($collectionLoginAccount->result as $entityLoginAccount) {
                if (!$this->_logoutAccount($entityLoginAccount)) {
                    return false;
                }
                
                $this->_addLoginLog(LoginLogStatus::SUCCESS, LoginLogType::LOGOUT, $entityLoginAccount);
            }
        }

        $this->setResponseCode(MessageCode::CODE_LOGOUT_SUCCESS);
        return true;
    }

    /**
     * 
     * @param type $username (code)
     * @param type $invite_code
     * @param type $login_type
     * @return boolean
     */
    public function wxUserLogin($username, $invite_code = null, $channel = null, $address = NULL, $lat = NULL, $long = NULL){

        $login_type = LoginAccountLoginType::WEIXIN;

        
        //check black list
        if (!$this->_checkBlackList($this->getIpAddress())) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "wxUserLogin - user has been blacklisted (ipaddress). $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }
        
        $serviceWxuser = $this->_getServiceWxuser();
        if(!$entityWxuser = $serviceWxuser->weixinAuthenticate($username, $login_type)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "wxUserLogin - weixinAuthenticate fail. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }
        
        //change username to openid
        $username = $entityWxuser->getOpenId();
        $nickname = $entityWxuser->getNickname();
        $app_id = $entityWxuser->getAppId();
        $avatar_url = $entityWxuser->getHeadimgurl();
        
        //check login account if exists
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        $serviceUserInvite = $this->_getServiceUserInvite();
        
        $isNewsUser = false;
        
        //用户账号不存在
        if (!$entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type, $app_id)) {
            
            log_message("error", "userAccountService IP => " . json_encode($this->getIpAddress()));
            
            //else user not exists, create user info
            //$role, $user_type, $username, $login_type, $status, $password = null, $name = null, $nickname = null, $avatar_url = null, $app_id = null
            list($entityLoginAccount, $entityUserProfile, $token) = $this->createUser(UserType::APPUSER, $username, $login_type, 
                    UserStatus::VERIFIED, null, null, $nickname, $avatar_url, $app_id);

            if(!$entityLoginAccount){
                $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
                log_message("error", "wxUserLogin fail -> createUser fail. $tmpLogEntity");
                return false;
            }
            
            $user_id = $entityLoginAccount->getUserId();
            
            //绑定微信提现账号
            $serviceUserCashoutMode = $this->_getServiceUserCashoutMode();
            if(!$serviceUserCashoutMode->createUserCashoutMode($user_id, $username, $nickname, UserCashoutModeType::WEIXIN)){
                
                $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id, "invite_code" => $invite_code));
                log_message("error", "wxUserLogin -> createUserCashoutMode fail. $tmpLogEntity");
            }
            
            $isNewsUser = true;
        }
        else{
            $filter = new UserProfile();
            $filter->setId($entityLoginAccount->getUserId());
            $serviceUserProfile = $this->_getServiceUserProfile();
            if (!$collection = $serviceUserProfile->selectUserProfile($filter)) {

                $tmpLogEntity = json_encode(array("id" => $user_id, "username" => $username, "login_type" => $login_type));
                log_message("error", "user profile not found. $tmpLogEntity");

                $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
                return false;
            }

            $entityUserProfile = $collection->result->current();

            //检查用户状态是否正常
            if($entityUserProfile->getStatus() != UserStatus::VERIFIED && $entityUserProfile->getStatus() != UserStatus::ANOMALOUS && $entityUserProfile->getStatus() != UserStatus::HIGHRISK){
                $this->setResponseCode(MessageCode::CODE_INVALID_USER_STATUS);
                return false;
            }
        }

        $user_id = $entityLoginAccount->getUserId();

        $this->setUpdatedBy($user_id);
        
        //this screen can only be done after user as been identified by userID
        if (!$this->_checkBlackList(NULL, $user_id)) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "user has been blacklisted (user_id). $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }
        
        $entityUserInvite_MySelf = NULL;
        if($isNewsUser){
            //创建邀请关系，首次收徒奖励
            if(!$entityUserInvite_MySelf = $serviceUserInvite->createUserInvite($user_id, $invite_code)){
                $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id, "invite_code" => $invite_code));
                log_message("error", "wxUserLogin -> createUserInvite fail. $tmpLogEntity");
            }
            else{
                $entityUserInvite_MySelf instanceof \AccountService\UserInvite\UserInvite;
            }
        }
        
        $this->_addLoginLog(LoginLogStatus::SUCCESS, LoginLogType::LOGIN, $entityLoginAccount, $address, $lat, $long);

        if(!empty($channel)){
            $channel = str_replace("channel@", "", $channel);
        }

        if ($entityAccessToken = $this->_proceedToLogin($entityLoginAccount, UserType::APPUSER, $channel)) {

            $userInfo = $this->_getLoginUserInfo($user_id, $entityUserProfile, $entityUserInvite_MySelf, true);
            $tokenInfo = $entityAccessToken->getSelectedField(array('token', 'expired_at'));

            $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
            return array('user' => $userInfo, 'access' => $tokenInfo);
        }
        $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
        return false;
    }
    
    public function forgotPwdSendOtp($username, $login_type = LoginAccountLoginType::MOBILE){
        
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
        if (!$entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type)) {
            
            //login log?
            $this->_addLoginLog(LoginLogStatus::FAILED, LoginLogType::FORGET_PASSWORD);

            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "forgotPwdSenOtp fail -> user login account not found. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }
        
        $user_id = $entityLoginAccount->getUserId();
        if(!$entityUserProfile = $serviceUserProfile->getUserProfile($user_id)){
            
            //login log?
            $this->_addLoginLog(LoginLogStatus::FAILED, LoginLogType::FORGET_PASSWORD);

            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "forgotPwdSenOtp fail -> user profile not found. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }
        
        if($entityUserProfile->getStatus() != UserStatus::VERIFIED && $entityUserProfile->getStatus() != UserStatus::ANOMALOUS && $entityUserProfile->getStatus() != UserStatus::HIGHRISK){

            //login log?
            $this->_addLoginLog(LoginLogStatus::FAILED, LoginLogType::FORGET_PASSWORD);

            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "forgotPwdSenOtp fail -> user not verify. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_INVALID_USER_STATUS);
            return false;
        }
        
        $otpResult = false;
        //check otp code
        if($login_type == LoginAccountLoginType::MOBILE){
            $otpResult = $this->sendOtpCode(OtpType::SMS, $username, $user_id);
        }
        else if($login_type == LoginAccountLoginType::EMAIL){
            $otpResult = $this->sendOtpCode(OtpType::EMAIL, $username, $user_id);
        }

        if(!$otpResult){
            
            //login log?
            $this->_addLoginLog(LoginLogStatus::FAILED, LoginLogType::FORGET_PASSWORD);

            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "forgotPwdSenOtp fail -> otp send fail. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }
        
        $this->_addLoginLog(LoginLogStatus::SUCCESS, LoginLogType::FORGET_PASSWORD, $entityLoginAccount);

        $this->setResponseCode(MessageCode::CODE_OTP_HAS_BEEN_SENT);
        return $otpResult;
    }

    public function forgotPwdVerifyOtp($username, $otp_code, $password, $login_type = LoginAccountLoginType::MOBILE){
        
        $otpType = null;
        if($login_type == LoginAccountLoginType::MOBILE){
            $regex = '^1(3|4|5|6|7|8|9)[0-9]\d{8}$^';
            $v = MobileNumberValidator::make($username, $regex);
            if($v->fails()){
                
                $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
                log_message("error", "forgotPwdVerifyOtp fail -> mobile number invalid. $tmpLogEntity");

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
            log_message("error", "forgotPwdVerifyOtp fail -> user has been blacklisted (ipaddress). $tmpLogEntity");
            
            //$this->setResponseCode(MessageCode::CODE_REGISTER_OTP_SEND);
            return false;
        }
        
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        $serviceUserProfile = $this->_getServiceUserProfile();
        $serviceOtp = $this->_getServiceOtp();
        $serviceUserInvite = $this->_getServiceUserInvite();
        
        if(!$serviceOtp->verifyOtp($otpType, $username, null, $otp_code)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "otp_type" => $otpType, "otp_code" => $otp_code));
            log_message("error", "forgotPwdVerifyOtp fail -> otp code can not verify. $tmpLogEntity");

            $this->setResponseCode($serviceOtp->getResponseCode());
            return false;
        }
        
        //check login account if exists
        if (!$entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type)) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "forgotPwdVerifyOtp fail -> user login account not found. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }
        
        $user_id = $entityLoginAccount->getUserId();
        if(!$entityUserProfile = $serviceUserProfile->getUserProfile($user_id)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "forgotPwdVerifyOtp fail -> user profile not found. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }
        
        if($entityUserProfile->getStatus() != UserStatus::VERIFIED && $entityUserProfile->getStatus() != UserStatus::ANOMALOUS && $entityUserProfile->getStatus() != UserStatus::HIGHRISK){

            //login log?
            $this->_addLoginLog(LoginLogStatus::FAILED, LoginLogType::FORGET_PASSWORD);

            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "forgotPwdVerifyOtp fail -> user not verify. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_INVALID_USER_STATUS);
            return false;
        }
        
        //change password
        $passwordObj = new PasswordObj();
        $passwordObj->setNewPassword($password);

        $entityLoginAccount->setPassword($passwordObj);
        
        if(!$serviceLoginAccount->updateLoginAccount($entityLoginAccount)){
            
            //login log?
            $this->_addLoginLog(LoginLogStatus::FAILED, LoginLogType::FORGET_PASSWORD);

            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "forgotPwdVerifyOtp fail -> update login account fail. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }
        
        $this->_addLoginLog(LoginLogStatus::SUCCESS, LoginLogType::FORGET_PASSWORD, $entityLoginAccount);

        if ($entityAccessToken = $this->_proceedToLogin($entityLoginAccount, UserType::APPUSER)) {

            $userInfo = $this->_getLoginUserInfo($user_id, $entityUserProfile);
            $tokenInfo = $entityAccessToken->getSelectedField(array('session_type', 'access_type', 'token', 'expired_at'));
            
            $this->setResponseCode(MessageCode::CODE_PASSWORD_UPDATE_SUCCESS);
            return array('user' => $userInfo, 'access' => $tokenInfo);
        }
        
        $this->setResponseCode(MessageCode::CODE_PASSWORD_UPDATE_FAIL);
        return false;
    }

    
    public function changePwd($user_id, $old_password, $new_password, $login_type = LoginAccountLoginType::MOBILE){
        
        //check black list
        if (!$this->_checkBlackList($this->getIpAddress())) {
            
            $tmpLogEntity = json_encode(array("$user_id" => $user_id, "login_type" => $login_type));
            log_message("error", "changePwd fail -> user has been blacklisted (ipaddress). $tmpLogEntity");
            
            //$this->setResponseCode(MessageCode::CODE_REGISTER_OTP_SEND);
            return false;
        }
        
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        $serviceUserProfile = $this->_getServiceUserProfile();
        $serviceOtp = $this->_getServiceOtp();
        $serviceUserInvite = $this->_getServiceUserInvite();
        
//        if(!$serviceOtp->verifyOtp($otpType, $username, null, $otp_code)){
//            
//            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "otp_type" => $otpType, "otp_code" => $otp_code));
//            log_message("error", "forgotPwdVerifyOtp fail -> otp code can not verify. $tmpLogEntity");
//
//            $this->setResponseCode($serviceOtp->getResponseCode());
//            return false;
//        }
        
        
        //check login account if exists
        if (!$entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserId($user_id, $login_type)) {

            $tmpLogEntity = json_encode(array("username" => $user_ids, "login_type" => $login_type));
            log_message("error", "changePwd fail -> user login account not found. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }
        
        if(!$entityLoginAccount->authenticate($old_password)){
            
            $tmpLogEntity = json_encode(array("username" => $user_id, "login_type" => $login_type));
            log_message("error", "changePwd fail -> user login account not found. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }
        
        
        //check login account if exists
        if (!$collectionLoginAccount = $serviceLoginAccount->selectLoginAccountByUserId($user_id)) {
            
            $tmpLogEntity = json_encode(array("user_id" => $user_id, "login_type" => $login_type));
            log_message("error", "changePwd fail -> user login account not found. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_PASSWORD_UPDATE_FAIL);
            return false;
        }
        
        if(!$entityUserProfile = $serviceUserProfile->getUserProfile($user_id)){
            
            $tmpLogEntity = json_encode(array("user_id" => $user_id, "login_type" => $login_type));
            log_message("error", "changePwd fail -> user profile not found. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_PASSWORD_UPDATE_FAIL);
            return false;
        }
        
        if($entityUserProfile->getStatus() != UserStatus::VERIFIED && $entityUserProfile->getStatus() != UserStatus::ANOMALOUS && $entityUserProfile->getStatus() != UserStatus::HIGHRISK){

            $tmpLogEntity = json_encode(array("user_id" => $user_id, "login_type" => $login_type));
            log_message("error", "changePwd fail -> user not verify. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_INVALID_USER_STATUS);
            return false;
        }
        
        $serviceLoginAccount->startDBTransaction();
        
        //change password
        $passwordObj = new PasswordObj();
        $passwordObj->setNewPassword($new_password);

        foreach ($collectionLoginAccount->result as $entityLoginAccount) {
            $entityLoginAccount->setPassword($passwordObj);
        
            if(!$serviceLoginAccount->updateLoginAccount($entityLoginAccount)){

                $serviceLoginAccount->rollbackDBTransaction();
                
                $tmpLogEntity = json_encode(array("user_id" => $user_id, "login_type" => $login_type));
                log_message("error", "changePwd fail -> update login account fail. $tmpLogEntity");

                $this->setResponseCode(MessageCode::CODE_PASSWORD_UPDATE_FAIL);
                return false;
            }
        }
        
        $serviceLoginAccount->completeDBTransaction();
        
        $this->setResponseCode(MessageCode::CODE_PASSWORD_UPDATE_SUCCESS);
        return true;
    }

    public function getUserInfo($user_id){
        
        $filter = new UserProfile();
        $filter->setId($user_id);

        $serviceUserProfile = $this->_getServiceUserProfile();
        if (!$collection = $serviceUserProfile->selectUserProfile($filter)) {
            
            $tmpLogEntity = json_encode(array("id" => $user_id));
            log_message("error", "getUserInfo - user profile not found. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        $entityUserProfile = $collection->result->current();

        if($userInfo = $this->_getLoginUserInfo($user_id, $entityUserProfile)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
            return $userInfo;
        }
        $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
        return false;
    }
    
    public function getMasterInfo($user_id){
        
        $serviceUserProfile = $this->_getServiceUserProfile();
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        $serviceUserInvite = $this->_getServiceUserInvite();
        
        if(!$entityUserInvite = $serviceUserInvite->getUserInviteByUserId($user_id)){
            
            log_message("error", "");
            $this->setResponseCode(MessageCode::CODE_USER_MASTER_NOT_BIND);
            return false;
        }
        
        $invite_user_id = $entityUserInvite->getParent1();
        if(!$entityUserInviteParent1 = $serviceUserInvite->getUserInviteByUserId($invite_user_id)){
            
            $this->setResponseCode(MessageCode::CODE_USER_INVITE_NOT_FOUND);
            return false;
        }

        
        if($userInfo = $this->_getLoginUserInfo($invite_user_id)){
            
            $mobile = isset($userInfo["mobile"]) ? $userInfo["mobile"] : null;
            
            if($mobile){
                $mobile = StringMasker::mask($mobile, 'both', 4, 3, "*", true);
            }
            
            $userInfo["mobile"] = $mobile;
            $userInfo["openid"] = null;
            $userInfo['refer_code'] = null;
            $userInfo['id'] = $invite_user_id;

            
            unset($userInfo['openid']);
            unset($userInfo['refer_code']);
            
            $this->setResponseCode(MessageCode::CODE_USER_INVITE_GET_SUCCESS);
            return $userInfo;
        }
        
        $this->setResponseCode(MessageCode::CODE_USER_INVITE_NOT_FOUND);
        return false;
    }
    
    // /**
    //  * 绑定邀请码
    //  * @param type $user_id
    //  * @param type $invite_code
    //  * @return boolean
    //  */
    // public function bindInviteCode($user_id, $invite_code){
        
    //     $serviceUserInvite = $this->_getServiceUserInvite();
        
    //     $entityUserRelation = null;
    //     if(!$invite_code){
            
    //         $tmpLogEntity = json_encode(array("user_id" => $user_id, "invite_code" => $invite_code));
    //         log_message("error", "bindInviteCode fail -> invite invite code. $tmpLogEntity");
            
    //         $this->setResponseCode(MessageCode::CODE_INVALID_INVITE_CODE);
    //         return false;
    //     }
        
    //     //绑定用户关系
    //     if(!$entityUserRelation = $serviceUserInvite->createUserInvite($user_id, $invite_code)){
            
    //         $tmpLogEntity = json_encode(array("user_id" => $user_id, "invite_code" => $invite_code));
    //         log_message("error", "bindInviteCode fail -> createUserInvite fail. $tmpLogEntity");
            
    //         $this->setResponseCode($serviceUserInvite->getResponseCode());
    //         return false;
    //     }

    //     if(empty($entityUserRelation->getParent1())){
    //         $this->setResponseCode(MessageCode::CODE_INVALID_INVITE_CODE);
    //         return false;
    //     }
        
    //     $this->setResponseCode($serviceUserInvite->getResponseCode());
    //     return $entityUserRelation;
    // }
    
    /**
     * 1. check mobile number (是否被占用)
     * 2. 
     * @param type $user_id
     * @param type $mobile
     */
    public function mobileBindOtp($user_id, $mobile){
        
        $login_type = LoginAccountLoginType::MOBILE;
        $username = $mobile;
        
        //check black list
        if (!$this->_checkBlackList($this->getIpAddress())) {
            
            $tmpLogEntity = json_encode(array("$user_id" => $user_id, "login_type" => $login_type));
            log_message("error", "mobileBindOtp fail -> user has been blacklisted (ipaddress). $tmpLogEntity");
            
            //$this->setResponseCode(MessageCode::CODE_REGISTER_OTP_SEND);
            return false;
        }
        
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        $serviceUserProfile = $this->_getServiceUserProfile();
        $serviceOtp = $this->_getServiceOtp();
        
        //check login account if exists
        if ($entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type)) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "mobileBindOtp fail -> mobile number already exists. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_MOBILE_NUMBER_ALREADY_EXISTS);
            return false;
        }
        
        if($entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserId($user_id, $login_type))
        
        $user_id = $entityLoginAccount->getUserId();
        if(!$entityUserProfile = $serviceUserProfile->getUserProfile($user_id)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "mobileBindOtp fail -> user already bind mobile number. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_USER_MOBILE_NUMBER_ALREADY_EXISTS);
            return false;
        }
        
        $otpResult = false;
        //check otp code
        if($login_type == LoginAccountLoginType::MOBILE){
            $otpResult = $this->sendOtpCode(OtpType::SMS, $username, $user_id);
        }
        else if($login_type == LoginAccountLoginType::EMAIL){
            $otpResult = $this->sendOtpCode(OtpType::EMAIL, $username, $user_id);
        }

        if(!$otpResult){
        
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "mobileBindOtp fail -> otp send fail. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }
        
        $this->setResponseCode(MessageCode::CODE_OTP_HAS_BEEN_SENT);
        return $otpResult;
    }
    
    public function mobileBindVerify($user_id, $mobile, $otp_code, $password){
        
        $username = $mobile;
        $login_type = LoginAccountLoginType::MOBILE;
        
        $otpType = null;
        if($login_type == LoginAccountLoginType::MOBILE){
            $regex = '^1(3|4|5|6|7|8|9)[0-9]\d{8}$^';
            $v = MobileNumberValidator::make($username, $regex);
            if($v->fails()){
                
                $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
                log_message("error", "mobileBindVerify fail -> mobile number invalid. $tmpLogEntity");

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
            log_message("error", "mobileBindVerify fail -> user has been blacklisted (ipaddress). $tmpLogEntity");
            
            //$this->setResponseCode(MessageCode::CODE_REGISTER_OTP_SEND);
            return false;
        }
        
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        $serviceUserProfile = $this->_getServiceUserProfile();
        $serviceOtp = $this->_getServiceOtp();
        $serviceUserInvite = $this->_getServiceUserInvite();
        
        if(!$serviceOtp->verifyOtp($otpType, $username, $user_id, $otp_code)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "otp_type" => $otpType, "otp_code" => $otp_code));
            log_message("error", "mobileBindVerify fail -> otp code can not verify. $tmpLogEntity");

            $this->setResponseCode($serviceOtp->getResponseCode());
            return false;
        }
        
        //check login account if exists
        if ($entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type)) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "mobileBindVerify fail -> mobile number already exists. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_MOBILE_NUMBER_ALREADY_EXISTS);
            return false;
        }
        
        if(!$entityUserProfile = $serviceUserProfile->getUserProfile($user_id)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "mobileBindVerify fail -> user already bind mobile number. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_MOBILE_BIND_FAIL);
            return false;
        }
        
        //create login account for mobile
        
        //change password
        $passwordObj = new PasswordObj();
        $passwordObj->setNewPassword($password);
        
        $entityLoginAccount = new LoginAccount();
        $entityLoginAccount->setUserId($user_id);
        $entityLoginAccount->setLoginType($login_type);
        $entityLoginAccount->setUsername($username);
        $entityLoginAccount->setPassword($passwordObj);
        
        if(!$entityLoginAccount = $serviceLoginAccount->addLoginAccount($entityLoginAccount)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "mobileBindVerify fail -> update login account fail. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_MOBILE_BIND_FAIL);
            return false;
        }

        //update user profile mobile
        $entityUserProfile->setMobile($mobile);
        $serviceUserProfile->updateUserProfile($entityUserProfile);

        if ($entityAccessToken = $this->_proceedToLogin($entityLoginAccount, UserType::APPUSER)) {

            $userInfo = $this->_getLoginUserInfo($user_id, $entityUserProfile);
            $tokenInfo = $entityAccessToken->getSelectedField(array('session_type', 'access_type', 'token', 'expired_at'));
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
            return array('user' => $userInfo, 'access' => $tokenInfo);
        }
        
        $this->setResponseCode(MessageCode::CODE_MOBILE_BIND_FAIL);
        return false;
    }

    public function mobileQuickBind($user_id, $mobileAuthToken){

        //检查用户是否存在
        $serviceUserProfile  = $this->_getServiceUserProfile();
        if(!$entityUserProfile = $serviceUserProfile->getUserProfile($user_id)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "mobileQuickBind fail -> user not found: $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_MOBILE_BIND_FAIL);
            return false;
        }

        $login_type = LoginAccountLoginType::MOBILE;
        $serviceLoginAccount = $this->_getServiceLoginAccount();
            
        //检测用户是否已经绑定过手机号
        if($entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserId($user_id, $login_type)){
            $this->setResponseCode(MessageCode::CODE_MOBILE_BIND_FAIL);
            return false;   
        }

        //根据token从阿里云获取手机号
        $servAliMobileAuth = $this->_getServiceAliMobileAuth();

        if(!$authRes = $servAliMobileAuth->mobileAuth($mobileAuthToken)){
            $this->setResponseCode(MessageCode::CODE_MOBILE_BIND_FAIL);
            return false;
        }

        if($authRes['Code'] != 'OK'){
            $this->setResponseCode(MessageCode::CODE_MOBILE_BIND_FAIL);
            return false;
        }
        
        $mobile = $authRes['GetMobileResultDTO']['Mobile'];
        if(empty($mobile)){
            $this->setResponseCode(MessageCode::CODE_MOBILE_BIND_FAIL);
            return false;
        }

        //检查手机号是否被占用
        if ($entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($mobile, $login_type)) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "mobileQuickBind fail -> mobile number already exists. $tmpLogEntity");
            $this->setResponseCode(MessageCode::CODE_MOBILE_NUMBER_ALREADY_EXISTS);
            return false;
        }
        
        //create login account for mobile
        $passwordObj = new PasswordObj();
        $policy      = AdminPasswordPolicyFactory::build($user_id);
        $password    = $passwordObj->generatePassword($policy);
        
        $entityLoginAccount = new LoginAccount();
        $entityLoginAccount->setUserId($user_id);
        $entityLoginAccount->setLoginType($login_type);
        $entityLoginAccount->setUsername($mobile);
        $entityLoginAccount->setPassword($passwordObj);
        
        if(!$entityLoginAccount = $serviceLoginAccount->addLoginAccount($entityLoginAccount)){
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "mobileQuickBind fail -> add login account fail. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_MOBILE_BIND_FAIL);
            return false;
        }

        //update user profile mobile
        $entityUserProfile->setMobile($mobile);
        $serviceUserProfile->updateUserProfile($entityUserProfile);

        if ($entityAccessToken = $this->_proceedToLogin($entityLoginAccount, UserType::APPUSER)) {

            $userInfo = $this->_getLoginUserInfo($user_id, $entityUserProfile);
            $tokenInfo = $entityAccessToken->getSelectedField(array('session_type', 'access_type', 'token', 'expired_at'));
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
            return array('user' => $userInfo, 'access' => $tokenInfo);
        }
        
        $this->setResponseCode(MessageCode::CODE_MOBILE_BIND_FAIL);
        return false;
    }
    
    public function weixinBind($user_id, $code){
        
        $login_type = LoginAccountLoginType::WEIXIN;
        $username = $code;
        
        //check black list
        if (!$this->_checkBlackList($this->getIpAddress())) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "weixinBind - user has been blacklisted (ipaddress). $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }
        
        //微信授权，获取用户资料
        $serviceWxuser = $this->_getServiceWxuser();
        if(!$entityWxuser = $serviceWxuser->weixinAuthenticate($username, $login_type)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "weixinBind - weixinAuthenticate fail. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }
        
        //change username to openid
        $username = $entityWxuser->getOpenId();
        $nickname = $entityWxuser->getNickname();
        $app_id = $entityWxuser->getAppId();
        $avatar_url = $entityWxuser->getHeadimgurl();
        
        
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        $serviceUserProfile = $this->_getServiceUserProfile();
        $serviceOtp = $this->_getServiceOtp();
        $serviceUserInvite = $this->_getServiceUserInvite();
        
        //check login account if exists
        if ($entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type, $app_id)) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "weixinBind fail -> weixin login account already exists. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_WEIXIN_USER_ALREADY_EXISTS);
            return false;
        }
        
        //check weixin login acccount if exists
        if ($entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserId($user_id, $login_type)) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "weixinBind fail -> user weixin login account already exists. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_USER_WEIXIN_USER_ALREADY_EXISTS);
            return false;
        }

        if(!$entityUserProfile = $serviceUserProfile->getUserProfile($user_id)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "weixinBind fail -> user already bind mobile number. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_USER_MOBILE_NUMBER_ALREADY_EXISTS);
            return false;
        }
        
        //create login account for weixin
        
        //change password
        $password = RandomCodeGenerator::generate(8);
        
        $passwordObj = new PasswordObj();
        $passwordObj->setNewPassword($password);
        
        $entityLoginAccount = new LoginAccount();
        $entityLoginAccount->setUserId($user_id);
        $entityLoginAccount->setLoginType($login_type);
        $entityLoginAccount->setUsername($username);
        $entityLoginAccount->setPassword($passwordObj);
        $entityLoginAccount->setAppId($app_id);
        
        if(!$entityLoginAccount = $serviceLoginAccount->addLoginAccount($entityLoginAccount)){
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id));
            log_message("error", "weixinBind fail -> update login account fail. $tmpLogEntity");

            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }
        
        //update user profile
        $entityUserProfile->setNickName($nickname);
        $entityUserProfile->setAvatarUrl($avatar_url);
        
        if(!$serviceUserProfile->updateUserProfile($entityUserProfile)){
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id, "nickname" => $nickname, "avatar_url" => $avatar_url));
            log_message("error", "weixinBind -> update user info fail. $tmpLogEntity");
        }
        
        //绑定微信提现账号
        $serviceUserCashoutMode = $this->_getServiceUserCashoutMode();
        if(!$serviceUserCashoutMode->createUserCashoutMode($user_id, $username, $nickname, UserCashoutModeType::WEIXIN)){

            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type, "user_id" => $user_id, "invite_code" => $invite_code));
            log_message("error", "wxUserLogin -> createUserCashoutMode fail. $tmpLogEntity");
        }

        
        if ($entityAccessToken = $this->_proceedToLogin($entityLoginAccount, UserType::APPUSER)) {

            $userInfo = $this->_getLoginUserInfo($user_id, $entityUserProfile);
            $tokenInfo = $entityAccessToken->getSelectedField(array('session_type', 'access_type', 'token', 'expired_at'));
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
            return array('user' => $userInfo, 'access' => $tokenInfo);
        }
        
        $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
        return false;
    }
    
    
    public function getAppShareLink($user_id, $share_type = 'wx'){
        
        //get invite code
        $serviceUserInvite = $this->_getServiceUserInvite();
        if(!$entityUserInvite = $serviceUserInvite->getUserInviteByUserId($user_id)){
            log_message("error", "getAppShareLink fail  -> user invite not found.  $user_id");
            $this->setResponseCode(MessageCode::CODE_USER_INVITE_NOT_FOUND);
            return false;
        }
        
        $invite_code = $entityUserInvite->getInviteCode();
        $serviceUserInvite->startDBTransaction();
        
        //获取APP分享入口域名
        $servNewsDomainPool = $this->_getServiceNewsDomain();

        $tryTimes = 1;
        $tryLimit = 20;

        $entityNewsDomainPool = NULL;
        while($tryTimes <= $tryLimit) {

            $tryTimes++;
            if(!$entityNewsDomainPool = $servNewsDomainPool->getMinNewsDomainByDomainType(NewsDomainPoolType::NEWS_E)){
                log_message("error", "getAppShareLink fail - get domain fail");
                $serviceUserInvite->rollbackDBTransaction();
                $this->setResponseCode(MessageCode::CODE_NEWS_DOMAIN_POOL_NOT_FOUND);
                return false;
            }


            // $shortUrl = $entityNewsDomainPool->getShortUrl();
            // if(empty($shortUrl)){
            //     continue;
            // }

            $domain = $entityNewsDomainPool->getDomainValue();

            $isAvailable = $servNewsDomainPool->checkShortUrl($domain);

            if($isAvailable == FLAG_YES){
                break;
            }else{
                $entityNewsDomainPool = NULL;
            }
        }

        if(empty($entityNewsDomainPool)){
            log_message("error", "getAppShareLink fail - get domain fail");
            $serviceUserInvite->rollbackDBTransaction();
            $this->setResponseCode(MessageCode::CODE_NEWS_DOMAIN_POOL_NOT_FOUND);
            return false;
        }
        
        
        $domain_id          = $entityNewsDomainPool->getId();
        $oriEntityAppDomain = clone $entityNewsDomainPool;
        
        $logObj = new \stdClass();
        $logObj->user_id            = $user_id;
        $logObj->domain_id          = $entityNewsDomainPool->getId();
        $logObj->server_id          = $entityNewsDomainPool->getServerId();
        $logObj->domain_type        = $entityNewsDomainPool->getDomainType();
        $logObj->domain_value       = $entityNewsDomainPool->getDomainValue();
        $logObj->total_show_num     = $entityNewsDomainPool->getTotalShowNum();
        $logObj->today_show_num     = $entityNewsDomainPool->getTodayShowNum();
        $logObj->total_share_num    = $entityNewsDomainPool->getTotalShareNum();
        $logObj->today_share_num    = $entityNewsDomainPool->getTodayShareNum();
        $logObj->total_use_num      = $entityNewsDomainPool->getTotalUseNum();
        $logObj->today_use_num      = $entityNewsDomainPool->getTodayUseNum();
        
        $tmpLogEntity = json_encode($logObj);
        log_message("info", "getAppShareLink success -> $tmpLogEntity");

        $entityNewsDomainPool = new NewsDomainPool();
        $entityNewsDomainPool->setId($oriEntityAppDomain->getId());
        $entityNewsDomainPool->setLastShowAt(BaseDateTime::now());
        $entityNewsDomainPool->setTotalShowNum(1);
        $entityNewsDomainPool->setTodayShowNum(1);
        
        if(!$servNewsDomainPool->updateNewsDomainNum($entityNewsDomainPool, $oriEntityAppDomain)){
            $tmpLogEntity = json_encode(array("domain_id" => $entityNewsDomainPool->getId(), "total_show_num" => 1, "today_show_num" => 1));
            log_message("error", "getAppShareLink updateNum fail. $tmpLogEntity");
        }
        
        if(!$protocol = getenv("APP_SHARE_LINK_PROTOCOL")){
            $protocol = "http://";
        }
        
        $share_code = RandomCodeGenerator::generate(32);
//        if(!$path = getenv("NEWS_SHARE_LINK_PATH")){
//            $path = "?route=news&id=";
//        }
        
        $domain_name = $oriEntityAppDomain->getDomainValue();
        if(empty($domain_name)){
            
            $serviceUserInvite->rollbackDBTransaction();
            
            log_message("error", "getAppShareLink fail - domain value is empty. " . json_encode($entityNewsDomainPool));
            
            $this->setResponseCode(MessageCode::CODE_NEWS_DOMAIN_POOL_NOT_FOUND);
            return false;
        }

        $prefix = RandomCodeGenerator::generate(6, NULL, FLAG_NO);

        if(strpos($domain_name, "http") === false){
            $domain_name = $protocol . $prefix. ".".$domain_name;
        }

        $end_with = substr($domain_name, strlen($domain_name) - 1);
        if($end_with != "/"){
            $domain_name = $domain_name . "/";
        }

        $params = array('r' => 'a', 'd_id' => $domain_id, 'a' => 'enter', 'ref' => $invite_code);
        $path = http_build_query($params);
        $shareLink = $domain_name . "?" . $path;

        $aes128 = AES128Encryption::build();
        $encode_path = $aes128->encrypt($path);
        $encodeShareLink = $domain_name . $encode_path;

        $logObj = new \stdClass();
        $logObj->domain_id = $domain_id;
        $logObj->share_code = $share_code;
        $logObj->share_link = $shareLink;
        $logObj->encode_share_link = $encodeShareLink;

        
        $tmpLogEntity = json_encode($logObj);
        log_message("info", "getAppShareLink -> $tmpLogEntity");
        
        $serviceUserInvite->completeDBTransaction();
        
        $shareArray = array();
        $shareArray["id"] = null;
        $shareArray["share_id"] = null;
        $shareArray["title"] = null;
//        $newsArray['category_code'] = $category_code;
//        $newsArray['category_name'] = $category_name;
//        $newsArray['category_price'] = $category_read_price;
        //$newsArray['share_link'] = $shareLink;
        $shareArray['share_link'] = $shareLink;
        $shareArray['encode_share_link'] = $encodeShareLink;
        
        $shareArray['app_id'] = null;
        $shareArray['package_name'] = null;
        $shareArray['apps'] = array();
        
        //share app id
        $serviceWxconfig = $this->_getServiceWxconfig();
        if($entityWxconfig = $serviceWxconfig->getActiveApp(WxconfigType::WXSHARE)){
            $shareArray['app_id'] = $entityWxconfig->getAppId();
            $shareArray['package_name'] = $entityWxconfig->getPackageName();
        }
        $filter = new Wxconfig();
        $filter->setType(WxconfigType::WXSHARE);
        $filter->setIsAvailable(FLAG_YES);
        $filter->setIsActive(FLAG_YES);
        $filter->setIsSelf(FLAG_NO);
        if($collectionWxconfig = $serviceWxconfig->selectWxconfig($filter, null, 100, 1)){
            $shareArray['apps'] = $collectionWxconfig->result->getSelectedField(array('app_id', 'package_name'));
        }
        
        //IOS分享
        $serviceCoreConfigData = $this->_getServiceCoreConfigData();
        //ios_share_url =
        $ios_share_url = $serviceCoreConfigData->getConfig(CoreConfigType::IOS_SHARE_URL);
        
        $shareArray['ios_share_url'] = $ios_share_url;
        $random_array = $this->getRandomShareInfo($share_type);
        $shareArray = array_merge($shareArray, $random_array);
        
        ///////////////////////////////////////平台分享news次数记录///////////////////////////////////////
        $date = BaseDateTime::now()->getFormat('Y-m-d');
        $hour = BaseDateTime::now()->getFormat('H');
        $servDataStatics = $this->_getServiceDataStatics();

        //更新platform_statics
        $platformStaticsEntity = new PlatformStatics();
        $platformStaticsEntity->setAppShareNum(1);
        //更新platform_daily_statics
        $platformDailyStaticsEntity = new PlatformDailyStatics();
        $platformDailyStaticsEntity->setDate($date);
        $platformDailyStaticsEntity->setAppShareNum(1);
        //更新platform_hour_statics
        $platformHourStaticsEntity = new PlatformHourStatics();
        $platformHourStaticsEntity->setDate($date);
        $platformHourStaticsEntity->setHour($hour);
        $platformHourStaticsEntity->setAppShareNum(1);
        $servDataStatics->updatePlatformStaticsNum($platformStaticsEntity, $platformDailyStaticsEntity, $platformHourStaticsEntity);
        

        //update user_statics、 user_daily_statics
        $updateUserStaticsData = array(
            "app_share_num"     => 1
        );
        
        //update user_daily_statics
        //$servDataStatics->updateUserDailyStaticsNum($user_id, BaseDateTime::now()->getFormat('Y-m-d'), BaseDateTime::now()->getFormat('H'), $updateUserStaticsData);

        
        $this->setResponseCode(MessageCode::CODE_NEWS_SHARE_GENERATE_SUCCESS);
        return $shareArray;
    }
    
    private function getRandomShareInfo($type = 'wx'){
        
        $serviceCoreConfigData = $this->_getServiceCoreConfigData();        
        //logo(oss)
        $app_logo_url = $serviceCoreConfigData->getConfig(CoreConfigType::APP_LOGO_URL);
//        //标题文案
//        $app_share_title = $serviceCoreConfigData->getConfig(CoreConfigType::APP_SHARE_TITLE);
//        //描述文案
//        $app_share_desc = $serviceCoreConfigData->getConfig(CoreConfigType::APP_SHARE_DESC);
 
        
        $weixin = array();
        $pengyouquan = array();
        $lianjie = array();
        
        $logo = array();
        $logo[] = "https://commjqskj30.oss-cn-beijing.aliyuncs.com/public/images/share/z0.png";
        $logo[] = "https://commjqskj30.oss-cn-beijing.aliyuncs.com/public/images/share/z1.png";
        $logo[] = "https://commjqskj30.oss-cn-beijing.aliyuncs.com/public/images/share/z2.png";
        $logo[] = "https://commjqskj30.oss-cn-beijing.aliyuncs.com/public/images/share/z3.png";
        $logo[] = "https://commjqskj30.oss-cn-beijing.aliyuncs.com/public/images/share/z4.png";
        
        shuffle($logo);
        $idx = random_int(0, count($logo) - 1);
        
        $weixin_logo = $logo[$idx];
        
        //分享到微信好友的文案
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "趣快报发红包啦！现金到账可提现！真的有钱领~",
            "app_share_desc" => "我抢到了最大的红包！你也快来试试！",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "趣快报火爆上线，打开就有红包领，我已经提现啦！",
            "app_share_desc" => "我抢到了最大的红包！你也快来试试！",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "趣快报推广期疯狂撒钱，注册就有钱领，提现秒到账",
            "app_share_desc" => "天天领红包到手软，太不可思议！",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "您的好友在趣快报送您一个惊喜红包！",
            "app_share_desc" => "转发文章就能赚钱，每天只花几分钟~",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "刚刚提现的50元成功到账了！手机赚钱居然这么容易！",
            "app_share_desc" => "现金到账可立即提现→点击下载",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "这个平台太给力了！天天发红包，我已抢到119元，快来试试！",
            "app_share_desc" => "现金到账可立即提现→点击下载",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "趣快报APP~大家都在玩，0成本，每天玩玩手机轻松赚零花钱！",
            "app_share_desc" => "点击这里去领红包→",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "我已经在这领到了现金，完全免费没风险，靠谱100%！",
            "app_share_desc" => "天天领红包到手软，太不可思议！戳这→",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "给你推荐一个最近在玩的赚钱APP，我的话费都是从这里赚到的！",
            "app_share_desc" => "转发文章就能赚钱，每天只花几分钟~",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "您的第二份工资在这！现在注册领119.9元新人红包！",
            "app_share_desc" => "转发文章就能赚钱，每天只花几分钟~",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "本人亲测可信，转发文章轻松赚零花，月赚万元，赚不到钱找我！",
            "app_share_desc" => "转发文章就能赚钱，每天只花几分钟~",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "趣快报真心不错，边看文章边赚零花，微信提现秒到账，你也试试！",
            "app_share_desc" => "这儿的视频太有趣了，涨姿势还赚钱！",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "趣快报发来一个红包未领取，30分钟后失效！",
            "app_share_desc" => "最高200元大红包速来抢！",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "今日福利：119.9元新人红包，人人有份！",
            "app_share_desc" => "这儿的视频太有趣了，涨姿势还赚钱！",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "【财运到】送您惊喜！抽现金红包，快来领取！",
            "app_share_desc" => "最高200元大红包速来抢！",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "就帮我点一下，送您最高119.9元现金！",
            "app_share_desc" => "你想看的新闻，这里都有",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "恭喜发财！点击领取给你的零钱，最多能有119块！",
            "app_share_desc" => "转发文章就能赚钱，每天只花几分钟~",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "好友送您红包福利：119.9元大礼包，当天提现到账！",
            "app_share_desc" => "天天领红包到手软，太不可思议！戳这→",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "趣快报邀请大礼，等你来领，免费提现！",
            "app_share_desc" => "我抢到了最大的红包！你也快来试试！戳这→",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "帮我点下吧，咱俩都有钱。当天提现，秒到账>>",
            "app_share_desc" => "您有15位好友已经在趣快报赚了3892元！快来看看吧>>",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "我玩趣快报3天赚了200元，邀请您一起来赚钱",
            "app_share_desc" => "好消息！告诉你一个简单赚钱的APP",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "我玩趣快报每天赚100元，邀您一起来赚钱",
            "app_share_desc" => "您有32位好友玩趣快报已经累计赚6861元！快快点开下载！",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "0成本手机赚钱，我都提过现啦！快来试试>>",
            "app_share_desc" => "平台靠谱，我已经提现到账，是真现金！",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "0成本手机赚钱，1分钟赚1元，我已经提现是真的！",
            "app_share_desc" => "是真现金，提现秒到，即将过期！",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "0成本转发文章即可赚钱，每天分享几篇，超简单！",
            "app_share_desc" => "平台靠谱，涨分快，亲测可信，快来！有问题联系我！",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "我的零花钱，都来自这里，懂得来，不懂的算了",
            "app_share_desc" => "学生靠这个，不再向家里要生活费，每月还能存5千",
        );
        $weixin[] = array(
            "app_share_logo" => "$weixin_logo",
            "app_share_title" => "您有82位好友在趣快报赚到钱了！点击查看",
            "app_share_desc" => "快来和我一起吧，赚钱超快，偷偷推荐给你>>",
        );

        $pengyouquan_logo = $logo[$idx];
        //分享到微信朋友圈的文案
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "我在【趣快报】又赚了100元，你也来试试！",
            "app_share_desc" => "快来和我一起吧，赚钱超快，偷偷推荐给你>>",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "刚刚提现的50元成功到账了！手机赚钱居然这么容易！",
            "app_share_desc" => "快来和我一起吧，赚钱超快，偷偷推荐给你>>",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "还差一个，可以领119元~朋友们帮帮忙，快来领红包！",
            "app_share_desc" => "平台靠谱，涨分快，亲测可信，快来！有问题联系我！",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "趣快报发来一个红包未领取，30分钟后失效！",
            "app_share_desc" => "是真现金，提现秒到，即将过期！",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "【幸运红包】免费看资讯就可以领钱，我提了100元，是真的",
            "app_share_desc" => "天天领红包到手软，太不可思议！",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "【通知】10亿红包派发中…恭喜您获得新人红包一个！",
            "app_share_desc" => "是真现金，提现秒到，即将过期！",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "看资讯看新闻，天天都能免费涨知识，还能把钱也赚了！",
            "app_share_desc" => "快来和我一起吧，赚钱超快，偷偷推荐给你>>",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "【现金红包】大家都在这里抢红包！天天免费领红包！",
            "app_share_desc" => "天天领红包到手软，太不可思议！",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "在家兼职赚钱，这个软件最靠谱！",
            "app_share_desc" => "快来和我一起吧，赚钱超快，偷偷推荐给你>>",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "帮我点下吧，咱俩都有钱。当天提现，秒到账>>",
            "app_share_desc" => "快来和我一起吧，赚钱超快，偷偷推荐给你>>",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "我玩趣快报3天赚了200元，邀请您一起来赚钱",
            "app_share_desc" => "天天领红包到手软，太不可思议！",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "好友给您发来了用手机就能日入300的赚钱秘诀，点击领取119.9元，提现秒到账~",
            "app_share_desc" => "快来和我一起吧，赚钱超快，偷偷推荐给你>>",
        );
        $pengyouquan[] = array(
            "app_share_logo" => $pengyouquan_logo,
            "app_share_title" => "您有64位好友在趣快报赚零花钱",
            "app_share_desc" => "天天领红包到手软，太不可思议！",
        );
        
        $lianjie_logo = $logo[$idx];
        //分享到复制链接的文案
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "我玩趣快报3天赚了200元，邀请您一起来赚钱",
            "app_share_desc" => ""
        );
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "[微信红包]恭喜发财，大吉大利，快来领取我送你的红包。",
            "app_share_desc" => "↓↓点击这里拆开红包↓↓"
        );
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "帮我点一下，咱俩都有钱，当天提现！",
            "app_share_desc" => ""
        );
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "给咱们群的人发点小福利，今天可以提现哟~",
            "app_share_desc" => ""
        );
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "很久没有联系你，最近在干嘛呢？",
            "app_share_desc" => "我天天在《趣快报》赚钱，已经赚了500块了，你也来试试吧。
↓↓下载还送现金红包↓↓"
        );
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "零投入，手机转发文章赚钱，涨分超快！",
            "app_share_desc" => ""
        );
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "趣快报-大家都在玩，0成本，每天玩玩手机轻松赚零花钱！",
            "app_share_desc" => ""
        );
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "趣快报-亲测可信，分享文章轻松赚零花钱，月入万元，赚不到钱找我",
            "app_share_desc" => ""
        );
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "阿姨每天发发文章，一个月赚6000元",
            "app_share_desc" => ""
        );
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "优秀文章精彩呈现，只要有人阅读就有奖励，收益更快！提现更便捷！1分钟赚1元，根本停不下来，注册还送现金哦！",
            "app_share_desc" => ""
        );
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "推荐一款真实靠谱的网赚APP给您，分享文章就能赚钱！收益高，秒提现，轻轻松松月入万元，现在注册还送现金红包！我已经提现多次到账啦，赶快点击链接注册赚钱吧！",
            "app_share_desc" => ""
        );
        $lianjie[] = array(
            "app_share_logo" => $lianjie_logo,
            "app_share_title" => "[有人@我] 和我一起薅羊毛吧 
快来【趣快报】和我一起分享阅读拿钱吧，奖励超高，你分享，我阅读咱们俩都有钱呢！",
            "app_share_desc" => "操作步骤：
[1]选择有意思的文章
[2]分享给好友或微信群
[3]好友阅读，奖金自动到账"
        );
        
        $result = array();
        if($type === "wx"){
            $result = $weixin;
        }
        else if($type == "pyq"){
            $result = $pengyouquan;
        }
        else if($type == "link"){
            $result = $lianjie;
        }
        shuffle($result);

        $len = count($result) - 1;
        $index = random_int(0, $len);

        return $result[$index];
    }
}
