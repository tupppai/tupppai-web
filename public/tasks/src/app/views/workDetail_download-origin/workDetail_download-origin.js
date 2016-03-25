define(['app/views/base', 'tpl!app/views/workDetail_download-origin/workDetail_download-origin.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });
