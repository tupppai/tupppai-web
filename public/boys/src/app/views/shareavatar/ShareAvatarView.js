define(['app/views/base', 'tpl!app/views/shareavatar/ShareAvatarView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click #uploadImage': 'uploadImage',
            	'click .effect img': 'replaceAvatar',
            },
            uploadImage:function() {
                var effect_id = 1;
                var boy_id = 1;
            	wx_choose_image(boy_id, effect_id);
            },
            replaceAvatar: function(e) {
                var num = $(e.currentTarget).index();  //获取点击图片的索引值
                var src = $(e.currentTarget).attr("src"); //获取点击图片的src
                $(".after").find("img").attr("src", src);
                $("#uploadImage").attr("num", num);
            },
            onRender: function() {
                var index = window.location.hash.substr(1); //获取url上的索引值
                $("#uploadImage").attr("index", index);
                var avatarEffect = $(".after").find(".avatar-effect").eq(index); //效果图

                $(".before").find("img").eq(index).removeClass("none").siblings("img").addClass("none");
                $(".effect").eq(index).removeClass("none").siblings(".avatar-effect").addClass("none");

                avatarEffect.removeClass("none").siblings("img").addClass("none"); //取索引值的效果图
                avatarEffect.find("img").eq(1).removeClass("none").siblings("img").addClass("none");  
            }
        });
    });
