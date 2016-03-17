define(['app/views/base', 'tpl!app/views/shareavatar/ShareAvatarView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click #uploadImage': 'uploadImage',
            	'click .effect-list span': 'replaceAvatar',
            },
            uploadImage:function() {
                var effect_id = 1;
                var boy_id = 1;
            	wx_choose_image(boy_id, effect_id);
            },
            replaceAvatar: function(e) {
                var index = $(e.currentTarget).index();  //获取点击图片的索引值
                var src = $(e.currentTarget).find("img").attr("src"); //获取点击图片的src
                $(".after").attr("src", src);
                $("#uploadImage").attr("index", index);
            }
        });
    });
