define(['underscore', 'app/collections/Asks', 'app/views/AskView', 'tpl!app/templates/AskItemView.html'],
    function (_, Asks, AskView, askItemTemplate) {
        "use strict";

        return function() {
            var asks = new Asks;


            var view = new AskView({
                collection: asks,
                template: askItemTemplate,
            });

            $(".appDownload").click(function(){
                    $("a.menu-bar-item").removeClass('active');
                    $("a.menu-bar-item[href='#download']").addClass('active');
                });

            window.app.home.close();
            window.app.content.show(view);
        };
    });
