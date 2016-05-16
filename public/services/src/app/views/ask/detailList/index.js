define(['app/views/ask/detail/detailView'], function (detailView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: '',
    	childView: detailView,
        onShow: function() {
            title('详情');
            $(".menuPs").addClass("hide");
        },
    });
});
