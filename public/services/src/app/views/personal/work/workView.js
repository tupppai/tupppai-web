define(['tpl!app/views/personal/work/work.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'main-ask-section',
            template: template,
            events: {
            	"click .commentOption": "replyPopup",
            	"click .cancel": "replyPopupHide",
            	"click .window-fix": "windowFix",
            },
            initialize: function() {
                this.listenTo(this.collection, 'change', this.render);
            },
            // initialize: function() {
            //     this.listenTo(this.model, 'change', this.render);
            // },
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


