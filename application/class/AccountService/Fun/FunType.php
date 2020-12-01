<?php

namespace AccountService\Fun;

class FunType{
    const MENU = 'MENU';
    const FUN = 'FUN';
    
    public static function getFunTypeCode(){
        return array(
            self::MENU,
            self::FUN,
        );
    }
}

