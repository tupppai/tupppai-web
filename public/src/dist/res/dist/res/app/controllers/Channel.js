define(['underscore', 
        'app/collections/Categories', 
        'app/views/channel/ChannelView',
        'app/views/channel/ChannelWorksView',
        'app/views/channel/ChannelNavView',
        'app/views/channel/ChannelFoldView',
        ],
    function (_, Categories, ChannelView, ChannelWorksView, ChannelNavView, ChannelFoldView) {
        "use strict";

        return function(type) {
            
            // main
            var type = type;
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
                if(type == 'ask' ) {
                    $(".header-nav[data-type=ask]").trigger('click');
                } else if(type == 'reply') {
                    $(".header-nav[data-type=reply]").trigger('click');
                } else if(type) {
                    $(".header-nav[data-id="+ type +"]").trigger('click');
                } else {
                    $(".nav-scroll div:first").trigger('click');
                }
                $('.header-back').addClass("height-reduce");
            },1000)
        };
    });
