define(['underscore', 'app/collections/Replies', 'app/views/upload/InprogressView'],
    function (_, Replies, InprogressView) {
        "use strict";

        return function() {
            
            var view = new InprogressView({});

            window.app.content.show(view);
        };
    });
