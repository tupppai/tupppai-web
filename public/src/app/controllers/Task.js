define(['underscore',
		'app/views/Base', 
        'app/views/task/TaskView',
        'app/collections/Replies'
        ],
    function (_, View, template, Replies) {
        "use strict";

        return function() {
            var replys = new Replies;
        	replys.url = '/replies?ask_id=2242';
            // replys.url = 'tutorial_details';
            // replys.data.tutorial_id = 2242;

            var view = new template({collection: replys});
            window.app.content.show(view);
        };
    });