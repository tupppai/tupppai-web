define(['underscore',
        'app/models/User',
		'app/views/homepage/HomeHeadView',
		],
    function (_, User, HomeHeadView) {
        "use strict";

        return function(type, uid) {
                $("title").html("图派-个人主页");

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
                var data_uid = $(".user-message").attr("data-uid");
                if( data_uid == uid ) {
                    $(".menu-nav-conduct").trigger("click");
                } else {
                    $(".menu-nav-"+ type +" ").trigger("click");
                }

            },400);
      

        
        };
    });
