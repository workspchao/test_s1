<?php

namespace Common\Helper;

use Common\Helper\FieldEncryptionInterface;

//require_once BASEPATH . 'libraries/Encryption.php';
require_once BASEPATH . 'libraries/AES.php';

class AES128Encryption implements FieldEncryptionInterface
{
    
    protected static $_instance;
    private $key = null;

    public static function build()
    {
        if( self::$_instance == NULL )
        {
            if( !$key = getenv('ENCRYPTION_KEY_128') )
                throw new \Exception('ENCRYPTION KEY IS Not Defined');

            self::$_instance = new AES128Encryption($key);
        }

        return self::$_instance;
    }
    
    protected $encryptor;
    
    function __construct($key)
    {
        $params = array(
            'driver' => 'openssl',
            'cipher' => 'aes-128',
            'mode' => 'cbc',
            'key' => $key
        );
        $this->key = $key;

        $this->encryptor = new \CI_AES();
    }

    public function encrypt($rawField)
    {
        return $this->encryptor->encrypt($rawField, $this->key);
    }

    public function decrypt($encryptedField)
    {
        return $this->encryptor->decrypt($encryptedField, $this->key);
    }
}
