define(['app/views/base', 'tpl!app/views/getavatar/GetAvatarView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	"click #uploadPopup": "popHowto",
            	"click .pop-howto": "popHowto",
                "click #uploadImage": "uploadImage"
            },
            initialize:function() {
                // 微信好友文案修改
                var options = {};
                options.id    = 1;
                share_friend(options,function(){},function(){})
                share_friend_circle(options,function(){},function(){})

            },
            uploadImage: function() {
                //todo 凌伟
                var effect_id = 1; //效果ID
                var boy_id = 1; //男神ID
                wx_choose_image(boy_id, effect_id);
            },
            popHowto : function(e) {
            	$(".pop-howto").removeClass("none");
            	if($(e.target).hasClass("pop-howto")) {
            		$(".pop-howto").addClass("none");
            	}
            },
            onRender: function() {
                setTimeout(function() {
                    var index = window.location.hash.substr(1); //获取url上的索引值
                    var avatarEffect = $(".after").find(".avatar-effect").eq(index); //效果图
                    var num = Math.round(Math.random() * 2); //取随机数

                    $(".get-avatar").attr("index", index); //把传进来的索引值赋值
                    $(".before").find("img").eq(index).removeClass("none").siblings("img").addClass("none"); //取索引值的原图

                    avatarEffect.removeClass("none").siblings("img").addClass("none"); //取索引值的效果图
                    avatarEffect.find("img").eq(num).removeClass("none").siblings("img").addClass("none");
                },100)
            }
        });
    });

