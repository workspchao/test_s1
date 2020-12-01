<?php

namespace AccountService\Account;


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class AliMobileAuthService {

    protected static $_instance = NULL;
    
    function __construct(){

        $accessKeyId = getenv("FILE_CLOUD_KEY");
        $accessKeySecret = getenv("FILE_CLOUD_SECRET");
        AlibabaCloud::accessKeyClient($accessKeyId, $accessKeySecret)
                        ->regionId('cn-hangzhou')
                        ->asDefaultClient();
    }

    public static function build()
    {
        if( self::$_instance == NULL )
        {
            self::$_instance = new AliMobileAuthService();
        }

        return self::$_instance;
    }

    public function mobileAuth($token){
        try {
            $result = AlibabaCloud::rpc()
                                  ->product('Dypnsapi')
                                  ->scheme('https') // https | http
                                  ->version('2017-05-25')
                                  ->action('GetMobile')
                                  ->method('POST')
                                  ->host('dypnsapi.aliyuncs.com')
                                  ->options([
                                                'query' => [
                                                  'RegionId' => "cn-hangzhou",
                                                  'AccessToken' => $token,
                                                ],
                                            ])
                                  ->request();

            return $result->toArray();
        } catch (ClientException $e) {
            log_message("error", "AliMobileAuthService mobile auth fail: ".$e->getErrorMessage());
            return false;
        } catch (ServerException $e) {
            log_message("error", "AliMobileAuthService mobile auth fail: ".$e->getErrorMessage());
            return false;
        }

        return false;
    }
}