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
    
    if(resp.ret == 0) {
        location.href = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa0b2dda705508552&redirect_uri=http://twww.tupppai.com/wechat&response_type=code&scope=snsapi_userinfo&connect_redirect=1#wechat_redirect';
        //图派男神活动授权
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
    alert( 'wx_singn' );
    $.post('/sign', {url: location.href}, function(data) {
        wx.config({
            debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: data.appId, // 必填，公众号的唯一标识
            timestamp: data.timestamp, // 必填，生成签名的时间戳
            nonceStr: data.nonceStr, // 必填，生成签名的随机串
            signature: data.signature,// 必填，签名，见附录1
            jsApiList: [
                'onMenuShareTimeline', //分享好友
                'onMenuShareAppMessage',//分享朋友圈
                'chooseImage',//从手机获取图片
                'uploadImage',//上传图片
                'downloadImage'//下载图片
            ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });
    });
};

//拍照或从手机相册中选图接口
function wx_choose_image(boy_id,effect_id) {
    wx.chooseImage({
        count: 1, // 默认9
        success: function (res) {
        
            var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
            var localId = localIds.toString();

            wx.uploadImage({
                localId: localId,
                isShowProgressTips: 1,
                success:function(res) {
                    var serverId = res.serverId;
                    alert( 'boy_id:'+boy_id );
                    alert( 'effect_id:'+effect_id );
                    var data = {
                        desc: boy_id +"-"+effect_id,
                        media_id: serverId
                    }
                    $.post('/wxactgod/upload',data,function(result){
                        if( result.result == 'ok' ){
                        location.href = 'http://' + location.hostname + '/boys/uploadsuccess/uploadsuccess#' + boy_id;
                        }
                    })
                }
            })
        }
    });
}
//分享给好友
function share_friend(options, success, cancel) {

    var opt = {};
    opt.title   = '你和男神之间的距离只有一个头像';
    opt.desc    = '图派PS爱好者免费为你定制男神同款特效头像，';
    opt.img     = 'http://' + location.hostname + '/img/favicon.ico';
    opt.link    = 'http://' + location.hostname + '/boys/index/index';
    opt.id = '';
    opt.code = '';

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
                alert( '分享后code:'+opt.code );
                if(opt.id != "" && opt.code == -2) {
                    location.href = 'http://' + location.hostname + '/boys/shareavatar/shareavatar#'+opt.id
                }  else if( opt.code == -2) {
                    location.href = 'http://' + location.hostname + '/boys/index/index#'+opt.id
                }
                
            },
            cancel: function () { 
                // 用户取消分享后执行的回调函数
                cancel && cancel();
            }
        });
    });
};
//分享朋友圈
function share_friend_circle(options, success, cancel) {
    
    var opt = {};
    opt.title   = '免费为你定制男神同款头像！';
    opt.img     = 'http://' + location.hostname + '/img/favicon.ico';
    opt.link    = 'http://' + location.hostname + '/boys/index/index';
    opt.id = '';
    for(var i in options) {
        if(options[i]) opt[i] = options[i];
    }
    
    
    opt.img     = 'http://' + location.hostname + '/img/favicon.ico';
    wx.ready(function() {
        //分享好友
        wx.onMenuShareTimeline({
            title: opt.title, // 分享标题
            link: opt.link, // 分享链接
            imgUrl: opt.img, // 分享图标
            success: function () { 
                // 用户确认分享后执行的回调函数
                if(opt.id != "" && opt.code == -2 ) {
                   location.href = 'http://' + location.hostname + '/boys/shareavatar/shareavatar#'+opt.id
                }else  if( opt.code == -2)  {
                    location.href = 'http://' + location.hostname + '/boys/index/index#'+opt.id
                }
                success && success();
            },
            cancel: function () { 
                // 用户取消分享后执行的回调函数

                cancel && cancel();
            }
        });
    });
};

