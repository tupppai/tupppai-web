define(['tpl!app/views/personal/honor/fans.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'followsList',
            template: template,
            events: {
                "click #followHe": "follow",
                // "click #cancelFollow": "cancelFollow",
            },
            //我关注的列表取消关注
            cancelFollow: function(e) {
                e.preventDefault();
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
                    $(e.currentTarget).parents(".followsList").remove();
                    fntoast("取消关注成功");
                });
            },
            //我粉丝列表关注或取消关注
            follow: function(e) {
                e.preventDefault();
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
                        $(e.currentTarget).attr("isFollow", 1);
                        $(e.currentTarget).addClass("mutual").removeClass("following");
                        fntoast("关注成功");
                    } else {
                        $(e.currentTarget).attr("isFollow", 0);
                        $(e.currentTarget).addClass("following").removeClass("mutual");
                        fntoast("取消关注成功");
                    }

                });
            },
        });
    });
