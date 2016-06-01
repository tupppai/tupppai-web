define(['app/views/hot/hotList/hotList', 'app/views/hot/bannerList/bannerList', 'app/views/hot/hotChannel/hotChannel'], 
	function (list, bannerList, hotChannel) {
    "use strict";
    return function() {
        var sections = ['_banner', '_channel', '_content'];
        var layoutView = window.app.render(sections);

        var collection = new window.app.collection();
        collection.url= "/banners";
        var banner = new bannerList({
            collection: collection
        });
        window.app.show(layoutView._banner, banner);         

        var collection = new window.app.collection();
        collection.url= "/categories/list";
        var channel = new hotChannel({
            collection: collection
        });
        window.app.show(layoutView._channel, channel); 

        var collection = new window.app.collection();
        collection.url= "/v2/populars";
        var lv = new list({
            collection: collection
        });
        window.app.show(layoutView._content, lv);         
    };
});
 
