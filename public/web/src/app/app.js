
define('app/app', [ 'marionette', 'app/util', 'imageLazyLoad','common'],
    function (marionette, util, imageLazyLoad,common) {
        "use strict";
        var app  = new marionette.Application();

        app.addRegions({
            header: '#header-section',
            content: '#content-section',
            footer: '#footer-section',
        });

        app.addInitializer(function (options) {

        });

        require([
                'app/views/common/header/header',
                'app/views/common/footer/footer'
                ], function(header,footer) {
            app.user = new window.app.model();
            app.user.url = '/user/status';
            app.head = new header({});
            app.header.show(app.head);
            app.foot = new footer();
            app.footer.show(app.foot);


            $('#header-login-avatar').addClass('none');
            $('#header-login-btn').removeClass('none');
            app.user.fetch({
                success:function(data) {
                    if(data.get('uid')) {

                    $("body").attr("data-uid", data.get('uid'));
                    $("body").attr("data-nickname", data.get('nickname'));
                    $("body").attr("data-src", data.get('avatar'));

                    $(".avatar-src").attr("href", "#user/user/" + data.get('uid'));
                    $(".avatar-src img").attr("src", data.get('avatar'));
                    $('#header-login-btn').addClass('none');
                    $('#header-login-avatar').removeClass('none');
                    }
                }
            })
        });

        for(var i in util) {
            app[i] = util[i];
        }
        return app;
    });
