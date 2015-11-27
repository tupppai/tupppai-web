define(['underscore',  'app/views/replydetail/ReplyDetailView'],
    function (_,  ReplyDetailView) {
        "use strict";

        return function() {

            var view = new ReplyDetailView({});

            window.app.content.show(view);
        };
    });
