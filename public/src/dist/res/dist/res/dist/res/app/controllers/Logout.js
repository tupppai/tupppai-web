define(['app/models/User'], function (User) {
        "use strict";

        return function() {
            var user = new User;
            user.url = '/user/logout';

            WB2.logout();
            user.fetch({
                success: function(){
                    location.href = '/#index';
                    location.reload();
                }
            });
        };

    });
