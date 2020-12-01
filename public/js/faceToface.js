var uri = location.search;
var isAndroid = navigator.userAgent.toLowerCase().indexOf('android') != -1;
var tool = {
    getQueryString : function (e) {
        var t = new RegExp("(\\?|^|&|#)" + e + "=([^&|^#]*)(&|$|#)", "i"),
            n = window.location.href.match(t);
        return null != n ? unescape(n[2]) : null
    },
    getUrl : function () {
        var protocol = location.protocol;
        var host = window.location.host;
        return protocol+'//'+host;
    },
    createScript :function(e) {
        var t = document.createElement("script");
        t.src = e, $("head").append(t);
    },
    getAppVersion : function () {
        return this.getQueryString('app_version').split(".").join("");
    },
    getChannel : function () {
        return this.getQueryString('channel');
    }
};
var getSendData =function() {
    return {
      access: getRequestParams('access'),
      'app-version': getRequestParams('app-version'),
      carrier: getRequestParams('carrier'),
      channel: getRequestParams('channel'),
      cookie: getRequestParams('cookie'),
      cookie_id:getRequestParams('cookie_id'),
      device_brand: getRequestParams('device_brand'),
      device_id: getRequestParams('device_id'),
      device_model: getRequestParams('device_model'),
      device_type: getRequestParams('device_type'),
      mc: getRequestParams('mc'),
      openudid: getRequestParams('openudid'),
      os_api: getRequestParams('os_api'),
      os_version: getRequestParams('os_version'),
      request_time: getRequestParams('request_time'),
      resolution: getRequestParams('resolution'),
      sim: getRequestParams('sim'),
      sm_device_id: getRequestParams('sm_device_id'),
      subv: getRequestParams('subv'),
      uid: getRequestParams('uid'),
      version_code: getRequestParams('version_code'),
      zqkey: getRequestParams('zqkey'),
      zqkey_id: getRequestParams('zqkey_id')
    }
  }
var getRequestParams = function(strname) {
    var url = location.search // 获取url中"?"符后的字串
    var theRequest = {}
    if (url.indexOf('?') !== -1) {
        var str = url.substr(1)
        var strs = str.split('&')
        for (var i = 0; i < strs.length; i++) {
        theRequest[strs[i].split('=')[0]] = decodeURI(strs[i].split('=')[1])
        }
    }
    if(strname){
        return theRequest[strname]
    }else{
        return theRequest
    }
}
function connectWebViewJavascriptBridge(callback){
    if(window.WebViewJavascriptBridge){
        callback(WebViewJavascriptBridge)
    }else{
        document.addEventListener('WebViewJavascriptBridgeReady',function(){
            callback(WebViewJavascriptBridge)
        },false)
    }
}
var loading ;
function getLoading() {
    var html = '<div class="Loading" ahw="1"><div class="spinner"> <img src="data:image/png;base64,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" alt="" /></div></div>';
    var css = "<style>.Loading{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0, 0, 0, 0);z-index:99}.Loading .spinner{position:absolute;width:1.2rem;height:1.2rem;top:40%;left:50%;margin:-.6rem 0 0 -.6rem;background-color:rgba(0, 0, 0, .7);border-radius: .16rem}.Loading .spinner img{width:40%;height:40%;margin:auto;left:0;right:0;top:0;bottom:0;animation:rotateImg 1.5s linear infinite;-webkit-animation:rotateImg 1.5s linear infinite;z-index:9999999999;position:absolute}@keyframes rotateImg{0%{transform:rotateZ(0deg)}50%{transform:rotateZ(180deg)}100%{transform:rotateZ(360deg)}}@-webkit-keyframes rotateImg{0%{-webkit-transform:rotateZ(0deg)}50%{-webkit-transform:rotateZ(180deg)}100%{-webkit-transform:rotateZ(360deg)}}</style>";
    loading = $(html);
    $("body").append(loading);
    loading.css("background", "rgba(255,255,255,.3)");
    $("head").append(css);
    return 0;
};

function _ajax(url, data) {
    var d = {cookie: tool.getQueryString('cookie'), cookie_id: tool.getQueryString('cookie_id')};
    var i;
    if(data){
        for( i in data){
            d[i] = data[i];
        }
    }
    // url = tool.getUrl()+url;
    // console.log('------><>===>', arguments)
    var n = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : "get",
        s = arguments.length > 3 && void 0 !== arguments[3] ? arguments[3] : "json";
        // console.log('------><>===>', n)
    return Q.Promise(function(resolve, reject, notify) {
        $.ajax({
            dataType: s,
            url: url,
            cache: !1,
            data: d,
            type: n,
            success: function(i) {
                resolve(i);
            },
            error: function(i, o) {
                reject(new Error("Can't XHR " + i));
            }
        })
    });
};
// 获取分享信息
// function getShareData() {
//     return _ajax('/webApi/invite/getShareData',getSendData());
// }
/*获取信息*/
function toGetData() {
    return _ajax('/user/cashout/show','', 'POST');
}
// function checkScanRequest(){
//     return _ajax('https://kd.youth.cn/scanInvite/checkScanReward','', 'POST');
// }
// function getScanRequest(){
//     return _ajax('/scanInvite/getScanReward','', 'POST');
// }
function mySwiper1(){
    new Swiper('.swiper1', {
        direction: 'vertical',
        autoplay: 2000,
        loop: true,
        slidesPerView: 1,
        slidesPerGroup: 1,
        noSwiping: true,
        noSwipingSelector: 'li'
    });
}
// function jumprecord(){
//     window.location.href = "/html/scanInvite/record.html"+uri;
// }

// function checkQr(){
//     var cookie =  tool.getQueryString('cookie');
//     var cookie_id = tool.getQueryString('cookie_id');
//     var channel = tool.getQueryString('channel');
//     var imgsrc = '/scanInvite/getQr?cookie='+cookie+'&cookie_id='+cookie_id+'&channel='+channel+'&random='+Math.random();
//     $("#qrimg").attr("src",imgsrc);
// }

var tanst = 0;
var st = 0;


// //检测用户是否获得红包奖励
// function checkScan(){
//     checkScanRequest().then(function (e) {



//         if(e.status == 1 && tanst == 0){
//             tanst = 1;
//             $('#main').append(getReceiveHtml());
//             $("#record2").click(function(){
//                 jumprecord();
//             });
//         }

//         return e;
//     }).then(function (e) {
//         //关闭弹窗
//         $("#close,#close2").click(function(){
//             $("#tanchuang1").remove();
//             tanst = 0;
//         });
//         //点击开红包
//         $("#open").off('click');
//         $("#open").on('click',function(){
//             if(st != 0){
//                 return false;
//             }
//             getScanRequest().then(function (e) {
//                 st = 1;
//                 return e;
//             }).then(function (e) {
//                 if(e.status == 1){
//                     $("#score_area").text(e.score);
//                     $("#tanchuang2").show();
//                     st = 0;
//                 }
//             });
//         });
//     });
// }

//弹出领取奖励
// function getReceiveHtml(){
//     var html = '<div class="tanchuang_bg" id="tanchuang1">\n' +
//         '<div class="tc_open" >\n' +
//         '<img class="tc_off" src="https://res.youth.cn/active/one/img/newInv/new/close.png" id="close"/>\n' +
//         '<img class="zqkd_icon" src="https://res.youth.cn/20190301/img/zq_icon.png" alt="" />\n' +
//         '<p>中青看点</p>\n' +
//         '<p>恭喜发财，大吉大利</p>\n' +
//         '<img class="open_btn" src="https://res.youth.cn/20190301/img/open_btn.png" id="open"/>\n' +
//         '<p>中青看点</p>\n' +
//         '</div>\n' +
//         '<div class="tc_reward" style="display: none;" id="tanchuang2">\n' +
//         '<img class="tc_off" src="https://res.youth.cn/active/one/img/newInv/new/close.png" id="close2"/>\n' +
//         '<img class="zqkd_icon" src="https://res.youth.cn/20190301/img/zq_icon.png" />\n' +
//         '<p>中青看点</p>\n' +
//         '<p>恭喜发财，大吉大利</p>\n' +
//         '<p class="flex_center_h"><span id="score_area"></span>青豆</p>\n' +
//         '<p class="tc_reward_btn" id="record2">已存入账号，可在中奖记录中查看>></p>\n' +
//         '</div>\n' +
//         '</div>';
//     return html;
// }

// //插入中间
// function getCenterHtml(data){
//     var cookie =  tool.getQueryString('cookie');
//     var cookie_id = tool.getQueryString('cookie_id');
//     var channel = tool.getQueryString('channel');
//     var imgsrc = '/scanInvite/getQr?cookie='+cookie+'&cookie_id='+cookie_id+'&channel='+channel+'&random='+Math.random();
//     var html = ejs.render('<p class="subtitle" style="display:none;">让越多人扫码奖金就越高</p>\n' +
//         '<div class="reward_btn">\n' +
//         '<img src="https://view.youth.cn/image/reward_btn2.png" id="record" />\n' +
//         '</div>\n' +
//         '<div class="code_bg" style="margin:0;height: 13rem;background:url(https://view.youth.cn/image/new32wan2.png) center center no-repeat;background-size: 100% auto;">\n' +
//         '<div class="code_box" style="top:4.5rem;width:3.6rem;height:3.6rem;">\n' +
//         '<img src="'+imgsrc+'" id="qrimg"/>\n' +
//         '</div>\n' +
//         '<div id="note_text" style="position: absolute;color:#551c00;font-size:0.28rem;line-height: .4rem;left:1.6rem;top:2.5rem;border-radius:100px;">好友每扫一次，您即可拆现金红包<br/>若好友安装登录，您还可得<span style="color: #d60f24;font-size: .3rem;">32元</span>邀请奖励\n'+
//         '</div>\n' +
//         '<div id="shareWx" style="position: absolute;width:5.65rem;height:1rem;left:1rem;bottom:1.1rem;border-radius:100px;">\n'+
//         '</div>\n' +
//         '</div>\n' +
//         '<div class="activit_rule" style="margin: -0.2rem .5rem .3rem;padding:0;color:#551c00;background:#ffe7b9;border-radius:10px;padding-bottom:.3rem;">\n' +
//         '<div class="flex_center_h rule_title" style="padding-top: .3rem;">\n' +
//         '<img src="https://res.youth.cn/20190301/img/denglong.png" alt="" />\n' +
//         '<p>活动规则</p>\n' +
//         '<img src="https://res.youth.cn/20190301/img/denglong.png" alt="" />\n' +
//         '</div>\n' +
//         '<p class="rule_desc" style="margin:.3rem .3rem 0;">1. 邀请好友扫描您的二维码，您可以获得一次拆现金机会，成功邀请好友加入中青看点，您还能获得<span style="color:#d60f24;">32元/位的现金奖励（无上限）；</span></p>\n' +
//         '<p class="rule_desc" style="margin:.3rem .3rem 0;">2. 您每天最多有<span style="color:#d60f24;">10次</span>拆现金机会，<span style="color:#d60f24;">被同一个好友扫码只能拆一次现金；</span></p>\n' +
//         '<p class="rule_desc" style="margin:.3rem .3rem 0;">3. 加入中青看点的好友还有机会获得现金奖励哦。</p>\n' +
//         '</div>',data);
//     return html;
// }
// //插入头部
function getHeadHtml(data){


    var html = '<div class="swiper1 swiper-container"><ul class="swiper-wrapper">';
    data.forEach(function(item){
        html += '<li class="flex_center_h swiper-slide swiper-no-swiping">\n' +
        '<img class="user_img" src="'+item.avatar+'" alt="" />\n' +
        '<p class="text_hidden user_name">' +item.nick_name+ '</p>\n' +
        '<p class="qingdou_number">刚刚提现了'+item.amount+'元</p>\n' +
        '</li>';
    })

    // var html = ejs.render('<div class="swiper1 swiper-container">\n' +
    //     '<ul class="swiper-wrapper">\n' +
    //     '<% data.forEach(function (item) {  %>\n' +
    //     '<li class="flex_center_h swiper-slide swiper-no-swiping">\n' +
    //     '<img class="user_img" src="<%- item.avatar %>" alt="" />\n' +
    //     '<p class="text_hidden user_name"><%- item.nick_name %></p>\n' +
    //     '<p class="qingdou_number">+<%- item.amount %>青豆</p>\n' +
    //     '</li>\n' +
    //     ' <% })%>\n' +
    //     '</ul>\n' +
    //     '</div>',data);

    return html;
}


function _init() {
    Q.fcall(function () {
        getLoading();
    }).then(function (e) {
        toGetData().then(function (e) {
            loading.remove();
            // $('body').append('<div class="main"><div class="face_code" id="main" style="background:#fcb505;padding-top:0;padding-bottom:.3rem;"></div></div>');
            //插入头部
            if(e.status){
                $('#show').append(getHeadHtml(e.result));
                mySwiper1();        //头部轮播
            }
            // //插入中间
            // if(e.status == 1){
                // $('#main').append(getCenterHtml(e.info));
            //     $("#record").click(function(){
            //         jumprecord();
            //     });
                
            // }

            return e;
        }).then(function (e) {
            checkScan();        //检测红包状态
            // var sh = setInterval(function(){
            //     checkScan();
            // },10000);
            //检测二维码过期
            // var sh2 = setInterval(function(){
            //     checkQr();
            // },60000);
        }).then(function() {
            // getShareData().then(function(events) {
            //     console.log('----???--->', events)
            //     if (events.code == 1) {
            //         $("#shareWx").click(function() {
            //             var shareData = events.data
            //             window.WebViewJavascriptBridge.callHandler(
            //                 "share2WeChatFriendsByOneKey",
            //                 { title: shareData.share_wechat_text_url },
            //                 function() {}
            //               );
            //         })

                    
            //     }
            // })
        });

    }).catch(function (e) {
        console.log(e);
    });
};
_init();


// getLoading();

// setTimeout(() => {
//     loading.remove();
// }, 1500);
