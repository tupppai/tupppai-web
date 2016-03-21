define(['app/models/user', 'app/views/index/IndexView' ], 
	function (User, indexView) {
    "use strict";
    return function() {
    	var user = new User;
        user.url = '/wxactgod/index'
        var view = new indexView({
        	model: user
        });
        window.app.content.show(view);
    };
});
