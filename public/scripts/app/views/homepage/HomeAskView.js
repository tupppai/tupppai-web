define([
        'app/views/Base', 
        'app/collections/Asks',
        'tpl!app/templates/homepage/HomeAskView.html'
       ],
    function (View, Asks, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collections: Asks,

            construct: function() {
                var uid = $(".menu-nav-reply").attr("data-id");
                var self = this;
                self.listenTo(self.collection, 'change', self.render);
                self.scroll();
                self.collection.reset();
                self.collection.data.uid = uid;
                self.collection.data.page = 0;
                self.collection.loading();
            },
            
        });
    });
