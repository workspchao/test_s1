<?php

namespace Common\Helper;

use Common\Core\EncryptedField;
use Common\Core\BaseDateTime;

class EncryptedDateTime extends BaseDateTime
{

    protected $encrypted_dt;

    function __construct(FieldEncryptionInterface $encryptor)
    {
        $this->encrypted_dt = new EncryptedField($encryptor);
    }

    /**
     * 
     * @return EncryptedField
     */
    public function getEncryptedDateTime()
    {
        return $this->encrypted_dt;
    }

    public function setDateTimeString($dtStr, $format = null)
    {
        $dt = BaseDateTime::fromString($dtStr, $format);
        if (!$dt->isNull()) {
            parent::setDateTimeString($dtStr, $format);
            $this->encrypted_dt->setValue($this->dtUnix);
        }
    }

    public function setDateTimeUnix($dtUnix)
    {
        $dt = BaseDateTime::fromUnix($dtUnix);
        if (!$dt->isNull()) {
            parent::setDateTimeUnix($dtUnix);
            $this->encrypted_dt->setValue($this->dtUnix);
        }
    }

    public function getString()
    {
        parent::setDateTimeUnix($this->encrypted_dt->getValue());
        return parent::getString();
    }

    public function getUnix()
    {
        parent::setDateTimeUnix($this->encrypted_dt->getValue());
        return parent::getUnix();
    }
}
