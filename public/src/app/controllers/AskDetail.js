define([
        'underscore',
        'app/models/Ask',
        'app/collections/Comments',
        'app/views/askdetail/AskDetailPlayView'
        ],
    function (_, Ask, Comments, ReplyDetailPlayView) {
        "use strict";

        return function(category_id , ask_id) {
            var category_id = category_id;

            setTimeout(function(){
                $("title").html("图派-求P详情");
                $('.header-back').addClass("height-reduce");
            },500);

            var model = new Ask;
            model.url = '/asks/'+ ask_id;
            model.fetch();

            var view = new ReplyDetailPlayView({
                model: model
            });
            window.app.content.show(view);

            setTimeout(function(){
                $('.center-loading').trigger("click");
            },700);
            setTimeout(function(){
                $(".reply-detail-bang").attr("category-id",category_id);
            },2200)

        };
    });