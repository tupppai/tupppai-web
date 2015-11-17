define(['underscore', 'app/models/User', 'app/modules/HomeModule','app/views/LoginView'],
    function (_, User, HomeModule, LoginView) {
        "use strict";

        return function(type, uid) {
            var user = new User;
            user.url = 'users/' + uid;

            var homeModule  = new HomeModule({model: user});
            window.app.home.show(homeModule);
            user.fetch();

            $(window.app.home.el).attr('data-uid', uid);
            $('#load_'+type).trigger('click');
            
            var view = new LoginView();
            window.app.modal.show(view);
        };
    });
