define([
        'app/views/Base', 
        'tpl!app/templates/askdetail/AskDetailActionView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,

            construct: function() {
                this.listenTo(this.model, 'change', this.render);
            },
    
        });
    });
