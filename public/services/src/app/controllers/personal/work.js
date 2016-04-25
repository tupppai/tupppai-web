define([ 'app/views/personal/ask/askView' ], function (askView) {
    "use strict";
    return function() {
    	
        var view = new askView();
        window.app.content.show(view);
    };
});
