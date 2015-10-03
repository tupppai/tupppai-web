define(['underscore', 'app/models/User', 'app/modules/HomeModule'],
    function (_, User, HomeModule) {
        "use strict";

        return function(type, uid) {
            var user = new User;
            user.url = 'users/' + uid;
            user.data.uid   = uid;
            console.log(type);
            var homeModule  = new HomeModule({model: user,});
            user.fetch();
    
           $('#load_ask').trigger('click');
                     
            window.app.content.close();
            window.app.home.show(homeModule);

        };
    });
