<?php

namespace Common\Microservice\WeixinService;

use Common\Core\BaseEntityCollection;
use Common\Helper\MicroserviceHelper\MicroserviceHelper;
use Common\Helper\ResponseMessage;
use Common\Helper\ResponseHeader;
use Common\Core\BaseDateTime;

class WeixinErrCode {

    const CODE_40001 = 40001;  //invalid credential	不合法的调用凭证
    const CODE_40002 = 40002;  //invalid grant_type	不合法的 grant_type
    const CODE_40003 = 40003;  //invalid openid	不合法的 OpenID
    const CODE_40004 = 40004;  //invalid media type	不合法的媒体文件类型
    const CODE_40007 = 40007;  //invalid media_id	不合法的 media_id
    const CODE_40008 = 40008;  //invalid message type	不合法的 message_type
    const CODE_40009 = 40009;  //invalid image size	不合法的图片大小
    const CODE_40010 = 40010;  //invalid voice size	不合法的语音大小
    const CODE_40011 = 40011;  //invalid video size	不合法的视频大小
    const CODE_40012 = 40012;  //invalid thumb size	不合法的缩略图大小
    const CODE_40013 = 40013;  //invalid appid	不合法的 AppID
    const CODE_40014 = 40014;  //invalid access_token	不合法的 access_token
    const CODE_40015 = 40015;  //invalid menu type	不合法的菜单类型
    const CODE_40016 = 40016;  //invalid button size	不合法的菜单按钮个数
    const CODE_40017 = 40017;  //invalid button type	不合法的按钮类型
    const CODE_40018 = 40018;  //invalid button name size	不合法的按钮名称长度
    const CODE_40019 = 40019;  //invalid button key size	不合法的按钮 KEY 长度
    const CODE_40020 = 40020;  //invalid button url size	不合法的 url 长度
    const CODE_40023 = 40023;  //invalid sub button size	不合法的子菜单按钮个数
    const CODE_40024 = 40024;  //invalid sub button type	不合法的子菜单类型
    const CODE_40025 = 40025;  //invalid sub button name size	不合法的子菜单按钮名称长度
    const CODE_40026 = 40026;  //invalid sub button key size	不合法的子菜单按钮 KEY 长度
    const CODE_40027 = 40027;  //invalid sub button url size	不合法的子菜单按钮 url 长度
    const CODE_40029 = 40029;  //invalid code	不合法或已过期的 code
    const CODE_40030 = 40030;  //invalid refresh_token	不合法的 refresh_token
    const CODE_40036 = 40036;  //invalid template_id size	不合法的 template_id 长度
    const CODE_40037 = 40037;  //invalid template_id	不合法的 template_id
    const CODE_40039 = 40039;  //invalid url size	不合法的 url 长度
    const CODE_40048 = 40048;  //invalid url domain	不合法的 url 域名
    const CODE_40054 = 40054;  //invalid sub button url domain	不合法的子菜单按钮 url 域名
    const CODE_40055 = 40055;  //invalid button url domain	不合法的菜单按钮 url 域名
    const CODE_40066 = 40066;  //invalid url	不合法的 url
    const CODE_41001 = 41001;  //access_token missing	缺失 access_token 参数
    const CODE_41002 = 41002;  //appid missing	缺失 appid 参数
    const CODE_41003 = 41003;  //refresh_token missing	缺失 refresh_token 参数
    const CODE_41004 = 41004;  //appsecret missing	缺失 secret 参数
    const CODE_41005 = 41005;  //media data missing	缺失二进制媒体文件
    const CODE_41006 = 41006;  //media_id missing	缺失 media_id 参数
    const CODE_41007 = 41007;  //sub_menu data missing	缺失子菜单数据
    const CODE_41008 = 41008;  //missing code	缺失 code 参数
    const CODE_41009 = 41009;  //missing openid	缺失 openid 参数
    const CODE_41010 = 41010;  //missing url	缺失 url 参数
    const CODE_42001 = 42001;  //access_token expired	access_token 超时
    const CODE_42002 = 42002;  //refresh_token expired	refresh_token 超时
    const CODE_42003 = 42003;  //code expired	code 超时
    const CODE_43001 = 43001;  //require GET method	需要使用 GET 方法请求
    const CODE_43002 = 43002;  //require POST method	需要使用 POST 方法请求
    const CODE_43003 = 43003;  //require https	需要使用 HTTPS
    const CODE_43004 = 43004;  //require subscribe	需要订阅关系
    const CODE_44001 = 44001;  //empty media data	空白的二进制数据
    const CODE_44002 = 44002;  //empty post data	空白的 POST 数据
    const CODE_44003 = 44003;  //empty news data	空白的 news 数据
    const CODE_44004 = 44004;  //empty content	空白的内容
    const CODE_44005 = 44005;  //empty list size	空白的列表
    const CODE_45001 = 45001;  //media size out of limit	二进制文件超过限制
    const CODE_45002 = 45002;  //content size out of limit	content 参数超过限制
    const CODE_45003 = 45003;  //title size out of limit	title 参数超过限制
    const CODE_45004 = 45004;  //description size out of limit	description 参数超过限制
    const CODE_45005 = 45005;  //url size out of limit	url 参数长度超过限制
    const CODE_45006 = 45006;  //picurl size out of limit	picurl 参数超过限制
    const CODE_45007 = 45007;  //playtime out of limit	播放时间超过限制（语音为 60s 最大）
    const CODE_45008 = 45008;  //article size out of limit	article 参数超过限制
    const CODE_45009 = 45009;  //api freq out of limit	接口调动频率超过限制
    const CODE_45010 = 45010;  //create menu limit	建立菜单被限制
    const CODE_45011 = 45011;  //api limit	频率限制
    const CODE_45012 = 45012;  //template size out of limit	模板大小超过限制
    const CODE_45016 = 45016;  //can't modify sys group	不能修改默认组
    const CODE_45017 = 45017;  //can't set group name too long sys group	修改组名过长
    const CODE_45018 = 45018;  //too many group now, no need to add new	组数量过多
    const CODE_50001 = 50001;  //api unauthorized	接口未授权

}
