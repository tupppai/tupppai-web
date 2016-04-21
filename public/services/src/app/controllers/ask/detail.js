define([ 'app/views/ask/detailView' ], function (detailView) {
    "use strict";
    return function() {
    	
        var view = new detailView();
        window.app.content.show(view);
    };
});
