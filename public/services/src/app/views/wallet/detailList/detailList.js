define(['app/views/wallet/detail/detailView'], function (detailView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'wallet-detail-list',
    	childView: detailView,
        onShow: function() {
            title('交易明细');
            $(".menuPs").addClass("hide");
        },
    });
});
