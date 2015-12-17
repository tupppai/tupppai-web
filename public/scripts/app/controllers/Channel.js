define(['underscore', 
        'app/collections/Channels', 
        'app/collections/Asks', 
        'app/collections/Categories', 
        'app/views/channel/ChannelView',
        'app/views/channel/ChannelWorksView',
        'app/views/channel/ChannelNavView',
        'app/views/channel/ChannelFoldView',
        'app/views/channel/ChannelDemandView',
        ],
    function (_,  Channels, Asks, Categories, ChannelView, ChannelWorksView, ChannelNavView, ChannelFoldView, ChannelDemandView) {
        "use strict";

        return function(channel_id) {
            var category_type = "channel";
            var channel = new Channels;
            channel.data.channel_id = 1002;
            channel.data.category_type = category_type;
            channel.data.type = "replies";
            
            setTimeout(function(){
                $('.header-back').addClass("height-reduce");
                $(".header-nav[data-id=6]").addClass('bgc-change');
            },400);
            setTimeout(function(){
                $(".pic-icon").trigger("click");
            },2000);
            // main
            var view = new ChannelView();
            window.app.content.show(view);
            
            // 导航栏
            var categorie = new Categories;
            var channelNav = new Backbone.Marionette.Region({el:"#channelNav"});
            var view = new ChannelNavView({
                collection: categorie
            });
            channelNav.show(view);
            
            // 求P内容
            setTimeout(function(){
                var ask = new Asks;
                ask.data.size = 6;
                var channelDemand = new Backbone.Marionette.Region({el:"#channelDemand"});
                var view = new ChannelDemandView({
                    collection: ask
                });
                channelDemand.show(view);
            },500);

            // // 频道
            // var channelWorksPic = new Backbone.Marionette.Region({el:"#channelWorksPic"});
            // var view = new ChannelWorksView({
            //     collection: channel
            // });
            // channelWorksPic.show(view);

            // // 频道
            // var channelWorksFold = new Backbone.Marionette.Region({el:"#channelWorksFold"});
            // var view = new ChannelFoldView({
            //     collection: channel
            // });
            // channelWorksFold.show(view);
        };
    });
