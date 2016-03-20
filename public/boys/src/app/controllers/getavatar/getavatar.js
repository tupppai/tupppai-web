define(['app/models/user', 'app/views/getavatar/GetAvatarView' ], 
		function (rand, GetAvatarView) {
    "use strict";
    return function() {
    	var randMessage = new rand;
    	randMessage.url = '/wxactgod/index'
        var view = new GetAvatarView({
        	model:randMessage
        });
        window.app.content.show(view);
    };
});
