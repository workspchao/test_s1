<?php

namespace AccountService\Fun;

use Common\Core\BaseEntity;
use Common\Core\BaseDateTime;

class Fun extends BaseEntity {

    const TABLE_NAME = 'fun';

    private $code;
    private $name;
    private $display_type;
    private $display_order;
    private $access_type;
    private $description;
    private $url;
    private $parent_id;

    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setDisplayType($display_type) {
        $this->display_type = $display_type;
        return $this;
    }

    public function getDisplayType() {
        return $this->display_type;
    }

    public function setDisplayOrder($display_order) {
        $this->display_order = $display_order;
        return $this;
    }

    public function getDisplayOrder() {
        return $this->display_order;
    }

    public function setAccessType($access_type) {
        $this->access_type = $access_type;
        return $this;
    }

    public function getAccessType() {
        return $this->access_type;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setParentId($parent_id) {
        $this->parent_id = $parent_id;
        return $this;
    }

    public function getParentId() {
        return $this->parent_id;
    }

    public function jsonSerialize() {

        $json = parent::jsonSerialize();

        $json["code"] = $this->getCode();
        $json["name"] = $this->getName();
        $json["display_type"] = $this->getDisplayType();
        $json["display_order"] = $this->getDisplayOrder();
        $json["access_type"] = $this->getAccessType();
        $json["description"] = $this->getDescription();
        $json["url"] = $this->getUrl();
        $json["parent_id"] = $this->getParentId();

        return $json;
    }

}
