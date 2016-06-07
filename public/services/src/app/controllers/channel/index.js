define(['app/views/channel/channelList/channelList', 'app/views/channel/otherChannel/otherChannel', 'lib/component/pullHandle',], 
	function (list, otherChannel, pullHandle) {
    "use strict";
    return function() {
        var sections = ['_content', '_other'];
        var layoutView = window.app.render(sections);

        var collection = new window.app.collection();
        collection.url= "/categories/list?page=1&size=15";
        var lv = new list({
            collection: collection
        });
        window.app.show(layoutView._content, lv);         

        var collection = new window.app.collection();
        collection.url= "/replies?size=2&category_id=0";
        var other = new otherChannel({
            collection: collection
        });
        window.app.show(layoutView._other, other); 
        lv.on('show', function() {
            new pullHandle({
                view: this,
                size: 5,
                url: '/categories/list?page=1&size=15',
                pullDown: true,
                container: '.hot-pageSection',
            });
        }); 
    };
});
 
