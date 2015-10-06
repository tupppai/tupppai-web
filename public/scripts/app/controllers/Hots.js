define(['underscore', 'app/collections/Asks', 'app/views/HotView', 'tpl!app/templates/HotItemView.html'],
    function (_, Asks, HotView, HotItemTemplate) {
        "use strict";

        return function() {
            var asks = new Asks;
            asks.data.type = 'hot';

            var view = new HotView({
                collection: asks,
                template: HotItemTemplate,
            });
            window.app.content.show(view);
        };
    });
