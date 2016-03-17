define([ 'app/views/obtainsuccess/ObtainSuccessView' ], function (ObtainSuccessView) {
    "use strict";
    return function() {
        var view = new ObtainSuccessView();
        window.app.content.show(view);
    };
});
