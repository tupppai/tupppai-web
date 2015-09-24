define(['marionette', 'app/models/User', 'app/modules/HeaderModule'],
    function (marionette, User, HeaderModule) {
        "use strict";

        var app  = new marionette.Application();
        app.user = new User;
        app.user.url = 'user/status';

        app.addRegions({
            header: '#headerView',
            content: '#contentView',
            modal: '#modalView'
        });

        app.addInitializer(function (options) {
            var headerModule = new HeaderModule({model: app.user});
            app.user.fetch();

            app.header.show(headerModule);
        });

        return app;
    });
