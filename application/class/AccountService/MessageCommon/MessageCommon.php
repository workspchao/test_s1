<?php

namespace AccountService\MessageCommon;

use Common\Core\BaseEntity;
use Common\Core\BaseDateTime;

class MessageCommon extends BaseEntity {

    const TABLE_NAME = 'message_common';

    private $country_language_code;
    private $code;
    private $message;

    public function setCountryLanguageCode($country_language_code) {
        $this->country_language_code = $country_language_code;
        return $this;
    }

    public function getCountryLanguageCode() {
        return $this->country_language_code;
    }

    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    public function getMessage() {
        return $this->message;
    }

    public function jsonSerialize() {

        $json = parent::jsonSerialize();

        $json["country_language_code"] = $this->getCountryLanguageCode();
        $json["code"] = $this->getCode();
        $json["message"] = $this->getMessage();

        return $json;
    }

}
