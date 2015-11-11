define(['app/models/User'], function (User) {
        "use strict";

        return function() {
            var user = new User;
            user.url = '/user/logout';

            user.fetch({
                success: function(){
                    location.href = '#askFlows';
                    location.reload();
                }
            });
        };

    });
