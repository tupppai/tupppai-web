define([
        'app/views/wallet/money/moneyView', 
    ], function (money) {
    "use strict";
    return function(id) {
		var layoutView = window.app.render(['_content']);

        var model = new window.app.model();
        model.url = '/users/' + id;
        var lv = new money({
            model: model
        });
        window.app.show(layoutView._content, lv);

    };
});
