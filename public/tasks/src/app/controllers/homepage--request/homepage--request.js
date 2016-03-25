define([ 'app/views/homepage--request/homepage--request' ], function (index) {
    "use strict";
    return function() {

        var view = new index();
        window.app.content.show(view);
    };
});
