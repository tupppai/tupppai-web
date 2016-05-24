define(['app/views/channel/channelList/channelList'], 
	function (list) {
    "use strict";
    return function() {
        var sections = ['_content'];
        var layoutView = window.app.render(sections);

        var collection = new window.app.collection();
        collection.url= "/categories/list?page=1&size=15";
        var lv = new list({
            collection: collection
        });
        window.app.show(layoutView._content, lv); 
        
    };
});
 
