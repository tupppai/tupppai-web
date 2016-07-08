define([
        'app/views/user/header/header',
		'app/views/user/work/work',
		],
	function (header,work) {
    "use strict";
    return function() {
        var sections = ['_header','_work'];
        var layoutView = window.app.render(sections);

        var headerView = new header({
        });
        window.app.show(layoutView._header, headerView);

        var workView = new work({
        });
        window.app.show(layoutView._work, workView);

    };
});

