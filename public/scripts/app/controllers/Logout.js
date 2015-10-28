define(['app/models/User'], function (User) {
        "use strict";

        return function() {
            var user = new User;
            user.url = '/user/logout';

            user.fetch({
                success: function(b,d){
                    console.log(b);
                    console.log(d);
                    location.href = '#asks';
                    location.reload();
                }
            });
        };

    });
