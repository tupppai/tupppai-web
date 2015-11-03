define(['underscore', 'app/models/User', 'app/views/SettingView'],
    function (_, User, SettingView) {
        "use strict";

        return function() {
            var user = new User;
            user.url = 'user/status?settings';

            var view = new SettingView({ model: user });
            window.app.content.show(view);
            user.fetch();
        };
    });
