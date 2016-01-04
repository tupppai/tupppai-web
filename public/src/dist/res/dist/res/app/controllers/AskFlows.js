define(['underscore', 'app/collections/Asks', 'app/views/ask/AskFlowsView'],
    function (_, Asks, AskFlowsView) {
        "use strict";

        return function(category_id) {
            var category_id = category_id;            

            setTimeout(function(){
                $(".upload-ask").attr("data-id",category_id);
            },1000);

            var asks = new Asks;
            asks.data.width = 300;
            asks.data.category_id = category_id;
            var view = new AskFlowsView({
                collection: asks
            });

            window.app.content.show(view);
        };
    });
