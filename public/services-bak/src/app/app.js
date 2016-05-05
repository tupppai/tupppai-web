define('app/app', 
	[ 
		'marionette',
        'app/views/menu/menuView',
	], 


	function (marionette,menuView) {
        "use strict";
        var app  = new marionette.Application();

        app.addRegions({
            content: '#contentView',
            payPage: '#indexMenu'
        });

        app.addInitializer(function (options) {
            app.menuView = new menuView();
            app.payPage.show(app.menuView);
        });

        return app;
    });
