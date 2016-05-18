define(['app/views/original/indexList/index'], 
	function (list) {
    "use strict";
    return function() {
	    var sections = [ '_view'];
		var layoutView = window.app.render(sections);

        var collection = new window.app.collection();
        collection.url= "/v2/asks";

        var view = new list({
        	collection: collection
        });
        window.app.show(layoutView._view, view);
    };
});
