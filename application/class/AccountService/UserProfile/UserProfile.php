<?php

namespace AccountService\UserProfile;

use Common\Core\BaseEntity;
use Common\Core\BaseDateTime;
use Common\Core\IpAddress;

class UserProfile extends BaseEntity {

    const TABLE_NAME = 'user_profile';

    private $user_type;
    private $accountID;
    private $name;
    private $nick_name;
    private $mobile;
    private $avatar_url;
    private $status;
    private $user_group_id;
    private $remark;
    private $ip_address;
    private $channel;
    private $last_updated_at;
    private $last_share_at;
    private $last_login_at;

    function __construct()
    {
        parent::__construct();

        $this->ip_address = new IpAddress();
        $this->last_updated_at = new BaseDateTime();
        $this->last_share_at = new BaseDateTime();
        $this->last_login_at = new BaseDateTime();
    }

    public function setIpAddress(IpAddress $ip_address) {
        $this->ip_address = $ip_address;
        return $this;
    }

    public function getIpAddress() {
        return $this->ip_address;
    }

    public function setRemark($remark) {
        $this->remark = $remark;
        return $this;
    }

    public function getRemark() {
        return $this->remark;
    }

    public function setUserGroupId($user_group_id) {
        $this->user_group_id = $user_group_id;
        return $this;
    }

    public function getUserGroupId() {
        return $this->user_group_id;
    }

    public function setMobile($mobile) {
        $this->mobile = $mobile;
        return $this;
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function setUserType($user_type) {
        $this->user_type = $user_type;
        return $this;
    }

    public function getUserType() {
        return $this->user_type;
    }

    public function setAccountID($accountID) {
        $this->accountID = $accountID;
        return $this;
    }

    public function getAccountID() {
        return $this->accountID;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setNickName($nick_name) {
        $this->nick_name = $nick_name;
        return $this;
    }

    public function getNickName() {
        return $this->nick_name;
    }

    public function setAvatarUrl($avatar_url) {
        $this->avatar_url = $avatar_url;
        return $this;
    }

    public function getAvatarUrl() {
        return $this->avatar_url;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setChannel($channel){
        $this->channel = $channel;
        return $this;
    }
    
    public function getChannel(){
        return $this->channel;
    }
    
    public function setLastUpdatedAt(BaseDateTime $last_updated_at) {
        $this->last_updated_at = $last_updated_at;
        return $this;
    }

    public function getLastUpdatedAt() {
        return $this->last_updated_at;
    }

    public function setLastLoginAt(BaseDateTime $last_login_at) {
        $this->last_login_at = $last_login_at;
        return $this;
    }

    public function getLastLoginAt() {
        return $this->last_login_at;
    }

    public function setLastShareAt(BaseDateTime $last_share_at) {
        $this->last_share_at = $last_share_at;
        return $this;
    }

    public function getLastShareAt() {
        return $this->last_share_at;
    }

    public function jsonSerialize() {

        $json = parent::jsonSerialize();

        $json["user_type"] = $this->getUserType();
        $json["accountID"] = $this->getAccountID();
        $json["name"] = $this->getName();
        $json["nick_name"] = $this->getNickName();
        $json["avatar_url"] = $this->getAvatarUrl();
        $json["status"] = $this->getStatus();
        $json["mobile"] = $this->getMobile();

        $json["user_group_id"] = $this->getUserGroupId();
        $json["remark"] = $this->getRemark();
        $json["ip_address"] = $this->getIpAddress();

        $json["channel"] = $this->getChannel();
        
        $json["last_login_at"] = !$this->getLastLoginAt()->isNull() ? $this->getLastLoginAt()->getString() : null;
        $json["last_share_at"] = !$this->getLastShareAt()->isNull() ? $this->getLastShareAt()->getString() : null;
        $json["last_updated_at"] = !$this->getLastUpdatedAt()->isNull() ? $this->getLastUpdatedAt()->getString() : null;

        return $json;
    }

}
