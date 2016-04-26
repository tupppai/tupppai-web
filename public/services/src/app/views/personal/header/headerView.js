define([
		'tpl!app/views/personal/header/header.html',
		'app/views/personal/work/workView', 
		'app/views/personal/processing/processingView',
		'app/views/personal/reply/replyView',
		],
    function (template, workView, processingView, replyView) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	"click .nav-item": "personalTap"
            },
            personalTap: function(e) {
                var self = this;
            	$(e.currentTarget).addClass("active").siblings(".nav-item").removeClass("active");

                var type = $(e.currentTarget).attr("data-type");
                self.collection.url= "/v2/asks?uid=1&type=" + type;
                self.collection.type = type;
                self.collection.reset();
                self.collection.fetch();
            },
            onRender: function() {
                var type = this.collection.type;
                this.$el.find("li.nav-item").removeClass('active');
                this.$el.find("li.nav-item[data-type='"+type+"']").addClass('active');
            }
        });
    });


