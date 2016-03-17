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
            	wx_choose_image();
            },
            replaceAvatar: function(e) {
                var src = $(e.currentTarget).attr("src");
                $(".after").attr("src", src);
            }
        });
    });
