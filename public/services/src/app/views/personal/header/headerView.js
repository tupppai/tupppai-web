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
                'click .nav-item': 'clickNav'
            },
            clickNav: function(e) {
                $(e.currentTarget).addClass("active").siblings(".nav-item").removeClass("active");

                var type = $(e.currentTarget).attr("data-type");
                var uid  = this.$(".header-portrait").attr("data-id");

                this.trigger('click:nav', type, uid);
            },
            onShow: function() {
                this.$("li.nav-item").removeClass('active');
                this.$("li.nav-item[data-type='ask']").addClass('active');
                var clickId = this.$(".header-portrait").attr("data-id");

                var currentId = $('body').attr("data-uid");
                if(clickId == currentId) {
                    $(".own").removeClass("hide");
                } else {
                    $(".ta").removeClass("hide");
                }
            }
        });
    });


