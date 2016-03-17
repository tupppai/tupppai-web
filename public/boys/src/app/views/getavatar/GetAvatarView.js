define(['app/views/base', 'tpl!app/views/getavatar/GetAvatarView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	"click .upload": "popHowto",
            	"click .pop-howto": "popHowto",
                "click #uploadImage": "uploadImage"
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
            }
        });
    });

