define([
        'app/models/Ask', 
        'app/collections/Asks', 
        'app/collections/Banners', 
        'app/views/index/IndexView', 
        'app/views/index/IndexItemView', 
        'app/views/index/IndexRecommendView', 
       ],
    function (Ask, Asks, Banners, IndexView, IndexItemView, IndexRecommendView) {
        "use strict";

        return function() {
            var view = new IndexView();
            window.app.content.show(view);

            var asks = new Asks;
            asks.url = '/populars';

            var indexItem = new Backbone.Marionette.Region({el:"#indexItemView"});
            var view = new IndexItemView({
                collection: asks
            });
            indexItem.show(view);

            var banners = new Banners;
            
            var indexRecommend = new Backbone.Marionette.Region({el:"#indexRecommendView"});
            var view = new IndexRecommendView({
                collection: banners
            });
            indexRecommend.show(view);
        };
    });
