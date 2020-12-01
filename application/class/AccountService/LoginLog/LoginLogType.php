<?php

namespace AccountService\LoginLog;

class LoginLogType
{
    const LOGIN = 'LOGIN';
    const LOGOUT = 'LOGOUT';
    const FORGET_PASSWORD = 'FORGET_PASSWORD';
    
    public static function getLoginLogTypeCodes(){
        return array(
            self::LOGIN,
            self::LOGOUT,
            self::FORGET_PASSWORD,
        );
    }
    
}