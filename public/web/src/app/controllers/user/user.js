define([
        'app/views/user/header/header',
        'app/views/user/work/work',
		'app/views/user/askList',
		],
	function (header,work, askList) {
    "use strict";
    return function(uid) {
        var sections = ['_header','_work','_askList'];
        var layoutView = window.app.render(sections);


        var userApi = new window.app.model();
        userApi.url = '/v2/user';
        var headerView = new header({
            model: userApi
        });
        window.app.show(layoutView._header, headerView);

        var workView = new work({
        });
        // window.app.show(layoutView._work, workView);

        var askListApi = new window.app.collection();
        askListApi.url = '/asks?uid=2762&type=ask';
        var askListView = new askList({
            collection: askListApi
        });
        window.app.show(layoutView._askList, askListView);

    };
});

