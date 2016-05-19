define(['app/views/activity/list/index', 'app/views/activity/header/headerView'], 
	function (list, headerView) {
    "use strict";
    return function() {
        var sections = ['_header', '_content'];
        var layoutView = window.app.render(sections);

        // var collection = new window.app.collection();
        // collection.url= "/v2/populars";
        var header = new headerView({
            // collection: collection
        });
        window.app.show(layoutView._header, header);         

        var collection = new window.app.collection();
        collection.url= "/replies?page=1&size=15&category_id=1010";
        var lv = new list({
            collection: collection
        });
        window.app.show(layoutView._content, lv); 

        lv.on('show', function() {
            this.$el.asynclist({
                root: this,
                collection: this.collection,
                renderMasonry: true,
                itemSelector: 'loading' 
            });
        });    

        header.on('click:nav', function(type, uid) {
            lv = new list({collection: collection});
            if(type == 'hot') {
                lv.collection.url= "replies?page=1&size=15&category_id=1010";
            }  else {
                lv.collection.url= "replies?page=1&size=15&category_id=1010";
            }
            lv.collection.type = type;
            window.app.show(layoutView._content, lv);
        });
        
    };
});
 
