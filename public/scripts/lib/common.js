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
                        if(result.ret != 1 && result.code == 1){
                            $(".login-popup").click();
                        }
                        else if(result.ret != 1){
                            error('操作失败', result.info);
                        }
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

    var select_width = 100;
    var width = 300;
    var height = 400;

    var scale_image = function(o, w, h){
        var img = new Image();
        img.src = o.src;
        if(img.width >0 && img.height>0)
        {
            if(img.width/img.height >= w/h)
            {
                o.width = w;
                o.height = (img.height*w) / img.width;
                o.alt = img.width + "x" + img.height;
            }
            else
            {
                o.height = h;
                o.width = (img.width * h) / img.height;
                o.alt = img.width + "x" + img.height;
            }
        }
        $(img).show();
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

    var crop = function(options, callback){
        var preview_obj = $(options.preview_id);
        var width = parseInt(preview_obj.css("width"));
        var height= parseInt(preview_obj.css("height"));
        if(width > height){
            options.setSelect = [0, 0, 0, height];
        }
        else {
            options.setSelect = [0, 0, width, 0];
        }

        preview_obj.Jcrop(options, callback);
    };

    var get_click = function (event) {
        var e = event || window.event;
        var scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
        var scrollY = document.documentElement.scrollTop || document.body.scrollTop;
        var x = e.pageX || e.clientX + scrollX;
        var y = e.pageY || e.clientY + scrollY;
        return { 'x': x, 'y': y };
    }

    var label = function(label_id, options){
        var _options = {
            div: "<div class='label'><input/></div>"
        }

        _options.offset = {};
        _options.offset.left = 0;
        _options.offset.top  = 0;

        for(var i in options){
            _options[i] = options[i];
        }

        //var offset= $("#"+_options.offset_div).offset();

        $("#"+label_id).click(function(e){
            var label = $("#"+label_id);
            var board = $("#"+_options.offset_div);

            if(e.target.id != label_id) {
                return false;
            }

            var c = get_click(e);
            var offset = label.offset();
            var x = c.x - offset.left;
            var y = c.y - offset.top;
            var width  = parseFloat(label.css("width"));
            var height = parseFloat(label.css("height"));

            var base_width  = parseFloat(board.css("width"));
            var base_height = parseFloat(board.css("height"));

            var offset_left = 0;
            var offset_top  = 0;
            if(base_width > width){
                offset_left = (base_width - width)/2;
            }
            if(base_height > height){
                offset_top = (base_height - height)/2;
            }

            div = $(_options.div);
            div.css("left", x + offset_left);
            div.css("top", y + offset_top);

            board.append(div);
            var label_font = $(div).find(".label-font");
            label_font.val(' ');
            label_font.focus();
            label_font.blur(function(){
                if($(this).val().trim() == ""){
                    $(this).parent().remove();
                }
            });

            //offset.append(div);
            div.drag('options', {
                cursor: 'move',
                min: {left: offset_left, top: offset_top},
                max: {left: width + offset_left, top: height + offset_top}
            });

            e.preventDefault();
        });
    };

    var jcrop_api = undefined;

    return {
        resize: function(div_id, width, ratio){
            if(ratio == undefined){
                ratio = 3/4;
            }

            var height  = width*ratio;

            if(height > 400){
                height = 400;
                width  = height/ratio;
                $(div_id).css("width", width+"px");
                $(div_id).css("height", height+"px");
                $(div_id).css("margin-top", '-' + height/2 + "px");
                $(div_id).css("margin-left", '-' + width/2 + "px");
            }
            else {
                $(div_id).css("width", width+"px");
                $(div_id).css("height", height+"px");
                $(div_id).css("margin-top", '-' + height/2 + "px");
                $(div_id).css("margin-left", '-' + width/2 + "px");
            }
        },
        jcrop_api: jcrop_api,
        jcrop_release: function () {
            if (Common.jcrop_api) {
                Common.jcrop_api.release();
            }
        },
        jcrop_destroy: function (){
            if (Common.jcrop_api) {
                Common.jcrop_api.destroy();
            }
        },
        toggle_modal: function (modal_id, need_login){
            if(need_login != undefined && need_login == true){
                if($("#_uid").val() == ""){
                    alert("请先登录");
                    return false;
                }
            }
            var modal = $("#" + modal_id);
            if(modal.hasClass("hidden")){
                modal.removeClass("hidden");
                modal.next().removeClass("hidden");
            }
            else {
                modal.addClass("hidden");
                modal.next().addClass("hidden");
            }
        },
        upload: upload,
        get_click: get_click,
        crop: crop,
        getSelectWidth: function(){
            return select_width;
        },
        getWidth: function(){
            return width;
        },
        getHeight: function(){
            return height;
        },
        label: label,
        preview: function(o, data){
            $("#"+o).attr("src", data.data.url);
            $("#"+o).attr("upload_id", data.data.id);
            $("#"+o).show();

            var img = document.getElementById(o);
            img.onload = function(){
                scale_image(this, width, height);
            }
        },
        //main function to the common tools
        init: function() {
            ajaxSetting();
        }
    };
}();
Common.init();


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
        str = s+'秒前';
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
        str = ts.getFullYear() + '-' + (ts.getMonth()+1) + '-' + ts.getDate() + ' ';
        str+= ts.getHours() + ':' + ts.getMinutes();
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

var account = {
      keypress:function(e) {
        if(e.which == 13) {
           $("#login_btn").click(); 
        }
    },
    login_keyup:function() {
        var username = $('#login_name').val();
        var password = $('#login_password').val();
        if(username != '' && password != '' ) {
            $('#login_btn').css('background','#F7DF68');
        }
        if(username == '' || password == '' ) {
            $('#login_btn').css('background','#EBEBEB');
        }

    },
    login: function(e) {
        var self = this;
        var username = $('#login_name').val();
        var password = $('#login_password').val();

        if (username == '') {
            $('#user_empty_reminder').removeClass('hide').show().fadeOut(1500);
            return false;
        } 
        if (password == '') {
            $('#user_password_reminder').removeClass('hide').show().fadeOut(1500);
            return false;
        }
        $.get('/user/login', {
            username: username, 
            password: password
        }, function(data) {
            history.go(1);
            location.reload(); 
        });
    },
    register_keyup:function() {
        var nickname = $('#register_nickname').val();
        var phone =  $('#register_photo').val();
        var password = $('#register_password').val();

        if(nickname != '' && phone != '' && password != '' ) {
            $('.register-btn').removeAttr('disabled').addClass('bg-btn');
        }
        if(nickname == '' || phone == '' || password == '' ) {
            $('.register-btn').addAttr('disabled').removeClass('bg-btn');
        }

    },
    register: function (e) {
        var self = this;

        var boy = $('.boy-option').hasClass('boy-pressed');
        var sex = boy ? 0 : 1;
        var avatar = $('#register-avatar').val();
        var nickname = $('#register_nickname').val();
        var phone =  $('#register_photo').val();
        var password = $('#register_password').val();


        if( nickname == '') {
            alert('昵称不能为空');
            return false;
        }
        if( phone == '') {
            alert('手机号码不能为空');
            return false;
        }
        if( password == '') {
            alert('密码不能为空');
            return false;
        }
        //todo: jq
        var url = "/user/save";
        var postData = {
            'nickname': nickname,
            'sex' : sex,
            'phone': phone,
            'password': password,
            'avatar' : avatar
        };
        $.get(url, postData, function( returnData ){
            console.log(returnData);
        });
    },
    optionSex: function(event) {
        $('.sex-pressed').removeClass('boy-pressed').removeClass('girl-pressed');
        $(event.currentTarget).addClass('boy-pressed');
        $(event.currentTarget).addClass('girl-pressed');
    }
}
