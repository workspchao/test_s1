<?php

namespace AccountService\AccessToken;

use AccountService\Account\UserType;
use AccountService\CoreConfigData\CoreConfigDataService;
use AccountService\CoreConfigData\CoreConfigType;

class TokenExpirationGetter {

    public static function get($userType) {
        switch ($userType) {
            case UserType::SYSTEM:
                return null; // no expiry
            case UserType::ADMIN:
                return self::getAdminExpiration();
            case UserType::APPUSER:
                return self::getAppUserExpiration();
            default:
                return null; //no exp
        }
    }

    protected static function getAdminExpiration() {
        $serviceCoreConfigData = CoreConfigDataService::build();
        
        if ($exp = $serviceCoreConfigData->getConfig(CoreConfigType::ADMIN_SESSION_PERIOD)){
            if(is_numeric($exp)){
                return $exp;
            }
        }
        //for test
        return null;
        return 15; //default 15 minutes
    }

    //转客token有效期
    protected static function getAppUserExpiration() {
        //24小时过期
        return 24 * 60;
    }
}
