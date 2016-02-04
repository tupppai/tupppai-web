var paths = [
    'marionette',
    'app/controllers/Index',
    'app/controllers/ReplyFlows',
    'app/controllers/Message',
    'app/controllers/Trend',
    'app/controllers/Setting',
    'app/controllers/AskDetail',
    'app/controllers/Logout',
    'app/controllers/HomePage',
    'app/controllers/Search',
    'app/controllers/ReplyDetailPlay',
    'app/controllers/Channel',
    'app/controllers/Money',
];

define('app/Router',
    [
        'marionette',
        'app/controllers/Index',
        'app/controllers/ReplyFlows',
        'app/controllers/Message',
        'app/controllers/Trend',
        'app/controllers/Setting',
        'app/controllers/AskDetail',
        'app/controllers/Logout',
        'app/controllers/HomePage',
        'app/controllers/Search',
        'app/controllers/ReplyDetailPlay',
        'app/controllers/Channel',
        'app/controllers/Money',
    ], 
    function (marionette) {
        'use strict';

        var routes = {};
        var controllers = {};
        //console.log(paths);

        for(var i = 1; i < paths.length; i ++) {
            var path = paths[i].substr('app/controllers/'.length);
            routes[path.toLowerCase()] = path;
            routes[path.toLowerCase() + '/:id'] = path;
            routes[path.toLowerCase() + '/:type/:id'] = path;
            controllers[path] = arguments[i];
        }

        //routes[''] = 'Asks';
        routes['*action'] = 'action';
        //extra action defined
        controllers['action'] = function (action) {
            //do nothing
            console.log(action);
        }
        //console.log(controllers);
        //console.log(routes);

        return marionette.AppRouter.extend({
            appRoutes: routes,
            controller: controllers
        });
    });
