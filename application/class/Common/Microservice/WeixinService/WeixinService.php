<?php

namespace Common\Microservice\WeixinService;

use Common\Core\BaseEntityCollection;
use Common\Helper\MicroserviceHelper\MicroserviceHelper;
use Common\Helper\ResponseMessage;
use Common\Helper\ResponseHeader;
use Common\Core\BaseDateTime;

class WeixinService
{
    protected $_microserviceHelper;

    function __construct()
    {
        if (!$base_url = getenv('WEIXIN_SERVICE_URL'))
            throw new \Exception('Weixin Service URL Not Defined');

        $this->_microserviceHelper = new MicroserviceHelper(array('base_url' => $base_url));
    }
    
    /**
     * 通过 code 获取 access_token
     * @param type $code
     * @param type $appId
     * @param type $appKey
     */
    public function getAccessToken($code, $appid, $secret, $grant_type = 'authorization_code'){
        //GET https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code

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
        
        //{"access_token": "ACCESS_TOKEN","expires_in": 7200,"refresh_token": "REFRESH_TOKEN","openid": "OPENID","scope": "SCOPE"}
        //{"errcode": 40029,"errmsg": "invalid code"}
        
        if($resp = $this->_microserviceHelper->call($option)){
            if(is_string($resp)){
                $resp = json_decode($resp);
            }
            
            if(is_object($resp)){
                if(isset($resp->errcode)){
                    log_message("error", "wxapi - getAccessToken fail 101 - " . json_encode($resp));
                    return false;
                }
                //return object
                return $resp;
            }
            
            if(is_array($resp)){
                if(isset($resp['errcode'])){
                    log_message("error", "wxapi - getAccessToken fail 102 - " . json_encode($resp));
                    return false;
                }
                //return array
                return $resp;
            }
        }
        $resp = $this->_microserviceHelper->getLastReponse();
        log_message("error", "wxapi - getAccessToken fail 103 - " . json_encode($resp));
        return false;
    }

    /**
     * 刷新或续期 access_token 使用
     * @param type $appId   
     * @param type $refreshToken
     * @param type $grantType
     * @return boolean
     */
    public function refreshToken($appid, $refresh_token, $grant_type = 'refresh_token'){
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
        
        //{"access_token": "ACCESS_TOKEN","expires_in": 7200,"refresh_token": "REFRESH_TOKEN","openid": "OPENID","scope": "SCOPE"}
        //{"errcode": 40029,"errmsg": "invalid code"}
        if($resp = $this->_microserviceHelper->call($option)){
            if(is_string($resp)){
                $resp = json_decode($resp);
            }
            
            if(is_object($resp)){
                if(isset($resp->errcode)){
                    log_message("error", "wxapi - refreshToken fail 101 - " . json_encode($resp));
                    return false;
                }
                //return object
                return $resp;
            }
            
            if(is_array($resp)){
                if(isset($resp['errcode'])){
                    log_message("error", "wxapi - refreshToken fail 102 - " . json_encode($resp));
                    return false;
                }
                //return array
                return $resp;
            }
        }
        $resp = $this->_microserviceHelper->getLastReponse();
        log_message("error", "wxapi - refreshToken fail 103 - " . json_encode($resp));
        return false;
    }

    /**
     * 检验授权凭证（access_token）是否有效
     * @param type $access_token   
     * @param type $openid
     * @return boolean
     */
    public function checkAccessToken($access_token, $openid){
        //GET https://api.weixin.qq.com/sns/auth?access_token=ACCESS_TOKEN&openid=OPENID

        $option = array(
            'method' => 'get',
            'uri' => 'sns/auth',
            'param' => array(
                'access_token' => $access_token,
                'openid' => $openid,
            )
        );
        
        //{"errcode": 0,"errmsg": "ok"}
        //{"errcode": 40003,"errmsg": "invalid openid"}
        if($resp = $this->_microserviceHelper->call($option)){
            if(is_string($resp)){
                $resp = json_decode($resp);
            }
            
            if(is_object($resp)){
                if(isset($resp->errcode) && $resp->errcode > 0){
                    log_message("error", "wxapi - checkAccessToken fail 101 - " . json_encode($resp));
                    return false;
                }
                //return object
                return $resp;
            }
            
            if(is_array($resp)){
                if(isset($resp['errcode']) && $resp['errcode'] > 0){
                    log_message("error", "wxapi - checkAccessToken fail 102 - " . json_encode($resp));
                    return false;
                }
                //return array
                return $resp;
            }
        }
        $resp = $this->_microserviceHelper->getLastReponse();
        log_message("error", "wxapi - checkAccessToken fail 103 - " . json_encode($resp));
        return false;
    }

    /**
     * 获取用户个人信息（UnionID 机制）
     * @param type $access_token
     * @param type $openid
     * @return boolean
     */
    public function getUserInfo($access_token, $openid){
        //GET https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID
        //{
        //  "openid": "OPENID",
        //  "nickname": "NICKNAME",
        //  "sex": 1,
        //  "province": "PROVINCE",
        //  "city": "CITY",
        //  "country": "COUNTRY",
        //  "headimgurl": "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
        //  "privilege": ["PRIVILEGE1", "PRIVILEGE2"],
        //  "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
        //}
        
        //参数	说明
        //openid        普通用户的标识，对当前开发者帐号唯一
        //nickname	普通用户昵称
        //sex           普通用户性别，1 为男性，2 为女性
        //province	普通用户个人资料填写的省份
        //city          普通用户个人资料填写的城市
        //country	国家，如中国为 CN
        //headimgurl	用户头像，最后一个数值代表正方形头像大小（有 0、46、64、96、132 数值可选，0 代表 640*640 正方形头像），用户没有头像时该项为空
        //privilege	用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
        //unionid	用户统一标识。针对一个微信开放平台帐号下的应用，同一用户的 unionid 是唯一的。
        $option = array(
            'method' => 'get',
            'uri' => 'sns/userinfo',
            'param' => array(
                'access_token' => $access_token,
                'openid' => $openid,
            )
        );
        
        if($resp = $this->_microserviceHelper->call($option)){
            if(is_string($resp)){
                $resp = json_decode($resp);
            }
            
            if(is_object($resp)){
                if(isset($resp->errcode)){
                    log_message("error", "wxapi - getUserInfo fail 101 - " . json_encode($resp));
                    return false;
                }
                //return object
                return $resp;
            }
            
            if(is_array($resp)){
                if(isset($resp['errcode'])){
                    log_message("error", "wxapi - getUserInfo fail 102 - " . json_encode($resp));
                    return false;
                }
                //return array
                return $resp;
            }
        }
        $resp = $this->_microserviceHelper->getLastReponse();
        log_message("error", "wxapi - getUserInfo fail 103 - " . json_encode($resp));
        return false;
    }
}
