define(['app/views/Base', 'app/models/User', 'tpl!app/templates/LoginView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function () {
                var self = this;
            }
        });
    });
