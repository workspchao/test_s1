<?php

namespace Common\Helper;

class SaltGenerator
{
    public static function generate($length = 8)
    {
        if($length > 32){
            $length = 32;
        }
        return substr(md5(uniqid(mt_rand(), true)), 0, $length);
    }
}
