{
    appDir: "./",
    baseUrl: ".",
    dir: "../res",
    optimize: "uglify",
    optimizeCss: "uglify",
    //fileExclusionRegExp: /^(r|build|node_modules)\.js$/,
    //fileExclusionRegExp: /^(?:media|node_modules|(?:r|build|min)\.js)$/,
    fileExclusionRegExp: /^(?:media|gulpfile.js|index.php|less|package.json|node_modules|(?:r|build|min)\.js)$/,
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
            name: 'app/router'
        }
    ],
    paths: {
        backbone: 'lib/backbone/backbone',
        underscore: 'lib/underscore/underscore',
        jquery: 'lib/jquery/jquery-1.9.0',
        deferred: 'lib/simply-deferred/deferred',
        marionette: 'lib/backbone/backbone.marionette',
        tpl: 'lib/require/tpl',
        common: 'lib/common',
        lazyload: 'lib/lazyload/lazyload',
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
        lazyload: {
            depts: ['jquery'],
            exports: 'lazyload'
        },   
    }
}
