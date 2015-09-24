define(['underscore', 'app/collections/Asks', 'app/views/AskListView'],
    function (_, Asks, AskListView) {
        "use strict";

        return function() {
            var asks = new Asks;

            var view = new AskListView({
                collection: asks 
            });
        };
    });
