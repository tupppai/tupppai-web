define(['underscore', 'app/views/search/SearchView'],
    function (_, SearchView) {
        "use strict";

        return function() {
            var view = new SearchView();

            window.app.content.show(view);
        };
    });
