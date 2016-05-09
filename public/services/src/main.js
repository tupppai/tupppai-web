require.config({
    paths: {
        backbone: 'lib/backbone/backbone',
        underscore: 'lib/underscore/underscore',
        zepto: 'lib/zepto/zepto',
        deferred: 'lib/simply-deferred/deferred',
        marionette: 'lib/backbone/backbone.marionette',
        tpl: 'lib/require/tpl',
        common: 'lib/common',
        wechat: 'lib/wechat/wechat',
        lazyload: 'lib/lazyload/lazyload',
        fastclick: 'lib/fastclick/fastclick',
        masonry: 'lib/masonry/masonry',
        asyncList: 'lib/component/asyncList',
        waterfall: 'lib/component/waterfall',
        wx: ['http://res.wx.qq.com/open/js/jweixin-1.0.0', 'lib/wx/jweixin']
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
        masonry: {
            depts: ['zepto'],
            exports: 'masonry'
        },        
        waterfall: {
            depts: ['zepto', 'masonry'],
            exports: 'waterfall'
        },        
        asyncList: {
            depts: ['zepto'],
            exports: 'asyncList'
        },
        wx:{
           exports: 'wx' 
        }
    }
});

require(['app/app', 'backbone', 'app/router', 'wx'],
    function (App, Backbone, router) { 
        "use strict"; 

        window.app = App;
        app.start();

        wx_sign();

		new router();
        Backbone.history.start(); 
        Backbone.history.on("all", function (route, router) {

        });
    });
