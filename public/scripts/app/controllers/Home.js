define(['underscore', 'app/models/User', 'app/modules/HomeModule', 'app/views/PopupView'],
    function (_, User, HomeModule, PopupView) {
        "use strict";

        return function(type, uid) {
            var user = new User;
            user.url = 'users/' + uid;

            var homeModule  = new HomeModule({model: user});
            window.app.home.show(homeModule);
            user.fetch();

            $(window.app.home.el).attr('data-uid', uid);
            $('#load_'+type).trigger('click');

            //var view = new PopupView();
            //window.app.modal.show(view);
        };
    });
