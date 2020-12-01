<?php

namespace Common\Helper;

use phpseclib\Crypt\RSA;
use Common\Helper\FieldEncryptionInterface;

/**
 * @package Security
 */
class RSAFieldEncryption implements FieldEncryptionInterface
{
    private $rsa;
    private $privateKey;
    private $publicKey;

    protected static $_instance = null;

    public static function build()
    {
        if( self::$_instance == NULL )
        {
            $priv_key = FILESDIR . 'rsa_2048_priv.pem';
            $pub_key = FILESDIR . 'rsa_2048_pub.pem';
            
            self::$_instance = new RSAFieldEncryption($pub_key, $priv_key);
        }

        return self::$_instance;
    }
    
    public function __construct($publicKey, $privateKey)
    {
        if (!file_exists($publicKey)) {
            throw new \Exception("Public key doesnt exists! Aborting...", 1);
        }

        if (!file_exists($privateKey)) {
            throw new \Exception("Private key doesnt exists! Aborting...", 1);
        }
        
        $this->publicKey = file_get_contents($publicKey);
        $this->privateKey = file_get_contents($privateKey);

        $this->rsa = new RSA();
        $this->rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
    }

    /**
     * decrypt by private key
     * @param type $encodedString
     * @return type
     */
    public function decrypt($encodedString)
    {
        $this->rsa->loadKey($this->privatekey);
        
        $ciphertext = base64_decode($encodedString);
        return @$this->rsa->decrypt($ciphertext);
    }

    /**
     * encrypt by public key
     * @param type $rawString
     * @return type
     */
    public function encrypt($rawString)
    {
        $this->rsa->loadKey($this->publicKey);
        
        $encrypted_str = @$this->rsa->encrypt($rawString);
        return base64_encode($encrypted_str);
    }
}
