define('app/app', [ 'marionette', 'swiper' ],
    function (marionette, swiper) {
        "use strict";
        var app  = new marionette.Application();

        app.addRegions({
            content: '#contentView',
        });

        return app;
    });
