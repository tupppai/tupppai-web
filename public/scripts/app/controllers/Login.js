define(['app/views/LoginView'],
    function (LoginView) {
        "use strict";

        return function() {

            var view = new LoginView().render();
            console.log(view);
            var html = view.el;

            $("#modalView").html(html);
            $('div[data-remodal-id=login-modal]').remodal().open();
        };

    });
