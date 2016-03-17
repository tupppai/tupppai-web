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
<<<<<<< HEAD
                var effect_id = 1;
                var boy_id = 1;
            	wx_choose_image(boy_id, effect_id);
=======
            	wx_choose_image();
            },
            replaceAvatar: function(e) {
                var src = $(e.currentTarget).attr("src");
                $(".after").attr("src", src);
>>>>>>> 23e577584b8d8b3a84a2e2aafd4fb47f75d8e699
            }
        });
    });
