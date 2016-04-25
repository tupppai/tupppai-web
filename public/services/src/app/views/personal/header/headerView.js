define(['app/views/base', 
		'tpl!app/views/personal/header/header.html',
		'app/views/personal/work/workView', 
		'app/views/personal/processing/processingView',
		'app/views/personal/reply/replyView',
		],
    function (View, template, workView, processingView, replyView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	"click .nav-item": "personalTap"
            },
            personalTap: function(e) {
            	$(e.currentTarget).addClass("active").siblings(".nav-item").removeClass("active");

                if($(e.currentTarget).hasClass("work")) {
                    var model = new window.app.model();
                    model.url= "v2/asks?uid=1&type=ask";
                    // var collections = new window.app.collections();
                    // collections.url= "/v2/ask";
                    var _content = new Backbone.Marionette.Region({el:"#_content"});
                    var work = new workView({
                        // collections: collections
                        model: model
                    });
                    _content.show(work);
                }
                if($(e.currentTarget).hasClass("processing")) {

                    var _content = new Backbone.Marionette.Region({el:"#_content"});
                    var processing = new processingView({
                    });
                    _content.show(processing);
                }            	
                if($(e.currentTarget).hasClass("reply")) {

                    var _content = new Backbone.Marionette.Region({el:"#_content"});
                    var reply = new replyView({
                    });
                    _content.show(reply);
            	}
            }
        });
    });


