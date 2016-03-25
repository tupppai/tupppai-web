define(['app/views/base', 'tpl!app/views/homepage_request/homepage_request.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });
