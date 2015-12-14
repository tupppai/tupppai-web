define([
        'app/views/Base', 
        'tpl!app/templates/message/MessageItemView.html'
        ],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function() {
                var self = this;
                $("a.menu-bar-item").removeClass('active');

                this.listenTo(this.collection, "change", this.render);

                self.scroll();
                self.collection.loading(self.showEmptyView);
            },
        });
    });
