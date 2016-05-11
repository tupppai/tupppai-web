define(['app/views/ask/index/indexView', 'waterfall', 'lib/component/asyncList'], 
	function (indexView, waterfall, asynclist) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'index-ask-pageSection clearfix grid',
    	childView: indexView,
        onShow: function() {
            title('帮P');
            $(".menuPs").removeClass("hide");
        }
    });
});
