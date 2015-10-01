define(['underscore', 'fancybox', 'app/models/Ask','app/views/DetailView', 'app/views/PopupView'],
    function (_, fancybox, Ask, DetailView, PopupView) {
        "use strict";

        return function(id) {

            var ask = new Ask;
        	ask.url = '/asks/'+id;
            ask.fetch();
            var view = new DetailView({
            	model: ask
            });
			window.app.content.show(view);

            var view = new PopupView({
                model: ask
            });
            window.app.modal.show(view);
        };
    });
