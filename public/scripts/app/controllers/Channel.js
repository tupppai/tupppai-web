define(['underscore', 
        'app/collections/Asks', 
        'app/collections/Categories', 
        'app/views/channel/ChannelView',
        'app/views/channel/ChannelWorksView',
        'app/views/channel/ChannelNavView',
        'app/views/channel/ChannelFoldView',
        'app/views/channel/ChannelDemandView',
        ],
    function (_, Asks, Categories, ChannelView, ChannelWorksView, ChannelNavView, ChannelFoldView, ChannelDemandView) {
        "use strict";

        return function() {

            
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
                var ask = new Asks;
                ask.data.size = 6;
                var channelDemand = new Backbone.Marionette.Region({el:"#channelDemand"});
                var view = new ChannelDemandView({
                    collection: ask
                });
                channelDemand.show(view);

        };
    });
