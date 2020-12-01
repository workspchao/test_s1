<?php

namespace Common\Validator;

abstract class BaseValidator
{

    protected $isFailed = true;
    protected $error_message = NULL;
    protected $error_code;

    abstract public function validate();

    public function fails()
    {
        return $this->isFailed;
    }

    protected function setErrorMessage($msg)
    {
        $this->error_message = $msg;
        return true;
    }

    public function getErrorMessage()
    {
        return $this->error_message;
    }

    protected function setErrorCode($code)
    {
        $this->error_code = $code;
        return true;
    }

    public function getErrorCode()
    {
        return $this->error_code;
    }
}
