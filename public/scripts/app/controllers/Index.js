define(['app/models/Ask', 'app/collections/Replies', 'app/views/index/IndexView', 'app/views/index/IndexItemView', ],
    function (Ask, Replies, IndexView, IndexItemView) {
        "use strict";

        return function() {

 
            var view = new IndexView();
            window.app.home.close();
            window.app.content.show(view);

            var replies = new Replies;

            var indexItem = new Backbone.Marionette.Region({el:"#indexItemView"});
            var view = new IndexItemView({
                collection: replies
            });
            indexItem.show(view);
        };
    });
