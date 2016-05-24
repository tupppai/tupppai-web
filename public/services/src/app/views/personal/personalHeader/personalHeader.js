define([
		'tpl!app/views/personal/personalHeader/personalHeader.html',
		'app/views/personal/original/originalView', 
		'app/views/personal/processing/processingView',
		'app/views/personal/works/worksView',
		],
    function (template, workView, processingView, replyView) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .nav-item": "clickNav",
                "click .follow": "follow"
            },
            clickNav: function(e) {
                $(e.currentTarget).addClass("active").siblings(".nav-item").removeClass("active");

                var type = $(e.currentTarget).attr("data-type");
                var uid  = this.$(".header-portrait").attr("data-id");

                this.trigger('click:nav', type, uid);
            },
            follow: function(e) {
                var dataUid = $(e.currentTarget).attr("data-uid");
                var isFollow = $(e.currentTarget).attr("is-follow");
                var follow;
                if(isFollow) {
                    follow = 0;
                } else {
                    follow = 1;
                };
                $.post('/user/follow', {
                    uid: dataUid,
                    status: follow
                }, function(data) {
                    if(isFollow) {
                        $(e.currentTarget).text("关注").removeClass("following");
                        fntoast("取消关注成功");
                    } else {
                        $(e.currentTarget).text("已关注").addClass("following");
                        fntoast("关注成功");
                    };
                });

            },
            onShow: function() {
                this.$("li.nav-item").removeClass('active');
                this.$("li.nav-item[data-type='ask']").addClass('active');
                var clickId = this.$(".header-portrait").attr("data-id");
                var currentId = $('body').attr("data-uid");
                if(clickId == currentId) {
                    $(".own").removeClass("hide");
                } else {
                    $(".ta").removeClass("hide");
                }
            }
        });
    });


