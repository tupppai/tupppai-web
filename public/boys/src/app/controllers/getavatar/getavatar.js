define(['app/models/user', 'app/views/getavatar/GetAvatarView' ], 
		function (User, GetAvatarView) {
    "use strict";
    return function() {
    	var randMessage = new User;
    	randMessage.url = '/wxactgod/index'
        var view = new GetAvatarView({
        	model:randMessage
        });
        window.app.content.show(view);
    };
});
