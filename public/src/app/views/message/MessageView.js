define(['app/views/Base', 'tpl!app/templates/message/MessageView.html'],
         
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .message .nav' : 'switchNav',
            },
            construct: function() {
                $("a.menu-bar-item").removeClass('active');
                this.listenTo(this.model, "change", this.render);
            },
            onRender: function() {
                setTimeout(function() {
                    $(".header-back").addClass("height-reduce");
                },1000);
            },
            switchNav: function(e) {
                var self = this;
                var type = $(e.currentTarget).attr('data');
             
                location.href = '/#message/' + type;
            }
        });
    });
