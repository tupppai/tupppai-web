define(['underscore', 'app/models/Ask','app/views/DetailView'],
    function (_,  Ask, DetailView) {
        "use strict";

        return function(id) {

            var ask = new Ask;
        	ask.url = '/asks/'+id;
            ask.fetch();
            var view = new DetailView({
            	model: ask
            });

			window.app.content.show(view);
        };
    });
