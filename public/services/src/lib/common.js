var append = function (el, item, options) {
    var opt = {
        time: 400
    }
    for(var i in options) {
        opt[i] = options[i];
    }
    el.append(item);
};

var error = function (title, desc, callback) {
    alert(title);    
    callback && callback();
};

var toast = function (desc, callback) {
    alert(desc);
    callback && callback();
};

var parse = function (resp, xhr) { 
    if(resp.ret == 2 && this.url == 'user/status') { 
        //todo 允许未登录 
        return true;
    }
    else if(resp.ret == 2) {
        console.log('not login');
          var appid = resp.data.wx_appid;
          var host  = location.host;
        
          var redirect = encodeURIComponent('?hash='+location.hash.substr(1));
	      location.href = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='
          +appid+'&redirect_uri=http://'+host+'/v2/wechat&response_type=code&scope=snsapi_userinfo&connect_redirect=1#wechat_redirect';
    }
    else if(resp.ret == 0 && resp.code == 1  ) {
        return error(resp.info);
    }
    //console.log('parsing base modelxxx');
    return resp.data;
};
var title = function(title) {
    var $body = $('body');
    document.title = title
    // hack在微信等webview中无法修改document.title的情况
    var $iframe = $('<iframe style="display:none" src="/favicon.ico"></iframe>');
    $iframe.on('load',function() {
        setTimeout(function() {
            $iframe.off('load').remove();
        }, 0);
    }).appendTo($body);
};
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
        //opt.url += '?t=' + new Date().getTime(); 
        // opt.url = 'http://twww.tupppai.com/' + opt.url;
          
        //扩展增强处理  
        var _opt = $.extend(opt,{  
            beforeSend:function(XMLHttpRequest){  
                //加载Loading图片
                if (typeof opt.loading === 'undefined' || opt.loading == true) $('body').append(loadingDiv);

                // if(opt.type.toLowerCase() == "post"){
                //     // pass
                // } else {
                //     opt.url = encodeURI(opt.url);
                // }     
                // fn.beforeSend(XMLHttpRequest);  
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
    window.addEventListener('load', function() { FastClick.attach(document.body); }, false);
})($);  
function clickLike(e) {
    var loveCount = $(e.currentTarget).attr('love-count');
    var id   = $(e.currentTarget).attr('id');
    var likeEle = $(e.currentTarget).find('.text-like-btn');
    var type   = 2;
    $.get('/lovr', {
        id: id,
        num: loveCount,
        type: 2
    }, function(data) {
        $(e.currentTarget).addClass("liked-icon")
        likeEle.text( Number(likeEle.text())+ 1 );
    })
}
//判断是否是微信登陆
function is_from_wechat() {
    var ua = navigator.userAgent.toLowerCase();

    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return true;    
    } else {
        return false;
    }
}
var wx = '';
function wx_sign() {
    var url = location.href.replace(location.hash, '');
    $.post('/sign', {url: url}, function(data) {
<<<<<<< HEAD
=======
        console.log(url);
>>>>>>> db7e75ff3d72575e012af60f799e6b78eb1c342a
        wx = require('wx');
        wx.config({
            debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: data.appId, // 必填，公众号的唯一标识
            timestamp: data.timestamp, // 必填，生成签名的时间戳
            nonceStr: data.nonceStr, // 必填，生成签名的随机串
            signature: data.signature,// 必填，签名，见附录1
            jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'onMenuShareQZone',
                'chooseImage',
                'uploadImage'
            ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });

    });
};
function share_friend(options, success, cancel) {

    var opt = {};
    opt.title   = '【图派】图片玩乐';
    opt.desc    = '首个互助式图片处理社区';
    opt.img     = 'http://7u2spr.com2.z0.glb.qiniucdn.com/movie/favicon.ico';
    opt.link    = location.href;

    for(var i in options) {
        if(options[i]) opt[i] = options[i];
    }
    wx.ready(function() {
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
    });
};
//微信预览图片
function wx_previewImage(src) {
    wx.previewImage({
        urls:src 
    });
}
function time( publishTime ){
    var d_minutes,d_hours,d_days;       
    var timeNow = parseInt(new Date().getTime()/1000);       
    var d;       
    d = timeNow - publishTime;       
    d_days = parseInt(d/86400);       
    d_hours = parseInt(d/3600);       
    d_minutes = parseInt(d/60);       
    if(d_days>0 && d_days<4){       
        return d_days+"天前";       
    }else if(d_days<=0 && d_hours>0){       
        return d_hours+"小时前";       
    }else if(d_hours<=0 && d_minutes>0){       
        return d_minutes+"分钟前";       
    } else if(d_hours<=0 && d_hours<= 0) {
        return "刚刚";
    } else{       
        var s = new Date(publishTime*1000);       
        s.getFullYear()+"年";
        return s.getFullYear()+"年"+(s.getMonth()+1)+"月"+s.getDate()+"日";       
    }  
}
//toast弹窗
function fntoast(title,hide) {
    
    $("#toast_show").removeClass('toast-hide');
    $('.comment-title').text(title);
    if(hide) {
        $('#success_icon').addClass('toast-hide');
    } else {
        $('#success_icon').removeClass('toast-hide');
    }
    setTimeout(function(){
        $("#toast_show").addClass('toast-hide');
    },2000)

    // $("#toast-popup .error-content").text(desc);

    // $("#show-toast-popup").click();

};
