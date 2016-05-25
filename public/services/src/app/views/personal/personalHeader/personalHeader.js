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

                $("body").attr("tapTapy", type)
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

                var tapTapy = $("body").attr("tapTapy")
                var clickId = $(".header-portrait").attr("data-id");
                var currentId = $('body').attr("data-uid");
                if(tapTapy == 'ask') {
                    $(".empty-p").text("暂时没有发布求P");
                    $(".empty-buttom").removeClass("hide").text("马上求P").attr("href", "#upload/ask");
                } else if(tapTapy == 'inprogresses') {
                    $(".empty-p").text("暂时没有添加帮P");
                    $(".empty-buttom").removeClass("hide").text("求P大厅").attr("href", "#original/index");
                } else if(tapTapy == "replies") {
                    $(".empty-p").text("暂时没有发布作品");
                    $(".empty-buttom").addClass("hide");
                }
                if(clickId == currentId) {
                    $(".own").removeClass("hide");
                } else {
                    $(".ta").removeClass("hide");
                }
            }
        });
    });


