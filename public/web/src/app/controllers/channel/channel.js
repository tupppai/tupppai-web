define([
        'app/views/channel/nav/nav',
        'app/views/channel/header/header',
        'app/views/channel/ask/ask',
		'app/views/channel/work/work',
		],
	function (nav, header,ask, work) {
    "use strict";
    return function() {
        var sections = ['_nav','_header','_ask', '_work'];
        var layoutView = window.app.render(sections);

        var navView = new nav({
        });
        window.app.show(layoutView._nav, navView);

        var headerView = new header({
        });
        window.app.show(layoutView._header, headerView);

        var askView = new ask({
        });
        window.app.show(layoutView._ask, askView);

        var workView = new work({
        });
        window.app.show(layoutView._work, workView);
    };
});

