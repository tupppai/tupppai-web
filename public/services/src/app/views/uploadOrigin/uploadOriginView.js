define(['app/views/base', 'tpl!app/views/uploadOrigin/uploadOrigin.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });


