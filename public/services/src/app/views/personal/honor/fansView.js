define(['tpl!app/views/personal/honor/fans.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click #followHe": "follow"
            },
            onShow: function() {

            },
            follow: function(e) {
                var dataUid = $(e.currentTarget).attr("data-uid");
                var isFollow = +$(e.currentTarget).attr("isFollow");
                if(isFollow) {
                    isFollow = 0;
                } else {
                    isFollow = 1;
                };
                $.post('/user/follow', {
                    uid: dataUid,
                    status: isFollow
                }, function(data) {
                    if(isFollow == 1) {
                        $(e.currentTarget).addClass("mutual").removeClass("following");
                        fntoast("关注成功");
                    } else {
                        $(e.currentTarget).addClass("following").removeClass("mutual");
                        fntoast("取消关注成功");
                    }

                });

            },
        });
    });
