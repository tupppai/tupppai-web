define(['app/views/ask/detailList/index'], 
    function (list) {
    "use strict";
    return function(ask_id, reply_id) {
        var sections = [ 'content'];
        var layoutView = window.app.render(sections);

        var collection = new window.app.collection();
        collection.url= "/v2/replies/ask/"+ ask_id;
        var lv = new list({
            collection: collection
        });
        window.app.show(layoutView.content, lv);        
    };
});


