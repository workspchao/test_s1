<?php

namespace Common\Microservice\WeixinService;

use Common\Core\BaseEntityCollection;
use Common\Helper\MicroserviceHelper\MicroserviceHelper;
use Common\Helper\ResponseMessage;
use Common\Helper\ResponseHeader;
use Common\Core\BaseDateTime;

class WeixinAppService extends WeixinBaseService {

    protected static $_instance = NULL;
    protected $_microserviceHelper;

    function __construct() {
        if (!$base_url = getenv('WEIXIN_SERVICE_URL'))
            throw new \Exception('Weixin Service URL Not Defined');

        $this->_microserviceHelper = new MicroserviceHelper(array('base_url' => $base_url));
    }

    public static function build() {
        if (self::$_instance == NULL) {
            self::$_instance = new WeixinAppService();
        }
        return self::$_instance;
    }

    public static function reset() {
        self::$_instance = NULL;
    }

    /**
     * 登录 - auth.code2Session - 登录凭证校验
     * 通过 code 获取 open_id 和 session_key
     * @param type $code
     * @param type $appId
     * @param type $appKey
     */
    public function getSessionKey($code, $appid, $secret, $grant_type = 'authorization_code') {
        //GET https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code

        $option = array(
            'method' => 'get',
            'uri' => 'sns/jscode2session',
            'param' => array(
                'appid' => $appid,
                'secret' => $secret,
                'js_code' => $code,
                'grant_type' => $grant_type,
            )
        );

        //{"openid": "用户唯一标识","session_key": "会话密钥", "unionid": "用户在开放平台的唯一标识符，在满足 UnionID 下发条件的情况下会返回，详见 UnionID 机制说明。"}
        //{"errcode": "错误码","errmsg": "错误信息"}

        if ($resp = $this->_microserviceHelper->call($option)) {
            if (is_string($resp)) {
                $resp = json_decode($resp);
            }

            if (is_object($resp)) {
                if (isset($resp->errcode)) {
                    log_message("error", "WeixinAppService - getSessionKey fail 101 - " . json_encode($resp));
                    return false;
                }
                //return object
                return $resp;
            }

            if (is_array($resp)) {
                if (isset($resp['errcode'])) {
                    log_message("error", "WeixinAppService - getSessionKey fail 102 - " . json_encode($resp));
                    return false;
                }
                //return array
                return $resp;
            }
        }
        $resp = $this->_microserviceHelper->getLastReponse();
        log_message("error", "WeixinAppService - getSessionKey fail 103 - " . json_encode($resp));
        return false;
    }

}
