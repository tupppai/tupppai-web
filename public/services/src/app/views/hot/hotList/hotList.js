define(['app/views/hot/hotWorks/hotWorks', 'lib/component/asyncList'], function (worksView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'hot-pageSection clearfix grid',
    	childView: worksView,
        onShow: function() {
            title('首页');
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
