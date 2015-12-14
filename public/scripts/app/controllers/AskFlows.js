define(['underscore', 'app/collections/Asks', 'app/views/ask/AskFlowsView'],
    function (_, Asks, AskFlowsView) {
        "use strict";

        return function() {

            setTimeout(function(){
                $("title").html("图派-原图");
            },100);

            var asks = new Asks;
            asks.data.width = 300;

            var view = new AskFlowsView({
                collection: asks
            });

            window.app.content.show(view);
        };
    });
