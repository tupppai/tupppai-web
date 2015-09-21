define(['marionette', 'app/Controller'],
    function (marionette, Controller) {
        'use strict';

        return marionette.AppRouter.extend({
            appRoutes: {
                '*action': 'logAction'
            },
            controller: Controller
        });
    });
