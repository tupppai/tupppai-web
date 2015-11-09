define(['underscore', 'app/collections/Replies', 'app/views/hot/HotFlowsView'],
    function (_, Replies, HotFlowsView) {
        "use strict";

        return function() {
            var replies = new Replies;
            replies.data.width = 300;

            var view = new HotFlowsView({
                collection: replies
            });

            window.app.content.show(view);
        };
    });
