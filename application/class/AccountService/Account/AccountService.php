<?php

namespace AccountService\Account;

use Common\Helper\GuidGenerator;
use Common\Core\BaseService;
use Common\Core\BaseDateTime;
use Common\Core\IpAddress;
//use Common\IncrementID\IncrementIDService;
use Common\ValueObject\PasswordObj;
use AccountService\Common\MessageCode;
use AccountService\IncrementTable\IncrementIDService;
use AccountService\IncrementTable\IncrementIDAttribute;
//use AccountService\Common\EncryptedFieldFactory;
use AccountService\UserProfile\UserProfile;
use AccountService\UserProfile\UserProfileService;
use AccountService\Account\UserType;
use AccountService\LoginAccount\LoginAccount;
use AccountService\LoginAccount\LoginAccountService;
use AccountService\PasswordPolicy\AdminPasswordPolicyFactory;
use AccountService\PasswordPolicy\PublicUserPasswordPolicyFactory;
use AccountService\VersionControl\VersionControl;
use AccountService\VersionControl\VersionControlService;
use AccountService\AccessToken\AccessTokenService;
use AccountService\AccessToken\AccessToken;
use AccountService\BlackList\BlackListService;
use AccountService\LoginLog\LoginLog;
use AccountService\LoginLog\LoginLogType;
use AccountService\LoginLog\LoginLogStatus;
use AccountService\LoginLog\LoginLogService;
use AccountService\CoreConfigData\CoreConfigDataService;
use AccountService\CoreConfigData\CoreConfigType;
use AccountService\Otp\Otp;
use AccountService\Otp\OtpService;
use AccountService\UserInvite\UserInvite;
use AccountService\UserInvite\UserInviteService;
use AccountService\LoginAccount\LoginAccountLoginType;
use AccountService\Wxconfig\WxconfigService;
use AccountService\Wxconfig\WxconfigType;
use AccountService\Wxuser\WxuserService;
use Common\Microservice\WeixinService\WeixinMobileService;
use AccountService\UserMission\UserMissionService;
use AccountService\Mission\MissionCode;
use AccountService\UserCashoutMode\UserCashoutModeService;
use AccountService\UserFun\UserFunService;

use AccountService\Account\AliMobileAuthService;
use AccountService\Account\UserStatus;

use AccountService\PlatformStatics\PlatformStatics;
use AccountService\PlatformDailyStatics\PlatformDailyStatics;
use AccountService\PlatformHourStatics\PlatformHourStatics;
use AccountService\DataStatics\DataStaticsService;
use AccountService\NewsDomainPool\NewsDomainPoolService;

//use Common\Microservice\WeixinService\WeixinServiceFactory;
//use Common\Microservice\FriendService\FriendServiceFactory;
//use Common\Microservice\EwalletService\EwalletServiceFactory;
//use Common\Microservice\EwalletService\EwalletClient;

class AccountService extends BaseService {

    protected static $_instance = NULL;

    function __construct() {
        
    }

    public static function build() {
        if (self::$_instance == NULL) {
            self::$_instance = new AccountService();
        }
        return self::$_instance;
    }

    private $_serviceUserFun = NULL;
    private $_serviceIncrement = null;
    private $_serviceUserProfile = null;
    private $_serviceLoginAccount = null;
    private $_serviceAccessToken = null;
    private $_serviceVersionControl = null;
    private $_serviceBlackList = null;
    private $_serviceLoginLog = null;
    private $_serviceOtp = null;
    private $_serviceUserInvite = null;
    private $_serviceCoreConfigData = null;
    private $_serviceWxconfig = null;
    private $_serviceWxuser = null;
    private $_serviceWeiXinMobile = null;
    private $_serviceUserMission = null;
    private $_serviceAppDomainPool = null;
    private $_serviceUserCashoutMode = null;
    private $_serviceNewsDomain = null;
    

    private $_serviceAliMobileAuth    = null;
    private $_serviceDataStatics      = null;


    protected function _getServiceDataStatics(){
        if(!$this->_serviceDataStatics){
            $this->_serviceDataStatics = DataStaticsService::build();
        }
        
        return $this->_serviceDataStatics;
    }

    protected function _getServiceAliMobileAuth(){
        if(!$this->_serviceAliMobileAuth){
            $this->_serviceAliMobileAuth = AliMobileAuthService::build();
        }
        
        return $this->_serviceAliMobileAuth;
    }

    protected function _getServiceUserFun() {
        if (!$this->_serviceUserFun) {
            $this->_serviceUserFun = UserFunService::build();
        }
        $this->_serviceUserFun->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceUserFun->setIpAddress($this->getIpAddress());
        return $this->_serviceUserFun;
    }

    protected function _getServiceIncrement() {
        if (!$this->_serviceIncrement) {
            $this->_serviceIncrement = IncrementIDService::build();
        }
        $this->_serviceIncrement->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceIncrement->setIpAddress($this->getIpAddress());
        return $this->_serviceIncrement;
    }

    protected function _getServiceUserProfile() {
        if (!$this->_serviceUserProfile) {
            $this->_serviceUserProfile = UserProfileService::build();
        }
        $this->_serviceUserProfile->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceUserProfile->setIpAddress($this->getIpAddress());
        return $this->_serviceUserProfile;
    }

    protected function _getServiceLoginAccount() {
        if (!$this->_serviceLoginAccount) {
            $this->_serviceLoginAccount = LoginAccountService::build();
        }
        $this->_serviceLoginAccount->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceLoginAccount->setIpAddress($this->getIpAddress());
        return $this->_serviceLoginAccount;
    }

    protected function _getServiceAccessToken() {
        if (!$this->_serviceAccessToken) {
            $this->_serviceAccessToken = AccessTokenService::build();
        }
        $this->_serviceAccessToken->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceAccessToken->setIpAddress($this->getIpAddress());
        return $this->_serviceAccessToken;
    }

    protected function _getServiceVersionControl() {
        if (!$this->_serviceVersionControl) {
            $this->_serviceVersionControl = VersionControlService::build();
        }
        $this->_serviceVersionControl->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceVersionControl->setIpAddress($this->getIpAddress());
        return $this->_serviceVersionControl;
    }

    protected function _getServiceBlackList() {
        if (!$this->_serviceBlackList) {
            $this->_serviceBlackList = BlackListService::build();
        }
        $this->_serviceBlackList->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceBlackList->setIpAddress($this->getIpAddress());
        return $this->_serviceBlackList;
    }

    protected function _getServiceLoginLog() {
        if (!$this->_serviceLoginLog) {
            $this->_serviceLoginLog = LoginLogService::build();
        }
        $this->_serviceLoginLog->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceLoginLog->setIpAddress($this->getIpAddress());
        $this->_serviceLoginLog->setUserAgent($this->getUserAgent());

        return $this->_serviceLoginLog;
    }

    protected function _getServiceOtp() {
        if (!$this->_serviceOtp) {
            $this->_serviceOtp = OtpService::build();
        }
        $this->_serviceOtp->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceOtp->setIpAddress($this->getIpAddress());
        return $this->_serviceOtp;
    }

    protected function _getServiceCoreConfigData() {
        if (!$this->_serviceCoreConfigData) {
            $this->_serviceCoreConfigData = CoreConfigDataService::build();
        }
        $this->_serviceCoreConfigData->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceCoreConfigData->setIpAddress($this->getIpAddress());
        return $this->_serviceCoreConfigData;
    }

    protected function _getServiceWxconfig() {
        if (!$this->_serviceWxconfig) {
            $this->_serviceWxconfig = WxconfigService::build();
        }
        $this->_serviceWxconfig->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceWxconfig->setIpAddress($this->getIpAddress());
        return $this->_serviceWxconfig;
    }

    protected function _getServiceWxuser() {
        if (!$this->_serviceWxuser) {
            $this->_serviceWxuser = WxuserService::build();
        }
        $this->_serviceWxuser->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceWxuser->setIpAddress($this->getIpAddress());
        return $this->_serviceWxuser;
    }

    protected function _getServiceUserInvite() {
        if (!$this->_serviceUserInvite) {
            $this->_serviceUserInvite = UserInviteService::build();
        }
        $this->_serviceUserInvite->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceUserInvite->setIpAddress($this->getIpAddress());
        return $this->_serviceUserInvite;
    }

    protected function _getServiceWeiXinMobile() {
        if (!$this->_serviceWeiXinMobile) {
            $this->_serviceWeiXinMobile = WeixinMobileService::build();
        }
        return $this->_serviceWeiXinMobile;
    }
    
    protected function _getServiceUserMission() {
        if (!$this->_serviceUserMission) {
            $this->_serviceUserMission = UserMissionService::build();
        }
        $this->_serviceUserMission->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceUserMission->setIpAddress($this->getIpAddress());
        return $this->_serviceUserMission;
    }
    
    protected function _getServiceUserCashoutMode() {
        if (!$this->_serviceUserCashoutMode) {
            $this->_serviceUserCashoutMode = UserCashoutModeService::build();
        }
        $this->_serviceUserCashoutMode->setUpdatedBy($this->getUpdatedBy());
        $this->_serviceUserCashoutMode->setIpAddress($this->getIpAddress());
        return $this->_serviceUserCashoutMode;
    }

    protected function _getServiceNewsDomain(){
        if(!$this->_serviceNewsDomain){
            $this->_serviceNewsDomain = NewsDomainPoolService::build();
        }
        $this->_serviceNewsDomain->setIpAddress($this->getIpAddress());
        $this->_serviceNewsDomain->setUpdatedBy($this->getUpdatedBy());
        return $this->_serviceNewsDomain;
    }
    
    /**
     * 1. get if the access token is valid
     * 2. check if the user is accessible to the function
     * 3. return user profile id if ok, else return false.
     * @param type $token
     * @param type $function
     * @param type $access_type
     * @param type $sessionType
     * @return boolean
     */
    public function checkAccess($token, $userType, $functionCode = NULL) {
        //get user from login service
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        list($entityLogin, $entityToken) = $serviceLoginAccount->getByAccessToken($token, $userType);
        if ($entityLogin) {
            return $this->_checkAccess($entityLogin->getUserId(), $userType, $functionCode);
        }

        $this->setResponseCode(MessageCode::CODE_INVALID_ACCESS_TOKEN);
        return false;
    }

    public function getUserByUserProfileId($user_profile_id){
        
        $serviceUserProfile = $this->_getServiceUserProfile();
        $filterUserProfile = new UserProfile();
        $filterUserProfile->setId($user_profile_id);
        
        if($collectionUserProfile = $serviceUserProfile->selectUserProfile($filterUserProfile)){
            
            $entityUserProfile = $collectionUserProfile->result->current();
            
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_GET_SUCCESS);
            return $entityUserProfile;
//            //return $entityUserProfile->getSelectedFeild();
//            $userInfo = $entityUserProfile->getSelectedField(array('id', 'name', 'user_type', 'accountID', 'user_status'));
//            return $userInfo;
        }
        
        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_GET_FAIL);
        return false;
    }
    
    public function getUsersByIds(array $user_ids){
        
        $serviceUserProfile = $this->_getServiceUserProfile();
        if(!$collection = $serviceUserProfile->getUsers($user_ids)){
            //user not found
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_NOT_FOUND);
            return false;
        }
        
        $results = array();
        
        foreach ($collection->result as $entityUserProfile) {
            $user = $entityUserProfile->getSelectedField(array('id','accountID','user_status','mobile','email','gender','name','full_name','dob','avatar_url','created_at','created_by'));
            $results[] = $user;
        }
        
        $collection->result = $results;
        
        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_GET_SUCCESS);
        return $collection;
    }
    
    #========== ========== ========== ==========#
    
    protected function _checkAccess($userId, $userType, $functionCode = NULL) {
        
        //check user type 
        if(!$entityUserProfile = $this->getUserByUserProfileId($userId) ){
            return false;
        }

        if($entityUserProfile->getUserType() != $userType){
            $this->setResponseCode(MessageCode::CODE_USER_NOT_ACCESSIBLE);
            return false;
        }

        //check user status, SUSPENDED 封禁状态，不允许使用app功能
        if($entityUserProfile->getStatus() == UserStatus::SUSPENDED){
            $this->setResponseCode(MessageCode::CODE_INVALID_ACCESS_TOKEN);
            return false;
        }


        $servUserFun = $this->_getServiceUserFun();

        if (!empty($functionCode)) {
            if (!$servUserFun->checkUserAccessible($userId, $functionCode)) {
                $tmpLogEntity = json_encode(array("user_id" => $userId, "fun_code" => $functionCode));
                log_message("error", "_checkAccess user not accessible. " . $tmpLogEntity);
                
                $this->setResponseCode(MessageCode::CODE_USER_NOT_ACCESSIBLE);
                return false;
            }
        }


        if($userType == UserType::APPUSER){
            //app 用户需要记录活跃时间
            $lastUpdatedAt = $entityUserProfile->getLastUpdatedAt();
            $currentHour = date("H");
            $currentDate = date("Y-m-d");


            $platformHourStaticsActiveNum  = 1;
            $platformDailyStaticsActiveNum = 1;

            if(!empty($lastUpdatedAt) && !$lastUpdatedAt->isNull()){
                $lastUpdatedHour = $lastUpdatedAt->getFormat("H");
                $lastUpdatedDate = $lastUpdatedAt->getFormat("Y-m-d");

                //判断上次活跃时间与当前是否处于同一个小时
                if($lastUpdatedHour == $currentHour){
                    $platformHourStaticsActiveNum = 0;
                }

                //判断上次活跃时间与当前是否处于同一天
                if($lastUpdatedDate == $currentDate){
                    $platformDailyStaticsActiveNum = 0;
                }
            }   
            
            
            $platformHourStaticsEntity  = NULL;
            $platformDailyStaticsEntity = NULL;
            if(!empty($platformHourStaticsActiveNum)){
                $platformHourStaticsEntity = new PlatformHourStatics();
                $platformHourStaticsEntity->setDate($currentDate);
                $platformHourStaticsEntity->setHour($currentHour);
                $platformHourStaticsEntity->setActiveNum(1);
            }
            
            if(!empty($platformDailyStaticsActiveNum)){
                $platformDailyStaticsEntity = new PlatformDailyStatics();
                $platformDailyStaticsEntity->setDate($currentDate);
                $platformDailyStaticsEntity->setActiveNum(1);
            }

            $servDataStatics = $this->_getServiceDataStatics();
            $servDataStatics->updatePlatformStaticsNum(NULL, $platformDailyStaticsEntity, $platformHourStaticsEntity);


            //更新用户的活跃时间
            $serviceUserProfile = $this->_getServiceUserProfile();
            $userProfileEntity  = new UserProfile();
            $userProfileEntity->setId($userId);
            $userProfileEntity->setLastUpdatedAt(BaseDateTime::now());
            $serviceUserProfile->updateUserProfile($userProfileEntity);
        }

        $this->setResponseCode(MessageCode::CODE_USER_IS_ACCESSIBLE);
        return $userId;
    }

    protected function _checkBlackList(IpAddress $ipAddress = NULL, $user_profile_id = NULL) {

        $serviceBlackList = $this->_getServiceBlackList();
        if ($ipAddress != NULL) {
            if (!$serviceBlackList->screen($ipAddress)) {
                $tmpLogEntity = json_encode($ipAddress);
                log_message("error", "IP has been blacklisted. $tmpLogEntity");
                $this->setResponseCode(MessageCode::CODE_REACHED_MAXIMUM_ATTEMPT);
                //throw new \Exception("IP has been blacklisted", MessageCode::CODE_REACHED_MAXIMUM_ATTEMPT);
                return false;
            }
        }
        else if ($user_profile_id != NULL) {
            //screen user blacklist
            if (!$serviceBlackList->screen(NULL, $user_profile_id)) {
                $tmpLogEntity  = json_encode(array("user_Id" => $user_profile_id));
                log_message("error","User has been blacklisted. $tmpLogEntity");
                $this->setResponseCode(MessageCode::CODE_REACHED_MAXIMUM_ATTEMPT);
                //throw new \Exception("User has been blacklisted", MessageCode::CODE_REACHED_MAXIMUM_ATTEMPT);
                return false;
            }
        }

        return true;
    }

    protected function _addLoginLog($status, $type, LoginAccount $entityLoginAccount = NULL, $address = NULL, $lat = NULL, $long = NULL) {
        
        $serviceLoginLog = $this->_getServiceLoginLog();

        $entityLoginLog = new LoginLog();
        $entityLoginLog->setAttempt(1); //redundant        
        $entityLoginLog->setLoginType($type);
        $entityLoginLog->setStatus($status);
        $entityLoginLog->setIpAddress($this->getIpAddress());
        $entityLoginLog->setUserAgent($this->getUserAgent());
        $entityLoginLog->setAddress($address);
        $entityLoginLog->setLat($lat);
        $entityLoginLog->setLong($long);

        if ($entityLoginAccount instanceof LoginAccount) {
            $entityLoginLog->setUserId($entityLoginAccount->getUserId());
            $entityLoginLog->setLoginAccountId($entityLoginAccount->getId());
        }

        if ($serviceLoginLog->addLoginLog($entityLoginLog)) {
            if ($status == LoginLogStatus::FAILED) {
                //login failed, check is need save to blacklist
                $serviceBlackList = $this->_getServiceBlackList();
                $serviceBlackList->checkInBlackList($entityLoginLog->getIpAddress(), $entityLoginLog->getUserId());
            }
            return true;
        }
        log_message("error", "_addLoginLog -> add login log failed");
        return false;
    }

    protected function _proceedToLogin(LoginAccount $loginAccount, $userType, $channel = null) {
        //create access token
        $serviceAccessToken = AccessTokenService::build();
        $serviceAccessToken->setUpdatedBy($this->getUpdatedBy());
        $serviceAccessToken->setIpAddress($this->getIpAddress());

        //invalidate previous tokens
        if (!$serviceAccessToken->invalidateAll($loginAccount)) {
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

//        if ($loginAccount->hasExpired()) {
//            log_message("error", "_proceedToLogin - login account expired");
//            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
//            return false;
////            //登录账号过期
////            //give a temporary token only valid for changing password
////            $token = $serviceAccessToken->generate($loginAccount, SessionType::LOGIN_RESET_PWD, AccessTokenAccessType::PASSWORD, $this->getClient());
////            $this->setResponseCode(MessageCode::CODE_PASSWORD_EXPIRED);
//        }

        if (!$entityAccessToken = $serviceAccessToken->generate($loginAccount, $userType)) {
            log_message("error", "_proceedToLogin - generate access token failed");
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        //更新用户最后登录时间
        $serviceUserProfile = $this->_getServiceUserProfile();

        $userProfileEntity  = new UserProfile();
        $userProfileEntity->setId($loginAccount->getUserId());
        $userProfileEntity->setLastLoginAt(BaseDateTime::now());
        $userProfileEntity->setChannel($channel);
        $serviceUserProfile->updateUserProfile($userProfileEntity);

        $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
        return $entityAccessToken;
    }

    protected function _logoutAccount(LoginAccount $entityLoginAccount) {

        //create access token
        $serviceAccessToken = AccessTokenService::build();
        $serviceAccessToken->setUpdatedBy($this->getUpdatedBy());
        $serviceAccessToken->setIpAddress($this->getIpAddress());

        //invalidate previous tokens
        if (!$serviceAccessToken->invalidateAll($entityLoginAccount)) {
            $this->setResponseCode(MessageCode::CODE_LOGOUT_FAIL);
            return false;
        }

        $this->setResponseCode(MessageCode::CODE_LOGOUT_SUCCESS);
        return true;
    }

    protected function _getLoginUserInfo($user_id, 
            UserProfile $entityUserProfile = null, 
            $entityUserInvite = null, 
            $isNew = false){
        
        $serviceUserProfile = $this->_getServiceUserProfile();
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        $serviceUserInvite = $this->_getServiceUserInvite();
        
        if(!$entityUserProfile){
            if(!$entityUserProfile = $serviceUserProfile->getUserProfile($user_id)){
                log_message("error", "_getLoginUserInfo, user not found. $user_id");
                return false;
            }
        }
        
        $app_id = null;
        $serviceWxconfig = $this->_getServiceWxconfig();
        if(!$entityWxconfig = $serviceWxconfig->getActiveApp(WxconfigType::ZKLOGIN)){
            log_message("error", "_getLoginUserInfo, wxconfig not found. $user_id, " . WxconfigType::ZKLOGIN);
            //return false;
        }
        else{
            $app_id = $entityWxconfig->getAppId();
        }
        
        $mobile = null;
        if($entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserId($user_id, LoginAccountLoginType::MOBILE)){
            $mobile = $entityLoginAccount->getUserName();
        }
        $openid = null;
        if($entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserId($user_id, LoginAccountLoginType::WEIXIN, $app_id)){
            $openid = $entityLoginAccount->getUserName();
        }
        $invite_code = null;
        $refer_id = null;
        $refer_code = null;
        if($entityUserInvite){
            $invite_code = $entityUserInvite->getInviteCode();
            $refer_id = $entityUserInvite->getParent1();
        }
        else{
            if($entityUserInvite = $serviceUserInvite->getUserInviteByUserId($user_id)){
                $invite_code = $entityUserInvite->getInviteCode();
                $refer_id = $entityUserInvite->getParent1();
            }
        }
        
//        if(!$entityUserInviteParent && !empty($refer_id)){
//            $entityUserInviteParent = $serviceUserInvite->getUserInviteByUserId($refer_id);
//        }
//        
//        if($entityUserInviteParent){
//            $refer_code = $entityUserInviteParent->getInviteCode();
//        }
        
        $userInfo = $entityUserProfile->getSelectedField(array('accountID', 'nick_name', 'avatar_url', 'created_at', 'id'));
        $userInfo["reg_at"] = $entityUserProfile->getCreatedAt()->getUnix();
        $userInfo["invite_code"] = $invite_code;
        $userInfo["mobile"] = $mobile;
        $userInfo["openid"] = $openid;
        //app_id不给前端
        //$userInfo["app_id"] = $app_id;
        //$userInfo['refer_code'] = $refer_code;
        $userInfo['user_mission'] = null;
        
        //新人
        if($isNew){
            //触发新人奖励
            $serviceUserMission = $this->_getServiceUserMission();
            if($entityUserMission = $serviceUserMission->finishUserMission($user_id, MissionCode::TASK_REGISTER)){
                $user_mission = $entityUserMission->getSelectedField(array('code', 'amount'));
                $user_mission['code'] = MissionCode::TASK_REGISTER;
                $userInfo['user_mission'] = $user_mission;
            }
        }
        
        return $userInfo;
    }
    
    #========== ========= ========== ==========#
    
    /**
     * 
     * @param type $otpType
     * @param type $custom_destination
     * @param type $user_profile_id
     * @return boolean
     */
    protected function sendOtpCode($otpType, $custom_destination, $user_profile_id){
        
        if($entityUserProfile = $this->getUserByUserProfileId($user_profile_id) ){
            $serviceOtp = $this->_getServiceOtp();
            if(!$entityOtp = $serviceOtp->generateOtp($otpType, $custom_destination, $entityUserProfile)){
                
                $this->setResponseCode($serviceOtp->getResponseCode());
                return false;
            }
            
            $this->setResponseCode(MessageCode::CODE_OTP_HAS_BEEN_SENT);
            return $entityOtp->getSelectedField(array('otp_type','destination', 'resend_period'));
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_NOT_FOUND);
        return false;
    }
    
    /**
     * 
     * @param type $otpType
     * @param type $custom_destination
     * @param type $user_profile_id
     * @return boolean
     */
    protected function sendGuestOtpCode($otpType, $custom_destination){
        
        $serviceOtp = $this->_getServiceOtp();
        if(!$entityOtp = $serviceOtp->generateOtp($otpType, $custom_destination)){

            $this->setResponseCode($serviceOtp->getResponseCode());
            return false;
        }

        $this->setResponseCode(MessageCode::CODE_OTP_HAS_BEEN_SENT);
        return $entityOtp->getSelectedField(array('otp_type','destination', 'resend_period'));
    }
    
    protected function generateAccountID($userType = UserType::ADMIN){
        
        $prefix = null;
        
        $serviceIncremnt = $this->_getServiceIncrement();
        $accountID = null;
        $serviceIncremnt->setNoOfDigit(7);
        if($userType == UserType::ADMIN){
            $accountID = $serviceIncremnt->getRawIncrementID(IncrementIDAttribute::ADMIN_ACCOUNT_ID, true);
            $prefix = $serviceIncremnt->getPrefix();
            if(!$prefix){
                $prefix = "ADM";
            }
        }
        else if($userType == UserType::APPUSER){
            $accountID = $serviceIncremnt->getRawIncrementID(IncrementIDAttribute::USER_ACCOUNT_ID, true);
            $prefix = $serviceIncremnt->getPrefix();
//            if(!$prefix){
//                $prefix = "U";
//            }
        }
        else if($userType == UserType::SYSTEM){
            $accountID = $serviceIncremnt->getRawIncrementID(IncrementIDAttribute::SYSTEM_ACCOUNT_ID, true);
            $prefix = $serviceIncremnt->getPrefix();
            if(!$prefix){
                $prefix = "SYS";
            }
        }else{
            return NULL;
        }
        
        return $prefix . $accountID;
    }
    
    /**
     * 
     * @param type $role
     * @param type $user_type
     * @param type $username
     * @param type $login_type
     * @param type $status
     * @param type $password
     * @param type $name
     * @param type $nickname
     * @param type $avatar_url
     * @param type $app_id
     * @return boolean
     */
    protected function createUser($user_type, $username, $login_type, $status, 
            $password = null, $name = null, $nickname = null, $avatar_url = null, $app_id = null){
        
        log_message("debug", "AccountService CreateUser update_by = " . $this->getUpdatedBy());
        log_message("debug", "AccountService CreateUser ip = " . json_encode($this->getIpAddress()));
        
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        $serviceUserProfile  = $this->_getServiceUserProfile();
        
        //check login account if exists
        if ($entityLoginAccountExisting = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type, $app_id)) {
            log_message("error", "createUser[failed] - login account already existing - $username, $login_type");
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_ALREADY_EXISTS);
            return false;
        }

        $accountID = $this->generateAccountID($user_type);
        
        //create user
        $entityUserProfile = new UserProfile();
        //$entityUserProfile->setId(GuidGenerator::generate());
        $entityUserProfile->setAccountID($accountID);
        $entityUserProfile->setUserType($user_type);
        $entityUserProfile->setStatus($status);
        $entityUserProfile->setName($name);
        $entityUserProfile->setNickName($nickname);
        //$entityUserProfile->setGender($gender);

        if($login_type == LoginAccountLoginType::MOBILE){
            $entityUserProfile->setMobile($username);
        }
        
        $entityUserProfile->setAvatarUrl($avatar_url);
        $entityUserProfile->setCreatedBy($this->getUpdatedBy());
        $entityUserProfile->setIpAddress($this->getIpAddress());
        log_message("debug", "address==>". $this->getIpAddress()->getString());
        //start db transaction
        $serviceUserProfile->startDBTransaction();

        //insert user profile
        if (!$entityUserProfile = $serviceUserProfile->addUserProfile($entityUserProfile)) {

            //rollback db transaction
            $serviceUserProfile->rollbackDBTransaction();

            log_message("error", "createUser[failed] - add profile failed");
            $this->setResponseCode($serviceUserProfile->getResponseCode());
            return false;
        }

        $passwordObj = new PasswordObj();
        if ($password == null) {
            $policy = AdminPasswordPolicyFactory::build($entityUserProfile->getId());
            $password = $passwordObj->generatePassword($policy);
        }
        else {
            $passwordObj->setNewPassword($password);
        }

        $entityLoginAccount = new LoginAccount();
        //$entityLoginAccount->setId(GuidGenerator::generate());
        $entityLoginAccount->setLoginType($login_type);
        $entityLoginAccount->setUsername($username);
        $entityLoginAccount->setPassword($passwordObj);
        $entityLoginAccount->setUserId($entityUserProfile->getId());
        $entityLoginAccount->setAppId($app_id);
        $entityLoginAccount->setCreatedBy($this->getUpdatedBy());

        if (!$entityLoginAccount = $serviceLoginAccount->addLoginAccount($entityLoginAccount)) {

            //rollback transaction
            $serviceUserProfile->rollbackDBTransaction();

            log_message("error", "createUser[failed] - login account create failed");
            $this->setResponseCode($serviceUserProfile->getResponseCode());
            return false;
        }

        //complete transaction
        $serviceUserProfile->completeDBTransaction();

        if($user_type == UserType::APPUSER){
            //如果是appuser, 更新platform statics 注册用户数量
            $currentHour = date("H");
            $currentDate = date("Y-m-d");

            $platformStaticsEntity = new PlatformStatics();
            $platformStaticsEntity->setUserNum(1);

            $platformHourStaticsEntity = new PlatformHourStatics();
            $platformHourStaticsEntity->setDate($currentDate);
            $platformHourStaticsEntity->setHour($currentHour);
            $platformHourStaticsEntity->setUserNum(1);


            $platformDailyStaticsEntity = new PlatformDailyStatics();
            $platformDailyStaticsEntity->setDate($currentDate);
            $platformDailyStaticsEntity->setUserNum(1);

            $servDataStatics = $this->_getServiceDataStatics();
            $servDataStatics->updatePlatformStaticsNum($platformStaticsEntity, $platformDailyStaticsEntity, $platformHourStaticsEntity);
        }

        $this->setResponseCode(MessageCode::CODE_USER_CREATE_SUCCESS);
        return array($entityLoginAccount, $entityUserProfile, $password);
    }

}
