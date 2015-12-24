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
            onRender: function() {
                $(".home-nav li").removeClass("active");
                $(".ask-uploading-popup-hide").addClass("hide");
            },

            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
            },
        });
    });
