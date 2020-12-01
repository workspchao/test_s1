<?php

namespace AccountService\BlackList;

use Common\Core\BaseEntity;
use Common\Core\BaseDateTime;
use Common\Core\IpAddress;

class BlackList extends BaseEntity {

    const TABLE_NAME = 'black_list';

    private $type;
    private $level;
    private $ip_address;
    private $user_id;
    private $status;
    private $released_by;
    private $released_at;
    private $remarks;

    function __construct()
    {
        parent::__construct();

        $this->ip_address = new IpAddress();
        $this->released_at = new BaseDateTime();
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setLevel($level) {
        $this->level = $level;
        return $this;
    }

    public function getLevel() {
        return $this->level;
    }

    public function setIpAddress(IpAddress $ip_address) {
        $this->ip_address = $ip_address;
        return $this;
    }

    public function getIpAddress() {
        return $this->ip_address;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setReleasedBy($released_by) {
        $this->released_by = $released_by;
        return $this;
    }

    public function getReleasedBy() {
        return $this->released_by;
    }

    public function setReleasedAt(BaseDateTime $released_at) {
        $this->released_at = $released_at;
        return $this;
    }

    public function getReleasedAt() {
        return $this->released_at;
    }

    public function setRemarks($remarks) {
        $this->remarks = $remarks;
        return $this;
    }

    public function getRemarks() {
        return $this->remarks;
    }

    public function jsonSerialize() {

        $json = parent::jsonSerialize();

        $json["type"] = $this->getType();
        $json["level"] = $this->getLevel();
        $json['ip_address'] = $this->getIpAddress()->getString();
        $json["user_id"] = $this->getUserId();
        $json["status"] = $this->getStatus();
        $json["released_by"] = $this->getReleasedBy();
        $json['released_at'] = $this->getReleasedAt()->getString();
        $json["remarks"] = $this->getRemarks();

        return $json;
    }

}
