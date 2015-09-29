define(['app/views/RegisterView'],
    function (RegisterView) {
        "use strict";

        return function() {

            var view = new RegisterView();
            window.app.modal.show(view);
            view.registerModal = $('div[data-remodal-id=register-modal]').remodal();
            view.registerModal.open();
            $(document).on('click','.sex-pressed',view.optionSex);
            $(document).on('click','.register-btn',view.register);
        };
    });
