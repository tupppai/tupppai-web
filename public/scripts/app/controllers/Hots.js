define(['underscore', 'app/collections/Replies', 'app/views/HotView', 'tpl!app/templates/HotItemView.html'],
    function (_, Replies, HotView, HotItemTemplate) {
        "use strict";

        return function() {
            var replies = new Replies;
            //asks.data.type = 'hot';

            var view = new HotView({
                collection: replies,
                template: HotItemTemplate,
            });
            window.app.home.close();
            window.app.content.show(view);
        };
    });
