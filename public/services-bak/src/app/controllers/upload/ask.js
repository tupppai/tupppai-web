define([ 'app/views/upload/askView' ], function (askView) {
    "use strict";
    return function() {
    	
        var view = new askView();
        window.app.content.show(view);
    };
});
