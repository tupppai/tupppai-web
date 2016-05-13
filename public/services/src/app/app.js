        
define('app/app', [ 'marionette', 'app/util'], 
    function (marionette, util) {
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
            app.user.url = '/v2/user'
            app.menuView = new menuView();
            app.header.show(app.menuView);

            app.user.fetch({
                success:function(data) {
                    $("body").attr("data-uid", data.get('uid'));
                    $("body").attr("data-nickname", data.get('nickname'));
                    $("body").attr("data-src", data.get('avatar'));                    

                    $(".personalCenter").attr("data-uid", data.get('uid'));
                    $(".personalCenter").attr("data-nickname", data.get('nickname'));
                    $(".personalCenter").attr("href", "#personal/personal/" + data.get('uid'));
                    $(".personalCenter img").attr("src", data.get('avatar'));
                }
            })
        });
        for(var i in util) {
            app[i] = util[i];
        }
        return app;
    });
