define([ 'app/views/getavatar/GetAvatarView' ], function (GetAvatarView) {
    "use strict";
    return function() {
        var view = new GetAvatarView();
        window.app.content.show(view);
    };
});
