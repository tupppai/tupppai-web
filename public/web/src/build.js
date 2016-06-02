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
                "zepto", 
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
        //jquery: 'lib/jquery/jquery-1.9.0',
        zepto: 'lib/zepto/zepto',
        deferred: 'lib/simply-deferred/deferred',
        marionette: 'lib/backbone/backbone.marionette',
        tpl: 'lib/require/tpl',
        common: 'lib/common',
        lazyload: 'lib/lazyload/lazyload',
        swiper: 'lib/swiper/swiper',
        fastclick: 'lib/fastclick/fastclick',
        masonry: 'lib/masonry/masonry',
        asyncList: 'lib/component/asyncList',
        imageLazyLoad: 'lib/imagesloaded/imageLazyLoad',
        waterfall: 'lib/component/waterfall',
        wx:'lib/wx/jweixin'
    },
    shim: {
        zepto: {
            deps: 'deferred',
            exports: '$'
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
        swiper: {
            depts: ['zepto'],
            exports: 'swiper'
        },   
        fastclick: {
            depts: ['zepto'],
            exports: 'fastclick'
        },        
        masonry: {
            depts: ['zepto'],
            exports: 'masonry'
        },
        asyncList: {
            depts: ['zepto'],
            exports: 'asyncList'
        },
        imageLazyLoad: {
            depts: ['zepto'],
            exports: 'imageLazyLoad'
        },
        wx:{
            exports: 'wx' 
        }
    }
}
