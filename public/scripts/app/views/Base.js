define(['marionette'],
    function (Marionette) {
        "use strict";
        
        return Marionette.ItemView.extend({
            initialize: function(){ 
                console.log('base view initialize'); 
                $(window).unbind('scroll');
                window.app.home.$el.hide();

                this.construct();
            },
            construct: function () {
            },
            onRender: function(){ 
                console.log('basic view render');
            }
        });
    });
