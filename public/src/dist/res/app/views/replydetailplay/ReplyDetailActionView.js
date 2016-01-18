define([
        'app/views/Base', 
        'tpl!app/templates/replydetailplay/ReplyDetailActionView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .super-like" : "superLike",
                "click .download" : "download",
            },

            construct: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
    
        });
    });
