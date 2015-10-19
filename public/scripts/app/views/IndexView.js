define(['app/views/Base', 'tpl!app/templates/IndexView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({

            template: template,
        });
    });
