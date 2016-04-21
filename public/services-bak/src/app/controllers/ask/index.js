define([ 'app/views/ask/indexView' ], 
	function (indexView) {
    "use strict";
    return function() {
    	
        var view = new indexView();
        window.app.content.show(view);
    };
});
