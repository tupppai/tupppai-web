define(['marionette', 'app/models/User', 'app/modules/HeaderModule', 'app/modules/HomeModule'],
    function (marionette, User, HeaderModule, HomeModule) {
        "use strict";

        var app  = new marionette.Application();
        app.user = new User;
        app.user.url = 'user/status';

        app.addRegions({
            header: '#headerView',
            content: '#contentView',
            home: '#homeView',
            modal: '#modalView'
        });

        app.addInitializer(function (options) {
            var headerModule = new HeaderModule({model: app.user});
            var homeModule   = new HomeModule({model: app.user});
            app.user.fetch();

            app.header.show(headerModule);
            app.home.show(homeModule);
        });

        return app;
    });
