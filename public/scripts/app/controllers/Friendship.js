define(['underscore', 'app/views/FriendshipView'],
    function (_, FriendshipView) {
        "use strict";

        return function() {
            var view = new FriendshipView({});
            window.app.content.show(view);

        };
    });
