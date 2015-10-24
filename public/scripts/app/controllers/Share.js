define(['underscore', 'app/views/ShareView'],
    function (_, ShareView) {
        "use strict";

        return function() {
            var view = new ShareView();

            window.app.home.close();
            window.app.content.show(view);
        };
    });
