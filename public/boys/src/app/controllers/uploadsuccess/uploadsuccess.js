define(['app/models/user', 'app/views/uploadsuccess/UploadSuccessView' ], function (User, UploadSuccessView) {
    "use strict";
    return function() {
    	var user = new User;
        user.url = '/wxactgod/index'
        var view = new UploadSuccessView({
        	model: user
        });
        window.app.content.show(view);
    };
});
