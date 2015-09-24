define(['underscore', 'app/collections/Asks', 'app/views/AskListView'],
    function (_, Asks, AskListView) {
        "use strict";

        return function() {
            var asks = new Asks;
            asks.data.type = 'hot';

            var view = new AskListView({
                collection: asks 
            });
        };
    });
