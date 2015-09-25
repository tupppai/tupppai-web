define(['marionette'],
    function (Marionette) {
        "use strict";
        
        return Marionette.ItemView.extend({
            initialize: function(){ 
                $(window).unbind('scroll');
                console.log('base view initialize'); 

                this.construct();
            },
            construct: function () {
            },
            onRender: function(){ 
                console.log('basic view render');
            }
        });
    });
