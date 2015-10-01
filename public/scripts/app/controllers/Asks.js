define(['underscore', 'fancybox', 'app/collections/Asks', 'app/views/ListView', 'tpl!app/templates/AskItemView.html'],
    function (_, fancybox, Asks, ListView, askItemTemplate) {
        "use strict";

        return function() {
            var asks = new Asks;

            var view = new ListView({
                collection: asks,
                template: askItemTemplate,
            });

            $("#click_fancybox").fancybox();

            window.app.home.close();
			window.app.content.show(view);
        };
    });
