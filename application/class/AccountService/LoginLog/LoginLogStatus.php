<?php

namespace AccountService\LoginLog;

class LoginLogStatus
{
    const SUCCESS = 'SUCCESS';
    const FAILED = 'FAILED';
    
    public static function getLoginLogStatusCodes(){
        return array(
            self::SUCCESS,
            self::FAILED,
        );
    }
}