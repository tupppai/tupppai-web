define(['app/views/LoginView'],
    function (LoginView) {
        "use strict";

        return function() {

            var view = new LoginView();
            view.render();
        };

    });
