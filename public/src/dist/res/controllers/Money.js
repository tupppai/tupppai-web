define(['app/views/Base', 
        'app/views/money/MoneyView',
        ],
    function (View,  template) {
        "use strict";
        return function() {
        	var view = new template();
            window.app.content.show(view);
        };
    });