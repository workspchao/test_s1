<?php

namespace Common\Helper;

use Common\Helper\FieldEncryptionInterface;

require_once BASEPATH . 'libraries/Encryption.php';

class AES256Encryption implements FieldEncryptionInterface
{
    
    protected static $_instance;

    public static function build()
    {
        if( self::$_instance == NULL )
        {
            if( !$key = getenv('ENCRYPTION_KEY_256') )
                throw new \Exception('ENCRYPTION KEY IS Not Defined');

            self::$_instance = new AES256Encryption($key);
        }

        return self::$_instance;
    }
    
    protected $encryptor;
    
    function __construct($key)
    {
        $params = array(
            'driver' => 'openssl',
            'cipher' => 'aes-256',
            'mode' => 'cbc',
            'key' => $key
        );

        $this->encryptor = new \CI_Encryption($params);
    }

    public function encrypt($rawField)
    {
        return $this->encryptor->encrypt($rawField);
    }

    public function decrypt($encryptedField)
    {
        return $this->encryptor->decrypt($encryptedField);
    }
}
