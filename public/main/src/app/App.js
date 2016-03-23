define('app/App',
    [
        'marionette', 
        'app/models/User', 
        'app/modules/HeaderModule', 
        'app/modules/FooterModule', 
        'app/views/register/LoginView', 
        'app/views/register/RegisterView',
        'app/views/register/ForgetPasswordView',
        'app/views/register/UserBindingView',
        'app/views/register/AmendPasswordView'
    ],
    function (marionette, User, HeaderModule, FooterModule,  LoginView, RegisterView, ForgetPasswordView, UserBindingView, AmendPasswordView) {
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
        app.user.url = '/user/status';

        app.addRegions({
            header: '#headerView',
            footer: '#footerView',
            content: '#contentView',
            modal: '#modalView',
            login: '#loginView',
            register: '#registerView',
            forgetPassword: '#forgetPasswordView',
            userBinding: '#userBindingView',
            amendPassword: '#amendPasswordView',
        });

        app.addInitializer(function (options) {
            app.headerModule = new HeaderModule({model: app.user});
            app.footerModule = new FooterModule();
            app.loginView    = new LoginView();
            app.registerView = new RegisterView();
            app.forgetPasswordView = new ForgetPasswordView();
            app.userBindingView = new UserBindingView();
            app.amendPasswordView = new AmendPasswordView();
            app.user.fetch({
                cache: false,
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
            app.userBinding.show(app.userBindingView);
            app.amendPassword.show(app.amendPasswordView);
 
            //app.home.$el.hide();
        });

        return app;
    });
