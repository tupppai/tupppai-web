define(['app/views/activity/works/worksView', 'lib/component/asyncList'], function (worksView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'hot-pageSection clearfix grid',
    	childView: worksView,
        onShow: function() {
            title('活动');
            this.$el.asynclist({
                root: this,
                renderMasonry: true,
                itemSelector: 'loading',
            });
        }
    });
});
