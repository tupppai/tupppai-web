define(['app/models/user', 'app/views/uploadagain/UploadAgainView' ], function (User,UploadAgainView) {
    "use strict";
    return function() {
    	var user = new User;
    	user.url = '/wxactgod/index';
        var view = new UploadAgainView({
        	model: user
        });
        window.app.content.show(view);
    };
});
