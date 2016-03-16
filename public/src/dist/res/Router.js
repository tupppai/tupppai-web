var paths = [
    'marionette',
    'Index',
    'ReplyFlows',
    'Message',
    'Trend',
    'Setting',
    'AskDetail',
    'Logout',
    'HomePage',
    'Search',
    'ReplyDetailPlay',
    'Channel',
    'Money',
];
var pathRoutes = [];

define('app/Router', ['marionette'], function (marionette) {
        'use strict';

        var routes = {};
        var controllers = {};
        //console.log(paths);

        for(var i = 1; i < paths.length; i ++) {
            var path = paths[i];
            routes[path.toLowerCase()] = path;
            routes[path.toLowerCase() + '/:id'] = path;
            routes[path.toLowerCase() + '/:type/:id'] = path;

            pathRoutes[path.toLowerCase()] = paths[i];

            controllers[path] = function() {
                var index   = location.hash.substr(1).split('/')[0];
                var url     = pathRoutes[index];
                require(['app/controllers/'+url], function (controller) {
                    var args = location.hash.substr(1).split('/');
                    switch(args.length) {
                    case 1:
                        controller();
                        break;
                    case 2:
                        controller(args[1]);
                        break;
                    case 3:
                        controller(args[1], args[2]);
                        break;
                    }
                });
            }
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
