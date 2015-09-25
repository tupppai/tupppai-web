define(['marionette', 'app/models/User', 'app/modules/HeaderModule', 'app/modules/HomeModule'],
    function (marionette, User, HeaderModule, HomeModule) {
        "use strict";

        window.REMODAL_GLOBALS = {
            NAMESPACE: 'modal',
            DEFAULTS: {
                hashTracking: false
            }
        };
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
            app.home.$el.hide();
        });

        return app;
    });
