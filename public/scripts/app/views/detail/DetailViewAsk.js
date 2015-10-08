define(['app/views/Base', 'tpl!app/templates/detail/DetailViewAsk.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });
