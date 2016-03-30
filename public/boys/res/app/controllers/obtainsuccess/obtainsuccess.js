define([ 'app/models/user', 'app/views/obtainsuccess/ObtainSuccessView' ], function (User, ObtainSuccessView) {
    "use strict";
    return function() {
    	var user = new User;
    	user.url = '/wxactgod/index';
        var view = new ObtainSuccessView({
        	 model: user
        });
        window.app.content.show(view);
    };
});
