define(['app/models/user', 'app/views/getavatar/GetAvatarView' ], 
		function (User, GetAvatarView) {
    "use strict";
    return function() {
        alert( 1 ); 
        var user = new User;
        user.url = '/wxactgod/index'
        var view = new GetAvatarView({
        	model:user
        });
        window.app.content.show(view);
    };
});
