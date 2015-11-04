define(['underscore', 'app/views/DynamicsView', 'app/collections/Replies'],
    function (_, DynamicsView, Replies) {
        "use strict";

        return function() {
            var replies = new Replies;
            replies.url = 'timeline';
            var view = new DynamicsView({collection: replies});
            window.app.content.show(view);

            replies.loadMore();
        };
    });
