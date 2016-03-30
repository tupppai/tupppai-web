define('app/app', 
	[ 
		'marionette',
	], 


	function (marionette) {
        "use strict";
        var app  = new marionette.Application();

        app.addRegions({
            content: '#contentView',
            payPage: '#headerContainer'
        });

        app.addInitializer(function (options) {
        });

        return app;
    });
