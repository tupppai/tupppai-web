define(['underscore', 'app/models/User', 'app/modules/HomeModule'],
    function (_, User, HomeModule) {
        "use strict";

        return function(type, uid) {
            var user = new User;
            user.url = 'users/' + uid;

            var homeModule  = new HomeModule({model: user});
            window.app.home.show(homeModule);
            user.fetch();

            $(window.app.home.el).attr('data-uid', uid);
            $('#load_'+type).trigger('click');
            
        };
    });
