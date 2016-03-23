/**
 * JSON   
 */
$.JSON = {};
var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;

if (typeof(JSON)=='object' && typeof JSON.stringify === "function") {
    $.JSON.stringify = JSON.stringify;
} else {
     $.JSON.stringify = function(value, replacer, space) {
        var i; gap = ""; indent = "";
        if (typeof space === "number") {
            for (i = 0; i < space; i += 1) {
                indent += " ";
            }
        } else {
            if (typeof space === "string") {
                indent = space;
            }
        }
        rep = replacer;
        if (replacer && typeof replacer !== "function" && (typeof replacer !== "object" || typeof replacer.length !== "number")) {
            throw new Error("JSON.stringify");
        }
        return str("", {"": value });
    };
}

if (typeof(JSON)=='object' && typeof JSON.parse === "function") {
    $.JSON.parse = JSON.parse;
} else {
    $.JSON.parse = function(text, reviver) {
        var j;
        function walk(holder, key) {
            var k, v, value = holder[key];
            if (value && typeof value === "object") {
                for (k in value) {
                    if (Object.prototype.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {value[k] = v; }
                        else {delete value[k]; }
                    }
                }
            }
            return reviver.call(holder, key, value);
        }
        text = String(text);
        cx.lastIndex = 0;
        if (cx.test(text)) {
            text = text.replace(cx, function(a) {
            return "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4); });
        }
        if (/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) {
            j = eval("(" + text + ")");
            return typeof reviver === "function" ? walk({"": j }, "") : j;
        }
        throw new SyntaxError("JSON.parse");
    };
}

/**
 * Common script to handle the theme demo
 */
var Common = function() {

    var ajaxSetting = function(){
        // ajax loading before success
        //重写jquery的ajax方法
        var last_ajax = [];
        $.cur_log_depth = 0;    //refresh each page
        $.max_log_depth = 3;    //max depth a page
        $.post_error = function(data){
            //jslog('/common/ajaxlog/log?data=' + encodeURIComponent(data.join(',')));
            //$.post('/common/ajaxlog/log?of=json', {data: data}, function(data){});
        }

        $._post = $.post;
        $.post = function () {
            //验证input格式
            $._post.apply(this, arguments);
        }
        $.fn.post = function () {
            //验证input格式
            $._post.apply(this, arguments);
        }

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
            loadingDiv.innerHTML = "<img src='/theme/img/loading.gif' alt='加载中...' />";
            loadingDiv.style.position = "absolute";
            loadingDiv.style.left = "49.5%";
            loadingDiv.style.top = "64%";
            loadingDiv.style.zIndex = '9999999';

            return loadingDiv;
        })();

        $._ajax = $.ajax;
        $.ajax = function (opt) {
            var url_hash = "";
            //if(opt.url) url_hash = $.time33(0<=opt.url.indexOf('?')? opt.url.substr(0, opt.url.indexOf('?')) : opt.url);
            url_hash = $.time33(opt.url);
            
            //加载Loading图片
            if (typeof opt.loading === 'undefined' || opt.loading == true) $('body').append(loadingDiv);

            if(opt.type==undefined) opt.type="get";
            if(opt.type.toLowerCase() == "post"){
                //opt.data[$("#__csrf_token").attr("name")] = $("#__csrf_token").val();
            } else {
                opt.url = encodeURI(opt.url);
            }

            if (last_ajax[url_hash]!=null) last_ajax[url_hash].abort();

            //bugfix: 兼容laravel的分页
            if(opt.data && opt.data.start && opt.data.length)
                opt.url += ('&page='+opt.data.start/opt.data.length);
            last_ajax[url_hash] = $._ajax(opt).complete(function(data){
                if(data.readyState == 0)
                    return false;
                if(opt.type.toLowerCase() == "post"){
                    try {
                        /**
                         * 提示错误信息
                         */
                        var result = $.JSON.parse(data.responseText);
                        parse(result);
                        /*
                        if(result.ret != 1 && result.code == 1){
                            $(".login-popup").click();
                        }
                        else if(result.ret != 1){
                            error('操作失败', result.info);
                        }
                        */
                    } catch (e) {
                        if($.cur_log_depth ++ < $.max_log_depth){
                            error('操作失败', '操作失败');
                            //js_log
                        }

                    }
                }
                else {
                    var result = $.JSON.parse(data.responseText);
                    if (result.ret == 0 && result.info == 'logout') {
                        location.reload();
                    }
                }

                $('#__loading').remove();
            });
        };
    };
    
    var upload = function(upload_id, callback, start_callback, options) {
        setTimeout(function(){
            $(upload_id).uploadify({
                'formData'     : {
                    'timestamp' : new Date().getTime(),
                    'token'     : new Date().getTime()
                },
                'method'    : 'post',
                'buttonText': '<button class="btn btn-primary">选择文件</button>',
                'swf'      : '/theme/vendors/uploadify/uploadify.swf',
                'uploader' : options && options.url ? options.url : '/image/preview',
                'queueID': 'fileQueue',
                'width' : options && options.width ? options.width: '80',
                'buttonImage' : '',
                //'buttonImage' : ''options && options.button_image ? options.button_image : '/img/upphoto.png',
                'auto': true,
                'multi': false,
                'onUploadSuccess': function (file, data, response) {
                    callback && callback($.JSON.parse(data), upload_id);
                },
                'onUploadProgress' : function(file, bytesUploaded, bytesTotal, totalBytesUploaded, totalBytesTotal) {
                    var progress_bar = $(".pace-inactive");
                    if(progress_bar.length > 0){
                        if(bytesUploaded == 0){
                            progress_bar.find(".pace-progress").css("width", "100%");
                        }
                        else {
                            progress_bar.hide();
                        }
                    }
                },
                'onUploadStart': function(data){

                    var progress_bar = $(".pace-inactive");
                    progress_bar.find(".pace-progress").css("width", 0);
                    progress_bar.show();
                    start_callback && start_callback (data);
                }
            });
        },10);
    };

    return {
        upload: upload,
        //main function to the common tools
        init: function() {
            ajaxSetting();
        }
    };
}();
Common.init();

function trimUrl(url) {

	var pos = url.indexOf('?');
	if(pos != -1) {
		url = url.substring(0, pos);//获取参数部分 
	}
	return url;
}

function getQueryVariable(variable){
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return(false);
}

function time( timeMatrixing ){
    var t =  Number( timeMatrixing )*1000;
    var now = new Date().getTime();
    var second = Math.ceil((now - t)/1000);
    var str = '';
    var s = 0;
    if( second < 60 ){ 
        s = Math.ceil(second);
        str = '刚刚';
    }
    else if( second < (60*60)){
        s = Math.ceil(second/60);
        str = s+'分钟前';
    }
    else if( second < (60*60*24) ){ 
        s = Math.ceil(second/(60*60));
        str = s+'小时前';
    }
    else if( second < (60*60*24*2) ){
        str = '一天前';
    }
    else if( second < (60*60*24*3) ){
        str = '一天前';
    }
    else if( second < (60*60*24*4) ){
        str = '两天前';
    }
    else if( second < (60*60*24*5) ){
        str = '三天前';
    }
    else if( second < (60*60*24*6) ){
        str = '四天前';
    }
    else if( second < (60*60*24*6) ){
        str = '五天前';
    }
    else if( second < (60*60*24*7) ){
        str = '六天前';
    }
    else if( second < (60*60*24*8) ){
        str = '一周前';
    }
    else{
        var ts = new Date( t );
        var minute = 0;
        var getMinute = ts.getMinutes();
        if( ts.getMinutes() < 10 ) {
            minute = '0' + getMinute;
        } else {
            minute = getMinute;
        }
        str = ts.getFullYear() + '-' + (ts.getMonth()+1) + '-' + ts.getDate() + ' ';
        str+= ts.getHours() + ':' + minute;
    }
    return str;
}

function append(el, item, options) {
    var opt = {
        time: 400
    }
    for(var i in options) {
        opt[i] = options[i];
    }
    var item = $(item).clone().hide();
    $(el).append(item);
    item.fadeIn(opt.time);
};

function error(title, desc, callback) {
    $("a#show-error-popup").fancybox({
        afterShow: function(){
            $('.confirm, .cancel').click(function(){ 
                $.fancybox.close();
                callback && callback();
            });
        },
        padding : 0
    });
    $("#error-popup .title").text(title);
    $("#error-popup .error-content").text(desc);

    $("#show-error-popup").click();
};

function toast(desc, callback) {
    $("a#show-toast-popup").fancybox({
        autoSize: true,
        closeBtn : false,
        helpers: {
            overlay : null
        }
    });
    $("#toast-popup .error-content").text(desc);

    $("#show-toast-popup").click();
    setTimeout(function() {
        $.fancybox.close();
        callback && callback();
    }, 2000);
};

function parse(resp, xhr) { 
    // todo  billqiang QQ
      // QC.Login({
      //       btnId:"qqLoginBtn",   //插入按钮的节点id
      //   });
    if(resp.ret == 0 && resp.code == 1  ) {


        WB2.anyWhere(function (W) {
            W.widget.connectButton({
                id: "wb_login_btn",
                //type: '3,2',
                callback: {
                    login: account.weibo_auth
                }
            });
            W.widget.connectButton({
                id: "wb_register_btn",
                //type: '3,2',
                callback: {
                    login: account.weibo_auth
                }
            });
        });
    }

    if(resp.ret == 0 && resp.code == 1 && this.url != 'user/status') { 
        if(WB2.oauthData.access_token) {
            //微博注册
            $(".binding-popup").click();
        }
        else {
            //原生登陆
            $(".login-popup").click();
        }
        return false;
    } 
    else if(resp.ret == 0 && this.url != 'user/status') {
        error('操作失败', resp.info);
    }
    //console.log('parsing base modelxxx');
    return resp.data;
};

var account = {
    keypress:function(e) {
        if(e.which == 13) {
           $("#login_btn").click(); 
        }
    },
    weibo_auth: function(e) {
        //默认只能绑定
        $.get('user/auth', {
            openid: WB2.oauthData.uid,
            type: 'weibo'
        }, function(data) {
            if(data.data.is_register == 0) {
                $(".binding-popup").click();
            }
            else {
                location.reload();
            }
        });

        if(e.gender == 'f') {
            $(".option-sex .option-girl input").click();
        }
        $('#register-avatar').attr('src', e.profile_image_url);
        $('#register_nickname').val(e.screen_name);
        $('#register_nickname').attr('type', 'weibo');
        $('#register_nickname').attr('openid', WB2.oauthData.uid);
        $(".login-popup").attr("href", "#binding-popup");

        if(!window.app.user.uid) {
            window.app.user.set('avatar', e.profile_image_url);
            window.app.user.set('nickname', e.screen_name);
            window.app.user.set('uid', e.uid);
            $(".login-popup").attr("href", "#binding-popup");
        }
        if($("#binding-popup").css("display") == 'none')
            $(".binding-popup").click();

    },
    login_keyup:function() {
        var username = $('#login_name').val();
        var password = $('#login_password').val();
        if(username != '' && password != '' ) {
            $('#login_btn').removeAttr('disabled').css('background','#F7DF68');
        }
        if(username == '' || password == '' ) {
            $('#login_btn').attr("disabled", true).css('background','#EBEBEB');
        }
    },

    login: function(e) {
        var self = this;
        var username = $('#login_name').val();
        var password = $('#login_password').val();

        $.post('/user/login', {
            username: username, 
            password: password
        }, function(returnData, data) {
            if( returnData.ret == 1 ) {
                history.go(1);
                location.reload();
            } else {
                console.log(returnData);
                return false;
            }
        });
    },
    register_keyup:function() {
        var nickname = $('#register_nickname').val();
        var phone =  $('#register_phone').val();
        var password = $('#register_password').val();

        if(nickname != '' && phone != '' && password != '' ) {
            $('.register-btn').removeAttr('disabled').addClass('bg-btn');
        }
        if(nickname == '' || phone == '' || password == '' ) {
            $('.register-btn').attr("disabled", true).removeClass('bg-btn');
        }
    },
    register: function (e) {
            var self = this;
            var boy = $('.boy-option').hasClass('boy-pressed');
            var sex = 1;
            var code = $('#register-popup input[name=registerCode]').val();
            var avatar = $('#register-avatar').attr('src');
            var nickname = $('#register_nickname').val();
            var phone    =  $('#register_phone').val();
            var password = $('#register_password').val();
    
            var url = "/user/register";
            var postData = {
                'nickname': nickname,
                'sex' : sex,
                'mobile': phone,
                'password': password,
                'avatar' : avatar,
                'code' : code
            };
            $.post(url, postData, function( returnData ){
                if(returnData.ret != 0)
                    location.reload();
            });

    },
    bind: function() {
        var boy = $('.boy-option').hasClass('boy-pressed');
        var sex = boy ? 0 : 1;
        var avatar = $('#register-avatar').attr('src');
        var nickname = $('#register_nickname').val();

        var phone = $('input[name=binding-phone]').val();
        var code = $('input[name=binding-code]').val();
        var password = $('input[name=binding-password]').val();

        var type    = $('#register_nickname').attr('type');
        var openid  = $('#register_nickname').attr('openid');
        if( phone == '') {
            //todo: 验证码
            alert('手机号不能为空');
            return false;
        }
        if( code == '') {
            alert('验证码不能为空');
            return false;
        }
        if( password == '' ) {
            alert('密码不能为空');
            return false;
        }
        var url = "/user/register";
        var postData = {
            'type': type,
            'openid': openid,
            'nickname': nickname,
            'avatar': avatar,
            'sex': sex,
            'mobile': phone,
            'code' : code,
            'password': password,
        };
        $.post(url, postData, function( returnData ){
            if(returnData.ret != 0)
                location.reload();
        });
    },
    optionSex: function(event) {
        $('.sex-pressed').removeClass('boy-pressed').removeClass('girl-pressed');
        $(event.currentTarget).addClass('boy-pressed');
        $(event.currentTarget).addClass('girl-pressed');
    }
};
