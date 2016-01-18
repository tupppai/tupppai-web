define(['app/views/Base', 'tpl!app/templates/search/UserSearchView.html'],
         
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
                this.collection.loading();
            },

        });
    });
