<?php

namespace AccountService\CoreConfigData;

use Common\Core\BaseEntity;
use Common\Core\BaseDateTime;

class CoreConfigData extends BaseEntity {

    const TABLE_NAME = 'core_config_data';

    private $code;
    private $value;
    private $description;

    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function jsonSerialize() {

        $json = parent::jsonSerialize();

        $json["code"] = $this->getCode();
        $json["value"] = $this->getValue();
        $json["description"] = $this->getDescription();

        return $json;
    }

}
