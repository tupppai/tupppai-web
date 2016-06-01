        
define('app/app', [ 'marionette', 'app/util', 'imageLazyLoad'], 
    function (marionette, util, imageLazyLoad) {
        "use strict";
        var app  = new marionette.Application();

        app.addRegions({
            header: '#header-section',
            content: '#content-section',
            footer: '#content-section',
        });

        app.addInitializer(function (options) {

        });

        require(['app/views/menu/menuView'], function(menuView) {
            app.user = new window.app.model();
            app.user.url = '/v2/user';
            app.menuView = new menuView();
            app.header.show(app.menuView);

            app.user.fetch({
                success:function(data) {
                    $(".personalCenter").attr("href", "#personal/index/" + data.get('uid'));
                    $(".personalCenter img").attr("src", data.get('avatar'));
                }
            })
        });

        for(var i in util) {
            app[i] = util[i];
        }
        return app;
    });
