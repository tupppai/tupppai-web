define(['app/views/Base', 
        'app/views/sharecourse/ShareCourseView',
        'app/collections/Threads',
        ],
    function (View,  template, Threads) {
        "use strict";
        return function(tutorial_id) {

            var tutorial_id = 2242;
            var threads = new Threads;
            threads.url = '/tutorial?tutorial_id=' + tutorial_id;

            var view = new template({collection: threads});
            window.app.content.show(view);
        };
    });