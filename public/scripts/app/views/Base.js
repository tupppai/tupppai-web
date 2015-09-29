define(['marionette', 'imagesLoaded'],
    function (Marionette, imagesLoaded) {
        "use strict";
        
        return Marionette.ItemView.extend({
            initialize: function(){ 
                console.log('base view initialize'); 
                $(window).unbind('scroll');

                this.construct();
            },
            construct: function () {
            },
            onRender: function(){ 
                var imgLoad = imagesLoaded('.is-loading', function() { 
                    console.log('all image loaded');
                });
                imgLoad.on('progress', function ( imgLoad, image ) {
                    if(image.isLoaded)  
                        image.img.parentNode.className =  '';
                });
            }
        });
    });
