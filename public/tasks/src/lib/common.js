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
};

function parse(resp, xhr) { 

    if(resp.ret == 2 && this.url == 'user/status') { 
        //todo 允许未登录 
        return true;
    }
    else if(resp.ret == 2) {
        alert('not login');
        //var appid = resp.data.wx_appid;
        //var host  = location.host;
        //var redirect = encodeURIComponent('?hash='+location.hash.substr(1));
	    //location.href = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='+appid+'&redirect_uri=http://'+host+'/wechat&response_type=code&scope=snsapi_userinfo&connect_redirect=1#wechat_redirect';
    }
    else if(resp.ret == 0 && resp.code == 1  ) {
        return error(resp.info);
    }
    //console.log('parsing base modelxxx');
    return resp.data;
};

function error(title, desc, callback) {
    alert(title);    
    callback && callback();
};

function toast(desc, callback) {
    alert(desc);
    callback && callback();
};

function url(route) {
    location.href = route;
};
