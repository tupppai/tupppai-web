define(['app/views/activity/activityWork/activityWork', 'lib/component/asyncList'], function (activityWork) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'hot-pageSection clearfix grid',
    	childView: activityWork,
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
