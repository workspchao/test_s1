<?php

namespace Common\Helper;

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        if (!is_array($_SERVER)) {
            return array();
        }

        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value;
            }
        }
        return $headers;
    }
}

class RequestHeader
{

    protected static $baseHeader = array();

    function __construct()
    {
        
    }

    /*
     * Get Customised headers from request
     */
    public static function get()
    {
        if (count(self::$baseHeader) <= 0) {
            if ($headers = getallheaders()) {
                self::$baseHeader = ArrayExtractor::extract($headers, array(
                    ResponseHeader::FIELD_X_APP,
                    ResponseHeader::FIELD_X_AUTHORIZATION,
                    ResponseHeader::FIELD_X_LANGUAGE,
                    ResponseHeader::FIELD_X_VERSION,
                    //ResponseHeader::FIELD_X_USER_AUTHORIZATION,
                    ResponseHeader::FIELD_X_LOCATION,
                    ResponseHeader::FIELD_X_SIGNATURE
                ));
            }
        }

        return self::$baseHeader;
    }

    /*
     * get any header by key
     */
    public static function getByKey($key)
    {
        if ($headers = getallheaders()) {
            if (array_key_exists($key, $headers))
                return $headers[$key];
        }

        return false;
    }

    public static function set($key, $value)
    {
        self::$baseHeader[$key] = $value;
        return self::$baseHeader;
    }
}
