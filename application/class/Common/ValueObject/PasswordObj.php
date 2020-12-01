<?php

namespace Common\ValueObject;

use Common\Core\BaseDateTime;
use Common\Helper\PasswordHasher;
use Common\Helper\SaltGenerator;
use AccountService\PasswordPolicy\PasswordPolicy;
use AccountService\PasswordPolicy\PasswordGenerator;

class PasswordObj {

    protected $salt;
    protected $password;
    protected $expired_at;

    protected $generatedPassword;   //this required to store original string as to inform the user

    function __construct() {
        $this->expired_at = new BaseDateTime();
    }
    
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return true;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return true;
    }

    public function getPassword()
    {
        return $this->password;
    }
    
    public function setExpiredAt(BaseDateTime $dt)
    {
        $this->expired_at = $dt;
        return $this;
    }
    
    /**
     * 
     * @return BaseDateTime
     */
    public function getExpiredAt()
    {
        return $this->expired_at;
    }

    public function setGeneratedPassword($password)
    {
        $this->generatedPassword = $password;
        return true;
    }

    public function getGeneratedPassword()
    {
        return $this->generatedPassword;
    }

    public function setNewPassword($password, PasswordPolicy $policy = NULL)
    {
        if( $policy != NULL )
        {
//            if( !$policy->validate($password) )
//                return false;
//
            $this->setExpiredAt($policy->getPasswordExpiryDate());
        }

        $this->setSalt(SaltGenerator::generate());
        $this->setPassword(PasswordHasher::hash($password, $this->getSalt()));        
        return true;
    }

    public function generatePassword(PasswordPolicy $policy = NULL)
    {
        $this->setSalt(SaltGenerator::generate());
        $randomPassword = PasswordGenerator::generate(10, $policy);
        $this->setPassword(PasswordHasher::hash($randomPassword, $this->getSalt()));
        $this->setGeneratedPassword($randomPassword);
        //$this->setExpiredAt(BaseDateTime::now());  //expired it if its generated
        return $randomPassword;
    }
}