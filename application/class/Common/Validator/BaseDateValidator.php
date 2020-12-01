<?php

namespace Common\Validator;

use Common\Core\BaseDateTime;

class BaseDateValidator extends BaseValidator
{

    protected $date;

    public static function make(BaseDateTime $dt)
    {
        $v = new BaseDateValidator();
        $v->date = $dt;
        $v->validate();

        return $v;
    }

    public function validate()
    {
        $this->isFailed = true;

        if (!$this->date->isNull()) {
            $this->isFailed = false;
        }
    }
}
