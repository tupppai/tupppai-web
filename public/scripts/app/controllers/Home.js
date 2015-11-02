define(['underscore', 'app/models/User', 'app/modules/HomeModule'],
    function (_, User, HomeModule) {
        "use strict";

        return function(type, uid) {
            var user = new User;
            user.url = 'user/status';
            user.fetch({
                success: function(data) {
                    $("#homeView").attr("data-own-uid", data.get('uid'));
                }
            });


            var user = new User;
            user.url = 'users/' + uid;
            user.data.uid   = uid;

            var homeModule  = new HomeModule({model: user});
            user.fetch();

            window.app.home.show(homeModule);
            $(window.app.home.el).attr('uid', uid);
            $('#load_'+type).trigger('click');
        };
    });
