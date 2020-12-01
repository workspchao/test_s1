<?php

use Common\Core\IpAddress;
use Common\Core\BaseDateTime;
use Common\Helper\ResponseHeader;
//use Common\Microservice\AccountService\FunctionCode;
use AccountService\AccessToken\AccessTokenService;
use AccountService\AccessToken\AccessToken;
use AccountService\VersionControl\VersionControlService;
use AccountService\VersionControl\VersionControl;
use AccountService\UserProfile\UserProfileService;
use AccountService\UserProfile\UserProfile;
use AccountService\LoginAccount\LoginAccountService;
use AccountService\LoginAccount\LoginAccount;
use AccountService\LoginAccount\LoginAccountLoginType;
use AccountService\Account\UserType;
use AccountService\Account\UserStatus;
use Common\Helper\IniWriter;
use Common\Helper\InputValidator;
use Common\Helper\RandomCodeGenerator;
use AccountService\IncrementTable\IncrementIDService;
use AccountService\IncrementTable\IncrementIDAttribute;
use AccountService\Common\MessageCode;
use AccountService\Common\ClientType;
use Common\ValueObject\PasswordObj;
use AccountService\Role\Role;
use AccountService\Role\RoleService;
use AccountService\Role\AdminRoleCode;
use AccountService\Role\SystemRoleCode;
use AccountService\Role\PublicRoleCode;
use AccountService\Fun\Fun;
use AccountService\Fun\FunService;
use AccountService\Fun\FunCode;
use AccountService\Fun\FunType;
use AccountService\Fun\FunAccessType;
use AccountService\UserRole\UserRole;
use AccountService\UserRole\UserRoleService;
use AccountService\RoleFun\RoleFun;
use AccountService\RoleFun\RoleFunService;

class Init_system extends Base_Controller {

    protected $_service;
    protected $_serviceIncrementID;
    protected $_serviceUserProfile;
    protected $_serviceLoginAccount;
    protected $_serviceAccessToken;
    protected $_serviceVersionControl;
    protected $_serviceRole;
    protected $_serviceFun;
    protected $_serviceUserRole;
    protected $_serviceRoleFun;

    private $_iniFilePath;
    private $_initVersion;
    
    private $_init_status;
    private $_init_step;
    private $_init_time;
    private $_init_version;
    
    private $params = array();
    private $entityUserProfileSystem;
    private $entityUserProfileAdmin;
    
    private $entityLoginAccountSystem;
    private $entityLoginAccountAdmin;
    
    private $entityAccessTokenSystem;
    private $entityVersionControlBatchJob;
    private $entityVersionControlAdminWeb;
    private $entityVersionControlAppAndroid;
    private $entityVersionControlAppIOS;
    private $entityVersionControlWeixinWeb;
    private $entityRoleSystemUser;
    private $entityRoleSuperAdmin;
    private $entityRoleAppUser;
    private $entityRoleNonAppUser;
    private $entityFunCommon;
    private $entityFunPublic;
    private $entityFunAdmin;
    private $entityRoleFunSystemCommon;
    private $entityRoleFunAdminCommon;
    private $entityRoleFunAdminPublic;
    private $entityRoleFunAdminAdmin;
    private $entityRoleFunAppCommon;
    private $entityRoleFunAppPublic;
    private $entityRoleFunNonAppCommon;
    private $entityRoleFunNonAppPublic;
    
    private $entityUserRoleSystem;
    private $entityUserRoleAdmin;
    private $entityUserRoleAppUser;

    function __construct() {
        
        parent::__construct();
        
        $this->_iniFilePath = "./upload/setup.lock";
        $this->_initVersion = "0.0.1";
        $this->_initPass = getenv("SETUP_PASS");
        
    }
    
    private function getServiceIncrementID(){
        if(!$this->_serviceIncrementID){
            $this->_serviceIncrementID = IncrementIDService::build();
        }
        $this->_serviceIncrementID->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceIncrementID->setUpdatedBy(0);
        return $this->_serviceIncrementID;
    }
    
    private function getServiceUserProfile(){
        if(!$this->_serviceUserProfile){
            $this->_serviceUserProfile = UserProfileService::build();
        }
        $this->_serviceUserProfile->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceUserProfile->setUpdatedBy(0);
        return $this->_serviceUserProfile;
    }
    
    private function getServiceLoginAccount(){
        if(!$this->_serviceLoginAccount){
            $this->_serviceLoginAccount = LoginAccountService::build();
        }
        $this->_serviceLoginAccount->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceLoginAccount->setUpdatedBy(0);
        return $this->_serviceLoginAccount;
    }
    
    private function getServiceAccessToken(){
        if(!$this->_serviceAccessToken){
            $this->_serviceAccessToken = AccessTokenService::build();
        }
        $this->_serviceAccessToken->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceAccessToken->setUpdatedBy(0);
        return $this->_serviceAccessToken;
    }
    
    private function getServiceVersionControl(){
        if(!$this->_serviceVersionControl){
            $this->_serviceVersionControl = VersionControlService::build();
        }
        $this->_serviceVersionControl->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceVersionControl->setUpdatedBy(0);
        return $this->_serviceVersionControl;
    }
    
    private function getServiceRole(){
        if(!$this->_serviceRole){
            $this->_serviceRole = RoleService::build();
        }
        $this->_serviceRole->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceRole->setUpdatedBy(0);
        return $this->_serviceRole;
    }
    
    private function getServiceFun(){
        if(!$this->_serviceFun){
            $this->_serviceFun = FunService::build();
        }
        $this->_serviceFun->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceFun->setUpdatedBy(0);
        return $this->_serviceFun;
    }
    
    private function getServiceUserRole(){
        if(!$this->_serviceUserRole){
            $this->_serviceUserRole = UserRoleService::build();
        }
        $this->_serviceUserRole->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceUserRole->setUpdatedBy(0);
        return $this->_serviceUserRole;
    }
    
    private function getServiceRoleFun(){
        if(!$this->_serviceRoleFun){
            $this->_serviceRoleFun = RoleFunService::build();
        }
        $this->_serviceRoleFun->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceRoleFun->setUpdatedBy(0);
        return $this->_serviceRoleFun;
    }
    
    private function _checkSetup(){
        
        $this->requiredGet(array('setup_pass'));
        
        $setup_pass = $this->input_get('setup_pass');
        if($setup_pass !== $this->_initPass){
            $errMsg = InputValidator::getInvalidParamMessage("Invalid password");
            
            $this->_response(InputValidator::constructInvalidParamResponse($errMsg));
            $this->_respondAndTerminate();
            return false;
        }
        
        $file = $this->_iniFilePath;
        
        // 读取 安装文件（setup.lock）中的配置信息
        if (!file_exists($file)) {
            //create file
            
            $config = array(
                "install" => array(
                    "init_status" => FLAG_NO,
                    "init_step" => null,
                    "init_time" => null,
                    "init_version" => null,
                )
            );
            
            $ini = new IniWriter();
            $ini->writeToFile($file, $config);
        }
        
        $settings = parse_ini_file($file, true);
        if(isset($settings['install'])){
            $settings = $settings['install'];
        }
        
        $this->_init_status = isset($settings['init_status']) ? $settings['init_status'] : null;
        $this->_init_step = isset($settings['init_step']) ? $settings['init_step'] : null;
        $this->_init_time = isset($settings['init_time']) ? $settings['init_time'] : null;
        $this->_init_version = isset($settings['init_version']) ? $settings['init_version'] : null;
    }
    
    private function _saveSetupStep(){
        try{
            $file = $this->_iniFilePath;
            $config = array(
                "install" => array(
                    "init_status" => $this->_init_status,
                    "init_step" => $this->_init_step,
                    "init_time" => $this->_init_time,
                    "init_version" => $this->_init_version,
                )
            );

            $ini = new IniWriter();
            $ini->writeToFile($file, $config);
        
            return true;
        }
        catch(\Exception $ex){
            log_message("error", "init file save fail.");
            log_message("error", "" . $ex->getMessage());
            return false;
        }
    }
    
    public function init_setup(){
        
        //check step
        $this->_checkSetup();
        
        if($this->_init_status === FLAG_YES){
            log_message("error", "init fail: system already initialize.");
            $this->_respondWithCode(MessageCode::CODE_INIT_SYSTEM_ALREADY_INIT, ResponseHeader::HEADER_NOT_FOUND);
            return false;
        }
        
        if($this->_init_step == null){
            $this->_init_step = 1;
        }
        
        //check version
        
        //1. create user profile(system, admin)
        $this->CreateUserProfiles();
        
        //2. create batch_job/admin_web/user_app_android/user_app_ios client
        $this->CreateVersionControls();
        
        //3. create role
        $this->CreateRoles();
        
        //4. create function
        $this->CreateFunctions();
        
        //5. create role_function
        $this->CreateRoleFunctions();
        
        //6. create user_role
        $this->CreateUserRoles();
           
        log_message("info", "System init success.");
        
        $this->response_message->getHeader()->setStatus(ResponseHeader::HEADER_SUCCESS);
        $this->response_message->setStatusCode(MessageCode::CODE_INIT_SUCCESS);
        $this->response_message->setMessage('System init success.');

        $this->_respond();
        return true;
    }
    
    /**
     * 1. create user profile
     * 1.1. create user profile for system
     * 1.2. create user profile for super admin
     * 
     * 1.3. create login account for system
     * 1.4. create login account for super admin
     * 
     * 1.5. create access token for system
     * 
     */
    protected function CreateUserProfiles(){
        
        //1.1 create user profile for system
        if(!$this->entityUserProfileSystem = $this->_createUserProfile("1", "1.2", UserType::SYSTEM, "System User", "USER_SYSTEM")){
            
        }
        
        //1.2 create user profile for super admin
        if(!$this->entityUserProfileAdmin = $this->_createUserProfile("1.2", "1.3", UserType::ADMIN, "Super Admin", "USER_ADMIN")){
            
        }
        
        //1.3. create login account for system
        if(!$this->entityLoginAccountSystem = $this->_createLoginAccount("1.3", "1.4", 
                LoginAccountLoginType::NONE, $this->entityUserProfileSystem, "LOGIN_ACCOUNT_SYSTEM")){
            return false;
        }
        
        //1.4. create login account for super admin
        if(!$this->entityLoginAccountAdmin = $this->_createLoginAccount("1.4", "1.5", 
                LoginAccountLoginType::USERNAME, $this->entityUserProfileAdmin, "LOGIN_ACCOUNT_ADMIN")){
            return false;
        }
        
        //1.5. create access token for system
        if(!$this->entityAccessTokenSystem = $this->_createAccessToken("1.5", "2", $this->entityLoginAccountSystem, "TOKEN_SYSTEM")){
            return false;
        }
        
    }
    
    /**
     * 2. create app client
     * 2.1. create client app for batch job
     * 2.2. create client app for admin web
     * 2.3. create client app for android
     * 2.4. create client app for ios
     * @return boolean
     */
    protected function CreateVersionControls(){
        
        //2.1. create client app for batch job
        $app_id = getenv("SETUP_APPID_BATCH_JOB");
        if(empty($app_id)){
            $app_id = RandomCodeGenerator::generate(20);
        }
        $app_name = "batch_job";
        $version = null;
        $platform = null;
        $download_url = null;
        $key = "APP_BATCH_JOB";
        if(!$this->entityVersionControlBatchJob = $this->_createVersionControl("2", "2.2", $app_id, $app_name, 
                $version, $platform, $download_url, $this->entityUserProfileSystem, $key)){
            return false;
        }
        
        //2.2. create client app for admin web
        $app_id = getenv("SETUP_APPID_ADMIN_WEB");
        if(empty($app_id)){
            $app_id = RandomCodeGenerator::generate(20);
        }
        $app_name = "admin_web";
        $version = null;
        $platform = 'WEB';
        $download_url = null;
        $key = "APP_ADMIN_WEB";
        if(!$this->entityVersionControlAdminWeb = $this->_createVersionControl("2.2", "2.3", $app_id, $app_name, 
                $version, $platform, $download_url, null, $key)){
            return false;
        }
        
        //2.3. create client app for android
        $app_id = getenv("SETUP_APPID_USER_APP_ANDROID");
        if(empty($app_id)){
            $app_id = RandomCodeGenerator::generate(20);
        }
        $app_name = "user_app_android";
        $version = "0.0.1";
        $platform = "ANDROID";
        $download_url = null;
        $system_user_id = null;
        $key = "APP_ADMIN_WEB";
        if(!$this->entityVersionControlAppAndroid = $this->_createVersionControl("2.3", "2.4", $app_id, $app_name, 
                $version, $platform, $download_url, null, $key)){
            return false;
        }
        
        //2.4. create client app for ios
        $app_id = getenv("SETUP_APPID_USER_APP_IOS");
        if(empty($app_id)){
            $app_id = RandomCodeGenerator::generate(20);
        }
        $app_name = "user_app_ios";
        $version = "0.0.1";
        $platform = "IOS";
        $download_url = null;
        $system_user_id = null;
        $key = "APP_ADMIN_WEB";
        if(!$this->entityVersionControlAppIOS = $this->_createVersionControl("2.4", "2.5", $app_id, $app_name, 
                $version, $platform, $download_url, null, $key)){
            return false;
        }
        
        //2.5. create client app for weixinweb
        $app_id = getenv("SETUP_APPID_WEIXIN_WEB");
        if(empty($app_id)){
            $app_id = RandomCodeGenerator::generate(20);
        }
        $app_name = "weixin_web";
        $version = "0.0.1";
        $platform = "WEB";
        $download_url = null;
        $system_user_id = null;
        $key = "APP_WEIXIN_WEB";
        if(!$this->entityVersionControlWeixinWeb = $this->_createVersionControl("2.5", "3", $app_id, $app_name, 
                $version, $platform, $download_url, null, $key)){
            return false;
        }
        
    }
    
    /**
     * 3. create roles
     * 3.1. create role system_user
     * 3.2. create role super_admin
     * 3.3. create role app_user
     * 3.4. create role non_app_user
     */
    protected function CreateRoles(){

        //3.1. create role system_user
        if(!$this->entityRoleSystemUser = $this->_createRole("3", "3.2", SystemRoleCode::SYSTEM_USER, "System User", "ROLE_SYSTEM_USER")){
            return false;
        }
        
        //3.2. create role super_admin
        if(!$this->entityRoleSuperAdmin = $this->_createRole("3.2", "3.3", AdminRoleCode::SUPER_ADMIN, "Super Admin", "ROLE_SUPER_ADMIN")){
            return false;
        }
        
        //3.3. create role app_user
        if(!$this->entityRoleAppUser = $this->_createRole("3.3", "3.4", PublicRoleCode::APP_USER, "App User", "ROLE_APP_USER")){
            return false;
        }
        
        //3.4. create role non_app_user
        if(!$this->entityRoleNonAppUser = $this->_createRole("3.4", "4", PublicRoleCode::NON_APP_USER, "Non App User", "ROLE_NON_APP_USER")){
            return false;
        }
    }
    
    /**
     * 4. create functions
     * 4.1. create function common_functions
     * 4.2. create function public_functions
     * 4.3. create function admin_functions
     */
    protected function CreateFunctions(){
        
        //4.1. create function common_functions
        if(!$this->entityFunCommon = $this->_createFun("4", "4.2", FunCode::COMMON_FUNCTIONS, "Common Functions", FunType::FUN, FunAccessType::READ, "FUN_COMMON_FUNCTION")){
            return false;
        }
        
        //4.2. create function public_functions
        if(!$this->entityFunPublic = $this->_createFun("4.2", "4.3", FunCode::PUBLIC_FUNCTIONS, "Public Functions", FunType::FUN, FunAccessType::READ, "FUN_PUBLIC_FUNCTION")){
            return false;
        }
        
        //4.3. create function admin_functions
        if(!$this->entityFunAdmin = $this->_createFun("4.3", "5", FunCode::ADMIN_FUNCTIONS, "Admin Functions", FunType::FUN, FunAccessType::READ, "FUN_ADMIN_FUNCTION")){
            return false;
        }
        
    }
    
    /**
     * 5. create role functions
     * 5.1. create role_function system_user common_functions
     * 5.2. create role_function super_admin common_functions
     * 5.3. create role_function super_admin public_functions
     * 5.4. create role_function super_admin admin_functions
     * 5.5. create role_function app_user    common_functions
     * 5.6. create role_function app_user    public_functions
     * 5.7. create role_function non_app_user   common_functions
     * 5.8. create role_function non_app_user   public_functions
     */
    protected function CreateRoleFunctions(){
        
        //5.1. create role_function system_user common_functions
        if(!$this->entityRoleFunSystemCommon = $this->_createRoleFun("5", "5.2", $this->entityRoleSystemUser, $this->entityFunCommon, "ROLE_FUN_SYSTEM_COMMON")){
            return false;
        }
        
        //5.2. create role_function super_admin common_functions
        if(!$this->entityRoleFunAdminCommon = $this->_createRoleFun("5.2", "5.3", $this->entityRoleSuperAdmin, $this->entityFunCommon, "ROLE_FUN_ADMIN_COMMON")){
            return false;
        }
        
        //5.3. create role_function super_admin public_functions
        if(!$this->entityRoleFunAdminPublic = $this->_createRoleFun("5.3", "5.4", $this->entityRoleSuperAdmin, $this->entityFunPublic, "ROLE_FUN_ADMIN_PUBLIC")){
            return false;
        }
        
        //5.4. create role_function super_admin admin_functions
        if(!$this->entityRoleFunAdminAdmin = $this->_createRoleFun("5.4", "5.5", $this->entityRoleSuperAdmin, $this->entityFunAdmin, "ROLE_FUN_ADMIN_ADMIN")){
            return false;
        }
        
        //5.5. create role_function app_user    common_functions
        if(!$this->entityRoleFunAppCommon = $this->_createRoleFun("5.5", "5.6", $this->entityRoleAppUser, $this->entityFunCommon, "ROLE_FUN_APP_COMMON")){
            return false;
        }
        
        //5.6. create role_function app_user    public_functions
        if(!$this->entityRoleFunAppPublic = $this->_createRoleFun("5.6", "5.7", $this->entityRoleAppUser, $this->entityFunPublic, "ROLE_FUN_APP_PUBLIC")){
            return false;
        }
        
        //5.7. create role_function non_app_user   common_functions
        if(!$this->entityRoleFunNonAppCommon = $this->_createRoleFun("5.7", "5.8", $this->entityRoleNonAppUser, $this->entityFunCommon, "ROLE_FUN_NON_APP_COMMON")){
            return false;
        }
        
        //5.8. create role_function non_app_user   public_functions
        if(!$this->entityRoleFunNonAppPublic = $this->_createRoleFun("5.8", "6", $this->entityRoleNonAppUser, $this->entityFunPublic, "ROLE_FUN_NON_APP_PUBLIC")){
            return false;
        }
        
    }
    
    /**
     * 6.create user role
     * 6.1. create user role for system_user system_user
     * 6.2. create user role for super_admin super_admin
     */
    protected function CreateUserRoles(){
        
        //6.1. create user role for system_user system_user
        if(!$this->entityUserRoleSystem = $this->_createUserRole("6", "6.2", $this->entityUserProfileSystem, $this->entityRoleSystemUser, "USER_ROLE_SYSTEM")){
            return false;
        }
        
        //6.2. create user role for super_admin super_admin
        if(!$this->entityUserRoleAdmin = $this->_createUserRole("6.2", "7", $this->entityUserProfileAdmin, $this->entityRoleSuperAdmin, "USER_ROLE_ADMIN")){
            return false;
        }
        
    }
    
    private function _createUserProfile($step1, $step2, $type, $name, $key){
        
        $serviceIncrement = $this->getServiceIncrementID();
        $serviceUserProfile = $this->getServiceUserProfile();
        
        if($this->_init_step == $step1){
            
            $filter = new UserProfile();
            $filter->setUserType($type);
            if($collectionUserProfile = $serviceUserProfile->selectUserProfile($filter, null, 1, 1)){
                
                $entityUserProfileInserted = $collectionUserProfile->result->current();
                $this->params["$key"] = $entityUserProfileInserted;
            
                $this->_init_step = $step2;
                if(!$this->_saveSetupStep()){
                    log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                    $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                    return false;
                }

                log_message("error", "init fail: $type user already exists.");
                $this->_respondWithCode(MessageCode::CODE_INIT_USER_ALREADY_EXISTS, ResponseHeader::HEADER_NOT_FOUND);
                return $entityUserProfileInserted;
            }
            
            $serviceIncrement->setNoOfDigit(7);
            if($type == UserType::SYSTEM){
                $accountID = "SYS" . $serviceIncrement->getRawIncrementID(IncrementIDAttribute::SYSTEM_ACCOUNT_ID, true);
            }
            else if($type == UserType::ADMIN){
                $accountID = "ADM" . $serviceIncrement->getRawIncrementID(IncrementIDAttribute::ADMIN_ACCOUNT_ID, true);
            }
            else if($type == UserType::USER){
                $accountID = "U" . $serviceIncrement->getRawIncrementID(IncrementIDAttribute::USER_ACCOUNT_ID, true);
            }

            $entityUserProfile = new UserProfile();
            $entityUserProfile->setUserType($type);
            $entityUserProfile->setAccountID($accountID);
            $entityUserProfile->setNickName($name);
            $entityUserProfile->setStatus(UserStatus::VERIFIED);
            
            if(!$entityUserProfileInserted = $serviceUserProfile->addUserProfile($entityUserProfile)){
                log_message("error", "init fail: create $type user fail.");
                $this->_respondWithCode(MessageCode::CODE_INIT_USER_CREATE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $entityUserProfileInserted;
            
            $this->_init_step = $step2;
            if(!$this->_saveSetupStep()){
                log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
        }
        
        if(!isset($this->params["$key"]) || $this->params["$key"] == null){
            $filter = new UserProfile();
            $filter->setUserType($type);
            if(!$collectionUserProfile = $serviceUserProfile->selectUserProfile($filter, null, 1, 1)){
                log_message("error", "init fail: $type user not found.");
                $this->_respondWithCode(MessageCode::CODE_INIT_USER_NOT_FOUND, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $collectionUserProfile->result->current();
        }
        
        return $this->params["$key"];
    }
    
    private function _createLoginAccount($step1, $step2, $type, $user, $key){
        
        $serviceLoginAccount = $this->getServiceLoginAccount();
        
        
        $passwordObj = new PasswordObj();
        $random_password = $passwordObj->generatePassword();

        if($user->getUserType() == UserType::SYSTEM){
            $username = $user->getAccountID();
            $password = $random_password;
        }
        else if($user->getUserType() == UserType::ADMIN){
            $this->requiredGet(array('admin_user','admin_pass'));
            $username = $this->input_get('admin_user');
            $password = $this->input_get('admin_pass');
        }
        
        if($this->_init_step == $step1){
            
            $filter = new LoginAccount();
            $filter->setUserId($user->getId());
            $filter->setLoginType($type);
            if($collectionLoginAccount = $serviceLoginAccount->selectLoginAccount($filter, null, 1, 1)){
                
                $entityLoginAccountInserted = $collectionLoginAccount->result->current();
                $this->params["$key"] = $entityLoginAccountInserted;

                $this->_init_step = $step2;
                if(!$this->_saveSetupStep()){
                    log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                    $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                    return false;
                }

                log_message("error", "init fail: login account $key already exists.");
                $this->_respondWithCode(MessageCode::CODE_INIT_LOGIN_ACCOUNT_ALREADY_EXISTS, ResponseHeader::HEADER_NOT_FOUND);
                return $entityLoginAccountInserted;
            }
            
            $passwordObj->setNewPassword($password);
            $passwordObj->setExpiredAt(new BaseDateTime());
            
            $entityLoginAccount = new LoginAccount();
            $entityLoginAccount->setUserId($user->getId());
            $entityLoginAccount->setLoginType($type);
            $entityLoginAccount->setUsername($username);
            $entityLoginAccount->setPassword($passwordObj);
            
            if(!$entityLoginAccountInserted = $serviceLoginAccount->addLoginAccount($entityLoginAccount)){
                log_message("error", "init fail: create login account $key fail.");
                $this->_respondWithCode(MessageCode::CODE_INIT_LOGIN_ACCOUNT_CREATE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $entityLoginAccountInserted;
            
            $this->_init_step = $step2;
            if(!$this->_saveSetupStep()){
                log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
        }
        
        if(!isset($this->params["$key"]) || $this->params["$key"] == null){
            $filter = new LoginAccount();
            $filter->setLoginType($type);
            $filter->setUserId($user->getId());
            $filter->setUsername($username);
            if(!$collectionLoginAccount = $serviceLoginAccount->selectLoginAccount($filter, null, 1, 1)){
                log_message("error", "init fail: login account $key not found.");
                $this->_respondWithCode(MessageCode::CODE_INIT_LOGIN_ACCOUNT_NOT_FOUND, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $collectionLoginAccount->result->current();
        }
        
        return $this->params["$key"];
    }
    
    private function _createAccessToken($step1, $step2, $entityLoginAccount, $key){
        
        $serviceAccessToken = $this->getServiceAccessToken();
        
        if($this->_init_step == $step1){

            if(!$entityAccessTokenInserted = $serviceAccessToken->generate($entityLoginAccount, UserType::SYSTEM)){
                log_message("error", "init fail: create access token $key fail.");
                $this->_respondWithCode(MessageCode::CODE_INIT_TOKEN_CREATE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $entityAccessTokenInserted;
            
            $this->_init_step = $step2;
            if(!$this->_saveSetupStep()){
                log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
        }

        if(!isset($this->params["$key"]) || $this->params["$key"] == null){
            
            if(!$entityAccessToken = $serviceAccessToken->getByLoginAccountID($entityLoginAccount->getId())){
                log_message("error", "init fail: token $key not found.");
                $this->_respondWithCode(MessageCode::CODE_INIT_TOKEN_NOT_FOUND, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $entityAccessToken;
        }
        
        return $this->params["$key"];
    }
    
    private function _createVersionControl($step1, $step2, $app_id, $app_name, $version, $platform, $download_url, $system_user, $key){
        
        $serviceVersionControl = $this->getServiceVersionControl();
        
        if($this->_init_step == $step1){
    
            $filter = new VersionControl();
            $filter->setAppname($app_name);
            if($collectionVersionControl = $serviceVersionControl->selectVersionControl($filter, null, 1, 1)){
                
                $entityVersionControlInserted = $collectionVersionControl->result->current();
                $this->params["$key"] = $entityVersionControlInserted;

                $this->_init_step = $step2;
                if(!$this->_saveSetupStep()){
                    log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                    $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                    return false;
                }
                log_message("error", "init fail: client app $app_name already exists.");
                $this->_respondWithCode(MessageCode::CODE_INIT_CLIENT_APP_ALREADY_EXISTS, ResponseHeader::HEADER_NOT_FOUND);
                return $entityVersionControlInserted;
            }
            
            $entityVersionControl = new VersionControl();
            $entityVersionControl->setAppId($app_id);
            $entityVersionControl->setAppKey(null);
            $entityVersionControl->setAppname($app_name);
            $entityVersionControl->setVersion($version);
            $entityVersionControl->setPlatform($platform);
            if($system_user)
                $entityVersionControl->setSystemUserId($system_user->getId());
            
            if(!$entityVersionControlInserted = $serviceVersionControl->addVersionControl($entityVersionControl)){
                log_message("error", "init fail: create client app $app_name fail.");
                $this->_respondWithCode(MessageCode::CODE_INIT_CLIENT_APP_CREATE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $entityVersionControlInserted;
            
            $this->_init_step = $step2;
            if(!$this->_saveSetupStep()){
                log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
        }
        
        if(!isset($this->params["$key"]) || $this->params["$key"] == null){
            
            $filter = new VersionControl();
            $filter->setAppname($app_name);
            if(!$collectionVersionControl = $serviceVersionControl->selectVersionControl($filter, null, 1, 1)){
                log_message("error", "init fail: client app $app_name not found.");
                $this->_respondWithCode(MessageCode::CODE_INIT_CLIENT_APP_NOT_FOUND, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $collectionVersionControl->result->current();
        }
        
        return $this->params["$key"];
    }
    
    private function _createRole($step1, $step2, $code, $name, $key){
        $serviceRole = $this->getServiceRole();
        
        //3.1. create role system_user
        if($this->_init_step == $step1){
            $filter = new Role();
            $filter->setCode($code);
            if($collectionRole = $serviceRole->selectRole($filter, null, 1, 1)){
                
                $entityRoleInserted = $collectionRole->result->current();
                $this->params["$key"] = $entityRoleInserted;

                $this->_init_step = $step2;
                if(!$this->_saveSetupStep()){
                    log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                    $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                    return false;
                }
                
                log_message("error", "init fail: role $code already exists.");
                $this->_respondWithCode(MessageCode::CODE_INIT_ROLE_ALREADY_EXISTS, ResponseHeader::HEADER_NOT_FOUND);
                return $entityRoleInserted;
            }
            
            $entityRole = new Role();
            $entityRole->setCode($code);
            $entityRole->setName($name);
            
            if(!$entityRoleInserted = $serviceRole->addRole($entityRole)){
                log_message("error", "init fail: create role $code fail.");
                $this->_respondWithCode(MessageCode::CODE_INIT_ROLE_CREATE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $entityRoleInserted;
            
            $this->_init_step = $step2;
            if(!$this->_saveSetupStep()){
                log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
        }
        
        if(!isset($this->params["$key"]) || $this->params["$key"] == null){
            
            $filter = new Role();
            $filter->setCode($code);
            if(!$collectionRole = $serviceRole->selectRole($filter, null, 1, 1)){
                log_message("error", "init fail: role $code not found.");
                $this->_respondWithCode(MessageCode::CODE_INIT_ROLE_NOT_FOUND, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $collectionRole->result->current();
        }
        
        return $this->params["$key"];
    }
    
    private function _createFun($step1, $step2, $code, $name, $display_type, $access_type, $key){
        
        $serviceFun = $this->getServiceFun();
        
        if($this->_init_step == $step1){
            $filter = new Fun();
            $filter->setCode($code);
            if($collectionFun = $serviceFun->selectFun($filter, null, 1, 1)){
                
                $entityFunInserted = $collectionFun->result->current();
                $this->params["$key"] = $entityFunInserted;

                $this->_init_step = $step2;
                if(!$this->_saveSetupStep()){
                    log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                    $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                    return false;
                }
                log_message("error", "init fail: fun $code already exists.");
                $this->_respondWithCode(MessageCode::CODE_INIT_FUN_ALREADY_EXISTS, ResponseHeader::HEADER_NOT_FOUND);
                return $entityFunInserted;
            }
            
            $entityFun = new Fun();
            $entityFun->setCode($code);
            $entityFun->setName($name);
            $entityFun->setDisplayType($display_type);
            $entityFun->setDisplayOrder(1);
            $entityFun->setAccessType($access_type);
            
            if(!$entityFunInserted = $serviceFun->addFun($entityFun)){
                log_message("error", "init fail: create fun $code fail.");
                $this->_respondWithCode(MessageCode::CODE_INIT_FUN_CREATE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $entityFunInserted;
            
            $this->_init_step = $step2;
            if(!$this->_saveSetupStep()){
                log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
        }
        
        if(!isset($this->params["$key"]) || $this->params["$key"] == null){
            
            $filter = new Fun();
            $filter->setCode($code);
            if(!$collection = $serviceFun->selectFun($filter, null, 1, 1)){
                log_message("error", "init fail: fun $code not found.");
                $this->_respondWithCode(MessageCode::CODE_INIT_FUN_NOT_FOUND, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $collection->result->current();
        }
        
        return $this->params["$key"];
    }
    
    private function _createRoleFun($step1, $step2, $role, $fun, $key){
        
        $serviceRoleFun = $this->getServiceRoleFun();
        
        if($this->_init_step == $step1){
            
            $filter = new RoleFun();
            $filter->setRole($role);
            $filter->setFun($fun);
            
            if($collection = $serviceRoleFun->selectRoleFun($filter, null, 1, 1)){
                
                $entityInserted = $collection->result->current();
                $this->params["$key"] = $entityInserted;

                $this->_init_step = $step2;
                if(!$this->_saveSetupStep()){
                    log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                    $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                    return false;
                }
                log_message("error", "init fail: role_fun $key already exists.");
                $this->_respondWithCode(MessageCode::CODE_INIT_ROLE_FUN_ALREADY_EXISTS, ResponseHeader::HEADER_NOT_FOUND);
                return $entityInserted;
            }
            
            $entity = clone $filter;
            
            if(!$entityInserted = $serviceRoleFun->addRoleFun($entity)){
                log_message("error", "init fail: role_fun $key fail.");
                $this->_respondWithCode(MessageCode::CODE_INIT_ROLE_FUN_CREATE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $entityInserted;
            
            $this->_init_step = $step2;
            if(!$this->_saveSetupStep()){
                log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
        }
        
        if(!isset($this->params["$key"]) || $this->params["$key"] == null){
            
            $filter = new RoleFun();
            $filter->setRole($this->entityRoleSystemUser);
            $filter->setFun($this->entityFunCommon);
            
            if(!$collection = $serviceRoleFun->selectRoleFun($filter, null, 1, 1)){
                log_message("error", "init fail: role_fun $key not found.");
                $this->_respondWithCode(MessageCode::CODE_INIT_ROLE_FUN_NOT_FOUND, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $collection->result->current();
        }
        
        return $this->params["$key"];
    }
    
    private function _createUserRole($step1, $step2, $user, $role, $key){
        
        $serviceUserRole = $this->getServiceUserRole();
        
        
        if($this->_init_step == $step1){
            
            $filter = new UserRole();
            $filter->setUser($user);
            $filter->setRole($role);
            
            if($collection = $serviceUserRole->selectUserRole($filter, null, 1, 1)){
                
                $entityInserted = $collection->result->current();
                $this->params["$key"] = $entityInserted;

                $this->_init_step = $step2;
                if(!$this->_saveSetupStep()){
                    log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                    $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                    return false;
                }
                log_message("error", "init fail: user_role $key already exists.");
                $this->_respondWithCode(MessageCode::CODE_INIT_USER_ROLE_ALREADY_EXISTS, ResponseHeader::HEADER_NOT_FOUND);
                return $entityInserted;
            }
            
            $entity = clone $filter;
            
            if(!$entityInserted = $serviceUserRole->addUserRole($entity)){
                log_message("error", "init fail: user_role $key fail.");
                $this->_respondWithCode(MessageCode::CODE_INIT_USER_ROLE_CREATE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $entityInserted;
            
            $this->_init_step = $step2;
            if(!$this->_saveSetupStep()){
                log_message("error", "init fail: ini file save fail, please make sure you have permission accessible.");
                $this->_respondWithCode(MessageCode::CODE_INIT_INI_FILE_SAVE_FAIL, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
        }
        
        if(!isset($this->params["$key"]) || $this->params["$key"] == null){
            
            $filter = new UserRole();
            $filter->setUser($user);
            $filter->setRole($role);
            
            if(!$collection = $serviceUserRole->selectUserRole($filter, null, 1, 1)){
                log_message("error", "init fail: user_role $key not found.");
                $this->_respondWithCode(MessageCode::CODE_INIT_USER_ROLE_NOT_FOUND, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            $this->params["$key"] = $collection->result->current();
        }
        
        return $this->params["$key"];
    }
    
}
