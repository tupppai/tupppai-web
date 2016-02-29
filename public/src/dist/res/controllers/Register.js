define(['app/views/RegisterView'],
    function (RegisterView) {
        "use strict";

        return function() {

            var view = new RegisterView();
            window.app.modal.show(view);
        
        };
    });
