define(['app/views/Base', 'tpl!app/templates/LoginView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            template: template,
            construct: function () {
                var self = this;
            }
        });
    });
