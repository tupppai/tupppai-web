define(['app/views/activity/list/index', 'app/views/activity/header/headerView'], 
	function (list, headerView) {
    "use strict";
    return function() {
        var sections = ['_header', '_content'];
        var layoutView = window.app.render(sections);

        // var collection = new window.app.collection();
        // collection.url= "/v2/populars";
        var header = new headerView({
            // collection: collection
        });
        window.app.show(layoutView._header, header);         

        var collection = new window.app.collection();
        collection.url= "/replies?page=1&size=15&category_id=1010";
        var lv = new list({
            collection: collection
        });
        window.app.show(layoutView._content, lv); 
        
    };
});
 
