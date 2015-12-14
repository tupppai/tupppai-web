define(['app/views/Base', 'app/collections/Topics', 'tpl!app/templates/search/TopicItemView.html'], 
    function (View, Topics, template) {
        "use strict";
       
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collection: Topics,
            construct: function() {
                var self = this;
                this.listenTo(this.collection, 'change', this.render);
                self.collection.loading(self.showEmptyView);
            },
        });
    });
