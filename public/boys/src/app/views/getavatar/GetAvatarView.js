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
            },
            onRender:function() {
            },
            clickShare: function(e) {
                $(".share-mask").removeClass("none");
                if ($(e.target).hasClass("share-mask")){
                    $(".share-mask").addClass("none");
                }
                //微信好友文案修改
                // var option = {};
                // options.link    = 'http://' + location.hostname + href;
                
                // share_friend(options,function(){},function(){})

                // //微信分享朋友圈文案
                // var options = {};
                // options.title   = wx_share_nickname+'邀请您一起出品电影《'+wx_film_title+'》';
                // options.link    = 'http://' + location.hostname + '/img/favicon.ico';
                
                // share_friend_circle(options,function(){},function(){})
            },
            uploadImage: function() {
                //todo
                var index = $(".get-avatar").attr("index");
                var effect_id = 1; //效果ID
                var boy_id = index; //男神ID
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
                    var randomDescribe = $(".tips").find(".random").eq(index); //效果图描述
                    var num = $(".after").attr("num"); //取随机数
                    if(!num) {
                        num = Math.round(Math.random() * 2);
                        // $(".after").attr("num", num);
                    }

                    $(".get-avatar").attr("index", index); //把传进来的索引值赋值
                    $(".before").find("img").eq(index).removeClass("none").siblings("img").addClass("none"); //取索引值的原图

                    avatarEffect.removeClass("none").siblings("img").addClass("none"); //取索引值的效果图
                    avatarEffect.find("img").eq(num).removeClass("none").siblings("img").addClass("none");                    

                    randomDescribe.removeClass("none").siblings("。random").addClass("none"); //取索引值的效果图
                    randomDescribe.find("img").eq(num).removeClass("none").siblings("img").addClass("none");
                },100)
            }
        });
    });

