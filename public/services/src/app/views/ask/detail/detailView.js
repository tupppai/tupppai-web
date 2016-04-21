define(['app/views/base', 'tpl!app/views/ask/detail/detail.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	"click .commentLine": "replyPopup",
            	"click .cancel": "replyPopupHide",
                "click .window-fix": "windowFix",
            	"click .comment-btn": "commons",
            },
            replyPopup: function(e) {
            	$("#replyWindow").removeClass("hide");
            },            
            replyPopupHide: function(e) {
            	$(".window-fix").addClass("hide");
            },            
            windowFix: function(e) {
            	if($(e.target).hasClass("window-fix")) {
            		$(e.currentTarget).addClass("hide");
            	}
            },
            commons: function(e) {
                $("#commentWindow").removeClass("hide")
            }
        });
    });


