define(['tpl!app/views/personal/empty/empty.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            onShow: function() {
	            var clickId = this.$(".header-portrait").attr("data-id");
	            var currentId = $('body').attr("data-uid");
	            if(clickId == currentId) {
	                $(".empty-buttom").removeClass("hide");
	            } else {
	                $(".empty-buttom").addClass("hide");
	            }
            }
        });
    });


