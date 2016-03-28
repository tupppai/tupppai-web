define([ 'app/views/workDetailDownloadOrigin/workDetailDownloadOrigin' ], function (index) {
    "use strict";
    return function() {
    	
        var view = new index();
        window.app.content.show(view);
    };
});
