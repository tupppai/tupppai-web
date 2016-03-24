define('app/app', 
	[ 
		'marionette',
		'app/views/headerContainer/headerContainerView',
	], 


	function (marionette, headerContainerView) {
        "use strict";
        var app  = new marionette.Application();

        app.addRegions({
            content: '#contentView',
            payPage: '#headerContainer'
        });

        app.addInitializer(function (options) {
            app.headerContainerView = new headerContainerView();
            app.payPage.show(app.headerContainerView);
        });

        return app;
    });
