<?php

namespace Common\Core;

class Language
{

    private $code = NULL;
    private $language = NULL;

    function __construct($code = 'zh-CN')
    {
        $this->setCode($code);
    }

    public function setCode($code)
    {
        $this->code = $code;
        return true;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setLanguage($lang)
    {
        $this->language = $lang;
        return true;
    }

    public function getLanguage()
    {
        return $this->language;
    }
}
