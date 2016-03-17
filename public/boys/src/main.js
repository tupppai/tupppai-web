require.config({
    paths: {
        backbone: 'lib/backbone/backbone',
        underscore: 'lib/underscore/underscore',
        zepto: 'lib/zepto/zepto',
        deferred: 'lib/simply-deferred/deferred',
        marionette: 'lib/backbone/backbone.marionette.min',
        tpl: 'lib/require/tpl',
        common: 'lib/common',
        lazyload: 'lib/lazyload/lazyload',
        fastclick: 'lib/fastclick/fastclick',
        swiper: 'lib/swiper/swiper',
        fx: 'lib/zepto/fx',
    },
    shim: {
        zepto: {
            exports: 'Zepto'
        },
        deferred: {
            deps: ['zepto']
        },
        underscore: {
            exports: '_'
        },
        backbone: {
            deps: ['zepto', 'underscore', 'common'],
            exports: 'Backbone'
        },
        marionette: {
            deps: ['zepto', 'deferred', 'underscore', 'backbone'],
            exports: 'Marionette'
        },
        common: {
            deps: ['zepto'],
            exports: 'common'
        },
        lazyload: {
            depts: ['zepto'],
            exports: 'lazyload'
        },   
        fastclick: {
            depts: ['zepto'],
            exports: 'fastclick'
        },        
        swiper: {
            depts: ['zepto'],
            exports: 'swiper'
        },        
        fx: {
            depts: ['zepto'],
            exports: 'fx'
        }
    }
});

require(['app/app', 'backbone', 'app/router'],
    function (App, Backbone) { 
        "use strict"; 

        window.app = App;
        App.start();

        // wx_sign();

        Backbone.history.start(); 
        Backbone.history.on("all", function (route, router) {
        });
    });
