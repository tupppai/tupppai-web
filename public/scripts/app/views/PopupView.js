define(['underscore', 'app/views/Base', 'app/models/Base', 'tpl!app/templates/PopupView.html'],
    function (_, View, ModelBase, template) {
        "use strict";
        
        return View.extend({
            template: template,
            
            construct: function () {
                var self = this;

                $(".fancybox").fancybox({});
            },
        });
    });
