define(['underscore',
        'app/models/User',
		'app/views/homepage/HomeHeadView',
		],
    function (_, User, HomeHeadView) {
        "use strict";

        return function(type, uid) {

            var user = new User;
            user.url = '/users/' + uid;
            user.fetch();
            
            var view = new HomeHeadView({
                model: user
            });
            window.app.content.show(view);

            setTimeout(function(){
                $('.header').addClass("hide");
                $('.header-back').addClass("height-reduce");
                $(".menu-nav-reply").trigger("click");
            },40);
      

        
        };
    });
