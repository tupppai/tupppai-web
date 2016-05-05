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
                var id = $(".header-portrait").attr("data-id");
                var self = this;
            	$(e.currentTarget).addClass("active").siblings(".nav-item").removeClass("active");

                var type = $(e.currentTarget).attr("data-type");
                self.options.listenList.url= "/v2/" + type + "?uid=" + id;
                if(type == "ask") {
                    self.options.listenList.url= "/v2/asks?uid="+ id +"&type=ask";
                }                

                self.options.listenList.type = type;
                self.options.listenList.reset();
                self.options.listenList.fetch();
            },
            onShow: function() {
                var type = this.options.listenList.type;
                this.$el.find("li.nav-item").removeClass('active');
                this.$el.find("li.nav-item[data-type='"+type+"']").addClass('active');

                var clickId = $(".header-portrait").attr("data-id");
                var currentId = $('body').attr("data-uid");
                if(clickId == currentId) {
                    $(".own").removeClass("hide");
                } else {
                    $(".ta").removeClass("hide");
                }
            }
        });
    });


