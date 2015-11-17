define(['underscore', 
		'tpl!app/templates/home/HomeView.html',
		'app/views/friendship/FriendshipItemView',
		'app/collections/Users'
       ],
    function (_, FriendshipView, FriendshipItemView, Users) {
        "use strict";

        return function(type, uid) {
            var view = new FriendshipView();
            window.app.content.show(view);

        	var friendships = new Users;
            if(type == 'follows') {
                friendships.url = '/follows';
            }
            else {
                friendships.url = '/fans';
            }
            friendships.data.uid = uid;
            $(window.app.content.el).attr('data-type', type);
            $(window.app.content.el).attr('data-uid', uid);
            
            var friendsRegion = new Backbone.Marionette.Region({el:"#friendshipItemView"});
            var view = new FriendshipItemView({
                collection: friendships
            });
            friendsRegion.show(view);
        };
    });
