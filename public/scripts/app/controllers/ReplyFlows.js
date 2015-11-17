define(['underscore', 'app/collections/Replies', 'app/views/reply/ReplyFlowsView'],
    function (_, Replies, ReplyFlowsView) {
        "use strict";

        return function() {
            var replies = new Replies;
            // replies.data.width = 300;

            var view = new ReplyFlowsView({
                collection: replies
            });

            window.app.content.show(view);
        };
    });
