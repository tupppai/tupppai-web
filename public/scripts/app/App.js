define([
        'marionette', 
        'app/models/User', 
        'app/modules/HeaderModule', 
        'app/modules/FooterModule', 
        'app/modules/HomeModule',  
        'app/views/LoginView', 
        'app/views/RegisterView',
       ],
    function (marionette, User, HeaderModule, FooterModule, HomeModule, LoginView, RegisterView) {
        "use strict";
        if(location.hash == ''){
            location.href = '#index';
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
            footer: '#footerView',
            content: '#contentView',
            home: '#homeView',
            modal: '#modalView',
            login: '#loginView',
            register: '#registerView'
        });

        app.addInitializer(function (options) {
            app.headerModule = new HeaderModule({model: app.user});
            app.footerModule = new FooterModule();
            //app.homeModule   = new HomeModule({model: app.user});
            app.loginView    = new LoginView();
            app.registerView = new RegisterView();
            app.user.fetch({
                success: function(data) {
                    $("body").attr("data-uid", data.get('uid'));

                    app.user.trigger('change');
                }
            });

            app.header.show(app.headerModule);
            app.footer.show(app.footerModule);
            app.login.show(app.loginView);
            app.register.show(app.registerView);

            //app.home.show(app.homeModule);
            //app.home.$el.hide();
        });

        return app;
    });
