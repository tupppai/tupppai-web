define(['tpl!app/views/channel/otherChannel/otherChannel.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            onShow: function() {
            	var self = this.$el;
            	self.css({
            		opacity: 0
            	});
            	setTimeout(function() {
	               self.css({
	            		opacity: 1
	            	});
            	}, 500)
            }
        });
    });
