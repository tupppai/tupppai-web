define(['app/views/detail/detailWorks/detailWorks'], function (detailWorks) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: '',
    	childView: detailWorks,
        onShow: function() {
            title('详情');
            $(".menuPs").addClass("hide");
        },
    });
});
