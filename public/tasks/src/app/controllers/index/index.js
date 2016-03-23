define([ 'app/views/index/index' ], function (index) {
    "use strict";
    return function() {
    	
        var view = new index();
        window.app.content.show(view);
    };
});
