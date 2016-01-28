define(['app/views/Base', 
        'app/views/sharecourse/ShareCourseView',
        'app/collections/Threads',
        ],
    function (View,  template, Threads) {
        "use strict";
        return function() {

            var threads = new Threads;
            threads.url = '/tutorial?tutorial_id=2242';

            var view = new template({collection: threads});
            window.app.content.show(view);
        };
    });