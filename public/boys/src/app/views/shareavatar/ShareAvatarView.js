define(['app/views/base', 'tpl!app/views/shareavatar/ShareAvatarView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click #uploadImage': 'uploadImage',
            	'click .effect-list img': 'replaceAvatar',
            },
            uploadImage:function() {
                //todo 凌伟
                var effect_id = 1;
                var boy_id = 1;
            	wx_choose_image(boy_id, effect_id);
            },
            replaceAvatar: function(e) {
                var src = $(e.currentTarget).attr("src");
                $(".after").attr("src", src);
            }
        });
    });
