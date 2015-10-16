define(['underscore', 'app/views/Base', 'app/models/Base', 'tpl!app/templates/ErrorPopupView.html'],
    function (_, View, ModelBase, template) {
        "use strict";
        
        return View.extend({
            template: template,
            
            construct: function () {
                var self = this;

                $(".error-popup").fancybox({});
            },
        });
    });
