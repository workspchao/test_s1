<?php

namespace Common\Validator;

use Common\Validator\BaseValidator;
use Common\Validator\RegexValidator;

class MobileNumberValidator extends BaseValidator{

    protected $mobileNumber;
    protected $regex;

    public static function make($mobileNumber, $regex)
    {
        $v = new MobileNumberValidator();
        $v->mobileNumber = $mobileNumber;
        $v->regex = $regex;
        $v->validate();

        return $v;
    }

    public function fails()
    {
        return $this->isFailed;
    }

    public function validate()
    {
        if($this->regex != NULL )
        {
            $v = RegexValidator::make($this->mobileNumber, $this->regex);
            $this->isFailed = $v->fails();
        }
        else
        {
            //if no regex is given, just make sure its not null
            $this->isFailed = !( $this->mobileNumber != NULL );
        }
    }
}