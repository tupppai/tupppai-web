define([
        'app/views/message/MessageView', 
	   ],
    function ( MessageView) {
        "use strict";

        return function() {
            // var messages = new Messages;
            // messages.data.type = 'comment';

            var view = new MessageView({});

            window.app.content.show(view);
        };
    });
