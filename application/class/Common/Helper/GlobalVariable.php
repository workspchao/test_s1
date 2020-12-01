<?php

namespace Common\Helper;

class GlobalVariable
{

    protected static $vars = array();

    public static function set($key, $value)
    {
        static::$vars[$key] = $value;
    }

    public static function get($key)
    {
        if (isset(static::$vars[$key])) {
            return static::$vars[$key];
        }

        return false;
    }
}
