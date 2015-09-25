define(['underscore', 'app/collections/Asks', 'app/views/ListView', 'tpl!app/templates/AskItemView.html'],
    function (_, Asks, ListView, askItemTemplate) {
        "use strict";

        return function() {
            var asks = new Asks;
            asks.data.type = 'hot';

            var view = new ListView({
                collection: asks,
                template: askItemTemplate
            });
        };
    });
