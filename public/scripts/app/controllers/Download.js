define(['underscore', 'app/views/DownloadView'],
    function (_, DownloadView) {
        "use strict";

        return function() {
            var view = new DownloadView();
            window.app.content.show(view);
        };
    });
