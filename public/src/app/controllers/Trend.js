define(['underscore',
        'app/views/trend/TrendView',
        'app/collections/replies'
        ],
    function (_, TrendView, Replies) {
        "use strict";

        return function() {

            var replies = new Replies;
            replies.url = 'timeline';
            var view = new TrendView({collection: replies});
            
            window.app.content.show(view);
        
            setTimeout(function(){
                $("title").html("图派-动态页面");
                $('.header-back').removeClass("height-reduce");
            },100);
        };
    });
