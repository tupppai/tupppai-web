define(['underscore', 
		'app/collections/Inprogresses', 
		'app/views/upload/InprogressItemView',
		'app/views/upload/InprogressView'
	],
    function (_, Inprogresses, InprogressItemView, InprogressView) {
        "use strict";

        return function() {
            
            var view = new InprogressView();

            window.app.content.show(view);

            var inprogresses = new Inprogresses;
            var inprogressItemView = new Backbone.Marionette.Region({el:"#InprogressItemView"});
            var view = new InprogressItemView({
                collection: inprogresses
            });
            inprogressItemView.show(view);
        };
    });
