define([ 'app/views/uploadsuccess/UploadSuccessView' ], function (UploadSuccessView) {
    "use strict";
    return function() {
        var view = new UploadSuccessView();
        window.app.content.show(view);
    };
});
