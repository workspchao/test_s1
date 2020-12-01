<?php

namespace Common\Microservice\WeixinService;

use Common\Core\BaseEntityCollection;
use Common\Helper\MicroserviceHelper\MicroserviceHelper;
use Common\Helper\ResponseMessage;
use Common\Helper\ResponseHeader;
use Common\Core\BaseDateTime;

class WeixinBaseService
{
    protected $_microserviceHelper;

    function __construct()
    {
        if (!$base_url = getenv('WEIXIN_SERVICE_URL'))
            throw new \Exception('Weixin Service URL Not Defined');

        $this->_microserviceHelper = new MicroserviceHelper(array('base_url' => $base_url));
    }

    /**
     * 企业微信基本支持 - 获取 access_token 接口 /token
     * @param type $appid
     * @param type $secret
     * @param type $grant_type
     */
    public function getQyAccessToken($appid, $secret) {
       //GET https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET

        $this->_microserviceHelper = new MicroserviceHelper(array('base_url' => "https://qyapi.weixin.qq.com/"));

       $option = array(
           'method' => 'get',
           'uri' => 'cgi-bin/gettoken',
           'param' => array(
               'corpid' => $appid,
               'corpsecret' => $secret,
           )
       );

       if ($resp = $this->_microserviceHelper->call($option)) {
           if (is_string($resp)) {
               $resp = json_decode($resp);
           }

           if (is_object($resp)) {
               if (isset($resp->errcode) && !empty($resp->errcode)) {
                   log_message("error", "WeixinBaseService - getAccessToken fail 101 - " . json_encode($resp));
                   return false;
               }
               //return object
               return $resp;
           }

           if (is_array($resp)) {
               if (isset($resp['errcode'])) {
                   log_message("error", "WeixinBaseService - getAccessToken fail 102 - " . json_encode($resp));
                   return false;
               }
               //return array
               return $resp;
           }
       }
       $resp = $this->_microserviceHelper->getLastReponse();
       log_message("error", "WeixinBaseService - getAccessToken fail 103 - " . json_encode($resp));
       return false;
    }
    
    /**
     * 基本支持 - 获取 access_token 接口 /token
     * @param type $appid
     * @param type $secret
     * @param type $grant_type
     */
//    public function getAccessToken($appid, $secret, $grant_type = 'client_credential') {
//        //GET https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET
//
//        $option = array(
//            'method' => 'get',
//            'uri' => 'cgi-bin/token',
//            'param' => array(
//                'grant_type' => $grant_type,
//                'appid' => $appid,
//                'secret' => $secret,
//            )
//        );
//
//        //{"access_token": "ACCESS_TOKEN","expires_in": 7200}
//        //{"errcode": 40029,"errmsg": "invalid code"}
//
//        if ($resp = $this->_microserviceHelper->call($option)) {
//            if (is_string($resp)) {
//                $resp = json_decode($resp);
//            }
//
//            if (is_object($resp)) {
//                if (isset($resp->errcode)) {
//                    log_message("error", "WeixinBaseService - getAccessToken fail 101 - " . json_encode($resp));
//                    return false;
//                }
//                //return object
//                return $resp;
//            }
//
//            if (is_array($resp)) {
//                if (isset($resp['errcode'])) {
//                    log_message("error", "WeixinBaseService - getAccessToken fail 102 - " . json_encode($resp));
//                    return false;
//                }
//                //return array
//                return $resp;
//            }
//        }
//        $resp = $this->_microserviceHelper->getLastReponse();
//        log_message("error", "WeixinBaseService - getAccessToken fail 103 - " . json_encode($resp));
//        return false;
//    }
//    
    /**
     * 用户管理  - 获取用户基本信息接口 /user/info
     * @param type $openid
     * @param type $access_token
     */
//    public function getUserInfo($openid, $access_token){
//        //GET https://api.weixin.qq.com/cgi-bin/user/info?access_token=27_sKJMZC7PGNlhClw_u75irK3MDqfaATYUI-MEYOK6Mxjvjr5Ag8C5IPy3ydq0S42_cAtii0Zr5sxdxJCq0inHd9rkiQiZ1Y1I1ssCrYGBYBGglQw4q1VqJu4I8AgMHReACAIDN&openid=asdf
//
//        $option = array(
//            'method' => 'get',
//            'uri' => 'cgi-bin/user/info',
//            'param' => array(
//                'access_token' => $access_token,
//                'openid' => $openid,
//            )
//        );
//
//        //{
//        //"subscribe": 1, //用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
//        //"openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M", //用户的标识，对当前公众号唯一
//        //"nickname": "Band", //用户的昵称
//        //"sex": 1, //用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
//        //"language": "zh_CN", //用户的语言，简体中文为zh_CN
//        //"city": "广州", //用户所在城市
//        //"province": "广东", //用户所在省份
//        //"country": "中国", //用户所在国家
//        //"headimgurl":"http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
//        //"subscribe_time": 1382694957,//用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
//        //"unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"//只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
//        //"remark": "",//公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
//        //"groupid": 0,//用户所在的分组ID（兼容旧的用户分组接口）
//        //"tagid_list":[128,2],//用户被打上的标签ID列表
//        //"subscribe_scene": "ADD_SCENE_QR_CODE",//返回用户关注的渠道来源，ADD_SCENE_SEARCH 公众号搜索，ADD_SCENE_ACCOUNT_MIGRATION 公众号迁移，ADD_SCENE_PROFILE_CARD 名片分享，ADD_SCENE_QR_CODE 扫描二维码，ADD_SCENE_PROFILE_ LINK 图文页内名称点击，ADD_SCENE_PROFILE_ITEM 图文页右上角菜单，ADD_SCENE_PAID 支付后关注，ADD_SCENE_OTHERS 其他
//        //"qr_scene": 98765,//二维码扫码场景（开发者自定义）
//        //"qr_scene_str": ""//二维码扫码场景描述（开发者自定义）
//        //}
//        
//        //{"errcode":40013,"errmsg":"invalid appid"}
//
//        if ($resp = $this->_microserviceHelper->call($option)) {
//            if (is_string($resp)) {
//                $resp = json_decode($resp);
//            }
//
//            if (is_object($resp)) {
//                if (isset($resp->errcode)) {
//                    log_message("error", "WeixinBaseService - getUserInfo fail 101 - " . json_encode($resp));
//                    return false;
//                }
//                //return object
//                return $resp;
//            }
//
//            if (is_array($resp)) {
//                if (isset($resp['errcode'])) {
//                    log_message("error", "WeixinBaseService - getUserInfo fail 102 - " . json_encode($resp));
//                    return false;
//                }
//                //return array
//                return $resp;
//            }
//        }
//        $resp = $this->_microserviceHelper->getLastReponse();
//        log_message("error", "WeixinBaseService - getUserInfo fail 103 - " . json_encode($resp));
//        return false;
//    }
//    
    
    /**
     * 
     * @param type $user_list {'user_list':[{"openid":"openid1","lang":"zh_CN"},{"openid":"openid2","lang":"zh_CN"}]}
     * @param type $access_token
     * @return boolean
     */
//    public function getUserInfoList($user_list, $access_token){
//        //POST https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=ACCESS_TOKEN
//
//        $option = array(
//            'method' => 'post',
//            'uri' => 'cgi-bin/user/info/batchget?access_token=' . $access_token,
//            'param' => array(
//                'user_list' => $user_list
//            )
//        );
//
////{
////    "user_info_list": [
////        {
////            "subscribe": 1, //用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
////            "openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M", //用户的标识，对当前公众号唯一
////            "nickname": "Band", //用户的昵称
////            "sex": 1, //用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
////            "language": "zh_CN", //用户的语言，简体中文为zh_CN
////            "city": "广州", //用户所在城市
////            "province": "广东", //用户所在省份
////            "country": "中国", //用户所在国家
////            "headimgurl":"http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
////            "subscribe_time": 1382694957,//用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
////            "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"//只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
////            "remark": "",//公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
////            "groupid": 0,//用户所在的分组ID（兼容旧的用户分组接口）
////            "tagid_list":[128,2],//用户被打上的标签ID列表
////            "subscribe_scene": "ADD_SCENE_QR_CODE",//返回用户关注的渠道来源，ADD_SCENE_SEARCH 公众号搜索，ADD_SCENE_ACCOUNT_MIGRATION 公众号迁移，ADD_SCENE_PROFILE_CARD 名片分享，ADD_SCENE_QR_CODE 扫描二维码，ADD_SCENE_PROFILE_ LINK 图文页内名称点击，ADD_SCENE_PROFILE_ITEM 图文页右上角菜单，ADD_SCENE_PAID 支付后关注，ADD_SCENE_OTHERS 其他
////            "qr_scene": 98765,//二维码扫码场景（开发者自定义）
////            "qr_scene_str": ""//二维码扫码场景描述（开发者自定义）
////        },
////        {
////            "subscribe": 1, //用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
////            "openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M", //用户的标识，对当前公众号唯一
////            "nickname": "Band", //用户的昵称
////            "sex": 1, //用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
////            "language": "zh_CN", //用户的语言，简体中文为zh_CN
////            "city": "广州", //用户所在城市
////            "province": "广东", //用户所在省份
////            "country": "中国", //用户所在国家
////            "headimgurl":"http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
////            "subscribe_time": 1382694957,//用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
////            "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"//只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
////            "remark": "",//公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
////            "groupid": 0,//用户所在的分组ID（兼容旧的用户分组接口）
////            "tagid_list":[128,2],//用户被打上的标签ID列表
////            "subscribe_scene": "ADD_SCENE_QR_CODE",//返回用户关注的渠道来源，ADD_SCENE_SEARCH 公众号搜索，ADD_SCENE_ACCOUNT_MIGRATION 公众号迁移，ADD_SCENE_PROFILE_CARD 名片分享，ADD_SCENE_QR_CODE 扫描二维码，ADD_SCENE_PROFILE_ LINK 图文页内名称点击，ADD_SCENE_PROFILE_ITEM 图文页右上角菜单，ADD_SCENE_PAID 支付后关注，ADD_SCENE_OTHERS 其他
////            "qr_scene": 98765,//二维码扫码场景（开发者自定义）
////            "qr_scene_str": ""//二维码扫码场景描述（开发者自定义）
////        }
////    ]
////}
//     
//        //{"errcode":40013,"errmsg":"invalid appid"}
//
//        if ($resp = $this->_microserviceHelper->call($option)) {
//            if (is_string($resp)) {
//                $resp = json_decode($resp);
//            }
//
//            if (is_object($resp)) {
//                if (isset($resp->errcode)) {
//                    log_message("error", "WeixinBaseService - getUserInfo fail 101 - " . json_encode($resp));
//                    return false;
//                }
//                //return object
//                return $resp;
//            }
//
//            if (is_array($resp)) {
//                if (isset($resp['errcode'])) {
//                    log_message("error", "WeixinBaseService - getUserInfo fail 102 - " . json_encode($resp));
//                    return false;
//                }
//                //return array
//                return $resp;
//            }
//        }
//        $resp = $this->_microserviceHelper->getLastReponse();
//        log_message("error", "WeixinBaseService - getUserInfo fail 103 - " . json_encode($resp));
//        return false;
//    }
//    
    
}
