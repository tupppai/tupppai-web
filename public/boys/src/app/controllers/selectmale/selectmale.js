define([ 'app/models/boy','app/views/selectmale/SelectMaleGodsView' ], function (SelectMaleGodsView) {
    "use strict";
    return function() {
    	var boyMessage = new boy;
    	boyMessage.url = '/wxactgod/index'
        var view = new SelectMaleGodsView({
        	model: boyMessage
        });
        window.app.content.show(view);
    };
});
