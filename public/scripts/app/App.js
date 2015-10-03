define([
        'marionette', 
        'app/models/User', 
        'app/modules/HeaderModule', 
        'app/modules/HomeModule',  
        'app/views/loginView', 
        'app/views/registerView'
       ],
    function (marionette, User, HeaderModule, HomeModule, LoginView, RegisterView) {
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
            modal: '#modalView',
            login: '#loginView',
            register: '#registerView'
        });

        app.addInitializer(function (options) {
            app.headerModule = new HeaderModule({model: app.user});
            app.homeModule   = new HomeModule({model: app.user});
            app.loginView    = new LoginView();
            app.registerView = new RegisterView();

            app.user.fetch();

            app.header.show(app.headerModule);
            app.login.show(app.loginView);
            app.register.show(app.registerView);


            //app.home.show(app.homeModule);
            //app.home.$el.hide();
        });

        return app;
    });
