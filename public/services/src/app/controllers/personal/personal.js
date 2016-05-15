define([
        'app/views/personal/list/index', 
        'app/views/personal/header/headerView',
		'app/views/personal/empty/emptyView', 
	], function (listView, headerView, emptyView) {
    "use strict";
    return function(id, type) {
		var layoutView = window.app.render(['_header', '_content', '_empty']);
        var collection = new window.app.collection();
        collection.url= "/v2/asks?uid=" + id + "&type=ask";
        collection.type = 'ask';

        var model = new window.app.model();

        var lv = new listView({
            collection: collection
        });
        window.app.show(layoutView._content, lv);      

        model.url = '/v2/users/' + id;
        var header = new headerView({
            model: model,
            listenList: collection,
            listenView: lv
        });
        window.app.show(layoutView._header, header);      

        // var empty = new emptyView({
        // });
        // window.app.show(layoutView._empty, empty); 
    };
});
