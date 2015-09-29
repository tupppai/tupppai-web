define(['underscore', 'app/models/Ask', 'app/views/CommentView'],
    function (_, Ask, CommentView) {
        "use strict";

   return function(id) {

            var ask = new Ask;
        	ask.url = '/asks/'+id;
            ask.fetch();
            var view = new CommentView({
            	model: ask
            });

			window.app.content.show(view);
        };
    });
