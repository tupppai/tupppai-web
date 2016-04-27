define(['app/views/list/index', 'app/views/hot/replyView'], 
	function (list, replyView) {
    "use strict";
    return function() {
        var sections = [ '_view'];
        var layoutView = window.app.render(sections);


        var collection = new window.app.collection();
        collection.url= "/v2/populars";

        var view = new replyView({
            collection: collection,
        });
        window.app.show(layoutView._view, view); 

    };
});
 
