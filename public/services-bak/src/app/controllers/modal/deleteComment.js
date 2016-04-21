define([ 'app/views/modal/deleteCommentView' ], function (deleteCommentView) {
    "use strict";
    return function() {
    	
        var view = new deleteCommentView();
        window.app.content.show(view);
    };
});
