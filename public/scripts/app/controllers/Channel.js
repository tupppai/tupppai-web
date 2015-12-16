define(['underscore', 
        'app/collections/Channels', 
        'app/views/channel/ChannelView',
        'app/views/channel/ChannelWorksView',
        'app/views/channel/ChannelFoldView',
        'app/views/channel/ChannelDemandView',
        ],
    function (_,  Channels, ChannelView, ChannelWorksView, ChannelFoldView, ChannelDemandView) {
        "use strict";

        return function() {

            var channel = new Channels;
            channel.data.channel_id = 1002;
            channel.data.size = 5;
            channel.data.type = "ask";

            var view = new ChannelView();
            window.app.content.show(view);

            var channelDemand = new Backbone.Marionette.Region({el:"#channelDemand"});
            var view = new ChannelDemandView({
                collection: channel
            });
            channelDemand.show(view);

            channel.data.type = "replies";
            var channelWorksPic = new Backbone.Marionette.Region({el:"#channelWorksPic"});
            var view = new ChannelWorksView({
                collection: channel
            });
            channelWorksPic.show(view);


            // channel.data.type = "replies";
            // var channelWorksFold = new Backbone.Marionette.Region({el:"#channelWorksFold"});
            // var view = new ChannelFoldView({
            //     collection: channel
            // });
            // channelWorksFold.show(view);

        };
    });
