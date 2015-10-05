define(['underscore', 'app/models/Ask','app/views/ShowView','app/views/PopupView'],
    function (_, Ask, ShowView, PopupView) {
        "use strict";

        return function(id) {

        	var ask = new Ask;
        	ask.url = '/asks/'+id;
            ask.fetch();
            var view = new ShowView({
                model: ask
            });
			window.app.content.show(view);

            var view = new PopupView({
                model: ask
            });
            window.app.modal.show(view);
        };
    });
