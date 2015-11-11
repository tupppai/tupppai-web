define(['underscore', 'app/collections/Asks', 'app/views/ask/AskFlowsView'],
    function (_, Asks, AskFlowsView) {
        "use strict";

        return function() {
            var asks = new Asks;
            asks.data.width = 300;

            var view = new AskFlowsView({
                collection: asks
            });

            window.app.content.show(view);
        };
    });
