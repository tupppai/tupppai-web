define([
        'app/views/Base', 
        'app/collections/Users', 
        'tpl!app/templates/homepage/HomeFansView.html'
       ],
    function (View, Users, template) {
            "use strict";
        
        return View.extend({
            tagName: 'div',
            className: 'empty',
            data: 0,
            collections: Users,
            template: template,
            onRender: function() {
                $(".home-nav li").removeClass("active");
            },

            construct: function() {
                var uid = $(".menu-nav-reply").attr("data-id");
                var self = this;
                this.listenTo(this.collection, 'change', this.render);
                self.scroll();
                self.collection.url = '/fans';
                self.collection.reset();
                self.collection.data.uid = uid;
                self.collection.data.page = 0;
                self.collection.loading();

            },

    
        });
    });
