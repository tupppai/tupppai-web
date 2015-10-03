define(['app/views/LoginView'],
    function (LoginView) {
        "use strict";

        return function() {
            var view = new LoginView();
            window.app.modal.show(view);
            $(document).on('click', '#login_btn', view.login);
        };
    });
