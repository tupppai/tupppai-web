define('app/app', [ 'marionette'], function (marionette) {

        "use strict";
        var app  = new marionette.Application();

        app.addRegions({
            content: '#contentView',
        });
        wx_sign();
        return app;
    });
