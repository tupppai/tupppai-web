define(['underscore', 'app/collections/Asks', 'app/views/ask/AskView'],
    function (_, Asks, AskView) {
        "use strict";

        return function() {
            var asks = new Asks;

            var view = new AskView({
                collection: asks
            });

            window.app.content.show(view);
        };
    });
