define([
        'app/views/detail/ask/ask',
		'app/views/detail/workList',
		],
	function (ask, workList) {
    "use strict";
    return function(ask_id) {
        var sections = ['_ask', '_workList'];
        var layoutView = window.app.render(sections);



        var askApi = new window.app.model();
        askApi.url = '/v2/asks/' + ask_id
        var askView = new ask({
            model: askApi
        });
        window.app.show(layoutView._ask, askView);

        var replyApi = new window.app.collection();
        replyApi.url = '/v2/ask/'+ask_id+'/replies'

        var workListView = new workList({
            collection: replyApi
        });
        window.app.show(layoutView._workList, workListView);
    };
});

