define([ 'app/views/workDetail/workDetail' ], function (index) {
    "use strict";
    return function() {
    	
        var view = new index();
        window.app.content.show(view);
    };
});
