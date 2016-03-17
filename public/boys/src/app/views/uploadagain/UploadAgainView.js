define(['app/views/base', 'tpl!app/views/uploadagain/UploadAgainView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });