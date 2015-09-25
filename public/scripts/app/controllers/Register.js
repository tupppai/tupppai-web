define(['app/views/RegisterView'],
    function (RegisterView) {
        "use strict";

        return function() {

            var view = new RegisterView().render();
            
            var html = view.el

            $("#modalView").html(html);
            $('div[data-remodal-id=register-modal]').remodal().open();
        };
    });
