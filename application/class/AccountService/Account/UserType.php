<?php

namespace AccountService\Account;

class UserType {
    
    const SYSTEM        = 'SYSTEM';
    const ADMIN         = 'ADMIN';
    const APPUSER       = 'APPUSER'; //转客
    
    public static function getUserTypeCodes(){
        return array(
            self::SYSTEM,
            self::ADMIN,
            self::APPUSER,
        );
    }
}   