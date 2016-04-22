define(['app/views/list/index', 'app/views/ask/index/indexView' ], 
	function (list, indexView) {
    "use strict";
    return function() {
	    var sections = [ '_view'];
		var layoutView = window.app.render(sections);

        var view = new indexView();
        window.app.show(layoutView._view, view);
    };
});
