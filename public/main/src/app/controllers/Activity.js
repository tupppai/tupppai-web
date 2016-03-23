define(['underscore', 
        'app/views/activity/ActiveView',
        ],
    function (_,  ActiveView) {
        "use strict";
        return function() {
        	var view = new ActiveView();
            window.app.content.show(view);
        };
    });
