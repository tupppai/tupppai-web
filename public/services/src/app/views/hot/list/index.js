define(['app/views/hot/reply/replyView'], function (replyView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'hot-pageSection clearfix',
    	childView: replyView
    });
});
