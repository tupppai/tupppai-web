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

        model.url = '/v2/users/' + id;
        var header = new headerView({
            model: model
        });
        window.app.show(layoutView._header, header);

        var lv = new listView({
            collection: collection
        });
        window.app.show(layoutView._content, lv);

        header.on('click:nav', function(type, uid) {
            lv = new listView({collection: collection});
            lv.collection.url= "/v2/" + type + "?uid=" + uid;
            if(type == 'ask') {
                lv.collection.url= "/v2/asks?uid="+ uid +"&type=asks";
            }
            lv.collection.type = type;
            window.app.show(layoutView._content, lv);
        });

        header.on('show', function() {
            //电影详情页面微信分享文案
            var options = {};
            share_friend(options,function(){},function(){});
            share_friend_circle(options,function(){},function(){})
        });
    };
});
