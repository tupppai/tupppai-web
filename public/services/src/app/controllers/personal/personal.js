define([
        'app/views/personal/list/index', 
        'app/views/personal/header/headerView',
		'app/views/personal/empty/emptyView', 
    ], function (listView, headerView, emptyView) {
    "use strict";
    return function(id, type) {
		var layoutView = window.app.render(['_header', '_content', '_empty']);
        var collection = new window.app.collection();
        collection.url= "/v2/asks?uid=" + id + "&type=ask";
        collection.type = 'ask';

        var model = new window.app.model();

        var lv = new listView({
            collection: collection
        });
        window.app.show(layoutView._content, lv);      

        model.url = '/v2/users/' + id;
        var header = new headerView({
            model: model
        });
        window.app.show(layoutView._header, header);

        lv.on('show', function() {
            this.$el.asynclist({
                root: this,
                collection: this.collection,
                renderMasonry: true,
                itemSelector: 'loading' 
            });
        });      

        header.on('click:nav-item', function(type) {
            var uid = id;
            $(".personal-grid").empty();
            if(type == 'ask') {
                lv.collection.url= "/v2/asks?uid="+ uid +"&type=asks";
            } else {
                lv.collection.url= "/v2/" + type + "?uid=" + uid;
            }
            lv.collection.type = type;
            lv.collection.fetch();
        });        
        // header.on('show', function() {
        //     var uid = id;
        //     this.$('.nav-item').click(function() {
        //         $(".personal-grid").empty();
        //         $(this).addClass("active").siblings(".nav-item").removeClass("active");
        //         var type = $(this).attr("data-type");
        //         lv.collection.url= "/v2/" + type + "?uid=" + uid;
        //         if(type == 'ask') {
        //             lv.collection.url= "/v2/asks?uid="+ uid +"&type=asks";
        //         }
        //         lv.collection.type = type;
        //         lv.collection.fetch();
        //         lv.trigger('show');
        //     });
        // });
    };
});
