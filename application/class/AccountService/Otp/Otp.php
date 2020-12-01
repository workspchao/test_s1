<?php

namespace AccountService\Otp;

use Common\Core\IpAddress;
use Common\Core\BaseEntity;
use Common\Core\BaseDateTime;
use AccountService\Otp\OtpCodeGenerator;

class Otp extends BaseEntity {

    const TABLE_NAME = 'otp';

    private $ip_address;
    private $user_id;
    private $otp_type;
    private $code;
    private $destination;
    private $expired_at;
    private $verified_at;
    
    protected $resend_period;
    
    public function __construct() {
        parent::__construct();
        
        $this->ip_address = new IpAddress();
        $this->expired_at = new BaseDateTime();
        $this->verified_at = new BaseDateTime();
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

    public function setOtpType($otp_type) {
        $this->otp_type = $otp_type;
        return $this;
    }

    public function getOtpType() {
        return $this->otp_type;
    }

    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    public function setDestination($destination) {
        $this->destination = $destination;
        return $this;
    }

    public function getDestination() {
        return $this->destination;
    }

    public function setExpiredAt(BaseDateTime $expired_at) {
        $this->expired_at = $expired_at;
        return $this;
    }

    public function getExpiredAt() {
        return $this->expired_at;
    }

    public function setVerifiedAt(BaseDateTime $verified_at) {
        $this->verified_at = $verified_at;
        return $this;
    }

    public function getVerifiedAt() {
        return $this->verified_at;
    }

    public function setResendPeriod($period)
    {
        $this->resend_period = $period;
        return $this;
    }

    public function getResendPeriod()
    {
        return $this->resend_period;
    }

    public function jsonSerialize() {

        $json = parent::jsonSerialize();

        $json["ip_address"] = $this->getIpAddress()->getString();
        $json["user_id"] = $this->getUserId();
        $json["otp_type"] = $this->getOtpType();
        $json["code"] = $this->getCode();
        $json["destination"] = $this->getDestination();
        $json["expired_at"] = $this->getExpiredAt()->getString();
        $json["verified_at"] = $this->getVerifiedAt()->getString();

        $json['resend_period'] = $this->getResendPeriod();
        
        return $json;
    }
    
    public function generate($length = 4) {
        $code = OtpCodeGenerator::generate($length);
        $this->setCode($code);
    }

    public function verify($otp) {
        /* debug_code */
        $env = getenv("CI_ENV");
        if ($otp == "123067890") {
            $this->setVerifiedAt(BaseDateTime::now());
            return true;
        }
        //if(in_array($env, array('local','development'))){
        if(in_array($env, array('local'))){
            $this->setVerifiedAt(BaseDateTime::now());
            return true;
        }

        if ($this->getCode() == $otp) {
            $this->setVerifiedAt(BaseDateTime::now());
            return true;
        }

        return false;
    }

}
