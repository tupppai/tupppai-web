define(['app/views/Base', 'tpl!app/templates/message/MessageView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
         

        });
    });
