define(['underscore', 
        'app/collections/Replies', 
        'app/views/channel/ChannelView',
        'app/views/channel/ChannelWorksView',
        'app/views/channel/ChannelFoldView',

        ],
    function (_, Replies, ChannelView, ChannelWorksView, ChannelFoldView) {
        "use strict";

        return function() {
            var replies = new Replies;

            var view = new ChannelView({
            });
            window.app.content.show(view);

            var channelWorksPic = new Backbone.Marionette.Region({el:"#channelWorksPic"});
            var view = new ChannelWorksView({
                collection: replies
            });
            channelWorksPic.show(view);

            // var channelWorksFold = new Backbone.Marionette.Region({el:"#channelWorksFold"});
            // var view = new ChannelFoldView({
            //     collection: replies
            // });
            // channelWorksFold.show(view);



        };
    });
