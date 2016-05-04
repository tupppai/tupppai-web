        
define('app/app', [ 'marionette', 'app/util'], 
    function (marionette, util) {
        "use strict";
        var app  = new marionette.Application();

        app.addRegions({
            header: '#header-section',
            content: '#content-section',
            footer: '#content-section',
        });

        app.addInitializer(function (options) {

        });

        require(['app/views/menu/menuView'], function(menuView) {
            app.user = new window.app.model();
            app.user.url = '/v2/user'
            app.user.fetch();
            app.menuView = new menuView();
            app.header.show(app.menuView);
        });
        for(var i in util) {
            app[i] = util[i];
        }
        return app;
    });
