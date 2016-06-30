define([
        'app/views/wallet/withdrawals/withdrawalsView', 
    ], function (withdrawalsView) {
    "use strict";
    return function(id, type) {
		var layoutView = window.app.render(['_content']);
        
        var lv = new withdrawalsView({
        });
        window.app.show(layoutView._content, lv);

    };
});
