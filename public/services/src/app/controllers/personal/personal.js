define([
        'app/views/personal/list/index', 
		'app/views/personal/header/headerView',
	], function (listView, headerView) {
    "use strict";
    return function(id, type) {
		var layoutView = window.app.render(['_header', '_content']);
        var collection = new window.app.collection();
        collection.url= "/v2/asks?uid=1&type=ask";
        collection.type = 'ask';

        // var model = new window.app.model();
        // model.url= "/v2/replies/ask/4269";
        var header = new headerView({
            // model: model,
            listenList: collection,
        });
        window.app.show(layoutView._header, header);   

        var lv = new listView({
            collection: collection
        });
        window.app.show(layoutView._content, lv); 
    };
});
