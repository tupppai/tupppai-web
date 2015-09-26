define(['underscore', 'app/models/Ask','app/views/ShowView','tpl!app/templates/ShowView.html'],
    function (_,  Ask ,ShowView, showTemplate) {
        "use strict";

        return function(id) {
        	var ask = new Ask;
        	ask.url = '/asks/'+id;
            ask.fetch();
            var view = new ShowView({
            	model: ask
            });

			window.app.content.show(view);
        };
    });
