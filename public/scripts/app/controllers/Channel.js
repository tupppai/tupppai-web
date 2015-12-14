define(['underscore', 'app/collections/Replies', 'app/views/channel/ChannelView'],
    function (_, Replies, ChannelView) {
        "use strict";

        return function() {
            // var replies = new Replies;
            // replies.data.width = 300;

            var view = new ChannelView({
                // collection: replies
            });

            window.app.content.show(view);
        };
    });
