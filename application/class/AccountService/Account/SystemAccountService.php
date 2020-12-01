<?php

namespace AccountService\Account;

use AccountService\Common\MessageCode;
use AccountService\LoginAccount\LoginAccountLoginType;
use AccountService\LoginLog\LoginLog;
use AccountService\Account\UserType;

class SystemAccountService extends AccountService {

    protected static $_instance = NULL;

    function __construct() {
        
    }

    public static function build() {
        if (self::$_instance == NULL) {
            self::$_instance = new SystemAccountService();
        }
        return self::$_instance;
    }
    
    public function systemLogin($user_profile_id)
    {
        $serviceUserProfile = $this->_getServiceUserProfile();
        
        if( $systemUser = $serviceUserProfile->getUserProfile($user_profile_id) ) {
            
            if($systemUser->getUserType() != UserType::SYSTEM){
                log_message("error", "SystemAccountService - systemLogin - invalid user type");
                $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
                return false;
            }
            
            //get login account
            $serviceLoginAccount = $this->_getServiceLoginAccount();
            if( !$entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserId($user_profile_id, LoginAccountLoginType::NONE) )
            {
                log_message("error", "SystemAccountService - systemLogin - login account not found by user id $user_profile_id");
                $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
                return false;
            }
            
            //generate access token
            $serviceAccessToken = $this->_getServiceAccessToken();
            //a better way to avoid multiple system job running at the same time?
            if( $entityToken = $serviceAccessToken->getExistingToken($entityLoginAccount) )
            {
                $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
                return array($entityLoginAccount, $entityToken);
            }                
            else if( $entityToken = $serviceAccessToken->generate($entityLoginAccount, UserType::SYSTEM))
            {
                $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
                return array($entityLoginAccount, $entityToken);
            }
        }
        
        $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
        return false;
    }
}
