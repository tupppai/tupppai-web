define([
        'app/views/channel/navList',
        'app/views/channel/header/header',
        'app/views/channel/askList',
		'app/views/channel/workList',
		],
	function (navList, header,askList, workList) {
    "use strict";
    return function() {
        var sections = ['_navList','_header','_askLista', '_workList'];
        var layoutView = window.app.render(sections);

        var channelNav = new window.app.collection();
        channelNav.url = "/v2/channel/list";


        var navListView = new navList({
            collection: channelNav
        });
        window.app.show(layoutView._navList, navListView);

        var bannerApi = new window.app.model();
        bannerApi.url = '/v2/channel/1010/info';
        var headerView = new header({
            model: bannerApi
        });
        window.app.show(layoutView._header, headerView);

        var askApi = new window.app.collection();
            askApi.url = "/v2/channel/0/asks";
        var askListView = new askList({
            collection: askApi
        });
        // window.app.show(layoutView._askLista, askListView);

        var channelWork = new window.app.collection();
        channelWork.url = "/v2/channel/0/replies";
        var workListView = new workList({
            collection: channelWork
        });
        window.app.show(layoutView._workList, workListView);


        $('.header-nav li').removeClass('home-press');
        $('.channel-header').addClass('home-press');
    };
});

