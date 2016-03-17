define([ 'app/models/boy','app/views/index/IndexView' ], 
	function (boy, indexView) {
    "use strict";
    return function() {
    	// var boyMessage = new boy;
    	// boyMessage.url = '/wxactgod/index'
        var view = new indexView({
        	// model: boyMessage
        });
        window.app.content.show(view);
    };
});
