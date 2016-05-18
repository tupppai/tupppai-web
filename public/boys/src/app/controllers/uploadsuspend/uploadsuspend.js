define([ 'app/views/uploadsuspend/UploadSuspendView' ], function (UploadSuspendView) {
    "use strict";
    return function() {
        var view = new UploadSuspendView();
        window.app.content.show(view);
    };
});
