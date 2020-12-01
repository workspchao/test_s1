<?php

namespace AccountService\LoginLog;

use Common\Core\BaseEntity;
use Common\Core\BaseDateTime;
use Common\Core\IpAddress;

class LoginLog extends BaseEntity {

    const TABLE_NAME = 'login_log';

    private $ip_address;
    private $address;
    private $lat;
    private $long;
    private $user_id;
    private $login_account_id;
    private $status;
    private $login_type;
    private $user_agent;
    private $attempt;

    function __construct()
    {
        parent::__construct();

        $this->ip_address = new IpAddress();
    }

    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setLat($lat) {
        $this->lat = $lat;
        return $this;
    }

    public function getLat() {
        return $this->lat;
    }

    public function setLong($long) {
        $this->long = $long;
        return $this;
    }

    public function getLong() {
        return $this->long;
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

    public function setLoginAccountId($login_account_id) {
        $this->login_account_id = $login_account_id;
        return $this;
    }

    public function getLoginAccountId() {
        return $this->login_account_id;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setLoginType($login_type) {
        $this->login_type = $login_type;
        return $this;
    }

    public function getLoginType() {
        return $this->login_type;
    }

    public function setUserAgent($user_agent) {
        $this->user_agent = $user_agent;
        return $this;
    }

    public function getUserAgent() {
        return $this->user_agent;
    }

    public function setAttempt($attempt) {
        $this->attempt = $attempt;
        return $this;
    }

    public function getAttempt() {
        return $this->attempt;
    }

    public function jsonSerialize() {

        $json = parent::jsonSerialize();

        $json["ip_address"] = $this->getIpAddress();
        $json["address"] = $this->getAddress();
        $json["lat"] = $this->getLat();
        $json["long"] = $this->getLong();

        $json["user_id"] = $this->getUserId();
        $json["login_account_id"] = $this->getLoginAccountId();
        $json["status"] = $this->getStatus();
        $json["login_type"] = $this->getLoginType();
        $json["user_agent"] = $this->getUserAgent();
        $json["attempt"] = $this->getAttempt();

        return $json;
    }

}
