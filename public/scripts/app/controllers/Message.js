define(['underscore', 'app/views/message/MessageView'],
    function (_, MessageView) {
        "use strict";

        return function() {
            var view = new MessageView();

            window.app.home.close();
            window.app.content.show(view);
        };
    });
