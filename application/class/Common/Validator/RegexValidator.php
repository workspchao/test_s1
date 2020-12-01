<?php

namespace Common\Validator;

class RegexValidator extends BaseValidator
{

    protected $regex_format;
    protected $input;

    public static function make($input, $format)
    {
        $v = new RegexValidator();
        $v->input = $input;
        $v->regex_format = $format;
        $v->validate();

        return $v;
    }

    public function validate()
    {
        $this->isFailed = true;

        try {
            $old_error = error_reporting(0); // Turn off error reporting
            if (preg_match($this->regex_format, $this->input))
                $this->isFailed = false;
            error_reporting($old_error);  // Set error reporting to old level
        } catch (\Exception $e) {
            error_log('RegexValidator invalid regex');
        }
    }
}
