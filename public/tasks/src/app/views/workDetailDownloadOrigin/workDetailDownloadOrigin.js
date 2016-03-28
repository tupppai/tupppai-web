
define(['app/views/base', 'tpl!app/views/workDetailDownloadOrigin/workDetailDownloadOrigin.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });
