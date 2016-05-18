define(['app/views/hot/reply/replyView', 'lib/component/asyncList'], function (replyView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'hot-pageSection clearfix grid',
    	childView: replyView,
        onShow: function() {
            title('热门作品');
            $(".menuPs").removeClass("hide");
            this.$el.asynclist({
                root: this,
                renderMasonry: true,
                itemSelector: 'loading',
                callback: function(item) {
                   $('.imageLoad2').imageLoad({scrop: true});
                }
            });
        }
    });
});
