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
                success:function(res) {

                    $("body").attr("data-user", res.attributes.data.left_amount);
                    $("body").attr("data-code", res.get('code'));
                    $("body").attr("data-reason", res.attributes.result.reason);
                    $("body").attr("data-image", res.attributes.data.image);
                    // $("body").attr("data-rand", data.get('rand'));
                    // $("body").attr("data-name", data.get('designer_name'));
                }
            })
        	
        })
        wx_sign();
        return app;
    });
