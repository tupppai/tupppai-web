define(['underscore', 'app/collections/Asks', 'app/views/ask/AsksView'],
    function (_, Asks, AsksView) {
        "use strict";

        return function() {
            var asks = new Asks;
            asks.data.width = 300;

            var view = new AsksView({
                collection: asks
            });

            window.app.content.show(view);
        };
    });
