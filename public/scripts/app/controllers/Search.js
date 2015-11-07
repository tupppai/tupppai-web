define([
        'underscore', 
        'app/collections/Threads',
        'app/collections/Users',
        'app/collections/Topics',
        'app/views/search/SearchView',
        'app/views/search/UserItemView',
        'app/views/search/ThreadItemView',
        'app/views/search/TopicItemView',
       ],
    function (_, Threads, Users, Topics, SearchView, UserItemView, ThreadItemView, TopicItemView) {
        "use strict";

        return function(type, keyword) {
            //渲染主页面
            var view = new SearchView();
            window.app.thread.show(view);

            //获取数据
            var threads = new Threads;
            threads.url = '/search/threads';
            threads.keyword = keyword;

            var users = new Users;
            users.url = '/search/users';
            users.url = '/search/users';
            users.keyword = keyword;
    
            var topics = new Topics;
            topics.url = '/search/topics/';
            topics.keyword = keyword;
            
            var userRegion = new Backbone.Marionette.Region({el:"#userItemView"});
            var users_view = new UserItemView({
                collection: users
            });
    
            var threadRegion = new Backbone.Marionette.Region({el:"#threadItemView"});
            var threads_view = new ThreadItemView({
                collection: threads 
            });

            var topicRegion = new Backbone.Marionette.Region({el:"#topicItemView"});
            var topic_view = new TopicItemView({
                collection: topics
            });

            switch(type) {
            case 'user':
                userRegion.show(view);
                break;
            case 'thread':
                threadRegion.show(view);
                break;
            case 'topic':
                topicRegion.show(view);
                break;
            default:
                userRegion.show(view);
                threadRegion.show(view);
                topicRegion.show(view);
                break;
            }
        };
    });
