define(['app/views/list/index', 'app/views/ask/detail/detailView'], 
	function (list, detailView) {
    "use strict";
    return function() {
        var sections = [ '_view', '_comment'];
		var layoutView = window.app.render(sections);

        var model = new window.app.model();
        model.url= "/v2/replies/ask/4269";
        var view = new detailView({
            model: model
        });
        window.app.show(layoutView._view, view);        

        // var collections = new window.app.collections();
        // collections.url= "/v2/comments?collections&type=2%target_id=";
        // var detailCommentView = new detailCommentView({
        //     collections: collections
        // });
        // window.app.show(layoutView._comment, detailCommentView);
    };
});
 
