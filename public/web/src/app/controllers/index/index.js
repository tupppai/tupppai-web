define([
		'app/views/index/banner/banner',
		'app/views/index/hot/hot',
		],
	function (banner, hot) {
    "use strict";
    return function() {
        var sections = ['_banner','hot'];
        var layoutView = window.app.render(sections);

        var header = new banner({
        });
        window.app.show(layoutView._banner, header);

        var header = new hot({
        });
        window.app.show(layoutView.hot, header);

    };
});

