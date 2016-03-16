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
            	wx_upload_image();
            }
        });
    });
