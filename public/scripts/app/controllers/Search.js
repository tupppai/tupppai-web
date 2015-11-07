define([
        'underscore', 
        'app/collections/Threads',
        'app/collections/Users',
        'app/views/search/SearchView',
        'app/views/search/UserItemView',
        'app/views/search/ContentItemView',
        'app/views/search/DiscussItemView',
       ],
    function (_, Threads, Users, SearchView, UserItemView, ContentItemView, DiscussItemView) {
        "use strict";

        return function() {
            //渲染主页面
            var view = new SearchView();
            window.app.content.show(view);

            //获取数据
            var threads = new Threads;
            threads.url = '/search/threads';

            var users = new Users;
            users.url = '/search/users';
    
            var userRegion = new Backbone.Marionette.Region({el:"#userItemView"});
            var view = new UserItemView({
<<<<<<< HEAD
                collection: friendships
=======
                collection: users
>>>>>>> 26d80fb552330f23b662be59a98a8e24e4685330
            });
            userRegion.show(view);
    
            var contentRegion = new Backbone.Marionette.Region({el:"#contentItemView"});
            var view = new ContentItemView({
<<<<<<< HEAD
                collection: reply
=======
                collection: threads 
>>>>>>> 26d80fb552330f23b662be59a98a8e24e4685330
            });
            contentRegion.show(view);

            var discussRegion = new Backbone.Marionette.Region({el:"#discussItemView"});
            var view = new DiscussItemView({});
            discussRegion.show(view);
        };
    });
