define(['underscore',
        'app/views/trend/TrendView',
        'app/collections/Replies'
        ],
    function (_, TrendView, Replies) {
        "use strict";

        return function() {

            var replies = new Replies;
            replies.url = 'timeline';
            var view = new TrendView({
                collection: replies
            });
            
            window.app.content.show(view);
        
    
        };
    });
