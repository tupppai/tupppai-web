require.config({
    paths: {
        backbone: 'lib/backbone/backbone',
        underscore: 'lib/underscore/underscore',
        zepto: 'lib/zepto/zepto',
        deferred: 'lib/simply-deferred/deferred',
        marionette: 'lib/backbone/backbone.marionette',
        tpl: 'lib/require/tpl',
        common: 'lib/common',
        pingpp: 'lib/pingpp/pingpp',
        lazyload: 'lib/lazyload/lazyload',
        swiper: 'lib/swiper/swiper',
        fastclick: 'lib/fastclick/fastclick',
        masonry: 'lib/masonry/masonry',
        asyncList: 'lib/component/asyncList',
        imageLazyLoad: 'lib/imagesloaded/imageLazyLoad',
        fx: 'lib/zepto/fx',
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
        common: {
            deps: ['zepto'],
            exports: 'common'
        },        
        pingpp: {
            deps: ['zepto'],
            exports: 'pingpp'
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
        fx: {
            depts: ['zepto'],
            exports: 'fx'
        },
        wx: {
           exports: 'wx' 
        }
    }
});

require(['app/app', 'backbone', 'app/router', 'wx'],
    function (App, Backbone, router) { 
        "use strict"; 

        window.app = App;
        app.start();

        new router();
        Backbone.history.start(); 
        Backbone.history.on("all", function (route, router) {
            var options = {};
            share_friend(options,function(){},function(){});
            share_friend_circle(options,function(){},function(){});
            share_zone(options,function(){},function(){});
            share_weibo(options,function(){},function(){});
            share_qq(options,function(){},function(){});

            $(window).unbind('scroll');
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
