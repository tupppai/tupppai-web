define([ 'app/views/item/swipe' ], function (swipe) {
    "use strict";
    return function() {
        var view = new swipe();
        window.app.content.show(view);
    };
});
