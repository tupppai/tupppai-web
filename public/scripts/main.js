require.config({
    urlArgs: "bust=v0.0.2",
    paths: {
        backbone: 'lib/backbone/backbone',
        underscore: 'lib/underscore/underscore',
        jquery: 'lib/jquery/jquery-1.9.0',
        marionette: 'lib/backbone/backbone.marionette',
        tpl: 'lib/require/tpl',
        imagesLoaded: 'lib/imagesloaded/imagesloaded',
        common: 'lib/common',
        fancybox: 'lib/fancybox/jquery.fancybox',
        //mousewheel: 'lib/fancybox/jquery.mousewheel',
        uploadify: 'lib/uploadify/jquery.uploadify.min'
    },
    shim: {
        jquery: {
            exports: 'jQuery'
        },
        underscore: {
            exports: '_'
        },
        backbone: {
            deps: ['jquery', 'underscore', 'common'],
            exports: 'Backbone'
        },
        marionette: {
            deps: ['jquery', 'underscore', 'backbone'],
            exports: 'Marionette'
        },
        imagesLoaded: {
            deps: ['jquery'],
            exports: 'imagesLoaded'
        },
        common: {
            deps: ['jquery'],
            exports: 'common'
        },
        /*
        mousewheel: {
            deps: ['jquery'],
            exports: 'mousewheel'
        },
        */
        fancybox: {
            deps: ['jquery'/*, 'mousewheel'*/],
            exports: 'fancybox'
        },
        uploadify: {
            deps: ['jquery'],
            exports: 'uploadify'
        }
        //'lib/backbone/backbone.localStorage': ['backbone']
    }
});

require(['app/App', 'backbone', 'app/Router'],
    function (app, Backbone, Router) { 
        "use strict"; 

        window.app = app;

        app.start();
        new Router();

        Backbone.history.start(); 
        console.log('begin...');
    });
