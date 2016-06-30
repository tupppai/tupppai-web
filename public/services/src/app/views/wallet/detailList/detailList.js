define(['app/views/wallet/detail/detailView','app/views/personal/empty/emptyView'], function (detailView, emptyView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'wallet-detail-list',
    	childView: detailView,
        emptyView: emptyView,
        onShow: function() {
            title('交易明细');
            $(".menuPs").addClass("hide");
            $(".empty-buttom").addClass("hide");
            $(".empty-p").text("暂时没有交易明细！")
        },
    });
});
