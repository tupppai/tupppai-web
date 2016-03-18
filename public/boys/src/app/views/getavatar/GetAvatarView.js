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
                "click #uploadImage": "uploadImage",
                "click .share": "clickShare",
                "click .share-mask": "clickShare",
                "click #downloadImage": "downloadImage",
            },
            downloadImage:function() {
                wx_download_image();
            },
            clickShare: function(e) {
                $(".share-mask").removeClass("none");
                if ($(e.target).hasClass("share-mask")){
                    $(".share-mask").addClass("none");
                }
            },
            initialize:function() {

            },
            uploadImage: function() {
                //todo
                var boyId = $(".get-avatar").attr("index");
                var effectId = $("#contentView").attr("num");
                var effect_id = effectId; //效果ID
                var boy_id = boyId; //男神ID
                wx_choose_image(boy_id, effect_id);
            },
            popHowto : function(e) {
            	$(".pop-howto").removeClass("none");
            	if($(e.target).hasClass("pop-howto")) {
            		$(".pop-howto").addClass("none");
            	}
            },
            onShow: function() {
                var url = location.href; //编译url上的乱码中文
                var tmp1 = url.split("#")[1]; //获取到？后面的
                var index = tmp1.split("-")[0]; //获取男神id
                var num = tmp1.split("-")[1]; //获取随机数
                var avatarEffect = $(".after").find(".avatar-effect").eq(index); //效果图
                var randomDescribe = $(".tips").find(".random").eq(index); //效果图描述
                
                $(".get-avatar").attr("index", index); //把传进来的索引值赋值
                $(".before").find("img").eq(index).removeClass("none").siblings("img").addClass("none"); //取索引值的原图

                avatarEffect.removeClass("none").siblings("img").addClass("none"); //取索引值的效果图
                avatarEffect.find("img").eq(num).removeClass("none").siblings("img").addClass("none");                    

                randomDescribe.removeClass("none").siblings(".random").addClass("none"); //取索引值的效果图
                randomDescribe.find("img").eq(num).removeClass("none").siblings("img").addClass("none");

                // 微信好友文案修改
                var options = {};
                options.id    = index;
                share_friend(options,function(){},function(){})
                share_friend_circle(options,function(){},function(){})
            }
        });
    });


