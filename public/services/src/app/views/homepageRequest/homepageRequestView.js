define(['app/views/base', 'tpl!app/views/homepageRequest/homepageRequest.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events : {
            	"click .list-item" : "switchHomePage",
            },
            switchHomePage: function(e) {
            	$(e.currentTarget).removeClass("unactive")
            			.siblings().addClass("unactive");
            },
        });
    });
