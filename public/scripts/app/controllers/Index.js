define(['app/models/Ask', 'app/collections/Asks', 'app/views/index/IndexView', 'app/views/index/IndexItemView', ],
    function (Ask, Asks, IndexView, IndexItemView) {
        "use strict";

        return function() {

 
            var view = new IndexView();
            window.app.content.show(view);

            var asks = new Asks;
            asks.url = '/asks/';

            var indexItem = new Backbone.Marionette.Region({el:"#indexItemView"});
            var view = new IndexItemView({
                collection: asks
            });
            indexItem.show(view);
        };
    });
