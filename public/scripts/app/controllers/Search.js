define([
        'underscore', 
        'app/collections/Friendships',
        'app/collections/Replies',
        'app/views/search/SearchView',
        'app/views/search/UserItemView',
        'app/views/search/ContentItemView',
        'app/views/search/DiscussItemView',
       ],
    function (_, Friendships, Replies, SearchView, UserItemView, ContentItemView, DiscussItemView) {
        "use strict";

        return function() {
            var friendships = new Friendships;
            var reply = new Replies;

            var view = new SearchView();
            window.app.content.show(view);
    
            var userRegion = new Backbone.Marionette.Region({el:"#userItemView"});
            var view = new UserItemView({
                collection: friendships
            });
            userRegion.show(view);
    
    
            var contentRegion = new Backbone.Marionette.Region({el:"#contentItemView"});
            var view = new ContentItemView({
                collection: reply
            });
            contentRegion.show(view);


            var discussRegion = new Backbone.Marionette.Region({el:"#discussItemView"});
            var view = new DiscussItemView({});
            discussRegion.show(view);
        };
    });
