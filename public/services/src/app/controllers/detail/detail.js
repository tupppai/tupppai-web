define(['app/views/detail/detailView'], 
    function (detailView) {
    "use strict";
    return function(type, id) {
        var sections = [ 'content'];
        var layoutView = window.app.render(sections);

        var model = new window.app.model();
        model.url= "/v2/thread/"+ type +"/" + id;
        var detail = new detailView({
            model: model
        });
        window.app.show(layoutView.content, detail);        
    };
});


