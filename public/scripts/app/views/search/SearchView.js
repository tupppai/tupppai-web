define(['app/views/Base', 'tpl!app/templates/search/SearchView.html'],
         
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            template: template

        });
    });
