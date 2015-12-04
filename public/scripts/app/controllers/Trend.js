define(['underscore','app/views/trend/TrendView'],
    function (_, trendView) {
        "use strict";

        return function() {

            var view = new trendView();
            window.app.content.show(view);
        
        };
    });
