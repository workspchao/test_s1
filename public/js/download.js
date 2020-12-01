DD = {
    loadJs: function (t, e) {
        var n = document.createElement("script");
        n.src = t, n.onload = function () {
            e && e()
        }, document.body.appendChild(n)
    }
};

////微信浏览器中，aler弹框不显示域名
//(function () {
//先判断是否为微信浏览器
var ua = window.navigator.userAgent.toLowerCase();
if (ua.match(/MicroMessenger/i) == 'micromessenger') {
    //重写alert方法，alert()方法重写，不能传多余参数
    window.alert = function (name) {
        var iframe = document.createElement("IFRAME");
        iframe.style.display = "none";
        iframe.setAttribute("src", 'data:text/plain');
        document.documentElement.appendChild(iframe);
        window.frames[0].window.alert(name);
        iframe.parentNode.removeChild(iframe);
    }
}
//})();

////微信浏览器中，confirm弹框不显示域名
//(function () {
//先判断是否为微信浏览器
var ua = window.navigator.userAgent.toLowerCase();
if (ua.match(/MicroMessenger/i) == 'micromessenger') {
    //重写confirm方法，confirm()方法重写，不能传多余参数
    window.confirm = function (message) {
        var iframe = document.createElement("IFRAME");
        iframe.style.display = "none";
        iframe.setAttribute("src", 'data:text/plain,');
        document.documentElement.appendChild(iframe);
        var alertFrame = window.frames[0];
        var result = alertFrame.window.confirm(message);
        iframe.parentNode.removeChild(iframe);
        return result;
    };
}
//})();

function clipboardText() {
    REF_CODE && DD.loadJs("https://cdn.bootcdn.net/ajax/libs/clipboard.js/2.0.6/clipboard.min.js", function () {
        //console.log("load clipboard js");
        if (REF_CODE) {
            //console.log("load clipboard ", CLIP_CODE);
            var t = new ClipboardJS("body", {
                text: function (t) {
                    return REF_CODE;
                }
            });
            t.on("success", function (t) {
                console.log("clipboardText", t.text)
            });
            t.on("error", function (e) {
                console.error("clipboardText", e);
            });
        }
    })
}

