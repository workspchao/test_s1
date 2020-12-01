<?php

namespace AccountService\Account;

class UserStatus
{
    const UNVERIFIED   = 'U';
    const VERIFIED     = 'V';
    const SUSPENDED    = 'S'; //封禁用户： 任何功能不可使用
    const ANOMALOUS    = 'A'; //异常用户： 不再计费、可正常使用、不可提现
    const HIGHRISK     = 'R'; //高风险用户： 可以正常使用(同verified)
    
    public static function getUserStatusCodes(){
        return array(
            self::UNVERIFIED,
            self::VERIFIED,
            self::SUSPENDED,
            self::ANOMALOUS,
            self::HIGHRISK,
        );
    }
}