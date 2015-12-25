define(['underscore', 
        'app/collections/Categories', 
        'app/views/channel/ChannelView',
        'app/views/channel/ChannelWorksView',
        'app/views/channel/ChannelNavView',
        'app/views/channel/ChannelFoldView',
        ],
    function (_, Categories, ChannelView, ChannelWorksView, ChannelNavView, ChannelFoldView) {
        "use strict";

        return function() {
            // main
            var view = new ChannelView();
            window.app.content.show(view);
            
            // 导航栏
            var categories = new Categories;
            var channelNav = new Backbone.Marionette.Region({el:"#channelNav"});
            var view = new ChannelNavView({
                collection: categories
            });
            channelNav.show(view);
            setTimeout(function(){
                $(".header-nav:first").trigger('click');
                $('.header-back').addClass("height-reduce");
            },1000)
        };
    });
