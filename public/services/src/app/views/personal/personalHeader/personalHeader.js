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
                "click .get-follow": "follow"
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
                    if(isFollow) {
                        $(e.currentTarget).text("已关注").addClass("following").removeClass("have－follow");
                        fntoast("关注成功");
                    } else {
                        $(e.currentTarget).text("关注").addClass("have－follow").removeClass("following");
                        fntoast("取消关注成功");
                    };
                });

            },
            onShow: function(data) {
                this.$("li.nav-item").removeClass('active');
                this.$("li.nav-item[data-type='ask']").addClass('active');

                var tapTapy = $("body").attr("tapTapy")
                var clickId = $(".header-portrait").attr("data-id");
                var currentId = $('body').attr("data-uid");
                if(clickId == currentId) {
                    $(".get-follow").addClass("hide")
                    $(".own").removeClass("hide");
                    $(".empty-buttom").removeClass("hide").text("马上求P").attr("href", "#upload/ask");
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
                } else {
                    $(".get-follow").removeClass("hide")
                    $(".ta").removeClass("hide");
                    $(".empty-buttom").addClass("hide");
                    if(tapTapy == 'ask') {
                        $(".empty-p").text("暂时没有发布求P");
                    } else if(tapTapy == 'inprogresses') {
                        $(".empty-p").text("暂时没有添加帮P");
                    } else if(tapTapy == "replies") {
                        $(".empty-p").text("暂时没有发布作品");
                    }
                }
            }
        });
    });


