define(['marionette', 'app/models/User', 'app/modules/HeaderModule', 'app/modules/HomeModule'],
    function (marionette, User, HeaderModule, HomeModule) {
        "use strict";
        if(location.hash == ''){
            location.href = '#asks';
        }

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
            app.headerModule = new HeaderModule({model: app.user});
            app.homeModule   = new HomeModule({model: app.user});
            app.user.fetch();

            app.header.show(app.headerModule);

            //app.home.show(app.homeModule);
            //app.home.$el.hide();
        });

        return app;
    });
