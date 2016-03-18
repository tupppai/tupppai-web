define(['app/views/base', 'tpl!app/views/uploadagain/UploadAgainView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	'click #uploadImage': "uploadImage"
            },
            uploadImage:function() {
            	var effect_id = $("#uploadImage").attr("effectId");; //效果ID
                var boy_id =  $("#uploadImage").attr("boyId");; //男神ID
                wx_choose_image(boy_id, effect_id);
            },

        });
    });