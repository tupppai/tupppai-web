define([ 'app/views/shareavatar/ShareAvatarView' ], function (ShareAvatarView) {
    "use strict";
    return function() {
        var view = new ShareAvatarView();
        window.app.content.show(view);
    };
});