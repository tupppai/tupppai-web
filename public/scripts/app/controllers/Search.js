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
                collection: users
            });
            userRegion.show(view);
    
            var contentRegion = new Backbone.Marionette.Region({el:"#contentItemView"});
            var view = new ContentItemView({
                collection: threads 
            });
            contentRegion.show(view);

            var discussRegion = new Backbone.Marionette.Region({el:"#discussItemView"});
            var view = new DiscussItemView({});
            discussRegion.show(view);
        };
    });
