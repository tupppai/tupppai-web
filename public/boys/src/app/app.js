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
                    var code = data.get('code');
                    //求P成功 没有作品也没有被拒绝
                    if(code == 1) {
                        location.href = 'http://' + location.hostname + '/boys/obtainsuccess/obtainsuccess';
                    } 
                    //求P成功有作品
                    if(code == 2) {
                        location.href = 'http://' + location.hostname + '/boys/obtainsuccess/obtainsuccess';
                    } 
                    //求P被拒绝
                    if(code == -1) {
                        location.href = 'http://' + location.hostname + '/boys/uploadagain/uploadagain';
                    } 
                    if(code == -2 ) {
                        location.href = 'http://' + location.hostname + '/boys/index/index';
                    }
                    $("body").attr("data-user", data.get('left_amount'));
                    // $("body").attr("data-rand", data.get('rand'));
                    // $("body").attr("data-name", data.get('designer_name'));
                }
            })
        	
        })
        wx_sign();
        return app;
    });
