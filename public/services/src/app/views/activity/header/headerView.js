define(['tpl!app/views/activity/header/header.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .works-tap": "worksTap",
                "click .mongolia-layer": "activityShare",
            },
            onShow: function() {

            },
            worksTap: function(e) {
                var type = $(e.currentTarget).attr("data-type");
                $(e.currentTarget).addClass("activity").siblings(".works-tap").removeClass("activity");
                this.trigger('click:nav', type);
            },
            activityShare: function(e) {
                $(e.currentTarget).addClass("hide");

                var img = $(".sectionContent").eq(0).find("img").attr("src");
                var title = $(".activity-title").text();
                //电影详情页面微信分享文案
                var options = {};
                options.title    = title;
                options.desc    = "晒创意毕业照赢宋仲基粉丝见面会门票啦";
                options.img    = "http://7u2spr.com1.z0.glb.clouddn.com/20160519-170929573d82c99d903.jpeg?imageView2/2/w/480";

                share_friend(options,function(){},function(){});
                share_friend_circle(options,function(){},function(){});
            }

        });
    });
