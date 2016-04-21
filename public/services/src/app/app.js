        
define('app/app', [ 'marionette', 'app/util', 'app/views/menu/menuView',], 
    function (marionette, util, menuView) {
        "use strict";
        var app  = new marionette.Application();

        app.addRegions({
            header: '#header-section',
            content: '#content-section',
            footer: '#content-section',
        });
        app.addInitializer(function (options) {
            app.menuView = new menuView();
            app.header.show(app.menuView);
        });

        for(var i in util) {
            app[i] = util[i];
        }
        return app;
    });
