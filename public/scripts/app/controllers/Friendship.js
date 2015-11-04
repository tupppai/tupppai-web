define([
		'underscore', 
		'app/views/friendship/FriendshipView',
		'app/collections/Friendships'
	   ],
    function (_, FriendshipView, Friendships) {
        "use strict";

        return function(type, uid) {
        	var friendships = new Friendships;
            if(type == 'follows') {
                friendships.url = '/follows';
            }
            else {
                friendships.url = '/fans';
            }
            friendships.data.uid = uid;
            var view = new FriendshipView({collection: friendships});

            $(window.app.content.el).attr('data-type', type);
            $(window.app.content.el).attr('data-uid', uid);

            window.app.content.show(view);

        };
    });
