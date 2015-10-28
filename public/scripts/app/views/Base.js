define(['marionette', 'imagesLoaded'],
    function (Marionette, imagesLoaded) {
        "use strict";
        
        return Marionette.ItemView.extend({
            initialize: function(){ 
                //console.log('base view initialize'); 
                $(window).unbind('scroll');

                this.construct();
            },
            construct: function () {
            },
            onRender: function(){ 
                this.loadImage(); 
            },
            loadImage: function() {
                var imgLoad = imagesLoaded('.is-loading', function() { 
                    //console.log('all image loaded');
                });
                imgLoad.on('progress', function ( imgLoad, image ) {
                    if(image.isLoaded) {
                        setTimeout(function() {
                            if(image) {
                                image.img.parentNode.className =  '';
                                $(image.img).hide();
                                $(image.img).fadeIn(300);
                            }
                        }, 400);
                    }
                });
            }
        });
    });
