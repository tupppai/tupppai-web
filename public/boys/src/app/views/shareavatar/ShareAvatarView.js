define(['app/views/base', 'tpl!app/views/shareavatar/ShareAvatarView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	'click #uploadImage': 'uploadImage'
            },
            uploadImage:function() {
                var effect_id = 1;
                var boy_id = 1;
            	wx_choose_image(boy_id, effect_id);
            }
        });
    });
