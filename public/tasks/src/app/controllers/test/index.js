define([ 'app/views/test/index' ], function (TestView) {
    "use strict";
    return function() {
    	debugger;
        var view = new TestView();
        window.app.content.show(view);
    };
});
