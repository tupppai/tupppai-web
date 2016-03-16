define('app/app', [ 'marionette', 'zepto', 'swiper', 'fx' ],
    function (marionette, zepto, swiper, fx) {

        "use strict";
        var app  = new marionette.Application();

        app.addRegions({
            content: '#contentView',
        });

        wx_sign();
        return app;
    });
