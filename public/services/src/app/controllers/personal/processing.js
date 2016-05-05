define([ 'app/views/personal/processing/processingView' ], function (processing) {
    "use strict";
    return function() {
    	
        var view = new processing();
        window.app.content.show(view);
    };
});
