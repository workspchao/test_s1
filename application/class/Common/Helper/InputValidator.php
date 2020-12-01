<?php

namespace Common\Helper;

use Valitron\Validator;
use Common\Validator\RegexValidator;

class InputValidator
{

    protected $input = array();
    protected $required = array();
    protected $checkZero = TRUE;
    protected $isFailed = TRUE;
    protected $additionalRules = array();
    protected $validator;

    protected $errorMessage;
    protected $errorResponse;

    function __construct(array $input = array())
    {
        $this->setInput($input);
        $this->_addCustomRules();
        $this->errorResponse = new ResponseMessage();
    }

    protected function _addCustomRules()
    {
        $this->validator->addRule('freeText', function ($field, $value, array $params, array $fields) {
            $v = RegexValidator::make($value, "/^[a-z0-9 ,.'&-]+$/i");
            return !$v->fails();
        }, 'is not valid');
    }

    public static function make(array $input, array $required, $checkZero = TRUE)
    {
        $v = new InputValidator($input);
        $v->setCheckZero($checkZero);
        $v->setRequired($required);
        $v->validate();

        return $v;
    }

    public function fails()
    {
        return $this->isFailed;
    }

    public function setInput(array $input)
    {
        $this->input = $input;
        $this->validator = new Validator($input);
        return true;
    }

    public function setRequired(array $requiredParam)
    {
        $this->required = $requiredParam;
        return true;
    }

    public function addAdditionalRules($rule, $fields)
    {
        // Get any other arguments passed to function
        $params = array_slice(func_get_args(), 0);
        call_user_func_array(array($this->validator, 'rule'), $params);
        return $this;
    }

    public function setCheckZero($bool)
    {
        if ($bool == false)
            $this->checkZero = false;
        else
            $this->checkZero = true;

        return true;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getRules()
    {
        return $this->required;
    }

    public function getValidator()
    {
        return $this->validator;
    }

    public function getAdditionalRules()
    {
        return $this->additionalRules;
    }

    public function getCheckZero()
    {
        return $this->checkZero;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getErrorResponse()
    {
        return $this->errorResponse;
    }

    public function validate()
    {
        $this->isFailed = TRUE;

        if ($this->getInput() === NULL) {
            $this->setErrorMessage(InputValidator::getNoInputMessage());
            return false;
        }

        foreach ($this->getRules() as $value) {
            if (!isset($this->getInput()[$value])) {
                $this->setErrorMessage(InputValidator::getInvalidParamMessage($value));
                return false;
            } else {
                if (is_array($this->getInput()[$value])) {
                    if (count($this->getInput()[$value]) <= 0) {
                        $this->setErrorMessage(InputValidator::getInvalidParamMessage($value));
                        return false;
                    }
                } else {
                    $val = trim($this->getInput()[$value]);

                    if (is_null($val)) {
                        $this->setErrorMessage(InputValidator::getInvalidParamMessage($value));
                        return false;
                    }

                    if ($this->getCheckZero()) {
                        if (empty($val)) {
                            $this->setErrorMessage(InputValidator::getInvalidParamMessage($value));
                            return false;
                        }
                    }
                }
            }
        }

        if (!$this->validator->validate()) {
            $this->setErrorMessage(InputValidator::getInvalidParamMessage(implode(",", array_keys($this->validator->errors()))));
            return false;
        }

        $this->isFailed = FALSE;
        return true;
    }

    public static function getNoInputMessage()
    {
        return 'No input parameter is given';
    }

    public static function getInvalidParamMessage($paramName)
    {
        return $paramName ? "Invalid or missing parameters: $paramName" : "Invalid or missing parameters.";
    }

    public static function constructInvalidParamResponse($err_msg)
    {
        $response = new ResponseMessage();

        $response->getHeader()->setStatus(ResponseHeader::HEADER_PARAMETER_MISSING_INVALID);
        $response->setStatusCode(ResponseHeader::HEADER_PARAMETER_MISSING_INVALID);
        $response->setStatus(false);
        $response->setMessage($err_msg);

        return $response;
    }

    public function setErrorMessage($err_msg)
    {
        $this->errorMessage = $err_msg;

        $this->errorResponse = InputValidator::constructInvalidParamResponse($err_msg);
    }
}
