define(['underscore', 'app/collections/Replies', 'app/views/hot/HotsView'],
    function (_, Replies, HotsView) {
        "use strict";

        return function() {
            var replies = new Replies;
            replies.data.width = 300;

            var view = new HotsView({
                collection: replies
            });

            window.app.content.show(view);
        };
    });
