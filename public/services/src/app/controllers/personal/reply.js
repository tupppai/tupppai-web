define([ 'app/views/personal/replyView' ], 
	function (replyView) {
	    "use strict";
	    return function() {
	    	
	        var view = new replyView();
	        window.app.content.show(view);
    };
});
