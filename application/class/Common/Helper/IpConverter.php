<?php

namespace Common\Helper;

class IpConverter
{

    public static function toString($integer)
    {
        return long2ip($integer);
    }

    public static function toInt($ip_str)
    {
        return ip2long($ip_str);
    }
}
