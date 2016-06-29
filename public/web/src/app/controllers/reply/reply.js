define([
        'app/views/reply/hotList',
        ],
    function ( hotList) {
    "use strict";
    return function() {
        var sections = ['_hotList'];
        var layoutView = window.app.render(sections);


        //评论列表
        var hot = new window.app.collection();
        hot.url = "/v2/populars";
        var header = new hotList({
            collection: hot
        });
        window.app.show(layoutView._hotList, header);

    };
});

