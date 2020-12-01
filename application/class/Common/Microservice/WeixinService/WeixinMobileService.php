<?php

namespace Common\Microservice\WeixinService;

use Common\Core\BaseEntityCollection;
use Common\Helper\MicroserviceHelper\MicroserviceHelper;
use Common\Helper\ResponseMessage;
use Common\Helper\ResponseHeader;
use Common\Core\BaseDateTime;

/**
 * 微信移动应用接口
 * 第一步：请求 CODE (移动应用微信授权登录 - 移动应用中调SDK获取临时票据)
 * 第二步：通过 code 获取 access_token 和 oepnid
 * 
 */
class WeixinMobileService extends WeixinBaseService {

    protected static $_instance = NULL;
    protected $_microserviceHelper;

    function __construct() {
        parent::__construct();
        
    }

    public static function build() {
        if (self::$_instance == NULL) {
            self::$_instance = new WeixinMobileService();
        }
        return self::$_instance;
    }

    public static function reset() {
        self::$_instance = NULL;
    }
    
    /**
     * 第二步：通过 code 获取 access_token 和 oepnid
     * @param type $code
     * @param type $appid
     * @param type $secret
     * @param type $grant_type
     * @return boolean|{
    "access_token": "ACCESS_TOKEN",
    "expires_in": 7200,
    "refresh_token": "REFRESH_TOKEN",
    "openid": "OPENID",
    "scope": "SCOPE",
    "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"}
     * 
     */
    public function getUserAccessToken($code, $appid, $secret, $grant_type = 'authorization_code'){
        
        //https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code
        $option = array(
            'method' => 'get',
            'uri' => 'sns/oauth2/access_token',
            'param' => array(
                'appid' => $appid,
                'secret' => $secret,
                'code' => $code,
                'grant_type' => $grant_type
            )
        );
        

        //正确返回
        //{
        //    "access_token": "ACCESS_TOKEN",
        //    "expires_in": 7200,
        //    "refresh_token": "REFRESH_TOKEN",
        //    "openid": "OPENID",
        //    "scope": "SCOPE",
        //    "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
        //}
        if(ENVIRONMENT == "local"){
            $resp = array(
                    "access_token" => "ACCESS_TOKEN",
                    "expires_in" => 7200,
                    "refresh_token" => "REFRESH_TOKEN",
                    "openid" => "OPENID_".$code,
                    "scope" => "SCOPE",
                    "unionid" => "o6_bmasdasdsad6_2sgVt7hMZOPfL"
                );
            return $resp;
        }
        
        if ($resp = $this->_microserviceHelper->call($option)) {
            if (is_string($resp)) {
                $resp = json_decode($resp);
            }

            if (is_object($resp)) {
                //convert to array
                $resp = json_decode(json_encode($resp), true);
                
                log_message("debug", "WeixinMobileService - convert to array" . json_encode($resp));
            }
            
            if (is_array($resp)) {
                if (isset($resp['errcode'])) {
                    log_message("error", "WeixinMobileService - getUserAccessToken fail 101 - " . json_encode($resp));
                    return false;
                }
                //return array
                return $resp;
            }
        }
        $resp = $this->_microserviceHelper->getLastReponse();
        log_message("error", "WeixinMobileService - getUserAccessToken fail 102 - " . json_encode($resp));
        return false;
    }

    /**
     * 刷新或续期 access_token 使用
     * @param type $appid
     * @param type $refresh_token
     * @param type $grant_type
     * @return boolean|string
     */
    public function refreshUserAccessToken($appid, $refresh_token, $grant_type = 'refresh_token'){
        //GET https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=APPID&grant_type=refresh_token&refresh_token=REFRESH_TOKEN
        $option = array(
            'method' => 'get',
            'uri' => 'sns/oauth2/refresh_token',
            'param' => array(
                'appid' => $appid,
                'grant_type' => $grant_type,
                'refresh_token' => $refresh_token,
            )
        );
        
        
        //正确返回
        //{
        //    "access_token": "ACCESS_TOKEN",
        //    "expires_in": 7200,
        //    "refresh_token": "REFRESH_TOKEN",
        //    "openid": "OPENID",
        //    "scope": "SCOPE"
        //}

        if(ENVIRONMENT == "local"){
            $resp = array(
                    "access_token" => "ACCESS_TOKEN",
                    "expires_in" => 7200,
                    "refresh_token" => "REFRESH_TOKEN",
                    "openid" => "OPENID",
                    "scope" => "SCOPE",
                );
            return $resp;
        }
        
        if ($resp = $this->_microserviceHelper->call($option)) {
            if (is_string($resp)) {
                $resp = json_decode($resp);
            }

            if (is_object($resp)) {
                //convert to array
                $resp = json_decode(json_encode($resp), true);
                
                log_message("debug", "WeixinMobileService - convert to array" . json_encode($resp));
            }
            
            if (is_array($resp)) {
                if (isset($resp['errcode'])) {
                    log_message("error", "WeixinMobileService - refreshUserAccessToken fail 201 - " . json_encode($resp));
                    return false;
                }
                //return array
                return $resp;
            }
        }
        $resp = $this->_microserviceHelper->getLastReponse();
        log_message("error", "WeixinMobileService - refreshUserAccessToken fail 202 - " . json_encode($resp));
        return false;
    }
    
    /**
     * 检验授权凭证（access_token）是否有效
     * @param type $access_token
     * @param type $openid
     * @return boolean|string
     */
    public function checkUserAccessToken($access_token, $openid){
       //GET https://api.weixin.qq.com/sns/auth?access_token=ACCESS_TOKEN&openid=OPENID
        $option = array(
            'method' => 'get',
            'uri' => 'sns/auth',
            'param' => array(
                'access_token' => $access_token,
                'openid' => $openid,
            )
        );
        
        
        //正确返回
        //{
        //    "errcode": 0,
        //    "errmsg": "ok"
        //}
        
        //错误返回
        //{
        //    "errcode": 40003,
        //    "errmsg": "invalid openid"
        //}

        if(ENVIRONMENT == "local"){
            $resp = array(
                    "errcode" => 0,
                    "errmsg" => "ok"
                );
            return $resp;
        }
        
        if ($resp = $this->_microserviceHelper->call($option)) {
            if (is_string($resp)) {
                $resp = json_decode($resp);
            }

            if (is_object($resp)) {
                //convert to array
                $resp = json_decode(json_encode($resp), true);
                
                log_message("debug", "WeixinMobileService - convert to array" . json_encode($resp));
            }
            
            if (is_array($resp)) {
                if (isset($resp['errcode']) && $resp['errcode'] > 0) {
                    log_message("error", "WeixinMobileService - checkUserAccessToken fail 301 - " . json_encode($resp));
                    return false;
                }
                //return array
                return $resp;
            }
        }
        $resp = $this->_microserviceHelper->getLastReponse();
        log_message("error", "WeixinMobileService - checkUserAccessToken fail 302 - " . json_encode($resp));
        return false;
    }
    
    /**
     * 获取用户个人信息（UnionID 机制）
     * 此接口用于获取用户个人信息。开发者可通过 OpenID 来获取用户基本信息。特别需要注意的是，如果开发者拥有多个移动应用、网站应用和公众帐号，可通过获取用户基本信息中的 unionid 来区分用户的唯一性，因为只要是同一个微信开放平台帐号下的移动应用、网站应用和公众帐号，用户的 unionid 是唯一的。换句话说，同一用户，对同一个微信开放平台下的不同应用，unionid 是相同的。请注意，在用户修改微信头像后，旧的微信头像 URL 将会失效，因此开发者应该自己在获取用户信息后，将头像图片保存下来，避免微信头像 URL 失效后的异常情况。
     * @param type $access_token
     * @param type $openid
     * @param type $lang ('zh_CN', 'zh_TW', 'en', 'zh-CN')
     * @return boolean|string
     */
    public function getUserBaseInfo($access_token, $openid, $lang = 'zh-CN'){
       //GET https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID
        $option = array(
            'method' => 'get',
            'uri' => 'sns/userinfo',
            'param' => array(
                'access_token' => $access_token,
                'openid' => $openid,
            )
        );
        
        //正确返回
        //{
        //    "openid": "OPENID",
        //    "nickname": "NICKNAME",
        //    "sex": 1,
        //    "province": "PROVINCE",
        //    "city": "CITY",
        //    "country": "COUNTRY",
        //    "headimgurl": "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
        //    "privilege": ["PRIVILEGE1", "PRIVILEGE2"],
        //    "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
        //}
        
        //openid	普通用户的标识，对当前开发者帐号唯一
        //nickname	普通用户昵称
        //sex	普通用户性别，1 为男性，2 为女性
        //province	普通用户个人资料填写的省份
        //city	普通用户个人资料填写的城市
        //country	国家，如中国为 CN
        //headimgurl	用户头像，最后一个数值代表正方形头像大小（有 0、46、64、96、132 数值可选，0 代表 640*640 正方形头像），用户没有头像时该项为空
        //privilege	用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
        //unionid	用户统一标识。针对一个微信开放平台帐号下的应用，同一用户的 unionid 是唯一的。

        //错误返回
        //{
        //    "errcode": 40003,
        //    "errmsg": "invalid openid"
        //}

        if(ENVIRONMENT == "local"){
            $resp = array(
                    "openid" => $openid,
                    "nickname" => "NICKNAME",
                    "sex" => 1,
                    "province" => "PROVINCE",
                    "city" => "CITY",
                    "country" => "COUNTRY",
                    "headimgurl" => "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
                    "privilege" => ["PRIVILEGE1", "PRIVILEGE2"],
                    "unionid" => " o6_bmasdasdsad6_2sgVt7hMZOPfL"
                );
            return $resp;
        }
        
        if ($resp = $this->_microserviceHelper->call($option)) {
            if (is_string($resp)) {
                $resp = json_decode($resp);
            }

            if (is_object($resp)) {
                //convert to array
                $resp = json_decode(json_encode($resp), true);
                
                log_message("debug", "WeixinMobileService - convert to array" . json_encode($resp));
            }
            
            if (is_array($resp)) {
                if (isset($resp['errcode'])) {
                    log_message("error", "WeixinMobileService - getUserBaseInfo fail 401 - " . json_encode($resp));
                    return false;
                }
                //return array
                return $resp;
            }
        }
        $resp = $this->_microserviceHelper->getLastReponse();
        log_message("error", "WeixinMobileService - getUserBaseInfo fail 402 - " . json_encode($resp));
        return false;
    }
    
    
}
