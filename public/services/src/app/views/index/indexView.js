define(['app/views/base', 'tpl!app/views/index/index.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });


