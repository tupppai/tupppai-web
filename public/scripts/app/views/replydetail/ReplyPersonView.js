define([
        'app/views/Base', 
        'tpl!app/templates/replydetail/ReplyPersonView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,

            construct: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
            onRender: function() {
                $(".create-time").animate({
                    'opacity' : 1
                },100);
            }
        });
    });
