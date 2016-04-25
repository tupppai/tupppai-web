define([ 'app/views/personal/processing/processingView' ], function (processing) {
    "use strict";
    return function() {
        var sections = [ '_view', '_header', '_content'];
		var layoutView = window.app.render(sections);
		
        // var model = new window.app.model();
        // model.url= "/v2/replies/ask/4269";
        var view = new detailView({
            // model: model
        });
        window.app.show(layoutView._view, view);     
        window.app.show(layoutView._view, view);        
    };
});
