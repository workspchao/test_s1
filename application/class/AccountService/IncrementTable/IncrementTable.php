<?php

namespace AccountService\IncrementTable;

use Common\Core\BaseEntity;
use Common\Core\BaseDateTime;

class IncrementTable extends BaseEntity {

    const TABLE_NAME = 'increment_table';

    private $attribute;
    private $value;
    private $last_increment_date;
    private $prefix;
    private $suffix;

    public function setAttribute($attribute) {
        $this->attribute = $attribute;
        return $this;
    }

    public function getAttribute() {
        return $this->attribute;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function setPrefix($prefix) {
        $this->prefix = $prefix;
        return $this;
    }

    public function getPrefix() {
        return $this->prefix;
    }

    public function setSuffix($suffix) {
        $this->suffix = $suffix;
        return $this;
    }

    public function getSuffix() {
        return $this->suffix;
    }

    public function setLastIncrementDate(BaseDateTime $last_increment_date) {
        $this->last_increment_date = $last_increment_date;
        return $this;
    }

    public function getLastIncrementDate() {
        return $this->last_increment_date;
    }

    public function jsonSerialize() {

        $json = parent::jsonSerialize();

        $json["attribute"] = $this->getAttribute();
        $json["value"] = $this->getValue();
        $json["prefix"] = $this->getPrefix();
        $json["suffix"] = $this->getSuffix();
        $json["last_increment_date"] = $this->getLastIncrementDate()->getString();

        return $json;
    }

    public static function create($attribute)
    {
        $inc = new IncrementTable();
        //$inc->setId(GuidGenerator::generate());
        $inc->setAttribute($attribute);
        $inc->setValue(1);
        //set to earliest date
        $inc->setLastIncrementDate(BaseDateTime::fromString('1970-01-01'));
        return $inc;
    }
}
