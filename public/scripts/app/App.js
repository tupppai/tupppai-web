define([
        'marionette', 
        'app/models/User', 
        'app/modules/HeaderModule', 
        'app/modules/FooterModule', 
        'app/modules/HomeModule',  
        'app/views/register/LoginView', 
        'app/views/register/RegisterView',
        'app/views/register/ForgetPasswordView',
        'app/views/register/UserBingdingView'
       ],
    function (marionette, User, HeaderModule, FooterModule, HomeModule, LoginView, RegisterView, ForgetPasswordView, UserBingdingView) {
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
            register: '#registerView',
            forgetPassword: '#forgetPasswordView',
            userBingding: '#userBingdingView',
        });

        app.addInitializer(function (options) {
            app.headerModule = new HeaderModule({model: app.user});
            app.footerModule = new FooterModule();
            //app.homeModule   = new HomeModule({model: app.user});
            app.loginView    = new LoginView();
            app.registerView = new RegisterView();
            app.forgetPasswordView = new ForgetPasswordView();
            app.userBingdingView = new UserBingdingView();
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
            app.forgetPassword.show(app.forgetPasswordView);
            app.userBingding.show(app.userBingdingView);

            //app.home.show(app.homeModule);
            //app.home.$el.hide();
        });

        return app;
    });
