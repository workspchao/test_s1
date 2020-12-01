<?php

namespace Common\Helper;

interface FieldEncryptionInterface
{
    public function encrypt($rawField);
    public function decrypt($encryptedField);
}
