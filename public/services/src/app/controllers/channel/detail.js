define(['app/views/channel/channelDetail/channelDetail', 'app/views/channel/channelDetailList/channelDetailList',], 
	function (channelDetail, list) {
    "use strict";
    return function(channel_id) {
        var sections = ['_content', '_list'];
        var layoutView = window.app.render(sections);

        var model = new window.app.model();
        model.url= "activities/" + channel_id;
        var header = new channelDetail({
            model: model
        });
        window.app.show(layoutView._content, header);         

        var collection = new window.app.collection();
        collection.url= "/replies?page=1&size=15&category_id=" + channel_id;
        collection.type = 'works';
        var lv = new list({
            collection: collection
        });
        window.app.show(layoutView._list, lv); 
        
        lv.on('show', function() {
            title('频道');
            $(".menuPs").removeClass("hide");
            this.$el.asynclist({
                root: this,
                collection: this.collection,
                renderMasonry: true,
                itemSelector: 'loading',
                callback: function(item) {
                   $('.imageLoad2').imageLoad({scrop: true});
                }
            });
        });

        header.on('click:nav', function(type) {
            lv = new list({collection: collection});
            if(type == 'works') {
                lv.collection.url= "/replies?page=1&size=15&category_id=" + channel_id;
            } else {
                lv.collection.url= "/asks?page=1&size=15&category_id=" + channel_id;
            }
            lv.collection.type = type;
            window.app.show(layoutView._list, lv);
        });
        
    };
});
 
