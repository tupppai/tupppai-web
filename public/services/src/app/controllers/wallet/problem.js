define([
        'app/views/wallet/problem/problemView', 
    ], function (problemView) {
    "use strict";
    return function(id) {
		var layoutView = window.app.render(['_content']);

        var lv = new problemView({
        });
        window.app.show(layoutView._content, lv);

    };
});
