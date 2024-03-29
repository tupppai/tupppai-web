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
            //tab切换
            clickNav: function(e) {
                $(e.currentTarget).addClass("active").siblings(".nav-item").removeClass("active");
                var type = $(e.currentTarget).attr("data-type");    //点击了哪个标签
                var uid  = this.$(".header-portrait").attr("data-id");  //当前页面中uid

                $("body").attr("tapTapy", type);
                this.trigger('click:nav', type, uid);
            },
            //关注或取消关注
            follow: function(e) {
                e.preventDefault();
                var dataUid = $(".header-portrait").attr("data-id");
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
                        $(e.currentTarget).attr("isFollow", 1);
                        $(e.currentTarget).text("已关注").addClass("following").removeClass("have－follow");
                        fntoast("关注成功");
                    } else {
                        $(e.currentTarget).attr("isFollow", 0);
                        $(e.currentTarget).text("关注").addClass("have－follow").removeClass("following");
                        fntoast("取消关注成功");
                    };
                });
            },
            onShow: function(data) {
                $(".menuPs").addClass("hide");
                this.$("li.nav-item").removeClass('active');
                this.$("li.nav-item[data-type='ask']").addClass('active');

                var tapTapy = $("body").attr("tapTapy");    //点击了哪个标签
                var clickId = $(".header-portrait").attr("data-id"); //当前页面中uid
                var currentId = window.app.user.get('uid'); //获取登入id
                if(clickId == currentId) {
                    $(".people-money").removeClass("hide");
                    $(".get-follow").addClass("hide")
                    $(".own").removeClass("hide");
                    $(".empty-buttom").removeClass("hide").text("马上求P").attr("href", "#upload/ask");
                    if(tapTapy == 'ask') {
                        $(".empty-p").text("暂时没有发布求P");
                        $(".empty-buttom").removeClass("hide").text("马上求P").attr("href", "#upload/ask");
                    }
                    if(tapTapy == 'inprogresses') {
                        $(".empty-p").text("暂时没有添加帮P");
                        $(".empty-buttom").removeClass("hide").text("求P大厅").attr("href", "#original/index");
                    }
                    if(tapTapy == "replies") {
                        $(".empty-p").text("暂时没有发布作品");
                        $(".empty-buttom").addClass("hide");
                    }
                } else {
                    $(".people-money").addClass("hide");
                    $(".get-follow").removeClass("hide")
                    $(".ta").removeClass("hide");
                    $(".empty-buttom").addClass("hide");
                    if(tapTapy == 'ask') {
                        $(".empty-p").text("暂时没有发布求P");
                    }
                    if(tapTapy == 'inprogresses') {
                        $(".empty-p").text("暂时没有添加帮P");
                    }
                    if(tapTapy == "replies") {
                        $(".empty-p").text("暂时没有发布作品");
                    }
                }
            }
        });
    });


