<?php

namespace Common\Helper;

use Symfony\Component\EventDispatcher\EventDispatcher;

class BaseEventDispatcher
{

    protected static $dispatcher;

    public static function get()
    {
        if (self::$dispatcher == NULL) {
            self::$dispatcher = new EventDispatcher();
        }

        return self::$dispatcher;
    }
}
