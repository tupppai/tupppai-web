define([
        'app/views/user/header/header',
		'app/views/user/askList',
		],
	function (header,askList) {
    "use strict";
    return function(uid) {
        var sections = ['_header','_askList'];
        var layoutView = window.app.render(sections);


        var userApi = new window.app.model();
        userApi.url = '/v2/user';
        var headerView = new header({
            model: userApi
        });
        window.app.show(layoutView._header, headerView);

        var askListApi = new window.app.collection();
        askListApi.type = 'ask';
        askListApi.url = '/asks?uid=2762&type=ask';
        var askListView = new askList({
            collection: askListApi
        });
        window.app.show(layoutView._askList, askListView);

        headerView.on('click:nav',function(type){
            if( type == 'work') {
                askListApi.type = type;
                askListApi.url= '/v2/user/replies';
                askListApi.reset();
                askListApi.fetch();
            } else if(type == 'ask') {
                askListApi.type = type;
                askListApi.url= '/asks?uid=2762&type=ask';
                askListApi.reset();
                askListApi.fetch();
            } else if(type == 'progress') {
                askListApi.type = type;
                askListApi.url= '/v2/inprogresses';
                askListApi.reset();
                askListApi.fetch();
            }
        });


    };
});

