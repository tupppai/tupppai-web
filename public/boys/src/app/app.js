define('app/app', [ 'app/models/user','marionette'], function (User, marionette) {

        "use strict";
        var app  = new marionette.Application();
        app.user = new User;
        app.user.url = '/wxactgod/index'
        app.addRegions({
            content: '#contentView',
        });
        app.addInitializer(function (options){
            app.user.fetch({
                success:function(data) {
                    $("body").attr("data-uid", data.get('request'));
                }
            })
        	
        })
        wx_sign();
        return app;
    });
