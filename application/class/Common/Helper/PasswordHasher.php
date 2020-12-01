<?php

namespace Common\Helper;

class PasswordHasher
{
    
    public static function hash($password, $salt, $mode = null)
    {
        $salted_password = $password . $salt;
        if($mode == "sha256"){
            return hash('sha256', $salted_password);
        }
        
        return password_hash($salted_password, PASSWORD_BCRYPT);
    }
    
    public static function compare($password, $salt, $hashed_password, $mode = null)
    {
        if($mode == "sha256"){
            $in = PasswordHasher::hash($password, $salt, $mode);
            return ($in == $hashed_password);
        }
        
        return password_verify($password . $salt, $hashed_password);
    }
    
}
