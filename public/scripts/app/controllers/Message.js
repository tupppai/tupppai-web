define(['underscore', 'app/views/message/MessageView', 'app/collections/Messages'],
    function (_, MessageView, Messages) {
        "use strict";

        return function() {
            var messages = new Messages;
            messages.data.type = 'comment';

            var view = new MessageView({collection: messages});

            window.app.content.show(view);
        };
    });
