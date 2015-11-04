define([
		'underscore', 
		'app/views/friendship/FriendshipView',
		'app/collections/Friendships'
	   ],
    function (_, FriendshipView, Friendships) {
        "use strict";

        return function() {
        	var friendships = new Friendships;
            friendships.fetch();
            var view = new FriendshipView({collection: friendships});

            window.app.content.show(view);

        };
    });
