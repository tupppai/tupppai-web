define([
        'app/views/home/HomeView', 
        'app/collections/Users', 
        'tpl!app/templates/home/FriendshipItemView.html'
       ],
    function (View,   Users, friendshipItemTemplate) {
        "use strict";

        var users = new Users;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            data: 0,
            collection: users,
            template: friendshipItemTemplate,
        });
    });
