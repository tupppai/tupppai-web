define(['underscore',
        'app/models/User',
		'app/views/homepage/HomeHeadView',
		],
    function (_, User, HomeHeadView) {
        "use strict";

        return function(type, uid) {
            setTimeout(function(){
                $("title").html("图派-个人主页");
            },100);

            var user = new User;
            user.url = '/users/' + uid;
            user.fetch();
            
            var view = new HomeHeadView({
                model: user
            });
            window.app.content.show(view);
         

            setTimeout(function(){
                $('.title-bar').addClass("hide");
                $('.header-back').addClass("height-reduce");
                
                $(".menu-nav-"+ type + " ").trigger("click");
            },400);
      

        
        };
    });
