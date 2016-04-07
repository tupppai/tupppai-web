define([ 'app/views/personal/workView' ], function (workView) {
    "use strict";
    return function() {
    	
        var view = new workView();
        window.app.content.show(view);
    };
});
