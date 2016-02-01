define(['underscore',
		'app/views/Base', 
        'app/views/task/TaskView',
        'app/collections/Replies'
        ],
    function (_, View, template, Replies) {
        "use strict";

        return function(tutorial_id, reply_id) {
 
            var replys = new Replies;
            replys.url = '/replies?ask_id=' + tutorial_id;
            replys.data.reply_id = reply_id;

            var view = new template({collection: replys});
            window.app.content.show(view);
        };
    });