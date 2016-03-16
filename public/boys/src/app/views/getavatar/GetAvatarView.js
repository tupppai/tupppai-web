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
            },
            popHowto : function(e) {
            	$(".pop-howto").removeClass("none");
            	if($(e.target).hasClass("pop-howto")) {
            		$(".pop-howto").addClass("none");
            	}
            }
        });
    });

