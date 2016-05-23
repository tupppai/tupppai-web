define([ 'app/views/upload/reply/replyView' ], function (replyView) {
    "use strict";
    return function(ask_id, category_id) {
    	
        var view = new replyView();
        window.app.content.show(view);
        $("body").attr("ask_id", ask_id);
        $("body").attr("category_id", category_id);
    };
});
