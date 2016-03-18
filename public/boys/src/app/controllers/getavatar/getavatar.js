define(['app/models/rand', 'app/views/getavatar/GetAvatarView' ], 
		function (rand, GetAvatarView) {
    "use strict";
    return function() {
    	var randMessage = new rand;
    	randMessage.url = '/wxactgod/getrandavatars'
        var view = new GetAvatarView({
        	model:randMessage
        });
        window.app.content.show(view);
    };
});
