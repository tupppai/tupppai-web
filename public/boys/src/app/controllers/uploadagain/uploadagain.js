define([ 'app/views/uploadagain/UploadAgainView' ], function (UploadAgainView) {
    "use strict";
    return function() {
        var view = new UploadAgainView();
        window.app.content.show(view);
    };
});
