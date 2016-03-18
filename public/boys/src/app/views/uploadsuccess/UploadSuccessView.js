define(['app/views/base', 'tpl!app/views/uploadsuccess/UploadSuccessView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            onShow: function() {

                // 微信好友文案修改
                var options = {};
                options.code = $('body').attr('data-code');
                share_friend(options,function(){},function(){})
                share_friend_circle(options,function(){},function(){})
                
            	var dataUser = $("body").attr("data-user");
            	$("#dataUser").html(dataUser);
            	$("#dataTime").html(dataUser*10+"分钟");

            }
        });
    });