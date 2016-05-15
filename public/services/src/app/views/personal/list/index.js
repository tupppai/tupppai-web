define([
		'app/views/personal/work/workView', 
		'app/views/personal/processing/processingView',
        'app/views/personal/reply/replyView',
		'app/views/personal/empty/emptyView',
        'lib/component/asyncList'
		],
    function (workView, processingView, replyView, emptyView, asynclist) {
        "use strict";
        
        return window.app.list.extend({
            tagName: 'div',
            className: 'grid personal-grid ',
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

                this.$el.asynclist({
                    root: this,
                    renderMasonry: true,
                    itemSelector: 
                });
                
                //电影详情页面微信分享文案
                var options = {};
                share_friend(options,function(){},function(){});
                share_friend_circle(options,function(){},function(){});
            }
        });
    });
