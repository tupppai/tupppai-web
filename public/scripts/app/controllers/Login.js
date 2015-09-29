define(['app/views/LoginView'],
    function (LoginView) {
        "use strict";

        return function() {
            var view = new LoginView();
            window.app.modal.show(view);
            view.loginModal = $('[data-remodal-id=login-modal]').remodal();
            view.loginModal.open();
            $(document).on('click', '#login_btn', view.login);
        };
    });
