define(['app/views/LandingView'],
    function (LandingView) {
        "use strict";

        return {
            logAction: function (action) {
                console.log(action);
                MyApp.content.show(new LandingView());
            }
        };
    });
