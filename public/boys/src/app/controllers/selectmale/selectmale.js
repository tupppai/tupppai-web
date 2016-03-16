define([ 'app/views/selectmale/SelectMaleGodsView' ], function (SelectMaleGodsView) {
    "use strict";
    return function() {
        var view = new SelectMaleGodsView();
        window.app.content.show(view);
    };
});
