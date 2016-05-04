define(['app/views/ask/index/indexView'], 
	function (indexView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'index-ask-pageSection clearfix grid',
    	childView: indexView
    });
});
