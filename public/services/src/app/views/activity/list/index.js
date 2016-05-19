define(['app/views/activity/index/indexView', 'lib/component/asyncList'], function (indexView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'hot-pageSection clearfix grid',
    	childView: indexView,
        onShow: function() {
            title('活动');
            $(".menuPs").removeClass("hide");
            // this.$el.asynclist({
            //     root: this,
            //     renderMasonry: true,
            //     itemSelector: 'loading',
            // });
        }
    });
});
