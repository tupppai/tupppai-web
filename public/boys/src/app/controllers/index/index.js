define(['app/views/index/IndexView' ], 
	function ( indexView) {
    "use strict";
    return function() {
        var view = new indexView({
        	// model: boyMessage
        });
        window.app.content.show(view);
    };
});
