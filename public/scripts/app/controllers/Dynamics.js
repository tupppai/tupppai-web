define(['underscore', 'app/views/DynamicsView'],
    function (_, DynamicsView) {
        "use strict";

        return function() {
            var view = new DynamicsView();
            window.app.home.close();
            window.app.content.show(view);
        };
    });
