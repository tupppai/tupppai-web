/**
 * 微信相关
 */
define(['lib/wechat/wechat'], function (wechat) {
    "use strict";

    var wechat_utils = {};

    /**
     * 判断是否为微信登陆
     */
    wechat_utils.is_from_wecaht = function () {
        var ua = navigator.userAgent.toLowerCase();

        if (ua.match(/MicroMessenger/i) == 'micromessenger') {
            return true;
        } else {
            return false;
        }
    }

    return wechat_utils;
});