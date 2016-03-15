(function($){  
    //备份ajax方法  
    var _ajax =$.ajax;  
    var ajaxs = [];

    $.time33 = function (string) {
        var hash = 0;
        for (var i=string.length-1; i>=0; i--) {
            hash = hash*33 + string.substr(i, i+1).charCodeAt();
        }
        return hash.toString(36);
    };
    var loadingDiv = (function(){
        var loadingDiv = document.createElement('div');
        loadingDiv.id = '__loading';
        loadingDiv.className = 'body_loading';
        loadingDiv.innerHTML = "<img src='/img/loading.gif' alt='加载中...' />";
        loadingDiv.style.position = "absolute";
        loadingDiv.style.left = "49%";
        loadingDiv.style.top = "64%";
        loadingDiv.style.zIndex = '9999999';

        return loadingDiv;
    })();
      
    //重写ajax方法  
    $.ajax=function(opt){  
        var url_hash = $.time33(opt.url); 
        if (ajaxs[url_hash]!=null) 
            ajaxs[url_hash].abort();

        //备份opt中error和success方法  
        var fn = {  
            beforeSend:function(XMLHttpRequest){},
            success:function(data, textStatus){},
            error:function(XMLHttpRequest, textStatus, errorThrown){}
        };
        if(opt.beforeSend){  
            fn.beforeSend=opt.beforeSend;  
        }          
        if(opt.error){  
            fn.error=opt.error;  
        }  
        if(opt.success){  
            fn.success=opt.success;  
        }  
          
        //扩展增强处理  
        var _opt = $.extend(opt,{  
            beforeSend:function(XMLHttpRequest){  
                //加载Loading图片
                if (typeof opt.loading === 'undefined' || opt.loading == true) $('body').append(loadingDiv);

                if(opt.type.toLowerCase() == "post"){
                    // pass
                } else {
                    opt.url = encodeURI(opt.url);
                }     
                fn.beforeSend(XMLHttpRequest);  
            },
            error:function(XMLHttpRequest, textStatus, errorThrown){  
                //错误方法增强处理  ....
                  
                fn.error(XMLHttpRequest, textStatus, errorThrown);  
                $('#__loading').remove();
            },  
            success:function(data, textStatus){  
                //成功回调方法增强处理  ....
                  
                data = parse(data);
                fn.success(data, textStatus);  
                $('#__loading').remove();
            }  
        });  

        ajaxs[url_hash] = _ajax(_opt);  
    };  
})($);  

function append(el, item, options) {
    var opt = {
        time: 400
    }
    for(var i in options) {
        opt[i] = options[i];
    }
    el.append(item);
    /*
    var item = $(item).clone().hide();
    $(el).append(item);
    item.show(opt.time);
    */
};

function parse(resp, xhr) { 
    if(resp.ret == 2) {
        location.href = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx386ec5d1292a1e8f&redirect_uri=http://film.tupppai.com/wechat&response_type=code&scope=snsapi_userinfo#wechat_redirect';
    }
    if(resp.ret == 0 && resp.code == 1  ) {

    }

    if(resp.ret == 0 && resp.code == 1 && this.url != 'user/status') { 
       
        return false;
    } 
    else if(resp.ret == 0 && this.url != 'user/status') {
        return error('操作失败', resp.info);
    }
    //console.log('parsing base modelxxx');
    return resp.data;
};

function wx_sign() {
    $.post('sign', {url: 'http://' + location.host + '/'}, function(data) {
        wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: data.appId, // 必填，公众号的唯一标识
            timestamp: data.timestamp, // 必填，生成签名的时间戳
            nonceStr: data.nonceStr, // 必填，生成签名的随机串
            signature: data.signature,// 必填，签名，见附录1
            jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'onMenuShareQZone'
            ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });
    });
};

function error(title, desc, callback) {
    
    $('#alert_show').addClass('show-block');

    $(".ale-head").text(title);
    $("#error-popup .error-content").text(desc);

    $("#show-error-popup").click();
};

function toast(desc, callback) {

    $("#toast-popup .error-content").text(desc);

    $("#show-toast-popup").click();

};

function _parseFloat(str) {
    if(str == '') {
        return 0;
    }
    return Math.round( parseFloat(str), 2 );
};

function share(options, success, cancel) {
    var opt = {};
    opt.title   = '出品联盟';
    opt.desc    = '出品联盟';
    opt.img     = 'http://' + location.hostname + '/favicon.ico';
    opt.link    = location.href;

    for(var i in options) {
        if(options[i]) opt[i] = options[i];
    }
    wx.ready(function() {
        //分享朋友圈
        wx.onMenuShareAppMessage({
            title: opt.title, // 分享标题
            desc: opt.desc, // 分享描述
            link: opt.link, // 分享链接
            imgUrl: opt.img, // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () { 
                // 用户确认分享后执行的回调函数
                success && success();
            },
            cancel: function () { 
                // 用户取消分享后执行的回调函数
                cancel && cancel();
            }
        });
//分享好友
        wx.onMenuShareTimeline({
            title: opt.title, // 分享标题
            link: opt.link, // 分享链接
            imgUrl: opt.img, // 分享图标
            success: function () { 
                // 用户确认分享后执行的回调函数
                success && success();
            },
            cancel: function () { 
                // 用户取消分享后执行的回调函数
                cancel && cancel();
            }
        });
    });
};
