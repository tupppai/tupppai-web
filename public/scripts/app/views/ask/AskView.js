define([
        'masonry', 
        'app/views/Base', 
        'tpl!app/templates/ask/AsksItemView.html'
       ],
    function (masonry, View, template) {
        "use strict";

        
        return View.extend({
            tagName: 'div',
            className: 'ask-container  grid',
            template: template,
            render: function() {

                $(this.el).append(template());
                console.log()
                debugger;
                setTimeout(function(){
                var msnry = new masonry( '.grid', {});    
            }, 1000);
                
            },
            onRender: function() {
            }
        });
    });
