<?php

namespace Common\Core;

class BaseConstant
{

    public static function toArray()
    {
        $oClass = new \ReflectionClass(static::class);
        return $oClass->getConstants();
    }

    public static function exists($value)
    {
        return in_array($value, static::toArray());
    }
}
