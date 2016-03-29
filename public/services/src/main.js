require.config({
    paths: {
        backbone: 'lib/backbone/backbone',
        underscore: 'lib/underscore/underscore',
        jquery: 'lib/jquery/jquery-1.9.0',
        deferred: 'lib/simply-deferred/deferred',
        marionette: 'lib/backbone/backbone.marionette',
        tpl: 'lib/require/tpl',
        common: 'lib/common',
        wechat: 'lib/wechat/wechat',
    },
    shim: {
        jquery: {
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
        wechat : {
            deps: ['jquery'],
            exports: 'wechat'
        },
        lazyload: {
            depts: ['jquery'],
            exports: 'lazyload'
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
