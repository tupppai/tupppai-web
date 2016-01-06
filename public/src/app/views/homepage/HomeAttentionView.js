define([
        'app/views/Base', 
        'app/collections/Users', 
        'tpl!app/templates/homepage/HomeFansView.html'
       ],
    function (View, Users, template) {
            "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            data: 0,
            collections: Users,
            template: template,
            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
            },
            onRender: function() {
                $(".home-nav li").removeClass("active");
            },
        });
    });
