{
    appDir: "./",
    baseUrl: ".",
    dir: "../res",
    optimize: "uglify",
    optimizeCss: "uglify",
    //fileExclusionRegExp: /^(r|build|node_modules)\.js$/,
    fileExclusionRegExp: /^(?:media|node_modules|(?:r|build|min)\.js)$/,
    modules: [
        {
            name: "main",
            include: [
                "jquery", 
                "backbone",
                "underscore"
            ]
        }, 
        {
            name: 'app/Router'
        }
    ],
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
        uploadify: 'lib/uploadify/jquery.uploadify.min',
        emojione: 'lib/emojione/emojione',
        emojiSelector: 'lib/face-selector/face-selector',
        superSlide: 'lib/superSlide/superSlide'
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
        fancybox: {
            deps: ['jquery'],
            exports: 'fancybox'
        },
        masonry: {
            exports: 'masonry'
        },
        uploadify: {
            deps: ['jquery'],
            exports: 'uploadify'
        },
        emojione: {
            exports: 'emojione'
        },
        emojiSelector: {
            deps: ['jquery'],
            exports: 'emojiSelector'
        },
	    superSlide: {
            deps: ['jquery'],
            exports: 'superSlide'
        }
    }
}
