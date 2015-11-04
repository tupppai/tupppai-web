define(['underscore', 'app/collections/Asks', 'app/views/AskView'],
    function (_, Asks, AskView) {
        "use strict";

        return function() {
            var asks = new Asks;


            var view = new AskView({
                collection: asks
            });

            $(".appDownload").click(function(){
                $("a.menu-bar-item").removeClass('active');
                $("a.menu-bar-item[href='#download']").addClass('active');
            });

            window.app.content.show(view);
        };
    });
