define(['app/views/ask/detail/detailView'], 
	function (detailView) {
    "use strict";
    return function(ask_id, reply_id) {
        var sections = [ '_view'];
		var layoutView = window.app.render(sections);

        var model = new window.app.model();
        model.url= "/v2/replies/ask/"+ ask_id;
        var view = new detailView({
            model: model
        });
        window.app.show(layoutView._view, view);        
    };
});
// define(['app/views/ask/detailList/index'], 
//     function (list) {
//     "use strict";
//     return function(ask_id, reply_id) {
//         var sections = [ 'content'];
//         var layoutView = window.app.render(sections);

//         var model = new window.app.model();
//         model.url= "/v2/replies/ask/"+ ask_id;
//         var lv = new list({
//             model: model
//         });
//         window.app.show(layoutView.content, lv);        
//     };
// });


