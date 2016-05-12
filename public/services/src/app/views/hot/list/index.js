define(['app/views/hot/reply/replyView'], function (replyView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'hot-pageSection clearfix grid',
    	childView: replyView,
        onShow: function() {
            title('热门作品');
            $(".menuPs").removeClass("hide");
            
            //电影详情页面微信分享文案
            var options = {};
            
            share_friend(options,function(){},function(){})
        },
    });
});
