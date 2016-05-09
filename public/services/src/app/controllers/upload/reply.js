define([ 'app/views/upload/reply/replyView' ], function (replyView) {
    "use strict";
    return function(id) {
    	
        var view = new replyView();
        window.app.content.show(view);
        $("body").attr("ask_id", id)
    };
});
