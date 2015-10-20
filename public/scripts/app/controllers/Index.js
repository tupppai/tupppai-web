define(['app/views/IndexView'],
    function (IndexView) {
        "use strict";

        return function() {
            var view = new IndexView();
            window.app.modal.show(view);

        };
    });
