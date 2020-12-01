<?php

namespace Common\Helper;

class ResponseHeader implements \Iterator
{
    const FIELD_X_AUTHORIZATION = 'X-authorization';
    const FIELD_X_LANGUAGE      = 'X-language';
    const FIELD_X_APP           = 'X-app';
    const FIELD_X_PLATFORM      = 'X-platform';
    const FIELD_X_VERSION       = 'X-version';
    const FIELD_X_LOCATION      = 'X-location'; //lat,lon
    const FIELD_X_SIGNATURE     = 'X-signature';
    //const FIELD_X_USER_AUTHORIZATION = 'X-User-Authorization';
    
    const FIELD_CONTENT_TYPE    = 'Content-Type';
    const FIELD_CACHE_CONTROL   = 'Cache-Control';
    const FIELD_USER_AGENT      = 'User-Agent';

    const VALUE_JSON            = 'application/json';
    const VALUE_HTML            = 'text/html';
    const VALUE_TEXT            = 'text/plain';

    const HEADER_SUCCESS                   = 200;  // when the request is successful
    const HEADER_PARAMETER_MISSING_INVALID = 400;  // when required params are invalid or missing
    const HEADER_UNAUTHORIZED              = 401;  // when the provided authentication details doesnt have access to a resource
    const HEADER_FORBIDDEN                 = 403;  // when authentication details not provided to access resource
    const HEADER_NOT_FOUND                 = 404;  // when resource is not found
    const HEADER_INTERNAL_SERVER_ERROR     = 500;  // when something unexpected happened on the server
    const HEADER_MOVED_PERMANENTLY         = 301;
    const HEADER_MOVE_TEMPORARILY          = 302;

    private $status;
    private $field = array();
    private $key_mapper = array();
    private $position = 0;

    function __construct()
    {
        //default
        $this->setStatus(self::HEADER_INTERNAL_SERVER_ERROR);
        $this->setField(self::FIELD_CONTENT_TYPE, self::VALUE_JSON);
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setField($field, $value)
    {
        $this->field[$field] = $field . ":" . $value;

        $is_mapped = false;
        foreach ($this->key_mapper as $mapped_field) {
            if ($mapped_field == $field) {
                $is_mapped = true;
                break;
            }
        }

        if (!$is_mapped) {
            $this->key_mapper[] = $field;
        }
    }

    public function getFieldValue($key)
    {
        if (isset($this->field[$key])) {
            return $this->field[$key];
        }

        return false;
    }

    public function getStatus()
    {
        return $this->status;
    }

    function rewind()
    {
        $this->position = 0;
    }

    function current()
    {
        $key = $this->key_mapper[$this->position];
        return $this->field[$key];
    }

    function key()
    {
        return $this->key_mapper[$this->position];
    }

    function next()
    {
        ++$this->position;
    }

    function valid()
    {
        return isset($this->key_mapper[$this->position]);
    }
}
