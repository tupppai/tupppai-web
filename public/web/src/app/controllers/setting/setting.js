define([
        'app/views/setting/nav/nav',
        'app/views/setting/information/information',
		'app/views/setting/safe/safe',
		],
	function (nav, information, safe) {
    "use strict";
    return function() {
        var sections = ['_nav', '_information', '_safe'];
        var layoutView = window.app.render(sections);


        var navView = new nav({
        });
        window.app.show(layoutView._nav, navView);

        var informationView = new information({
        });
        // window.app.show(layoutView._information, informationView);

        var safeView = new safe({
        });
        window.app.show(layoutView._safe, safeView);
    };
});

