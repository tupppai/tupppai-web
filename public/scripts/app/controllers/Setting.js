define(['underscore', 'app/views/SettingView'],
    function (_, SettingView) {
        "use strict";

        return function() {
            var view = new SettingView();
            window.app.home.close();
            window.app.content.show(view);
        };
    });
