define(['app/views/list/index', 'app/views/ask/detail/detailView' ], 
	function (list, detailView) {
    "use strict";
    return function() {
        var sections = [ '_view'];
		var layoutView = window.app.render(sections);

        var model = new window.app.model();
        model.url=" /v2/replies/ask/4341";
        var view = new detailView({
            model: model
        });
        model.fetch({
            success: function() {
                layoutView._view.show(view);
            }
        });
    };
});
 