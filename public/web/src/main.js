require.config({
    paths: {
        backbone: 'lib/backbone/backbone',
        underscore: 'lib/underscore/underscore',
        zepto: 'lib/zepto/zepto',
        deferred: 'lib/simply-deferred/deferred',
        marionette: 'lib/backbone/backbone.marionette',
        tpl: 'lib/require/tpl',
        common: 'lib/common',
        lazyload: 'lib/lazyload/lazyload',
        swiper: 'lib/swiper/swiper',
        fancybox: 'lib/fancybox/jquery.fancybox',
        masonry: 'lib/masonry/masonry',
        asyncList: 'lib/component/asyncList',
        imageLazyLoad: 'lib/imagesloaded/imageLazyLoad',
        jquery: 'lib/jquery/jquery-1.9.0',
    },
    shim: {
        jquery: {
            exports: 'jquery'
        },
        zepto: {
            exports: '$'
        },
        deferred: {
            deps: ['jquery']
        },
        underscore: {
            exports: '_'
        },
        backbone: {
            deps: ['jquery', 'underscore', 'common'],
            exports: 'Backbone'
        },
        marionette: {
            deps: ['jquery', 'deferred', 'underscore', 'backbone'],
            exports: 'Marionette'
        },
        common: {
            deps: ['jquery'],
            exports: 'common'
        },
        lazyload: {
            depts: ['jquery'],
            exports: 'lazyload'
        },
        swiper: {
            depts: ['jquery'],
            exports: 'swiper'
        },
        fancybox: {
            deps: ['jquery',],
            exports: 'fancybox'
        },
        masonry: {
            depts: ['jquery'],
            exports: 'masonry'
        },
        asyncList: {
            depts: ['jquery'],
            exports: 'asyncList'
        },
        imageLazyLoad: {
            depts: ['jquery'],
            exports: 'imageLazyLoad'
        },
    }
});

require(['app/app', 'backbone', 'app/router','jquery','fancybox'],
    function (App, Backbone, router, jquery,fancybox) {
        "use strict";

        window.app = App;
        app.start();


        new router();
        Backbone.history.start();
        Backbone.history.on("all", function (route, router) {
            $("#footer-section").css({
                paddingBottom: "0"
            });
            $(".header").css({
                position: "static"
            });
            $(".container > div").css({
                borderTop: "0.71rem solid #f7f7f7"
            })
        });
    });
