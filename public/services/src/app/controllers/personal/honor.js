define([
        'app/views/personal/honorList/honorList', 
    ], function (honorList) {
    "use strict";
    return function(id, type) {
		var layoutView = window.app.render(['_header', '_content', '_empty']);

        var collection = new window.app.collection();
        collection.url= "/" + type + "?uid=" + id;
        var lv = new honorList({
            collection: collection
        });
        window.app.show(layoutView._content, lv);
        $("body").attr("honor-type", type)
        
    };
});
