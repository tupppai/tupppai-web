define(['tpl!app/views/personal/work/work.html', 'waterfall'],
    function (template, waterfall) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'main-ask-section grid-item',
            template: template,
            events: {
            	"click .commentOption": "replyPopup",
            	"click .cancel": "replyPopupHide",
            	"click .window-fix": "windowFix",
            },
            onShow: function() {
                // 渲染瀑布流
                $('.grid').waterfall({
                  // options
                  root: '.grid',
                  itemSelector: '.grid-item',
                  columnWidth: $('.grid-item').width()/2
                });
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


