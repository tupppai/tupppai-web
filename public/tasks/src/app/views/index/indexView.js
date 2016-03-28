define(['app/views/base', 'tpl!app/views/index/index.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	"mouseenter .share": "shareFriends",
            	"mouseleave .share": "shareFriends",
            },
            shareFriends : function(e) {
            	console.log(e.type)
            	if(e.type == 'mouseenter') {
            		$(e.currentTarget).parents(".section-rightarea").find(".qrcode").removeClass("none");
            	} else {
            		$(e.currentTarget).parents(".section-rightarea").find(".qrcode").addClass("none");
            	}
            }
        });
    });


