<?php

namespace AccountService\AccessToken;

use Common\Core\BaseEntity;
use Common\Core\BaseDateTime;
use AccountService\LoginAccount\LoginAccount;

class AccessToken extends BaseEntity {

    const TABLE_NAME = 'access_token';

    private $user_id;
    private $login_account_id;
    private $token;
    private $expired_at;
    
    function __construct() {
        $this->expired_at = new BaseDateTime();
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

    public function setToken($token) {
        $this->token = $token;
        return $this;
    }

    public function getToken() {
        return $this->token;
    }

    public function setExpiredAt($expired_at) {
        $this->expired_at = $expired_at;
        return $this;
    }

    public function getExpiredAt() {
        return $this->expired_at;
    }

    public function jsonSerialize() {

        $json = parent::jsonSerialize();

        $json["user_id"] = $this->getUserId();
        $json["login_account_id"] = $this->getLoginAccountId();
        $json["token"] = $this->getToken();
        $json["expired_at"] = $this->getExpiredAt();
        
        return $json;
    }
    
    public function generate()
    {
        $token = md5(base64_encode(pack('N6', mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), uniqid())));
        $this->setToken($token);
        return $token;
    }

    public static function createFromLoginAccount(LoginAccount $account, $expiredInMinute = NULL)
    {
        $accessToken = new AccessToken();

        //$accessToken->setId(GuidGenerator::generate());
        $accessToken->setUserId($account->getUserId());
        $accessToken->setLoginAccountId($account->getId());
        
        if( $expiredInMinute != NULL )
        {
            $dt = BaseDateTime::now();
            $dt->addMinute($expiredInMinute);
            $accessToken->setExpiredAt($dt);
        }

        $accessToken->generate();

        return $accessToken;
    }
    
    
    /**
     * 
     * @return boolean (true:expired, false:valid)
     */
    public function isExpired()
    {
        //if expired_at is null, it always valid
        if( $this->expired_at->getUnix() == NULL )
            return false;

        return ( $this->expired_at->getUnix() <= BaseDateTime::now()->getUnix() );
    }

    /**
     * 
     * @return boolean (true:valid, false:invalid)
     */
    public function isValid() {
        return $this->isExpired() == false;
    }

    public function setExpired() {
        $this->setExpiredAt(BaseDateTime::now());
        return $this;
    }

}
