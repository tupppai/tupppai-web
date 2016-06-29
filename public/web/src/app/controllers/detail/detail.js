define([
        'app/views/detail/ask/ask',
		'app/views/detail/work/work',
		],
	function (ask, work) {
    "use strict";
    return function() {
        var sections = ['_ask', '_work'];
        var layoutView = window.app.render(sections);


        var askView = new ask({
        });
        window.app.show(layoutView._ask, askView);

        var workView = new work({
        });
        window.app.show(layoutView._work, workView);
    };
});

