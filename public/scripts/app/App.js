define(['marionette', 'app/views/HeaderView'],
    function (marionette, HeaderView) {
        "use strict";

        var app = new marionette.Application();

        app.addRegions({
            header: '#headerView',
            content: '#Content'
        });

        app.addInitializer(function (options) {

            app.header.show(new HeaderView());
        });

        return app;
    });
