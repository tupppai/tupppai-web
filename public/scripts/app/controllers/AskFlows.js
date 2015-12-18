define(['underscore', 'app/collections/Asks', 'app/views/ask/AskFlowsView'],
    function (_, Asks, AskFlowsView) {
        "use strict";

        return function(category_id) {

            var category_id = category_id;            

            setTimeout(function(){
                $(".upload-ask").attr("data-id",category_id);
            },1000);

            setTimeout(function(){
                $("title").html("图派-原图");
                $('.header-back').removeClass("height-reduce");
            },100);

            var asks = new Asks;
            asks.data.width = 300;

            var view = new AskFlowsView({
                collection: asks
            });

            window.app.content.show(view);
        };
    });
