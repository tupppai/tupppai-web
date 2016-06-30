define(['app/views/wallet/detailList/detailList'], 
    function (detailList) {
    "use strict";
    return function() {
        var sections = ['_content'];
        var layoutView = window.app.render(sections);

        var collection = new window.app.collection();
        collection.url= "/v2/user/transactions";
        var lv = new detailList({
            collection: collection  
        });
        window.app.show(layoutView._content, lv);         
    };
});
 
