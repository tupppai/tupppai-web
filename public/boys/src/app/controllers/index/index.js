define([ 'app/views/index/IndexView' ], function (indexView) {
    "use strict";
    return function() {
        var view = new indexView();
        window.app.content.show(view);
    };
});
