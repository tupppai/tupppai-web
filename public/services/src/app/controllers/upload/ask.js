define([ 'app/views/upload/ask/askView' ], function (askView) {
    "use strict";
    return function(channel_id) {
   
        var view = new askView();
        window.app.content.show(view);
        $("body").attr("channel_id", channel_id)
    };
});
