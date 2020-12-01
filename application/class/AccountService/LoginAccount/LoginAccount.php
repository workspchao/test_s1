<?php

namespace AccountService\LoginAccount;

use Common\Core\BaseEntity;
use Common\Core\BaseDateTime;
use Common\ValueObject\PasswordObj;
use Common\Helper\PasswordHasher;

class LoginAccount extends BaseEntity {

    const TABLE_NAME = 'login_account';

    private $user_id;
    private $login_type;
    private $username;
    private $salt;
    private $password;
    private $app_id;

    function __construct() {
        parent::__construct();
        
        $this->password = new PasswordObj;
    }
    
    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setLoginType($login_type) {
        $this->login_type = $login_type;
        return $this;
    }

    public function getLoginType() {
        return $this->login_type;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setPassword(PasswordObj $password) {
        $this->password = $password;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setAppId($app_id) {
        $this->app_id = $app_id;
        return $this;
    }

    public function getAppId() {
        return $this->app_id;
    }

    public function jsonSerialize() {

        $json = parent::jsonSerialize();

        $json["user_id"] = $this->getUserId();
        $json["login_type"] = $this->getLoginType();
        $json["username"] = $this->getUsername();
        $json['salt'] = $this->getPassword()->getSalt();
        $json['password'] = $this->getPassword()->getPassword();
        $json['expired_at'] = $this->getPassword()->getExpiredAt()->getString();
        $json["app_id"] = $this->getAppId();

        return $json;
    }
    
    public function authenticate($password)
    {
        return PasswordHasher::compare($password, $this->getPassword()->getSalt(), $this->getPassword()->getPassword());
    }

}
