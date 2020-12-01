<?php

namespace Common\Core;

use Common\Helper\FieldEncryptionInterface;

class EncryptedField
{

    protected $value;
    protected $encrypted_value;
    protected $hashed_value;

    protected $encryptor;

    function __construct(FieldEncryptionInterface $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    public function setValue($value)
    {
        $this->value = $value;
        $this->encrypted_value = $this->encodeValue($this->value);
        $this->hashed_value = $this->hashValue($this->value);
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setEncryptedValue($encrypted_value)
    {
        $this->encrypted_value = $encrypted_value;
        $this->value = $this->decodeValue($this->encrypted_value);
        $this->hashed_value = $this->hashValue($this->value);
        return $this;
    }

    public function getEncodedValue()
    {
        return $this->encrypted_value;
    }

    public function setHashedValue($hashed_value)
    {
        $this->hashed_value = $hashed_value;
        return $this;
    }

    public function getHashedValue()
    {
        return $this->hashed_value;
    }

    protected function encodeValue($value)
    {
        return $this->encryptor->encrypt($value);
    }

    protected function decodeValue($value)
    {
        return $this->encryptor->decrypt($value);
    }

    protected function hashValue($value)
    {
        //all hashed value to be saved as lower case for a smarter search
        return hash('sha256', strtolower($value));
    }
}
