define(['tpl!app/views/personal/original/original.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'main-ask-section loading clearfix',
            template: template,
            events: {
            	"click .commentOption": "replyPopup",
            	"click .cancel": "replyPopupHide",
            	"click .window-fix": "windowFix",
            },
            onShow: function() {

            },
            replyPopup: function(e) {
            	$("#replyWindow").removeClass("hide")
            },            
            replyPopupHide: function(e) {
            	$("#replyWindow").addClass("hide")
            },            
            windowFix: function(e) {
            	if($(e.target).hasClass("window-fix")) {
            		$("#replyWindow").addClass("hide")
            	}
            },
        });
    });


