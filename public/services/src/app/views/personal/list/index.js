define([
		'app/views/personal/work/workView', 
		'app/views/personal/processing/processingView',
        'app/views/personal/reply/replyView',
		'app/views/personal/empty/emptyView',
		],
    function (workView, processingView, replyView, emptyView) {
        "use strict";
        
        return window.app.list.extend({
            tagName: 'div',
            className: 'grid personal-grid',
            emptyView: emptyView,
            getChildView: function(item) {
                switch(item.collection.type) {
                    case 'replies':
                        return replyView;
                    case 'inprogresses':
                        return processingView;
                    case 'ask':
                    default:
                        return workView;
                }
            },
            onShow: function() {
                title('个人中心');
                $(".menuPs").removeClass("hide");

                // 渲染瀑布流
                $('.grid').waterfall({
                  // options
                  root: '.grid',
                  itemSelector: '.grid-item',
                  columnWidth: $('.grid-item').width()/2
                });

                //电影详情页面微信分享文案
                var options = {};
                share_friend(options,function(){},function(){});
                share_friend_circle(options,function(){},function(){})
            }
        });
    });
