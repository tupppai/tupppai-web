require.config({
    paths: {
        backbone: 'lib/backbone/backbone',
        underscore: 'lib/underscore/underscore',
        jquery: 'lib/jquery/jquery-1.9.0',
        cookie: 'lib/jquery/jquery.cookie',
        marionette: 'lib/backbone/backbone.marionette',
        tpl: 'lib/require/tpl',
        imagesLoaded: 'lib/imagesloaded/imagesloaded',
        common: 'lib/common',
        fancybox: 'lib/fancybox/jquery.fancybox',
        swipe: 'lib/swipe/swipe',
        masonry: 'lib/masonry/masonry.pkgd',
        masonryMin: 'lib/masonry/jquery.masonry.min',
        //mousewheel: 'lib/fancybox/jquery.mousewheel',
        uploadify: 'lib/uploadify/jquery.uploadify.min',
        emojiSelector: 'lib/face-selector/face-selector',
        emojione: 'lib/emojione/emojione',
        uploadify: 'lib/uploadify/jquery.uploadify.min'
    },
    shim: {
        jquery: {
            exports: 'jQuery'
        },
        cookie: {
            exports: 'cookie'
        },
        underscore: {
            exports: '_'
        },
        backbone: {
            deps: ['jquery', 'cookie', 'underscore', 'common'],
            exports: 'Backbone'
        },
        marionette: {
            deps: ['jquery', 'underscore', 'backbone', 'emojiSelector'],
            exports: 'Marionette'
        },
        imagesLoaded: {
            deps: ['jquery'],
            exports: 'imagesLoaded'
        },
        swipe: {
            exports: 'swipe'
        },
        common: {
            deps: ['jquery', 'swipe'],
            exports: 'common'
        },
        /*
        mousewheel: {
            deps: ['jquery'],
            exports: 'mousewheel'
        },
        */
        fancybox: {
            deps: ['jquery',/*, 'mousewheel'*/],
            exports: 'fancybox'
        },
        masonry: {
            exports: 'masonry'
        },
        masonryMin: {
            exports: 'masonryMin'
        },
        uploadify: {
            deps: ['jquery'],
            exports: 'uploadify'
        },
        emojiSelector: {
            deps: ['jquery', 'underscore'],
            exports: 'emojiSelector'
        },
        emojione: {
            exports: 'emojione'
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
