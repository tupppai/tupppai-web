define([
		'app/views/index/banner/banner',
        'app/views/index/hotList',
		'app/views/index/starList',
		],
	function (banner, hotList, starList) {
    "use strict";
    return function() {
        var sections = ['_banner','_hotList', '_starList'];
        var layoutView = window.app.render(sections);

        //出品详情
        var bannerApi = new window.app.model();
        bannerApi.url = '/v2/bannerAndTags';
        var header = new banner({
                model: bannerApi
        });
        window.app.show(layoutView._banner, header);

        // 评论列表
        var hot = new window.app.collection();
        hot.url = "/v2/populars";
        var header = new hotList({
            collection: hot
        });
        window.app.show(layoutView._hotList, header);

        //评论列表
        var star = new window.app.collection();
        star.url = "/v2/recommendUser";
        var starView = new starList({
            collection: star
        });
        window.app.show(layoutView._starList, starView);

        $('.header-nav li').removeClass('home-press');
        $('.index-header').addClass('home-press');
    };
});

