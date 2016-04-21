define('app/app', [ 'marionette', 'app/util'], function (marionette, util) {
    "use strict";
    var app  = new marionette.Application();

    app.addRegions({
        header: '#header-section',
        content: '#content-section',
        footer: '#content-section'
    });

    for(var i in util) {
        app[i] = util[i];
    }
    return app;
});
