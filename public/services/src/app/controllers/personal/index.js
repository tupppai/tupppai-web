define([
        'app/views/personal/personalList/personalList', 
        'app/views/personal/personalHeader/personalHeader',
		'app/views/personal/empty/emptyView', 
    ], function (personalList, personalHeader, emptyView) {
    "use strict";
    return function(id, type) {
		var layoutView = window.app.render(['_header', '_content', '_empty']);

        var model = new window.app.model();
        model.url = '/v2/users/' + id;
        var header = new personalHeader({
            model: model
        });
        window.app.show(layoutView._header, header);

        var collection = new window.app.collection();
        collection.url= "/v2/asks?uid=" + id + "&type=ask";
        collection.type = 'ask';
        var lv = new personalList({
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
            lv = new personalList({collection: collection});
            if(type == 'ask') {
                lv.collection.url= "/v2/asks?uid="+ uid +"&type=asks";
                $(".empty-p").text("暂时没有发布求P");
                $(".empty-buttom").removeClass("hide").text("马上求P").attr("href", "#upload/ask");
            } else if(type == 'inprogresses') {
                lv.collection.url= "/v2/" + type + "?uid=" + uid;
                $(".empty-p").text("暂时没有添加帮P");
                $(".empty-buttom").removeClass("hide").text("求P大厅").attr("href", "#original/index");
            } else {
                lv.collection.url= "/v2/" + type + "?uid=" + uid;
                $(".empty-p").text("暂时没有发布作品");
                $(".empty-buttom").addClass("hide");
            }
            lv.collection.type = type;
            window.app.show(layoutView._content, lv);
        });
    };
});
