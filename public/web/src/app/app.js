
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
            app.user.url = '/v2/user';
            app.head = new header();
            app.header.show(app.head);
            app.foot = new footer();
            app.footer.show(app.foot);

            app.user.fetch({
                success:function(data) {
                    $("body").attr("data-uid", data.get('uid'));
                    $("body").attr("data-nickname", data.get('nickname'));
                    $("body").attr("data-src", data.get('avatar'));

                    // $(".personalCenter").attr("data-uid", data.get('uid'));
                    // $(".personalCenter").attr("data-nickname", data.get('nickname'));
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
