define([
        'underscore', 
        'app/models/Search',
        'app/collections/Threads',
        'app/collections/Users',
        'app/collections/Topics',
        'app/views/search/SearchView',
        'app/views/search/UserItemView',
        'app/views/search/ThreadItemView',
        'app/views/search/TopicItemView'
       ],
    function (_, Search, Threads, Users, Topics, SearchView, UserItemView, ThreadItemView, TopicItemView) {
        "use strict";

        return function(type, keyword) {

            setTimeout(function(){
                $("title").html("图派-搜索主页");
                $('.header-back').removeClass("height-reduce");
            },100);
            //渲染主页面
            var search = new Search({type: type});
            var view = new SearchView({model: search});
            window.app.content.show(view);
            $('#keyword').val(keyword);

            //获取数据
            var threads = new Threads;
            threads.url = '/search/threads';
            threads.data.keyword = keyword;

            var users = new Users;
            users.url = '/search/users';
            users.data.keyword = keyword;
    
            var topics = new Topics;
            topics.url = '/search/topics';
            topics.data.keyword = keyword;
            
            var userRegion = new Backbone.Marionette.Region({el:"#userItemView"});
            var user_view = new UserItemView({
                collection: users
            });
            
    
            var threadRegion = new Backbone.Marionette.Region({el:"#threadItemView"});
            var thread_view = new ThreadItemView({
                collection: threads 
            });

            var topicRegion = new Backbone.Marionette.Region({el:"#topicItemView"});
            var topic_view = new TopicItemView({
                collection: topics
            });

            switch(type) {
            case 'user':
                userRegion.show(user_view);
                break;
            case 'thread':
                threadRegion.show(thread_view);
                break;
            case 'topic':
                topicRegion.show(topic_view);
                break;
            default:
                userRegion.show(user_view);
                threadRegion.show(thread_view);
                topicRegion.show(topic_view);
                break;
            }
        }
    });
