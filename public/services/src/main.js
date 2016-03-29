require.config({
    paths: {
        backbone: 'lib/backbone/backbone',
        underscore: 'lib/underscore/underscore',
        zepto: 'lib/zepto/zepto.min',
        deferred: 'lib/simply-deferred/deferred',
        marionette: 'lib/backbone/backbone.marionette',
        fastclick: 'lib/fastclick/fastclick',
        lazyload: 'lib/lazyload/lazyload',
        tpl: 'lib/require/tpl',
        common: 'lib/common',
        wechat: 'lib/wechat/wechat',
    },
    shim: {
        zepto: {
            exports: '$'
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
        wechat : {
            deps: ['zepto'],
            exports: 'wechat'
        },
        lazyload: {
            depts: ['zepto'],
            exports: 'lazyload'
        },   
        fastclick: {
            depts: ['zepto'],
            exports: 'fastclick'
        },
    }
});

require(['app/app', 'backbone', 'app/router'],
    function (App, Backbone) { 
        "use strict"; 

        window.app = App;
        App.start();

        Backbone.history.start(); 
        Backbone.history.on("all", function (route, router) {
        });
    });
