define(['app/views/ask/index/indexView', 'waterfall', 'lib/component/asyncList'], 
	function (indexView, waterfall, asynclist) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'index-ask-pageSection clearfix grid',
    	childView: indexView,
        onShow: function() {
            title('帮P');
            $(".menuPs").removeClass("hide");
           
            var _this = this;
            
            var async_list = $('.grid').asynclist({
                collection: _this.collection
            });
            //电影详情页面微信分享文案
            var options = {};
            share_friend(options,function(){},function(){});
            share_friend_circle(options,function(){},function(){})
            /*
            function() {
                _this.collection.load_more({
                    finished: function() {
                        async_list.finish();    
                    },
                    not_finished: function() {
                        async_list.success();
                    }
                });
            }); 
            */
        }
    });
});
