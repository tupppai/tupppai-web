define(['app/views/ask/index/indexView', 'lib/component/asyncList'], 
	function (indexView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'index-ask-pageSection clearfix grid',
    	childView: indexView,
        initialize: function() {
        },
        onRender: function() {
            title('帮P');
            $(".menuPs").removeClass("hide");
        },
        onShow: function() {
            /*
            this.$el.asynclist({
                root: this,
                renderMasonry: true,
                itemSelector: 'loading',
                collection: this.collection
            });
            */
            //电影详情页面微信分享文案
            var options = {};
            share_friend(options,function(){},function(){});
            share_friend_circle(options,function(){},function(){})
        }
    });
});
