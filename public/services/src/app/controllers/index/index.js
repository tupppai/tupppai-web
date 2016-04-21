define(['app/views/ask/index/indexView', 'app/views/list/index'], 
    function (view, list) {
    	"use strict"; 
    
        return function() {
            var sections = [ '_view', '_list' ];
			var layoutView = window.app.render(sections);

			//view init
            //var model = new window.app.model( { value: 1 } );
            var viewView = new view({ 
				//model: model
			});
            layoutView._view.show(viewView);

			//list init
            // var collection = new window.app.collection( [ 1,2,3 ]);
            // var listView = new list({
            //     collection: collection
            // });
            // layoutView._list.show(listView);
        };
    });
