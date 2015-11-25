define(['common', 'app/views/Base', 'tpl!app/templates/UserSafetyView.html'],
    function (common, View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,

        });
    });
