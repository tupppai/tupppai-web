define(['marionette', 'tpl!app/templates/HeaderView.html'],
    function (Marionette, template) {
        "use strict";
        
        return Marionette.ItemView.extend({
            el: '<div class="title-bar">',
            template: template()
        });

    });
