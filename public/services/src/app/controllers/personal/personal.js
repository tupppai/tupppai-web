define(['app/views/list/index',
		'app/views/personal/work/workView', 
		'app/views/personal/processing/processingView',
		'app/views/personal/reply/replyView',
		'app/views/personal/header/headerView',
		 ],
	function (list, workView, processingView, replyView, headerView) {
    "use strict";
    return function() {
        var sections = ['_header', '_content'];
		var layoutView = window.app.render(sections);
		
        // var model = new window.app.model();
        // model.url= "/v2/replies/ask/4269";
        var header = new headerView({
            // model: model
        });
        window.app.show(layoutView._header, header);     
    };
});
