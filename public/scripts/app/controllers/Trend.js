define(['underscore','app/views/trend/TrendView','app/collections/Replies'],
    function (_, trendView, Replies) {
        "use strict";

        return function() {
        	var replies = new Replies;
        	replies.url = 'timeline';
            var view = new trendView({collection: replies});
            
            window.app.content.show(view);
        
        };
    });
