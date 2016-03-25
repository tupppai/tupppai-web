define([ 'app/views/workDetail_download-origin/workDetail_download-origin' ], function (index) {
    "use strict";
    return function() {
    	
        var view = new index();
        window.app.content.show(view);
    };
});
