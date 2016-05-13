define(['app/views/hot/reply/replyView', 'lib/component/asyncList'], function (replyView) {
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
            share_friend(options,function(){},function(){});
<<<<<<< HEAD
            share_friend_circle(options,function(){},function(){})

            // 渲染瀑布流
            $('.grid').waterfall({
              // options
              root: '.grid',
              itemSelector: '.grid-item',
              columnWidth: $('.grid-item').width()/2
            });
        },
=======
            this.$el.asynclist(this);
        }
>>>>>>> fd2e0419af848976b968f2d11b5619052a4299b7
    });
});
