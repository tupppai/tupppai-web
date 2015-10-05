define(['underscore', 'app/collections/Asks', 'app/views/ListView', 'tpl!app/templates/AskItemView.html'],
    function (_, Asks, ListView, askItemTemplate) {
        "use strict";

        return function() {
            var asks = new Asks;

            var view = new ListView({
                collection: asks,
                template: askItemTemplate,
            });

            window.app.home.close();
			window.app.content.show(view);
        };
    });
