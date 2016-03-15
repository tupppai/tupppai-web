define('app/app', [ 'marionette', ],
    function (marionette) {
        "use strict";
        var app  = new marionette.Application();
debugger;
        app.addRegions({
            content: '#contentView',
        });

        return app;
    });
