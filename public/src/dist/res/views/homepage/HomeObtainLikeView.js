define([
        'app/views/Base', 
        'tpl!app/templates/homepage/HomeObtainLikeView.html'
       ],
    function (View, template) {
            "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            data: 0,
            template: template,
            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
            }
        });
    });
