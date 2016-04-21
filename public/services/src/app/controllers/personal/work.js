define([ 'app/views/personal/work/workView' ], function (workView) {
    "use strict";
    return function() {
    	
        var view = new workView();
        window.app.content.show(view);
    };
});
