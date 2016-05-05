define(['app/views/ask/index/indexView', 'waterfall'], 
	function (indexView, waterfall) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'index-ask-pageSection clearfix grid',
    	childView: indexView
    });
});
