define(['app/views/hot/list/index'], 
	function (list) {
    "use strict";
    return function() {
        var sections = ['_content'];
        var layoutView = window.app.render(sections);

        var collection = new window.app.collection();
        collection.url= "/v2/populars";
        var lv = new list({
            collection: collection
        });
        window.app.show(layoutView._content, lv); 
        
    };
});
 
