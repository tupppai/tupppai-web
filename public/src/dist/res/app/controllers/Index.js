define([
        'app/models/Ask', 
        'app/collections/Asks', 
        'app/collections/Banners', 
        'app/views/index/IndexView', 
        'app/views/index/IndexItemView', 
        'app/views/index/IndexBannerView', 
       ],
    function (Ask, Asks, Banners, IndexView, IndexItemView, IndexBannerView) {
        "use strict";

        return function() {

    

            $("title").html("图派-首页");
            $('.header-back').removeClass("height-reduce");
            
            var asks = new Asks;
            asks.url = '/populars';
            asks.data.size = 16;

            $('.title-bar').removeClass("hide");
            $('.header-back').removeClass("height-reduce");

            var view = new IndexView({});
            window.app.content.show(view);
            
            var indexItem = new Backbone.Marionette.Region({el:"#indexItemView"});
            var view = new IndexItemView({
                collection: asks
            });
            indexItem.show(view);

            var banners = new Banners;
            
            var indexBanner = new Backbone.Marionette.Region({el:"#indexBannerView"});
            var view = new IndexBannerView({
                collection: banners
            });
            indexBanner.show(view);

            var widthHide = $(window).width();
        };
    });
