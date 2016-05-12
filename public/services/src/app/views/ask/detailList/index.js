define(['app/views/ask/detail/detailView'], function (detailView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: '',
    	childView: detailView,
        onShow: function() {
            title('详情');
            $(".menuPs").addClass("hide");
            
            var img = $(".sectionContent").eq(0).find("img").attr("src");
            var desc = $(".workDesc").eq(0).text();
            //电影详情页面微信分享文案
            var options = {};
            options.title    = "图片分享";
            options.desc    = desc;
            options.img    = img;
            
            share_friend(options,function(){},function(){});
            share_friend_circle(options,function(){},function(){})
        },
    });
});
