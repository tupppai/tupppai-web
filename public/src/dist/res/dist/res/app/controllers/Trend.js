define(['underscore','app/views/trend/TrendView','app/collections/Replies'],
    function (_, trendView, Replies) {
        "use strict";

        return function() {

            setTimeout(function(){
                $("title").html("图派-动态页面");
                $('.header-back').removeClass("height-reduce");
            },100);

        	var replies = new Replies;
        	replies.url = 'timeline';
            var view = new trendView({collection: replies});
            
            window.app.content.show(view);
        
        };
    });
